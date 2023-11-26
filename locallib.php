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

/**
 * This file contains the definition for the class assignment
 *
 * This class provides all the functionality for the programming assign module.
 *
 * @package   mod_assignprogram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Controller class for mod_assignprogram
 * @package mod_assignprogram
 */
class assign_ctrl
{
    /** @var stdClass the assignment record that contains the global settings for this assign instance */
    private $instance;

    /** @var array $var array an array containing per-user assignment records, each having calculated properties (e.g. dates) */
    private $userinstances = [];

    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private $context;

    /** @var stdClass the course this assign instance belongs to */
    private $course;
    /** @var cm_info the course module for this assign instance */
    private $coursemodule;
    /** @var array cache for things like the coursemodule name or the scale menu -
     *             only lives for a single request.
     */
    private $cache;
    /** @var string A key used to identify userlists created by this object. */
    private $useridlistid = null;
    /** @var array cached list of participants for this assignment. The cache key will be group, showactive and the context id */
    private $participants = array();

    public function __construct($coursemodulecontext, $coursemodule, $course)
    {
        $this->context = $coursemodulecontext;
        $this->course = $course;

        // Ensure that $this->coursemodule is a cm_info object (or null).
        $this->coursemodule = cm_info::create($coursemodule);

        // Temporary cache only lives for a single request - used to reduce db lookups.
        $this->cache = array();

        // Extra entropy is required for uniqid() to work on cygwin.
        $this->useridlistid = clean_param(uniqid('', true), PARAM_ALPHANUM);
    }

    /**
     * Add this instance to the database.
     *
     * @param stdClass $formdata The data submitted from the form
     * @return mixed false if an error occurs or the int id of the new instance
     */
    public function add_instance(stdClass $formdata)
    {
        global $DB;
        //$adminconfig = $this->get_admin_config();
        $update = new stdClass();
        $update->course = $formdata->course;
        $update->courseid = $formdata->course;
        $update->timemodified = time();
        $update->timecreated = time();

        $update->name = $formdata->name;
        $update->externalname = $formdata->externalname;
        $update->externallink = $formdata->externallink;
        $update->alwaysshowlink = !empty($formdata->alwaysshowlink);
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->alwaysshowdescription = !empty($formdata->alwaysshowdescription);
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->duedate = $formdata->duedate;
        $update->cutoffdate = $formdata->cutoffdate;
        $update->grade = $formdata->grade;
        $update->passingpercent = $formdata->passingpercent;

        $returnid = $DB->insert_record('assignprogram', $update);
        $this->instance = $DB->get_record('assignprogram', array('id'=>$returnid), '*', MUST_EXIST);
        // Cache the course record.
        $this->course = $DB->get_record('course', array('id'=>$formdata->course), '*', MUST_EXIST);

        return $returnid;
    }

    /**
     * Update this instance in the database.
     *
     * @param stdClass $formdata - the data submitted from the form
     * @return bool false if an error occurs
     */
    public function update_instance($formdata): bool
    {
        global $DB;
        //$adminconfig = $this->get_admin_config();
        $update = new stdClass();
        $update->id = $formdata->instance;
        $update->course = $formdata->course;
        $update->courseid = $formdata->course;
        $update->timemodified = time();
        $update->timecreated = time();

        $update->name = $formdata->name;
        $update->externalname = $formdata->externalname;
        $update->externallink = $formdata->externallink;
        $update->alwaysshowlink = !empty($formdata->alwaysshowlink);
        $update->intro = $formdata->intro;
        $update->introformat = $formdata->introformat;
        $update->alwaysshowdescription = !empty($formdata->alwaysshowdescription);
        $update->allowsubmissionsfromdate = $formdata->allowsubmissionsfromdate;
        $update->duedate = $formdata->duedate;
        $update->cutoffdate = $formdata->cutoffdate;
        $update->grade = $formdata->grade;
        $update->passingpercent = $formdata->passingpercent;

        $result = $DB->update_record('assignprogram', $update);
        $this->instance = $DB->get_record('assignprogram', array('id'=>$update->id), '*', MUST_EXIST);
        return $result;
    }

    /**
     * Delete this instance from the database.
     *
     * @return bool false if an error occurs
     */
    public function delete_instance()
    {
        global $DB;
        $result = true;
        $DB->delete_records('assignprogram_grades', array('assignment' => $this->get_instance()->id));
        $DB->delete_records('assignprogram', array('id'=>$this->get_instance()->id));

        return $result;
    }
}