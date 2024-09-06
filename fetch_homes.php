<?php
session_start();
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, name, address, contact_number, number_of_elders, district, email FROM elderly_homes";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $homes = [];
        while ($row = $result->fetch_assoc()) {
            $homes[] = $row;
        }
        echo json_encode(['success' => true, 'homes' => $homes]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No homes found']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
