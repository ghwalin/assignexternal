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

use core\context;
use dml_exception;
use mod_assignexternal\controller\grade_control;
use mod_assignexternal\data\assign;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderer for summary
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_summary implements renderable, templatable {
    /** @var int $coursemoduleid the unique identifier for the current coursemodule */
    private int $coursemoduleid;
    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private $context;

    /**
     * default constructor
     * @param int $coursemoduleid
     * @param context $context
     */
    public function __construct(int $coursemoduleid, context $context) {
        $this->coursemoduleid = $coursemoduleid;
        $this->context = $context;
    }

    /**
     * Export this data, so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     * @throws dml_exception
     */
    public function export_for_template(renderer_base $output): stdClass {
        global $CFG;

        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
        $assignment = new assign();
        $assignment->load_db($this->coursemoduleid);

        require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/grade_control.php');
        $gradecontrol = new grade_control($this->coursemoduleid, $this->context);

        $data = new stdClass();
        $data->link_grading = "view.php?id=$this->coursemoduleid&action=grading";
        $data->link_grader = "view.php?id=$this->coursemoduleid&action=grader";
        $data->hidden = 'TODO hidden';
        $data->student_count = $gradecontrol->count_coursemodule_students();
        $data->graded_count = $gradecontrol->count_grades();

        $timeremaining = $assignment->get_duedate() - time();
        if ($timeremaining <= 0) {
            $due = get_string('assignmentisdue', 'assignexternal');
        } else {
            $due = format_time($timeremaining);
        }
        $data->timeremaining = $due;

        return $data;
    }

}
