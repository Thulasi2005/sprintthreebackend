<?php
header('Content-Type: application/json');
include 'db_connect.php'; // Include your database connection file

// Get POST data
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$date_of_birth = $_POST['date_of_birth'];
$national_identity_number = $_POST['national_identity_number'];
$contact_number = $_POST['contact_number'];
$permanent_address = $_POST['permanent_address'];
$occupation = $_POST['occupation'];
$username = $_POST['username'];
$password = $_POST['password'];
$terms_accepted = $_POST['terms_accepted'];
$otp_code = $_POST['otp_code'];

// Check OTP validity
$otp_query = "SELECT * FROM donors WHERE email = ? AND otp_code = ? AND otp_expiry > NOW()";
$otp_stmt = $conn->prepare($otp_query);
$otp_stmt->bind_param("ss", $email, $otp_code);
$otp_stmt->execute();
$otp_result = $otp_stmt->get_result();

if ($otp_result->num_rows > 0) {
    // OTP is valid, proceed with registration
    $register_query = "INSERT INTO donors (full_name, email, date_of_birth, national_identity_number, contact_number, permanent_address, occupation, username, password, terms_accepted) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $register_stmt = $conn->prepare($register_query);
    $register_stmt->bind_param("ssssssssss", $full_name, $email, $date_of_birth, $national_identity_number, $contact_number, $permanent_address, $occupation, $username, $password, $terms_accepted);
    if ($register_stmt->execute()) {
        // Clear OTP code after successful registration
        $clear_otp_query = "UPDATE donors SET otp_code = NULL, otp_expiry = NULL WHERE email = ?";
        $clear_otp_stmt = $conn->prepare($clear_otp_query);
        $clear_otp_stmt->bind_param("s", $email);
        $clear_otp_stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Registration successful']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid or expired OTP']);
}

$otp_stmt->close();
$register_stmt->close();
$conn->close();
?>
