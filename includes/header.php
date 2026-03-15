<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
?>

<div class="navbar">

    <div class="logo">
        <a href="/meetup-app/index.php">Converge</a>
    </div>

    <div class="nav-links">

        <?php if(isset($_SESSION["user_id"])) { ?>

            <a href="/meetup-app/user/dashboard.php">Dashboard</a>
            <a href="/meetup-app/user/profile.php">Profile</a>
            <a href="/meetup-app/auth/logout.php">Logout</a>

        <?php } else { ?>

            <a href="/meetup-app/auth/login.php">Login</a>
            <a href="/meetup-app/auth/register.php">Register</a>

        <?php } ?>

    </div>

</div>
