<?php

namespace mod_assignexternal\output;
use core\context;
use mod_assignexternal\controller\grade_control;
use renderable;
use renderer_base;
use templatable;
use stdClass;
class view_grading  implements renderable, templatable
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
    public function __construct($coursemoduleid, $context) {
        $this->coursemoduleid = $coursemoduleid;
        $this->context = $context;
    }

    /**
     * Export this data, so it can be used as the context for a mustache template.
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): \stdClass {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/grade_control.php');
        $data = new stdClass();
        $grade_control = new grade_control($this->coursemoduleid, $this->context);
        $data->grades = $grade_control->list_grades();
        return $data;
    }
}