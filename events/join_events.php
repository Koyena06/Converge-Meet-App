<?php

session_start();
include("../config/db.php");

$user_id = $_SESSION["user_id"];
$event_id = $_GET["event_id"];


$check = "SELECT * FROM event_participants 
          WHERE user_id='$user_id' AND event_id='$event_id'";

$result = $conn->query($check);

if($result->num_rows > 0){

    echo "You already joined this event.";

}else{

    $sql = "INSERT INTO event_participants (event_id,user_id)
            VALUES('$event_id','$user_id')";

    if($conn->query($sql)){
        echo "Successfully joined event!";
    }else{
        echo "Error joining event.";
    }
}

echo "<br><a href='view_events.php'>Back</a>";