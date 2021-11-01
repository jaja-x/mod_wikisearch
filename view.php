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
require_once $CFG->libdir . '/formslib.php';
require_once './lib.php';

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

$url = new moodle_url('/mod/wikisearch/view.php', array('id' => $cm->id));
$PAGE->set_url($url);
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

// outputs the header of moodle
echo $OUTPUT->header();

class wikisearch_form extends moodleform {

    // Add elements to form
    public function definition() {
        global $CFG;

        $mform = $this->_form; // Don't forget the underscore!

        $mform->addElement('text', 'searchfield', get_string('searchfield', 'mod_wikisearch'), 'placeholder="' . get_string('searchfieldplaceholder', 'mod_wikisearch') . '" size="50"');
        $mform->setType('searchfield', PARAM_TEXT);
        $mform->setDefault('searchfield', optional_param('searchfield', '', PARAM_TEXT));

        // Add search button
        $mform->addElement('submit', 'search', get_string('search', 'mod_wikisearch'));
    }

    // Custom validation should be added here
    function validation($data, $files) {
        return [];
    }
}

// Instantiate wikisearch_form
$mform = new wikisearch_form($url);

// displays the form
$mform->display();

if ($fromform = $mform->get_data()) {
    $search_results = wikisearch_search_results($fromform->searchfield);

    echo '<h3>' . get_string('searchresults', 'mod_wikisearch') . count($search_results) . '</h3>';

    foreach ($search_results as $wiki_page) {
        $wiki_page_url = $CFG->wwwroot . '/mod/wiki/view.php?pageid=' . $wiki_page->id;
        $wiki_page_content = wikisearch_highlight_keywords($wiki_page->cachedcontent, $fromform->searchfield);;
        ?>

            <table class="generaltable" width="100%">
                <thead>
                    <tr>
                        <th class="header ctitle lastcol" style="text-align:left;" scope="col">
                        <?php echo $wiki_page->title; ?> (<a href="<?php echo $wiki_page_url; ?>"><?php
                            echo get_string('viewwikipage', 'mod_wikisearch');
                        ?></a>)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="lastrow">
                        <td class="wikisearchresults cell c0 lastcol">
                            <?php echo $wiki_page_content; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

        <?php
    }
}

// outputs the footer of moodle
echo $OUTPUT->footer();
