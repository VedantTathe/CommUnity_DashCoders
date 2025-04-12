<?php
include 'db.php'; // Include the database connection
session_start(); // Start the session

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html'); // Redirect if not logged in
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $message = $_POST['message'];
    $sender_id = $_SESSION['user_id'];

    // Fetch sender's name
    $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->bind_param("i", $sender_id);
    $stmt->execute();
    $sender_name = $stmt->get_result()->fetch_assoc()['name'];

    // Insert the message
    $sql = $conn->prepare("INSERT INTO messages (request_id, sender_id, sender_name, content) 
                           VALUES (?, ?, ?, ?)");
    $sql->bind_param("iiss", $request_id, $sender_id, $sender_name, $message);

    if ($sql->execute()) {
        header("Location: messages.php?request_id=$request_id"); // Redirect with request_id
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
