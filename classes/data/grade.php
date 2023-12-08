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
        $this->id = null;
        $this->assignment = null;
        $this->userid = null;
        $this->grader = null;
        $this->feedbackexternal = '';
        $this->gradeexternal = 0;
        $this->feedbackmanual = '';
        $this->grademanual = 0;
    }
    public function init($formdata)
    {
        $this->id = $formdata->id;
        $this->assignment = $formdata->assignment;
        $this->userid = $formdata->userid;
        $this->grader = $formdata->grader;
        $this->gradeexternal = $formdata->gradeexternal;
        $this->feedbackexternal = $formdata->feedbackexternal;
        $this->grademanual = $formdata->grademanual;
        $this->feedbackmanual = $formdata->feedbackmanual;
    }
}