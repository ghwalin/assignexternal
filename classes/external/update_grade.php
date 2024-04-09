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

namespace mod_assignexternal\external;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once("$CFG->dirroot/lib/externallib.php");

use dml_exception;
use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use mod_assign_external;
use mod_assignexternal\data\assign;
use mod_assignexternal\data\grade;

/**
 * webservice to update the externalgrade and externalfeedback
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_grade extends external_api {
    /**
     * creates the return structure
     * @return external_single_structure
     */
    public static function execute_returns() {
        return
            new external_single_structure([
                'type' => new external_value(PARAM_TEXT, 'info, warning, error'),
                'name' => new external_value(PARAM_TEXT, 'the name of this warning'),
                'message' => new external_value(PARAM_TEXT, 'warning message'),
            ]);
    }

    /**
     * Update grades from an external system
     * @param $assignmentname  String the name of the external assignment
     * @param $username  String the external username
     * @param $points float the number of points
     * @param $max  float the maximum points from tests
     * @param $externallink  string the url of the students repo
     * @param $feedback  string the feedback as json-structure
     * @return array
     * @throws dml_exception
     * @throws invalid_parameter_exception
     */
    public static function execute(
        string $assignmentname,
        string $username,
        float  $points,
        float  $max,
        string $externallink,
        string $feedback
    ): array {
        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'assignment_name' => $assignmentname,
                'user_name' => $username,
                'points' => $points,
                'max' => $max,
                'externallink' => $externallink,
                'feedback' => $feedback,
            ]
        );
        $externalusername = self::customfieldid_username();
        $userid = self::get_user_id($params['user_name'], $externalusername);
        if (!empty($userid)) {
            $assignment = self::read_assignment($assignmentname, $userid);
            if (empty($assignment->get_id())) {
                echo 'WARNING: no assignment ' . $params['assignment_name'] . ' found';
                return self::generate_warning(
                    'error',
                    'no_assignment',
                    'No assignment with name "' . $params['assignment_name'] . '" found. Contact your teacher.'
                );
                // TODO: Error and status 404.
           /* } else if ($assignment->get_cutoffdate() < time()) {    FIXME
                echo 'WARNING: the assignment is overdue, points/feedback not updated';
                return self::generate_warning(
                    'info',
                    'overdue',
                    'The assignment is overdue, points/feedback not updated'
                ); */
            } else {
                self::update_grades($assignment->get_id(), $userid, $params);
            }
        } else {
            echo 'WARNING: no username ' . $params['user_name'] . ' found';
            return self::generate_warning(
                'error',
                'no_user',
                'No user found with username "' . $params['user_name'] . '" Update your Moodle profile.'
            );
        }

        return self::generate_warning(
            'info',
            'success',
            'Update successful'
        );
    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters() {
        return new external_function_parameters(
            [
                'assignment_name' => new external_value(
                    PARAM_TEXT,
                    'name of the assignment'
                ),
                'user_name' => new external_value(
                    PARAM_TEXT,
                    'username of the github user'
                ),
                'points' => new external_value(
                    PARAM_FLOAT,
                    'the points for grading'
                ),
                'max' => new external_value(
                    PARAM_FLOAT,
                    'the maximum points'
                ),
                'externallink' => new external_value(
                    PARAM_TEXT,
                    'the url of the student repository',
                    0,
                    ''
                ),
                'feedback' => new external_value(
                    PARAM_TEXT,
                    'the feedback for this grade',
                    0,
                    '[]'
                ),
            ]
        );
    }

    /**
     * returns the id of the custom field for the external username
     */

    private static function customfieldid_username(): int {
        global $DB;

        $customfield = $DB->get_record(
            'user_info_field',
            ['shortname' => get_config('local_gradeassignments', 'external_username')],
            'id,shortname');
        return $customfield->id;
    }

    /**
     * returns the moodle userid by the external username
     * @param string $username the external username
     * @param int $fieldid the id of the custom field for external username
     * @return int  the moodle-userid
     * @throws dml_exception
     */
    private static function get_user_id(string $username, int $fieldid): ?int {
        global $DB;
        $query = 'SELECT userid' .
            '  FROM {user_info_data}' .
            ' WHERE fieldid=:fieldid' .
            '   AND data=:ghusername';
        $user = $DB->get_record_sql(
            $query,
            [
                'fieldid' => $fieldid,
                'ghusername' => $username,
            ]
        );
        if (!empty($user)) {
            return $user->userid;
        } else {
            return null;
        }
    }

    /**
     * reads the assignment data
     * @param string $assignmentname
     * @param int $userid
     * @return assign
     * @throws dml_exception
     */
    private static function read_assignment(string $assignmentname, int $userid): assign {

        $assignment = new assign();
        $assignment->load_db_external($assignmentname, $userid);
        return $assignment;
    }

    private static function generate_warning(string $type, string $name, string $message): array {
        return [
            'type' => $type,
            'name' => $name,
            'message' => $message,
        ];
    }

    /**
     * updates the grade for a programming assignment
     * @param int $assignmentid
     * @param int $userid
     * @param array $params
     * @return void
     * @throws dml_exception
     */
    private static function update_grades(int $assignmentid, int $userid, array $params): void {
        global $DB;
        $grade = new grade();
        $grade->load_db($assignmentid, $userid);
        $grade->set_assignmentexternal($assignmentid);
        $grade->set_userid($userid);
        $grade->set_externalgrade($params['points']);
        $feedback = urldecode($params['feedback']);
        $grade->set_externalfeedback(format_text($feedback, FORMAT_MARKDOWN));
        $grade->set_externallink($params['externallink']);
        if (empty($grade->get_id())) {
            $DB->insert_record('assignexternal_grades', $grade->to_stdclass());
        } else {
            $DB->update_record('assignexternal_grades', $grade->to_stdclass());
        }
    }

    /**
     * reads the grade using the assignment-name and userid
     *
     * @param int $userid
     * @param int $coursemoduleid
     * @return object|null
     * @throws dml_exception
     */
    private static function read_grade(int $coursemoduleid, int $userid): ?object {
        global $DB;

        $data = $DB->get_record(
            'assignexternal_grades',
            [
                'userid' => $userid,
                'assignexternal' => $coursemoduleid,
            ]
        );
        if (!$data) {
            return null;
        }
        return $data;
    }
}
