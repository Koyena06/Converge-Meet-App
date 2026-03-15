<?php
    /* displays the details of the user who is already logged-in */

    session_start();
    include("../config/db.php");

    $user_id=$_SESSION["user_id"];

    $sql="SELECT * FROM users WHERE id='$user_id'";
    $result=$conn->query($sql);

    $user=$result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="navbar">

        <div class="logo">Converge</div>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="interests.php">Interests</a>
            <a href="../auth/logout.php">Logout</a>
        </div>

    </div>

    <div class="profile">

        <h2>User Profile</h2>

        <p><strong>Name:</strong> <?php echo $user["name"]; ?></p>

        <p><strong>Email:</strong> <?php echo $user["email"]; ?></p>

        <br>

        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

    </div>

</body>
</html>
