<?php

namespace mod_assignprogram\controller;

use cm_info;
use core\context;
use mod_assignprogram\data\Grade;
use mod_assignprogram\form\grader_form;
use moodle_url;
use stdClass;

/**
 * Controller for grading
 *
 * @package   mod_assignprogram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_control
{
    /** @var int  the coursemodule-id */
    private $coursemoduleid = null;
    /** @var context the context of the course module for this grade instance
     *               (or just the course if we are creating a new one)
     */
    private context $context;
    /** @var string A key used to identify userlists created by this object. */
    private $userlist = null;

    /** @var string The key to identify the user */
    private $userid;

    /**
     * default constructor
     * @param $coursemoduleid
     * @param $context
     * @param $userid
     */
    public function __construct($coursemoduleid, $context, $userid = 0)
    {
        global $CFG;
        require_once($CFG->libdir . '/modinfolib.php');
        $this->coursemoduleid = $coursemoduleid;
        $this->context = $context;
        $this->userlist = $this->read_coursemodule_students();
        if ($userid == 0) {
            $students = $this->read_coursemodule_students();
            reset($students);
            $this->userid = key($students);
        } else {
            $this->userid = $userid;
        }

    }

    /**
     * creates a list of all users and grades/feedback
     * @return array list of users and grades/feedback
     */
    public function list_grades()
    {
        $grades = $this->read_grades();
        $users = $this->read_coursemodule_students();
        $gradelist = array();
        foreach ($users as $userid => $user) {
            $grade = new \stdClass();
            $grade->coursemoduleid = $this->coursemoduleid;
            $grade->userid = $userid;
            $grade->firstname = $user->firstname;
            $grade->lastname = $user->lastname;
            if (array_key_exists($userid, $grades)) {
                $gradedata = $grades[$userid];
                $grade->status = $this->get_status($gradedata->gradeexternal);
                $grade->gradeexternal = $gradedata->gradeexternal;
                $grade->manualgrade = $gradedata->manualgrade;
                $grade->feedback = $gradedata->externalfeedback . $gradedata->manualfeedback;
                $grade->gradefinal = $gradedata->gradeexternal + $gradedata->manualgrade;
            } else {
                $grade->status = 'pending';
            }
            $gradelist[] = $grade;
        }

        return $gradelist;
    }

    /**
     * process the feedback form for a student
     * @return void
     * @throws \dml_exception
     */
    public function process_feedback(): void
    {
        global $CFG;
        global $PAGE, $OUTPUT;
        require_once($CFG->dirroot . '/mod/assignprogram/classes/form/graderform.php');
        $user = $this->read_coursemodule_student($this->userid);

        $assignment = new \stdClass();

        $assignment->userid = $this->userid;
        $assignment->assignmentid = $this->coursemoduleid;
        $assignment->firstname = $user->firstname;
        $assignment->lastname = $user->lastname;
        $assignment->gradeexternalmax = 99;  // FIXME
        $assignment->manualgrademax = 99;  // FIXME

        $mform = new grader_form(null, $assignment);

// Form processing and displaying is done here.
        if ($mform->is_cancelled()) {
            error_log('Cancelled');
        } else if ($formdata = $mform->get_data()) {
            global $DB;
            require_once($CFG->dirroot . '/mod/assignprogram/classes/data/grade.php');
            $grade = new grade();
            $grade->init($formdata);
            if ($grade->id == -1)
                $result = $DB->insert_record('assignprogram_grades', $grade);
            else
                $result = $DB->update_record('assignprogram_grades', $grade);

            redirect(new moodle_url('view.php',
                array(
                    'id' => $this->coursemoduleid,
                    'action' => 'grader',
                    'userid' => $this->userid
                )));
        } else {
            $grades = $this->read_grades();
            $grade = new \stdClass();
            if (array_key_exists($this->userid, $grades)) {
                $gradedata = $grades[$this->userid];
                $grade->gradeid = $gradedata->id;
                $grade->status = $this->get_status($gradedata->gradeexternal);
                $grade->timeleft = 'FIXME';
                $grade->gradeexternal = $gradedata->gradeexternal;
                $grade->externalfeedback['text'] = $gradedata->externalfeedback;
                $grade->externalfeedback['format'] = 1; // FIXME
                $grade->manualgrade = $gradedata->manualgrade;
                $grade->manualfeedback['text'] = $gradedata->manualfeedback;
                $grade->manualfeedback['format'] = 1; // FIXME
                $grade->gradefinal = $gradedata->gradeexternal + $gradedata->manualgrade;
            } else {
                $grade->gradeid = -1;
                $grade->status = 'pending';
                $grade->timeleft = 'FIXME';
                $grade->gradeexternal = '';
                $grade->manualgrade = '';
                $grade->externalfeedback['text'] = '';
                $grade->externalfeedback['format'] = 1; // FIXME
                $grade->manualfeedback['text'] = '<p>Nothing here</p>';
                $grade->manualfeedback['format'] = 1;
                $grade->gradefinal = 0;
            }
            $mform->set_data($grade);

            // Display the form.
            $PAGE->set_title("foobar");
            //$PAGE->add_body_class('limitedwidth');
            $PAGE->set_heading("foofoo");

            echo $OUTPUT->header();

            $mform->display();

            echo $OUTPUT->footer();
        }
    }

    /**
     * reads all grades for the current coursemodule
     * @return array list of grades
     * @throws \dml_exception
     */
    private function read_grades()
    {
        error_log(var_export($this->context, true));
        global $DB;
        $grades = $DB->get_records_list(
            'assignprogram_grades',
            'assignprogram',
            array($this->coursemoduleid)
        );
        $gradelist = array();
        foreach ($grades as $grade) {
            $gradelist[$grade->userid] = $grade;
        }
        return $gradelist;
    }

    /**
     * reads the data of a user enrolled in this course
     * @param $userid
     * @return stdClass
     */
    private function read_coursemodule_student($userid): \stdClass
    {
        $users = get_enrolled_users(
            $this->context,
            'mod/assign:submit',
            0,
            'u.id, u.firstname, u.lastname'
        );
        foreach ($users as $user) {
            if ($user->id == $userid)
                return $user;
        }
        return new \stdClass();
    }

    /**
     * reads all students for the coursemodule
     * @return array of students
     */
    private function read_coursemodule_students(): array
    {
        $userlist = array();
        $users = get_enrolled_users(
            $this->context,
            'mod/assign:submit',
            0,
            'u.id, u.firstname, u.lastname'
        );
        foreach ($users as $user) {
            $userlist[$user->id] = $user;
        }
        return $userlist;
    }

    /**
     * get the status of the students assignment
     * @param $grade
     * @return string
     */
    private function get_status($grade)
    {
        if (!$grade) {
            return 'pending';
        } else {
            return 'done';
        }
    }
}