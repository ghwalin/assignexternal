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
require_once("$CFG->dirroot/lib/externallib.php");

use context_course;
use core_external\restricted_context_exception;
use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use invalid_parameter_exception;
use mod_assign_external;
use required_capability_exception;
use stdClass;

/**
 * webservice to update the externalgrade and externalfeedback
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class read_students extends external_api {
    /**
     * creates the return structure
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure|external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'userid' => new external_value(PARAM_INT, 'user-id'),
                'firstname' => new external_value(PARAM_TEXT, 'firstname'),
                'lastname' => new external_value(PARAM_TEXT, 'lastname'),
                'email' => new external_value(PARAM_TEXT, 'email address'),
            ])
        );
    }

    /**
     * execute the service
     * @param int $courseid
     * @throws restricted_context_exception
     * @throws invalid_parameter_exception
     * @throws required_capability_exception
     */
    public static function execute(
        int $courseid
    ): array {
        $params = self::validate_parameters(
            self::execute_parameters(),
            ['courseid' => $courseid]
        );
        $context = context_course::instance($courseid);
        self::validate_context($context);
        require_capability('mod/assign:reviewgrades', $context);
        $users = get_enrolled_users($context, 'mod/assign:submit');
        $students = [];
        foreach ($users as $user) {
            $student = new stdClass();
            $student->firstname = $user->firstname;
            $student->lastname = $user->lastname;
            $student->userid = $user->id;
            $student->email = $user->email;
            $students[] = (array)$student;
        }

        return $students;

    }

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            ['courseid' => new external_value(PARAM_INT, 'id of the course')]
        );
    }

}
