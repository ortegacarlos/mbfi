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