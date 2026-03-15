<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>
            Converge
        </title>
        <link rel="stylesheet" href="assets/css/style.css">

    </head>
    <body>
        <?php include("includes/header.php"); ?>
        <div class="hero">

            <h1>Find Friends With Similar Interests</h1>

            <p>
                Discover people who share your hobbies, explore new places,
                and plan exciting meetups together.
            </p>

            <a href="auth/register.php" class="btn btn-primary">Get Started</a>

            <a href="auth/login.php" class="btn btn-secondary">Login</a>

        </div>
        <?php include("includes/footer.php"); ?>

    </body>
</html>
