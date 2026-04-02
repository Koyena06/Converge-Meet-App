<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<div class="navbar">

    <div class="logo">
        <a href="/Converge-Meet-App-main/index.php">Converge</a>
    </div>

    <div class="nav-links">

        <?php if(isset($_SESSION["user_id"])) { ?>

            <a href="/Converge-Meet-App-main/user/dashboard.php">Dashboard</a>
            <a href="/Converge-Meet-App-main/user/profile.php">Profile</a>
            <a href="/Converge-Meet-App-main/user/interests.php">Interests</a>
            <a href="/Converge-Meet-App-main/auth/logout.php">Logout</a>

        <?php } else { ?>

            <a href="/Converge-Meet-App-main/index.php">Home</a>
            <a href="/Converge-Meet-App-main/auth/login.php">Login</a>
            <a href="/Converge-Meet-App-main/auth/register.php">Register</a>

        <?php } ?>

    </div>

</div>