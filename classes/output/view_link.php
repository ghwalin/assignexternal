<?php

namespace mod_assignexternal\output;
use mod_assignexternal\data\assign;
use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Renderer for view_link
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_link implements renderable, templatable
{
    private $externallink = null;

    /**
     * default constructor
     * @param $coursemoduleid
     * @throws \dml_exception
     */
    public function __construct($coursemoduleid) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
        $assignment = new assign();
        $assignment->load_db($coursemoduleid);
        $this->externallink = $assignment->getExternallink();
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