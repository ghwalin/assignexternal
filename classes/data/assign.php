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
    /** @var bool|null if not set the description won't show until the allowsubmissionsformdate */
    private $alwaysshowdescription;
    /** @var string the name of the assignment in the external system */
    private $externalname;
    /** @var string the URL to the assignment in the external system */
    private $externallink;
    /** @var bool if not set the externallink won't show until the allowsubmissionsformdate*/
    private $alwaysshowlink;
    /** @var int|null  the time when submissions are allowed */
    private $allowsubmissionsfromdate;
    /** @var int|null the time this assignment is due */
    private $duedate;
    /** @var int|null the time when submissions are no longer possible */
    private $cutoffdate;
    /** @var int|null the date and time this assignment was last modified */
    private $timemodified;
    /** @var float the maximum grade from the external system */
    private $externalgrademax;
    /** @var float the maximum grade from the manual grading */
    private $manualgrademax;
    /** @var float the percentage of the total grade (external + manual) to reach for completing the assignment */
    private $passingpercentage;
    /** @var bool|null if set the user must reach the passingpercentage to complete the assignment */
    private $haspassinggrade;
    /** @var bool|null if set the student must have a grade to complete the assignment */
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
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getCourse(): int
    {
        return $this->course;
    }

    /**
     * @param int $course
     */
    public function setCourse(int $course): void
    {
        $this->course = $course;
    }

    /**
     * @return int
     */
    public function getCoursemodule(): int
    {
        return $this->coursemodule;
    }

    /**
     * @param int $coursemodule
     */
    public function setCoursemodule(int $coursemodule): void
    {
        $this->coursemodule = $coursemodule;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getIntro(): string
    {
        return $this->intro;
    }

    /**
     * @param string $intro
     */
    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    /**
     * @return int
     */
    public function getIntroformat(): int
    {
        return $this->introformat;
    }

    /**
     * @param int $introformat
     */
    public function setIntroformat(int $introformat): void
    {
        $this->introformat = $introformat;
    }

    /**
     * @return bool
     */
    public function getAlwaysshowdescription(): bool
    {
        return $this->alwaysshowdescription;
    }

    /**
     * @param bool $alwaysshowdescription
     */
    public function setAlwaysshowdescription(bool $alwaysshowdescription): void
    {
        $this->alwaysshowdescription = $alwaysshowdescription;
    }

    /**
     * @return string
     */
    public function getExternalname(): string
    {
        return $this->externalname;
    }

    /**
     * @param string $externalname
     */
    public function setExternalname(string $externalname): void
    {
        $this->externalname = $externalname;
    }

    /**
     * @return string
     */
    public function getExternallink(): string
    {
        return $this->externallink;
    }

    /**
     * @param string $externallink
     */
    public function setExternallink(string $externallink): void
    {
        $this->externallink = $externallink;
    }

    /**
     * @return bool
     */
    public function getAlwaysshowlink(): bool
    {
        return $this->alwaysshowlink;
    }

    /**
     * @param bool $alwaysshowlink
     */
    public function setAlwaysshowlink(bool $alwaysshowlink): void
    {
        $this->alwaysshowlink = $alwaysshowlink;
    }

    /**
     * @return int|null
     */
    public function getAllowsubmissionsfromdate(): ?int
    {
        return $this->allowsubmissionsfromdate;
    }

    /**
     * @param int|null $allowsubmissionsfromdate
     */
    public function setAllowsubmissionsfromdate(?int $allowsubmissionsfromdate): void
    {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
    }

    /**
     * @return int|null
     */
    public function getDuedate(): ?int
    {
        return $this->duedate;
    }

    /**
     * @param int|null $duedate
     */
    public function setDuedate(?int $duedate): void
    {
        $this->duedate = $duedate;
    }

    /**
     * @return int|null
     */
    public function getCutoffdate(): ?int
    {
        return $this->cutoffdate;
    }

    /**
     * @param int|null $cutoffdate
     */
    public function setCutoffdate(?int $cutoffdate): void
    {
        $this->cutoffdate = $cutoffdate;
    }

    /**
     * @return int|null
     */
    public function getTimemodified(): ?int
    {
        return $this->timemodified;
    }

    /**
     * @param int|null $timemodified
     */
    public function setTimemodified(?int $timemodified): void
    {
        $this->timemodified = $timemodified;
    }

    /**
     * @return float
     */
    public function getExternalgrademax(): float
    {
        return $this->externalgrademax;
    }

    /**
     * @param float $externalgrademax
     */
    public function setExternalgrademax(float $externalgrademax): void
    {
        $this->externalgrademax = $externalgrademax;
    }

    /**
     * @return float
     */
    public function getManualgrademax(): float
    {
        return $this->manualgrademax;
    }

    /**
     * @param float $manualgrademax
     */
    public function setManualgrademax(float $manualgrademax): void
    {
        $this->manualgrademax = $manualgrademax;
    }

    /**
     * @return float
     */
    public function getPassingpercentage(): float
    {
        return $this->passingpercentage;
    }

    /**
     * @param float $passingpercentage
     */
    public function setPassingpercentage(float $passingpercentage): void
    {
        $this->passingpercentage = $passingpercentage;
    }

    /**
     * @return bool|null
     */
    public function getHaspassinggrade(): ?bool
    {
        return $this->haspassinggrade;
    }

    /**
     * @param bool|null $haspassinggrade
     */
    public function setHaspassinggrade(?bool $haspassinggrade): void
    {
        $this->haspassinggrade = $haspassinggrade;
    }

    /**
     * @return bool|null
     */
    public function getHasgrade(): ?bool
    {
        return $this->hasgrade;
    }

    /**
     * @param bool|null $hasgrade
     */
    public function setHasgrade(?bool $hasgrade): void
    {
        $this->hasgrade = $hasgrade;
    }




}