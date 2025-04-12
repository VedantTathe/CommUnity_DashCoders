<?php
// Include the database connection
require 'db.php';
session_start();  // Start session to access the logged-in user's data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $image = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = uniqid() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], 'uploads/' . $image);
    }

    // Get the logged-in user's ID from session
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Prepare the SQL statement
    $stmt = $conn->prepare('INSERT INTO posts (title, description, type, image, user_id) VALUES (?, ?, ?, ?, ?)');
    $stmt->bind_param('ssssi', $title, $description, $type, $image, $user_id);  // Added user_id
    $stmt->execute();
    $stmt->close();

    echo 'Success';
}

$conn->close();
?>
