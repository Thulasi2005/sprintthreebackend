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

// Retrieve donations from the database
$result = $conn->query("SELECT id, donor_name, contact_number, address, receipt_image, approval_status FROM donations");

if ($result) {
    $donations = [];
    while ($row = $result->fetch_assoc()) {
        $donations[] = $row;
    }
    echo json_encode(['status' => 'success', 'data' => $donations]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch donations']);
}

// Close the database connection
$conn->close();
?>
