<?php
require_once('../../config.php');
require_once("$CFG->libdir/formslib.php");
require_once('awssubmit.php'); // Include your AWS S3 upload script

require_login();

$courseid = required_param('courseid', PARAM_INT);

class upload_file_form extends moodleform {
    private $courseid;

    public function __construct($url, $customdata = null) {
        // Pass customdata through to the parent constructor
        parent::__construct($url, $customdata);
        $this->courseid = $customdata['courseid'];
    }

    public function definition() {
        $mform = $this->_form;

        $mform->addElement('filepicker', 'userfile', "Upload here", null, array(
            'subdirs' => 0, 
            'maxbytes' => 5000000, 
            'areamaxbytes' => 10485760, 
            'maxfiles' => 1,
            'accepted_types' => '*'
        ));

        $this->add_action_buttons(true, get_string('savechanges'));
    }
}

// Ensure courseid is passed correctly into the form constructor
$mform = new upload_file_form(new moodle_url('/mod/vivavoce/vivavocestart.php', ['courseid' => $courseid]), ['courseid' => $courseid]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/index.php'));
} else if ($data = $mform->get_data()) {

    $context = context_user::instance($USER->id);
    $draftitemid = file_get_submitted_draft_itemid('userfile');
    $fs = get_file_storage();
    
    // Assuming there's only one file
    $files = $fs->get_area_files($context->id, 'user', 'draft', $draftitemid, 'id DESC', false);
    if ($file = reset($files)) { // Get the first (and supposedly only) file
        $filecontent = $file->get_content();



        try {
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => $file->get_filename(), 
                'Body' => $filecontent, // Binary content of the file
            ]);
            // Print the URL of the uploaded file
            echo "File uploaded successfully. URL: " . $result['ObjectURL'];
        } catch (AwsException $e) {
            // Print error message if the upload fails
            echo "Error uploading file: " . $e->getMessage();
        }





        $record = new stdClass();
        $record->assignment_instance_id = $courseid;
        $record->name = $USER->username;
        $record->dissofile = $result['ObjectURL']; // aws link stored

        $DB->insert_record('vivavocesubmissions', $record);
    }




    redirect(new moodle_url('/mod/vivavoce/vivavoce.php', ['courseid' => $courseid]));
} else {
    // Ensure the initial set URL also correctly includes the courseid
    $PAGE->set_url('/mod/vivavoce/vivavocestart.php', ['courseid' => $courseid]);
    $PAGE->set_title('Upload Files');
    $PAGE->set_heading('Upload your Report');
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}
