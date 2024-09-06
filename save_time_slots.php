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

$input = json_decode(file_get_contents('php://input'), true);

$user = $conn->real_escape_string($input['username']);
$date = $conn->real_escape_string($input['date']);
$slots = $conn->real_escape_string(json_encode($input['slots']));

// Prepare and execute the query
$sql = "
    INSERT INTO user_time_slots (username, date, slots)
    VALUES ('$user', '$date', '$slots')
    ON DUPLICATE KEY UPDATE slots = '$slots'
";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['error' => 'Failed to save time slots']);
}

$conn->close();
?>
