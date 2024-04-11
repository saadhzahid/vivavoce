<?php
require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_login();

$courseid = required_param('courseid', PARAM_INT);

// Set up the page context
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_title('View All Submissions');
$PAGE->set_heading('View all Student Submissions');

// Output the header
echo $OUTPUT->header();

global $DB;
$submissions = $DB->get_records('vivavocesubmissions');

// Display submissions in cards
if (!empty($submissions)) {
    foreach ($submissions as $submission) {
        // Determine card color based on isgraded
        $cardColor = ($submission->isgraded == 1) ? '#61e31c' : '#FF967F';

        // Make the card clickable, directing to a new page
        echo '<a href="gradesubmission.php?submission_id=' . $submission->id . '" class="card-link" style="text-decoration: none;">';
        echo '<div class="card" style="background-color:' . $cardColor . '; color: white; margin-bottom: 10px;">'; // Added inline CSS for card color
        echo '<div class="card-body">';
        echo '<h5 class="card-title">Student Name: ' . $submission->name . '</h5>';
        echo '<p class="card-text">Assignment ID: ' . $submission->assignment_instance_id . '</p>';

        // Link for downloading the dissertation file
        echo '<a href="' . $submission->dissofile . '" style="color: white; target="_blank">Download Student Dissertation</a></p>';

        // Close div tags for card
        echo '</div>';
        echo '</div>';
        echo '</a>';
    }
} else {
    echo '<p>No submissions found.</p>';
}

// Output the footer
echo $OUTPUT->footer();
?>
