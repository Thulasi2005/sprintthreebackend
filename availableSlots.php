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

$date = $_GET['date'];

// Use prepared statements to prevent SQL injection
$sql = "SELECT username AS home_name, slots FROM user_time_slots WHERE date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$availableSlots = array();
$timeSlots = [
    "9am to 10am",
    "10am to 11am",
    "11am to 12pm",
    "12pm to 1pm",
    "1pm to 2pm",
    "2pm to 3pm",
    "3pm to 4pm"
];

while ($row = $result->fetch_assoc()) {
    $slots = json_decode($row['slots']); // Decode the JSON-encoded slots array
    if (is_array($slots)) {
        foreach ($slots as $index => $isAvailable) {
            if ($isAvailable) {
                $availableSlots[] = array('home_name' => $row['home_name'], 'slot' => $timeSlots[$index]);
            }
        }
    }
}

$stmt->close();
$conn->close();

// Set content type to JSON
header('Content-Type: application/json');

// Encode array to JSON and output
echo json_encode($availableSlots, JSON_PRETTY_PRINT);
?>
