<?php

namespace mod_assignprogram\data;

class grade
{
    public $id;
    public $assignment;
    public $userid;
    public $grader;
    public $gradeexternal;
    public $feedbackexternal;
    public $grademanual;
    public $feedbackmanual;

    public function __construct() {
        global  $USER;
        $this->id = null;
        $this->assignment = null;
        $this->userid = null;
        $this->grader = $USER->id;
        $this->feedbackexternal = '';
        $this->gradeexternal = 0;
        $this->feedbackmanual = '';
        $this->grademanual = 0;
    }
    public function init($formdata)
    {
        $this->id = $formdata->gradeid;
        $this->assignment = $formdata->id;
        $this->userid = $formdata->userid;
        //$this->grader = $formdata->grader;
        $this->gradeexternal = $formdata->gradeexternal;
        $this->feedbackexternal = $formdata->feedbackexternal['text'];
        $this->grademanual = $formdata->grademanual;
        $this->feedbackmanual = $formdata->feedbackmanual['text'];
    }

    public function load($data) {
        $this->id = $data->gradeid;
        $this->assignment = $data->assignmentid;
        $this->userid = $data->userid;
        //$this->grader = $data->grader;
        $this->gradeexternal = $data->gradeexternal;
        $this->feedbackexternal = $data->feedbackexternal;
        $this->grademanual = $data->grademanual;
        $this->feedbackmanual = $data->feedbackmanual;
    }
}