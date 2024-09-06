<?php
require 'config.php';

header("Access-Control-Allow-Origin: *");

$sql = "SELECT id, elderly_home_id, date, time_slot, status FROM bookings";
$result = $conn->query($sql);

$bookings = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

echo json_encode(['bookings' => $bookings]);

$conn->close();
?>
