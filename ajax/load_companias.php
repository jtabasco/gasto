<?php
// Conectar a la base de datos
include "../conexion.php";
// Crear una variable para almacenar los datos
$data_array = array();
// SQL para obtener los datos
$sql = "SELECT * FROM companias_telefonicas ORDER BY nombre ASC";
// Ejeuctar el SQL
$query = mysqli_query($conn, $sql);
// Recorrer los resultados
while($data = mysqli_fetch_array($query)){
	// Poner los datos en un array en el orden de los campos de la tabla
  $id = $data[0];
  $nombre = $data[1];
  $dominio = $data[2];
  $activo = $data[3];

  $botones = '<div class="text-center">
                    <button class="btn btn-primary btn-sm me-1" onclick="GetDataCompania('.$id.')" data-bs-toggle="modal" data-bs-target="#editCompania" title="Editar Compañía">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>';

  $data_array[] = array(
    '<a data-bs-toggle="modal" data-bs-target="#editCompania" onclick="GetDataCompania('.$id.')">'.$nombre.'</a>',
    $dominio,
    $activo,
    $botones
  );	
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($new_array);
$conn->close();
?> 