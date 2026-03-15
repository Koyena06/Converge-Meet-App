<?php
    session_start();
    include("../config/db.php");

    if ($_SERVER["REQUEST_METHOD"]=="POST"){

        $name=$_POST["name"];
        $email=$_POST["email"];
        $password=$_POST["password"];

        $hashed_password = password_hash($password, PASSWORD_DEFAULT); //hashing of passwords- converts a plain password into an encrypted version

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
<!DOCTYPE html>
<html>
    <head>
        <title>Register</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
    
        <div class="form-container">
        
            <h2>Create Account</h2>
            
            <form method="POST">
                
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
                
            </form>
            
            <p style="margin-top:20px;text-align:center;">
                Already have an account?
                <a href="login.php">Login</a>
            </p>
        
        </div>
    
    </body>
</html>
