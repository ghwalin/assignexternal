<?php

namespace mod_assignprogram\controller;
use cm_info;
use mod_assignprogram\data\Assign;
class assign_control
{
    /** @var stdClass the assignment record that contains the global settings for this assign instance */
    private mixed $instance;

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


    /**
     * default constructor
     */
    public function __construct($coursemodulecontext, $coursemodule, $course)
    {
        global $CFG;
        require_once ($CFG->libdir . '/modinfolib.php');
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
    public function add_instance($formdata)
    {
        global $DB;
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignprogram/classes/data/assign.php');
        $assign = new Assign($formdata);
        $returnid = $DB->insert_record('assignprogram', $assign);
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
        error_log("instance: " . $formdata->instance);
        global $DB;
        global $CFG;
        require_once($CFG->dirroot . '/mod/assignprogram/classes/data/assign.php');
        $assign = new Assign($formdata);
        error_log("assignmentID: " . $assign->getId());
        $result = $DB->update_record('assignprogram', $assign);
        $this->setInstance( $DB->get_record('assignprogram', array('id'=>$assign->getId()), '*', MUST_EXIST));
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

    public function getInstance(): mixed
    {
        return $this->instance;
    }

    public function setInstance(mixed $instance): void
    {
        $this->instance = $instance;
    }


    public function getContext(): context
    {
        return $this->context;
    }

    public function setContext(context $context): void
    {
        $this->context = $context;
    }

    public function getCourse(): stdClass
    {
        return $this->course;
    }

    public function setCourse(stdClass $course): void
    {
        $this->course = $course;
    }

    public function getCoursemodule(): cm_info
    {
        return $this->coursemodule;
    }

    public function setCoursemodule(cm_info $coursemodule): void
    {
        $this->coursemodule = $coursemodule;
    }

    public function getCache(): array
    {
        return $this->cache;
    }

    public function setCache(array $cache): void
    {
        $this->cache = $cache;
    }

    public function getUseridlistid(): ?string
    {
        return $this->useridlistid;
    }

    public function setUseridlistid(?string $useridlistid): void
    {
        $this->useridlistid = $useridlistid;
    }


}