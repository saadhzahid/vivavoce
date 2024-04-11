<?php

use PHPUnit\Framework\TestCase;

require_once 'path/to/your/script.php'; // Include the script you want to test

class ViewAllSubmissionsTest extends TestCase
{
    public function testPageContent()
    {
        // Mock the global $PAGE and $DB objects
        $pageMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['set_context', 'set_pagelayout', 'set_title', 'set_heading', 'header', 'footer'])
            ->getMock();

        $pageMock->expects($this->any())
            ->method('set_context')
            ->willReturnSelf();

        $pageMock->expects($this->any())
            ->method('set_pagelayout')
            ->willReturnSelf();

        $pageMock->expects($this->any())
            ->method('set_title')
            ->willReturnSelf();

        $pageMock->expects($this->any())
            ->method('set_heading')
            ->willReturnSelf();

        $pageMock->expects($this->once())
            ->method('header')
            ->willReturn('');

        $pageMock->expects($this->once())
            ->method('footer')
            ->willReturn('');

        // Replace the global $PAGE object with the mock
        global $PAGE;
        $PAGE = $pageMock;

        // Mock the global $DB object
        $dbMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['get_records'])
            ->getMock();

        $fakeSubmission = new stdClass();
        $fakeSubmission->id = 1;
        $fakeSubmission->name = 'Test Student';
        $fakeSubmission->assignment_instance_id = 1;
        $fakeSubmission->dissofile = 'path/to/file.pdf';
        $fakeSubmission->isgraded = 1;

        $dbMock->expects($this->once())
            ->method('get_records')
            ->willReturn([$fakeSubmission]);

        global $DB;
        $DB = $dbMock;

        ob_start();
        $_GET['courseid'] = 1;
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertStringContainsString('<title>View All Submissions</title>', $output);
        $this->assertStringContainsString('<h5 class="card-title">Student Name: Test Student</h5>', $output);
        $this->assertStringContainsString('<p class="card-text">Assignment ID: 1</p>', $output);
        $this->assertStringContainsString('<a href="path/to/file.pdf" style="color: white; target="_blank">Download Student Dissertation</a>', $output);
        $this->assertStringContainsString('<p>No submissions found.</p>', $output);
    }

}

?>
