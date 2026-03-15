<?php

    /* allows users to select interests */
    session_start();
    include("../config/db.php");

    $user_id=$_SESSION["user_id"];

    //user entered interest
    if(isset($_POST["interest"])){
        $interest_id=$_POST["interest"];

        $sql="INSERT INTO user_interests (user_id, interest_id) VALUES('$user_id','$interest_id')";

        $conn->query($sql);

        echo "Interest saved.";
    }
    //fetching all interests
    $result=$conn->query("SELECT * FROM interests");
?> 

<!DOCTYPE html>
<html>
    <head>
        <title>Select Interests</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>
    
    <body>
    
        <div class="navbar">
        
            <div class="logo">Converge</div>
        
            <div class="nav-links">
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="../auth/logout.php">Logout</a>
            </div>
        
        </div>
        
        
        <div class="form-container">
        
            <h2>Select Your Interests</h2>
        
            <?php
            if(isset($message)){
                echo "<p class='success'>".$message."</p>";
            }
            ?>
        
            <form method="POST">

    <div class="form-group">

        <label>Choose Interest</label>

        <select name="interest">

            <?php
            while($row=$result->fetch_assoc()){
                echo "<option value='".$row['id']."'>".$row['interest_name']."</option>";
            }
            ?>

        </select>

    </div>

    <button type="submit" class="btn btn-primary">Add Interest</button>

</form>


<h3>Your Interests</h3>

<div class="interests">

<?php
$my = $conn->query("
SELECT interest_name 
FROM interests
JOIN user_interests 
ON interests.id = user_interests.interest_id
WHERE user_interests.user_id = '$user_id'
");

while($i = $my->fetch_assoc()){
    echo "<span class='interest-tag'>".$i["interest_name"]."</span>";
}
?>

</div>
    
    </body>
</html>

