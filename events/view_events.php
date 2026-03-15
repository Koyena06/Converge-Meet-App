<?php

session_start();
include("../config/db.php");

$sql = "SELECT * FROM events ORDER BY event_time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Upcoming Events</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<div class="navbar">

    <div class="logo">Converge</div>

    <div class="nav-links">
        <a href="../user/dashboard.php">Dashboard</a>
        <a href="../user/profile.php">Profile</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

</div>

<div class="dashboard">

<h2>Upcoming Events</h2>

<?php
while($row = $result->fetch_assoc()){
?>

<div class="event-card">

    <h3>Event #<?php echo $row["id"]; ?></h3>

    <p><strong>Place ID:</strong> <?php echo $row["place_id"]; ?></p>

    <p><strong>Time:</strong> <?php echo $row["event_time"]; ?></p>

    <p><?php echo $row["description"]; ?></p>

    <a href="join_event.php?event_id=<?php echo $row['id']; ?>" class="btn btn-primary">
        Join Event
    </a>

</div>

<br>

<?php
}
?>

</div>

</body>
</html>
