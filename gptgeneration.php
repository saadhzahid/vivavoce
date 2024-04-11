<?php
require_once(__DIR__ . '/../../config.php');

//only allows json responses
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

// Decode the JSON from the request body
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true); // Convert JSON to PHP array
$userContext = $input['context'] ?? 'Default context if not provided'; // Use provided context or a default
$userPrompt = $input['prompt'] ?? 'Default context if not provided'; // Use provided context or a default


//apikey, change to env variable
$apiKey = $CFG->openai_api_key;
$prompt = "{$userPrompt}. {$userContext}";

//decides model
$postData = json_encode([
    'model' => 'gpt-3.5-turbo-0613',
    'messages' => [['role' => 'system', 'content' => $prompt]]
]);

//makes request using curl
$curl = curl_init('https://api.openai.com/v1/chat/completions');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postData,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        "Authorization: Bearer {$apiKey}"
    ],
]);

$response = curl_exec($curl);
if ($response === false) {
    // If cURL encounters an error
    echo json_encode(['error' => curl_error($curl)]);
    curl_close($curl);
    exit;
}

$httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

//error checking which gives response that I can call from my JS
if ($httpcode == 200) {
    $data = json_decode($response, true);
    $generatedResponse = trim($data['choices'][0]['message']['content']);
    echo json_encode(['response' => $generatedResponse]);
} else {
    // If the OpenAI API response is not 200 OK
    echo json_encode([
        'error' => "Failed to retrieve data from OpenAI.",
        'httpStatus' => $httpcode,
        'apiResponse' => json_decode($response, true)
    ]);
}
