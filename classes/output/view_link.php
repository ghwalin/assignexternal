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

use dml_exception;
use mod_assignexternal\data\assign;
use renderable;
use renderer_base;
use stdClass;
use templatable;

/**
 * Renderer for view_link
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_link implements renderable, templatable {
    /** @var string $externallink the link to the external assignment */
    private string $externallink;

    /**
     * default constructor
     * @param int $coursemoduleid
     * @throws dml_exception
     */
    public function __construct(int $coursemoduleid) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
        $assignment = new assign();
        $assignment->load_db($coursemoduleid);
        $this->externallink = $assignment->get_externallink();
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
