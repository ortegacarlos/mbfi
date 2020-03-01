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
 * @package     moodlecore
 * @subpackage  backup-moodle2
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_bfi_activity_task
 */

/**
 * Define the complete bfi structure for backup, with file and id annotations
 */
class backup_bfi_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated
        $bfi = new backup_nested_element('bfi', array('id'), array(
            'course', 'name', 'intro', 'introformat',
            'timecreated', 'timemodified'));

        $characteristic_values = new backup_nested_element('characteristic_values');

        $characteristic_value = new backup_nested_element('characteristic_value', array('id'), array(
            'bfiid', 'userid', 'username', 'fullname', 'extraversion', 'agreeableness',
            'conscientiousness', 'neuroticism', 'openness', 'timecreated', 'timemodified'));

        // Build the tree
        $bfi->add_child($characteristic_values);
        $characteristic_values->add_child($characteristic_value);

        // Define sources
        $bfi->set_source_table('bfi', array('id' => backup::VAR_ACTIVITYID));

        $characteristic_value->set_source_sql('
            SELECT  *
            FROM    {bfi_characteristic_values}
            WHERE   bfiid = ?',
            array(backup::VAR_PARENTID));

        // Define file annotations
        $bfi->annotate_files('mod_bfi', 'intro', null); // This file area hasn't itemid

        // Return the root element (bfi), wrapped into standard activity structure
        return $this->prepare_activity_structure($bfi);
    }
}
