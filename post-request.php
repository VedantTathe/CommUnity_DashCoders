<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Initialize default region or saved region
$defaultLatitude = 19.1631; // Example: Nanded
$defaultLongitude = 77.3144;

$savedLatitude = $_SESSION['region']['latitude'] ?? $defaultLatitude;
$savedLongitude = $_SESSION['region']['longitude'] ?? $defaultLongitude;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the help request submission
    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['category'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $category = $_POST['category'];
        $user_id = $_SESSION['user_id'];
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];

        // Save the region in the session
        $_SESSION['region'] = ['latitude' => $latitude, 'longitude' => $longitude];

        // Insert help request into the database
        $sql = "INSERT INTO requests (title, description, category, user_id, latitude, longitude) 
                VALUES ('$title', '$description', '$category', '$user_id', '$latitude', '$longitude')";
        if ($conn->query($sql) === TRUE) {
            header('Location: dashboard.php');
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Help Request</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(0deg,rgba(58, 232, 252, 0.81) 10%,rgba(174, 241, 223, 0.81) 40%);
            color: #333;
          
        }

        h1 {
            margin: 20px 0;
            font-size: 2rem;
            text-align: center;
            color: #4CAF50;
        }

        #map {
            height: 300px;
            width: 50%;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin: 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 90%;
        }

        label {
            font-size: 1rem;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            font-size: 1.2rem;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
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

header {
    background: linear-gradient(180deg, #f2f2f2 0%, #1adff5 100%);
    
    color: white;
    padding: 10px 20px;
}
header h1{
    text-align: center;
    color:rgb(30, 34, 38);
    
}
a{
    text-decoration: none;;
}





        @media (max-width: 768px) {
            h1 {
                font-size: 1.8rem;
            }

            #map {
                height: 250px;
            }

            form {
                padding: 15px;
            }

            button {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            #map {
                height: 200px;
            }

            form {
                padding: 10px;
            }

            button {
                padding: 10px;
                font-size: 0.9rem;
            }
        }


    </style>
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
    <h1>Post Help Request</h1>
    <center>
    <div id="map"></div>
    </center>
    <form method="POST">
        <label for="title">Request Title:</label>
        <input type="text" id="title" name="title" placeholder="Request Title" required>

        <label for="description">Request Details:</label>
        <textarea id="description" name="description" placeholder="Request Details" rows="4" required></textarea>

        <label for="category">Category:</label>
        <select id="category" name="category">
            <option value="groceries">Groceries</option>
            <option value="repairs">Repairs</option>
            <option value="other">Other</option>
        </select>
        <input type="hidden" name="poster_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($savedLatitude); ?>" readonly required>

        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($savedLongitude); ?>" readonly required>

        <button type="submit">Post Request</button>
    </form>



    <div id="saved-region">
        <p><strong>Saved Region:</strong></p>
        <p>Latitude: <?php echo htmlspecialchars($savedLatitude); ?></p>
        <p>Longitude: <?php echo htmlspecialchars($savedLongitude); ?></p>
    </div>

   <!-- Load Leaflet.js library first -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" defer></script>

<!-- Load leaflet-search library after Leaflet -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/3.0.4/leaflet-search.min.js" defer></script>

<!-- Load Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Load leaflet-search CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-search/3.0.4/leaflet-search.min.css" />

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize the map and set its view to the saved region or default
        const savedLat = <?php echo $savedLatitude; ?>;
        const savedLng = <?php echo $savedLongitude; ?>;

        const map = L.map('map').setView([savedLat, savedLng], 14);

        // Add OpenStreetMap tiles to the map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add a draggable marker to the map
        const marker = L.marker([savedLat, savedLng], { draggable: true }).addTo(map);

        // Update latitude and longitude fields when the marker is dragged
        marker.on('moveend', (event) => {
            const { lat, lng } = event.target.getLatLng();
            document.getElementById('latitude').value = lat.toFixed(6); // Format to 6 decimal places
            document.getElementById('longitude').value = lng.toFixed(6);
        });

        // Add search control to the map
        const searchControl = new L.Control.Search({
            url: 'https://nominatim.openstreetmap.org/search?format=json&q={s}', // Search endpoint
            jsonpParam: 'json_callback',
            propertyName: 'display_name',
            propertyLoc: ['lat', 'lon'],
            marker: false,
            moveToLocation: function (latlng) {
                map.setView(latlng, 14); // Zoom to the selected location
                marker.setLatLng(latlng); // Move marker to the selected location
                document.getElementById('latitude').value = latlng.lat.toFixed(6);
                document.getElementById('longitude').value = latlng.lng.toFixed(6);
            },
        });

        map.addControl(searchControl);
    });
</script>

</body>
</html>
