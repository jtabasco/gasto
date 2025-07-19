<?php 
	
	session_start();
	include "../conexion.php";
  
	// Obtener el tipo de modal (préstamo o compra)
	$tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'compra';
    
//$data_array = array();
//$data_array = [];
// SQL para obtener los datos
$sql = "SELECT id,nombre FROM filtro_usuarios WHERE (id not in (".$_SESSION['idUser'].") and familiaid = ".$_SESSION['familia'].")";
$sql1= "SELECT * from tdetalle_gasto";
// Ejeuctar el SQL
$query = $conn->query($sql);

$result=mysqli_query($conn,$sql1);
$cont=mysqli_num_rows($result);
echo '<option default value=-1>Seleccione Deudor</option>';

// Solo mostrar "Todos" si es compra y no hay registros en tdetalle_gasto
if ($cont==0 && $tipo == 'compra'){ // no hay registros y es compra
	echo '<option value=0>Todos</option>';	
};

// Recorrer los resultados
 //echo '<option value=0>Todos</option>';	
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla

  //$data_array[] = array($data->id,$data->comprador, $data->detalle, $data->Monto,$data->fecha, $data->duedor, $data->pagado, $data->debe);
  echo "<option value=".$data->id.">".$data->nombre."</option>";	
  
}
// crear un array con el array de los datos, importante que esten dentro de : data


 ?>