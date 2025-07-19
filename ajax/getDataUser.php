<?php 
	
	session_start();
	include "../conexion.php";
    $id =$_POST['id'];
	
    
//$data_array = array();
$data_array = [];
// SQL para obtener los datos
$sql = "SELECT u.*, f.nombre as familia_nombre, ct.nombre as compania_nombre, ct.dominio_mms
        FROM usuarios u 
        LEFT JOIN familia f ON u.familia_id = f.id 
        LEFT JOIN companias_telefonicas ct ON u.compania_telefonica_id = ct.id
        WHERE u.id = $id";
// Ejeuctar el SQL
$query = mysqli_query($conn, $sql);
// Recorrer los resultados
while($row = mysqli_fetch_array($query)){
	// Poner los datos en un array en el orden de los campos de la tabla

  $data_array[] = array(
    $row[0], // id
    $row[1], // nombre
    $row[2], // password
    $row[3], // rol_id
    $row[4], // email
    $row[5], // activo
    $row[6], // tel
    $row['familia_id'], // familia_id
    $row['familia_nombre'], // familia_nombre
    $row['compania_telefonica_id'], // compania_id
    $row['compania_nombre'] // compania_nombre
  );	
  
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("datos"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($data_array);
$conn->close();
?>