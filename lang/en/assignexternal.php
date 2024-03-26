<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin strings are defined here.
 *
 * @package     mod_assignexternal
 * @category    string
 * @copyright   2023 Marcel Suter <marcel@ghwalin.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['pluginname'] = 'external assignment';

$string['allowsubmissionsfromdate'] = 'Allow submissions from';
$string['allowsubmissionsfromdate_help'] = 'If enabled, students will not be able to submit before this date. If disabled, students will be able to start submitting right away.';
$string['alwaysshowdescription'] = 'Always show description';
$string['alwaysshowlink'] = 'Always show link';
$string['alwaysshowlink_help'] = 'If disabled, the assignment link above will only become visible to students on the "Allow submissions from" date.';
$string['assignmentisdue'] = 'Assignment is due';
$string['assignmentname'] = 'Assignment name';
$string['availability'] = 'Availability';

$string['changeuser'] = 'Change user';
$string['cutoffdate'] = 'Cut-off date';
$string['cutoffdate_help'] = 'If set, submissions will not be accepted after this date without an extension. If not set, submissions will always be accepted.';
$string['cutoffdatevalidation'] = 'Cut-off date cannot be earlier than the due date.';
$string['cutoffdatefromdatevalidation'] = 'Cut-off date cannot be earlier than the allow submissions from date.';

$string['done'] = 'done';
$string['description'] = 'Description';
$string['duedate'] = 'Due date';
$string['duedate_help'] = 'TODO duedate_help';
$string['duedateaftersubmissionvalidation'] = 'Due date must be after the allow submissions from date.';
$string['duedatevalidation'] = 'Due date cannot be earlier than the allow submissions from date.';

$string['extenddate'] = 'TODO extenddate';
$string['extenddate_help'] = 'TODO extenddate_help';
$string['external'] = 'External';
$string['externalgrade'] = 'External grade';
$string['externalfeedback'] = 'Feedback from external system';
$string['externalgrademax'] = 'External grade max.';
$string['externalgrademax_help'] = 'Maximum grade from external assignment';
$string['externallink'] = 'Assignment link';
$string['externallink_help'] = 'The link to the assignment in the external system';
$string['externalname'] = 'External assignment';
$string['externalname_help'] = 'The name of the assignment in the external system';

$string['feedback'] = 'Feedback';
$string['finalgrade'] = 'Final grade';

$string['grade'] = 'Grade';
$string['graded'] = 'Graded';
$string['grading'] = 'Grading';
$string['gradingoverview'] = 'Grading overview';
$string['gradingstatus'] = 'Grading status';
$string['grantextension'] = 'Grant extension';

$string['haspassinggrade'] = 'Student needs a passing grade to complete the assignment';

$string['mandatory'] = 'Mandatory';
$string['manual'] = 'Manual';
$string['manualfeedback'] = 'Manual feedback';
$string['manualgrade'] = 'Manual grade';
$string['manualgrademax'] = 'Manual grade max.';
$string['manualgrademax_help'] = 'Maximum grade from manual grading';
$string['modulename'] = 'External assignment';
$string['modulename_help'] = 'The external assignment activity module lets you give your students an assignment in an external system (e.g. GitHub Classroom).\nIt includes a webservice to update the student\'s grading from the external assessment';
$string['modulenameplural'] = 'External assignments';

$string['nextuser'] = 'Next user';

$string['override'] = 'Override';

$string['passingpercentage'] = 'Percentage to pass';
$string['passingpercentage_help'] = 'What percentage of the maximum grade (external + manual) must be achieved to pass';
$string['pending'] = 'pending';
$string['pluginadministration'] = 'External Assignment';
$string['pluginname'] = 'External Assignment';
$string['previoususer'] = 'Previous user';

$string['scores'] = 'Scores';
$string['scorereached'] = 'Score reached';
$string['scoremaximum'] = 'Maximum score';
$string['seefeedback'] = 'See feedback';
$string['selectedusers'] = 'TODO selectedusers';
$string['submissionsdue'] = 'Due:';
$string['submissionsopen'] = 'Opens:';
$string['submissionsopened'] = 'Opened:';
$string['submissionstatus'] = 'Submission status';
$string['studentlink'] = 'Link to your assignment';

$string['timeremaining'] = 'Time left';
$string['timeremainingcolon'] = 'Time remaining: {$a}';
$string['totalgrade'] = 'Total grade';
