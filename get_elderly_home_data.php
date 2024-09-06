<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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

// Get username from query parameter
$user = isset($_GET['username']) ? $_GET['username'] : '';

$sql = "SELECT * FROM elderly_homes WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $user);
$stmt->execute();
$result = $stmt->get_result();

$response = array();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $response['success'] = true;
    $response['data'] = array(
        'name' => $row['name'],
        'address' => $row['address'],
        'contact_number' => $row['contact_number']
    );
} else {
    $response['success'] = false;
    $response['message'] = 'No data found';
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>
