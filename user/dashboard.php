<?php
    /* main page user will see after being logged-in.
    If session does not exist user will be redirected to the main page*/
    session_start();
    if(!isset($_SESSION["user_id"])){
        header("Location: ../auth/login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Dashboard</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
    
        <div class="navbar">
        
            <div class="logo">Converge</div>
            
                <div class="nav-links">
                    <a href="profile.php">Profile</a>
                    <a href="interests.php">Interests</a>
                    <a href="../auth/logout.php">Logout</a>
                </div>
        </div>
                
                
            <div class="dashboard">
            
                <h2>Welcome <?php echo $_SESSION["name"]; ?></h2>
                
                <div class="card-container">
                
                    <div class="card">
                        <h3>Profile</h3>
                        <p>View and update your personal information.</p>
                        <a href="profile.php" class="btn btn-primary">Open</a>
                    </div>
                
                    <div class="card">
                        <h3>Select Interests</h3>
                        <p>Choose interests to find friends with similar hobbies.</p>
                        <a href="interests.php" class="btn btn-primary">Select</a>
                    </div>
                
                    <div class="card">
                        <h3>Create Event</h3>
                        <p>Plan a meetup event with your friends.</p>
                        <a href="../events/create_event.php" class="btn btn-primary">Create</a>
                    </div>
                
                    <div class="card">
                        <h3>View Events</h3>
                        <p>See upcoming meetups and join events.</p>
                        <a href="../events/view_event.php" class="btn btn-primary">View</a>
                    </div>
                </div>
        
            </div>
    
    </body>
</html>
