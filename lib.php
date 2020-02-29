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
    global $DB, $CFG;

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
 * Save a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @param mod_mgroup_mod_form $mform The form.
 * @return bool True if successful, false on failure.
 */
function mgroup_save_file($path, $mform = null) {

    if(isset($path, $mform)) {
        if($mform->save_file('userfile', $path, true)) {
            return true;
        }
    }

    \core\notification::error(get_string('err_savefile', 'mgroup'));
    return false;
}

/**
 * Read a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @return object Array if successful, null on failure.
 */
function mgroup_read_file($path) {
    global $MGROUP_CONTENT_FILE;

    $parameters = array();

    if(isset($path)) {
        if($content = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
            foreach($content as $line) {
                $parameters[] = explode(',', utf8_encode($line));
            }
            $MGROUP_CONTENT_FILE = $parameters;
            return $parameters;
        }
    }

    \core\notification::error(get_string('err_readfile', 'mgroup'));
    return null;
}

/**
 * Check a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @return boolean True if successful, false on failure.
 */
function mgroup_check_file($path, $characteristics) {

    $content = mgroup_read_file($path);

    if(isset($path, $characteristics, $content)) {
        $errrors = false;
        foreach($content as $line_number => $line) {
            //$parameters = explode(',', $line);
            if(! mgroup_check_parameters($line, $characteristics)) {
                $errrors = true;
                \core\notification::error(get_string('err_checkparameters', 'mgroup', array('number' => $line_number+1)));
            }
        }
        if(! $errrors) {
            return true;
        }
    }

    \core\notification::error(get_string('err_checkfile', 'mgroup'));
    return false;
}

/**
 * Check the parameters of each individual of the mod_mgroup.
 *
 * @param object $parameters Array with parameters.
 * @param int $characteristics Number of characteristics.
 * @return boolean True if successful, false on failure.
 */
function mgroup_check_parameters($parameters, $characteristics) {

    if(isset($parameters, $characteristics)) {
        if($characteristics != (count($parameters)-2)) {
            return false;
        }
        foreach($parameters as $parameter) {
            if(is_null($parameter)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Check users enrolled in the course of the mod_mgroup.
 *
 * @param string $course Course id.
 * @param string $path Text field path.
 * @return boolean True if successful, false on failure.
 */
function mgroup_check_users_in_course($course, $path) {
    global $DB, $MGROUP_CONTENT_FILE;

    if(isset($course, $path)) {
        //$content = mgroup_read_file($path);
        $errors = false;
        $sql = "SELECT  a.id, a.username, b.userid, b.modifierid
                FROM    {user} a
                JOIN    {user_enrolments} b ON a.id = b.userid
                WHERE   a.username = :id
                        AND b.modifierid = :course";
        foreach ($MGROUP_CONTENT_FILE as $user) {
            list($id, $fullname) = $user;
            if(! $DB->record_exists_sql($sql, array('id' => $id, 'course' => $course))) {
                $errors = true;
                \core\notification::error(get_string('err_user', 'mgroup', array('name' => $fullname)));
            }
        }
        if(! $errors) {
            return true;
        }
    }
    \core\notification::error(get_string('err_checkusers', 'mgroup'));
    return false;
}

/**
 * Group conformation in the course of the mod_mgroup.
 *
 * @param object $mgroup An object from the form.
 * @param string $path Text field path.
 * @return object Array with results if successful, null on failure.
 */
function mgroup_form_groups($mgroup, $path) {

    $characteristics = $mgroup->numberofcharacteristics;
    $groupsize = $mgroup->groupsize;
    $populationsize = $mgroup->populationsize;
    $selectionoperator = $mgroup->selectionoperator;
    $mutationoperator = $mgroup->mutationoperator;
    $groupingtype = (int)$mgroup->groupingtype;
    $hetecharacteristics = null;
    $homocharacteristics = null;
    if($groupingtype == 2) {
        $hetecharacteristics = array();
        $homocharacteristics = array();
        for ($i=0; $i < $characteristics; $i++) {
            $char = 'char'.($i+1);
            $characteristic = $mgroup->$char;
            if($characteristic == '1') {
                $hetecharacteristics[] = $i;
            }
            else {
                $homocharacteristics[] = $i;
            }
        }
    }

    $data = new Java('Data', $path, $groupsize, $groupingtype, $hetecharacteristics, $homocharacteristics);
    $generations = 0;
    $ga = new Java('GA', $data, $populationsize, $selectionoperator, $mutationoperator);
    $ga->initialPopulation();
    if($groupingtype == 0) {
        $ga->checkFitnessMinimize();
    }
    $ga->evaluation();
    //java_values($ga->getPopulation()[$ga->getBestPosition()]->getRawFitness()) > 0.01
    while ($generations < 1000) {
        $ga->rouletteWheelW((int)ceil(java_values($ga->getPopulationSize())) * ((double)java_values($ga->getSelectionPercent()) / 100));
        $ga->reproduction();
        $ga->mutation();
        if($groupingtype == 0) {
            $ga->checkFitnessMinimize();
        }
        $ga->evaluation();
        $generations++;
    }
    $results = java_values($ga->getPopulation()[$ga->getBestPosition()]->getGenes());
    if(isset($results)) {
        return $results;
    }
    return null;
}