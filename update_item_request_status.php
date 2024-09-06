<?php
// update_item_request_status.php

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'] ?? '';
$status = $input['status'] ?? '';
$donorUsername = $input['donor_username'] ?? '';

if (empty($id) || empty($status) || empty($donorUsername)) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$sql = "UPDATE item_requests SET status = ?, donor_username = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssi', $status, $donorUsername, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Request status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update request status']);
}

$stmt->close();
$conn->close();
?>
