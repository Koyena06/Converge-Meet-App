<?php

    /*allows user to create meetup events*/

    session_start();
    include("../config/db.php");

    $user_id = $_SESSION["user_id"];
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $place_id=$_POST["place"];
        $event_time=$_POST["time"];
        $description=$_POST["description"];

        $sql = "INSERT INTO events (creator_id, place_id,event_time,description) VALUES('$user_id', '$place_id','$event_time','$description')";
        $conn->query($sql);

        echo "Event created!";
    }
?>
<form method="POST">

    Place ID:<br>
    <input type="number" name="place"><br>

    Event Time:<br>
    <input type="datetime-local" name="time"><br>

    Description:<br>
    <textarea name="description"></textarea><br>

    <button type="submit">Create Event</button>
</form>