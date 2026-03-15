<?php
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
<!DOCTYPE html>
    <html>
        <head>
        <title>Create Event</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    
    <body>
    
        <?php include("../includes/header.php"); ?>
        
        <div class="form-container">
        
            <h2>Create Meetup Event</h2>
            
            <?php
            if(isset($message)){
                echo "<p>".$message."</p>";
            }
            ?>
            
            <form method="POST">
                
                <div class="form-group">
                    <label>Place ID</label>
                    <input type="number" name="place" required>
                </div>
                
                <div class="form-group">
                    <label>Event Time</label>
                    <input type="datetime-local" name="time" required>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Create Event</button>
            
            </form>
        
        </div>
        
        <?php include("../includes/footer.php"); ?>
    
    </body>
</html>
