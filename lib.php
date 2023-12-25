<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the moodle hooks for the assignprogram module.
 *
 * It delegates most functions to the assignment class.
 *
 * @package     mod_assignprogram
 * @copyright   2023 Marcel Suter <marcel@ghwalin.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use mod_assignprogram\controller\assign_control;

defined('MOODLE_INTERNAL') || die();

/**
 * Adds an assignment instance
 *
 * This is done by calling the add_instance() method of the assignment type class
 * @param stdClass $data
 * @param mod_assignprogram_mod_form $form
 * @return int The instance id of the new assignment
 */
function assignprogram_add_instance(stdClass $data, mod_assignprogram_mod_form $form = null) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignprogram/classes/controller/assign_control.php');

    $assignment = new assign_control(context_module::instance($data->coursemodule), null, null);
    return $assignment->add_instance($data);
}

/**
 * delete an assignment instance
 * @param int $id
 * @return bool
 */
function assignprogram_delete_instance($id) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignprogram/classes/controller/assign_control.php');
    $cm = get_coursemodule_from_instance('assignprogram', $id, 0, false, MUST_EXIST);
    $context = context_module::instance($cm->id);

    $assignment = new assign_control($context, null, null);
    return $assignment->delete_instance();
}

/**
 * Update an assignment instance
 *
 * This is done by calling the update_instance() method of the assignment type class
 * @param stdClass $data
 * @param stdClass $form - unused
 * @return object
 */
function assignprogram_update_instance(stdClass $data, $form) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignprogram/classes/controller/assign_control.php');
    $context = context_module::instance($data->coursemodule);
    $assignment = new assign_control($context, null, null);
    return $assignment->update_instance($data);
}

function mod_assignprogram_cm_info_view(cm_info $coursemodule) {
    $info = new cached_cm_info();
    global $DB;
    $assignment = $DB->get_record(
        'assignprogram',
        array('id'=>$coursemodule->instance),
        'externallink, alwaysshowassignment, allowsubmissionsfromdate', MUST_EXIST);
    // TODO check for alwaysshowassignment and allowsubmissionsfromdata
    $info->content = '<a href="' . $assignment->externallink . ' target="_blank">GitHub Classroom Assignment</a>';
    return $info;
}

function mod_assignprogram_cm_info_dynamic(cm_info $coursemodule) {
    $context = context_module::instance($coursemodule->id);
    $urlparams = array('cmid' => $coursemodule->id,
        'action' => optional_param('action', '', PARAM_ALPHA),
        'rownum' => optional_param('rownum', 0, PARAM_INT));
    if ($urlparams['action'] != '') {

    }
}