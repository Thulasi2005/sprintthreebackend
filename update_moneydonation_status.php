<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $donationId = $_POST['donation_id'] ?? '';
    $status = $_POST['status'] ?? '';

    if (in_array($status, ['approved', 'unapproved'])) {
        $stmt = $conn->prepare("UPDATE donations SET approval_status = ? WHERE id = ?");
        $stmt->bind_param('si', $status, $donationId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Donation status updated successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update donation status: ' . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>
