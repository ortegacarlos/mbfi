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
 * The main mod_mbfi configuration form.
 *
 * @package     mod_mbfi
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_mbfi
 * @copyright  2020 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_mbfi_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $DB, $COURSE;
        
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('mbfiname', 'mbfi'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->applyFilter('name', 'trim');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'mbfiname', 'mbfi');

        // Adding grouping feedback.
        $datasource = array();
        $course = $DB->get_record('course', array('id' => $COURSE->id), '*', MUST_EXIST);
        $recordsfeedback = get_all_instances_in_course('feedback', $course);
        if(! empty($recordsfeedback)) {
            $options = array();
            foreach($recordsfeedback as $recordfeedback) {
                $options[(int)$recordfeedback->id] = $recordfeedback->name;
            }
            $datasource[] = $mform->createElement('radio', 'datasource', '', get_string('feedback', 'mbfi'), 1, null);
            $datasource[] = $mform->createElement('radio', 'datasource', '', get_string('uploadfile', 'mbfi'), 0, null);
            $mform->addGroup($datasource, 'datasourcear', get_string('datasource', 'mbfi'), array('<br />'), false);
            $mform->addHelpButton('datasourcear', 'datasource', 'mbfi');
            $mform->setDefault('datasource', $datasource[0]->_attributes['value']);
            $feedback = $mform->addElement('select', 'feedback', get_string('feedbackar', 'mbfi'), $options, null);
            $feedback->setSelected(array_key_first($options));
            $mform->addHelpButton('feedback', 'feedbackar', 'mbfi');
            $mform->hideIf('feedback', 'datasource', 'neq', 1);
        }

        // Adding the "userfile" field
        $mform->addElement('filepicker', 'userfile', get_string('userfile', 'mbfi'), null,
                array('maxbytes'=>1048576, 'accepted_types'=>'.csv'));
        $mform->addHelpButton('userfile', 'userfile', 'mbfi');
        if(empty($recordsfeedback)) {
            $mform->addRule('userfile', null, 'required', null, 'client');
        }
        else {
            $mform->hideIf('userfile', 'datasource', 'neq', 0);
        }

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
        
    }
}
