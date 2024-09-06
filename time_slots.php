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

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

$elderlyHomeId = $data['elderly_home_id'];
$date = $data['date'];
$timeSlots = $data['time_slots'];

// Insert or update time slots
foreach ($timeSlots as $slot) {
    $timeSlot = $slot['time_slot'];
    $isAvailable = $slot['is_available'] ? 1 : 0;

    $sql = "INSERT INTO time_slots (elderly_home_id, date, time_slot, is_available)
            VALUES ('$elderlyHomeId', '$date', '$timeSlot', '$isAvailable')
            ON DUPLICATE KEY UPDATE is_available='$isAvailable'";

    if (!$conn->query($sql)) {
        echo json_encode(['error' => 'Failed to update time slots']);
        $conn->close();
        exit();
    }
}

echo json_encode(['message' => 'Time slots updated successfully']);

$conn->close();
?>
