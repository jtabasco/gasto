<?php 
	
	session_start();
	include "../conexion.php";
    $id =$_POST['id'];
	
    
//$data_array = array();
$data_array = [];
// SQL para obtener los datos
$sql = "DELETE FROM tdetalle_gasto where deudor='$id'";
// Ejeuctar el SQL
$query = $conn->query($sql);
  

 ?>