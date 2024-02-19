<?php

namespace mod_assignexternal\data;

/**
 * represents the grading information
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade
{
    /** @var int|null the unique id of this grade */
    private $id;
    /** @var int|null the id of the external assignment this grade belongs to*/
    private $assignexternal;
    /** @var int|null the id of the user this grade belongs to  */
    private $userid;
    /** @var int the userid of the grader */
    private $grader;
    /** @var string the URL of the submission in the external system */
    private $externallink;
    /** @var float the grade from the external system */
    private $externalgrade;
    /** @var string the feedback from the external system as HTML-code */
    private $externalfeedback;
    /** @var float the grade from manual grading */
    private $manualgrade;
    /** @var string the manual feedback as HTML-code */
    private $manualfeedback;

    /**
     * default constructor
     */
    public function __construct() {
        global  $USER;
        $this->setId(null);
        $this->setAssignexternal(null);
        $this->setUserid(null);
        $this->setGrader($USER->id);
        $this->setExternallink('');
        $this->setExternalfeedback('');
        $this->setExternalgrade(0);
        $this->setManualfeedback('');
        $this->setManualgrade(0);
    }

    /**
     * initialize the attributes from the formdata
     * @param $formdata
     * @return void
     */
    public function load_formdata($formdata): void
    {
        $this->load_data($formdata);
        $this->setAssignexternal($formdata->id);
    }

    /**
     * loads the gradeing data from the database
     * @param int $coursemodule
     * @param int $userid
     * @return void
     * @throws \dml_exception
     */
    public function load_db($coursemodule, $userid): void
    {
        global $DB;
        $data = $DB->get_record(
            'assignexternal_grades',
            ['assignexternal' => $coursemodule, 'userid' => $userid],
            '*',
            IGNORE_MISSING
        );

        if ($data) {
            $data->gradeid = $data->id;
            $this->load_data($data);
            $this->assignexternal = $data->assignexternal;
            $this->grader = $data->grader;
        }
    }

    private function load_data($data): void
    {
        $this->setId($data->gradeid);
        $this->setUserid($data->userid);
        $this->setExternallink($data->externallink);
        $this->setExternalgrade($data->externalgrade);
        $this->setManualgrade($data->manualgrade);
        if (is_array($data->externalfeedback)) {
            $this->setExternalfeedback($data->externalfeedback['text']);
        } else {
            $this->setExternalfeedback($data->externalfeedback);
        }
        if (is_array($data->manualfeedback)) {
            $this->setManualfeedback($data->manualfeedback['text']);
        } else {
            $this->setManualfeedback($data->manualfeedback);
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
     * calculates the total grade from external and manual grade
     * @return int
     */
    public function total_grade() {
        return $this->externalgrade + $this->manualgrade;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function getAssignexternal()
    {
        return $this->assignexternal;
    }

    /**
     * @param null $assignexternal
     */
    public function setAssignexternal($assignexternal): void
    {
        $this->assignexternal = $assignexternal;
    }

    /**
     * @return null
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param null $userid
     */
    public function setUserid($userid): void
    {
        $this->userid = $userid;
    }

    /**
     * @return mixed
     */
    public function getGrader()
    {
        return $this->grader;
    }

    /**
     * @param mixed $grader
     */
    public function setGrader($grader): void
    {
        $this->grader = $grader;
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
     * @return int
     */
    public function getExternalgrade(): int
    {
        return $this->externalgrade;
    }

    /**
     * @param int $externalgrade
     */
    public function setExternalgrade(int $externalgrade): void
    {
        $this->externalgrade = $externalgrade;
    }

    /**
     * @return string
     */
    public function getExternalfeedback(): string
    {
        return $this->externalfeedback;
    }

    /**
     * @param string $externalfeedback
     */
    public function setExternalfeedback(string $externalfeedback): void
    {
        $this->externalfeedback = $externalfeedback;
    }

    /**
     * @return int
     */
    public function getManualgrade(): int
    {
        return $this->manualgrade;
    }

    /**
     * @param int $manualgrade
     */
    public function setManualgrade(int $manualgrade): void
    {
        $this->manualgrade = $manualgrade;
    }

    /**
     * @return string
     */
    public function getManualfeedback(): string
    {
        return $this->manualfeedback;
    }

    /**
     * @param string $manualfeedback
     */
    public function setManualfeedback(string $manualfeedback): void
    {
        $this->manualfeedback = $manualfeedback;
    }


}