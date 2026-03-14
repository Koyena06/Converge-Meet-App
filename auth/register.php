<?php
    session_start();
    include("../config/db.php");

    if ($_SERVER["REQUEST_METHOD"]=="POST"){

        $name=$_POST["name"];
        $email=$_POST["email"];
        $password=$_POST["password"];

        $hashed_password = password_hash(password, PASSWORD_DEFAULT); //hashing of passwords- converts a plain password into an encrypted version

        $sql ="INSERT INTO users (name, email, password) VALUES ('$name','$email','$hashed_password')";

        if ($conn->query($sql)==TRUE){
            echo "Registration successfull";
            header("Location: login.php");
        }
        else{
            echo "Error: ".$conn->error;
        }

    }
?>
<form method="POST">

Name:<br>
<input type="text" name="name" required><br>

Email:<br>
<input type="email" name="email" required><br>

Password:<br>
<input type="password" name="password" required><br>

<button type="submit">Register</button>

</form>