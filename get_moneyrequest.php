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
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Fetch requests from the database
$sql = "SELECT id, elderly_home_name, amount_required, contact_address, contact_number, purpose, description, created_at FROM money_donation_requests";
$result = $conn->query($sql);

// Check for SQL errors
if ($conn->error) {
    http_response_code(500);
    echo json_encode(["error" => "SQL error: " . $conn->error]);
    exit;
}

$requests = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
}

echo json_encode($requests);

$conn->close();
?>
