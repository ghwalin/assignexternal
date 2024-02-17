<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_assignexternal
 * @category    upgrade
 * @copyright   2023 Marcel Suter <marcel@ghwalin.ch>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute mod_assignexternal upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_assignexternal_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2024021301) {
        $table = new xmldb_table('assignexternal');
        $field = new xmldb_field('haspassinggrade', XMLDB_TYPE_INTEGER, 2, null, true, false, 0, 'passingpercentage');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('hasgrade', XMLDB_TYPE_INTEGER, 2, null, true, false, 0, 'haspassinggrade');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define table assignexternal_overrides to be created.
        $table = new xmldb_table('assignexternal_overrides');

        // Adding fields to table assignexternal_overrides.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('assignexternal', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('allowsubmissionsfromdate', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');
        $table->add_field('duedate', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');
        $table->add_field('cutoffdate', XMLDB_TYPE_INTEGER, '10', null, false, null, '0');

        // Adding keys to table assignexternal_overrides.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_assignexternal', XMLDB_KEY_FOREIGN, ['assignexternal'], 'assignexternal', ['id']);

        // Adding indexes to table assignexternal_overrides.
        $table->add_index('userid', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        //$table->add_index('ix_assignexternal', XMLDB_INDEX_NOTUNIQUE, ['assignexternal']);

        // Conditionally launch create table for assignexternal_overrides.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_mod_savepoint(true, 2024021301, 'assignexternal');
    }

    return true;
}
