<?php

namespace mod_assignprogram\data;
use stdClass;
class Assign
{
    public $id;
    public $course;
    public $courseid;
    public $timemodified;
    public $timecreated;
    public $name;
    public $externalname;
    public $externallink;
    public $alwaysshowlink;
    public $intro;
    public $introformat;
    public $alwaysshowdescription;
    public $allowsubmissionsfromdate;
    public $duedate;
    public $cutoffdate;
    public $grade;
    public $passingpercentage;

    /**
     * constructor
     * @param stdClass $formdata
     */
    public function __construct(\stdClass $formdata)
    {
        $this->id = ($formdata->instance);
        $this->course = ($formdata->course);
        $this->courseid = ($formdata->course);
        $this->timemodified = ( time());
        $this->timecreated =(time());
        $this->name = ($formdata->name);
        $this->externalname =($formdata->externalname);
        $this->externallink = ($formdata->externallink);
        $this->alwaysshowlink =(!empty($formdata->alwaysshowlink));
        $this->intro =($formdata->intro);
        $this->introformat = ($formdata->introformat);
        $this->alwaysshowdescription = (!empty($formdata->alwaysshowdescription));
        $this->allowsubmissionsfromdate = ( $formdata->allowsubmissionsfromdate);
        $this->duedate = ( $formdata->duedate);
        $this->cutoffdate = ($formdata->cutoffdate);
        $this->grade = ($formdata->grade);
        $this->passingpercentage = ( $formdata->passingpercentage);
    }




}