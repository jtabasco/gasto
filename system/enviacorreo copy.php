<?php 

	session_start();
	include "../conexion.php";
	require '../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    // Configuración de correo
    $config = include '../config/mail_config.php';

    $id = $_POST['id'];
    $debe = $_POST['debe'];
    $dias = $_POST['dias'];

    // Validar entradas
    if (!isset($id, $debe, $dias)) {
        echo json_encode(false);
        exit;
    }

    // Incluir función helper para compañías
    include 'functions_companias.php';
    
    $sql = mysqli_query($conn, "SELECT tel, nombre, email, compania_telefonica_id FROM usuarios WHERE id='$id'");
    $result = mysqli_fetch_array($sql);
    
    // Verificar si se puede enviar MMS
    $puede_enviar_mms = puedeEnviarMMS($result['compania_telefonica_id'], $conn);
    $info_compania = obtenerInfoCompaniaUsuario($result['compania_telefonica_id'], $conn);
    
    $tel = construirEmailMMS($result['tel'], $result['compania_telefonica_id'], $conn);
    $deudor = $result['nombre'];
    $email = $result['email'];

    // Función para enviar correo usando PHPMailer
    function enviarCorreo($destinatario, $asunto, $mensaje, $from) {
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
            
            // Configuración del remitente - usar la configuración del config en lugar del parámetro
            $mail->setFrom($config['from'], 'Sistema de Gastos');
            
            // Configuración del destinatario
            $mail->addAddress($destinatario);
            
            // Configuración del contenido
            $mail->isHTML(false); // Texto plano
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            
            // Enviar correo
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando correo: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Configuración del correo
    $from = "administrador@jtabasco.com";
    $subject = "Recordatorio de Deuda";
    $message = 'Hola ' . $deudor . ', tiene deudas de $' . $debe . ' por ' . $dias . ' dias. Visita jtabasco.com/gasto';

    // Solo enviar si se puede enviar MMS
    $enviado = false;
    $mensaje_error = '';
    
    if ($puede_enviar_mms && $tel) {
        // Enviar SMS
        $enviado = enviarCorreo($tel, $subject, $message, $from);

        // Enviar correo electrónico si falla
        if (!$enviado) {
            $enviado = enviarCorreo($email, $subject, $message, $from);
        }
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