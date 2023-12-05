<?php

namespace mod_assignprogram\output;
use renderable;
use renderer_base;
use templatable;
use stdClass;
class view_page  implements renderable, templatable
{
    private $sometext = null;
    public function __construct($sometext) {
        $this->sometext = $sometext;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @return stdClass
     */
    public function export_for_template(renderer_base $output): stdClass {
        $data = new stdClass();
        $data->sometext = $this->sometext;
        return $data;
    }



}