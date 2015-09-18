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
 * File containing the form definition to post in the forum.
 *
 * @package mod_forum
 * @copyright Jamie Pratt <me@jamiep.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined ( 'MOODLE_INTERNAL' ) || die ();
require_once ($CFG->libdir . '/formslib.php');
require_once ($CFG->dirroot . '/repository/lib.php');

/**
 * Class to post in a forum.
 *
 * @package mod_forum
 * @copyright Jamie Pratt <me@jamiep.org>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_submission_form extends moodleform {
	
	public static function attachment_options() {
		global $COURSE, $PAGE, $CFG;
		$maxbytes = get_user_max_upload_file_size ( $PAGE->context, $CFG->maxbytes, $COURSE->maxbytes, 0 );
		return array (
				'subdirs' => 0,
				'maxbytes' => $maxbytes,
				'maxfiles' => 1,
				'accepted_types' => '*',
				'return_types' => FILE_INTERNAL 
		);
	}
	function definition() {
		global $CFG, $OUTPUT;
		
		$mform = & $this->_form;
		
		$course = $this->_customdata['course'];
		$jcode = $this->_customdata['jcode'];
		
		$mform->addElement ( 'filepicker', 'userfile', get_string ( 'file' ), $this->attachment_options () );
		
		$submit_string = get_string('submit');
		$this->add_action_buttons ( true, $submit_string );

		$mform->addElement('hidden', 'course');
		$mform->setType('course', PARAM_INT);
		
		$mform->addElement('hidden', 'jcode');
		$mform->setType('jcode', PARAM_INT);
	}
}
