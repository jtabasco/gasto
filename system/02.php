<?php 
	
	session_start();
	include "../con.php";
    $id=1;
    

	
 


		// Ejeuctar el SQL
		$sql = mysqli_query($conn,"SELECT detalle_gasto.idgasto,usuarios.nombre,usuarios.email,detalle_gasto.monto FROM `detalle_gasto` inner join usuarios WHERE detalle_gasto.deudor=usuarios.id and detalle_gasto.id='$id'");
		$result = mysqli_fetch_array($sql);
		$duedor= $result['nombre'];
		$email= $result['email'];
		$deuda=$result['monto'];
		$idgasto=$result['idgasto'];
        
        //buscamos detalles de la compra
        $sql = mysqli_query($conn,"SELECT Gastos.id,Gastos.detalle,Gastos.Monto,Gastos.fecha,usuarios.nombre as comprador, usuarios.email  FROM `Gastos` inner join usuarios WHERE usuarios.id=Gastos.comprador and Gastos.id='".$idgasto."'");
		$result = mysqli_fetch_array($sql);
		$comprador= $result['comprador'];
		$datalle=$result['detalle'];
		$monto=$result['Monto'];
		$fecha=$result['fecha'];

		// todo listo para el correo

		ini_set( 'display_errors', 1 );
        error_reporting( E_ALL );
        $from = "administrador@jtabasco.com";
        //$to = "jtabasco41@gmail.com";
        $subject = "Actualización de su deuda.";
        //$message = "PHP mail works just fine";
        $message = 'Hola, '.$duedor.chr(13).'Se ha actualizado su pago de la siguiente compra.'.chr(13).chr(10).chr(13).'Compra de: '.$datalle.chr(13).chr(10).'Monto: $'.$monto.chr(13).chr(10).'Fecha: '.$fecha.chr(13).chr(10).chr(13).chr(13).'El valor de su deuda en esa compra ear de $'.$deuda.chr(13).chr(10).chr(13).chr(10).chr(13).chr(13).chr(13).'Atentamente '.chr(13).chr(10).'administrador del sistema.';
        $headers = "From:" . $from;

        // Enviarlo
        $enviado=false;
        if (mail($email,$subject,$message, $headers)){
            $enviado=true;
            };
	
	
	




 ?>