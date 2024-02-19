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
 * shows reports for assignexternal
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
global $DB, $PAGE;

$courseid = required_param('courseid', PARAM_INT);
$assignmentid  = required_param('assignmentid', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$coursemodule = get_coursemodule_from_instance('assignexternal', $assignmentid, $courseid, false, MUST_EXIST);
$assignment = $DB->get_record('assignexternal', ['id' => $coursemodule->instance], '*', MUST_EXIST);

$PAGE->set_url('/mod/assignexternal/reports.php',
    array('courseid' => $courseid, 'assignmentid' => $assignmentid));

require_login($course, true, $coursemodule);
$coursecontext = context_course::instance($courseid);
$modulecontext = context_module::instance($coursemodule->id);

require_capability('mod/assignment:view', $modulecontext);