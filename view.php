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
 * Prints an instance of mod_wikisearch.
 *
 * @package     mod_wikisearch
 * @copyright   2020 LEARNING.BOG.GE <contact@learning.bog.ge>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// require files
require_once '../../config.php';
require_once './locallib.php';

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id.
$w  = optional_param('w', 0, PARAM_INT);

if ($id) {
    $cm             = get_coursemodule_from_id('wikisearch', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('wikisearch', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($w) {
    $moduleinstance = $DB->get_record('wikisearch', array('id' => $n), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('wikisearch', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_wikisearch'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

// $event = \mod_wikisearch\event\course_module_viewed::create(array(
//     'objectid' => $moduleinstance->id,
//     'context' => $modulecontext
// ));
// $event->add_record_snapshot('course', $course);
// $event->add_record_snapshot('wikisearch', $moduleinstance);
// $event->trigger();

$PAGE->set_url('/mod/wikisearch/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

echo $OUTPUT->header();

// content
echo 'Search results will be here ...';

echo $OUTPUT->footer();
