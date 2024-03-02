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
 * represents the override for one user
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override {
    /** @var int|null the unique id of this grade */
    private ?int $id;
    /** @var int|null the id of the external assignment this grade belongs to */
    private ?int $assignexternal;
    /** @var int|null the id of the user this grade belongs to */
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
        $this->set_id(null);
        $this->set_assignexternal(null);
        $this->set_userid(null);
        $this->set_allowsubmissionsfromdate(null);
        $this->set_duedate(null);
        $this->set_cutoffdate(null);
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
     * loads the override from the database
     * @param int $coursemodule
     * @param int $userid
     * @return void
     * @throws dml_exception
     */
    public function load_db(int $coursemodule, int $userid): void {
        global $DB;
        $data = $DB->get_record(
            'assignexternal_overrides',
            ['assignexternal' => $coursemodule, 'userid' => $userid]
        );
        if (!empty($data)) {
            $this->load_data($data);
        }
    }

    /**
     * loads the attribute values from a stdClass
     * @param stdClass $data
     * @return void
     */
    private function load_data($data): void {
        $this->set_id($data->id);
        $this->set_assignexternal($data->assignexternal);
        $this->set_userid($data->userid);
        if (!empty($data->allowsubmissionsfromdate)) {
            $this->set_allowsubmissionsfromdate($data->allowsubmissionsfromdate);
        }
        if (!empty($data->duedate)) {
            $this->set_duedate($data->duedate);
        }
        if (!empty($data->cutoffdate)) {
            $this->set_cutoffdate($data->cutoffdate);
        }
    }

    /**
     * initialize the attributes from the formdata
     * @param $formdata
     * @return void
     */
    public function load_formdata($formdata): void {
        $this->load_data($formdata);
        $this->assignexternal = $formdata->id;
    }

    /**
     * @return int|null
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
     * @return int|null
     */
    public function get_assignexternal(): ?int {
        return $this->assignexternal;
    }

    /**
     * @param int|null $assignexternal
     */
    public function set_assignexternal(?int $assignexternal): void {
        $this->assignexternal = $assignexternal;
    }

    /**
     * @return int|null
     */
    public function get_userid(): ?int {
        return $this->userid;
    }

    /**
     * @param int|null $userid
     */
    public function set_userid(?int $userid): void {
        $this->userid = $userid;
    }

    /**
     * @return int|null
     */
    public function get_allowsubmissionsfromdate(): ?int {
        return $this->allowsubmissionsfromdate;
    }

    /**
     * @param int|null $allowsubmissionsfromdate
     */
    public function set_allowsubmissionsfromdate(?int $allowsubmissionsfromdate): void {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
    }

    /**
     * @return int|null
     */
    public function get_duedate(): ?int {
        return $this->duedate;
    }

    /**
     * @param int|null $duedate
     */
    public function set_duedate(?int $duedate): void {
        $this->duedate = $duedate;
    }

    /**
     * @return int|null
     */
    public function get_cutoffdate(): ?int {
        return $this->cutoffdate;
    }

    /**
     * @param int|null $cutoffdate
     */
    public function set_cutoffdate(?int $cutoffdate): void {
        $this->cutoffdate = $cutoffdate;
    }

}
