<?php
include 'db.php'; // Include the database connection
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Check if request_id is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $user_id = $_SESSION['user_id'];

    // Verify that the logged-in user owns the request
    $sql = $conn->prepare("SELECT * FROM requests WHERE id = ? AND user_id = ?");
    $sql->bind_param("ii", $request_id, $user_id);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows > 0) {
        // Delete the request
        $delete_sql = $conn->prepare("DELETE FROM requests WHERE id = ?");
        $delete_sql->bind_param("i", $request_id);

        if ($delete_sql->execute()) {
            echo "<script>
                    alert('Request deleted successfully.');
                    window.location.href = 'offer-help.php';
                  </script>";
        } else {
            echo "<p class='error'>Error deleting request: " . $conn->error . "</p>";
        }
    } else {
        echo "<p class='error'>You are not authorized to delete this request.</p>";
    }
} else {
    echo "<p class='error'>Invalid request.</p>";
}
?>
