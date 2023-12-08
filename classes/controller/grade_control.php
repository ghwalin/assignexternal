<?php

namespace mod_assignprogram\controller;

use cm_info;
use core\context;
use mod_assignprogram\data\Grade;
use stdClass;

class grade_control
{
    /** @var int  the coursemodule-id */
    private $cmid = null;
    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private $context;
    /** @var string A key used to identify userlists created by this object. */
    private $userlist = null;


    /**
     * default constructor
     */
    public function __construct($cmid, $context)
    {
        global $CFG;
        require_once($CFG->libdir . '/modinfolib.php');
        $this->cmid = $cmid;
        $this->context = $context;
        $this->userlist = $this->read_cm_students($context);
    }

    /**
     * creates a list of all users and grades/feedback
     * @return array list of users and grades/feedback
     */
    public function list_grades()
    {
        $grades = $this->read_grades();
        $users = $this->read_cm_students();
        $gradelist = array();
        foreach ($users as $userid => $user) {
            $grade = new \stdClass();

            $grade->firstname = $user->firstname;
            $grade->lastname = $user->lastname;
            if (array_key_exists($userid, $grades)) {
                $gradedata = $grades[$userid];
                $grade->status = $this->get_status($gradedata->gradeexternal);
                $grade->gradeexternal = $gradedata->gradeexternal;
                $grade->grademanual = $gradedata->grademanual;
                $grade->feedback = $gradedata->feedbackexternal . $gradedata->feedbackmanual;
                $grade->gradefinal = $gradedata->gradeexternal + $gradedata->grademanual;
            } else {
                $grade->status = 'pending';
            }
            $gradelist[] = $grade;
        }

        return $gradelist;
    }

    /**
     * reads all grades for the current coursemodule
     * @return array list of grades
     */
    private function read_grades()
    {
        global $DB;
        $grades = $DB->get_records_list(
            'assignprogram_grades',
            'assignment',
            array($this->cmid)
        );
        $gradelist = array();
        foreach ($grades as $grade) {
            $gradelist[$grade->userid] = $grade;
        }
        return $gradelist;
    }

    /**
     * reads all students for the coursemodule
     * @return array of students
     */
    private function read_cm_students()
    {
        global $DB;
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
    private function get_status($grade) {
        if (!$grade) {
            return 'pending';
        } else {
            return 'done';
        }
    }
}