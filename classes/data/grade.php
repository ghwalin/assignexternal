<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
namespace mod_assignexternal\data;

use dml_exception;
use stdClass;

/**
 * represents the grading information
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade {
    /** @var int|null the unique id of this grade */
    private $id;
    /** @var int|null the id of the external assignment this grade belongs to */
    private $assignexternal;
    /** @var int|null the id of the user this grade belongs to */
    private $userid;
    /** @var int the userid of the grader */
    private $grader;
    /** @var string the URL of the submission in the external system */
    private $externallink;
    /** @var float the grade from the external system */
    private $externalgrade;
    /** @var string|null the feedback from the external system as HTML-code */
    private $externalfeedback;
    /** @var float the grade from manual grading */
    private $manualgrade;
    /** @var string|null the manual feedback as HTML-code */
    private $manualfeedback;

    /**
     * default constructor
     */
    public function __construct() {
        global $USER;
        $this->set_id(null);
        $this->set_assignmentexternal(null);
        $this->set_userid(null);
        $this->set_grader($USER->id);
        $this->set_externallink('');
        $this->set_externalfeedback('');
        $this->set_externalgrade(0);
        $this->set_manualfeedback('');
        $this->set_manualgrade(0);
    }

    /**
     * initialize the attributes from the formdata
     * @param $formdata
     * @return void
     */
    public function load_formdata($formdata): void {
        $this->load_data($formdata);
        $this->set_assignmentexternal($formdata->id);
    }

    private function load_data($data): void {
        $this->set_id($data->gradeid);
        $this->set_userid($data->userid);
        $this->set_externallink($data->externallink);
        if (empty($data->externalgrade)) {
            $this->set_externalgrade(0.0);
        } else {
            $this->set_externalgrade($data->externalgrade);
        }
        if (empty($data->manualgrade)) {
            $this->set_manualgrade(0.0);
        } else {
            $this->set_manualgrade($data->manualgrade);
        }
        if (is_array($data->externalfeedback)) {
            $this->set_externalfeedback($data->externalfeedback['text']);
        } else {
            $this->set_externalfeedback($data->externalfeedback);
        }
        if (is_array($data->manualfeedback)) {
            $this->set_manualfeedback($data->manualfeedback['text']);
        } else {
            $this->set_manualfeedback($data->manualfeedback);
        }
    }

    /**
     * loads the gradeing data from the database
     * @param int $coursemodule
     * @param int $userid
     * @return void
     * @throws dml_exception
     */
    public function load_db($coursemodule, $userid): void {
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

    /**
     * casts the object to a stdClass
     * @return stdClass
     */
    public function to_stdclass(): stdClass {
        $result = new stdClass();
        foreach ($this as $property => $value) {
            if ($value != null) {
                $result->$property = $value;
            }
        }
        return $result;

    }

    /**
     * calculates the total grade from external and manual grade
     * @return float
     */
    public function total_grade(): float {
        return $this->externalgrade + $this->manualgrade;
    }

    /**
     * @return null
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function set_id(?int $id): void {
        $this->id = $id;
    }

    /**
     * @return null
     */
    public function get_assignmentexternal() {
        return $this->assignexternal;
    }

    /**
     * @param null $assignexternal
     */
    public function set_assignmentexternal($assignexternal): void {
        $this->assignexternal = $assignexternal;
    }

    /**
     * @return int|null
     */
    public function get_userid() {
        return $this->userid;
    }

    /**
     * @param int|null $userid
     */
    public function set_userid($userid): void {
        $this->userid = $userid;
    }

    /**
     * @return mixed
     */
    public function get_grader() {
        return $this->grader;
    }

    /**
     * @param mixed $grader
     */
    public function set_grader($grader): void {
        $this->grader = $grader;
    }

    /**
     * @return string
     */
    public function get_externallink(): string {
        return $this->externallink;
    }

    /**
     * @param string $externallink
     */
    public function set_externallink(string $externallink): void {
        $this->externallink = $externallink;
    }

    /**
     * @return float
     */
    public function get_externalgrade(): float {
        return $this->externalgrade;
    }

    /**
     * @param float $externalgrade
     */
    public function set_externalgrade(float $externalgrade): void {
        $this->externalgrade = $externalgrade;
    }

    /**
     * @return string|null
     */
    public function get_externalfeedback(): ?string {
        return $this->externalfeedback;
    }

    /**
     * @param string|null $externalfeedback
     */
    public function set_externalfeedback(?string $externalfeedback): void {
        $this->externalfeedback = $externalfeedback;
    }

    /**
     * @return float
     */
    public function get_manualgrade(): float {
        return $this->manualgrade;
    }

    /**
     * @param int $manualgrade
     */
    public function set_manualgrade(float $manualgrade): void {
        $this->manualgrade = $manualgrade;
    }

    /**
     * @return string|null
     */
    public function get_manualfeedback(): ?string {
        return $this->manualfeedback;
    }

    /**
     * @param string|null $manualfeedback
     */
    public function set_manualfeedback(?string $manualfeedback): void {
        $this->manualfeedback = $manualfeedback;
    }

}
