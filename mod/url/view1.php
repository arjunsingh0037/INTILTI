<link rel="stylesheet" href="style.css">
<script src="http://code.jquery.com/jquery-latest.min.js" type="text/javascript"></script>
<script src="script.js"></script>
<style type="text/css">
    
</style>
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
 * URL module main user interface
 *
 * @package    mod_url
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once("$CFG->dirroot/mod/url/lib.php");
require_once("$CFG->dirroot/mod/url/locallib.php");
require_once($CFG->libdir . '/completionlib.php');
global $CFG,$PAGE,$USER,$DB;
$id       = optional_param('id', 0, PARAM_INT);        // Course module ID
$u        = optional_param('u', 0, PARAM_INT);         // URL instance id
$redirect = optional_param('redirect', 0, PARAM_BOOL);
$forceview = optional_param('forceview', 0, PARAM_BOOL);

if ($u) {  // Two ways to specify the module
    $url = $DB->get_record('url', array('id'=>$u), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('url', $url->id, $url->course, false, MUST_EXIST);

} else {
    $cm = get_coursemodule_from_id('url', $id, 0, false, MUST_EXIST);
    $url = $DB->get_record('url', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/url:view', $context);
$arr2 = array();
echo "<div id='cssmenu'>
<ul>";
$cm = $DB->get_record('course_modules',array('id'=>$id),'section,course');
//$cs = $DB->get_records('course_sections',array('id'=>$cm->section),'name,sequence,course,section');
$cs = $DB->get_records('course_format_options',array('courseid'=>$cm->course,'sectionid'=>$cm->section,'name'=>'parent'));
foreach($cs as $c){
$sectionhead = $DB->get_record('course_sections',array('course'=>$cm->course,'section'=>$c->value),'name');
$secsplit = explode('-',$sectionhead->name,2);
echo "<li class='active'><a href='#'class='subjectname'><span>".$secsplit[1]."</span></a></li>";
$cs1 = $DB->get_records('course_format_options',array('name'=>'parent','value'=>$c->value));
$arr2 = $cs1;
}
//print_object($arr2);
foreach($arr2 as $arr22){
$csections []= $DB->get_record('course_sections',array('id'=>$arr22->sectionid),'name,sequence');
}
$labarray =array();
$a33 = array();
$sec222 = array();
$count = count($csections);
//print_object($csections);
foreach($csections as $csec1){
	if(stripos($csec1->name,'Course')!==false){
		$csections1[] = $csec1; 
	}
}
$i = 1;
$flag = 0;
foreach($csections as $csec1){
	if(stripos($csec1->name,'Unit')!==false){
		$flag++;
		$unit = explode('Unit',$csec1->name);
		//print_object($unit[1]);
		$sec222[$unit[1]] = $csec1;
	}
	sort($sec222);
}
$a33 = array_merge($csections1,$sec222);
foreach($csections as $csec1){
        if(stripos($csec1->name,'Labs')!==false){
                $labarray[] = $csec1;
        }
}
$finalsection = array_merge($a33,$labarray);
//print_object($finalsection);
if($finalsection[0]->sequence == ''){
}else{
	if($id == $finalsection[0]->sequence){
	echo "<li class='exp'>";
	}else{
	echo "<li>";
	}
?>
<a onclick="top.window.location='/lms3/mod/url/view.php?id=<?php echo $finalsection[0]->sequence;?>'">
<span> &#x1F3E0; <?php echo $finalsection[0]->name;?></span>
</a>
<?php echo "</li>";
}
foreach($finalsection as $secs){
	$seqnum = explode(',',$secs->sequence);
				/*echo "<li>";
                                ?>
                                <a onclick="top.window.location='/lms3/mod/url/view.php?id=<?php echo $csections[0]->sequence;?>'">
                                        <span> &#x26AA; <?php echo $csections[0]->name;?></span>
                                </a>
                                <?php echo "</li>";*/

   		//echo "<li><a href=''><span> &#x1F3E0; ".$secs->name."</span></a></li>";
	if(count($seqnum) < 2){
		//if()
   		//echo "<li><a href=''><span> &#x1F3E0; ".$secs->name."</span></a></li>";
	}else{
		echo "<li class='active has-sub'><a href='#'><span>&#x26AA;  ".$secs->name."</span></a>
      			<ul>";
			foreach($seqnum as $k=>$seqno){
				if($k == 0){
					$subsecname = 'Objectives';
				}
				if($k == 1){
					$subsecname = 'Contents';
				}
				if($k == 2){
					$subsecname = 'Summary';
				}
				if($id == $seqno){
					echo "<li class='exp'>";
				}else{
					echo "<li class=''>";
				}
				?>
				<a onclick="top.window.location='/lms3/mod/url/view.php?id=<?php echo $seqno;?>'">
					<span> &#x26AC; <?php echo $subsecname;?></span>
				</a>
				<?php echo "</li>";	
			}
      			echo "</ul>
  	 	      </li>"; 
	}
				/*echo "<li>";
                                ?>
                                <a onclick="top.window.location='/lms3/mod/url/view.php?id=<?php echo $csections[$count-1]->sequence;?>'">
                                        <span> &#x26AC; <?php echo $csections[count-1]->name;?></span>
                                </a>
                                <?php echo "</li>";*/

}
				if($finalsection[$count-1]->sequence == ''){
				}else{
					if($id == $finalsection[$count-1]->sequence){
					        echo "<li class='exp'>";
       					}else{
        					echo "<li>";
        				}
                                	?>
                                	<a onclick="top.window.location='/lms3/mod/url/view.php?id=<?php echo $finalsection[$count-1]->sequence;?>'">
                                        	<span> &#x26AA; <?php echo $finalsection[$count-1]->name;?></span>
                                	</a>
                                	<?php echo "</li>";
				}

echo "</ul>
</div>";?>
<style>
li.exp{
font-weight: bolder;
border-left: 2px solid red!important;	
}
a.subjectname {
    text-align: left;
    color: #454e53!important;
    text-transform: uppercase;
}
</style>
<script>
$('li.exp').closest("ul").parent("li").addClass("open");
$('li.exp').closest("ul").css("display","block");
</script>


