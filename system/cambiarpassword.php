<?php 
    include "../con.php";  
    $email =$_POST['email'];
    $p1 =$_POST['p1'];
    
    $p1=md5($p1);
    
    $conn->query("update usuarios set pass='$p1' where email='$email' ")or die($conn->error);
    echo "ok";
?>