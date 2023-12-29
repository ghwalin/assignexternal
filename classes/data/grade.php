<?php

namespace mod_assignprogram\data;

/**
 * represents the grading information
 *
 * @package   mod_assignprogram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade
{
    public $id;
    public $assignprogram;
    public $userid;
    public $grader;
    public $externallink;
    public $externalgrade;
    public $externalfeedback;
    public $manualgrade;
    public $manualfeedback;

    /**
     * default constructor
     */
    public function __construct() {
        global  $USER;
        $this->id = null;
        $this->assignprogram = null;
        $this->userid = null;
        $this->grader = $USER->id;
        $this->externallink = null;
        $this->externalfeedback = '';
        $this->externalgrade = 0;
        $this->manualfeedback = '';
        $this->manualgrade = 0;
    }

    /**
     * initialize the attributes from the formdata
     * @param $formdata
     * @return void
     */
    public function init($formdata)
    {
        $this->id = $formdata->gradeid;
        $this->assignprogram = $formdata->id;
        $this->userid = $formdata->userid;
        //$this->grader = $formdata->grader;
        $this->externallink = $formdata->externallink;
        $this->externalgrade = $formdata->externalgrade;
        $this->externalfeedback = $formdata->externalfeedback['text'];
        $this->manualgrade = $formdata->manualgrade;
        $this->manualfeedback = $formdata->manualfeedback['text'];
    }

    /**
     * load data from a data-object
     * @param $data
     * @return void
     */
    public function load($data) {
        $this->id = $data->gradeid;
        $this->assignprogram = $data->assignprogramid;
        $this->userid = $data->userid;
        //$this->grader = $data->grader;
        $this->externallink = $data->externallink;
        $this->externalgrade = $data->externalgrade;
        $this->externalfeedback = $data->externalfeedback;
        $this->manualgrade = $data->manualgrade;
        $this->manualfeedback = $data->manualfeedback;
    }
}