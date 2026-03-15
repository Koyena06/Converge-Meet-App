<?php
    session_start();
    include("../config/db.php");
    if($_SERVER["REQUEST_METHOD"]=="POST"){
        $email=$_POST["email"];
        $password=$_POST["password"];

        $sql="SELECT * FROM users WHERE email='$email'";
        $result=$conn->query($sql);//result returned from db query

        if($result->num_rows==1){
            $user=$result->fetch_assoc();

            if(password_verify($password,$user["password"])){
                 $_SESSION["user_id"]=$user["id"];
                 $_SESSION["name"]=$user["name"];

                 header("Location: ../user/dashboarsd.php");
            }
            else{
                echo "Incorrect Password";
            }
        }
        else{
                echo "User not found";
        }

    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="../assets/css/style.css">
    </head>

    <body>
        <div class="form-container">

            <h2>Login</h2>

            <form method="POST">

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
    
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
    
                <button type="submit" class="btn btn-primary">Login</button>

            </form>

        </div>

    </body>
</html>
