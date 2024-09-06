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

$requestId = $_GET['request_id']; // Get request ID from query string

// Fetch request details from the database
$sql = "SELECT * FROM money_donation_requests WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $requestId);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();

echo json_encode($request);

$stmt->close();
$conn->close();
?>
