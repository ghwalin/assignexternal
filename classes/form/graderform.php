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
namespace mod_assignexternal\form;

defined('MOODLE_INTERNAL') || die();
use coding_exception;
use moodleform;

require_once("$CFG->libdir/formslib.php");

/**
 * definition and validation of the grading form
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grader_form extends moodleform {

    /**
     * definition of the grader form
     * @return void
     * @throws coding_exception
     */
    public function definition() {

        $mform = $this->_form;
        $mform->addElement(
            'header',
            'grading',
            get_string('grading', 'assignexternal') . ' ' . $this->_customdata->firstname . ' ' . $this->_customdata->lastname);
        $mform->setExpanded('grading');

        $mform->addElement('static', 'status', get_string('status'), $this->_customdata->status);
        $mform->addElement(
            'static',
            'timeremaining',
            get_string('time'),
            $this->_customdata->timeremainingstr
        );

        $mform->addElement('header', 'external', get_string('externalfeedback', 'assignexternal'));
        $mform->setExpanded('external');
        $mform->addElement(
            'static',
            'externallink_static',
            get_string('externallink', 'assignexternal'),
            $this->_customdata->externallink
        );
        $mform->addElement(
            'float',
            'externalgrade',
            get_string('grading', 'assignexternal') .
            ' (max. ' . (float)$this->_customdata->externalgrademax . ')'
        );

        $mform->addElement(
            'editor',
            'externalfeedback',
            get_string('feedback',
                null,
                self::editor_options())
        );
        $mform->setType('externalfeedback', PARAM_RAW);

        $mform->addElement('header', 'manual', get_string('manualfeedback', 'assignexternal'));
        $mform->setExpanded('manual');

        $mform->addElement(
            'float',
            'manualgrade',
            get_string('grading', 'assignexternal') .
            ' (max. ' . (float)$this->_customdata->manualgrademax . ')'
        );
        $mform->setDefault('manualgrade', 0);
        $mform->addRule(
            'manualgrade',
            get_string('mandatory', 'assignexternal'),
            'numeric',
            '',
            'client'
        );

        $mform->addElement('editor', 'manualfeedback', get_string('feedback'));
        $mform->setType('manualfeedback', PARAM_RAW);

        // --- For development only ---
        $mform->addElement('header', 'development', 'Infos');
        $mform->addElement('static', 'courseidx', 'CourseId', $this->_customdata->courseid);
        $mform->addElement('static', 'idx', 'CourseModuleId', $this->_customdata->id);
        $mform->addElement('static', 'instancex', 'Instance', $this->_customdata->id);
        $mform->addElement('static', 'useridx', 'UserId', $this->_customdata->userid);
        $mform->addElement('static', 'gradeidx', 'GradeId', $this->_customdata->gradeid);
        $mform->addElement('static', 'graderx', 'Grader', $this->_customdata->gradeid);
        // --- For development only ---

        $mform->addElement('hidden', 'id', $this->_customdata->id);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'courseid', $this->_customdata->courseid);
        $mform->setType('courseid', PARAM_INT);
        $mform->addElement('hidden', 'instance', $this->_customdata->id);
        $mform->setType('instance', PARAM_INT);
        $mform->addElement('hidden', 'userid', $this->_customdata->userid);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'gradeid', $this->_customdata->gradeid);
        $mform->setType('gradeid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'grader');
        $mform->setType('action', PARAM_ALPHA);
        $mform->addElement('hidden', 'externallink', $this->_customdata->externallink);
        $mform->setType('externallink', PARAM_ALPHA);

        $buttonarray = [];
        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', [' '], false);
        $mform->closeHeaderBefore('buttonar');

        $this->set_data($this->_customdata);
    }

    /**
     * returns an array of options for the editor
     * @return array  options for the editor
     */
    private static function editor_options(): array {
        return [
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => FORMAT_MARKDOWN,
            'context' => null,
            'noclean' => 0,
            'trusttext' => true,
            'enable_filemanagement' => false,
        ];
    }

    /**
     * validates the formdata
     * @param $data
     * @param $files
     * @return array  error messages
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);  // TODO validate grade external/manual.
        debugging(var_export($errors, true));
        return $errors;
    }
}
