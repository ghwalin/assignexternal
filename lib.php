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
function assignprogram_add_instance(stdClass $data, mod_assignprogram_mod_form $form = null)
{
    global $CFG, $CONTEXT;
    require_once($CFG->dirroot . '/mod/assignprogram/classes/controller/assign_control.php');
    $instance = context_module::instance($data->coursemodule);
    $assignment = new assign_control($instance, null, null);
    return $assignment->add_instance($data, $instance->instanceid);
}

/**
 * delete an assignment instance
 * @param int $id
 * @return bool
 */
function assignprogram_delete_instance($id)
{
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
 * @return bool
 */
function assignprogram_update_instance(stdClass $data, $form)
{
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignprogram/classes/controller/assign_control.php');
    $context = context_module::instance($data->coursemodule);
    $assignment = new assign_control($context, null, null);
    return $assignment->update_instance($data, $context->instanceid);
}

/**
 * Returns additional information for showing the assignment in course listing
 *
 * @param stdClass $coursemodule
 * @return cached_cm_info
 * @throws dml_exception
 */
function assignprogram_get_coursemodule_info(\stdClass $coursemodule)
{
    global $DB;
    $assignment = $DB->get_record(
        'assignprogram',
        array('id' => $coursemodule->instance),
        'id, name, externallink, alwaysshowlink, allowsubmissionsfromdate, duedate, cutoffdate',
        MUST_EXIST);
    $result = new cached_cm_info();
    $result->name = $assignment->name;
    if ($assignment->duedate) {
        $result->customdata['duedate'] = $assignment->duedate;
    }
    if ($assignment->cutoffdate) {
        $result->customdata['cutoffdate'] = $assignment->cutoffdate;
    }
    if ($assignment->allowsubmissionsfromdate) {
        $result->customdata['allowsubmissionsfromdate'] = $assignment->allowsubmissionsfromdate;
    }
    $result->customdata['alwaysshowlink'] = $assignment->alwaysshowlink;

    $result->customdata['externallink'] = $assignment->externallink;
    return $result;
}

/**
 * customize module display for the current user on course listing
 *
 * @param cm_info $coursemodule
 * @return cached_cm_info
 * @throws dml_exception
 */
function assignprogram_cm_info_view(cm_info $coursemodule)
{
    $externallink = '<a href="' . $coursemodule->customdata['externallink'] .
        '" target="_blank">' . get_string('externallink', 'assignprogram') . '</a>';
    $content = '';
    if (array_key_exists('allowsubmissionsfromdate', $coursemodule->customdata)) {
        if ($coursemodule->customdata['alwaysshowlink'] ||
            $coursemodule->customdata['allowsubmissionsfromdate'] < time()) {
            $content .= $externallink;
        }
        $content .= '<br><strong>' . get_string('activitydate:submissionsopen', 'assign') . '</strong> ' . userdate($coursemodule->customdata['allowsubmissionsfromdate']);

    } else {
        $content .= $externallink;
    }

    if (array_key_exists('duedate', $coursemodule->customdata)) {
        $content .= '<br><strong>' . get_string('activitydate:submissionsdue', 'assign') . '</strong> ' . userdate($coursemodule->customdata['duedate']);
    }

    $coursemodule->set_content($content);

}

/**
 * customize module display
 * @param cm_info $coursemodule
 * @return void
 * @throws coding_exception
 */
function assignprogram_cm_info_dynamic(cm_info $coursemodule)
{
    $context = context_module::instance($coursemodule->id);
}
