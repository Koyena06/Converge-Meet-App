
<?php
    $host="localhost";
    $user="root";
    $password="";
    $database="meetup_db";

    //create connection 
    $conn=new mysqli($host, $user, $password, $database);

    //check connection
    if($conn->connect_error){
        die("Database Connection Failed: ". $conn->connect_error);
    }
    //database connection complete
?>
