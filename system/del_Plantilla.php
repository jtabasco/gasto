<?php 
	
	session_start();
	include "../con.php";
    $idplan=$_POST['idplan'];
    
    
    


$sql = "DELETE from plantilla_gastos WHERE id='$idplan'";
			
// Ejeuctar el SQL
$query = $conn->query($sql);



 ?>