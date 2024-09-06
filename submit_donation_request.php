<?php
// submit_donation_request.php

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$username = $input['username'] ?? '';
$category = $input['category'] ?? '';
$quantities = $input['quantities'] ?? [];
$descriptions = $input['descriptions'] ?? [];
$isEmergency = $input['isEmergency'] ?? false;
$homeName = $input['homeName'] ?? '';
$homeAddress = $input['homeAddress'] ?? '';
$homeContactNumber = $input['homeContactNumber'] ?? '';

// Validate input
if (empty($username) || empty($category) || empty($homeName) || empty($homeAddress) || empty($homeContactNumber)) {
    echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
    exit();
}

// Database connection
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Insert request into database
$sql = "INSERT INTO item_requests (username, category, quantities, descriptions, is_emergency, home_name, home_address, home_contact_number) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
    exit();
}

$quantities_json = json_encode($quantities);
$descriptions_json = json_encode($descriptions);
$isEmergency = $isEmergency ? 1 : 0;

$stmt->bind_param('ssssssss', $username, $category, $quantities_json, $descriptions_json, $isEmergency, $homeName, $homeAddress, $homeContactNumber);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Request submitted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to submit request: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
