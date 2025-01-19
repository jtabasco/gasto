<?php 
	
	session_start();
	include "../con.php";
    $Usuario=$_POST['Usuario'];
    $Id=$_POST['Id'];
    //$Usuario =$_POST['Usuario'];
    $Rol=$_POST['Rol'];
    $email=$_POST['Email'];
    $Tel=$_POST['Tel'];
    $activo=$_POST['Activo'];
    


    
     	$sql = "UPDATE  usuarios SET 
				nombre='$Usuario', 
				rol_id='$Rol',
                email='$email',
                tel='$Tel',
                activo='$activo'
				where id='$Id'";
    
    
 
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	echo json_encode('ok');
}else{
    echo json_encode('No ok');	
}


 ?>