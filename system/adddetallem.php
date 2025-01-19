<?php 
	
	session_start();
	include "../con.php";
    $deudor=$_POST['deudor'];
    $pagado=$_POST['pago'];
    $fecha=date('Y-m-d');
   

If ($deudor==0){
// 	anadir detalles de gastos
		$sql=	"INSERT INTO tdetalle_gasto (deudor)
  				SELECT id 
  				FROM usuarios WHERE activo='Si' and id not in (".$_SESSION['idUser']. ")";
  		$query = $conn->query($sql);
  		//  Actulaizamos importe por dividir
  		$sql=  "UPDATE tdetalle_gasto SET pagado='$pagado',fecha='$fecha'" ;
		$query = $conn->query($sql);  
					
}else{
		$sql = "INSERT INTO  tdetalle_gasto (deudor,pagado,fecha) 
				VALUES('$deudor','$pagado','$fecha')";
				
		// Ejeuctar el SQL
		$query = $conn->query($sql);
}  				



    


if ($query){
	echo json_encode('ok');
     
}else{
    echo json_encode('No ok');	
}


 ?>