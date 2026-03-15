<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch user's friends
$sql = "SELECT u.id, u.name, l.latitude, l.longitude, l.timestamp
        FROM friends f
        JOIN users u ON (f.friend_id = u.id OR f.user_id = u.id) AND u.id != '$user_id'
        JOIN locations l ON l.user_id = u.id
        WHERE (f.user_id = '$user_id' OR f.friend_id = '$user_id') AND f.status = 'accepted'
        ORDER BY l.timestamp DESC";
$result = $conn->query($sql);

$friends = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $friends[] = $row;
    }
}

// If no friends, use dummy locations
if (empty($friends)) {
    $friends = [
        ['id' => 'dummy1', 'name' => 'Dummy Friend 1', 'latitude' => 40.7128, 'longitude' => -74.0060, 'timestamp' => date('Y-m-d H:i:s')],
        ['id' => 'dummy2', 'name' => 'Dummy Friend 2', 'latitude' => 34.0522, 'longitude' => -118.2437, 'timestamp' => date('Y-m-d H:i:s')],
        ['id' => 'dummy3', 'name' => 'Dummy Friend 3', 'latitude' => 41.8781, 'longitude' => -87.6298, 'timestamp' => date('Y-m-d H:i:s')]
    ];
}

// Fetch user's own location
$sql_user = "SELECT latitude, longitude FROM locations WHERE user_id = '$user_id' ORDER BY timestamp DESC LIMIT 1";
$result_user = $conn->query($sql_user);
$user_location = $result_user->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Map - Converge Meet App</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .map-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .map-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .map-header {
            padding: 20px;
            background: #FF6B6B;
            color: white;
            text-align: center;
        }
        .map-header h1 {
            margin: 0;
            font-size: 28px;
        }
        #map {
            height: 600px;
            width: 100%;
        }
        .map-info {
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }
        .map-info p {
            margin: 0;
            color: #555;
        }
        .navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        @media (max-width: 768px) {
            .map-container {
                margin: 20px;
                padding: 10px;
            }
            #map {
                height: 400px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="logo">Converge Meet</div>
        <div class="nav-links">
            <a href="../user/dashboard.php">Dashboard</a>
            <a href="../friends/friends.php">Friends</a>
            <a href="../events/view_events.php">Events</a>
            <a href="../auth/logout.php">Logout</a>
        </div>
    </nav>

    <div class="map-container">
        <div class="map-card">
            <div class="map-header">
                <h1>Real-Time Map</h1>
            </div>
            <div id="map"></div>
            <div class="map-info">
                <p><strong>Your location:</strong> Tracking in real-time • <strong>Friends:</strong> Locations update every 10 seconds</p>
            </div>
        </div>
    </div>

    <script>
        var map = L.map('map').setView([<?php echo $user_location ? $user_location['latitude'] : 0; ?>, <?php echo $user_location ? $user_location['longitude'] : 0; ?>], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var markers = {};

        // Add user's marker
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(function(position) {
                var lat = position.coords.latitude;
                var lng = position.coords.longitude;

                if (markers['user']) {
                    map.removeLayer(markers['user']);
                }

                markers['user'] = L.marker([lat, lng]).addTo(map)
                    .bindPopup('You are here').openPopup();

                // Update user's location in database
                fetch('update_location.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'latitude=' + lat + '&longitude=' + lng
                });
            });
        }

        // Function to update friends' locations
        function updateFriendsLocations() {
            fetch('get_friends_locations.php')
                .then(response => response.json())
                .then(data => {
                    data.forEach(friend => {
                        if (markers[friend.id]) {
                            map.removeLayer(markers[friend.id]);
                        }
                        markers[friend.id] = L.marker([friend.latitude, friend.longitude]).addTo(map)
                            .bindPopup(friend.name);
                    });
                });
        }

        // Initial load
        updateFriendsLocations();

        // Update every 10 seconds
        setInterval(updateFriendsLocations, 10000);
    </script>
</body>
</html>
