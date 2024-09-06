<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Adjust as necessary for CORS

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$donorUsername = isset($_GET['username']) ? $conn->real_escape_string($_GET['username']) : '';

// Query to get bookings for the specific donor
$sql = "SELECT slot AS time_slot, 
               CASE 
                 WHEN status = 'booked' THEN 'Confirmed'
                 WHEN status = '' THEN 'Cancelled'
                 ELSE 'Unknown'
               END AS status
        FROM bookingss 
        WHERE donor_username = '$donorUsername'";

$result = $conn->query($sql);

if (!$result) {
    // Print the error if the query fails
    echo json_encode(['error' => $conn->error]);
    $conn->close();
    exit();
}

$bookings = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

echo json_encode($bookings);

$conn->close();
?>
