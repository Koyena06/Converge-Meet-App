<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// 1. Fetch all suggested places from Bhubaneswar
$places_result = $conn->query("SELECT * FROM places ORDER BY place_name ASC");

// 2. Fetch User's own location from user_location table
$stmt_user = $conn->prepare("SELECT latitude, longitude FROM user_location WHERE user_id = ? ORDER BY updated_at DESC LIMIT 1");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_loc = $stmt_user->get_result()->fetch_assoc();
$user_json = json_encode($user_loc);

// 3. Fetch Friends' locations (1 is friends with 2,3 | 3 is friends with 2,4)
$friends_sql = "SELECT u.id, u.name, l.latitude, l.longitude 
                FROM users u 
                JOIN user_location l ON u.id = l.user_id 
                JOIN friends f ON (f.sender_id = u.id OR f.receiver_id = u.id)
                WHERE (f.sender_id = ? OR f.receiver_id = ?) 
                AND f.status = 'accepted' 
                AND u.id != ?
                AND l.updated_at = (SELECT MAX(updated_at) FROM user_location WHERE user_id = u.id)";
                
$stmt_f = $conn->prepare($friends_sql);
$stmt_f->bind_param("iii", $user_id, $user_id, $user_id);
$stmt_f->execute();
$friends_data = $stmt_f->get_result()->fetch_all(MYSQLI_ASSOC);
$friends_json = json_encode($friends_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Suggest Places | Converge</title>
    <link rel="stylesheet" href="../style1.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        .mini-map { height: 180px; width: 100%; z-index: 1; border-bottom: 1px solid #eee; }
        .place-card { 
            background: white; 
            border-radius: 15px; 
            overflow: hidden; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            transition: transform 0.3s;
        }
        .place-card:hover { transform: translateY(-5px); }
        .category-badge {
            background: #fff5f5; 
            color: #d63031; 
            padding: 3px 10px; 
            border-radius: 20px; 
            font-size: 0.7rem; 
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php require_once '../includes/header.php'; ?>

<main class="dashboard-page">
    <div class="hero-content" style="text-align: center; margin-bottom: 40px;">
        <h1 class="page-title">Top <em>Spots</em></h1>
        <p class="page-subtitle">Check out these popular hangouts in Bhubaneswar.</p>
    </div>

    <div class="dashboard-grid">
        <?php while($place = $places_result->fetch_assoc()): ?>
            <div class="place-card">
                <div id="map_<?= $place['place_id'] ?>" class="mini-map"></div>

                <div style="padding: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <h3 style="margin: 0; font-size: 1.1rem;"><?= htmlspecialchars($place['place_name']) ?></h3>
                        <span class="category-badge"><?= strtoupper($place['category']) ?></span>
                    </div>
                    
                    <p style="color: #666; font-size: 0.85rem; margin-bottom: 20px;">
                        📍 Located at <?= $place['lat'] ?>, <?= $place['lng'] ?>
                    </p>
                    
                    <a href="../events/create_event.php?place_id=<?= $place['place_id'] ?>" class="btn btn-primary" style="display: block; text-align: center; font-size: 0.85rem; text-decoration: none;">
                        Plan Event Here
                    </a>
                </div>

                <script>
                    (function() {
                        var pLat = <?= $place['lat'] ?>;
                        var pLng = <?= $place['lng'] ?>;
                        var userLoc = <?= $user_json ?>;
                        var friends = <?= $friends_json ?>;

                        // Init Map centered on the suggested place
                        var m = L.map('map_<?= $place['place_id'] ?>', { zoomControl: false }).setView([pLat, pLng], 14);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(m);

                        // 1. Suggested Place (Gold Star/Marker)
                        L.circleMarker([pLat, pLng], {
                            radius: 10, color: '#e1b12c', fillColor: '#f1c40f', fillOpacity: 0.8
                        }).addTo(m).bindPopup("<b><?= $place['place_name'] ?></b>");

                        // 2. User Location (Blue Dot)
                        if(userLoc) {
                            L.circleMarker([userLoc.latitude, userLoc.longitude], {
                                radius: 6, color: '#0984e3', fillColor: '#3498db', fillOpacity: 1
                            }).addTo(m).bindPopup("You");
                        }

                        // 3. Friends Locations (Red Dots)
                        friends.forEach(function(f) {
                            L.circleMarker([f.latitude, f.longitude], {
                                radius: 5, color: '#d63031', fillColor: '#e74c3c', fillOpacity: 1
                            }).addTo(m).bindPopup(f.name);
                        });
                    })();
                </script>
            </div>
        <?php endwhile; ?>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<?php require_once '../includes/footer.php'; ?>

</body>
</html>