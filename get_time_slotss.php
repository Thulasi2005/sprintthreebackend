<?php
header('Content-Type: application/json');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}

$reqUsername = $conn->real_escape_string($_GET['username']);
$reqDate = $conn->real_escape_string($_GET['date']);

// Fetch time slots for the given username and date
$sql = "SELECT slots FROM user_time_slots WHERE username = '$reqUsername' AND date = '$reqDate'";
$result = $conn->query($sql);

$response = ['slots' => array_fill(0, 7, false)]; // Default to all false if no data

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['slots'] = json_decode($row['slots']);
}

echo json_encode($response);

$conn->close();
?>
