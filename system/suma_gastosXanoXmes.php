<?php
session_start();
//$cliente=$_SESSION['link'];
// Conectar a la base de datos
include "../con.php";
$ano=$_POST['ano'];
$mes=$_POST['mes'];
//require_once "../include/functions.php";
//$con = new mysqli("localhost","root","","fincimex");
// Crear una variable para almacenar los datos
$data_array = array();
// SQL para obtener los datos
//$sql = 'SELECT dist,nombre,org,tipo_cli,cliente,des,Ubi,firma,fecha,CONCAT(left(obs,15),'&'...) as n_obs FROM lista_cliente';
$sql = "SELECT usuarios.nombre, sum(Monto) as importe FROM `Gastos` inner join usuarios where usuarios.id=Gastos.comprador and year(fecha)='$ano' and month(fecha)='$mes' GROUP by year(fecha),month(fecha),comprador";
$sqlcat = "SELECT categoria.categoria, sum(Monto) as importe FROM `Gastos` inner join categoria where categoria.id=Gastos.idcat and year(fecha)='$ano' and month(fecha)='$mes' GROUP by year(fecha),month(fecha),categoria";
// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla
	//ok
 $data_array[] = array($data->nombre,$data->importe); 
//'<a  data-bs-toggle="modal" data-toggle="tooltip" title="Click para editar" data-bs-target="#ActualizaPago" onclick=GetDataGasto('.$data->id.')'.'>'.$data->duedor.'</a>'
}
// crear un array con el array de los datos, importante que esten dentro de : data
//$new_array  = $data_array;
$new_array  = array("data"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($new_array);
?>