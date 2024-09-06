<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$donorName = $_POST['username'];
$date = $_POST['date'];
$slot = $_POST['slot'];
$homeName = $_POST['home_name'];

// Debugging output
error_log("Received data - DonorName: $donorName, Date: $date, Slot: $slot, HomeName: $homeName");

// Check if slot is already booked
$sql = "SELECT * FROM bookingss WHERE date = ? AND home_name = ? AND slot = ? AND status = 'booked'";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("sss", $date, $homeName, $slot);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['message' => 'Slot already booked.']);
} else {
    // Book the slot
    $sql = "INSERT INTO bookingss (home_name, date, slot, donor_username, status) VALUES (?, ?, ?, ?, 'booked')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssss", $homeName, $date, $slot, $donorName);

    if (!$stmt->execute()) {
        // Check for duplicate entry error specifically
        if ($stmt->errno === 1062) {
            echo json_encode(['message' => 'Slot already booked.']);
        } else {
            echo json_encode(['message' => 'Failed to book slot. Error: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['message' => 'Booking successful!']);
    }
}

$stmt->close();
$conn->close();
?>
