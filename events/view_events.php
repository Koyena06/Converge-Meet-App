<?php
 /*displays all events for a user*/

    session_start();
    include("../config/db.php");

    $sql = "SELECT * FROM events ORDER BY event_time ASC";
    $result = $conn->query($sql);

    echo "<h2>Upcoming Events</h2>";

    while($row = $result->fetch_assoc()){

        echo "<div>";

        echo "Event ID: " . $row["id"] . "<br>";
        echo "Place ID: " . $row["place_id"] . "<br>";
        echo "Time: " . $row["event_time"] . "<br>";
        echo "Description: " . $row["description"] . "<br>";

        echo "<hr>";

        echo "</div>";
    }
