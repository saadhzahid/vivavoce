<?php

use PHPUnit\Framework\TestCase;

require_once 'path/to/your/script.php'; // Include the script you want to test

class OpenAIChatTest extends TestCase
{
    public function testChatCompletionSuccess()
    {
        // Mock cURL request
        $curlMock = $this->getMockBuilder('stdClass')
            ->setMethods(['exec', 'getinfo', 'close'])
            ->getMock();

        // Configure the mock to return a successful response
        $curlMock->expects($this->once())
            ->method('exec')
            ->willReturn(json_encode([
                'choices' => [
                    ['message' => ['content' => 'Generated response']]
                ]
            ]));

        $curlMock->expects($this->once())
            ->method('getinfo')
            ->willReturn(200);

        // Replace the curl_init() call with our mock object
        global $curl;
        $curl = $curlMock;

        // Simulate a POST request
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $inputJSON = json_encode(['context' => 'Test context', 'prompt' => 'Test prompt']);
        $input = json_decode($inputJSON, true);
        $expectedResponse = 'Generated response';

        // Capture the output of the script
        ob_start();
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertJson($output);
        $responseData = json_decode($output, true);
        $this->assertArrayHasKey('response', $responseData);
        $this->assertEquals($expectedResponse, $responseData['response']);
    }

    public function testChatCompletionFailure()
    {
        // Mock cURL request
        $curlMock = $this->getMockBuilder('stdClass')
            ->setMethods(['exec', 'getinfo', 'close'])
            ->getMock();

        // Configure the mock to return a failure response
        $curlMock->expects($this->once())
            ->method('exec')
            ->willReturn(false);

        // Replace the curl_init() call with our mock object
        global $curl;
        $curl = $curlMock;

        // Simulate a POST request
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $inputJSON = json_encode(['context' => 'Test context', 'prompt' => 'Test prompt']);

        // Capture the output of the script
        ob_start();
        include 'path/to/your/script.php';
        $output = ob_get_clean();

        // Assertions
        $this->assertJson($output);
        $responseData = json_decode($output, true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('Failed to retrieve data from OpenAI.', $responseData['error']);
    }
}

?>
