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
 * Prints an instance of mod_mbfi.
 *
 * @package     mod_mbfi
 * @copyright   2020 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id.
$m  = optional_param('m', 0, PARAM_INT);

if ($id) {
    $cm             = get_coursemodule_from_id('mbfi', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('mbfi', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($m) {
    $moduleinstance = $DB->get_record('mbfi', array('id' => $m), '*', MUST_EXIST); //Cambio $n por $m
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('mbfi', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mbfi'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_mbfi\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('mbfi', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/mbfi/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

echo '<div class="clearer"></div>';

$individuals = $DB->get_records('mbfi_characteristic_values', array('mbfiid' => $moduleinstance->id));

$table = new html_table();
$fullnamehd = get_string('fullnamehd', 'mbfi');
$extraversionhd = get_string('extraversionhd', 'mbfi');
$agreeablenesshd = get_string('agreeablenesshd', 'mbfi');
$conscientiousnesshd = get_string('conscientiousnesshd', 'mbfi');
$neuroticismhd = get_string('neuroticismhd', 'mbfi');
$opennesshd = get_string('opennesshd', 'mbfi');

$table->head = array($fullnamehd, $extraversionhd, $agreeablenesshd, $conscientiousnesshd, $neuroticismhd, $opennesshd);
foreach($individuals as $individual) {
    $user = $DB->get_record('user', array('id' => $individual->userid));
    if(isset($user)) {
        //$user->firstname = $individual->fullname;
        //$user->lastname = '';
        $fullname = $OUTPUT->user_picture($user, array('courseid' => $course->id, 'size' => 50, 'popup' => true, 'includefullname' => true, 'link' => true));
        //$username = $individual->username;
        //$fullname = $individual->fullname;
        $extraversion = $individual->extraversion;
        $agreeableness = $individual->agreeableness;
        $conscientiousness = $individual->conscientiousness;
        $neuroticism = $individual->neuroticism;
        $openness = $individual->openness;
        $table->data[] = array($fullname, $extraversion, $agreeableness, $conscientiousness, $neuroticism, $openness);
    }
}
echo html_writer::table($table);
echo $OUTPUT->footer();