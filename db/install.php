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
 * Code to be executed after the plugin's database scheme has been installed is defined here.
 *
 * @package     mod_vivavoce
 * @category    upgrade
 * @copyright   2024 Saadh Zahid<saadhzahidwork@.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom code to be run on installing the plugin.
 */

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

// Standard Moodle library.
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/dmllib.php');

// Create the database tables if they don't exist.
function xmldb_mod_vivavoce_install() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define the new table 'mdl_vivavoce' if it doesn't exist.
    $table = new xmldb_table('vivavoce');

    // Define columns for the table.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('goals_objectives', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('deliverables', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('constraints', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('other_info', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('datedue', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('questiontype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('manualquestions', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('displayquestions', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('allquestions', XMLDB_TYPE_TEXT, null, null, null, null, null);

    // Add keys.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Conditionally launch create table for vivavoce.
    if (!$dbman->table_exists($table)) {
        $dbman = $DB->get_manager();
        $dbman->create_table($table);
    }

    // Define the new table 'mdl_vivavocesubmissions' if it doesn't exist.
    $table = new xmldb_table('vivavocesubmissions');

    // Define columns for the table.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('assignment_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('dissofile', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('submission', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('isgraded', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
    $table->add_field('feedback', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('viewblock', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

    // Add keys.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_assignment_instance', XMLDB_KEY_FOREIGN, array('assignment_instance_id'), 'vivavoce', array('id'));

    // Conditionally launch create table for vivavocesubmissions.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }






    // Install the table.
    return true;
}
function xmldb_vivavoce_install() {
    global $DB;
    $dbman = $DB->get_manager();

    // Define the new table 'mdl_vivavoce' if it doesn't exist.
    $table = new xmldb_table('vivavoce');

    // Define columns for the table.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('course', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('goals_objectives', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('deliverables', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('constraints', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('other_info', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('datedue', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
    $table->add_field('questiontype', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('manualquestions', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('displayquestions', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('allquestions', XMLDB_TYPE_TEXT, null, null, null, null, null);

    // Add keys.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

    // Conditionally launch create table for vivavoce.
    if (!$dbman->table_exists($table)) {
        $dbman = $DB->get_manager();
        $dbman->create_table($table);
    }

    // Define the new table 'mdl_vivavocesubmissions' if it doesn't exist.
    $table = new xmldb_table('vivavocesubmissions');

    // Define columns for the table.
    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('assignment_instance_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
    $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
    $table->add_field('dissofile', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
    $table->add_field('submission', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('grade', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
    $table->add_field('isgraded', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
    $table->add_field('feedback', XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table->add_field('viewblock', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');

    // Add keys.
    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('fk_assignment_instance', XMLDB_KEY_FOREIGN, array('assignment_instance_id'), 'vivavoce', array('id'));

    // Conditionally launch create table for vivavocesubmissions.
    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }






    // Install the table.
    return true;
}