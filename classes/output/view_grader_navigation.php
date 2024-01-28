<?php

namespace mod_assignexternal\output;

use core\context;
use mod_assignexternal\controller\grade_control;
use renderable;
use renderer_base;
use templatable;

/**
 * Renderer for view_grader_navigation
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_grader_navigation implements renderable, templatable
{
    /** @var int @var the id of the coursemodule
     *
     */
    private $coursemoduleid = null;
    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private $context;

    /**
     * default constructor
     * @param $coursemoduleid
     * @param $context
     */
    public function __construct($coursemoduleid, $context)
    {
        $this->coursemoduleid = $coursemoduleid;
        $this->context = $context;
    }

    /**
     * Export this data, so it can be used as the context for a mustache template.
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): \stdClass
    {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/grade_control.php');

        $grade_control = new grade_control($this->coursemoduleid, $this->context);
        $user = $grade_control->read_coursemodule_student($grade_control->get_userid());

        $data = new \stdClass();
        $data->grades = $grade_control->list_grades();
        $data->courseid = $this->context->get_course_context()->instanceid;
        //$data->coursename = $this->context->get_course_context()->get_context_name();  // TODO is this used at all?
        $data->cmid = $this->coursemoduleid;
        $data->name = $this->context->get_context_name();
        $data->userid = $grade_control->get_userid();
        $data->firstname = $user->firstname;
        $data->lastname = $user->lastname;
        $data->email = $user->email;
        $data->duedate = $grade_control->get_assign()->duedate;
        return $data;
    }
}