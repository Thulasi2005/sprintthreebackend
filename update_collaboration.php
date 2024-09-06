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

    if (!empty($data['request_id']) && !empty($data['selected_items']) && is_array($data['selected_items'])) {
        $request_id = htmlspecialchars(strip_tags($data['request_id']));
        $selected_items = $data['selected_items'];

        // Update items in donation_items table (assuming a separate table for items)
        foreach ($selected_items as $item_id) {
            $stmt = $conn->prepare("UPDATE donation_items SET status = 'Selected' WHERE item_id = ? AND request_id = ?");
            $stmt->bind_param("is", $item_id, $request_id);
            $stmt->execute();
        }

        echo json_encode(['status' => 'success', 'message' => 'Selection updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
