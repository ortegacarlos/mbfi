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
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_mgroup_activity_task
 */

/**
 * Define the complete mgroup structure for backup, with file and id annotations
 */
class backup_mgroup_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        // Define each element separated
        $mgroup = new backup_nested_element('mgroup', array('id'), array(
            'course', 'name', 'groupsize', 'intro', 'introformat',
            'timecreated', 'timemodified'));

        $individuals = new backup_nested_element('individuals');

        $individual = new backup_nested_element('individual', array('id'), array(
            'mgroupid', 'workgroup', 'userid', 'username',
            'fullname', 'timecreated', 'timemodified'));

        // Build the tree
        $mgroup->add_child($individuals);
        $individuals->add_child($individual);

        // Define sources
        $mgroup->set_source_table('mgroup', array('id' => backup::VAR_ACTIVITYID));

        $individual->set_source_sql('
            SELECT  *
            FROM    {mgroup_individuals}
            WHERE   mgroupid = ?',
            array(backup::VAR_PARENTID));

        // Define file annotations
        $mgroup->annotate_files('mod_mgroup', 'intro', null); // This file area hasn't itemid

        // Return the root element (mgroup), wrapped into standard activity structure
        return $this->prepare_activity_structure($mgroup);
    }
}
