<?php 
	
	session_start();
	include "../conexion.php";
	require '../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    
    $deudor =$_POST['deudor'];
    $debe=$_POST['debe'];
    
    // Configuración de correo
    $config = include '../config/mail_config.php';
    
    // Incluir función helper para compañías
    include '../system/functions_companias.php';

    // Función para enviar MMS usando PHPMailer
    function enviarMMS($destinatario, $asunto, $mensaje) {
        global $config;
        
        $mail = new PHPMailer(true);
        
        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];
            $mail->CharSet = 'UTF-8';
            
            // Configuración del remitente
            $mail->setFrom($config['from'], 'Sistema de Gastos');
            
            // Configuración del destinatario
            $mail->addAddress($destinatario);
            
            // Configuración del contenido
            $mail->isHTML(false); // SMS es texto plano
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            
            // Enviar correo
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando MMS: " . $mail->ErrorInfo);
            return false;
        }
    }

		$sql = mysqli_query($conn,"SELECT tel, compania_telefonica_id FROM `usuarios` WHERE nombre='$deudor'");
		$result = mysqli_fetch_array($sql);
		
		// Verificar si se puede enviar MMS
		$puede_enviar_mms = puedeEnviarMMS($result['compania_telefonica_id'], $conn);
		$info_compania = obtenerInfoCompaniaUsuario($result['compania_telefonica_id'], $conn);
		
		$tel = construirEmailMMS($result['tel'], $result['compania_telefonica_id'], $conn);
		
		// Solo enviar si se puede enviar MMS
		$enviado = false;
		$mensaje_error = '';
		
		if ($puede_enviar_mms && $tel) {
			$subject = "Recordatorio de Deuda";
			$message = 'Hola '.$deudor.', tiene deudas pendientes de pago por un monto total de $'.$debe.'  Visita jtabasco.com/gasto para detalles.';

			// Enviar usando PHPMailer
			$enviado = enviarMMS($tel, $subject, $message);
		} else {
			$compania_nombre = $info_compania ? $info_compania['nombre'] : 'No asignada';
			$mensaje_error = "No se puede enviar MMS - Compañía '$compania_nombre' no está activa";
		}
		
		$response = [
			'success' => $enviado,
			'message' => $enviado ? 'Mensaje enviado correctamente' : $mensaje_error,
			'compania_info' => [
				'puede_enviar_mms' => $puede_enviar_mms,
				'compania_nombre' => $info_compania ? $info_compania['nombre'] : 'No asignada',
				'compania_activa' => $info_compania ? ($info_compania['activo'] === 'Si') : false
			]
		];
		
		echo json_encode($response);
      $conn->close();
 ?>