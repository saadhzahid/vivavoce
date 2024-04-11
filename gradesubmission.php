<?php
require_once(__DIR__ . '/../../config.php'); // Include Moodle configuration
require_once($CFG->libdir . '/adminlib.php'); // Include the admin library
require_once($CFG->libdir . '/formslib.php'); // Include Moodle forms library

require __DIR__ . '/../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\TranscribeService\TranscribeServiceClient;
use Aws\Exception\AwsException;


// Ensure user is logged in
require_login();

// Get submission ID from URL parameter
$submission_id = required_param('submission_id', PARAM_INT);
$PAGE->requires->css('/mod/vivavoce/css/submissionpage.css');

// Set up page parameters
$PAGE->set_context(context_system::instance());
$PAGE->set_url($_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']); // Set the URL based on current URL
$PAGE->set_pagelayout('standard');
$PAGE->set_title('Student Submission'); // Set the title of the page
$PAGE->set_heading('Student Submission'); // Set the heading of the page

// Output header
echo $OUTPUT->header();

// Get submission record
$submission = $DB->get_record('vivavocesubmissions', array('id' => $submission_id));
$submissionlinks = $submission->submission;
$submissionlinks = trim($submissionlinks, '"');

// Split the string into key-value pairs
$pairs = explode('", "', $submissionlinks);

$resultArray = array();
foreach ($pairs as $pair) {
    // Split each pair by the separator to get key and value
    list($key, $value) = explode('": "', $pair);
    
    // Add key-value pair to the result array
    $resultArray[$key] = $value;
}



// Output videos
echo '<div class="video-container">';
foreach ($resultArray as $key => $url) {
    echo '<div class="video-item">';
    echo '<h3>' . $key . '</h3>';

    echo '<video class="video-player" controls>';
    echo '<source src="' . $url . '" type="video/mp4">';
    echo 'Your browser does not support the video tag.';
    echo '</video>';
    echo '</div>';

    // try {
    //     $transcribeResult = $transcribeClient->startTranscriptionJob([
    //         'TranscriptionJobName' => 'TranscriptionJob_' . uniqid(), // Unique name for each job
    //         'LanguageCode' => 'en-US', // Language code for transcription
    //         'Media' => [
    //             'MediaFileUri' => 's3://moodlevivabucket/Saadh Zahid_course_2_question_3.mp4' // Use the URL from $resultArray directly
    //         ],
    //         'OutputBucketName' => $bucketName // S3 bucket to store transcription results
    //     ]);

    //     // Print transcription job details
    //     echo "Transcription Job Name: " . $transcribeResult['TranscriptionJob']['TranscriptionJobName'];
    // } catch (AwsException $e) {
    //     // Handle any errors
    //     echo "Error creating transcription job for URL: $url - " . $e->getMessage();
    // }
}



class submission_form extends moodleform {
    // Define the form elements

    function __construct($submission_id) {
        $this->submission_id = $submission_id;
        parent::__construct();
    }

    function definition() {
        $mform = $this->_form;

        $mform->addElement('hidden', 'submission_id', $this->submission_id);

        $mform->addElement('text', 'grade', 'grade');
        $mform->addElement('textarea', 'feedback', 'feedback:', array('rows' => 10, 'cols' => 70));
        
        $mform->setType('feedback', PARAM_TEXT);
        $mform->setType('grade', PARAM_INT);
        $this->add_action_buttons();
    }
}
$form = new submission_form($submission_id);
if ($form->is_cancelled()) {
    // Handle form cancellation
} else if ($fromform = $form->get_data()) {

    $grade = $fromform->grade;
    $feedback = $fromform->feedback;

    $existingSubmission = $DB->get_record('vivavocesubmissions', array('id' => $submission_id));
    $existingSubmission->isgraded = 1;
    $existingSubmission->grade = $grade;
    $existingSubmission->feedback = $feedback;



    if (!$DB->update_record('vivavocesubmissions', $existingSubmission)) {
        echo 'Error: Failed to update submission record';
    } else {
        // Success message
        echo 'Submission record updated successfully';
            
        echo '<script>
        
        alert("Feedback Submitted Successfully.")
        
        </script>';

        // Redirect to Moodle's front page
        redirect(new moodle_url('/'));




    }



   
} else {
    // Show the form
    $form->display();
}










// Output footer
echo $OUTPUT->footer();
