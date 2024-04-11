<?php

require __DIR__ . '/../../vendor/autoload.php';
require_once(__DIR__ . '/../../config.php');

use Aws\S3\S3Client;
use Aws\TranscribeService\TranscribeServiceClient;
use Aws\Exception\AwsException;

// AWS credentials and S3 bucket name
$bucketName = $CFG->bucketName;
$accessKeyId = $CFG->accessKeyId;
$secretAccessKey = $CFG->secretAccessKey;
$region = $CFG->region; 


// Instantiate the S3 client
$s3 = new S3Client([
    'version' => 'latest',
    'region' => $region,
    'credentials' => [
        'key' => $accessKeyId,
        'secret' => $secretAccessKey,
    ]
]);




// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['video'])) {
    // Retrieve the uploaded file details
    $uploadedFile = $_FILES['video'];
    $filePath = $uploadedFile['tmp_name']; // Temporary path of the uploaded file
    $fileName = $uploadedFile['name']; // Original file name

    // Check if the filePath is not empty
    if (!empty($filePath)) {
        try {
            // Upload file to S3 bucket
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => $fileName, // Use the original file name as the object key
                'Body' => fopen($filePath, 'rb'), // Open file for reading
            ]);

            // Print the URL of the uploaded file
            $fileUrl = $result['ObjectURL'];

            echo "File uploaded successfully: " . $fileUrl;

            
            




        } catch (AwsException $e) {
            // Print error message if the upload fails
            echo "Error uploading file: " . $e->getMessage();
        }
    } else {
        echo "The uploaded file path is empty. Please check your file upload settings.";
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) { // Add condition for other file types
    // Retrieve the uploaded file details
    $uploadedFile = $_FILES['file'];
    $filePath = $uploadedFile['tmp_name']; // Temporary path of the uploaded file
    $fileName = $uploadedFile['name']; // Original file name

    // Check if the filePath is not empty
    if (!empty($filePath)) {
        try {
            // Upload file to S3 bucket
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => $fileName, // Use the original file name as the object key
                'Body' => fopen($filePath, 'rb'), // Open file for reading
            ]);

            // Print the URL of the uploaded file
            echo "File uploaded successfully. URL: " . $result['ObjectURL'];






        } catch (AwsException $e) {
            // Print error message if the upload fails
            echo "Error uploading file: " . $e->getMessage();
        }
    } else {
        echo "The uploaded file path is empty. Please check your file upload settings.";
    }
} else {
    // If the request method is not POST or no file is uploaded
    echo "Invalid request";
}

?>
