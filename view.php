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

use mod_assignprogram\data\grade;
use mod_assignprogram\output\view_feedback;
use mod_assignprogram\output\view_grading;
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
} elseif ($urlparams['action'] == 'grading') {
    show_grading($output, $context, $cmid);
}
echo $output->footer();

function show_details($output, $context, $cmid)
{
    global $PAGE;
    global $DB;
    global $CFG;
    global $USER;

    $PAGE->set_url('/mod/assignprogram/view.php', array('id' => $cmid));
    $PAGE->set_title('My title');  // FIXME
    $PAGE->set_heading('My modules page heading');
    $PAGE->set_pagelayout('standard');

    echo $output->header();

    $renderable = new view_link($cmid);
    echo $output->render($renderable);

    if (has_capability('mod/assign:reviewgrades', $context)) {
        $renderable = new view_summary($cmid);
        echo $output->render($renderable);
    } else {
        require_once($CFG->dirroot . '/mod/assignprogram/classes/data/grade.php');
        error_log("cmid=$cmid / userid=$USER->id");
        $gradedata = $DB->get_record(
            'assignprogram_grades',
            array('assignment'=>$cmid, 'userid'=>$USER->id),
            '*'
        );
        $grade = new grade();
        if ($gradedata) {
            error_log('data found');
            $grade->init($gradedata);
        }
        $renderable = new view_feedback(
            $grade->feedbackexternal,
            $grade->gradeexternal,
            $grade->feedbackmanual,
            $grade->grademanual,
            100 // FIXME
        );
        echo $output->render($renderable);
    }

}

function show_grading($output, $context, $cmid) {
    global $PAGE;
    global $DB;
    global $CFG;
    global $USER;
    require_capability('mod/assign:reviewgrades', $context);
    $PAGE->set_url('/mod/assignprogram/view.php', array('id' => $cmid));
    $PAGE->set_title('My title');  // FIXME
    $PAGE->set_heading('My modules page heading');
    $PAGE->set_pagelayout('base');

    echo $output->header();

    $renderable = new view_grading($cmid,$context);
    echo $output->render($renderable);

}