<?php
// task_complete.php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $escrowId = $_POST['escrow_id'];
    $posterConfirmation = $_POST['poster_confirmation'];

    if ($posterConfirmation === "yes") {
        // Release payment to the provider
        $stmt = $conn->prepare("
            UPDATE users AS u
            JOIN escrow AS e ON u.id = e.service_provider_id
            SET u.wallet_balance = u.wallet_balance + e.amount, e.status = 'Completed'
            WHERE e.id = ?
        ");
        $stmt->bind_param("i", $escrowId);

        if ($stmt->execute()) {
            echo "Payment released successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Task not confirmed.";
    }

    $stmt->close();
    $conn->close();
}
?>
