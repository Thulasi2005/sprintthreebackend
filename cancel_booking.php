<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root"; // replace with your database username
$password = ""; // replace with your database password
$dbname = "donation_db"; // replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['home_name']) || !isset($data['slot']) || !isset($data['date'])) {
    echo json_encode(['success' => false, 'error' => 'Required parameters are missing']);
    exit();
}

$home_name = $conn->real_escape_string($data['home_name']);
$slot = $conn->real_escape_string($data['slot']);
$date = $conn->real_escape_string($data['date']);

$sql = "UPDATE bookingss SET status = 'available' WHERE home_name = '$home_name' AND slot = '$slot' AND date = '$date'";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$conn->close();
?>
