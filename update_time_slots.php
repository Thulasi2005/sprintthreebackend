<?php
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request to update time slot status
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['id'] ?? null;
    $status = $data['is_available'] ?? null; // Changed to match frontend

    if ($id !== null && $status !== null) {
        // Ensure $status is treated as an integer
        $status = intval($status);

        $sql = "UPDATE time_slots SET is_available = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
            exit;
        }

        $stmt->bind_param("ii", $status, $id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Failed to execute statement']);
            exit;
        }

        echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle GET request to fetch time slots
    $sql = "SELECT * FROM time_slots";
    $result = $conn->query($sql);

    if ($result) {
        $time_slots = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($time_slots);
    } else {
        error_log("Query failed: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'Failed to fetch data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
