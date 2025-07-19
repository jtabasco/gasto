<?php 
	
	
	include "../conexion.php";
  
	
    
//$data_array = array();
//$data_array = [];
// SQL para obtener los datos
$sql= "DELETE  from tdetalle_gasto";
// Ejeuctar el SQL
$query = $conn->query($sql);


 ?>