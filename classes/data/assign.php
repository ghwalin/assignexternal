<?php

namespace mod_assignexternal\data;

use stdClass;
use mod_assignexternal\data\override;
/**
 * represents an external assignment
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign
{
    /** @var int|null unique id of the external assignment */
    private $id;
    /** @var int|null the id of the course this assignment belongs to */
    private $course;
    /** @var int|null the coursemodule of this assignment */
    private $coursemodule;
    /** @var string the name of the assignment */
    private $name;
    /** @var string the description of the assignment */
    private $intro;
    /** @var string the format of the intro */
    private $introformat;
    /** @var bool if not set the description won't show until the allowsubmissionsformdate */
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
    /** @var float|null the maximum grade from the external system */
    private $externalgrademax;
    /** @var float|null the maximum grade from the manual grading */
    private $manualgrademax;
    /** @var float|null the percentage of the total grade (external + manual) to reach for completing the assignment */
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
    public function __construct()
    {
        $this->setId(null);
        $this->setCourse(null);
        $this->setCoursemodule(null);
        $this->setName('');
        $this->setIntro('');
        $this->setIntroformat(FORMAT_HTML);
        $this->setAlwaysshowdescription(false);
        $this->setExternalname('');
        $this->setExternallink('');
        $this->setAlwaysshowlink(false);
        $this->setAllowsubmissionsfromdate(null);
        $this->setDuedate(null);
        $this->setCutoffdate(null);
        $this->setTimemodified(null);
        $this->setExternalgrademax(null);
        $this->setManualgrademax(null);
        $this->setPassingpercentage(null);
        $this->setHaspassinggrade( false);
        $this->setHasgrade(false);
    }

    /**
     * loads the attributes for the assignment from the formdata
     * @param stdClass $formdata
     * @return void
     */
    public function load_formdata(\stdClass $formdata) {
        $this->setId($formdata->instance);
        $this->setCoursemodule( '0');
        $this->extracted($formdata);
        $this->setTimemodified(time());
    }

    /**
     * loads the attributes of the assignment from the database
     * @param int $coursemoduleid
     * @param int|null $userid
     * @return void
     * @throws \dml_exception
     */
    public function load_db(int $coursemoduleid, ?int $userid=null): void
    {
        global $DB;
        $data = $DB->get_record('assignexternal', ['coursemodule'=>$coursemoduleid]);
        if (!empty($data)) {
            $this->setId($data->id);
            $this->extracted($data);
            $this->setTimemodified($data->timemodified);
            if (!empty($userid)) {
                $this->load_override($coursemoduleid, $userid);
            }
        }
    }

    /**
     * loads the assignment using the external assignmentname and userid
     * @param string $assignmentname
     * @param int $userid
     * @return void
     * @throws \dml_exception
     */
    public function load_db_external(string $assignmentname, int $userid) {
        global $DB;
        $query =
            'SELECT ae.id, ae.course, ae.coursemodule, ae.externalgrademax, ae.duedate, ae.cutoffdate, ae.externalname' .
            ' FROM {user_enrolments} ue' .
            ' JOIN {enrol} en ON (ue.enrolid = en.id)' .
            ' JOIN {assignexternal} ae ON (ae.course = en.courseid)' .
            ' WHERE ae.externalname=:assignmentname AND ue.userid=:userid';
        $data = $DB->get_record_sql(
            $query,
            [
                'userid' => $userid,
                'assignmentname' => $assignmentname
            ]
        );
        if (!empty($data)) {
            $this->setId($data->id);
            $this->setCourse($data->course);
            $this->setCoursemodule($data->coursemodule);
            $this->setExternalgrademax($data->externalgrademax);
            $this->setDuedate($data->duedate);
            $this->setCutoffdate($data->cutoffdate);
            $this->setExternalname($data->externalname);
            $this->load_override($this->coursemodule, $userid);
        }
    }
    /**
     * loads the user overrides for this assignment
     * @param int $coursemodule
     * @param int $userid
     * @return void
     * @throws \dml_exception
     */
    private function load_override(int $coursemodule, int $userid) {
        global $CFG;
        require_once ($CFG->dirroot . '/mod/assignexternal/classes/data/override.php');
        $override = new override();
        error_log("CourseModule=$coursemodule / userid=$userid");
        $override->load_db($coursemodule, $userid);

        if (!empty($override->getAllowsubmissionsfromdate())) {
            $this->setAllowsubmissionsfromdate($override->getAllowsubmissionsfromdate());
        }
        if (!empty($override->getDuedate())) {
            $this->setDuedate($override->getDuedate());
        }
        if (!empty($override->getCutoffdate())) {
            $this->setCutoffdate($override->getCutoffdate());
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getCourse(): ?int
    {
        return $this->course;
    }

    public function setCourse(?int $course): void
    {
        $this->course = $course;
    }

    public function getCoursemodule(): ?int
    {
        return $this->coursemodule;
    }

    public function setCoursemodule(?int $coursemodule): void
    {
        $this->coursemodule = $coursemodule;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getIntro(): string
    {
        return $this->intro;
    }

    public function setIntro(string $intro): void
    {
        $this->intro = $intro;
    }

    public function getIntroformat(): string
    {
        return $this->introformat;
    }

    public function setIntroformat(string $introformat): void
    {
        $this->introformat = $introformat;
    }

    public function isAlwaysshowdescription(): bool
    {
        return $this->alwaysshowdescription;
    }

    public function setAlwaysshowdescription(bool $alwaysshowdescription): void
    {
        $this->alwaysshowdescription = $alwaysshowdescription;
    }

    public function getExternalname(): string
    {
        return $this->externalname;
    }

    public function setExternalname(string $externalname): void
    {
        $this->externalname = $externalname;
    }

    public function getExternallink(): string
    {
        return $this->externallink;
    }

    public function setExternallink(string $externallink): void
    {
        $this->externallink = $externallink;
    }

    public function isAlwaysshowlink(): bool
    {
        return $this->alwaysshowlink;
    }

    public function setAlwaysshowlink(bool $alwaysshowlink): void
    {
        $this->alwaysshowlink = $alwaysshowlink;
    }

    public function getAllowsubmissionsfromdate(): ?int
    {
        return $this->allowsubmissionsfromdate;
    }

    public function setAllowsubmissionsfromdate(?int $allowsubmissionsfromdate): void
    {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
    }

    public function getDuedate(): ?int
    {
        return $this->duedate;
    }

    public function setDuedate(?int $duedate): void
    {
        $this->duedate = $duedate;
    }

    public function getCutoffdate(): ?int
    {
        return $this->cutoffdate;
    }

    public function setCutoffdate(?int $cutoffdate): void
    {
        $this->cutoffdate = $cutoffdate;
    }

    public function getTimemodified(): ?int
    {
        return $this->timemodified;
    }

    public function setTimemodified(?int $timemodified): void
    {
        $this->timemodified = $timemodified;
    }

    public function getExternalgrademax(): ?float
    {
        return $this->externalgrademax;
    }

    public function setExternalgrademax(?float $externalgrademax): void
    {
        $this->externalgrademax = $externalgrademax;
    }

    public function getManualgrademax(): ?float
    {
        return $this->manualgrademax;
    }

    public function setManualgrademax(?float $manualgrademax): void
    {
        $this->manualgrademax = $manualgrademax;
    }

    public function getPassingpercentage(): ?float
    {
        return $this->passingpercentage;
    }

    public function setPassingpercentage(?float $passingpercentage): void
    {
        $this->passingpercentage = $passingpercentage;
    }

    public function isHaspassinggrade(): bool
    {
        return $this->haspassinggrade;
    }

    public function setHaspassinggrade(bool $haspassinggrade): void
    {
        $this->haspassinggrade = $haspassinggrade;
    }

    public function isHasgrade(): bool
    {
        return $this->hasgrade;
    }

    public function setHasgrade(bool $hasgrade): void
    {
        $this->hasgrade = $hasgrade;
    }



}