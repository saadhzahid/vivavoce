<?php
require_once('../../config.php');
require_login();

$submission_id = required_param('submission_id', PARAM_INT);

function validate_submission_id($submission_id) {
    global $DB;
    return $DB->record_exists('vivavocesubmissions', array('id' => $submission_id));
}


// Assuming you have a function to validate the submission ID
if (!validate_submission_id($submission_id)) {
    die('Invalid submission ID');
}

global $DB;
$submission = $DB->get_record('vivavocesubmissions', array('id' => $submission_id), '*', MUST_EXIST);

// Assuming 'dissofile' is the BLOB field and 'filename' stores the original file name
$filedata = $submission->dissofile;
$filename = $submission->filename; // Make sure you have a column for the filename

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($filedata));
echo $filedata;
exit;
?>
