<?php

namespace mod_assignprogram\controller;

use cm_info;
use core\context;
use mod_assignprogram\data\assign;
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
    private $coursemoduleid;
    /** @var context the context of the course module for this grade instance
     *               (or just the course if we are creating a new one)
     */
    private context $context;

    /** @var assign $assign  the assignprogram instance this grade belongs to*/
    private assign $assign;

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
        $this->assign = new assign(null,$coursemoduleid);
        $this->userlist = $this->read_coursemodule_students();
        if ($userid == 0) {
            reset($this->userlist);
            $this->userid = key($this->userlist);
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
        $gradelist = array();
        foreach ($this->userlist as $userid => $user) {
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
        $data = new \stdClass();

        $assignment = new assign(null,$this->coursemoduleid);
        $data->userid = $this->userid;
        $data->assignmentid = $this->coursemoduleid;
        $data->firstname = $user->firstname;
        $data->lastname = $user->lastname;
        $data->externalgrademax = $assignment->externalgrademax;
        $data->manualgrademax = $assignment->manualgrademax;
        $data->gradeid = -1;
        $data->assignprogram = -1;
        $data->status = 'pending';
        $data->timeleft = 'FIXME';
        $data->gradeexternal = '';
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

            if (array_key_exists($this->userid, $grades)) {
                $gradedata = $grades[$this->userid];
                $data->gradeid = $gradedata->id;
                $data->assignprogram = $gradedata->assignprogram;
                $data->status = $this->get_status($gradedata->externalgrade);
                $data->timeleft = 'FIXME';
                $data->externalgrade = $gradedata->externalgrade;
                $data->externalfeedback['text'] = $gradedata->externalfeedback;
                $data->externalfeedback['format'] = 1; // FIXME
                $data->manualgrade = $gradedata->manualgrade;
                $data->manualfeedback['text'] = $gradedata->manualfeedback;
                $data->manualfeedback['format'] = 1; // FIXME
                $data->gradefinal = $gradedata->gradeexternal + $gradedata->manualgrade;

            }
            $mform->set_data($data);
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