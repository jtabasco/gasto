<?php
// Conectar a la base de datos
include "../conexion.php";
//require_once "../include/functions.php";
//$con = new mysqli("localhost","root","","fincimex");
// Crear una variable para almacenar los datos
$data_array = array();
//$ano=$_POST['ano'];
//$mes=$_POST['mes'];

// SQL para obtener los datos
//$sql = 'SELECT dist,nombre,org,tipo_cli,cliente,des,Ubi,firma,fecha,CONCAT(left(obs,15),'&'...) as n_obs FROM lista_cliente';
$sql = "SELECT * FROM `deudas`";
// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla
	//ok
 $data_array[] = array($data->duedor,$data->Deuda); 

}
// crear un array con el array de los datos, importante que esten dentro de : data
//$new_array  = $data_array;
$new_array  = array("data"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($new_array);
$conn->close();
?>