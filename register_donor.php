<?php
session_start();
require 'config.php'; // Database connection configuration

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST data
    $fullName = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob = $_POST['date_of_birth'] ?? '';
    $identity = $_POST['national_identity_number'] ?? '';
    $contact = $_POST['contact_number'] ?? '';
    $address = $_POST['permanent_address'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $termsAccepted = $_POST['terms_accepted'] ?? '';

    // Check if required fields are provided
    if (empty($fullName) || empty($email) || empty($dob) || empty($identity) || empty($contact) || empty($address) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL to insert new donor
    $sql = "INSERT INTO donors (full_name, email, date_of_birth, national_identity_number, contact_number, permanent_address, occupation, username, password, terms_accepted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param("ssssssssss", $fullName, $email, $dob, $identity, $contact, $address, $occupation, $username, $hashedPassword, $termsAccepted);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
