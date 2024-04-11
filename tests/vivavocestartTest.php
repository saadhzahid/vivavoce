<?php

use PHPUnit\Framework\TestCase;

require_once 'path/to/your/script.php'; // Include the script you want to test

class UploadFileFormTest extends TestCase
{
    public function testFormDisplay()
    {
        // Mock the global $PAGE, $USER, $CFG, $DB, and $OUTPUT objects
        $pageMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['set_url', 'set_title', 'set_heading', 'header', 'footer'])
            ->getMock();

        $pageMock->expects($this->once())
            ->method('set_url')
            ->willReturnSelf();

        $pageMock->expects($this->once())
            ->method('set_title')
            ->willReturnSelf();

        $pageMock->expects($this->once())
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

        // Mock the $CFG object
        $cfgMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['libdir'])
            ->getMock();

        $cfgMock->expects($this->any())
            ->method('libdir')
            ->willReturn('path/to/your/libdir');

        // Replace the global $CFG object with the mock
        global $CFG;
        $CFG = $cfgMock;

        // Mock the $USER object
        $userMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['id', 'username'])
            ->getMock();

        $userMock->id = 1;
        $userMock->username = 'testuser';

        // Replace the global $USER object with the mock
        global $USER;
        $USER = $userMock;

        // Mock the $DB object
        $dbMock = $this->getMockBuilder(stdClass::class)
            ->setMethods(['insert_record'])
            ->getMock();

        $dbMock->expects($this->once())
            ->method('insert_record')
            ->willReturn(true);

        // Replace the global $DB object with the mock
        global $DB;
        $DB = $dbMock;

        // Mock the $OUTPUT object
        $outputMock = $this->getMockBuilder(stdClass::class)
            ->getMock();

        $outputMock->expects($this->once())
            ->method('header')
            ->willReturn('');

        $outputMock->expects($this->once())
            ->method('footer')
            ->willReturn('');

        // Replace the global $OUTPUT object with the mock
        global $OUTPUT;
        $OUTPUT = $outputMock;

        // Capture the output of the script
        ob_start();
        $_GET['courseid'] = 1;
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertStringContainsString('<title>Upload Files</title>', $output);
        $this->assertStringContainsString('<h1 class="main">Upload your Report</h1>', $output);
        $this->assertStringContainsString('<form', $output);
        $this->assertStringContainsString('enctype="multipart/form-data"', $output);
    }

}

?>
