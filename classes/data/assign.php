<?php

namespace mod_assignprogram\data;

use stdClass;

/**
 * represents a programming assignment
 *
 * @package   mod_assignprogram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign
{
    public $id;
    public $course;
    public $name;
    public $intro;
    public $introformat;
    public $alwaysshowdescription;
    public $externalname;
    public $externallink;
    public $alwaysshowlink;
    public $allowsubmissionsfromdate;
    public $duedate;
    public $cutoffdate;
    public $timemodified;
    public $externalgrademax;
    public $manualgrademax;
    public $passingpercentage;

    /**
     * constructor
     * @param stdClass $formdata
     */
    public function __construct(\stdClass $formdata)
    {
        $this->id = ($formdata->instance);
        $this->course = ($formdata->course);
        $this->name = ($formdata->name);
        $this->intro = ($formdata->intro);
        $this->introformat = ($formdata->introformat);
        $this->alwaysshowdescription = (!empty($formdata->alwaysshowdescription));
        $this->externalname = ($formdata->externalname);
        $this->externallink = ($formdata->externallink);
        $this->alwaysshowlink = (!empty($formdata->alwaysshowlink));
        $this->allowsubmissionsfromdate = ($formdata->allowsubmissionsfromdate);
        $this->duedate = ($formdata->duedate);
        $this->cutoffdate = ($formdata->cutoffdate);
        $this->timemodified = (time());
        $this->externalgrademax = ($formdata->externalgrademax);
        $this->manualgrademax = ($formdata->manualgrademax);
        $this->passingpercentage = ($formdata->passingpercentage);
    }
}