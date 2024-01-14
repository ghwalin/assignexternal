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
 * Activity view page for the mod_assignexternal plugin.
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_assignexternal\controller\grade_control;
use mod_assignexternal\data\grade;
use mod_assignexternal\output\view_grader_navigation;
use mod_assignexternal\output\view_grading;
use mod_assignexternal\output\view_link;
use mod_assignexternal\output\view_summary;
use mod_assignexternal\output\renderer;

require_once('../../config.php');
global $PAGE;

$coursemoduleid = required_param('id', PARAM_INT);

list ($course, $coursemodule) = get_course_and_cm_from_cmid($coursemoduleid, 'assignexternal');
require_login($course, true, $coursemodule);
$context = context_module::instance($coursemodule->id);
require_capability('mod/assign:view', $context);

$urlparams = array('cmid' => $coursemoduleid,
    'action' => optional_param('action', '', PARAM_ALPHA),
    'userid' => optional_param('userid', 0, PARAM_INT)
);



if ($urlparams['action'] == '') {
    show_details($context, $coursemoduleid);
} elseif ($urlparams['action'] == 'grading') {
    show_grading($context, $coursemoduleid);
} elseif ($urlparams['action'] == 'grader') {
    show_grader($context, $coursemoduleid, $urlparams['userid']);
}


/**
 * shows the details for the programming assignment
 * @param $context
 * @param $coursemoduleid
 * @return void
 * @throws coding_exception
 * @throws dml_exception
 */
function show_details($context, $coursemoduleid): void
{
    global $PAGE;
    global $DB;
    global $CFG;
    global $USER;

    $PAGE->set_url('/mod/assignexternal/view.php', array('id' => $coursemoduleid));
    $PAGE->set_title('My title');  // FIXME
    $PAGE->set_heading('My modules page heading');
    $PAGE->set_pagelayout('standard');

    $output = $PAGE->get_renderer('mod_assignexternal');
    echo $output->header();

    $renderable = new view_link($coursemoduleid);
    echo $output->render($renderable);

    if (has_capability('mod/assign:reviewgrades', $context)) {
        $renderable = new view_summary($coursemoduleid);
        echo $output->render($renderable);
    } else {
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/grade.php');
        $gradedata = $DB->get_record(
            'assignexternal_grades',
            array('assignment' => $coursemoduleid, 'userid' => $USER->id),
            '*'
        );
        $grade = new grade();
        if ($gradedata) {
            $grade->init($gradedata);
        }
        $renderable = new view_summary(
            $coursemoduleid
        );
        echo $output->render($renderable);
    }
    echo $output->footer();
}

/**
 * shows the grading overview
 * @param $context
 * @param $coursemoduleid
 * @return void
 * @throws coding_exception
 * @throws required_capability_exception
 */
function show_grading($context, $coursemoduleid): void
{
    global $PAGE;
    require_capability('mod/assign:reviewgrades', $context);

    $PAGE->set_url('/mod/assignexternal/view.php', array('id' => $coursemoduleid));
    $PAGE->set_title('My title');  // FIXME
    $PAGE->set_heading('My modules page heading');
    $PAGE->set_pagelayout('base');
    $PAGE->add_body_class('assignexternal-grading');
    $output = $PAGE->get_renderer('mod_assignexternal');
    echo $output->header();

    $renderable = new view_grading($coursemoduleid, $context);
    echo $output->render($renderable);
    echo $output->footer();

}

/**
 * shows the grades and feedbacks
 * @param $output
 * @param $context
 * @param $coursemoduleid
 * @param $userid
 * @return void
 * @throws coding_exception
 * @throws required_capability_exception
 * @throws dml_exception
 */
function show_grader($context, $coursemoduleid, $userid): void
{
    global $CFG;
    global $PAGE;

    require_once($CFG->dirroot . '/mod/assignexternal/classes/controller/grade_control.php');

    require_capability('mod/assign:reviewgrades', $context);
    $PAGE->set_url(
        '/mod/assignexternal/view.php',
        array(
            'id' => $coursemoduleid,
            'action' => 'grader',
            'userid' => $userid
        )
    );
    $PAGE->set_url('/mod/assignexternal/view.php', array('id' => $coursemoduleid));
    $PAGE->set_title('My title');  // FIXME
    $PAGE->set_heading('My modules page heading');
    $PAGE->set_pagelayout('base');
    $PAGE->add_body_class('assignexternal-grading');
    $output = $PAGE->get_renderer('mod_assignexternal');
    echo $output->header();

    $renderable = new view_grader_navigation($coursemoduleid, $context);
    echo $output->render($renderable);

    $grade_control = new grade_control($coursemoduleid, $context, $userid);
    $grade_control->process_feedback();

    echo $output->footer();
}