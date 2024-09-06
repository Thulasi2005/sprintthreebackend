<?php
header('Content-Type: application/json');

// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(array("status" => "error", "message" => "Connection failed: " . $conn->connect_error)));
}

// Read POST data
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id']) && isset($data['approval_status'])) {
    $id = $data['id'];
    $approval_status = $data['approval_status'];

    // Validate the approval_status value
    $allowed_statuses = ['approved', 'unapproved', 'pending'];
    if (!in_array($approval_status, $allowed_statuses)) {
        echo json_encode(array("status" => "error", "message" => "Invalid approval status."));
        $conn->close();
        exit();
    }

    // Prepare SQL statement to update status
    $stmt = $conn->prepare("UPDATE payment_requests SET approval_status = ? WHERE id = ?");
    $stmt->bind_param("si", $approval_status, $id);

    if ($stmt->execute()) {
        echo json_encode(array("status" => "success", "message" => "Status updated successfully."));
    } else {
        echo json_encode(array("status" => "error", "message" => "Failed to update status."));
    }

    $stmt->close();
} else {
    echo json_encode(array("status" => "error", "message" => "Invalid input data."));
}

$conn->close();
?>
