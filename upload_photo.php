<?php
session_start();
require 'db.php'; // Replace with your database connection file

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $userId = $_SESSION['user_id'];
    $photo = $_FILES['profile_photo'];

    // Validate file
    if ($photo['size'] > 0 && $photo['size'] <= 2 * 1024 * 1024) { // Limit: 2MB
        $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $target = "uploads/" . uniqid('photo_', true) . '.' . $ext;
            if (move_uploaded_file($photo['tmp_name'], $target)) {
                $query = "UPDATE users SET profile_photo = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param('si', $target, $userId);
                if ($stmt->execute()) {
                    header('Location: profile.php');
                    exit;
                }
            }
        }
    }
    echo "Failed to upload photo. Ensure it's a valid image under 2MB.";
}
