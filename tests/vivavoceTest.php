<?php

use PHPUnit\Framework\TestCase;

require_once 'path/to/your/script.php'; // Include the script you want to test

class SubmissionPageTest extends TestCase
{
    public function testPageContent()
    {
        // Mock the global $PAGE, $DB, and $USER objects
        $pageMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['requires', 'set_context', 'set_url', 'set_pagelayout', 'set_title', 'set_heading', 'header', 'footer', 'requires', 'js', 'css', 'heading', 'header', 'footer'])
            ->getMock();

        $pageMock->expects($this->any())
            ->method('requires')
            ->willReturnSelf();

        $pageMock->expects($this->any())
            ->method('js')
            ->willReturnSelf();

        $pageMock->expects($this->any())
            ->method('css')
            ->willReturnSelf();

        $pageMock->expects($this->any())
            ->method('heading')
            ->willReturn('');

        $pageMock->expects($this->any())
            ->method('header')
            ->willReturn('');

        $pageMock->expects($this->any())
            ->method('footer')
            ->willReturn('');

        // Replace the global $PAGE object with the mock
        global $PAGE;
        $PAGE = $pageMock;

        // Mock the global $DB object
        $dbMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get_record'])
            ->getMock();

        // Assuming you have a record in the vivavocesubmissions table
        $fakeSubmission = new stdClass();
        $fakeSubmission->id = 1;
        $fakeSubmission->submission = '{"Video 1": "video1.mp4", "Video 2": "video2.mp4"}';

        $dbMock->expects($this->once())
            ->method('get_record')
            ->willReturn($fakeSubmission);

        // Replace the global $DB object with the mock
        global $DB;
        $DB = $dbMock;

        // Mock the global $USER object
        $userMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['username'])
            ->getMock();

        $userMock->expects($this->any())
            ->method('username')
            ->willReturn('testuser');

        // Replace the global $USER object with the mock
        global $USER;
        $USER = $userMock;

        // Capture the output of the script
        ob_start();
        $_GET['courseid'] = 1;
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertStringContainsString('<title>Submission Page</title>', $output);
        $this->assertStringContainsString('<h3>Question 1</h3>', $output);
        $this->assertStringContainsString('<video muted id="video" autoplay></video>', $output);
        $this->assertStringContainsString('<button id="startrecording" class="record-btn"', $output);
        $this->assertStringContainsString('<button id="stoprecording" class="stop-btn" style="display: none;"', $output);
        $this->assertStringContainsString('<button id="retakevideo" class="retake-btn"><i class="fas fa-redo"></i> Retake Video</button>', $output);
        $this->assertStringContainsString('<button type="submit" id="finishsubmission" name="finishsubmission" class="pagination-link"', $output);
    }

}

?>
