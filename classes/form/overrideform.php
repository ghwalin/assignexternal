<?php

namespace mod_assignexternal\form;

use moodleform;

require_once("$CFG->libdir/formslib.php");
/**
 * definition and validation of the grading form
 *
 * @package   mod_assignexternal
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override_form extends moodleform
{

    /**
     * definition of the grader form
     * @return void
     * @throws \coding_exception
     */
    public function definition()
    {
        $mform = $this->_form;
        $mform->addElement(
            'header',
            'extension',
            get_string('grantextension', 'assignexternal')
        );
        $mform->setExpanded('extension');
        $mform->addElement('static', 'selectedusers', get_string('selectedusers'), '');
        $count = 0;

        foreach ($this->_customdata->users as $userid => $user) {
            $mform->addElement('hidden', 'uid[' . $count . ']', $userid);
            $mform->addElement('static', 'fullname' . $count, '', $user->firstname . ' ' . $user->lastname);
            $count++;
        }

        $options = array('optional' => true);
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', get_string('allowsubmissionsfromdate', 'assignexternal'), $options);
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'assignexternal');

        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'assignexternal'), $options);
        $mform->addHelpButton('duedate', 'duedate', 'assignexternal');

        $mform->addElement('date_time_selector', 'cutoffdate', get_string('cutoffdate', 'assign'), $options);
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'assignexternal');

        $mform->addElement('hidden', 'id', $this->_customdata->id);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons();
        $this->set_data($this->_customdata);
    }

    /**
     * validates the formdata
     * @param $data
     * @param $files
     * @return array  error messages
     */
    public function validation($data, $files): array
    {
        $errors = parent::validation($data, $files);  // TODO validate dates
        error_log(var_export($errors, true));
        return $errors;
    }
}