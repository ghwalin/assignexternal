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
 * This file contains the moodle hooks for the assignexternal module.
 *
 * It delegates most functions to the assignment class.
 *
 * @package     mod_assignexternal
 * @copyright   2023 Marcel Suter <marcel@ghwalin.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use mod_assignexternal\controller\assign_control;
use mod_assignexternal\data\assign;
use mod_assignexternal\data\grade;

/**
 * Adds an assignment instance
 *
 * This is done by calling the add_instance() method of the assignment type class
 * @param stdClass $data
 * @param mod_assignexternal_mod_form $form
 * @return int The instance id of the new assignment
 */
function assignexternal_add_instance(stdClass $data, mod_assignexternal_mod_form $form = null) {
    global $CFG, $CONTEXT;
    require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/assign_control.php');
    $instance = context_module::instance($data->coursemodule);
    $assigncontrol = new assign_control($instance, null, null);
    $assignid = $assigncontrol->add_instance($data, $instance->instanceid);
    return $assignid;
}

/**
 * delete an assignment instance
 * @param int $id
 * @return bool
 */
function assignexternal_delete_instance($id) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/assign_control.php');
    $cm = get_coursemodule_from_instance('assignexternal', $id, 0, false, MUST_EXIST);
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
function assignexternal_update_instance(stdClass $data, $form) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/assign_control.php');
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
function assignexternal_get_coursemodule_info(stdClass $coursemodule) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
    $assignment = new assign();
    $assignment->load_db($coursemodule->instance);
    $result = new cached_cm_info();
    $result->name = $assignment->get_name();
    if ($assignment->get_duedate()) {
        $result->customdata['duedate'] = $assignment->get_duedate();
    }
    if ($assignment->get_cutoffdate()) {
        $result->customdata['cutoffdate'] = $assignment->get_cutoffdate();
    }
    if ($assignment->get_allowsubmissionsfromdate()) {
        $result->customdata['allowsubmissionsfromdate'] = $assignment->get_allowsubmissionsfromdate();
    }
    $result->customdata['alwaysshowlink'] = $assignment->is_alwaysshowlink();

    $result->customdata['externallink'] = $assignment->get_externallink();

    if ($coursemodule->completion == COMPLETION_TRACKING_AUTOMATIC) {
        $result->customdata['customcompletionrules']['haspassinggrade'] = $assignment->is_haspassinggrade();
    }

    return $result;
}

/**
 * customize module display for the current user on course listing
 *
 * @param cm_info $coursemodule
 * @return void
 * @throws dml_exception
 */
function assignexternal_cm_info_view(cm_info $coursemodule): void {
    $externallink = '<a href="' . $coursemodule->customdata['externallink'] .
        '" target="_blank">' . get_string('externallink', 'assignexternal') . '</a>';
    $content = '';
    if (array_key_exists('allowsubmissionsfromdate', $coursemodule->customdata)) {
        if ($coursemodule->customdata['alwaysshowlink'] ||
            $coursemodule->customdata['allowsubmissionsfromdate'] < time()) {
            $content .= $externallink;
        }
        if ($coursemodule->customdata['allowsubmissionsfromdate'] >= time()) {
            $label = get_string('submissionsopen', 'assignexternal');
        } else {
            $label = get_string('submissionsopened', 'assignexternal');
        }
        $content .= '<br><strong>' . $label . '</strong> ' . userdate($coursemodule->customdata['allowsubmissionsfromdate']);

    } else {
        $content .= $externallink;
    }

    if (array_key_exists('duedate', $coursemodule->customdata)) {
        $content .= '<br><strong>' . get_string('submissionsdue', 'assignexternal') . '</strong> ' .
            userdate($coursemodule->customdata['duedate']);
    }

    // TODO information about completion settings

    $coursemodule->set_content($content);

}

/**
 * customize module display
 * @param cm_info $coursemodule
 * @return void
 * @throws coding_exception
 */
function assignexternal_cm_info_dynamic(cm_info $coursemodule) {
    $context = context_module::instance($coursemodule->id);
}


/**
 * Return the features this module supports
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know or string for the module purpose.
 */
function assignexternal_supports($feature) {
    switch ($feature) {
        /*  TODO
         case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_ADVANCED_GRADING:
            return true;
        case FEATURE_PLAGIARISM:
            return true;
        case FEATURE_COMMENT:
            return true;
         */
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        case FEATURE_MOD_PURPOSE:
            return MOD_PURPOSE_ASSESSMENT;

        default:
            return null;
    }
}

/**
 * Obtains the automatic completion state for this external assignment based on the conditions in settings.
 *
 * @param object $course Course
 * @param object $cm Course-module
 * @param int $userid User ID
 * @param bool $type Type of comparison (or/and; can be used as return value if no conditions)
 * @return bool True if completed, false if not, $type if conditions not set.
 * @throws dml_exception
 */
function assignexternal_get_completion_state(
    $course,
    $coursemodule,
    $userid,
    $type
) {
    global $CFG, $DB;

    $assign = $DB->get_record('assignexternal', ['id' => $coursemodule->instance], '*', MUST_EXIST);
    if ($assign->haspassinggrade) {
        $grade = new grade();
        $grade->load_db($coursemodule, $userid);
        $completed = false;
        if ($assign->haspassinggrade) {
            $maxgrade = $assign->externalgrademax + $assign->manualgrademax;
            $passinggrade = $maxgrade * $assign->passingpercentage / 100;
            $completed = $grade->total_grade() >= $passinggrade;
        }
        return $completed;
    } else {
        return $type;
    }
}

/**
 * Callback which returns human-readable strings describing the active completion custom rules for the module instance.
 *
 * @param cm_info|stdClass $coursemodule object with fields ->completion and ->customdata['customcompletionrules']
 * @return array $descriptions the array of descriptions for the custom rules.
 */
function mod_assignexternal_get_completion_active_rule_descriptions($coursemodule) {
    if (empty($cm->customdata['customcompletionrules']) || $cm->completion != COMPLETION_TRACKING_AUTOMATIC) {
        return ['foobar'];
    }

    $descriptions = [];
    foreach ($coursemodule->customdata['customcompletionrules'] as $key => $val) {
        if ($key == 'haspassinggrade') {
            $descriptions[] = get_string('haspassinggradedesc', 'assignexternal', $val);
        }
    }
    return $descriptions;
}
