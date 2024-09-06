<?php
// Database connection configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db"; // Correct variable name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname); // Use $dbname instead of $db_name

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch elderly home details
$sql = "SELECT * FROM elderly_homes";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Create an array to hold the data
    $homes = array();

    // Fetch the data
    while ($row = $result->fetch_assoc()) {
        $homes[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'address' => $row['address'],
            'contact_number' => $row['contact_number'],
            'number_of_elders' => $row['number_of_elders'],
            'registration_number' => $row['registration_number'],
            'district' => $row['district'],
            'email' => $row['email'],
            'full_name' => $row['full_name'],
            'national_identity_number' => $row['national_identity_number'],
            'role_or_job_title' => $row['role_or_job_title'],
        );
    }

    // Convert the array to JSON and output it
    header('Content-Type: application/json');
    echo json_encode($homes);
} else {
    // If no data found, output an empty JSON array
    echo json_encode(array());
}

// Close the connection
$conn->close();
?>
