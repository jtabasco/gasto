<?php
// Conectar a la base de datos
include "../con.php";
//$con = new mysqli("localhost","root","","fincimex");
// Crear una variable para almacenar los datos
$data_array = array();
// SQL para obtener los datos
$sql = "SELECT usuarios.id, usuarios.nombre,usuarios.tel, rol.rol,usuarios.email,usuarios.activo FROM usuarios INNER JOIN rol  ON usuarios.rol_id = rol.id";
// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla
  //$data_array[] = array($data->org, $data->comercio, $data->moneda, $data->nombre_pv, $data->chapilla, $data->ip, $data->mascara, "<a href='lo.php?".$data->getway."'>".$data->getway."</a>");
  $data_array[] = array('<a  data-bs-toggle="modal" data-bs-target="#editModalPos" onclick=GetDataUser('.$data->id.')'.'>'.$data->nombre.'</a>', $data->rol,$data->email,$data->tel,$data->activo);	
}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($new_array);

?>