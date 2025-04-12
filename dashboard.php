a<?php
include 'db.php'; // Include database connection
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html'); // Redirect if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(0deg,rgba(58, 232, 252, 0.81) 10%,rgba(174, 241, 223, 0.81) 40%);
        }
        header {
            background: linear-gradient(180deg, #f2f2f2 0%, #1adff5 100%);
            
            color: white;
            padding: 10px 20px;
        }
        nav a {
        color: #000000;
        text-decoration: none;
        margin: 0 15px;
        font-size: 16px;
        border-radius: 10px;
        border: 2px solid black;
        padding: 7px;
        background-color:rgb(34, 201, 209);
        animation-delay: 1s;
        transition: transform 0.3s ease, background-color 0.3s ease;
        display: inline-block;
      }
      nav{
        margin-top:10px;
      }
      nav a:hover{
        background-color: #f8f8f8;
        transform: scale(1.1);
       
      }
        main {
            padding: 20px;
           
           
        }
        .request {
            display: flex;
            flex-direction:column;
            justify-content: center;
            align-items: center;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 10px;
            border-radius: 20px;
            background-color:rgb(228, 235, 238);
            width:25%;
            min-height: 350px;
            position: relative;
            border:5px solid black;
           
            
        }
        .request h1 {
            margin: 0;
            margin-top:10%;
           font-size:30px;
            position: absolute;
            top: 0; 
            left: 50%; 
             transform: translateX(-50%);

        }
        .request p {
            margin: 10px 0;
            font-size: 16px;
            
        }
        .actions {
            text-align: center;
            margin-top:20px
            
        }
        .actions a {
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 3px;
            margin: 10px;
           
        }
        @media (max-width: 700px) {
            .actions a{
                font-size:1vh;
            }
        }
        .actions a:hover {
            background-color: #0056b3;
        }
        header h1{
            color:rgb(30, 34, 38);
        }
        .requests-container {
    display: flex;
    align-item:center;
    flex-wrap: wrap; /* Allow cards to wrap to the next line */
    gap: 10px; /* Add space between the cards */
    justify-content: center; /* Align the cards properly */
}
a{
    text-decoration:none;
}



/*popupadd*/
#ad-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none; /* Hide by default */
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        #ad-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            max-width: 500px;
            width: 80%;
            position:relative;
        }
        #close-ad {
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 50%;
            position: absolute;
            top: 10px;
            right: 10px;
            width: 40px;
            height: 40px;
            text-align: center;
            line-height: 20px;
            font-size: 20px;
        }
        #close-ad img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }
       
    </style>
</head>
<body>

<!-- Ad Popup -->

<div id="ad-popup">
    <a href="ad.html" style="text-decoration: none; color: black;">
        <div id="ad-content">
            <h2>Special Advertisement</h2>
            <p>Check out our latest deals!</p>
            
            <!-- Image inside the ad popup -->
            <img src="./uploads/6772ddb31266e_retail-shop.jpg" alt="Advertisement" style="width: 100%; height: 70%; object-fit: cover; margin-bottom: 15px;">
        </div>
    </a>
    <button id="close-ad"><img src="./uploads/close.png" alt="Close Ad"></button>
</div>


    <header>
        <a href="index.html">
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
            <a href="profile.php">profile</a>
            <a href="ad.html">Advertisment</a>
            <a href="logout.php">Logout</a>
        </nav>
       </center>
    </header>
    <main>
        <h2>Help Requests</h2>
        <div class="requests-container">
        <?php
// Assuming you already have a database connection in $conn
$sql = "SELECT requests.*, users.name AS user_name 
        FROM requests 
        JOIN users ON requests.user_id = users.id"; // Join the users table to fetch the name

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<div class='request'>
                <div>
                    
                
                    <h1>{$row['title']}</h1>
                    <p>" . nl2br(htmlspecialchars($row['description'])) . "</p>
                    <p><strong>Category:</strong> {$row['category']}</p>
                    <p><strong>Request ID:</strong> {$row['id']}</p>
                    <p><strong>Posted by:</strong> {$row['user_name']}</p> <!-- Display the user's name -->
                </div>
                <div class='actions'>
                    <a href='messages.php?request_id={$row['id']}'>view message</a>
                </div>
              </div>";
    }
} else {
    echo "<p>No requests available.</p>";
}
?>

<script>
        // Display the ad popup when the page loads
        window.onload = function() {
            document.getElementById('ad-popup').style.display = 'flex';
        }

        // Close the ad when the close button is clicked
        document.getElementById('close-ad').onclick = function() {
            document.getElementById('ad-popup').style.display = 'none';
        }

        // Redirect to the ad page when the "View Ad" button is clicked
        document.getElementById('view-ad').onclick = function() {
            window.location.href = 'ad.html'; // Redirect to your ad page
        }
    </script>

</div>
    </main>
</body>
</html>  