<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$place_id = isset($_GET['place_id']) ? (int) $_GET['place_id'] : 0;

if ($place_id <= 0) {
    die("Invalid place ID.");
}

// Fetch place details
$place_query = "SELECT * FROM places WHERE id = ?";
$stmt = mysqli_prepare($conn, $place_query);
mysqli_stmt_bind_param($stmt, "i", $place_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$place  = mysqli_fetch_assoc($result);

if (!$place) {
    mysqli_close($conn);
    die("Place not found.");
}

// Fetch upcoming events at this place
$events_query = "
    SELECT e.id, e.description, e.event_time, u.name AS organizer
    FROM events e
    JOIN users u ON e.creator_id = u.id
    WHERE e.place_id = ? AND e.event_time >= NOW()
    ORDER BY e.event_time ASC
";
$stmt2 = mysqli_prepare($conn, $events_query);
mysqli_stmt_bind_param($stmt2, "i", $place_id);
mysqli_stmt_execute($stmt2);
$events_result = mysqli_stmt_get_result($stmt2);
$events        = mysqli_fetch_all($events_result, MYSQLI_ASSOC);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($place['place_name']) ?> – Details</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #mini-map { height: 300px; width: 100%; margin-top: 12px; border-radius: 8px; }
    </style>
</head>
<body>
    <h2><?= htmlspecialchars($place['place_name']) ?></h2>
    <p><strong>Category:</strong> <?= htmlspecialchars($place['category']) ?></p>
    <p><strong>Coordinates:</strong> <?= $place['lat'] ?>, <?= $place['lng'] ?></p>

    <!-- Mini Map -->
    <div id="mini-map"></div>

    <hr>
    <h3>Upcoming Events Here</h3>

    <?php if (empty($events)): ?>
        <p>No upcoming events at this place.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($events as $event): ?>
                <li>
                    <strong><?= htmlspecialchars($event['description']) ?></strong><br>
                    📅 <?= htmlspecialchars($event['event_time']) ?><br>
                    👤 Organised by <?= htmlspecialchars($event['organizer']) ?><br>
                    <a href="../events/join_event.php?event_id=<?= $event['id'] ?>">
                        <button>Join Event</button>
                    </a>
                </li>
                <br>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <br>
    <a href="suggest_places.php">← Back to Suggestions</a>
    &nbsp;|&nbsp;
    <a href="../events/create_event.php?place_id=<?= $place['id'] ?>">
        ➕ Create Event Here
    </a>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const lat = <?= (float) $place['lat'] ?>;
        const lng = <?= (float) $place['lng'] ?>;
        const name = <?= json_encode($place['place_name']) ?>;

        const map = L.map('mini-map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng])
            .addTo(map)
            .bindPopup(`<b>${name}</b>`)
            .openPopup();
    </script>
</body>
</html>
