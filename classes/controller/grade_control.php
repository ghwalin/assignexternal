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
namespace mod_assignexternal\controller;

use cm_info;
use coding_exception;
use core\context;
use dml_exception;
use mod_assignexternal\data\assign;
use mod_assignexternal\data\Grade;
use mod_assignexternal\data\override;
use mod_assignexternal\form\grader_form;
use mod_assignexternal\form\override_form;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * Controller for grading
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_control {
    /** @var int  the coursemodule-id */
    private $coursemoduleid;

    /** @var int the course-id */
    private $courseid;

    /** @var context the context of the course module for this grade instance
     *               (or just the course if we are creating a new one)
     */
    private context $context;

    /** @var assign $assign the assignexternal instance this grade belongs to */
    private assign $assign;

    /** @var string A key used to identify userlists created by this object. */
    private $userlist;

    /** @var string The key to identify the user */
    private $userid;

    /**
     * default constructor
     * @param $coursemoduleid
     * @param $context
     * @param $userid
     */
    public function __construct($coursemoduleid, $context, $userid = 0) {
        global $CFG;
        require_once($CFG->libdir . '/modinfolib.php');
        $this->coursemoduleid = $coursemoduleid;
        $this->courseid = $context->get_course_context()->instanceid;
        $this->context = $context;
        $this->assign = new assign();
        $this->assign->load_db($coursemoduleid);
        $this->userlist = $this->read_coursemodule_students();
        if ($userid == 0) {
            reset($this->userlist);
            $this->userid = key($this->userlist);
        } else {
            $this->userid = $userid;
        }

    }

    /**
     * reads the students for the coursemodule filtered by userid(s)
     * @param mixed $filter
     * @return array of students
     */
    public function read_coursemodule_students(mixed $filter = null): array {
        if ($filter != null && !is_array($filter)) {
            $filter = [$filter];
        }
        $userlist = [];
        $users = get_enrolled_users(
            $this->context,
            'mod/assign:submit',
            0,
            'u.id, u.firstname, u.lastname, u.email'
        );
        foreach ($users as $user) {
            if ($filter == null || in_array($user->id, $filter))
                $userlist[$user->id] = $user;
        }
        return $userlist;
    }

    /**
     * creates a list of all users and grades/feedback
     * @return array list of users and grades/feedback
     */
    public function list_grades() {
        $grades = $this->read_grades();
        $gradelist = [];
        foreach ($this->userlist as $userid => $user) {
            $grade = new stdClass();
            $grade->courseid = $this->courseid;
            $grade->coursemoduleid = $this->coursemoduleid;
            $grade->userid = $userid;
            $grade->firstname = $user->firstname;
            $grade->lastname = $user->lastname;
            if (array_key_exists($userid, $grades)) {
                $gradedata = $grades[$userid];
                $grade->status = $this->get_status($gradedata->externalgrade);
                $grade->externalgrade = $gradedata->externalgrade;
                $grade->manualgrade = $gradedata->manualgrade;
                $grade->gradefinal = $gradedata->externalgrade + $gradedata->manualgrade;
            } else {
                $grade->status = $this->get_status(null);
            }
            $gradelist[] = $grade;
        }

        return $gradelist;
    }

    /**
     * reads all grades for the current coursemodule
     * @return array list of grades
     * @throws dml_exception
     */
    private function read_grades() {
        global $DB;
        $grades = $DB->get_records_list(
            'assignexternal_grades',
            'assignexternal',
            [$this->coursemoduleid]
        );
        $gradelist = [];
        foreach ($grades as $grade) {
            $gradelist[$grade->userid] = $grade;
        }
        return $gradelist;
    }

    /**
     * get the status of the students assignment
     * @param $grade
     * @return string
     */
    private function get_status($grade): string {
        if (!$grade) {
            return get_string('pending', 'assignexternal');
        } else {
            return get_string('done', 'assignexternal');
        }
    }

    /**
     * process the feedback form for a student
     * @return void
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function process_feedback(): void {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/form/graderform.php');
        $users = $this->read_coursemodule_students([$this->userid]);
        $user = reset($users);
        $data = new stdClass();

        $assignment = new assign();
        $assignment->load_db($this->coursemoduleid);
        $data->id = $this->coursemoduleid;
        $data->userid = $this->userid;
        $data->assignmentid = $assignment->get_id();
        $data->courseid = $this->courseid;
        $data->firstname = $user->firstname;
        $data->lastname = $user->lastname;
        $data->externalgrademax = $assignment->get_externalgrademax();
        $data->manualgrademax = $assignment->get_manualgrademax();
        $data->gradeid = -1;
        $data->assignexternal = -1;
        $data->status = get_string('pending', 'assignexternal');

        // Time remaining.
        $timeremaining = $assignment->get_duedate() - time();
        $due = '';
        if ($timeremaining <= 0) {
            $due = get_string('assignmentisdue', 'assignexternal');
        } else {
            $due = get_string('timeremainingcolon', 'assignexternal', format_time($timeremaining));
        }
        $data->timeremainingstr = $due;

        $data->externalgrade = '';
        $data->manualgrade = '';
        $data->externallink = '';
        $data->externalfeedback['text'] = '';
        $data->externalfeedback['format'] = 1;
        $data->manualfeedback['text'] = '<p>Nothing here</p>';
        $data->manualfeedback['format'] = 1;
        $data->gradefinal = 0;
        $mform = new grader_form(null, $data);

        // Form processing and displaying is done here.
        if ($mform->is_cancelled()) {
            debugging('Cancelled');  // TODO reset the form.
        } else if ($formdata = $mform->get_data()) {
            global $DB;
            require_once($CFG->dirroot . '/mod/assignexternal/classes/data/grade.php');
            $grade = new grade();
            $grade->load_formdata($formdata);
            debugging(var_export($formdata,true));
            debugging(var_export($grade,true));
            if ($grade->get_id() == -1) {
                $grade->set_id($DB->insert_record('assignexternal_grades', $grade->to_stdclass()));
            } else {
                $result = $DB->update_record('assignexternal_grades', $grade->to_stdclass());
            }
            $this->grade_item_update($grade);

            redirect(
                new moodle_url('view.php',
                    [
                        'id' => $this->coursemoduleid,
                        'action' => 'grader',
                        'userid' => $this->userid,
                    ]
                )
            );
        } else {
            $grades = $this->read_grades();

            if (array_key_exists($this->userid, $grades)) {
                $gradedata = $grades[$this->userid];
                $data->gradeid = $gradedata->id;
                $data->assignexternal = $gradedata->assignexternal;
                $data->status = $this->get_status($gradedata->externalgrade);
                $data->externalgrade = $gradedata->externalgrade;
                $data->externalfeedback['text'] = $gradedata->externalfeedback;
                $data->externalfeedback['format'] = 1;
                $data->manualgrade = $gradedata->manualgrade;
                $data->manualfeedback['text'] = $gradedata->manualfeedback;
                $data->manualfeedback['format'] = 1;
                $data->gradefinal = $gradedata->externalgrade + $gradedata->manualgrade;

            }
            $mform->set_data($data);
            $mform->display();
        }
    }

    /**
     * Inserts or updates the grade for a user in grade_grades
     * @param grade $grade the grading data for this user
     * @return int
     */
    public function grade_item_update($grade): int {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');

        $gradevalues = new stdClass;
        $gradevalues->userid = $this->userid;
        $gradevalues->rawgrade = floatval($grade->get_externalgrade()) + floatval($grade->get_manualgrade());
        $link = new moodle_url('/mod/assignexternal/view.php',
            ['id' => $this->coursemoduleid]
        );
        $gradevalues->feedback = '<a href="' . $link->out(true) . '">' .
            get_string('seefeedback', 'assignexternal') . '</a>';
        $gradevalues->feedbackformat = 1;

        return grade_update(
            'mod/assignexternal',
            $this->courseid,
            'mod',
            'assignexternal',
            $this->assign->get_id(),
            0,
            $gradevalues);
    }

    /**
     * counts the number of grades
     * @return int
     * @throws dml_exception
     */
    public function count_grades(): int {
        $grades = $this->read_grades();
        return count($grades);
    }

    /**
     * process the override form
     * @param array $userids
     * @return void
     * @throws dml_exception
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function process_override(array $userids): void {
        global $CFG;
        global $PAGE, $OUTPUT;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/form/overrideform.php');

        $data = new stdClass();
        $assignment = new assign();
        $assignment->load_db($this->coursemoduleid);
        $data->id = $this->coursemoduleid;
        $data->assignmentid = $assignment->get_id();
        $data->courseid = $this->courseid;
        $data->allowsubmissionsfromdate = $assignment->get_allowsubmissionsfromdate();
        $data->duedate = $assignment->get_duedate();
        $data->cutoffdate = $assignment->get_duedate();
        $data->users = $this->read_coursemodule_students($userids);

        // Form processing and displaying is done here.
        $url = new moodle_url('/mod/assignexternal/view.php', ['action' => 'override']);
        $mform = new override_form($url->out(false), $data);
        if ($mform->is_cancelled()) {
            debugging('Cancelled');  // TODO reset the form.
        } else if ($formdata = $mform->get_data()) {
            require_once($CFG->dirroot . '/mod/assignexternal/classes/data/override.php');
            debugging(var_export($formdata->uid, true));
            foreach ($formdata->uid as $userid) {
                $override = new override();
                $override->set_assignexternal($formdata->id);
                $override->set_userid($userid);
                $override->set_allowsubmissionsfromdate($formdata->allowsubmissionsfromdate);
                $override->set_duedate($formdata->duedate);
                $override->set_cutoffdate($formdata->cutoffdate);
                $this->override_update($override);
            }
            $url = new moodle_url(
                '/mod/assignexternal/view.php',
                [
                    'id' => $formdata->id,
                    'action' => 'grading',
                ]
            );
            redirect($url);

        } else {
            $mform->set_data($data);
            $mform->display();
        }
    }

    /**
     * inserts or updates a user override
     * @param override $override
     * @return void
     * @throws dml_exception
     */
    private function override_update(override $override): void {
        global $DB;
        if ($record = $DB->get_record(
            'assignexternal_overrides',
            [
                'assignexternal' => $override->get_assignexternal(),
                'userid' => $override->get_userid(),
            ]
        )) {
            $override->set_id($record->id);
            $DB->update_record('assignexternal_overrides', $override->to_stdclass());
        } else {
            $DB->insert_record('assignexternal_overrides', $override->to_stdclass());
        }
    }

    /**
     * counts the students for the assignment
     * @return int
     */
    public function count_coursemodule_students(): int {
        $users = $this->read_coursemodule_students();
        return count($users);
    }

    /**
     * @return int gets the userid for this grade
     */
    public function get_userid(): int {
        return $this->userid;
    }

    /**
     * @return assign gets the assignment this grade belongs to
     */
    public function get_assign(): assign {
        return $this->assign;
    }
}
