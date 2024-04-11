<?php
require_once(__DIR__ . '/../../config.php'); // Include Moodle configuration
require_once($CFG->libdir . '/formslib.php'); // Include Moodle forms library
require_login(); // Require login


if (!isset($_SESSION)) {
    session_start();
}


// Get the course ID from the URL parameter
$courseid = required_param('courseid', PARAM_INT);
$PAGE->requires->js('/mod/vivavoce/js/vivavocescript.js');
$PAGE->requires->css('/mod/vivavoce/css/vivavocewindow.css');



global $DB, $USER;


$vivavoce_data = $DB->get_record('vivavoce', ['id' => $courseid]);



if ($vivavoce_data) {
    // Data retrieval successful
    $questions = $vivavoce_data->displayquestions;
    $questionsArray = array_map(function($question) {
        return ltrim($question, "- ");
    }, preg_split("/\r\n|\n|\r/", $questions));



} else {
    $errorInfo = $DB->get_last_error();
    $errorMessage = $errorInfo[2]; // Get the error message
    $errorCode = $errorInfo[0]; // Get the error code
    throw new Exception("Failed to retrieve data from the database. Error message: $errorMessage (Error code: $errorCode)");
}

$num_questions = count($questionsArray);

echo $num_questions;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;

if ($page < 1 || $page > $num_questions) {
    header("Location: ?courseid=$courseid&page=1");
    exit;
}




if (isset($_POST['finishsubmission'])) {
    // Set session variable for success message

    $submittedVideos = $_POST['submittedVideos'];
    $submittedVideosArray = json_decode($submittedVideos, true);


    $formattedPairs = [];

    // Iterate over each key-value pair and format it
    foreach ($submittedVideosArray as $key => $value) {
        // Enclose the key and value in double quotes and concatenate them with ":"
        $formattedPair = '"' . $key . '": "' . $value . '"';
        // Add the formatted pair to the array
        $formattedPairs[] = $formattedPair;
    }
    
    // Combine all formatted pairs into a single string separated by commas
    $resultString = implode(', ', $formattedPairs);




    $existingSubmission = $DB->get_record('vivavocesubmissions', array('assignment_instance_id' => $courseid, 'name' => $USER->username));
    $existingSubmission->isgraded = 0;
    $existingSubmission->submission = $resultString; // Convert array to JSON string
    $existingSubmission->viewblock = 1; //blocks user from resubmitting
    $DB->update_record('vivavocesubmissions', $existingSubmission);



    $_SESSION['submission_success'] = true;
    // Redirect to Moodle front page
    header("Location: {$CFG->wwwroot}");
    exit;
}


echo $OUTPUT->header();
echo $OUTPUT->heading($title);

// Display the current question as an h1 tag
$currentQuestion = $questionsArray[$page - 1]; // Adjust for array index starting at 0







echo '<div class="centered-container">';

//add sidebar



echo '<h3>Question '. htmlspecialchars($page) . '</h3>';
echo '<hr class="solid">'; // Divider line

echo '<h2>'. htmlspecialchars($currentQuestion) . '</h2>';
echo '<div id="video-container"><video muted id="video" autoplay></video></div>';
echo '<div class="buttons-container">'; // Container for buttons
echo '<button id="startrecording" class="record-btn" title="Click to start recording"><i class="fas fa-circle"></i> Start Recording</button>';

echo '<button id="stoprecording" class="stop-btn" style="display: none;" title="Click to stop recording"><i class="fas fa-square"></i> Stop Recording</button>';



echo '<button id="retakevideo" class="retake-btn"><i class="fas fa-redo"></i> Retake Video</button>';
echo '</div>'; // Close the buttons container
echo '<input type="hidden" name="courseid" value="' . $courseid . '">';
echo '<input type="hidden" name="question_number" value="' . $page . '">';
echo '<input type="hidden" name="student_name" value="' . fullname($USER) . '">';
echo '<input type="hidden" id="currentQuestion" value="'. htmlspecialchars($currentQuestion) .'">';





echo '<div class="pagination-container">';
if ($page > 1) {
    echo '<a href="?courseid=' . $courseid . '&page=' . ($page - 1) . '" class="pagination-link"><i class="fas fa-chevron-left"></i> Previous</a>';
}
if ($page < $num_questions) {
    echo '<a href="?courseid=' . $courseid . '&page=' . ($page + 1) . '" class="pagination-link">Next <i class="fas fa-chevron-right"></i></a>';
} 

// Display "Finish Submission" button only on the last page
if ($page == $num_questions) {
    // Form to handle the submission
    echo '<form action="" method="POST">'; // Replace with the appropriate action and method

    echo '<button type="submit" id="finishsubmission" name="finishsubmission" class="pagination-link" onclick="submitForm()" >Finish Submission</button>'; 
    
    echo '<input type="hidden" id="submittedVideosInput" name="submittedVideos">'; // Hidden input field to capture submittedVideos

    
    // Change class to pagination-link

    echo '<script>
    function submitForm() {


        // Accessing submittedVideos from local storage
        var submittedVideos = localStorage.getItem(\'submittedVideos\');
        
        // Populate the hidden input field with the submittedVideos data
        document.getElementById(\'submittedVideosInput\').value = submittedVideos;
        
        // Clearing local storage after submission
        localStorage.removeItem(\'submittedVideos\');
        
        // Submit the form
        document.getElementById(\'submissionForm\').submit();
    }
    </script>';

    echo '</form>';



}




echo '</div>'; // Close the pagination container
//div to hold the download link
echo '<div id="linkContainer"></div>';
echo '</div>'; // Close the centered-container
echo $OUTPUT->footer();
?>