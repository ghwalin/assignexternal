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
 * Web service for mod assignprogram
 * @package    mod_assignprogram
 * @subpackage db
 * @copyright  2023 Marcel Suter <marcel@ghwalin.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
    'mod_assign_submit_grading_form' => array(
        'classname'     => 'grade_control',
        'methodname'    => 'submit_grader_form',
        'classpath'     => 'mod/assignprogram/classes/grade_control.php',
        'description'   => 'Submit the grading form data via ajax',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'mod/assign:grade',
        'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
    ),
);