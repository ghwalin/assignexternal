<?php

namespace mod_assignprogram\data;

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
    public function __construct($formdata)
    {
        $this->setId($formdata->instance);
        $this->setCourse($formdata->course);
        $this->setCourseid($formdata->course);
        $this->setTimemodified( time());
        $this->setTimecreated(time());

        $this->setName($formdata->name);
        $this->setExternalname($formdata->externalname);
        $this->setExternallink($formdata->externallink);
        $this->setAlwaysshowlink(!empty($formdata->alwaysshowlink));
        $this->setIntro($formdata->intro);
        $this->setIntroformat($formdata->introformat);
        $this->setAlwaysshowdescription(!empty($formdata->alwaysshowdescription));
        $this->setAllowsubmissionsfromdate( $formdata->allowsubmissionsfromdate);
        $this->setDuedate( $formdata->duedate);
        $this->setCutoffdate($formdata->cutoffdate);
        $this->setGrade($formdata->grade);
        $this->setPassingpercentage( $formdata->passingpercentage);
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }


    /**
     * @return mixed
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param mixed $course
     */
    public function setCourse($course): void
    {
        $this->course = $course;
    }

    /**
     * @return mixed
     */
    public function getCourseid()
    {
        return $this->courseid;
    }

    /**
     * @param mixed $courseid
     */
    public function setCourseid($courseid): void
    {
        $this->courseid = $courseid;
    }

    /**
     * @return mixed
     */
    public function getTimemodified()
    {
        return $this->timemodified;
    }

    /**
     * @param mixed $timemodified
     */
    public function setTimemodified($timemodified): void
    {
        $this->timemodified = $timemodified;
    }

    /**
     * @return mixed
     */
    public function getTimecreated()
    {
        return $this->timecreated;
    }

    /**
     * @param mixed $timecreated
     */
    public function setTimecreated($timecreated): void
    {
        $this->timecreated = $timecreated;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getExternalname()
    {
        return $this->externalname;
    }

    /**
     * @param mixed $externalname
     */
    public function setExternalname($externalname): void
    {
        $this->externalname = $externalname;
    }

    /**
     * @return mixed
     */
    public function getExternallink()
    {
        return $this->externallink;
    }

    /**
     * @param mixed $externallink
     */
    public function setExternallink($externallink): void
    {
        $this->externallink = $externallink;
    }

    /**
     * @return mixed
     */
    public function getAlwaysshowlink()
    {
        return $this->alwaysshowlink;
    }

    /**
     * @param mixed $alwaysshowlink
     */
    public function setAlwaysshowlink($alwaysshowlink): void
    {
        $this->alwaysshowlink = $alwaysshowlink;
    }

    /**
     * @return mixed
     */
    public function getIntro()
    {
        return $this->intro;
    }

    /**
     * @param mixed $intro
     */
    public function setIntro($intro): void
    {
        $this->intro = $intro;
    }

    /**
     * @return mixed
     */
    public function getIntroformat()
    {
        return $this->introformat;
    }

    /**
     * @param mixed $introformat
     */
    public function setIntroformat($introformat): void
    {
        $this->introformat = $introformat;
    }

    /**
     * @return mixed
     */
    public function getAlwaysshowdescription()
    {
        return $this->alwaysshowdescription;
    }

    /**
     * @param mixed $alwaysshowdescription
     */
    public function setAlwaysshowdescription($alwaysshowdescription): void
    {
        $this->alwaysshowdescription = $alwaysshowdescription;
    }

    /**
     * @return mixed
     */
    public function getAllowsubmissionsfromdate()
    {
        return $this->allowsubmissionsfromdate;
    }

    /**
     * @param mixed $allowsubmissionsfromdate
     */
    public function setAllowsubmissionsfromdate($allowsubmissionsfromdate): void
    {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
    }

    /**
     * @return mixed
     */
    public function getDuedate()
    {
        return $this->duedate;
    }

    /**
     * @param mixed $duedate
     */
    public function setDuedate($duedate): void
    {
        $this->duedate = $duedate;
    }

    /**
     * @return mixed
     */
    public function getCutoffdate()
    {
        return $this->cutoffdate;
    }

    /**
     * @param mixed $cutoffdate
     */
    public function setCutoffdate($cutoffdate): void
    {
        $this->cutoffdate = $cutoffdate;
    }

    /**
     * @return mixed
     */
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param mixed $grade
     */
    public function setGrade($grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return mixed
     */
    public function getPassingpercentage()
    {
        return $this->passingpercentage;
    }

    /**
     * @param mixed $passingpercentage
     */
    public function setPassingpercentage($passingpercentage): void
    {
        $this->passingpercentage = $passingpercentage;
    }


}