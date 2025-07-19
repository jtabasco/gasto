<?php
// Conectar a la base de datos
include "../conexion.php";
//$con = new mysqli("localhost","root","","fincimex");
// Crear una variable para almacenar los datos
$data_array = array();
// SQL para obtener los datos
$sql = "SELECT u.*, f.nombre as familia_nombre, ct.nombre as compania_nombre 
                             FROM usuarios u 
                             LEFT JOIN familia f ON u.familia_id = f.id 
                             LEFT JOIN companias_telefonicas ct ON u.compania_telefonica_id = ct.id
                             ORDER BY u.nombre ASC";
// Ejeuctar el SQL
$query = mysqli_query($conn, $sql);
// Recorrer los resultados
while($data = mysqli_fetch_array($query)){
	// Poner los datos en un array en el orden de los campos de la tabla
  //$data_array[] = array($data->org, $data->comercio, $data->moneda, $data->nombre_pv, $data->chapilla, $data->ip, $data->mascara, "<a href='lo.php?".$data->getway."'>".$data->getway."</a>");
  $id = $data[0];
  $nombre = $data[1];
  $rol_id = $data[3];
  $email = $data[4];
  $activo = $data[5];
  $tel = $data[6];
  $familia_nombre = $data['familia_nombre'] ? $data['familia_nombre'] : 'Sin Familia';
  $compania_nombre = $data['compania_nombre'] ? $data['compania_nombre'] : 'Sin Compañía';

  $query_rol = mysqli_query($conn, "SELECT rol FROM rol WHERE id = $rol_id");
  $rol = mysqli_fetch_row($query_rol)[0];

  $botones = '<div class="text-center">
                    <button class="btn btn-primary btn-sm me-1" onclick="GetDataUser('.$id.')" data-bs-toggle="modal" data-bs-target="#editModalPos" title="Editar Usuario">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-success btn-sm" onclick="testMMS('.$id.')" title="Enviar MMS de Prueba">
                        <i class="bi bi-messenger"></i>
                    </button>
                </div>';

  $data_array[] = array(
    '<a data-bs-toggle="modal" data-bs-target="#editModalPos" onclick="GetDataUser('.$id.')">'.$nombre.'</a>',
    $rol,
    $email,
    $tel,
    $compania_nombre,
    $familia_nombre,
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