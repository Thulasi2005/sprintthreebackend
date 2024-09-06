<?php
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $elderly_home_id = $_GET['elderly_home_id'];
    $date = $_GET['date'];

    if ($elderly_home_id && $date) {
        $sql = "SELECT time_slot, is_available FROM time_slots WHERE elderly_home_id = ? AND date = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            error_log("Prepare failed: " . $conn->error);
            echo json_encode(['success' => false, 'message' => 'Failed to prepare statement']);
            exit;
        }

        $stmt->bind_param("is", $elderly_home_id, $date);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            echo json_encode(['success' => false, 'message' => 'Failed to execute statement']);
            exit;
        }

        $result = $stmt->get_result();
        $time_slots = [];
        while ($row = $result->fetch_assoc()) {
            $time_slots[] = $row;
        }

        echo json_encode(['success' => true, 'time_slots' => $time_slots]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
