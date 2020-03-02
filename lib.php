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
 * Library of interface functions and constants.
 *
 * @package     mod_bfi
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function bfi_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_bfi into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $bfi An object from the form.
 * @param mod_bfi_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function bfi_add_instance($bfi, $mform = null) {
    global $DB;

    if(! bfi_check_feedback_completed($bfi->feedback)) {
        $feedbackname = $DB->get_field('feedback', 'name', array('id' => $bfi->feedback));
        \core\notification::error(get_string('err_feedbackcompleted', 'bfi', array('name' => $feedbackname)));
        print_error('error');
    }

    $feedbackscompleted = $DB->get_records('feedback_completed', array('feedback' => $bfi->feedback));
    $answers = bfi_calculate_dimensions($feedbackcompleted);
    
    if(isset($answers)) {
        var_dump($answers);
        print_error('error');
    }
    else {
        print_error('error');
    }

    $bfi->timecreated = time();
    $bfi->id = $DB->insert_record('bfi', $bfi);
    //file_put_contents($CFG->dataroot.'/temp/filestorage/resultscreate.json', json_encode($results));
    return $bfi->id;
}

/**
 * Updates an instance of the mod_bfi in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $bfi An object from the form in mod_form.php.
 * @param mod_bfi_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function bfi_update_instance($bfi, $mform = null) {
    global $DB, $CFG;

    $bfi->timemodified = time();
    $bfi->id = $bfi->instance;

    //file_put_contents($CFG->dataroot.'/temp/filestorage/resultsupdate.json', json_encode($results));
    return $DB->update_record('bfi', $bfi);
}

/**
 * Removes an instance of the mod_bfi from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function bfi_delete_instance($id) {
    global $DB;

    if (! $bfi = $DB->get_record('bfi', array('id' => $id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records here.

    if(! $DB->delete_records('bfi', array('id' => $id))) {
        $result = false;
    }
    if(! $DB->delete_records('bfi_characteristic_values', array('bfiid' => $id))) {
        $result = false;
    }

    return $result;
}

/**
 * Check if feedback is completed by at least one individual.
 *
 * @param int $feedbackid Id of the feedback instance.
 * @return bool True if is completed, false otherwise.
 */
function bfi_check_feedback_completed($feedbackid) {
    global $DB;

    return $DB->record_exists('feedback_completed', array('feedback' => $feedbackid));
}

/**
 * Calculate the five dimensions of each individual.
 *
 * @param object $feedbackscompleted Array of the feedback completed.
 * @return object Array with the values of each dimension, null otherwise.
 */
function bfi_calculate_dimensions($feedbackscompleted) {
    global $DB;

    if(! empty($feedbackscompleted)) {
        foreach($feedbackscompleted as $feedbackcompleted) {
            $countanswers = $DB->count_records('feedback_value', array('completed' => $feedbackcompleted->id));
            if($countanswers == 45) {
                $answers = $DB->get_records('feedback_value', array('completed' => $feedbackcompleted->id));
                $results = bfi_organize_values(array_values($answers));
                return $results;
            }
            else {
                $firstname = $DB->get_field('user', 'firstname', array('id' => $feedbackcompleted->userid));
                $lastname = $DB->get_field('user', 'lastname', array('id' => $feedbackcompleted->userid));
                \core\notification::error(get_string('err_answerscounting', 'bfi', array('fullname' => $firstname.' '.$lastname)));
                return null;
            }
        }
    }
}

/**
 * Get the feedback value of each individual.
 *
 * @param object $answers Array of the feedback value of each individual.
 * @return object Array with organized values, null if the individual doesn't agree with the informed consent.
 */
function bfi_organize_values($answers) {

    $dimensions = new stdClass();
    $dimensions->extraversion = array();
    $dimensions->agreeableness = array();
    $dimensions->conscientiousness = array();
    $dimensions->neuroticism = array();
    $dimensions->openness = array();

    if($answers[0]->value == '1') {
        for ($i=1; $i < sizeof($answers); $i++) { 
            switch ($i) {
                case 1 : case 6: case 11: case 16: case 27: case 32: case 40: case 43:
                    switch ($i) {
                        case 6: case 16: case 27:
                            $dimensions->extraversion[$i] = 6 - (int)$answers[$i]->value;
                            break;
                        default:
                            $dimensions->extraversion[$i] = (int)$answers[$i]->value;
                            break;
                    }
                    break;
                case 2: case 7: case 13: case 22: case 24: case 28: case 33: case 37: case 41:
                    switch ($i) {
                        case 2: case 13: case 22: case 33:
                            $dimensions->agreeableness[$i] = 6 - (int)$answers[$i]->value;
                            break;
                        default:
                            $dimensions->agreeableness[$i] = (int)$answers[$i]->value;
                            break;
                    }
                    break;
                case 3: case 8: case 14: case 18: case 21: case 25: case 29: case 34: case 42:
                    switch ($i) {
                        case 8: case 18: case 25: case 42:
                            $dimensions->conscientiousness[$i] = 6 - (int)$answers[$i]->value;
                            break;
                        default:
                            $dimensions->conscientiousness[$i] = (int)$answers[$i]->value;
                            break;
                    }
                    break;
                case 4: case 9: case 15: case 19: case 26: case 30: case 35: case 38:
                    switch ($i) {
                        case 9: case 19: case 35:
                            $dimensions->neuroticism[$i] = 6 - (int)$answers[$i]->value;
                            break;
                        default:
                            $dimensions->neuroticism[$i] = (int)$answers[$i]->value;
                            break;
                    }
                    break;
                case 5: case 10: case 12: case 17: case 20: case 23: case 31: case 36: case 39: case 44:
                    switch ($i) {
                        case 12: case 44:
                            $dimensions->openness[$i] = 6 - (int)$answers[$i]->value;
                            break;
                        default:
                            $dimensions->openness[$i] = (int)$answers[$i]->value;
                            break;
                    }
                    break;
                default:
                    #code...
                    break;
            }
        }

        return $dimensions;
    }

    return null;
}