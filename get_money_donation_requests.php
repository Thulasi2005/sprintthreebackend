<?php
header('Content-Type: application/json');

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

// SQL query to fetch all donation requests
$sql = "SELECT 
            id,
            elderly_home_name,
            contact_address,
            contact_number,
            amount_required,
            purpose,
            description,
            supporting_documents,
            selected_images,
            status,
            created_at
        FROM money_donation_requests";

$result = $conn->query($sql);

$donationRequests = array();

if ($result->num_rows > 0) {
    // Output data for each row
    while($row = $result->fetch_assoc()) {
        $donationRequests[] = array(
            'id' => $row['id'],
            'elderly_home_name' => $row['elderly_home_name'],
            'contact_address' => $row['contact_address'],
            'contact_number' => $row['contact_number'],
            'amount_required' => $row['amount_required'],
            'purpose' => $row['purpose'],
            'description' => $row['description'],
            'supporting_documents' => $row['supporting_documents'],
            'selected_images' => $row['selected_images'],
            'status' => $row['status'],
            'created_at' => $row['created_at']
        );
    }
} else {
    // No records found
    echo json_encode([]);
    exit();
}

// Return JSON response
echo json_encode($donationRequests);

// Close database connection
$conn->close();
?>
