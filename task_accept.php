<?php
// task_accept.php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $taskId = $_POST['task_id'];
    $serviceProviderId = $_POST['provider_id'];

    // Update escrow record
    $stmt = $conn->prepare("UPDATE escrow SET service_provider_id = ?, status = 'In Progress' WHERE task_id = ?");
    $stmt->bind_param("ii", $serviceProviderId, $taskId);

    if ($stmt->execute()) {
        echo "Task accepted successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
