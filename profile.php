<?php
session_start();
require 'db.php'; // Database connection script

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch user data
$stmt = $conn->prepare("SELECT name, email, profile_photo FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    $username = $user['name'];
    $email = $user['email'];
    $profilePhoto = $user['profile_photo'] ? $user['profile_photo'] : 'default-avatar.png'; // Default avatar if no photo
} else {
    echo "User not found.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<header>
    <a href="dashboard.php">
    <h1
          style="
            font-size: 48px;
            font-weight: 700;
            margin: 0;
            z-index: 2;
            position: relative;
            
          "
        >
        CommUnity
        </h1></a>
      
        <center>
        <nav>
            <a href="post-request.php">Post Request</a>
            <a href="offer-help.php">Offer Help</a>
            <a href="ad.html">Job</a>
            <a href="profile.php">profile</a>
            <a href="logout.php">Logout</a>
        </nav>
       </center>
    </header>
    <center>
    <div class="profile-container">
        <div class="card">
            <div class="profile-photo">
                <img src="<?php echo htmlspecialchars($profilePhoto); ?>" alt="Profile Photo">
            </div>
            <h1 class="username"><?php echo htmlspecialchars($username); ?></h1>
            <p class="email"><?php echo htmlspecialchars($email); ?></p>
            <a href="dashboard.php" class="btn">Back to Dashboard</a>

            <form action="upload_photo.php" method="POST" enctype="multipart/form-data" class="upload-form">
                <label for="profile_photo" class="file-label">Update Profile Photo</label>
                <input type="file" name="profile_photo" id="profile_photo" accept="image/*">
                <button type="submit" class="btn upload-btn">Upload</button>
            </form>
        </div>
    </div>
</center>
</body>
</html>
