<?php


namespace mod_assignexternal\external;
global $CFG;
require_once("$CFG->dirroot/lib/externallib.php");

use external_function_parameters;
use external_single_structure;
use external_value;
use mod_assign_external;
use mod_assignexternal\data\grade;

/**
 * webservice to update the externalgrade and externalfeedback
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_grade extends \external_api
{
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters()
    {
        return new external_function_parameters(
            array(
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
                )
            )
        );
    }

    /**
     * creates the return structure
     * @return external_single_structure
     */
    public static function execute_returns()
    {
        return new external_single_structure(array());
    }

    /**
     * Update grades from an external system
     * @param $assignment_name  String the name of the external assignment
     * @param $user_name  String the external username
     * @param $points float the number of points
     * @param $max  float the maximum points from tests
     * @param $externallink  string the url of the students repo
     * @param $feedback  string the feedback as json-structure
     * @return array
     * @throws \dml_exception
     * @throws \invalid_parameter_exception
     */
    public static function execute(
        string $assignment_name,
        string $user_name,
        float  $points,
        float  $max,
        string $externallink,
        string $feedback
    ): array
    {
        $params = self::validate_parameters(
            self::execute_parameters(),
            array(
                'assignment_name' => $assignment_name,
                'user_name' => $user_name,
                'points' => $points,
                'max' => $max,
                'externallink' => $externallink,
                'feedback' => $feedback
            )
        );
        $external_username = self::customfieldid_username();
        $userid = self::get_user_id($params['user_name'], $external_username);
        if (!empty($userid)) {
            $data = self::read_grade($userid, $assignment_name);
            if (!empty($data)) {

                $grade = new grade();
                $grade->load($data);
                $grade->gradeexternal = $params['points'];
                $grade->externalfeedback = $params['feedback'];
                self::update_grade($grade);
            } else {
                echo 'WARNING: no assignment ' . $params['assignment_name'] . ' found';
                // TODO: Error and status 404
            }
        } else {
            echo 'WARNING: no username ' . $params['user_name'] . ' found';
            // TODO: Error and status 404
        }


        return array();
    }

    /**
     * returns the id of the custom field for the external username
     */

    private static function customfieldid_username()
    {
        global $DB;

        $custom_field = $DB->get_record(
            'user_info_field',
            array('shortname' => get_config('local_gradeassignments', 'external_username')),
            'id,shortname');
        return $custom_field->id;
    }

    /**
     * returns the moodle userid by the external username
     * @param $user_name string the external username
     * @param $fieldid  int the id of the custom field for external username
     * @return int  the moodle-userid
     * @throws \dml_exception
     */
    private static function get_user_id($user_name, $fieldid)
    {
        global $DB;
        $query = 'SELECT userid' .
            '  FROM {user_info_data}' .
            ' WHERE fieldid=:fieldid' .
            '   AND data=:ghusername';
        $user = $DB->get_record_sql(
            $query,
            [
                'fieldid' => $fieldid,
                'ghusername' => $user_name
            ]
        );
        if (!empty($user)) return $user->userid;
        else return null;
    }

    /**
     * reads the grade using the assignment-name and userid
     *
     * @param int $userid
     * @param string $assignment_name
     * @return object|null
     * @throws \dml_exception
     */
    private static function read_grade(int $userid, string $assignment_name): ?object
    {
        global $DB;

        $query =
            'SELECT ue.id as enroleid, ue.userid, en.id, en.courseid,' .
            '       ap.id AS assignmentid, ap.name, ap.course AS courseid, ap.coursemodule, ap.externalgrademax, ap.externalname, ' .
            '       ag.id AS gradeid, ag.externalgrade, ag.externalfeedback, ag.manualgrade, ag.manualfeedback' .
            '  FROM mdl_user_enrolments AS ue' .
            '  JOIN mdl_enrol AS en ON (ue.enrolid = en.id)' .
            '  JOIN mdl_assignexternal AS ap ON (ap.course = en.courseid)' .
            '  LEFT JOIN mdl_assignexternal_grades AS ag ON (ag.assignexternal = ap.id)' .
            ' WHERE ue.userid=:userid AND ap.externalname=:assignment_name' .
            ' ';
        $data = $DB->get_records_sql(
            $query,
            [
                'userid' => $userid,
                'assignment_name' => $assignment_name
            ]
        );
        return current($data);
    }

    /**
     * updates the grade for a programming assignment
     * @param grade $grade
     * @return void
     * @throws \dml_exception
     */
    private static function update_grade(grade $grade) {
        global $DB;
        var_dump($grade);
        if (empty($grade->id)) {
            $DB->insert_record('assignexternal_grades', $grade);
        } else {
            $DB->update_record('assignexternal_grades', $grade);
        }
    }
}