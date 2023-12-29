<?php

namespace mod_assignprogram\output;
use renderable;
use renderer_base;
use templatable;
use stdClass;
class view_link implements renderable, templatable
{
    private $externallink = null;

    /**
     * default constructor
     * @param $course_module
     */
    public function __construct($course_module) {
        $this->externallink = $course_module->externallink;
    }

    /**
     * Export this data, so it can be used as the context for a mustache template.
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->externallink = $this->externallink;
        return $data;
    }
}