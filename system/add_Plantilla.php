<?php 
	
	session_start();
	include "../conexion.php";
    $Comprador=$_SESSION['idUser'];
    $Detalle =$_POST['Detalle'];
    $Monto=$_POST['Monto'];
    $Cat=$_POST['Cat'];
    
    


$sql = "INSERT INTO  plantilla_gastos (comprador,detalle,Monto,idcat) 
					VALUES('$Comprador','$Detalle','$Monto','$Cat')";
			
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	//hacemos el resto
	// buscamos el id del gasto

		$sql = mysqli_query($conn,"SELECT MAX(id) as maxid FROM plantilla_gastos");
		$result = mysqli_fetch_array($sql);
		$maxid = $result['maxid'];
		$sql="UPDATE `tdetalle_gasto` SET `idgasto`=".$maxid;
		$query = $conn->query($sql);

	// 	anadir detalles de gastos
		$sql=	"INSERT INTO plantilla_detalle_gasto (idgasto,deudor,monto,pagado)
  				SELECT idgasto,deudor,monto,pagado
  				FROM tdetalle_gasto";
  		$query = $conn->query($sql);

  	//  Actulaizamos importe por dividir
  		// $sql=  "UPDATE detalle_gasto c 
		// 		INNER JOIN (
		// 		    SELECT id, deuda
		// 		    from deudaXcompra
		// 		) x ON c.idgasto = x.id
		// 		SET c.monto=x.deuda";
		// $query = $conn->query($sql);  	

	//borramos tdetalle_gasto
		$sql=  "DELETE FROM tdetalle_gasto";
		$query = $conn->query($sql);		
	// en maxid esta el ultimo gasro añadido
	// buscamos los correos del comprador y deudores
	 
	  
	echo json_encode('ok');
}else{
echo json_encode('No ok');	
}

$conn->close();
?>