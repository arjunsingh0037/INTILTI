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
 * @copyright  2018 Arjun Singh <arjunsingh@transneuron.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/local/ltiprovider/lib.php');
$PAGE->set_url('/local/ltiprovider/report.php');
$context = context_system::instance();
if (!$context) {
    print_error('nocontext');
}else{
    $PAGE->set_context($context);
}
require_login();
require_capability('local/ltiprovider:view', $context);
$title = 'Consumers Report';
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
echo $OUTPUT->header();
echo $OUTPUT->heading('LTI Consumers Report');
$tools = $DB->get_records('local_ltiprovider');
$table = new html_table();
$table->head  = array(
    'Sl.no',
    get_string('name', 'local_ltiprovider'),
    'Course Name',
    'Consumer Name',
    'Consumer Site',
    'Last Access');
$table->size  = array('5%','20%', '20%', '20%', '22%', '13%');
$table->align = array('left','left', 'left', 'left', 'left', 'left');
$table->width = '99%';
$flag = 0;
$i =1;
//print_object($tools);
foreach ($tools as $tool) {
    if (!$toolcontext = context::instance_by_id($tool->contextid, IGNORE_MISSING)) {
        local_ltiprovider_delete_tool($tool);
        continue;
    }
    $toolname = $toolcontext->get_context_name();
    $consumers = $DB->get_records('local_ltiprovider_user',array('toolid'=>$tool->id));
    if($consumers){
        foreach ($consumers as $ck => $consumer) {
            $username = $DB->get_record('user',array('id'=>$consumer->userid),'firstname,lastname,username');
            $coursename = $DB->get_record('course',array('id'=>$tool->courseid),'id,fullname');
            $courslink = html_writer::link(new moodle_url('/course/view.php', array('id'=>$coursename->id)),$coursename->fullname);
            $context = $DB->get_record('context',array('id'=>$tool->contextid),'contextlevel,instanceid');
            // print_object($context);
            if(json_decode($consumer->sourceid)->data->userid != 2){
                $flag ++;
                if($context->contextlevel == 50){
                    //course
                    $ltitoolurl = html_writer::link(new moodle_url('/course/view.php', array('id'=>$coursename->id)),$toolname,array('target' => '_blank'));
                }elseif ($context->contextlevel == 70) {
                    //module
                    //$ltitoolurl = html_writer::link(new moodle_url('/mod/resource/view.php', array('id'=>$context->instanceid)),$toolname);
                    $cm = $DB->get_record('course_modules',array('id'=>$context->instanceid),'module,instance');
                    $mod = $DB->get_record('modules',array('id'=>$cm->module),'name');
                    $ltitoolurl = html_writer::link(new moodle_url('/mod/'.$mod->name.'/view.php',array('id'=>$context->instanceid)),$toolname,array('target' => '_blank'));
                }
                $site_url = 'http://'.parse_url($consumer->serviceurl)['host'];
                $consumer_site_url = html_writer::link($site_url,$site_url,array('target' => '_blank'));
                $table->data []= array($i,$ltitoolurl,$courslink,$username->firstname.' '.$username->lastname,$consumer_site_url,userdate($consumer->lastaccess));
            } 
            $i++;
        }
    }  
}
if($flag == 0){
    $table->data [] = array('','','No Records Found','','');
}
echo html_writer::table($table);
echo $OUTPUT->footer();
