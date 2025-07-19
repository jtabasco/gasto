<?php 
	
	session_start();
	include "../conexion.php";
    $id =$_POST['id'];
	
    
//$data_array = array();
$data_array = [];
// SQL para obtener los datos
$sql = "SELECT * FROM deudas_detalladas where id='$id'";
// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla

  $data_array[] = array($data->id,"Compra pagada por ". $data->comprador, $data->detalle, $data->Monto,$data->fecha, $data->duedor, $data->pagado, $data->debe);	
  
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("datos"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($data_array);
  

 ?>