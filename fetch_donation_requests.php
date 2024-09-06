<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM donation_requests";
$result = $conn->query($sql);

$response = array();

if ($result->num_rows > 0) {
    $requests = array();
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    $response['success'] = true;
    $response['data'] = $requests;
} else {
    $response['success'] = false;
    $response['message'] = 'No requests found';
}

echo json_encode($response);

$conn->close();
?>
