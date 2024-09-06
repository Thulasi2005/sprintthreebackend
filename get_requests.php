<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM donation_requests";
$result = $conn->query($sql);

$requests = [];
while ($row = $result->fetch_assoc()) {
    $request_id = $row['request_id'];

    $item_sql = "SELECT * FROM donation_items WHERE request_id = '$request_id'";
    $item_result = $conn->query($item_sql);

    $items = [];
    while ($item_row = $item_result->fetch_assoc()) {
        $items[] = [
            'item' => $item_row['item'],
            'quantity' => (int) $item_row['quantity'], // Ensure quantity is treated as an integer
            'description' => $item_row['description']
        ];
    }

    $row['items'] = $items;
    $requests[] = $row;
}

echo json_encode($requests);

$conn->close();
?>
