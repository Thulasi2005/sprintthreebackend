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

// Get the home_name from request
if (!isset($_GET['home_name'])) {
    echo json_encode(['error' => 'Home name parameter is missing']);
    exit();
}
$home_name = $conn->real_escape_string($_GET['home_name']);

// Fetch booked slots for the given home_name
$sql = "SELECT date, slot, donor_username FROM bookingss WHERE home_name = '$home_name' AND status = 'booked'";
$result = $conn->query($sql);

$slots = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $slots[] = $row;
    }
}

// Output the result as JSON
echo json_encode(['slots' => $slots]);

$conn->close();
?>
