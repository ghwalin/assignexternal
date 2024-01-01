<?php

namespace mod_assignprogram\data;

use stdClass;

/**
 * represents a programming assignment
 *
 * @package   mod_assignprogram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign
{
    public $id;
    public $course;
    public $coursemodule;
    public $name;
    public $intro;
    public $introformat;
    public $alwaysshowdescription;
    public $externalname;
    public $externallink;
    public $alwaysshowlink;
    public $allowsubmissionsfromdate;
    public $duedate;
    public $cutoffdate;
    public $timemodified;
    public $externalgrademax;
    public $manualgrademax;
    public $passingpercentage;

    /**
     * constructor
     * @param stdClass|null $formdata
     * @param int|null $coursemoduleid
     */
    public function __construct(\stdClass $formdata=null, int $coursemoduleid=null)
    {
        if (!empty($formdata)) $this->load_formdata($formdata);
        else $this->load_db($coursemoduleid);
    }

    private function load_formdata(\stdClass $formdata) {
        $this->id = $formdata->instance;
        $this->coursemodule = '0'; // TODO determine coursemodule
        $this->extracted($formdata);
        $this->timemodified = time();
    }

    public function load_db($coursemoduleid) {
        global $DB;
        $sql =
            'SELECT ap.id, ap.course, coursemodule, name, intro, introformat, alwaysshowdescription, externalname, ' .
            '       externallink, alwaysshowlink, allowsubmissionsfromdate, duedate, cutoffdate, timemodified ' .
            '       externalgrademax, manualgrademax, passingpercentage' .
            ' FROM mdl_assignprogram AS ap INNER JOIN mdl_course_modules AS cm ON (ap.id = cm.instance) ' .
            'WHERE cm.id=:coursemodule';
        $data = $DB->get_record_sql(
            $sql,
            ['coursemodule' => $coursemoduleid]
        );
        error_log(var_export($data,true));
        if (!empty($data)) {
            $this->id = $data->id;
            $this->extracted($data);
            $this->timemodified = $data->timemodified;
        }
    }

    /**
     * @param $data
     * @return void
     */
    private function extracted($data): void
    {
        $this->course = $data->course;
        $this->name = $data->name;
        $this->intro = $data->intro;
        $this->introformat = $data->introformat;
        $this->alwaysshowdescription = !empty($data->alwaysshowdescription);
        $this->externalname = $data->externalname;
        $this->externallink = $data->externallink;
        $this->alwaysshowlink = !empty($data->alwaysshowlink);
        $this->allowsubmissionsfromdate = $data->allowsubmissionsfromdate;
        $this->duedate = $data->duedate;
        $this->cutoffdate = $data->cutoffdate;
        $this->externalgrademax = $data->externalgrademax;
        $this->manualgrademax = $data->manualgrademax;
        $this->passingpercentage = $data->passingpercentage;
    }
}