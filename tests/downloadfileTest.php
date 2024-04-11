<?php

use PHPUnit\Framework\TestCase;

require_once 'path/to/your/script.php'; // Include the script you want to test

class FileDownloadTest extends TestCase
{
    public function testValidSubmissionId()
    {
        // Mock the global $DB object
        $dbMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['record_exists', 'get_record'])
            ->getMock();

        $dbMock->expects($this->once())
            ->method('record_exists')
            ->willReturn(true);

        // Assuming you have a record in the vivavocesubmissions table
        $fakeSubmission = new stdClass();
        $fakeSubmission->dissofile = 'Fake file data';
        $fakeSubmission->filename = 'test_file.txt';

        $dbMock->expects($this->once())
            ->method('get_record')
            ->willReturn($fakeSubmission);

        // Replace the global $DB object with the mock
        global $DB;
        $DB = $dbMock;

        // Simulate a valid submission ID
        $submission_id = 1;

        // Capture the output of the script
        ob_start();
        $_GET['submission_id'] = $submission_id;
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertEquals('application/octet-stream', $this->getResponseHeader('Content-Type'));
        $this->assertEquals('attachment; filename="test_file.txt"', $this->getResponseHeader('Content-Disposition'));
        $this->assertEquals('13', $this->getResponseHeader('Content-Length'));
        $this->assertEquals('Fake file data', $output);
    }

    public function testInvalidSubmissionId()
    {
        // Mock the global $DB object
        $dbMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['record_exists'])
            ->getMock();

        $dbMock->expects($this->once())
            ->method('record_exists')
            ->willReturn(false);

        // Replace the global $DB object with the mock
        global $DB;
        $DB = $dbMock;

        // Simulate an invalid submission ID
        $submission_id = -1;

        // Capture the output of the script
        ob_start();
        $_GET['submission_id'] = $submission_id;
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertEquals('Invalid submission ID', $output);
    }

    // Helper function to retrieve response headers
    private function getResponseHeader($header)
    {
        $headers = xdebug_get_headers();
        foreach ($headers as $h) {
            if (strpos($h, $header) !== false) {
                return $h;
            }
        }
        return null;
    }
}

?>
