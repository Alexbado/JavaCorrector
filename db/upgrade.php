<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
defined('MOODLE_INTERNAL') || die();

function xmldb_jcode_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2007040100) {

        $table = new xmldb_table('jcode');
        $field = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('jcode');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('jcode');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'intro');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2007040100, 'jcode');
    }

    if ($oldversion < 2007040101) {

        $table = new xmldb_table('jcode');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'introformat');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('jcode');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
            'timecreated');

        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('jcode');
        $index = new xmldb_index('courseindex', XMLDB_INDEX_NOTUNIQUE, array('course'));

        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        upgrade_mod_savepoint(true, 2007040101, 'jcode');
    }

    if ($oldversion < 2007040200) {

        upgrade_mod_savepoint(true, 2007040200, 'jcode');
    }
    
    if ($oldversion < 2015022811) {
    	$table = new xmldb_table('jcode_files');
    	$table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    	$table->add_field('jcode_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    	$table->add_field('user_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    	$table->add_field('filename', XMLDB_TYPE_CHAR, '128', null, null, null, null);
    	$table->add_field('result', XMLDB_TYPE_TEXT, null, null, null, null, null);
    	$table->add_field('grade', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, null);
    	$table->add_field('submit_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    	$table->add_field('feedback', XMLDB_TYPE_TEXT, null, null, null, null, null);
    	$table->add_field('locked', XMLDB_TYPE_INTEGER, '1', null, null, null, '0');
    	$table->add_field('feedback_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    
    	$table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    
    	if (!$dbman->table_exists($table)) {
    		$dbman->create_table($table);
    	}
    
    	upgrade_mod_savepoint(true, 2015022811, 'jcode');
    }
    

    return true;
}
