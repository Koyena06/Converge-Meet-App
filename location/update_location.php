<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION["user_id"])) {
    http_response_code(403);
    exit();
}

$user_id = $_SESSION["user_id"];
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];

$sql = "INSERT INTO locations (user_id, latitude, longitude, timestamp) VALUES ('$user_id', '$latitude', '$longitude', NOW())";
$conn->query($sql);
?>
