<?php
    /* main page user will see after being logged-in.
    If session does not exist user will be redirected to the main page*/
    session_start();
    if(!isset($_SESSION["user_id"])){
        header("Location: ../auth/login/php");
        exit();
    }
?>

<h2>Welcome <?php echo $_SESSION["name"]; ?></h2>

<a href="profile.php">Profile</a><br>
<a href="interests.php">Select Interests</a><br>
<a href="../events/create_event.php">Create Event</a><br>
<a href="../events/view_event.php">View Events</a><br>
<a href="../auth/logout.php">Logout</a>