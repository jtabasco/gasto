<?php 
	
	session_start();
	include "../conexion.php";
    $Usuario=$_POST['Usuario'];
    $Id=$_POST['Id'];
    //$Usuario =$_POST['Usuario'];
    $Rol=$_POST['Rol'];
    $email=$_POST['Email'];
    $Tel=$_POST['Tel'];
    $activo=$_POST['Activo'];
    $familia_id = $_POST['Familia'];
    $Compania = $_POST['Compania'] ?? null;
    
    // Si familia_id está vacío, establecerlo como NULL
    $familia_sql = ($familia_id === "" || $familia_id === null) ? "NULL" : "'$familia_id'";
    
    // Si compañía está vacío, establecerlo como NULL
    $compania_sql = ($Compania === "" || $Compania === null) ? "NULL" : "'$Compania'";
    
     	$sql = "UPDATE  usuarios SET 
				nombre='$Usuario', 
				rol_id='$Rol',
                email='$email',
                tel='$Tel',
                compania_telefonica_id=$compania_sql,
                activo='$activo',
                familia_id=$familia_sql
				where id='$Id'";
    
    
 
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	echo json_encode('ok');
}else{
    echo json_encode('No ok');	
}

$conn->close();
?>