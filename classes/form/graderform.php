<?php

namespace mod_assignprogram\form;

use moodleform;

require_once("$CFG->libdir/formslib.php");

class grader_form extends moodleform
{
    // Add elements to form.

    public function definition()
    {

        $mform = $this->_form;
        $mform->addElement(
            'header',
            'grading',
            'Bewertung ' . $this->_customdata->firstname .' ' . $this->_customdata->lastname);
        $mform->setExpanded('grading');

        $elem = $mform->addElement('text', 'status', get_string('status'));
        $mform->freeze('status');
        $mform->setType('status', PARAM_ALPHA);

        $elem = $mform->addElement('text', 'timeleft', get_string('time'));
        $mform->freeze('timeleft');
        $mform->setType('timeleft', PARAM_ALPHA);


        $mform->addElement('header', 'external', 'T-Feedback aus externem System');
        $mform->setExpanded('external');

        $elem = $mform->addElement(
            'float',
            'gradeexternal',
            'T-Bewertung (max. ' . $this->_customdata->gradeexternalmax . ')'
        );

        $elem = $mform->addElement(
            'editor',
            'feedbackexternal',
            get_string('feedback',
                null,
                self::editor_options())
        );
        $mform->setType('feedbackexternal', PARAM_RAW);


        $mform->addElement('header', 'manual', 'Manuelles Feedback');
        $mform->setExpanded('manual');

        $elem = $mform->addElement(
            'float',
            'grademanual',
            'T-Bewertung (max. ' . $this->_customdata->grademanualmax . ')'
        );

        $elem = $mform->addElement('editor', 'feedbackmanual', get_string('feedback'));
        $mform->setType('feedbackmanual', PARAM_RAW);

        $mform->addElement('hidden', 'id', $this->_customdata->assignmentid);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'userid', $this->_customdata->userid);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'gradeid', $this->_customdata->gradeid);
        $mform->setType('gradeid', PARAM_INT);
        $mform->addElement('hidden', 'action', 'grader');
        $mform->setType('action', PARAM_ALPHA);

        $buttonarray = array();
        $buttonarray[] =& $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] =& $mform->createElement('submit', 'cancel', get_string('cancel'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');

        $this->set_data($this->_customdata);
    }

    public function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        error_log(var_export($errors, true));
        return $errors;
    }

    private static function editor_options(): array
    {
        return array(
            'subdirs' => 0,
            'maxbytes' => 0,
            'maxfiles' => 0,
            'changeformat' => 0,
            'context' => null,
            'noclean' => 0,
            'trusttext' => true,
            'enable_filemanagement' => false);
    }
}