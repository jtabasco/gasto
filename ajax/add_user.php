<?php 
	
	session_start();
	include "../conexion.php";
    $Usuario=$_POST['Usuario'];
    $Rol =$_POST['Rol'];
    $Email=$_POST['Email'];
    $Activo=$_POST['Activo'];
    $Tel=$_POST['Tel'];
    $Compania = $_POST['Compania'] ?? null;

    // Si compañía está vacío, establecerlo como NULL
    $compania_sql = ($Compania === "" || $Compania === null) ? "NULL" : "'$Compania'";

$sql = "INSERT INTO  usuarios (nombre,rol_id,email,tel,compania_telefonica_id,activo) 
					VALUES('$Usuario','$Rol','$Email','$Tel',$compania_sql,'$Activo')";
			
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	echo json_encode('ok');
}else{
echo json_encode('No ok');	
}

$conn->close();
?>