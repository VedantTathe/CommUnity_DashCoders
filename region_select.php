<?php
session_start();

// Check if the user is logged in
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
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Save the region in the session
    $_SESSION['region'] = ['latitude' => $latitude, 'longitude' => $longitude];

    // Update saved region to display immediately
    $savedLatitude = $latitude;
    $savedLongitude = $longitude;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Your Region</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            text-align: center;
        }
        h1 {
            margin: 20px 0;
            color: #333;
        }
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }
        form {
            display: inline-block;
            text-align: left;
            max-width: 300px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        #saved-region {
            margin-top: 20px;
            text-align: center;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <h1>Select Your Region</h1>
    <div id="map"></div>
    <form method="POST">
        <label for="latitude">Latitude:</label>
        <input type="text" id="latitude" name="latitude" value="<?php echo htmlspecialchars($savedLatitude); ?>" readonly required>

        <label for="longitude">Longitude:</label>
        <input type="text" id="longitude" name="longitude" value="<?php echo htmlspecialchars($savedLongitude); ?>" readonly required>

        <button type="submit">Save Region</button>
    </form>

    <div id="saved-region">
        <p><strong>Saved Region:</strong></p>
        <p>Latitude: <?php echo htmlspecialchars($savedLatitude); ?></p>
        <p>Longitude: <?php echo htmlspecialchars($savedLongitude); ?></p>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize the map and set its view to the saved region or default
        const map = L.map('map').setView([<?php echo $savedLatitude; ?>, <?php echo $savedLongitude; ?>], 14);

        // Add OpenStreetMap tiles to the map
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add a draggable marker to the map
        const marker = L.marker([<?php echo $savedLatitude; ?>, <?php echo $savedLongitude; ?>], { draggable: true }).addTo(map);

        // Update latitude and longitude fields when the marker is dragged
        marker.on('moveend', (event) => {
            const { lat, lng } = event.target.getLatLng();
            document.getElementById('latitude').value = lat.toFixed(6); // Format to 6 decimal places
            document.getElementById('longitude').value = lng.toFixed(6);
        });
    </script>
</body>
</html>
