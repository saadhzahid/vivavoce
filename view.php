<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Prints an instance of mod_vivavoce.
 *
 * @package     mod_vivavoce
 * @copyright   2024 Saadh Zahid<saadhzahidwork@.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course module id.
$id = required_param('id', PARAM_INT);

// Get the course module object associated with the given ID
$cm = get_coursemodule_from_id('vivavoce', $id, 0, false, MUST_EXIST);


// Gets the course object associated with the course module
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

//instance id, actual id of the assignment, important/
$instance_id = $cm->instance;

//require login func
require_login($course, true, $cm);



//instance of module created
$modulecontext = context_module::instance($cm->id);

//parameters for url
$urlparams = array(
    'id' => $id,
    
);

// Create a Moodle URL object for the view.php page with the defined URL parameters
$url = new moodle_url('/mod/vivavoce/view.php', $urlparams);
$PAGE->set_url($url); // Set the URL for the current page


// Output the header.
echo $OUTPUT->header();

$context = context_module::instance($cm->id);


// Check if the user is enrolled as a teacher. Display Add submission button, else if a teacher, show the view all submission and grade buttons 
if (has_capability('moodle/course:manageactivities', $context)) {
    // The user is a teacher (or has teacher-like capabilities)




    $timeCreate = "24th of March, 2024";
    $dateDue = "1st of June, 2024";

    // Open and Due Dates
    echo html_writer::start_div('mb-3'); // Margin bottom for spacing
    echo html_writer::tag('p', "Open Date: <strong>{$timeCreate}</strong>", array('class' => 'alert alert-info'));
    echo html_writer::tag('p', "Due Date: <strong>{$dateDue}</strong>", array('class' => 'alert alert-warning'));
    echo html_writer::end_div();

    // Buttons with Bootstrap classes
    $viewassignment = new moodle_url('/mod/vivavoce/viewallsubmissions.php', array('courseid' => $instance_id));
    $viewassignmentHTML = html_writer::link($viewassignment, 'View All Submissions', array('class' => 'btn btn-primary m-1'));
    echo $viewassignmentHTML;

    // $gradeUrl = new moodle_url('/mod/assign/view.php', array('id' => $cm->id, 'action' => 'grade'));
    // $gradeButtonHTML = html_writer::link($gradeUrl, 'Grade', array('class' => 'btn btn-success m-1'));
    // echo $gradeButtonHTML;

    // Grading Summary in a Card
    $totalSubmissions = $DB->count_records('vivavocesubmissions');

    // Query the database to get the count of graded submissions
    $gradedSubmissions = $DB->count_records_select('vivavocesubmissions', 'isgraded = ?', array(1));
    
    echo html_writer::start_div('card mt-3');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', 'Grading Summary', array('class' => 'card-title'));
    echo html_writer::tag('p', "Total Submissions: <span class='badge'>{$totalSubmissions}</span>", array('class' => 'card-text'));
    echo html_writer::tag('p', "Submissions Graded: <span class='badge'>{$gradedSubmissions}</span>", array('class' => 'card-text'));
    echo html_writer::end_div(); // Close card-body
    echo html_writer::end_div(); // Close card







} else {
    // The user is a student or does not have teacher-like capabilities

    $username = $USER->username;

    // Check if there is a record in mdl_vivavocesubmissions for the current user with viewblock = 1
    $submission = $DB->get_record('vivavocesubmissions', array('name' => $username, 'viewblock' => 1, 'assignment_instance_id' => $instance_id));
    
    if ($submission) {
        // If a record exists, display the grading summary
    
        echo html_writer::start_div('grading-summary mb-3'); // Start div for grading summary
    
        // Title for the grading summary
        echo html_writer::tag('h3', 'Grading Summary', array('class' => 'grading-summary-title'));
    
        // Grading summary card
        echo html_writer::start_div('card');
        echo html_writer::start_div('card-body');
    
        // Check the database for grading status
        $grading_status = ($submission->isgraded == 0) ? 'Not Graded' : 'Graded';
        $grade = ($grading_status === 'Graded') ? $submission->grade : '';
        $feedback = ($grading_status === 'Graded') ? $submission->feedback : '';
    
        // Display grading status
        echo html_writer::start_div('grading-status');
        echo html_writer::tag('p', "<strong>Grading Status:</strong> {$grading_status}");
        echo html_writer::end_div(); // Close grading-status div
    
        // If submission is graded, display grade and feedback
        if ($grading_status === 'Graded') {
            // Display grade
            echo html_writer::start_div('grade');
            echo html_writer::tag('p', "<strong>Grade:</strong> {$grade}");
            echo html_writer::end_div(); // Close grade div
    
            // Display feedback
            echo html_writer::start_div('feedback');
            echo html_writer::tag('p', "<strong>Feedback:</strong> {$feedback}");
            echo html_writer::end_div(); // Close feedback div
        }
    
        // Close card-body and card divs
        echo html_writer::end_div(); // Close card-body div
        echo html_writer::end_div(); // Close card div
    
        // Close div for grading summary
        echo html_writer::end_div(); // Close grading-summary div





    } else {
        // If no record exists, display default content

        $timeCreate = "24th of March, 2024";
        $dateDue = "1st of June, 2024";

        // Open and Due Dates
        echo html_writer::start_div('mb-3'); // Margin bottom for spacing
        echo html_writer::tag('p', "Open Date: <strong>{$timeCreate}</strong>", array('class' => 'alert alert-info'));
        echo html_writer::tag('p', "Due Date: <strong>{$dateDue}</strong>", array('class' => 'alert alert-warning'));
        echo html_writer::end_div();

        // Buttons with Bootstrap classes

        $startassignment = new moodle_url('/mod/vivavoce/vivavocestart.php', array('courseid' => $instance_id));
        $startassignmentHTML = html_writer::link($startassignment, 'Start Assignment', array('class' => 'btn btn-primary m-1'));
        echo $startassignmentHTML;
    }
}







// Output the footer.
echo $OUTPUT->footer();
?>