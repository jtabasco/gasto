<?php 
	
	session_start();
	include "../conexion.php";
	$plan=$_POST['plan'];
    // $deudor=$_POST['deudor'];
    // $pagado=$_POST['pago'];
    $fecha=date("Y-m-d");

//$data_array = array();
$data_array = [];

// buscamos datos del gasto plan para añadirlo al formulario
  $sql="SELECT * FROM plantilla_gastos where id='$plan'";
  $query = $conn->query($sql);	
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un -array en el orden de los campos de la tabla
	//ok
 $data_array[] = array($data->detalle,$data->Monto,$fecha,$data->idcat); 

}
// borro deudores primero
 $sql=	"DELETE FROM tdetalle_gasto";
  		$query = $conn->query($sql);

//añado deudores
 $sql=	"INSERT INTO tdetalle_gasto (deudor,monto,fecha,pagado)
  				SELECT deudor,monto,now(),pagado
  				FROM plantilla_detalle_gasto where idgasto='$plan'";
  		$query = $conn->query($sql);

// crear un array con el array de los datos, importante que esten dentro de : data
echo json_encode($data_array);
$conn->close();
?>