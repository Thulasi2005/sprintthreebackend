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

// Fetch elderly homes
$sql = "SELECT id, name, address FROM elderly_homes";
$result = $conn->query($sql);

$elderlyHomes = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $elderlyHomes[] = $row;
    }
}

echo json_encode($elderlyHomes);

$conn->close();
?>
