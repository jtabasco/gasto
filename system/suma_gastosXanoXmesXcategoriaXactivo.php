<?php
session_start();
//$cliente=$_SESSION['link'];
// Conectar a la base de datos
include "../conexion.php";
$ano=$_POST['ano'];
$mes=$_POST['mes'];
 
$sql = "SELECT sum(importe) as importe FROM unionXvat where deudor='".$_SESSION['idUser']."'  and month(fecha)='$mes' and year(fecha)='$ano' GROUP by year(fecha),month(fecha)";

//$sql = "SELECT sum(Monto) as importe FROM detalle_gasto where deudor='".$_SESSION['idUser']."' and pagado='Si' and month(fecha)='$mes' and year(fecha)='$ano' GROUP by year(fecha),month(fecha)";
// Ejeuctar el SQL
$query = $conn->query($sql);

$result=mysqli_query($conn,$sql);
$cont=mysqli_num_rows($result);
if ($cont==0){ // no hay registros
    echo json_encode(array("data" => array()));
    exit;
};

// Recorrer los resultados
 	
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla

  //$data_array[] = array($data->id,$data->comprador, $data->detalle, $data->Monto,$data->fecha, $data->duedor, $data->pagado, $data->debe);
  $tot=$data->importe;	
  
}


//require_once "../include/functions.php";
//$con = new mysqli("localhost","root","","fincimex");
// Crear una variable para almacenar los datos
$data_array = array();
// SQL para obtener los datos
$sql="SELECT categoria, sum(importe) as importe FROM unionXvat where  deudor='".$_SESSION['idUser']."' and  year(fecha)='$ano' and month(fecha)='$mes' GROUP by year(fecha),month(fecha),categoria";
//$sql="SELECT categoria.categoria, sum(detalle_gasto.monto) as importe FROM detalle_gasto inner join categoria inner join Gastos where detalle_gasto.idgasto=Gastos.id and detalle_gasto.deudor='".$_SESSION['idUser']."' and detalle_gasto.pagado='Si' and categoria.id=Gastos.idcat and year(detalle_gasto.fecha)='$ano' and month(detalle_gasto.fecha)='$mes' GROUP by year(detalle_gasto.fecha),month(detalle_gasto.fecha),categoria";
//$sql = 'SELECT dist,nombre,org,tipo_cli,cliente,des,Ubi,firma,fecha,CONCAT(left(obs,15),'&'...) as n_obs FROM lista_cliente';

// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla
	//ok
 $data_array[] = array($data->categoria,$data->importe,number_format($data->importe/$tot*100,1)); 
//'<a  data-bs-toggle="modal" data-toggle="tooltip" title="Click para editar" data-bs-target="#ActualizaPago" onclick=GetDataGasto('.$data->id.')'.'>'.$data->duedor.'</a>'
}
// crear un array con el array de los datos, importante que esten dentro de : data
//$new_array  = $data_array;
$new_array  = array("data"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($new_array);
$conn->close();
?>