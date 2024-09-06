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
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        echo json_encode(['status' => 'error', 'message' => 'Token and new password are required']);
        exit;
    }

    // Validate the token
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();

    if (!$email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid token']);
        exit;
    }

    // Update the user's password in the appropriate table
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Try updating in donors table first
    $stmt = $conn->prepare("UPDATE donors SET password = ? WHERE email = ?");
    $stmt->bind_param('ss', $hashedPassword, $email);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $passwordUpdated = true;
    } else {
        // If no rows were affected, try the elderly_homes table
        $stmt = $conn->prepare("UPDATE elderly_homes SET password = ? WHERE email = ?");
        $stmt->bind_param('ss', $hashedPassword, $email);
        $passwordUpdated = $stmt->execute() && $stmt->affected_rows > 0;
    }

    if ($passwordUpdated) {
        // Delete the token so it can't be reused
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status' => 'success', 'message' => 'Password reset successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to reset password']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

// Close the database connection
$conn->close();
?>
