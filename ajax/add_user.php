<?php 
	
	session_start();
	include "../con.php";
    $Usuario=$_POST['Usuario'];
    $Rol =$_POST['Rol'];
    $Email=$_POST['Email'];
    $Activo=$_POST['Activo'];
    $Tel=$_POST['Tel'];

    


$sql = "INSERT INTO  usuarios (nombre,rol_id,email,tel,activo) 
					VALUES('$Usuario','$Rol','$Email','$Tel','$Activo')";
			
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	echo json_encode('ok');
}else{
echo json_encode('No ok');	
}


 ?>