<?php
session_start();
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $contactNumber = $_POST['contact_number'] ?? '';
    $numberOfElders = $_POST['number_of_elders'] ?? '';
    $registrationNumber = $_POST['registration_number'] ?? '';
    $district = $_POST['district'] ?? '';
    $email = $_POST['email'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $nationalIdentityNumber = $_POST['national_identity_number'] ?? '';
    $roleOrJobTitle = $_POST['role_or_job_title'] ?? '';
    $termsAccepted = $_POST['terms_accepted'] ?? '0';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $sql = "INSERT INTO elderly_homes (name, address, contact_number, number_of_elders, registration_number, district, email, full_name, national_identity_number, role_or_job_title, terms_accepted, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("sssssssssssss", $name, $address, $contactNumber, $numberOfElders, $registrationNumber, $district, $email, $fullName, $nationalIdentityNumber, $roleOrJobTitle, $termsAccepted, $username, $hashedPassword);
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
