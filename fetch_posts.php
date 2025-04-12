<?php
// Include the database connection
require 'db.php';
session_start();  // Start session to access the logged-in user's data

$type = $_GET['type'] ?? '';  // Get the type from the query string

// Build the query based on whether a type is provided
$query = 'SELECT * FROM posts';
if ($type) {
    $query .= ' WHERE type = ?';
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $type);
} else {
    $stmt = $conn->prepare($query);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts</title>
    <!-- Link to your CSS file -->
    <link rel="stylesheet" href="posts.css">
</head>
<body>

<div class="posts">
<?php
while ($row = $result->fetch_assoc()) {
    echo '<div class="post">';
    echo '<div class="post-left">';
    if ($row['image']) {
        echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" alt="Post Image" />';
    }
    echo '</div>';
    
    echo '<div class="post-right">';
    echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
    echo '<p>' . htmlspecialchars($row['description']) . '</p>';
    echo '<p><strong>Type:</strong> ' . htmlspecialchars($row['type']) . '</p>';
    
    // Only show delete button if the logged-in user is the one who uploaded the post
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']) {
        echo '<a href="delete_post.php?post_id=' . $row['id'] . '" class="btn">Delete</a>';
    }
    echo '</div>'; // End post-right
    
    echo '</div>'; // End post
}
?>
</div> <!-- End posts -->

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
