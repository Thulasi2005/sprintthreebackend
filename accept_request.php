<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method == 'POST') {
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if (json_last_error() === JSON_ERROR_NONE && !empty($data['request_id'])) {
        $request_id = $data['request_id'];
        
        // Update request status to 'accepted'
        $stmt = $conn->prepare("UPDATE donation_requests SET status = 'accepted' WHERE request_id = ?");
        $stmt->bind_param("s", $request_id);
        $stmt->execute();

        // Get donor details
        $stmt = $conn->prepare("SELECT * FROM donors WHERE request_id = ?");
        $stmt->bind_param("s", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $donor = $result->fetch_assoc();

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'donor_details' => $donor
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to accept request']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
