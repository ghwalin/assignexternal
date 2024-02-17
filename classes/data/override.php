<?php

namespace mod_assignexternal\data;


/**
 * represents the override for one user
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override
{
    /** @var int|null the unique id of this grade */
    private ?int $id;
    /** @var int|null the id of the external assignment this grade belongs to*/
    private ?int $assignexternal;
    /** @var int|null the id of the user this grade belongs to  */
    private ?int $userid;
    /** @var int|null  the time when submissions are allowed */
    private ?int $allowsubmissionsfromdate;
    /** @var int|null the time this assignment is due */
    private ?int $duedate;
    /** @var int|null the time when submissions are no longer possible */
    private ?int $cutoffdate;

    /**
     * default constructor
     */
    public function __construct() {
        $this->setId(null);
        $this->setAssignexternal(null);
        $this->setUserid(null);
        $this->setAllowsubmissionsfromdate(null);
        $this->setDuedate(null);
        $this->setCutoffdate(null);
    }

    /**
     * casts the object to a stdClass
     * @return \stdClass
     */
    public function to_stdClass(): \stdClass
    {
        $result = new \stdClass();
        foreach ($this as $property => $value) {
            if ($value != null) {
                $result->$property = $value;
            }
        }
        return $result;
    }

    /**
     * loads the override from the database
     * @param int $coursemodule
     * @param int $userid
     * @return void
     * @throws \dml_exception
     */
    public function load_db(int $coursemodule, int $userid): void
    {
        global $DB;
        error_log("assignexternal=$coursemodule / userid=$userid");
        $data = $DB->get_record(
            'assignexternal_overrides',
            ['assignexternal'=>$coursemodule, 'userid'=>$userid]
        );
        if (!empty($data)) {
            error_log(var_export($data, true));
            $this->load_data($data);
        }
    }
    /**
     * initialize the attributes from the formdata
     * @param $formdata
     * @return void
     */
    public function load_formdata($formdata): void
    {
        $this->load_data($formdata);
        $this->assignexternal = $formdata->id;
    }

    /**
     * loads the attribute values from a stdClass
     * @param \stdClass $data
     * @return void
     */
    private function load_data($data): void
    {
        $this->setId($data->id);
        $this->setAssignexternal($data->assignexternal);
        $this->setUserid($data->userid);
        if (!empty($data->allowsubmissionsfromdate)) {
            $this->setAllowsubmissionsfromdate($data->allowsubmissionsfromdate);
        }
        if (!empty($data->duedate)) {
            $this->setDuedate($data->duedate);
        }
        if (!empty($data->cutoffdate)) {
            $this->setCutoffdate($data->cutoffdate);
        }
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getAssignexternal(): ?int
    {
        return $this->assignexternal;
    }

    /**
     * @param int|null $assignexternal
     */
    public function setAssignexternal(?int $assignexternal): void
    {
        $this->assignexternal = $assignexternal;
    }

    /**
     * @return int|null
     */
    public function getUserid(): ?int
    {
        return $this->userid;
    }

    /**
     * @param int|null $userid
     */
    public function setUserid(?int $userid): void
    {
        $this->userid = $userid;
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


}