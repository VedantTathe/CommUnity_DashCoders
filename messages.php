<?php
include 'db.php'; // Include the database connection
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html'); // Redirect if not logged in
    exit;
}

// Initialize variables
$request_id = null;

// Determine the request_id (GET or POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = $_POST['request_id'] ?? null;
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $request_id = $_GET['request_id'] ?? null;
}

// Validate request_id
if (!$request_id) {
    echo "<p class='error'>Invalid or missing request ID.</p>";
    exit;
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message'] ?? '');
    $sender_id = $_SESSION['user_id'];

    if (!empty($message)) {
        // Insert the message into the database
        $sql = $conn->prepare("INSERT INTO messages (request_id, sender_id, content) VALUES (?, ?, ?)");
        $sql->bind_param("iis", $request_id, $sender_id, $message);

        if ($sql->execute()) {
            header("Location: messages.php?request_id=$request_id"); // Redirect back to the chat
            exit;
        } else {
            echo "<p class='error'>Error submitting message: " . $conn->error . "</p>";
        }
    } else {
        echo "<p class='error'>Please enter a message.</p>";
    }
}

// Handle request acceptance
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_request'])) {
    $acceptor_id = $_SESSION['user_id'];

    // Check if the request has already been accepted
    $check_acceptor_sql = $conn->prepare("SELECT acceptor_id FROM requests WHERE id = ?");
    $check_acceptor_sql->bind_param("i", $request_id);
    $check_acceptor_sql->execute();
    $acceptor_result = $check_acceptor_sql->get_result();

    if ($acceptor_result->num_rows > 0) {
        $acceptor_row = $acceptor_result->fetch_assoc();
        if ($acceptor_row['acceptor_id']) {
            echo "<p class='error'>This request has already been accepted by another user (ID: " . htmlspecialchars($acceptor_row['acceptor_id'], ENT_QUOTES) . ").</p>";
            exit;
        }
    } else {
        echo "<p class='error'>Request not found.</p>";
        exit;
    }

    // Update the request to set the acceptor_id
    $update_sql = $conn->prepare("UPDATE requests SET acceptor_id = ? WHERE id = ? AND acceptor_id IS NULL");
    $update_sql->bind_param("ii", $acceptor_id, $request_id);

    if ($update_sql->execute() && $update_sql->affected_rows > 0) {
        // Notify the requester
        $request_sql = $conn->prepare("SELECT user_id FROM requests WHERE id = ?");
        $request_sql->bind_param("i", $request_id);
        $request_sql->execute();
        $request_result = $request_sql->get_result();

        if ($request_result->num_rows > 0) {
            $request = $request_result->fetch_assoc();
            $requester_user_id = $request['user_id'];

            $notification = "Your request has been accepted by user ID {$acceptor_id}!";
            $notify_sql = $conn->prepare("INSERT INTO notifications (user_id, content) VALUES (?, ?)");
            $notify_sql->bind_param("is", $requester_user_id, $notification);

            if ($notify_sql->execute()) {
                echo "<p class='success'>Request accepted and the user has been notified.</p>";
                header("Location: messages.php?request_id=$request_id"); // Redirect to the chat
                exit;
            } else {
                echo "<p class='error'>Error sending notification: " . $conn->error . "</p>";
            }
        } else {
            echo "<p class='error'>Unable to find the requester user ID.</p>";
        }
    } else {
        echo "<p class='error'>Error accepting request: " . $conn->error . "</p>";
    }
}

// Fetch request details and display the chat interface
$sql = $conn->prepare("SELECT requests.*, users.name AS requester_name, requests.acceptor_id 
                       FROM requests 
                       JOIN users ON requests.user_id = users.id 
                       WHERE requests.id = ?");
$sql->bind_param("i", $request_id);
$sql->execute();
$result = $sql->get_result();

if ($result->num_rows > 0) {
    $request = $result->fetch_assoc();
    $requester_name = htmlspecialchars($request['requester_name'], ENT_QUOTES);
    $acceptor_id = $request['acceptor_id'];

    // Fetch the name of the user who accepted the request
    if ($acceptor_id) {
        $acceptor_sql = $conn->prepare("SELECT name FROM users WHERE id = ?");
        $acceptor_sql->bind_param("i", $acceptor_id);
        $acceptor_sql->execute();
        $acceptor_result = $acceptor_sql->get_result();

        if ($acceptor_result->num_rows > 0) {
            $acceptor = $acceptor_result->fetch_assoc();
            $acceptor_name = htmlspecialchars($acceptor['name'], ENT_QUOTES);
        } else {
            $acceptor_name = "Unknown";
        }
    } else {
        $acceptor_name = "Not accepted yet";
    }

    // Display the chat header and form
    echo "
    <nav>
        <div><h1>CommUnity</h1></div>
        <div class='link-header'>
            <a href='post-request.php'>Post Request</a>
            <a href='offer-help.php'>Offer Help</a>
            <a href='profile.php'>Profile</a>
            <a href='logout.php'>Log Out</a>
        </div>
    </nav>";

    echo "<div class='chat-container'>
            <h1><div class='chat'>Chat With</div> {$requester_name}</h1>
            <p>Status: Request {$acceptor_name}</p>
            <form action='messages.php' method='POST'>
                <input type='hidden' name='request_id' value='$request_id'>
                <textarea name='message' placeholder='Type your message here...' required></textarea>
                <button type='submit' id='ide1'><img src='send.png'></button>
            </form>";

    // Display the accept request button if the user is not the requester
    if ($request['user_id'] !== $_SESSION['user_id'] && !$request['acceptor_id']) {
        echo "<form action='messages.php' method='POST'>
                <input type='hidden' name='request_id' value='$request_id'>
                <button type='submit' name='accept_request'>Accept Request</button>
              </form>";
    }

    // Fetch and display previous messages
    $chat_sql = $conn->prepare("SELECT messages.*, users.name AS sender_name 
                                FROM messages 
                                JOIN users ON messages.sender_id = users.id 
                                WHERE messages.request_id = ? 
                                ORDER BY messages.created_at ASC");
    $chat_sql->bind_param("i", $request_id);
    $chat_sql->execute();
    $chat_result = $chat_sql->get_result();

    echo "<div class='message-history'>";
    while ($msg = $chat_result->fetch_assoc()) {
        $sender_name = htmlspecialchars($msg['sender_name'], ENT_QUOTES);
        $content = htmlspecialchars($msg['content'], ENT_QUOTES);
        $timestamp = htmlspecialchars($msg['created_at'], ENT_QUOTES);

        echo "<div class='message'>
                <strong>{$sender_name}:</strong>
                <p>{$content}</p>
                <span class='timestamp'>{$timestamp}</span>
              </div>";
    }
    echo "</div>";
} else {
    echo "<p class='error'>Request not found.</p>";
}
echo "</div>";
?>

<!-- Link to the external CSS and JavaScript -->
<link rel="stylesheet" href="message.css">
<script src="message.js"></script>
