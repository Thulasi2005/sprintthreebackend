<?php
session_start();
require 'config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameOrEmail = isset($_POST['usernameOrEmail']) ? $_POST['usernameOrEmail'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Prepare SQL to fetch user based on username or email
    $sql = "SELECT * FROM elderly_homes WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // Bind parameters and execute
    $stmt->bind_param("ss", $usernameOrEmail, $usernameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user is found
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify the password
        if (password_verify($password, $user['password'])) {
            $_SESSION['elderly_home_id'] = $user['id'];
            echo json_encode([
                'success' => true, 
                'message' => 'Login successful',
                'username' => $user['username'] // Include username in response
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid password']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found']);
    }

    // Close statement
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

// Close connection
$conn->close();
?>
