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

echo "<div id='cssmenu'>
<ul>
   <li><a href='#'><span> &#x1F3E0; Course Objectives</span></a></li>
   <li class='active has-sub'><a href='#'><span>&#x26AA;  Unit 1</span></a>
      <ul>
	<li class=''><a onclick='window.location.href='www.google.com'><span>&#x26AC; Objectives</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Contents</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Summary</span></a></li>
      </ul>
   </li>
   <li class='active has-sub'><a href='#'><span>&#x26AA;  Unit 2</span></a>
      <ul>
         <li class=''><a href='#'><span>&#x26AC; Objectives</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Contents</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Summary</span></a></li>
      </ul>
   </li>
   <li class='active has-sub'><a href='#'><span>&#x26AA;  Unit 3</span></a>
      <ul>
         <li class=''><a href='#'><span>&#x26AC; Objectives</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Contents</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Summary</span></a></li>
      </ul>
   </li>
   <li class='active has-sub'><a href='#'><span>&#x26AA;  Unit 4</span></a>
      <ul>
         <li class=''><a href='#'><span>&#x26AC; Objectives</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Contents</span></a></li>
         <li class=''><a href='#'><span>&#x26AC; Summary</span></a></li>
      </ul>
   </li>
   <li class='last'><a href='#'><span>&#x1F4BB;  Labs</span></a></li>
</ul>
</div>";

  /*  require_once($CFG->libdir.'/blocklib.php');
    $instance = new stdClass;
    $instance->id = 47;
    $object = block_instance('popularcourses',$instance);
    echo $object->get_content()->text;
*/
