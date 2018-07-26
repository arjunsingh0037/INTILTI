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

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

class local_lti_csv_enrol_form extends moodleform {
    function definition() {
        $mform = $this->_form;
                
        $mform->setType('id', PARAM_INT);

        $mform->addElement('filepicker', 'userfile', 'Upload CSV file', null, array('accepted_types' => '*.csv'));
        $this->add_action_buttons(true, get_string('savechanges'));

    }
}
