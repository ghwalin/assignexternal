<?php

namespace mod_assignprogram\output;
use renderable;
use renderer_base;
use templatable;
use stdClass;
class view_feedback  implements renderable, templatable
{
    private $feedback_external = null;
    private $grade_external = 0;
    private $feedback_manual = null;
    private $grade_manual = 0;
    private $grade_max = null;
    public function __construct(
        $feedback_external,
        $grade_external,
        $feedback_manual,
        $grade_manual,
        $grade_max
    ) {
        $this->feedback_external = $feedback_external;
        $this->feedback_manual=  $feedback_manual;
        $this->grade_external = $grade_external;
        $this->grade_manual = $grade_manual;
        $this->grade_max = $grade_max;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->feedback = $this->feedback_external . $this->feedback_manual;
        $data->grade = $this->grade_external + $this->grade_manual;
        $data->grade_max = $this->grade_max;
        return $data;
    }

}