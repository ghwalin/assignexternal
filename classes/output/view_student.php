<?php

namespace mod_assignexternal\output;

use core\context;
use mod_assignexternal\data\assign;
use mod_assignexternal\data\grade;
use renderable;
use renderer_base;
use templatable;

/**
 * Renderer for external assignment for students
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class view_student implements renderable, templatable
{
    private int|null $coursemoduleid;
    /** @var context the context of the course module for this assign instance
     *               (or just the course if we are creating a new one)
     */
    private context $context;

    /**
     * default constructor
     * @param int $coursemoduleid
     * @param context $context
     */
    public function __construct(int $coursemoduleid, context $context)
    {
        $this->coursemoduleid = $coursemoduleid;
        $this->context = $context;
    }

    /**
     * Export this data, so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return \stdClass
     * @throws \dml_exception|\coding_exception
     */
    public function export_for_template(renderer_base $output): \stdClass
    {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/assign.php');
        $assignment = new assign();
        $assignment->load_db($this->coursemoduleid, $USER->id);
        require_once($CFG->dirroot . '/mod/assignexternal/classes/data/grade.php');
        $grade = new grade();
        $grade->load_db($this->coursemoduleid, $USER->id);

        $data = new \stdClass();
        $data->externallink = $grade->getExternallink();
        $data->gradingstatus = 'TODO';
        $data->modified = format_time($assignment->getTimemodified());
        $timeremaining = $assignment->getDuedate() - time();
        if ($timeremaining<= 0) {
            $due = get_string('assignmentisdue', 'assignexternal');
        } else {
            $due = format_time($timeremaining);
        }
        $data->timeremaining = $due;

        $data->externalgrade = $grade->getExternalgrade();
        $data->externalgrademax = $assignment->getExternalgrademax();
        $data->manualgrade = $grade->getManualgrade();
        $data->manualgrademax = $assignment->getManualgrademax();
        $data->totalgrade = $data->externalgrade + $data->manualgrade;
        $data->totalgrademax = $data->externalgrademax + $data->manualgrademax;

        $data->externalfeedback = format_text($grade->getExternalfeedback(), FORMAT_MARKDOWN);
        $data->manualfeedback = $grade->getManualfeedback();
        return $data;
    }
}