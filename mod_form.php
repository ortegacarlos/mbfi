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
 * The main mod_bfi configuration form.
 *
 * @package     mod_bfi
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_bfi
 * @copyright  2020 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_bfi_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $DB, $COURSE;
        
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('bfiname', 'bfi'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->applyFilter('name', 'trim');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'bfiname', 'bfi');

        // Adding grouping feedback.
        $feedback = array();
        //$recordsfeedback = $DB->get_records('feedback', array('course' => $COURSE->id));
        $course = $DB->get_record('course', array('id' => $COURSE->id), '*', MUST_EXIST);
        $recordsfeedback = get_all_instances_in_course('feedback', $course);
        if(! empty($recordsfeedback)) {
            foreach($recordsfeedback as $recordfeedback) {
                $feedback[] = $mform->createElement('radio', 'feedback', '', $recordfeedback->name, (int)$recordfeedback->id, null);
            }
            $mform->addGroup($feedback, 'feedbackar', get_string('feedbackar', 'bfi'), array('<br />'), false);
            $mform->addRule('feedbackar', null, 'required', null, 'client');
            $mform->setDefault('feedback', $mform->getElementValue('feedback'));
            $mform->addHelpButton('feedbackar', 'feedbackar', 'bfi');
        }
        else {
            //\core\notification::error(get_string('err_recordsfeedback', 'bfi'));
            //echo $OUTPUT->notification(get_string('err_recordsfeedback', 'bfi'));
            $mform->addElement('html', '<span class="notifications" id="user-notifications">');
            $mform->addElement('html', '<div class="alert alert-danger alert-block fade in " role="alert" data-aria-autofocus="true" tabindex="0">');
            $mform->addElement('html', get_string('err_recordsfeedback', 'bfi'));
            $mform->addElement('html', '</div>');
            $mform->addElement('html', '</span>');
        }

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
        
    }
}
