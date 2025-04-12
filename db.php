<?php
// Database configuration
$servername = "127.0.0.1";
$username = "root"; // Replace with your database username
// $password = "";
$password = "";
$dbname = "neighbor_helping_app"; 
// $port="3308";
$port="3306";


// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname,$port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
