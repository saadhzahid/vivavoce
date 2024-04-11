<?php

use PHPUnit\Framework\TestCase;

require_once 'path/to/your/script.php'; // Include the script you want to test

class SubmissionPageTest extends TestCase
{
    public function testPageContent()
    {
        // Mock the global $PAGE object
        $pageMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['requires', 'set_context', 'set_url', 'set_pagelayout', 'set_title', 'set_heading', 'header', 'footer'])
            ->getMock();

        $pageMock->expects($this->any())
            ->method('requires')
            ->willReturnSelf();

        // Replace the global $PAGE object with the mock
        global $PAGE;
        $PAGE = $pageMock;

        // Mock the global $DB object
        $dbMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get_record', 'update_record'])
            ->getMock();

        // Assuming you have a record in the vivavocesubmissions table
        $fakeSubmission = new stdClass();
        $fakeSubmission->id = 1;
        $fakeSubmission->submission = '{"Video 1": "video1.mp4", "Video 2": "video2.mp4"}';

        $dbMock->expects($this->once())
            ->method('get_record')
            ->willReturn($fakeSubmission);

        $dbMock->expects($this->once())
            ->method('update_record')
            ->willReturn(true);

        // Replace the global $DB object with the mock
        global $DB;
        $DB = $dbMock;

        // Capture the output of the script
        ob_start();
        $_GET['submission_id'] = 1;
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertStringContainsString('<title>Student Submission</title>', $output);
        $this->assertStringContainsString('<h1>Student Submission</h1>', $output);
        $this->assertStringContainsString('<video class="video-player" controls>', $output);
        $this->assertStringContainsString('<form method="post" action="" class="mform" id="mform1">', $output);
    }

}

?>
