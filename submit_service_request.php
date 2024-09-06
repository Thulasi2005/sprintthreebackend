<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

$homeName = $_POST['homeName'] ?? '';
$contactNumber = $_POST['contactNumber'] ?? '';
$address = $_POST['address'] ?? '';
$numElders = $_POST['numElders'] ?? '';
$service = $_POST['service'] ?? '';
$description = $_POST['description'] ?? '';
$timePeriod = $_POST['timePeriod'] ?? '';

if(empty($homeName) || empty($contactNumber) || empty($address) || empty($numElders) || empty($service) || empty($description) || empty($timePeriod)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

$sql = "INSERT INTO service_requests (homeName, contactNumber, address, numElders, service, description, timePeriod) 
        VALUES ('$homeName', '$contactNumber', '$address', '$numElders', '$service', '$description', '$timePeriod')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $sql . '<br>' . $conn->error]);
}

$conn->close();
?>
