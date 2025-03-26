<?php
header('Content-Type: application/json'); // Ensure JSON response
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 0); // Hide errors from output
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt'); // Save errors in a log file

$response = [
    'success' => false,
    'message' => 'An error occurred while processing the file.',
    'data' => null,
];

try {
    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No file uploaded or an error occurred during upload.');
    }

    $file = $_FILES['file'];
    $filePath = $file['tmp_name'];
    $fileName = $file['name'];

    if (!is_readable($filePath)) {
        throw new Exception('The uploaded file is not readable.');
    }

    // Read and process the CSV file
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileExtension !== 'csv') {
        throw new Exception('Unsupported file type. Only CSV files are allowed.');
    }

    $fileContent = file_get_contents($filePath);
    if ($fileContent === false) {
        throw new Exception('Failed to read the file content.');
    }

    $rows = array_map('str_getcsv', explode("\n", trim($fileContent)));
    $header = array_shift($rows);
    $jsonData = [];

    foreach ($rows as $row) {
        if (!empty(array_filter($row))) {
            $jsonData[] = array_combine($header, $row);
        }
    }

    $response['success'] = true;
    $response['message'] = 'CSV file processed successfully.';
    $response['data'] = $jsonData;
    $response['columns'] = $header; // Add columns here

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

// Send JSON response
echo json_encode($response);
exit;
