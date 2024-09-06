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

    if (!empty($data['collaboration_room_id']) && !empty($data['selected_items'])) {
        $collaboration_room_id = $conn->real_escape_string($data['collaboration_room_id']);
        $selected_items = $data['selected_items'];

        foreach ($selected_items as $item_id) {
            // Example query to update or insert selected items in the database
            $stmt = $conn->prepare("UPDATE collaboration_items SET selected = 1 WHERE collaboration_room_id = ? AND id = ?");
            $stmt->bind_param("ss", $collaboration_room_id, $item_id);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success', 'message' => 'Selection confirmed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
