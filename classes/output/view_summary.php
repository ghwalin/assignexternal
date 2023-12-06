<?php

namespace mod_assignprogram\output;
use renderable;
use renderer_base;
use templatable;
use stdClass;
class view_summary  implements renderable, templatable
{
    private $cmid = null;
    public function __construct($cmid) {
        $this->cmid = $cmid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->link_grading = "view.php?id=$this->cmid&action=grading";
        $data->link_grader = "view.php?id=$this->cmid&action=grader";
        return $data;
    }

}