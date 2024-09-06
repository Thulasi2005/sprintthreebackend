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

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit;
    }

    if (!empty($data['collaboration_room_id']) && !empty($data['selected_items'])) {
        $collaboration_room_id = htmlspecialchars(strip_tags($data['collaboration_room_id']));
        $selected_items = $data['selected_items'];

        // Process selected items
        foreach ($selected_items as $item) {
            $stmt = $conn->prepare("
                INSERT INTO donations (collaboration_room_id, item)
                VALUES (?, ?)
            ");
            $stmt->bind_param("ss", $collaboration_room_id, $item);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Collaboration Room ID and selected items are required.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
