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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of jcode
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package mod_jcode
 * @copyright 2011 Your Name
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace jcode with the name of your module and remove this line.
require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/config.php');
require_once (dirname ( __FILE__ ) . '/lib.php');
global $DB, $USER;

$id = optional_param ( 'id', 0, PARAM_INT );
$n = optional_param ( 'n', 0, PARAM_INT ); 
$page = optional_param ( 'page','', PARAM_TEXT );

if ($id) {
	$cm = get_coursemodule_from_id ( 'jcode', $id, 0, false, MUST_EXIST );
	$course = $DB->get_record ( 'course', array (
			'id' => $cm->course 
	), '*', MUST_EXIST );
	$jcode = $DB->get_record ( 'jcode', array (
			'id' => $cm->instance 
	), '*', MUST_EXIST );
} else if ($n) {
	$jcode = $DB->get_record ( 'jcode', array (
			'id' => $n 
	), '*', MUST_EXIST );
	$course = $DB->get_record ( 'course', array (
			'id' => $jcode->course 
	), '*', MUST_EXIST );
	$cm = get_coursemodule_from_instance ( 'jcode', $jcode->id, $course->id, false, MUST_EXIST );
} else {
	error ( 'You must specify a course_module ID or an instance ID' );
}

require_login ( $course, true, $cm );
$context = context_module::instance ( $cm->id );

$event = \mod_jcode\event\course_module_viewed::create ( array (
		'objectid' => $PAGE->cm->instance,
		'context' => $PAGE->context 
) );
// Print the page header.

$PAGE->set_url ( '/mod/jcode/view.php', array (
		'id' => $cm->id 
) );
$PAGE->set_title ( format_string ( $jcode->name ) );
$PAGE->set_heading ( format_string ( $course->fullname ) );
$PAGE->set_context ( $context );
$download = optional_param ( 'download', 0, PARAM_INT );
if($download){
	require_once 'download.php';
}

// Output starts here.
echo $OUTPUT->header ();

echo $OUTPUT->container_start('submissionstatustable');
if ($page == 'lista') {
	echo "<center><a href='{$CFG->wwwroot}/mod/jcode/view.php?id=$id'>Voltar</a></center>";
	echo $OUTPUT->heading ( 'Avaliar Envios' );
	require_once 'lista.php';
} else {
	if ($jcode->intro) {
		echo $OUTPUT->heading($jcode->name, 3);
		echo $OUTPUT->box ( format_module_intro ( 'jcode', $jcode, $cm->id ), 'generalbox mod_introbox', 'jcodeintro' );
	}
	if(!is_siteadmin($USER)){
		echo $OUTPUT->heading ( 'Envio de Atividade' );
		require_once 'submission.php';
	}
	if (has_capability ( 'mod/jcode:manager', $context )) {
		echo "<center><a href='{$CFG->wwwroot}/mod/jcode/view.php?id=$id&page=lista'>Ver/Avaliar todos os envios</a></center>";
	}
}

echo $OUTPUT->container_end();

// Finish the page.
echo $OUTPUT->footer ();
