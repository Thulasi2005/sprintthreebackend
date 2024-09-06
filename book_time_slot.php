<?php
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $elderly_home_id = $data['elderly_home_id'];
    $date = $data['date'];
    $time_slot = $data['time_slot'];

    if ($elderly_home_id && $date && $time_slot) {
        // Update the time slot availability and status
        $update_sql = "UPDATE time_slots SET is_available = 0, status = 'confirmed' WHERE elderly_home_id = ? AND date = ? AND time_slot = ?";
        $update_stmt = $conn->prepare($update_sql);
        
        if ($update_stmt === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to prepare update statement: ' . $conn->error]);
            exit;
        }

        $update_stmt->bind_param("iss", $elderly_home_id, $date, $time_slot);
        if ($update_stmt->execute() && $update_stmt->affected_rows > 0) {
            // Insert into the bookings table
            $insert_sql = "INSERT INTO bookings (elderly_home_id, date, time_slot, status) VALUES (?, ?, ?, 'Pending')";
            $insert_stmt = $conn->prepare($insert_sql);
            
            if ($insert_stmt === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to prepare insert statement: ' . $conn->error]);
                exit;
            }

            $insert_stmt->bind_param("iss", $elderly_home_id, $date, $time_slot);
            if ($insert_stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Booking successful and recorded']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Booking successful but failed to record in bookings table: ' . $insert_stmt->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'No time slot was updated. It may have already been booked.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$conn->close();
?>
