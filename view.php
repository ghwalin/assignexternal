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
 * Activity view page for the mod_assignprogram plugin.
 *
 * @package   mod_assignprogram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_assignprogram\output\view_link;
use mod_assignprogram\output\view_summary;
use mod_assignprogram\output\renderer;

require_once('../../config.php');
global $PAGE;

$cmid = required_param('id', PARAM_INT);

list ($course, $cm) = get_course_and_cm_from_cmid($cmid, 'assignprogram');
require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/assign:view', $context);

/*
mod/assign:grade
mod/assign:reviewgrades
 */
$urlparams = array('cmid' => $cmid,
    'action' => optional_param('action', '', PARAM_ALPHA),
    'rownum' => optional_param('rownum', 0, PARAM_INT));

$output = $PAGE->get_renderer('mod_assignprogram');

if ($urlparams['action'] == '') {
    show_details($output, $context, $cmid);
} elseif ($urlparams['action'] == '') {
    show_grading($output, $context);
}
echo $output->footer();

function show_details($output, $context, $cmid)
{
    global $PAGE;
    $PAGE->set_url('/mod/assignprogram/view.php', array('id' => $cm->id));
    $PAGE->set_title('My title');
    $PAGE->set_heading('My modules page heading');
    $PAGE->set_pagelayout('standard');

    echo $output->header();

    $renderable = new view_link($cm);
    echo $output->render($renderable);

    if (has_capability('mod/assign:reviewgrades', $context)) {
        $renderable = new view_summary($cmid);
        echo $output->render($renderable);
    }

}

function show_grading($output, $context) {

}