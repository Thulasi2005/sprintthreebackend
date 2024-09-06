<?php
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM service_requests";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Convert NULL to null for JSON compatibility
        $row['status'] = $row['status'] === NULL ? null : $row['status'];
        $data[] = $row;
    }
} else {
    $data = array("message" => "No requests available");
}

$conn->close();

echo json_encode($data);
?>
