<?php 
	
	session_start();
	include "../conexion.php";
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $dominio = $_POST['dominio'];
    $activo = $_POST['activo'];
    
    $sql = "UPDATE companias_telefonicas SET 
				nombre='$nombre', 
				dominio_mms='$dominio',
                activo='$activo'
				where id='$id'";
    
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	echo json_encode('ok');
}else{
    echo json_encode('No ok');	
}

$conn->close();
?> 