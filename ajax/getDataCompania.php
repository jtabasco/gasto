<?php 
	
	session_start();
	include "../conexion.php";
    $id = $_POST['id'];
	
$data_array = [];
// SQL para obtener los datos
$sql = "SELECT * FROM companias_telefonicas WHERE id = $id";
// Ejeuctar el SQL
$query = mysqli_query($conn, $sql);
// Recorrer los resultados
while($row = mysqli_fetch_array($query)){
	// Poner los datos en un array en el orden de los campos de la tabla

  $data_array[] = array(
    $row[0], // id
    $row[1], // nombre
    $row[2], // dominio_mms
    $row[3], // activo
    $row[4]  // fecha_creacion
  );	
  
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("datos"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($data_array);
$conn->close();
?> 