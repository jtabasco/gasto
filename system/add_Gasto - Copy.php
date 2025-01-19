<?php 
	
	session_start();
	include "../con.php";
	require '../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    $Comprador=$_SESSION['idUser'];
    $Detalle =$_POST['Detalle'];
    $Monto=$_POST['Monto'];
    $Fecha=$_POST['Fecha'];
    $Cat=$_POST['Cat'];
    


$sql = "INSERT INTO  Gastos (comprador,detalle,Monto,fecha,idcat) 
					VALUES('$Comprador','$Detalle','$Monto','$Fecha','$Cat')";
			
// Ejeuctar el SQL
$query = $conn->query($sql);

if ($query){
	//hacemos el resto
	// buscamos el id del gasto

		$sql = mysqli_query($conn,"SELECT MAX(id) as maxid FROM `Gastos` WHERE 1");
		$result = mysqli_fetch_array($sql);
		$maxid = $result['maxid'];
		$sql="UPDATE `tdetalle_gasto` SET `idgasto`=".$maxid;
		$query = $conn->query($sql);

	// 	anadir detalles de gastos
		$sql=	"INSERT INTO detalle_gasto (idgasto,deudor,monto,fecha,pagado)
  				SELECT idgasto,deudor,monto,fecha,pagado
  				FROM tdetalle_gasto";
  		$query = $conn->query($sql);

  	//  Actulaizamos importe por dividir
  		$sql=  "UPDATE detalle_gasto c 
				INNER JOIN (
				    SELECT id, deuda
				    from deudaXcompra
				) x ON c.idgasto = x.id
				SET c.monto=x.deuda";
		$query = $conn->query($sql);  	

	//borramos tdetalle_gasto
		$sql=  "DELETE FROM tdetalle_gasto";
		$query = $conn->query($sql);		
	// en maxid esta el ultimo gasro añadido
	// buscamos los correos del comprador y deudores
	 
	   $sql = mysqli_query($conn,"SELECT Gastos.id,Gastos.detalle,Gastos.Monto,Gastos.fecha,usuarios.nombre as comprador, usuarios.email  FROM `Gastos` inner join usuarios WHERE usuarios.id=Gastos.comprador and Gastos.id='".$maxid."'");
		$result = mysqli_fetch_array($sql);
		$comprador= $result['comprador'];
		$compradoremail= $result['email'];
		$datalle=$result['detalle'];
		$monto=$result['Monto'];
		$fecha=$result['fecha'];
		

			


		//$sql = mysqli_query($conn,"SELECT detalle_gasto.idgasto,detalle_gasto.monto,usuarios.nombre as deudor, usuarios.email  FROM detalle_gasto inner join usuarios WHERE usuarios.id=detalle_gasto.deudor and detalle_gasto.idgasto='".$maxid."'");


		$sql = "SELECT detalle_gasto.idgasto,detalle_gasto.monto,usuarios.nombre as deudor, usuarios.email,usuarios.tel  FROM detalle_gasto inner join usuarios WHERE usuarios.id=detalle_gasto.deudor and detalle_gasto.idgasto='".$maxid."'";

		// Ejeuctar el SQL
		$query = $conn->query($sql);

		$to="";
		$deudores="";
		// Recorrer los resultados
		 //echo '<option value=0>Todos</option>';	
		while($data = $query->fetch_object()){
			// Poner los datos en un array en el orden de los campos de la tabla

		  $to=$to.$data->email.",";
		  $deudores=$deudores.$data->deudor.", ";
		  $deuda=$data->monto;
		  $tel=$data->tel.'@tmomail.net';

		  // buscamos las deudas de cada deudor pra informarselo
	 
	   	$sqlo = mysqli_query($conn,"SELECT * FROM `deudas` WHERE duedor='".$data->deudor."'");
		$resultado = mysqli_fetch_array($sqlo);
		$tdeuda= $resultado['Deuda'];

		  // enviamos un correo paara que se vea como sms
		  	ini_set( 'display_errors', 1 );
        	error_reporting( E_ALL );
        	$from = "administrador@jtabasco.com";
        	$subject = "Notificacion de deuda nueva.";
            $message = 'Hola '.$data->deudor.', se ha registrado una compra con valor de $'.$monto.' en la cual tienes una de deuda nueva de $'.$deuda.' Total de Deudas $'.$tdeuda.' Visita jtabasco.com/gasto para detalles.';
        	$headers = "From:" . $from;

        	// Enviarlo
        	$enviado=false;
       		 if (mail($tel,$subject,$message, $headers)){
            	$enviado=true;
            };	

		  
		}
		// quito los dos ultimos caracteres de deudores y añado un .
		$deudores=substr($deudores, 0, -2).".";
		$to=substr($to, 0, -1);

		// todo listo para el correo

		// correo al comprador
		ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = "administrador@jtabasco.com";
        //$to = "jtabasco41@gmail.com";
        $subject = "Confirmación de compra registrada..";
        //$message = "PHP mail works just fine";
        $message = 'Hola, '.$comprador.chr(13).'Se ha registrado su compra con los detalles a continuacion.'.chr(13).chr(10).chr(13).'Compra de: '.$datalle.chr(13).chr(10).'Monto: $'.$monto.chr(13).chr(10).'Fecha: '.$fecha.chr(13).chr(10).chr(13).chr(13).'Repartido entre los siguientes deudores a cargos iguales de $'.$deuda.chr(13).chr(10).$deudores.chr(13).chr(10).chr(13).chr(13).chr(13).'Atentamente '.chr(13).chr(10).'administrador del sistema.';
        $headers = "From:" . $from;

        // Enviarlo
        $enviado=false;
        if (mail($compradoremail,$subject,$message, $headers)){
            $enviado=true;
            };

        // correo a los deudores
        ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = "administrador@jtabasco.com";
        //$to = "jtabasco41@gmail.com";
        $subject = "Notificación de deuda nueva.";
        //$message = "PHP mail works just fine";
        $message = 'Hola, '.$deudores.chr(13).'Se ha registrado una compra con los detalles a continuacion.'.chr(13).chr(10).chr(13).'Comprador:'.$comprador.chr(13).chr(10).'Compra de: '.$datalle.chr(13).chr(10).'Monto: $'.$monto.chr(13).chr(10).'Fecha: '.$fecha.chr(13).chr(10).chr(13).chr(13).'Repartida entre '.$deudores.' y '.$comprador.chr(13).chr(10).'Su deuda en esa compra es de $'.$deuda.chr(13).chr(10).chr(13).chr(10).'Visite jtabasco.com/gasto '.chr(13).chr(13).chr(13).'Atentamente '.chr(13).chr(10).'administrador del sistema.';
        $headers = "From:" . $from;

        // Enviarlo
        $enviado=false;
        if (mail($to,$subject,$message, $headers)){
            $enviado=true;
            };






		

	echo json_encode('ok');
}else{
echo json_encode('No ok');	
}


 ?>