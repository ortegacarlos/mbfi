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
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_mbfi_activity_task
 */

/**
 * Define the complete mbfi structure for backup, with file and id annotations
 */
class backup_mbfi_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated
        $mbfi = new backup_nested_element('mbfi', array('id'), array(
            'course', 'name', 'intro', 'introformat',
            'timecreated', 'timemodified'));

        $characteristic_values = new backup_nested_element('characteristic_values');

        $characteristic_value = new backup_nested_element('characteristic_value', array('id'), array(
            'userid', 'username', 'fullname', 'email', 'extraversion', 'agreeableness',
            'conscientiousness', 'neuroticism', 'openness', 'timecreated', 'timemodified'));

        // Build the tree
        $mbfi->add_child($characteristic_values);
        $characteristic_values->add_child($characteristic_value);

        // Define sources
        $mbfi->set_source_table('mbfi', array('id' => backup::VAR_ACTIVITYID));

        $characteristic_value->set_source_sql('
            SELECT  *
            FROM    {mbfi_characteristic_values}
            WHERE   mbfiid = ?',
            array(backup::VAR_PARENTID));

        // Define id annotations

        // Define file annotations
        $mbfi->annotate_files('mod_mbfi', 'intro', null); // This file area hasn't itemid

        // Return the root element (mbfi), wrapped into standard activity structure
        return $this->prepare_activity_structure($mbfi);
    }
}
