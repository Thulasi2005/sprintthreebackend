<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from POST request
$data = json_decode(file_get_contents('php://input'), true);
$request_id = $data['request_id'] ?? '';
$status = $data['status'] ?? '';

$sql = "UPDATE donation_requests SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $status, $request_id);

if ($stmt->execute()) {
    $response['success'] = true;
    $response['message'] = 'Status updated successfully';
} else {
    $response['success'] = false;
    $response['message'] = 'Failed to update status';
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>
