<?php
header('Content-Type: application/json');

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "donation_db";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]));
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $donorName = $_POST['donor_name'] ?? '';
    $contactNumber = $_POST['contact_number'] ?? '';
    $address = $_POST['address'] ?? '';

    // Check if the receipt image was uploaded
    if (isset($_FILES['receipt_image']) && $_FILES['receipt_image']['error'] === UPLOAD_ERR_OK) {
        $receiptImage = $_FILES['receipt_image'];
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($receiptImage['name']);

        // Ensure the upload directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Move the uploaded file to the upload directory
        if (move_uploaded_file($receiptImage['tmp_name'], $uploadFile)) {
            // Prepare SQL statement
            $stmt = $conn->prepare("INSERT INTO donations (donor_name, contact_number, address, receipt_image) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $donorName, $contactNumber, $address, $uploadFile);

            // Execute the statement
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Donation submitted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to insert data into the database: ' . $stmt->error]);
            }

            // Close the statement
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No receipt image uploaded']);
    }

    // Close the database connection
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
