<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = ""; // Your database password
$dbname = "donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect and sanitize form data
    $elderlyHomeName = sanitizeInput($_POST['elderly_home_name']);
    $contactAddress = sanitizeInput($_POST['contact_address']);
    $contactNumber = sanitizeInput($_POST['contact_number']);
    $amountRequired = sanitizeInput($_POST['amount']);
    $purpose = sanitizeInput($_POST['purpose']);
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';

    // Prepare file paths for storage
    $supportingDocuments = '';
    $selectedImages = '';

    // Ensure the uploads directory exists and is writable
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Process supporting documents
    if (!empty($_FILES['supporting_documents']['name'][0])) {
        $supportingDocumentsArray = [];
        foreach ($_FILES['supporting_documents']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['supporting_documents']['name'][$key]);
            $fileTmpName = $_FILES['supporting_documents']['tmp_name'][$key];
            $fileDestination = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $supportingDocumentsArray[] = $fileDestination;
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to upload supporting document: $fileName"]);
                exit;
            }
        }
        $supportingDocuments = implode(",", $supportingDocumentsArray);
    }

    // Process selected images
    if (!empty($_FILES['selected_images']['name'][0])) {
        $selectedImagesArray = [];
        foreach ($_FILES['selected_images']['tmp_name'] as $key => $tmp_name) {
            $fileName = basename($_FILES['selected_images']['name'][$key]);
            $fileTmpName = $_FILES['selected_images']['tmp_name'][$key];
            $fileDestination = $uploadDir . $fileName;

            if (move_uploaded_file($fileTmpName, $fileDestination)) {
                $selectedImagesArray[] = $fileDestination;
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to upload image: $fileName"]);
                exit;
            }
        }
        $selectedImages = implode(",", $selectedImagesArray);
    }

    // Insert into database
    $sql = "INSERT INTO money_donation_requests (
                elderly_home_name, 
                contact_address, 
                contact_number, 
                amount_required, 
                purpose, 
                description, 
                supporting_documents, 
                selected_images
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?
            )";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "ssssssss", 
            $elderlyHomeName, 
            $contactAddress, 
            $contactNumber, 
            $amountRequired, 
            $purpose, 
            $description, 
            $supportingDocuments, 
            $selectedImages
        );

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Request submitted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to submit request."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to prepare SQL statement."]);
    }
}

$conn->close();
?>
