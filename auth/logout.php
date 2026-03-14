<?php
    /*helps user to log out*/
    session_start();

    session_destroy(); //to destroy all session variables

    header("Location: ../index.php");//redirects user to homepage
