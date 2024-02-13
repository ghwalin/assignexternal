<?php

namespace mod_assignexternal\data;

use stdClass;

/**
 * represents an external assignment
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign
{
    /** @var int unique id of the external assignment */
    private $id;
    /** @var int the id of the course this assignment belongs to */
    private $course;
    /** @var int the coursemodule of this assignment */
    private $coursemodule;
    /** @var string the name of the assignment */
    private $name;
    /** @var string the description of the assignment */
    private $intro;
    /** @var int the format of the intro */
    private $introformat;
    /** @var bool if not set the description won't show until the allowsubmissionsformdate */
    private $alwaysshowdescription;
    /** @var string the name of the assignment in the external system */
    private $externalname;
    /** @var string the URL to the assignment in the external system */
    private $externallink;
    /** @var bool if not set the externallink won't show until the allowsubmissionsformdate*/
    private $alwaysshowlink;
    /** @var datetime  TODO*/
    private $allowsubmissionsfromdate;
    /** @var datetime TODO */
    private $duedate;
    /** @var datetime TODO */
    private $cutoffdate;
    /** @var datetime the date and time this assignment was last modified */
    private $timemodified;
    /** @var float the maximum grade from the external system */
    private $externalgrademax;
    /** @var float the maximum grade from the manual grading */
    private $manualgrademax;
    /** @var float the percentage of the total grade (external + manual) to reach for completing the assignment */
    private $passingpercentage;
    /** @var bool if set the user must reach the passingpercentage to complete the assignment */
    private $haspassinggrade;
    /** @var bool if set the student must have a grade to complete the assignment */
    private $hasgrade;

    /**
     * constructor
     * @param stdClass|null $formdata
     * @param int|null $coursemoduleid
     */
    public function __construct(\stdClass $formdata=null, int $coursemoduleid=null)
    {
        $this->setHaspassinggrade( null);
        $this->setHasgrade(null);
        if (!empty($formdata)) $this->load_formdata($formdata);
        else $this->load_db($coursemoduleid);
    }

    /**
     * loads the attributes for the assignment from the formdata
     * @param stdClass $formdata
     * @return void
     */
    private function load_formdata(\stdClass $formdata) {
        $this->setId($formdata->instance);
        $this->setCoursemodule( '0');
        $this->extracted($formdata);
        $this->setTimemodified(time());
    }

    /**
     * loads the attributes of the assignment from the database
     * @param $coursemoduleid
     * @return void
     * @throws \dml_exception
     */
    public function load_db($coursemoduleid) {
        global $DB;
        $sql =
            'SELECT ap.id, ap.course, coursemodule, name, intro, introformat, alwaysshowdescription, externalname, ' .
            '       externallink, alwaysshowlink, allowsubmissionsfromdate, duedate, cutoffdate, timemodified, ' .
            '       externalgrademax, manualgrademax, passingpercentage, haspassinggrade, hasgrade' .
            ' FROM {assignexternal} ap INNER JOIN {course_modules} cm ON (ap.id = cm.instance) ' .
            'WHERE cm.id=:coursemodule';
        $data = $DB->get_record_sql(
            $sql,
            ['coursemodule' => $coursemoduleid]
        );
        if (!empty($data)) {
            $this->setId( $data->id);
            $this->extracted($data);
            $this->setTimemodified($data->timemodified);
        }
    }

    /**
     * casts the object to a stdClass
     * @return stdClass
     */
    public function to_stdClass() {
        $result = new \stdClass();
        foreach ($this as $property => $value) {
            if ($value != null) {
                $result->$property = $value;
            }
        }
        return $result;

    }
    /**
     * extracts the values for the attributes
     * @param $data
     * @return void
     */
    private function extracted($data): void
    {
        $this->setCourse($data->course);
        $this->setName($data->name);
        $this->setIntro($data->intro);
        $this->setIntroformat($data->introformat);
        $this->setAlwaysshowdescription(!empty($data->alwaysshowdescription));
        $this->setExternalname($data->externalname);
        $this->setExternallink($data->externallink);
        $this->setAlwaysshowlink(!empty($data->alwaysshowlink));
        $this->setAllowsubmissionsfromdate($data->allowsubmissionsfromdate);
        $this->setDuedate($data->duedate);
        $this->setCutoffdate($data->cutoffdate);
        $this->setExternalgrademax($data->externalgrademax);
        $this->setManualgrademax($data->manualgrademax);
        $this->setPassingpercentage($data->passingpercentage);
        if (isset($data->haspassingpercentage))
            $this->setHaspassinggrade($data->haspassinggrade);
        if (isset($data->hasgrade))
            $this->setHasgrade($data->hasgrade);
    }

    /**
     * @return mixed
     */
    public function getId()
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
    public function getCoursemodule()
    {
        return $this->coursemodule;
    }

    /**
     * @param mixed $coursemodule
     */
    public function setCoursemodule($coursemodule): void
    {
        $this->coursemodule = $coursemodule;
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
    public function getExternalgrademax()
    {
        return $this->externalgrademax;
    }

    /**
     * @param mixed $externalgrademax
     */
    public function setExternalgrademax($externalgrademax): void
    {
        $this->externalgrademax = $externalgrademax;
    }

    /**
     * @return mixed
     */
    public function getManualgrademax()
    {
        return $this->manualgrademax;
    }

    /**
     * @param mixed $manualgrademax
     */
    public function setManualgrademax($manualgrademax): void
    {
        $this->manualgrademax = $manualgrademax;
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

    /**
     * @return null
     */
    public function getHaspassinggrade()
    {
        return $this->haspassinggrade;
    }

    /**
     * @param null $haspassinggrade
     */
    public function setHaspassinggrade($haspassinggrade): void
    {
        $this->haspassinggrade = $haspassinggrade;
    }

    /**
     * @return null
     */
    public function getHasgrade()
    {
        return $this->hasgrade;
    }

    /**
     * @param null $hasgrade
     */
    public function setHasgrade($hasgrade): void
    {
        $this->hasgrade = $hasgrade;
    }


}