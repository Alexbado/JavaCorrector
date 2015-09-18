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
 * The main jcode configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package mod_jcode
 * @copyright 2011 Your Name
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined ( 'MOODLE_INTERNAL' ) || die ();

require_once ($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_jcode_mod_form extends moodleform_mod {
	
	/**
	 * Defines forms elements
	 */
	public function definition() {
		GLOBAL $mods;
		print_object ( $mods );
		$mform = $this->_form;
		
		$mform->addElement ( 'header', 'general', get_string ( 'general', 'form' ) );
		
		$mform->addElement ( 'text', 'name', get_string ( 'jcodename', 'jcode' ), array (
				'size' => '64' 
		) );
		
		if (! empty ( $CFG->formatstringstriptags )) {
			$mform->setType ( 'name', PARAM_TEXT );
		} else {
			$mform->setType ( 'name', PARAM_CLEAN );
		}
		$mform->addRule ( 'name', null, 'required', null, 'client' );
		$mform->addRule ( 'name', get_string ( 'maximumchars', '', 255 ), 'maxlength', 255, 'client' );
		$mform->addHelpButton ( 'name', 'jcodename', 'jcode' );
		
		$this->add_intro_editor();
		
		$mform->addElement ( 'text', 'parameters', get_string ( 'parameters', 'jcode' ) );
		$mform->addRule('parameters', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
		
		
		$mform->addElement ( 'text', 'correctanswer', get_string ( 'correctanswer', 'jcode' ), array (
				'size' => '128'
		) );
		
		$ctx = null;
		if ($this->current && $this->current->coursemodule) {
			$cm = get_coursemodule_from_instance('jcode', $this->current->id, 0, false, MUST_EXIST);
			$ctx = context_module::instance($cm->id);
		}
		if ($this->current && $this->current->course) {
			if (!$ctx) {
				$ctx = context_course::instance($this->current->course);
			}
		}
		
		if (empty($entry->id)) {
			$entry = new stdClass;
			$entry->id = null;
		}
		
		$name = get_string ( 'resubmit', 'jcode' );
		$mform->addElement ( 'selectyesno', 'resubmit', $name );
		
		$name = get_string('timeavaliable', 'jcode');
		$mform->addElement('date_time_selector', 'timeavaliable', $name, array('optional'=>true));
		
		$name = get_string('timedue', 'jcode');
		$mform->addElement('date_time_selector', 'timedue', $name, array('optional'=>true));
		
        $this->standard_grading_coursemodule_elements();
        $this->standard_coursemodule_elements();

		$this->add_action_buttons ();
	}
}
