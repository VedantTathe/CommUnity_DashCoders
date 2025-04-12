<?php
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['fullName'];
    $phone = $_POST['phoneNumber'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $workType = $_POST['workType'];
    $skills = $_POST['skills'];

    $sql = "INSERT INTO workers (full_name, phone, password, work_type, skills) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullName, $phone, $password, $workType, $skills);

    if ($stmt->execute()) {
        // Redirect to dashboard on success
        header("Location: offer-help.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request!";
}
?>
