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
 * renderers/core_renderer.php
 *
 * @package    theme_klass
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
/**
 * Klass theme core renderer class
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_klass_core_renderer extends theme_boost\output\core_renderer {
    /**
     * Header custom menu renderer.
     *
     * @param custom_menu $menu
     * @return string
     */
    public function custom_menu_render(custom_menu $menu) {
        global $CFG;
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';
        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }
        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_klass/custom_menu_item', $context);
        }
        return $content;
    }
    public function course_main_content(){
        global $DB,$PAGE,$USER,$OUTPUT;
	//print_object($USER->email.' --- '.$USER->id);
	if($DB->record_exists('lti_enrol',array('email'=>$USER->email))){
		$ltiuserdata = $DB->get_records('lti_enrol',array('email'=>$USER->email));
		foreach($ltiuserdata as $ltiid => $ltidata){
			$update = new stdClass();
			$update->id = $ltiid;
			$update->userid = $USER->id;
			$DB->update_record('lti_enrol',$update);
		}
	}
	
        $content = '';
$subseccc = array();
        $course = $DB->get_record('course',array('id'=>3));
	$allusers = get_enrolled_users(context_course::instance(3));
//	print_object($allusers);
	$arr2 = array();	
	//$course = get_course(3);
	//$modinfo = get_fast_modinfo($course);
	//print_object($modinfo->get_section_info_all());
 	$parent_sections = (array) $DB->get_records('course_sections');
	//print_object($parent_sections);	
foreach($parent_sections as $parents){
//print_object($parents->name);
$pos = stripos($parents->name, 'IBM');
                if($pos !== false){
                  $arr2[$parents->section] = $parents->name;    
                }

}
$flag = 0;
$user_lti_enrol = array();
$content .= '<div id="region-main-box" class="col-xs-12">
                <section id="region-main">
                    <div class="card card-block">
                        <span class="notifications" id="user-notifications"></span>
                        <div role="main">
                            <span id="maincontent"></span>
                            <div class="course-content">';
foreach($arr2 as $secid=>$sectionname){
	$secname = explode('-',$sectionname);
	$secname1 = $secname[0];
	foreach($allusers as $users){
		if($DB->record_exists('lti_enrol',array('email'=>$users->email))){
			$user_lti_enrol = $DB->get_records('lti_enrol',array('email'=>$users->email),'userid,email,semester,subject');
foreach($user_lti_enrol as $ltiusers){
			   if($USER->id == $ltiusers->userid){
				if($secname1 == $ltiusers->subject){	
					$subsections = $DB->get_records('course_format_options',array('courseid'=>'3'));
					//print_object($subsections);
					//print_object('user '.$ltiusers->email.' has course '.$ltiusers->subject.' ('.$secid.')' );
					foreach($subsections as $subs){
						if($subs->name == 'parent' && $subs->value == $secid){
							$subsecs[] = $subs;
						
					
					foreach($subsecs as $subbb){
						$secname = $DB->get_record('course_sections',array('id'=>$subbb->sectionid),'id,name,section,course');
						if($secname->name == 'Course Objectives'){
							$subseccc[$subbb->sectionid] = $subbb;
						}
						if($secname->name == 'Unit 1'){
							$subsecunit[$subbb->sectionid] = $subbb; 
						}
					}
		}
		}
					/*foreach($subseccc as $sid => $sval){
					 $modobj = $DB->get_record('course_modules',array('section'=>$sid),'id,module,instance');
					}*/
				}
			   }
			}
		}
	}
}
$flag = 0;
if($subseccc != null){
foreach($subseccc as $sid => $sval){
 $modobj = $DB->get_record('course_modules',array('section'=>$sid),'id,module,instance');
$content .='<div class="sublinks">';
$section = $DB->get_record('course_modules',array('id'=>$modobj->id),'section');
$format = $DB->get_record('course_format_options',array('sectionid'=>$section->section,'name'=>'parent'),'value');
$sectionaneme = $DB->get_record('course_sections',array('section'=>$format->value,'course'=>$sval->courseid),'name'); 
if(!$modobj){
	$flag = 1;	
}else{
$secsplit = explode('-',$sectionaneme->name,2);
 $content.= html_writer::link(new moodle_url('/mod/url/view.php', array('id' => $modobj->id)),
     $secsplit[1], array('class' => 'btn btn-default','style'=>'width:100%;margin:5px'));
}
$content .= '</div>';
}
if($flag == 1){
foreach($subsecunit as $suid => $suval){
$modobj1 = $DB->get_record('course_modules',array('section'=>$suid),'id,module,instance');
$content1 .='<div class="sublinks">';
$section1 = $DB->get_record('course_modules',array('id'=>$modobj1->id),'section');
$format1 = $DB->get_record('course_format_options',array('sectionid'=>$section1->section,'name'=>'parent'),'value');
$sectionaneme1 = $DB->get_record('course_sections',array('section'=>$format1->value),'name'); 
$content.= html_writer::link(new moodle_url('/mod/url/view.php', array('id' => $modobj1->id)),
     $sectionaneme1->name, array('class' => 'btn btn-default','style'=>'width:100%;margin:5px'));
$content .= '</div>';
}
}
}else{
 $content .= '<div class="alert alert-danger">
    <strong>Access Denied!</strong> You have not registered to this IBM module.
  </div>';
}
$content .= '</div>
                        </div>
                    </div>    
                </section>
            </div>';      
return $content; 

    }
}
