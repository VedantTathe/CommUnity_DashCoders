<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO requests (title, description, category, user_id) VALUES ('$title', '$description', '$category', '$user_id')";
    if ($conn->query($sql) === TRUE) {
        header('Location: dashboard.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
$taskId = $_POST['task_id'];
    $posterId = $_POST['poster_id'];
    $amount = $_POST['amount'];

    // Insert into escrow table
    $stmt = $conn->prepare("INSERT INTO escrow (task_id, amount, status, task_poster_id) VALUES (?, ?, 'Pending', ?)");
    $stmt->bind_param("idi", $taskId, $amount, $posterId);

    if ($stmt->execute()) {
        echo "Task posted and payment secured in escrow.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
?>
