<?php 
	
	session_start();
	include "../con.php";
  
	
    
//$data_array = array();
//$data_array = [];
// SQL para obtener los datos
$sql = "SELECT id,categoria FROM categoria ";
$sql1= "SELECT * from tdetalle_gasto";
// Ejeuctar el SQL
$query = $conn->query($sql);

$result=mysqli_query($conn,$sql);
$cont=mysqli_num_rows($result);
if ($cont==0){ // no hay regitos lleva el total
	echo '<option value=0>Cree plantillas</option>';
	}else{
	echo '<option value=0>Seleccione categoria</option>';	
};

// Recorrer los resultados
 	
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla

  //$data_array[] = array($data->id,$data->comprador, $data->detalle, $data->Monto,$data->fecha, $data->duedor, $data->pagado, $data->debe);
  echo "<option value=".$data->id.">".$data->categoria."</option>";	
  
}
// crear un array con el array de los datos, importante que esten dentro de : data


 ?>