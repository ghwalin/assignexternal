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

/**
 * redirect from gradebook
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
global $DB;

$id = required_param('id', PARAM_INT);
$coursemodule = get_coursemodule_from_id('assignexternal', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $coursemodule->course], '*', MUST_EXIST);
$assignment = $DB->get_record('assignexternal', ['id' => $coursemodule->instance], '*', MUST_EXIST);

require_login($course, false, $coursemodule);
$modulecontext = context_module::instance($coursemodule->id);

// Re-direct the user.
if (has_capability('mod/assign:manage', $modulecontext)) {
    $url = new moodle_url('reports.php', ['courseid' => $coursemodule->course,
        'id' => $assignment->id]);
} else {
    $url = new moodle_url('view.php', ['id' => $id]);
}
redirect($url);