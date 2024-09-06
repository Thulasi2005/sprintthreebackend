<?php
header('Content-Type: application/json');

// Database credentials
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array('success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error)));
}

// Read JSON input
$input = file_get_contents('php://input');

// Log raw input for debugging
file_put_contents('php://stderr', "Raw Input: " . $input . "\n");

// Decode JSON
$inputData = json_decode($input, true);

// Log decoded input for debugging
file_put_contents('php://stderr', "Decoded Input: " . print_r($inputData, true) . "\n");

// Check if JSON is valid
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(array('success' => false, 'message' => 'Invalid JSON input: ' . json_last_error_msg()));
    exit();
}

// Validate input
$id = isset($inputData['id']) ? intval($inputData['id']) : 0;
$status = isset($inputData['status']) ? $conn->real_escape_string($inputData['status']) : '';

if (empty($id) || empty($status)) {
    echo json_encode(array('success' => false, 'message' => 'Invalid parameters'));
    exit();
}

// Prepare SQL statement
$sql = "UPDATE donor_requests SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(array('success' => false, 'message' => 'SQL prepare error: ' . $conn->error));
    exit();
}

$stmt->bind_param('si', $status, $id);

if ($stmt->execute()) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false, 'message' => 'Failed to update request: ' . $stmt->error));
}

$stmt->close();
$conn->close();
?>
