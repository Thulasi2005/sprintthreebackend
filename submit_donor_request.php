<?php
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['donor_name']) && !empty($data['donor_name']) &&
        isset($data['donor_contact_number']) && !empty($data['donor_contact_number']) &&
        isset($data['donor_occupation']) && !empty($data['donor_occupation']) &&
        isset($data['donor_national_identity_number']) && !empty($data['donor_national_identity_number']) &&
        isset($data['purpose']) && !empty($data['purpose']) &&
        isset($data['description']) && !empty($data['description']) &&
        isset($data['time_period']) && !empty($data['time_period'])) {
        
        // Prepare SQL statement
        $stmt = $conn->prepare("INSERT INTO donor_requests 
            (donor_name, donor_contact_number, donor_occupation, donor_national_identity_number, purpose, description, time_period) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssss", 
            $data['donor_name'], 
            $data['donor_contact_number'], 
            $data['donor_occupation'], 
            $data['donor_national_identity_number'], 
            $data['purpose'], 
            $data['description'], 
            $data['time_period']
        );

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Donor request submitted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to submit donor request.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

// Close the connection
$conn->close();
?>
