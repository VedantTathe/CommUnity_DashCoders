<?php
include 'db.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get form values
    $fullName = $_POST['fullName'];
    $phone = $_POST['phoneNumber'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // File upload handling
    $aadharPhotoName = $_FILES['aadharPhoto']['name'];
    $aadharPhotoTmp = $_FILES['aadharPhoto']['tmp_name'];
    $uploadDir = 'uploads/';

    // Make sure uploads folder exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $uniqueFilename = uniqid() . '_' . basename($aadharPhotoName);
    $uploadPath = $uploadDir . $uniqueFilename;

    if (move_uploaded_file($aadharPhotoTmp, $uploadPath)) {
        // Insert into DB
        $sql = "INSERT INTO users (name, phone, email, address, password, profile_photo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $fullName, $phone, $email, $address, $password, $uniqueFilename);

        if ($stmt->execute()) {
            header("Location: offer-help.php"); // Redirect on success
            exit();
        } else {
            echo "Database error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Failed to upload Aadhaar photo.";
    }

    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
