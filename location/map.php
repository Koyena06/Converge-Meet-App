<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Check if we are focusing on a specific friend from the Events or Friends page
$focus_friend_id = isset($_GET['friend_id']) ? (int)$_GET['friend_id'] : null;

// Fetch user's own last known location from the RENAMED table: user_location
$stmt = $conn->prepare("SELECT latitude, longitude FROM user_location WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_location = $stmt->get_result()->fetch_assoc();

// Default coordinates (Bhubaneswar center) if no location is found
$start_lat = $user_location['latitude'] ?? 20.2961; 
$start_lng = $user_location['longitude'] ?? 85.8245;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Live Map | Converge</title>
    <link rel="stylesheet" href="../style1.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { 
            height: 600px; 
            width: 100%; 
            border-radius: 15px; 
            z-index: 1; 
            border: 2px solid #eee;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }
        .map-card { 
            background: white; 
            padding: 20px; 
            border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        /* Custom Blue Dot for the Logged-in User */
        .user-marker { 
            background: #0984e3; 
            width: 14px; 
            height: 14px; 
            border-radius: 50%; 
            border: 3px solid white; 
            box-shadow: 0 0 8px rgba(0,0,0,0.4); 
        }
    </style>
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<main class="dashboard-page">
    <div class="hero-content" style="text-align: center; margin-bottom: 30px;">
        <h1 class="page-title">Live <em>Network</em></h1>
        <p class="page-subtitle">
            <?php echo $focus_friend_id ? "Finding your friend on the map..." : "Viewing all active connections in your area."; ?>
        </p>
    </div>

    <div class="map-card">
        <div id="map"></div>
        <div style="margin-top: 15px; font-size: 0.85rem; color: #666; display: flex; justify-content: space-between; align-items: center;">
            <span><span style="color:#0984e3; font-size:1.2rem;">●</span> Your Location</span>
            <span><span style="color:#d63031; font-size:1.2rem;">●</span> Friends</span>
            <span style="font-style: italic;">Auto-refreshing every 10s</span>
        </div>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // 1. Initialize the Map
    var map = L.map('map').setView([<?= $start_lat ?>, <?= $start_lng ?>], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var markers = {};
    var focusId = <?= json_encode($focus_friend_id) ?>;

    // 2. Track Browser Geolocation (Real-Time)
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(function(position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            // Remove old user marker
            if (markers['user']) { map.removeLayer(markers['user']); }
            
            // Create custom blue icon
            var userIcon = L.divIcon({className: 'user-marker'});
            markers['user'] = L.marker([lat, lng], {icon: userIcon}).addTo(map)
                .bindPopup('<b>You</b>');

            // If not focusing on a specific friend, center on user once
            if (!focusId && !markers['initial_center']) {
                map.setView([lat, lng], 13);
                markers['initial_center'] = true;
            }

            // Update database via update_location.php
            var fd = new FormData();
            fd.append('latitude', lat);
            fd.append('longitude', lng);
            fetch('update_location.php', { method: 'POST', body: fd });
        }, function(error) {
            console.error("Geolocation error: ", error);
        }, { enableHighAccuracy: true });
    }

    // 3. Fetch Friends from the Server
    function fetchFriends() {
        fetch('get_friends_locations.php')
            .then(response => response.json())
            .then(data => {
                data.forEach(friend => {
                    // Remove existing marker for this friend to avoid duplicates
                    if (markers[friend.id]) { map.removeLayer(markers[friend.id]); }

                    // Create friend marker (Default Red Leaflet marker)
                    markers[friend.id] = L.marker([friend.latitude, friend.longitude]).addTo(map)
                        .bindPopup('<b>' + friend.name + '</b><br>Last seen: ' + friend.last_seen);

                    // If we clicked "View on Map" for this person, fly to them
                    if (focusId && friend.id == focusId) {
                        map.flyTo([friend.latitude, friend.longitude], 15, { duration: 2 });
                        markers[friend.id].openPopup();
                        focusId = null; // Clear focus after found
                    }
                });
            })
            .catch(err => console.error("Error fetching friends: ", err));
    }

    // Initial load and periodic refresh
    fetchFriends();
    setInterval(fetchFriends, 10000);
</script>

<?php require_once '../includes/footer.php'; ?>
</body>
</html>