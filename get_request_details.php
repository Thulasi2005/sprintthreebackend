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

        // Fetch request details
        $stmt = $conn->prepare("SELECT * FROM donation_requests WHERE request_id = ?");
        $stmt->bind_param("s", $request_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $request = $result->fetch_assoc();

        if ($request) {
            // Fetch associated items
            $stmt = $conn->prepare("SELECT * FROM donation_items WHERE request_id = ?");
            $stmt->bind_param("s", $request_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $items = $result->fetch_all(MYSQLI_ASSOC);

            echo json_encode(['request' => $request, 'items' => $items]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Request not found']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request ID']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
