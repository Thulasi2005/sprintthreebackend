<?php
header('Content-Type: application/json');

// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$requestId = $_POST['request_id']; // Get request ID from POST data
$status = $_POST['status']; // Get status from POST data

// Validate status
if (!in_array($status, ['Accepted', 'Declined'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
    exit;
}

// Update request status in the database
$sql = "UPDATE money_donation_requests SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $requestId);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success', 'message' => 'Request status updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update request status']);
}

$stmt->close();
$conn->close();
?>
