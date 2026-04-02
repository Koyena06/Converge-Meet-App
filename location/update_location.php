<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION["user_id"]) || !isset($_POST['latitude'])) {
    exit();
}

$user_id = $_SESSION["user_id"];
$lat = $_POST['latitude'];
$lng = $_POST['longitude'];

// Update if exists, insert if not (Assuming user_id is UNIQUE or PRIMARY in locations)
$sql = "INSERT INTO locations (user_id, latitude, longitude, updated_at) 
        VALUES (?, ?, ?, NOW()) 
        ON DUPLICATE KEY UPDATE latitude = VALUES(latitude), longitude = VALUES(longitude), updated_at = NOW()";

$stmt = $conn->prepare($sql);
$stmt->bind_param("idd", $user_id, $lat, $lng);
$stmt->execute();
?>