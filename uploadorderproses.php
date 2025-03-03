<?php
require 'vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\IOFactory;

$response = ['status' => 'error', 'message' => 'Invalid request'];
header('Content-Type: application/json');

// $validApiKey = 'AAS';

// function getApiKeyFromHeaders() {
//     $headers = getallheaders();
//     if (isset($headers['x-api-key'])) {
//         return $headers['x-api-key'];
//     } elseif (isset($headers['X-API-KEY'])) {
//         return $headers['X-API-KEY'];
//     }
//     return null;
// }

// $apiKey = getApiKeyFromHeaders();

// if ($apiKey !== $validApiKey) {
//     http_response_code(401);
//     echo json_encode(['message' => 'Unauthorized: Invalid API key']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $store = $_POST['store'] ?? '';
    $warehouseck = $_POST['warehouseck'] ?? '';
    $token = $_POST['token'] ?? '';
    $orderDate = $_POST['orderDate'] ?? '';
    $dueDate = $_POST['dueDate'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    $fileUpload = $_FILES['fileUpload'] ?? null;

    // if (empty($store) || empty($orderDate) || empty($dueDate) || empty($remarks) || !$fileUpload) {
    //     http_response_code(400); 
    //     $response['message'] = 'All fields are required.';
    //     echo json_encode($response);
    //     exit;
    // }

    $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    if (!in_array($fileUpload['type'], $allowedTypes)) {
        http_response_code(400); 
        $response['message'] = 'Invalid file type. Only .xlsx files are allowed.';
        echo json_encode($response);
        exit;
    }

    $uploadsDir = 'dokumen/';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true);
    }
    $filePath = $uploadsDir . basename($fileUpload['name']);
    if (!move_uploaded_file($fileUpload['tmp_name'], $filePath)) {
        http_response_code(500); 
        $response['message'] = 'Failed to upload file.';
        echo json_encode($response);
        exit;
    }

    try {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $excelData = $sheet->toArray(null, true, true, true);
        
        http_response_code(200);
        $response['status'] = 'success';
        $response['message'] = 'File uploaded successfully';
        $response['params'] = [
            'token' => $token,
            'store' => $store,
            'warehouseck' => $warehouseck,
            'orderDate' => $orderDate,
            'dueDate' => $dueDate,
            'remarks' => $remarks
        ];
        $response['excelData'] = $excelData;
    } catch (Exception $e) {
        http_response_code(500); 
        $response['message'] = 'Error reading Excel file: ' . $e->getMessage();
    }
} else {
    http_response_code(401); 
    $response['message'] = 'Unauthorized access.';
}

echo json_encode($response);
?>
