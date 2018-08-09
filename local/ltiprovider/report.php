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
 * List the tool provided in a course
 *
 * @package    local
 * @subpackage ltiprovider
 * @copyright  2011 Juan Leyva <juanleyvadelgado@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/local/ltiprovider/lib.php');

$courseid = required_param('courseid', PARAM_INT);
if (! ($course = $DB->get_record('course', array('id'=>$courseid)))) {
    print_error('invalidcourseid', 'error');
}

$PAGE->set_url('/local/ltiprovider/report.php', array('courseid' => $courseid));

context_helper::preload_course($course->id);
if (!$context = context_course::instance($course->id)) {
    print_error('nocontext');
}

require_login($course);
require_capability('local/ltiprovider:view', $context);

// $PAGE->navbar->add(get_string('toolsprovided', 'local_ltiprovider'));
echo $OUTPUT->header();

echo $OUTPUT->heading('LTI Consumers Report');

$tools = $DB->get_records('local_ltiprovider', array('courseid' => $course->id));

$table = new html_table();
$table->head  = array(
    get_string('name', 'local_ltiprovider'),
    'Consumer LoginId',
    'Consumer Name',
    'Consumer Site',
    'Last Access');
$table->size  = array('20%', '25%', '20%', '20%', '15%');
$table->align = array('left', 'left', 'left', 'left', 'center');
$table->width = '99%';
$flag = 0;
foreach ($tools as $tool) {
    if (!$toolcontext = context::instance_by_id($tool->contextid, IGNORE_MISSING)) {
        local_ltiprovider_delete_tool($tool);
        continue;
    }
    $toolname = $toolcontext->get_context_name();
    $consumers = $DB->get_records('local_ltiprovider_user',array('toolid'=>$tool->id));
    if(!$consumers){
        $table->data [] = array('','','No Records Found','','');
    }else{
        foreach ($consumers as $ck => $consumer) {
            $username = $DB->get_record('user',array('id'=>$consumer->userid),'firstname,lastname,username');
            if(json_decode($consumer->sourceid)->data->userid != 2){
                $table->data []= array($toolname,json_decode($consumer->sourceid)->data->username,$username->firstname.' '.$username->lastname,'http://'.parse_url($consumer->serviceurl)['host'],userdate($consumer->lastaccess));
            } 
        }
    }
}
echo html_writer::table($table);
echo $OUTPUT->single_button(new moodle_url('/course/view.php.php', array('courseid' => $course->id)), 'Back to course');
echo $OUTPUT->footer();
