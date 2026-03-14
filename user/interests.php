<?php

    /* allows users to select interests */
    session_start();
    include("../config/db.php");

    $user_id=$_SESSION["user_id"];

    //user entered interest
    if(isset($_POST["interest"])){
        $interest_id=$_POST["interest"];

        $sql="INSERT INTO user_interests (user_id, interest_id) VALUES('$user_id','$interest_id')";

        $conn->query($sql);

        echo "Interest saved.";
    }
    //fetching all interests
    $result=$conn->query("SELECT * FROM interests");
?> 

<form method="POST">

<select name="interest">
<?php
    while($row=$result->fetch_assoc()){
        echo "<option value='".$row['id']."'>".$row['interest_name']."</option>";
    }
?>
</select>
<button type="submit">Add Interest</button>

</form>

