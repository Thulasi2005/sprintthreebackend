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
    die(json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$request_method = $_SERVER['REQUEST_METHOD'];

if ($request_method == 'POST') {
    $rawData = file_get_contents("php://input");
    $data = json_decode($rawData, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit;
    }

    if (!empty($data['request_id'])) {
        $request_id = $conn->real_escape_string($data['request_id']);
        
        // Generate unique collaboration room ID
        $collaboration_room_id = 'CR' . time() . rand(1000, 9999);

        $stmt = $conn->prepare("INSERT INTO collaboration_rooms (request_id, collaboration_room_id) VALUES (?, ?)");
        $stmt->bind_param("ss", $request_id, $collaboration_room_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode([
                'status' => 'success',
                'collaboration_room_id' => $collaboration_room_id
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to create collaboration room.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Request ID is required.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
