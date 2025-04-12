<?php
// post_task.php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
}
?>
