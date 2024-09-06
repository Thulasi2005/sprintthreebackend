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

    if (
        !empty($data['name']) &&
        !empty($data['address']) &&
        !empty($data['contact']) &&
        !empty($data['items']) &&
        is_array($data['items'])
    ) {
        $conn->begin_transaction(); // Start transaction

        try {
            $name = htmlspecialchars(strip_tags($data['name']));
            $address = htmlspecialchars(strip_tags($data['address']));
            $contact = htmlspecialchars(strip_tags($data['contact']));

            // Simulate a unique request ID
            $request_id = 'R' . time();

            // Insert request into donation_requests table
            $stmt = $conn->prepare("INSERT INTO donation_requests (request_id, name, address, contact) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $request_id, $name, $address, $contact);

            if (!$stmt->execute()) {
                throw new Exception('Failed to insert request: ' . $stmt->error);
            }

            // Prepare statement for inserting items
            $stmt = $conn->prepare("INSERT INTO donation_items (request_id, item, quantity, description) VALUES (?, ?, ?, ?)");

            foreach ($data['items'] as $item) {
                $item_name = htmlspecialchars(strip_tags($item['item']));
                $quantity = intval($item['quantity']);
                $description = htmlspecialchars(strip_tags($item['description']));

                $stmt->bind_param("ssis", $request_id, $item_name, $quantity, $description);

                if (!$stmt->execute()) {
                    throw new Exception('Failed to insert item: ' . $stmt->error . ' Item: ' . json_encode($item));
                }
            }

            $conn->commit(); // Commit transaction

            $response = [
                'status' => 'success',
                'request_id' => $request_id,
                'name' => $name,
                'address' => $address,
                'contact' => $contact,
                'items' => $data['items']
            ];

            echo json_encode($response);
        } catch (Exception $e) {
            $conn->rollback(); // Roll back transaction on error
            error_log($e->getMessage()); // Log the error to the server log
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>
