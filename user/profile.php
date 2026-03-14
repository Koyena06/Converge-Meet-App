<?php
    /* displays the details of the user who is already logged-in */

    session_start();
    include("../config/db.php");

    $user_id=$_SESSION["user_id"];

    $sql="SELECT * FROM users WHERE id='$user_id'";
    $result=$conn->query($sql);

    $user=$result->fetch_assoc();
?>

<h2>User Profile</h2>

Name: <?php echo $user["name"]; ?><br>
Email: <?php echo $user["email"];?><br>
