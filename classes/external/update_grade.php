<?php


namespace mod_assignexternal\external;
global $CFG;
require_once("$CFG->dirroot/lib/externallib.php");

use external_function_parameters;
use external_single_structure;
use external_value;
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
        return
        new external_single_structure([
            'type' => new external_value(PARAM_TEXT, 'info, warning, error'),
            'name' => new external_value(PARAM_TEXT, 'the name of this warning'),
            'message' => new external_value(PARAM_TEXT, 'warning message')
        ]);
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
            $assignment = self::read_assignment($assignment_name, $userid);
            if (empty($assignment->getId())) {
                echo 'WARNING: no assignment ' . $params['assignment_name'] . ' found';
                return self::generate_warning(
                    'error',
                    'no_assignment',
                    'No assignment with name "'. $params['assignment_name']. '" found. Contact your teacher.'
                );
                // TODO: Error and status 404
            } elseif ($assignment->getCutoffdate() < time()) {
                echo 'WARNING: the assignment is overdue, points/feedback not updated';
                return self::generate_warning(
                    'info',
                    'overdue',
                    'The assignment is overdue, points/feedback not updated'
                );
            } else {
                self::update_grade($assignment->getId(), $userid, $params);
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
     * returns the id of the custom field for the external username
     */

    private static function customfieldid_username(): int
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
     * @param string $user_name  the external username
     * @param int $fieldid  the id of the custom field for external username
     * @return int  the moodle-userid
     * @throws \dml_exception
     */
    private static function get_user_id(string $user_name, int $fieldid): ?int
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
     * reads the assignment data
     * @param string $assignmentname
     * @param int $userid
     * @return assign
     * @throws \dml_exception
     */
    private static function read_assignment(string $assignmentname, int $userid): assign
    {

        $assignment = new assign();
        $assignment->load_db_external($assignmentname, $userid);
        return $assignment;
    }
    /**
     * reads the grade using the assignment-name and userid
     *
     * @param int $userid
     * @param int $coursemoduleid
     * @return object|null
     * @throws \dml_exception
     */
    private static function read_grade(int $coursemoduleid, int $userid): ?object
    {
        global $DB;

        $data = $DB->get_record(
            'assignexternal_grades',
            [
                'userid' => $userid,
                'assignexternal' => $coursemoduleid
            ]
        );
        if (!$data) {
            return null;
        }
        return $data;
    }

    /**
     * updates the grade for a programming assignment
     * @param int $assignmentid
     * @param int $userid
     * @param array $params
     * @return void
     * @throws \dml_exception
     */
    private static function update_grade(int $assignmentid, int $userid, array $params): void
    {
        global $DB;
        $grade = new grade();
        $grade->load_db($assignmentid, $userid);
        $grade->setAssignexternal($assignmentid);
        $grade->setUserid($userid);
        $grade->setExternalgrade($params['points']);
        $feedback = urldecode($params['feedback']);
        $grade->setExternalfeedback(format_text($feedback, FORMAT_MARKDOWN));
        $grade->setExternallink($params['externallink']);
        if (empty($grade->getId())) {
            $DB->insert_record('assignexternal_grades', $grade->to_stdClass());
        } else {
            $DB->update_record('assignexternal_grades', $grade->to_stdClass());
        }
    }

    private static function generate_warning(string $type, string $name, string $message): array
    {
        return[
            'type' => $type,
            'name' => $name,
            'message' => $message
        ];
    }
}