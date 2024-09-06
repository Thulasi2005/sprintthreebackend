<?php
header('Content-Type: application/json');
include 'config.php'; // Include your database connection file

$response = [];

try {
    // Check if email is set in the POST request
    if (!isset($_POST['email'])) {
        throw new Exception('Email not provided');
    }

    // Get email from POST request
    $email = $_POST['email'];

    // Generate a random OTP
    $otp = rand(100000, 999999);

    // Set OTP expiry time (e.g., 10 minutes)
    $expiry_time = date('Y-m-d H:i:s', strtotime('+10 minutes'));

    // Check if email exists in the database
    $query = "SELECT * FROM donors WHERE email = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Database query preparation failed');
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email exists, update OTP
        $update_query = "UPDATE donors SET otp_code = ?, otp_expiry = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_query);
        if (!$update_stmt) {
            throw new Exception('Database update query preparation failed');
        }
        $update_stmt->bind_param("sss", $otp, $expiry_time, $email);
        $update_stmt->execute();
    } else {
        // Email does not exist, respond with error
        throw new Exception('Email not found');
    }

    // Send OTP to user's email
    $to = $email;
    $subject = "Your OTP Code";
    $message = "Your OTP code is: $otp\n\nThis code will expire in 10 minutes.";
    $headers = "From: no-reply@yourdomain.com";

    if (mail($to, $subject, $message, $headers)) {
        $response = ['success' => true, 'message' => 'OTP sent successfully'];
    } else {
        throw new Exception('Failed to send OTP');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);

if (isset($stmt)) {
    $stmt->close();
}

if (isset($update_stmt)) {
    $update_stmt->close();
}

if (isset($conn)) {
    $conn->close();
}
?>
