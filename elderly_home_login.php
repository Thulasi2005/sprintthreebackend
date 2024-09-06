<?php
session_start();
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = filter_input(INPUT_POST, 'usernameOrEmail', FILTER_SANITIZE_STRING);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

    // Check if username or email and password are provided
    if (empty($usernameOrEmail) || empty($password)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please provide username/email and password']);
        exit;
    }

    // Prepare SQL to check the login credentials
    $sql = "SELECT * FROM elderly_home WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct
            // Store user info in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            echo json_encode(['success' => true, 'message' => 'Login successful']);
        } else {
            // Incorrect password
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Incorrect password']);
        }
    } else {
        // No user found with the given username or email
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }

    // Close connections
    $stmt->close();
    $conn->close();
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
