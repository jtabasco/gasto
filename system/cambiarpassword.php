<?php 
    include "../conexion.php";  
    $email = $_POST['email'];
    $p1 = $_POST['p1'];
    
    $p1 = md5($p1);
    
    $conn->query("UPDATE usuarios SET pass='$p1' WHERE email='$email'") or die($conn->error);
    echo "ok";
    $conn->close();
?>