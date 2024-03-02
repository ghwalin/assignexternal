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
namespace mod_assignexternal\controller;

use cm_info;
use core\context;
use dml_exception;
use mod_assignexternal\data\assign;
use stdClass;

/**
 * Controller for the programming assignment
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_control {
    /** @var stdClass the assignment record that contains the global settings for this assign instance */
    private mixed $instance;

    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private $context;

    /** @var stdClass the course this assign instance belongs to */
    private $course;
    /** @var cm_info the course module for this assign instance */
    private ?cm_info $coursemodule;
    /** @var array cache for things like the coursemodule name or the scale menu -
     *             only lives for a single request.
     */
    private $cache;
    /** @var string A key used to identify userlists created by this object. */
    private $useridlistid;


    /**
     * default constructor
     */
    public function __construct($coursemodulecontext, $coursemodule, $course) {
        global $CFG;
        require_once($CFG->libdir . '/modinfolib.php');
        $this->context = $coursemodulecontext;
        $this->course = $course;

        // Ensure that $this->coursemodule is a cm_info object (or null).
        $this->coursemodule = cm_info::create($coursemodule);
        // Temporary cache only lives for a single request - used to reduce db lookups.
        $this->cache = [];

        // Extra entropy is required for uniqid() to work on cygwin.
        $this->useridlistid = clean_param(uniqid('', true), PARAM_ALPHANUM);
    }

    /**
     * Add this instance to the database.
     *
     * @param stdClass $formdata The data submitted from the form
     * @return mixed false if an error occurs or the int id of the new instance
     */
    public function add_instance(stdClass $formdata, $coursemoduleid) {
        global $DB;
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
        $assign = new assign();
        $assign->load_formdata($formdata);
        $assign->set_coursemodule($coursemoduleid);
        $returnid = $DB->insert_record('assignexternal', $assign->to_stdclass());
        $this->instance = $DB->get_record('assignexternal', ['id' => $returnid], '*', MUST_EXIST);
        // Cache the course record.
        $this->course = $DB->get_record('course', ['id' => $formdata->course], '*', MUST_EXIST);
        $this->grade_item_update();
        return $returnid;
    }

    /**
     * Inserts or updates the grade settings for this assignment in grade_items
     * @return int
     */
    public function grade_item_update() {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        $params['itemname'] = $this->instance->name;
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax'] = $this->instance->externalgrademax + $this->instance->manualgrademax;
        $params['grademin'] = 0;
        return grade_update(
            'mod/assignexternal',
            $this->instance->course,
            'mod',
            'assignexternal',
            $this->instance->id,
            0,
            null,
            $params);
    }

    /**
     * Update this instance in the database.
     *
     * @param stdClass $formdata - the data submitted from the form
     * @return bool false if an error occurs
     * @throws dml_exception
     */
    public function update_instance(stdClass $formdata, int $coursemoduleid): bool {
        global $DB;
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
        $assign = new assign();
        $assign->load_formdata($formdata);
        $assign->set_coursemodule($coursemoduleid);
        $result = $DB->update_record('assignexternal', $assign->to_stdclass());
        $this->set_instance($DB->get_record('assignexternal', ['id' => $assign->get_id()], '*', MUST_EXIST));
        $this->grade_item_update();
        return $result;
    }

    /**
     * Delete this instance from the database.
     *
     * @return bool false if an error occurs
     */
    public function delete_instance() {
        global $DB;
        $result = true;
        $DB->delete_records('assignexternal_grades', ['assignment' => $this->get_instance()->id]);
        $DB->delete_records('assignexternal', ['id' => $this->get_instance()->id]);

        return $result;
    }

    public function get_instance(): mixed {
        return $this->instance;
    }

    public function set_instance(mixed $instance): void {
        $this->instance = $instance;
    }


    public function get_context(): context {
        return $this->context;
    }

    public function set_context(context $context): void {
        $this->context = $context;
    }

    public function get_course(): stdClass {
        return $this->course;
    }

    public function set_course(stdClass $course): void {
        $this->course = $course;
    }

    public function get_coursemodule(): cm_info {
        return $this->coursemodule;
    }

    public function set_coursemodule(cm_info $coursemodule): void {
        $this->coursemodule = $coursemodule;
    }

    public function get_cache(): array {
        return $this->cache;
    }

    public function set_cache(array $cache): void {
        $this->cache = $cache;
    }

    public function get_useridlistid(): ?string {
        return $this->useridlistid;
    }

    public function set_useridlistid(?string $useridlistid): void {
        $this->useridlistid = $useridlistid;
    }

}
