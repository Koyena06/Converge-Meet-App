<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
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

header('Content-Type: application/json');
echo json_encode($friends);
?>