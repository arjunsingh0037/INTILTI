<?php

//  BRIGHTALLY CUSTOM CODE
//  Coder: Ted vd Brink
//  Contact: ted.vandenbrink@brightalley.nl
//  Date: 6 juni 2012
//
//  Description: Enrols users into a course by allowing a user to upload an csv file with only email adresses
//  Using this block allows you to use CSV files with only emailaddress
//  After running the upload you can download a txt file that contains a log of the enrolled and failed users.

//  License: GNU General Public License http://www.gnu.org/copyleft/gpl.html

require('../../config.php');
require_once("$CFG->dirroot/local/ltiprovider/upload_form.php");
require_once("$CFG->dirroot/repository/lib.php");

GLOBAL $USER,$DB,$CFG;
require_login();
$context = context_system::instance();
if (!has_capability('local/ltiprovider:manage',$context,$USER->id)) {
    die("Unauthorized.");
}

$title = 'LTI user upload';
$PAGE->set_url('/local/ltiprovider/upload_users.php');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($title);
$PAGE->set_pagelayout('standard');

$data = new stdClass();
$options = array('subdirs' => 1, 'maxbytes' => $CFG->userquota, 'maxfiles' => - 1, 'accepted_types' => '*.csv', 'return_types' => FILE_INTERNAL);

file_prepare_standard_filemanager($data, 'files', $options, $context, 'user', 'csvenrol', 0);


$mform = new local_lti_csv_enrol_form(null, array('options' => $options));
$flag = 0;
$count = 0;
$existing_userdata = array();
$notinserted= array();
$formdata = $mform->get_data();
if ($mform->is_cancelled()) {
   redirect(new moodle_url($CFG->wwwroot.'/local/ltiprovider/upload_users.php'));  
} else if ($formdata && $mform->get_file_content('userfile')) {
    
    //upload file, store, and process csv
    $content = $mform->get_file_content('userfile'); //save uploaded file
    //read file and insert into table 'lti_enrol'
    $flag = 0;
    $result = array_filter(array_map("trim", explode("\n", $content)));
    foreach ($result as $key => $value)
    {
        $eachline[$key] = explode(",", $value);
        unset($eachline[0]);
        if($eachline){
            //print_object($eachline);  
            
            foreach ($eachline as $lk => $lval) {
                $fname = $lval[0];
                $lname = $lval[1];
                //$email = trim($lval[2]);
		$email = trim(preg_replace('/\s+/',' ', $lval[2]));
                $semester = $lval[3];
                //$subject = trim($lval[4]);
		$subject = trim(preg_replace('/\s+/',' ', $lval[4]));
                //print_object($lval);
                $insert = new stdClass();
                $insert->firstname = $fname;
                $insert->lastname = $lname;
                $insert->email =$email ;
                $insert->semester = $semester;
                $insert->subject = $subject;
                //print_object($insert);
                if($DB->record_exists('lti_enrol',array('email'=>$email,'subject'=>$subject,'semester'=>$semester))){
                    $existing_userdata [] = $DB->get_record('lti_enrol',array('email'=>$email,'subject'=>$subject,'semester'=>$semester)); 
                }else{
                    $inserted = $DB->insert_record('lti_enrol',$insert);
                    if(!$inserted){
                        $flag = -1;
                        $notinserted [] = $lval;
                    }else{
                        $flag = 1;
                        $count++;
                    } 
                }
                
            }
            unset($eachline);
        }   
    }   
}
echo $OUTPUT->header();
echo $OUTPUT->box_start('generalbox');
$mform->display();
if($flag == 1){
    echo '<div class="alert alert-success">
    <strong>'.$count.'</strong> Users data successfully uploaded.
  </div>';
}
if($existing_userdata){
    $existing_no = count($existing_userdata);
    echo '<div class="alert alert-danger">
    <strong>'.$existing_no.'</strong> Users data already exists.
  </div>';
    //print_object($existing_userdata);
    foreach ($existing_userdata as $key => $value) {
        $newarray[$value->email][] = $value; 
    }
    //print_object($newarray);
}
if($flag == '-1'){
   echo '<div class="alert alert-danger">
    <strong>Error uploadind data for some users.</strong>
    </div>'; 
}
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
