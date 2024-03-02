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
namespace mod_assignexternal\output;

use coding_exception;
use core\context;
use mod_assignexternal\controller\grade_control;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderer for view_grader_navigation
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_grader_navigation implements renderable, templatable {
    /** @var int @var the id of the coursemodule */
    private int $coursemoduleid;
    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private context $context;

    /** @var int the userid of the currently selected user */
    private int $userid;

    /**
     * default constructor
     * @param $coursemoduleid
     * @param $context
     */
    public function __construct(int $coursemoduleid, context $context, int $userid) {
        $this->coursemoduleid = $coursemoduleid;
        $this->context = $context;
        $this->userid = $userid;
    }

    /**
     * Export this data, so it can be used as the context for a mustache template.
     * @param renderer_base $output
     * @return stdClass
     * @throws coding_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/grade_control.php');

        $gradecontrol = new grade_control($this->coursemoduleid, $this->context);
        $users = $gradecontrol->read_coursemodule_students($this->userid);
        $user = reset($users);

        $data = new stdClass();
        $data->grades = $gradecontrol->list_grades();
        $data->courseid = $this->context->get_course_context()->instanceid;
        //TODO is this used at all? $data->coursename = $this->context->get_course_context()->get_context_name();
        $data->cmid = $this->coursemoduleid;
        $data->name = $this->context->get_context_name();
        $data->userid = $gradecontrol->get_userid();
        $data->firstname = $user->firstname;
        $data->lastname = $user->lastname;
        $data->email = $user->email;
        $data->duedate = $gradecontrol->get_assign()->get_duedate();
        return $data;
    }
}
