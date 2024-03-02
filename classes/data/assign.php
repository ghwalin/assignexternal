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
 * represents an external assignment
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign {
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
    /** @var bool if not set the externallink won't show until the allowsubmissionsformdate */
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

    /**
     * constructor
     * @param stdClass|null $formdata
     * @param int|null $coursemoduleid
     */
    public function __construct() {
        $this->set_id(null);
        $this->set_course(null);
        $this->set_coursemodule(null);
        $this->set_name('');
        $this->set_intro('');
        $this->set_introformat(FORMAT_HTML);
        $this->set_alwaysshowdescription(false);
        $this->set_externalname('');
        $this->set_externallink('');
        $this->set_alwaysshowlink(false);
        $this->set_allowsubmissionsfromdate(null);
        $this->set_duedate(null);
        $this->set_cutoffdate(null);
        $this->set_timemodified(null);
        $this->set_externalgrademax(null);
        $this->set_manualgrademax(null);
        $this->set_passingpercentage(null);
        $this->set_haspassinggrade(false);
    }

    /**
     * loads the attributes for the assignment from the formdata
     * @param stdClass $formdata
     * @return void
     */
    public function load_formdata(stdClass $formdata) {
        $this->set_id((int)$formdata->instance);
        $this->set_coursemodule('0');
        $this->extracted($formdata);
        $this->set_timemodified(time());
    }

    /**
     * extracts the values for the attributes
     * @param $data
     * @return void
     */
    private function extracted($data): void {
        $this->set_course($data->course);
        $this->set_name($data->name);
        $this->set_intro($data->intro);
        $this->set_introformat($data->introformat);
        $this->set_alwaysshowdescription(!empty($data->alwaysshowdescription));
        $this->set_externalname($data->externalname);
        $this->set_externallink($data->externallink);
        $this->set_alwaysshowlink(!empty($data->alwaysshowlink));
        $this->set_allowsubmissionsfromdate($data->allowsubmissionsfromdate);
        $this->set_duedate($data->duedate);
        $this->set_cutoffdate($data->cutoffdate);
        $this->set_externalgrademax($data->externalgrademax);
        $this->set_manualgrademax($data->manualgrademax);
        $this->set_passingpercentage($data->passingpercentage);
        if (isset($data->haspassingpercentage))
            $this->set_haspassinggrade($data->haspassinggrade);
    }

    /**
     * loads the attributes of the assignment from the database
     * @param int $coursemoduleid
     * @param int|null $userid
     * @return void
     * @throws dml_exception
     */
    public function load_db(int $coursemoduleid, ?int $userid = null): void {
        global $DB;
        $data = $DB->get_record('assignexternal', ['coursemodule' => $coursemoduleid]);
        if (!empty($data)) {
            $this->set_id($data->id);
            $this->extracted($data);
            $this->set_timemodified($data->timemodified);
            if (!empty($userid)) {
                $this->load_override($coursemoduleid, $userid);
            }
        }
    }

    /**
     * loads the user overrides for this assignment
     * @param int $coursemodule
     * @param int $userid
     * @return void
     * @throws dml_exception
     */
    private function load_override(int $coursemodule, int $userid) {
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/override.php');
        $override = new override();
        $override->load_db($coursemodule, $userid);

        if (!empty($override->get_allowsubmissionsfromdate())) {
            $this->set_allowsubmissionsfromdate($override->get_allowsubmissionsfromdate());
        }
        if (!empty($override->get_duedate())) {
            $this->set_duedate($override->get_duedate());
        }
        if (!empty($override->get_cutoffdate())) {
            $this->set_cutoffdate($override->get_cutoffdate());
        }

    }



    /**
     * loads the assignment using the external assignmentname and userid
     * @param string $assignmentname
     * @param int $userid
     * @return void
     * @throws dml_exception
     */
    public function load_db_external(string $assignmentname, int $userid): void {
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
                'assignmentname' => $assignmentname,
            ]
        );
        if (!empty($data)) {
            $this->set_id($data->id);
            $this->set_course($data->course);
            $this->set_coursemodule($data->coursemodule);
            $this->set_externalgrademax($data->externalgrademax);
            $this->set_duedate($data->duedate);
            $this->set_cutoffdate($data->cutoffdate);
            $this->set_externalname($data->externalname);
            $this->load_override($this->coursemodule, $userid);
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

    public function get_id(): ?int {
        return $this->id;
    }

    public function set_id(?int $id): void {
        $this->id = $id;
    }

    public function get_course(): ?int {
        return $this->course;
    }

    public function set_course(?int $course): void {
        $this->course = $course;
    }

    public function get_coursemodule(): ?int {
        return $this->coursemodule;
    }

    public function set_coursemodule(?int $coursemodule): void {
        $this->coursemodule = $coursemodule;
    }

    public function get_name(): string {
        return $this->name;
    }

    public function set_name(string $name): void {
        $this->name = $name;
    }

    public function get_intro(): string {
        return $this->intro;
    }

    public function set_intro(string $intro): void {
        $this->intro = $intro;
    }

    public function get_introformat(): string {
        return $this->introformat;
    }

    public function set_introformat(string $introformat): void {
        $this->introformat = $introformat;
    }

    public function is_alwaysshowdescription(): bool {
        return $this->alwaysshowdescription;
    }

    public function set_alwaysshowdescription(bool $alwaysshowdescription): void {
        $this->alwaysshowdescription = $alwaysshowdescription;
    }

    public function get_externalname(): string {
        return $this->externalname;
    }

    public function set_externalname(string $externalname): void {
        $this->externalname = $externalname;
    }

    public function get_externallink(): string {
        return $this->externallink;
    }

    public function set_externallink(string $externallink): void {
        $this->externallink = $externallink;
    }

    public function is_alwaysshowlink(): bool {
        return $this->alwaysshowlink;
    }

    public function set_alwaysshowlink(bool $alwaysshowlink): void {
        $this->alwaysshowlink = $alwaysshowlink;
    }

    public function get_allowsubmissionsfromdate(): ?int {
        return $this->allowsubmissionsfromdate;
    }

    public function set_allowsubmissionsfromdate(?int $allowsubmissionsfromdate): void {
        $this->allowsubmissionsfromdate = $allowsubmissionsfromdate;
    }

    public function get_duedate(): ?int {
        return $this->duedate;
    }

    public function set_duedate(?int $duedate): void {
        $this->duedate = $duedate;
    }

    public function get_cutoffdate(): ?int {
        return $this->cutoffdate;
    }

    public function set_cutoffdate(?int $cutoffdate): void {
        $this->cutoffdate = $cutoffdate;
    }

    public function get_timemodified(): ?int {
        return $this->timemodified;
    }

    public function set_timemodified(?int $timemodified): void {
        $this->timemodified = $timemodified;
    }

    public function get_externalgrademax(): ?float {
        return $this->externalgrademax;
    }

    public function set_externalgrademax(?float $externalgrademax): void {
        $this->externalgrademax = $externalgrademax;
    }

    public function get_manualgrademax(): ?float {
        return $this->manualgrademax;
    }

    public function set_manualgrademax(?float $manualgrademax): void {
        $this->manualgrademax = $manualgrademax;
    }

    public function get_passingpercentage(): ?float {
        return $this->passingpercentage;
    }

    public function set_passingpercentage(?float $passingpercentage): void {
        $this->passingpercentage = $passingpercentage;
    }

    public function is_haspassinggrade(): bool {
        return $this->haspassinggrade;
    }

    public function set_haspassinggrade(bool $haspassinggrade): void {
        $this->haspassinggrade = $haspassinggrade;
    }

}
