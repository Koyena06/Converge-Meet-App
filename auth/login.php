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
<form method="POST">

Email:<br>
<input type="email" name="email"><br>

Password:<br>
<input type="password" name="password"><br>

<button type="submit">Login</button>

</form>
