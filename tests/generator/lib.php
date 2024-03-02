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

defined('MOODLE_INTERNAL') || die();

/**
 * assignexternal module data generator class
 *
 * @package mod_assignexternal
 * @category test
 * @copyright 2024 Marcel Suter <marcel@ghwalin.ch>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assignexternal_generator extends testing_module_generator {
    /**
     * Create a new instance of the assignment activity.
     *
     * @param array|stdClass|null $record
     * @param array|null $options
     * @return stdClass
     */
    public function create_instance($record = null, array $options = null) {
        $record = (object)(array)$record;
        $defaultsettings = array(
            'alwaysshowdescription'             => 1,
            'externalname'                      => 'behat_test',
            'externallink'                      => 'https://ghwalin.ch',
            'alwaysshowlink'                    => 1,
            'duedate'                           => 0,
            'allowsubmissionsfromdate'          => 0,
            'cutoffdate'                        => 0,
            'externalgrademax'                  => 100,
            'manualgrademax'                    => 10,
            'passingpercentage'                 => 60,
            'haspassinggrade'                   => 1,

        );

        foreach ($defaultsettings as $name => $value) {
            if (!isset($record->{$name})) {
                $record->{$name} = $value;
            }
        }

        return parent::create_instance($record, (array)$options);
    }
}