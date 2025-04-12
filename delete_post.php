<?php
// Include the database connection
require 'db.php';
session_start();  // Start session to access the logged-in user's data

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo 'You must be logged in to delete a post.';
    exit;
}

$post_id = $_GET['post_id'];  // Get the post ID to delete

// Get the user_id of the post to verify ownership
$stmt = $conn->prepare('SELECT user_id FROM posts WHERE id = ?');
$stmt->bind_param('i', $post_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id);
    $stmt->fetch();

    // Check if the logged-in user is the one who uploaded the post
    if ($user_id == $_SESSION['user_id']) {
        // Delete the post
        $stmt = $conn->prepare('DELETE FROM posts WHERE id = ?');
        $stmt->bind_param('i', $post_id);
        $stmt->execute();
        $stmt->close();

        echo 'Post deleted successfully.';
    } else {
        echo 'You are not authorized to delete this post.';
    }
} else {
    echo 'Post not found.';
}

$conn->close();
?>
