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
 * @package     mod_mbfi
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

//The content of the text file to be used in later functions
global $MBFI_CONTENT_FILE;
$MBFI_CONTENT_FILE = null;

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function mbfi_supports($feature) {
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
 * Saves a new instance of the mod_mbfi into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $mbfi An object from the form.
 * @param mod_mbfi_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function mbfi_add_instance($mbfi, $mform = null) {
    global $DB, $CFG, $MBFI_CONTENT_FILE;

    $path = $CFG->dataroot.'/temp/filestorage/mbfiuserfile_'.(time() + rand()).'.csv';
    $feedbackscompleted = null;
    $datasource = '0'; // data source file

    if (isset($mbfi->datasource)) {
        $datasource = $mbfi->datasource;
    }

    if ($datasource == '0') {
        if (!mbfi_save_file($path, $mform)) {
            print_error('error');
        }

        if (!mbfi_check_file(45, $path)) {
            mbfi_delete_file($path);
            print_error('error');
        }

        $feedbackscompleted = mbfi_organize_file_data();

        if (empty($feedbackscompleted)) {
            mbfi_delete_file($path);
            print_error('error');
        }
    } else {
        if (!mbfi_check_feedback_completed($mbfi->feedback)) {
            $feedbackname = $DB->get_field('feedback', 'name', array('id' => $mbfi->feedback));
            \core\notification::error(get_string('err_feedbackcompleted', 'mbfi', array('name' => $feedbackname)));
            print_error('error');
        }
    
        $feedbackscompleted = $DB->get_records('feedback_completed', array('feedback' => $mbfi->feedback));
    }

    $dimensionsdata = mbfi_calculate_dimensions($feedbackscompleted);
    
    if (isset($dimensionsdata)) {
        $mbfi->timecreated = time();
        $mbfi->id = $DB->insert_record('mbfi', $mbfi);
        foreach ($dimensionsdata as $dimensiondata) {
            $dimensiondata->mbfiid = $mbfi->id;
            $dimensiondata->timecreated = time();
            $DB->insert_record('mbfi_characteristic_values', $dimensiondata);
        }
    } else {
        mbfi_delete_file($path);
        \core\notification::error(get_string('err_calculatedimensions', 'mbfi'));
        print_error('error');
    }

    mbfi_delete_file($path);

    return $mbfi->id;
}

/**
 * Updates an instance of the mod_mbfi in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $mbfi An object from the form in mod_form.php.
 * @param mod_mbfi_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function mbfi_update_instance($mbfi, $mform = null) {
    global $DB, $CFG, $MBFI_CONTENT_FILE;

    $path = $CFG->dataroot.'/temp/filestorage/mbfiuserfile_'.(time() + rand()).'.csv';
    $feedbackscompleted = null;
    $datasource = '0';

    if (isset($mbfi->datasource)) {
        $datasource = $mbfi->datasource;
    }

    if ($datasource == '0') {
        if (!mbfi_save_file($path, $mform)) {
            print_error('error');
        }

        if (!mbfi_check_file(45, $path)) {
            mbfi_delete_file($path);
            print_error('error');
        }

        $feedbackscompleted = mbfi_organize_file_data();

        if (empty($feedbackscompleted)) {
            mbfi_delete_file($path);
            print_error('error');
        }
    } else {
        if (!mbfi_check_feedback_completed($mbfi->feedback)) {
            $feedbackname = $DB->get_field('feedback', 'name', array('id' => $mbfi->feedback));
            \core\notification::error(get_string('err_feedbackcompleted', 'mbfi', array('name' => $feedbackname)));
            print_error('error');
        }
    
        $feedbackscompleted = $DB->get_records('feedback_completed', array('feedback' => $mbfi->feedback));
    }

    $dimensionsdata = mbfi_calculate_dimensions($feedbackscompleted);
    
    if (isset($dimensionsdata)) {
        $individualsdimensions = $DB->get_records('mbfi_characteristic_values', array('mbfiid' => $mbfi->instance));
        foreach ($individualsdimensions as $individualdimensions) {
            $DB->delete_records('mbfi_characteristic_values', array('id' => $individualdimensions->id));
        }
        foreach ($dimensionsdata as $dimensiondata) {
            $dimensiondata->mbfiid = $mbfi->instance;
            $dimensiondata->timecreated = time();
            $DB->insert_record('mbfi_characteristic_values', $dimensiondata);
        }
    } else {
        mbfi_delete_file($path);
        \core\notification::error(get_string('err_calculatedimensions', 'mbfi'));
        print_error('error');
    }

    mbfi_delete_file($path);
    $mbfi->id = $mbfi->instance;
    $mbfi->timemodified = time();

    return $DB->update_record('mbfi', $mbfi);
}

/**
 * Removes an instance of the mod_mbfi from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function mbfi_delete_instance($id) {
    global $DB;

    if (!$mbfi = $DB->get_record('mbfi', array('id' => $id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records here.

    if (!$DB->delete_records('mbfi', array('id' => $id))) {
        $result = false;
    }
    if (!$DB->delete_records('mbfi_characteristic_values', array('mbfiid' => $id))) {
        $result = false;
    }

    return $result;
}

/**
 * Save a text file of the mod_mbfi.
 *
 * @param string $path Text file path.
 * @param mod_mbfi_mod_form $mform The form.
 * @return bool True if successful, false on failure.
 */
function mbfi_save_file($path, $mform) {

    if (isset($path, $mform)) {
        if ($mform->save_file('userfile', $path, true)) {
            return true;
        }
    }

    \core\notification::error(get_string('err_savefile', 'mbfi'));
    return false;
}

/**
 * Delete a text file of the mod_bfi.
 *
 * @param string $path Text file path.
 * @return bool True if successful, false on failure.
 */
function mbfi_delete_file($path) {

    if (isset($path)) {
        if (unlink($path)) {
            return true;
        }
    }

    \core\notification::error(get_string('err_deletefile', 'mbfi'));
    return false;
}

/**
 * Read a text file of the mod_mbfi.
 *
 * @param string $path Text file path.
 * @return object Array if successful, null on failure.
 */
function mbfi_read_file($path) {
    global $MBFI_CONTENT_FILE;

    $answers = array();

    if (isset($path)) {
        if ($content = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
            foreach ($content as $line) {
                $answers[] = explode(',', $line);
            }
            $header = array_shift($answers);
            $MBFI_CONTENT_FILE = $answers;
            return $answers;
        }
    }

    \core\notification::error(get_string('err_readfile', 'mbfi'));
    return null;
}

/**
 * Check a text file of the mod_mbfi.
 *
 * @param int $answers Number of answers.
 * @return boolean True if successful, false on failure.
 */
function mbfi_check_file($answers, $path) {

    $content = mbfi_read_file($path);

    if (isset($answers, $content)) {
        $errrors = false;
        foreach ($content as $line_number => $line) {
            if (!mbfi_check_answers($line, $answers)) {
                $errrors = true;
                \core\notification::error(get_string('err_checkparameters', 'mbfi', array('number' => $line_number + 1)));
            }
        }
        if (!$errrors) {
            return true;
        }
    }

    \core\notification::error(get_string('err_checkfile', 'mbfi'));
    return false;
}

/**
 * Check the answers of each individual of the mod_mbfi.
 *
 * @param object $answers Array with answers.
 * @param int $amountanswers Amount of answers.
 * @return boolean True if successful, false on failure.
 */
function mbfi_check_answers($answers, $amountanswers) {

    if (isset($answers, $amountanswers)) {
        if ($amountanswers != (count($answers) - 6)) {
            return false;
        }
        for ($i = 6; $i < count($answers); $i++) {
            if (is_null($answers[$i])) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Check if feedback is completed by at least one individual.
 *
 * @param int $feedbackid Id of the feedback instance.
 * @return bool True if is completed, false otherwise.
 */
function mbfi_check_feedback_completed($feedbackid) {
    global $DB;

    if (!empty($feedbackid)) {
        return $DB->record_exists('feedback_completed', array('feedback' => $feedbackid));
    }

    \core\notification::error(get_string('err_nonefeedback', 'mbfi'));
    return true;
}

/**
 * Organize user file data.
 *
 * @return object Array with data of each individual, null on failure.
 */
function mbfi_organize_file_data() {
    global $MBFI_CONTENT_FILE;
    $dataindividuals = array();

    if (!empty($MBFI_CONTENT_FILE)) {
        foreach ($MBFI_CONTENT_FILE as $line) {
            $data = new stdClass();
            $data->amountanswers = 45;
            $data->fullname = str_replace('"', '', $line[0]);
            $data->email = $line[2];
            $data->answers = array();
            $line[6] = ($line[6] == 'Si' || $line[6] == 'Yes') ? '1' : '2';
            for ($i = 6; $i < count($line); $i++) {
                $answer = new stdClass();
                $answer->value = $line[$i];
                $data->answers[] = $answer;
            }
    
            $dataindividuals[] = $data;
        }
    
        return $dataindividuals;
    }
    
    \core\notification::error(get_string('err_organizefiledata', 'mbfi'));
    return null;
}

/**
 * Calculate the five dimensions of each individual.
 *
 * @param object $feedbackscompleted Array of the feedback completed or data file.
 * @return object Array with the values of each dimension, null otherwise.
 */
function mbfi_calculate_dimensions($feedbackscompleted) {
    global $DB;

    if (!empty($feedbackscompleted)) {
        $datavalues = array();
        $userid = null;
        $username = null;
        $fullname = null;
        $email = null;
        foreach ($feedbackscompleted as $index => $feedbackcompleted) {
            $amountanswers = (isset($feedbackcompleted->id)) ? $DB->count_records('feedback_value', array('completed' => $feedbackcompleted->id)) : $feedbackcompleted->amountanswers;
            if (isset($feedbackcompleted->userid)) {
                $userid = $feedbackcompleted->userid;
                $datauser = $DB->get_record('user', array('id' => $userid));
                $username = $datauser->username;
                $fullname = $datauser->firstname.' '.$datauser->lastname;
                $email = $datauser->email;
            } else {
                $userid = 0 - ($index + 1);
                $username = substr(str_shuffle('0123456789'), 0, 8);
                $fullname = $feedbackcompleted->fullname;
                $email = $feedbackcompleted->email;
            }
            if ($amountanswers == 45) {
                $answers = (isset($feedbackcompleted->id)) ? array_values($DB->get_records('feedback_value', array('completed' => $feedbackcompleted->id))) : $feedbackcompleted->answers;
                $results = mbfi_organize_values($answers);
                if (!empty($results)) {
                    $dimension = mbfi_calculate_values($results);
                    $data = new stdClass();
                    $data->mbfiid = null;
                    $data->userid = $userid;
                    $data->username = $username;
                    $data->fullname = $fullname;
                    $data->email = $email;
                    $data->extraversion = $dimension->extraversion;
                    $data->agreeableness = $dimension->agreeableness;
                    $data->conscientiousness = $dimension->conscientiousness;
                    $data->neuroticism = $dimension->neuroticism;
                    $data->openness = $dimension->openness;
                    $data->timecreated = 0;
                    $data->timemodified = 0;
                    $datavalues[] = $data;
                }
            } else {
                \core\notification::error(get_string('err_answerscounting', 'mbfi', array('fullname' => $firstname.' '.$lastname)));
                return null;
            }
        }

        return $datavalues;
    }

    return null;
}

/**
 * Get the feedback value of each individual.
 *
 * @param object $answers Array of the feedback value of each individual.
 * @return object Array with organized values, null if the individual doesn't agree with the informed consent.
 */
function mbfi_organize_values($answers) {

    $dimensions = new stdClass();
    $dimensions->extraversion = array();
    $dimensions->agreeableness = array();
    $dimensions->conscientiousness = array();
    $dimensions->neuroticism = array();
    $dimensions->openness = array();

    if ($answers[0]->value == '1') {
        for ($i=1; $i < count($answers); $i++) { 
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
                    break;
            }
        }

        return $dimensions;
    }

    return null;
}

/**
 * Calculate value of each dimension.
 *
 * @param object $dimensionsvalues Array with values of each individual.
 * @return object Array with the value of each dimension, null otherwise.
 */
function mbfi_calculate_values($dimensionsvalues) {

    if (!empty($dimensionsvalues)) {
        $dimension = new stdClass();
        $dimension->extraversion = number_format((array_sum($dimensionsvalues->extraversion) / sizeof($dimensionsvalues->extraversion)), 4);
        $dimension->agreeableness = number_format((array_sum($dimensionsvalues->agreeableness) / sizeof($dimensionsvalues->agreeableness)), 4);
        $dimension->conscientiousness = number_format((array_sum($dimensionsvalues->conscientiousness) / sizeof($dimensionsvalues->conscientiousness)), 4);
        $dimension->neuroticism = number_format((array_sum($dimensionsvalues->neuroticism) / sizeof($dimensionsvalues->neuroticism)), 4);
        $dimension->openness = number_format((array_sum($dimensionsvalues->openness) / sizeof($dimensionsvalues->openness)), 4);

        return $dimension;
    }

    return null;
}