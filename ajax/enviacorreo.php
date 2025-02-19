<?php 
	
	session_start();
	include "../con.php";
	require '../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    
    $deudor =$_POST['deudor'];
    $debe=$_POST['debe'];
    


		$sql = mysqli_query($conn,"SELECT tel FROM `usuarios` WHERE nombre='$duedor'");
		$result = mysqli_fetch_array($sql);
		$tel = $result['tel'].'@tmomail.net';
		
		  // enviamos un correo paara que se vea como sms
		  	ini_set( 'display_errors', 1 );
        	error_reporting( E_ALL );
        	$from = "administrador@jtabasco.com";
        	$subject = "Recordatorio de Deuda";
            $message = 'Hola '.$deudor.', tiene deudas pendientes de pago por un monto total de $'.$debe.'  Visita jtabasco.com/gasto para detalles.';
        	$headers = "From:" . $from;

        	// Enviarlo
        	$enviado=false;
       		 if (mail($tel,$subject,$message, $headers)){
            	$enviado=true;
            };	
         
      echo json_encode($enviado);
 ?>