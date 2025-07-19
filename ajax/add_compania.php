<?php 
	
	session_start();
	include "../conexion.php";
    $nombre = $_POST['nombre'];
    $dominio = $_POST['dominio'];
    $activo = $_POST['activo'];

$sql = "INSERT INTO companias_telefonicas (nombre, dominio_mms, activo) 
					VALUES('$nombre','$dominio','$activo')";
			
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	echo json_encode('ok');
}else{
echo json_encode('No ok');	
}

$conn->close();
?> 