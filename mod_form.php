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
 * This file contains the forms to create and edit an instance of this module
 *
 * @package   mod_assignprogamram
 * @copyright 2023 Marcel Suter <marcel@ghwalin.ch>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Assignment program settings form.
 *
 * @package   mod_assign
 */
class mod_assignexternal_mod_form extends moodleform_mod
{
    function definition()
    {
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('assignmentname', 'assign'), ['size'=>'64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('text', 'externalname', get_string('externalname', 'assignexternal'), ['size'=>'64']);
        $mform->setType('externalname', PARAM_TEXT);
        $mform->addHelpButton('externalname', 'externalname', 'assignexternal');

        $mform->addElement('text', 'externallink', get_string('externallink', 'assignexternal'), ['size'=>'64']);
        $mform->setType('externallink', PARAM_TEXT);
        $mform->addRule('externallink', null, 'required', null, 'client');
        $mform->addHelpButton('externallink', 'externallink', 'assignexternal');

        $mform->addElement('checkbox', 'alwaysshowlink', get_string('alwaysshowlink', 'assignexternal'));
        $mform->addHelpButton('alwaysshowlink', 'alwaysshowlink', 'assignexternal');

        $this->standard_intro_elements(get_string('description', 'assign'));

        $mform->addElement('checkbox', 'alwaysshowdescription', get_string('alwaysshowdescription', 'assign'));
        $mform->addHelpButton('alwaysshowdescription', 'alwaysshowdescription', 'assign');
        $mform->disabledIf('alwaysshowdescription', 'allowsubmissionsfromdate[enabled]', 'notchecked');

        $mform->addElement('header', 'availability', get_string('availability', 'assign'));
        $mform->setExpanded('availability', true);

        $options = array('optional'=>true);
        $mform->addElement('date_time_selector', 'allowsubmissionsfromdate', get_string('allowsubmissionsfromdate', 'assign'), $options);
        $mform->addHelpButton('allowsubmissionsfromdate', 'allowsubmissionsfromdate', 'assign');

        $mform->addElement('date_time_selector', 'duedate', get_string('duedate', 'assign'), $options);
        $mform->addHelpButton('duedate', 'duedate', 'assign');

        $mform->addElement('date_time_selector', 'cutoffdate', get_string('cutoffdate', 'assign'), $options);
        $mform->addHelpButton('cutoffdate', 'cutoffdate', 'assign');

        $mform->addElement('header', 'grading', get_string('grading', 'assignexternal'));
        $mform->setExpanded('grading', true);

        $mform->addElement('float', 'externalgrademax', get_string('externalgrademax', 'assignexternal'));
        $mform->addRule('externalgrademax', null, 'required', null, 'client');
        $mform->setDefault('externalgrademax', 100);
        $mform->addHelpButton('externalgrademax', 'externalgrademax', 'assignexternal');

        $mform->addElement('float', 'manualgrademax', get_string('manualgrademax', 'assignexternal'));
        $mform->addRule('manualgrademax', null, 'required', null, 'client');
        $mform->setDefault('manualgrademax', 0);
        $mform->addHelpButton('manualgrademax', 'manualgrademax', 'assignexternal');

        $mform->addElement('float', 'passingpercentage', get_string('passingpercentage', 'assignexternal'));
        $mform->addRule('passingpercentage', null, 'required', null, 'client');
        $mform->setDefault('passingpercentage', 60);
        $mform->addHelpButton('passingpercentage', 'passingpercentage', 'assignexternal');

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }


}