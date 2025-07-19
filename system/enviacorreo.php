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
    $tipo_envio = $_POST['tipo_envio'] ?? 'mms'; // 'sms' o 'mms'
    $imagen_path = $_POST['imagen_path'] ?? null;
    $mensaje_personalizado = $_POST['mensaje'] ?? null; // Mensaje personalizado opcional

    // Validar entradas
    if (!isset($id, $debe, $dias)) {
        echo json_encode(['success' => false, 'message' => 'Datos requeridos faltantes']);
        exit;
    }

    // Función para enviar MMS usando PHPMailer
    function enviarMMS($destinatario, $asunto, $mensaje, $imagen_path = null) {
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
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            $mail->AltBody = strip_tags($mensaje);
            
            // Adjuntar imagen si se proporciona
            if ($imagen_path && file_exists($imagen_path)) {
                $mail->addAttachment($imagen_path, 'imagen.jpg');
            }
            
            // Enviar correo
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando MMS: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Función para enviar SMS simple (sin imagen) usando PHPMailer
    function enviarSMS($destinatario, $asunto, $mensaje) {
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
            error_log("Error enviando SMS: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Incluir función helper para compañías
    include 'functions_companias.php';
    
    $sql = mysqli_query($conn, "SELECT tel, nombre, email, compania_telefonica_id FROM usuarios WHERE id='$id'");
    $result = mysqli_fetch_array($sql);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        exit;
    }
    
    // Verificar si se puede enviar MMS al usuario
    $puede_enviar_mms = puedeEnviarMMS($result['compania_telefonica_id'], $conn);
    $info_compania = obtenerInfoCompaniaUsuario($result['compania_telefonica_id'], $conn);
    
    // Construir email MMS con dominio correcto
    $tel = construirEmailMMS($result['tel'], $result['compania_telefonica_id'], $conn);
    $deudor = $result['nombre'];
    $email = $result['email'];
    
    // Obtener detalles completos de las deudas
    $sql_deudas = mysqli_query($conn, "
        SELECT 
            dd.duedor,
            dd.comprador,
            dd.debe,
            dd.fecha,
            DATEDIFF(CURDATE(), dd.fecha) as dias_atraso,
            u.nombre as nombre_comprador
        FROM deudas_detalladas dd
        LEFT JOIN usuarios u ON dd.comprador = u.nombre
        WHERE dd.duedor = '$deudor'
        ORDER BY dd.fecha DESC
    ");
    
    $total_deuda = 0;
    $detalles_deudas = [];
    $fecha_mas_antigua = null;
    $fecha_mas_reciente = null;
    
    while ($deuda = mysqli_fetch_array($sql_deudas)) {
        $total_deuda += $deuda['debe'];
        $detalles_deudas[] = $deuda;
        
        if (!$fecha_mas_antigua || $deuda['fecha'] < $fecha_mas_antigua) {
            $fecha_mas_antigua = $deuda['fecha'];
        }
        if (!$fecha_mas_reciente || $deuda['fecha'] > $fecha_mas_reciente) {
            $fecha_mas_reciente = $deuda['fecha'];
        }
    }
    
    $dias_atraso_total = $dias; // Usar el parámetro original
    $cantidad_deudas = count($detalles_deudas);

    // Configuración del correo
    $subject = "Recordatorio de Deuda";
    
    // Construir mensaje enriquecido
    if ($mensaje_personalizado) {
        $message = $mensaje_personalizado;
    } else {
        $message = "🏦 RECORDATORIO DE DEUDAS - SISTEMA DE GASTOS\n\n";
        $message .= "Hola $deudor,\n\n";
        $message .= "📊 RESUMEN DE TU SITUACIÓN:\n";
        $message .= "• Total de deudas: $" . number_format($total_deuda, 2) . "\n";
        $message .= "• Cantidad de deudas: $cantidad_deudas\n";
        $message .= "• Días de atraso: $dias_atraso_total días\n";
        $message .= "• F más antigua: " . date('d/m/Y', strtotime($fecha_mas_antigua)) . "\n";
        $message .= "• F más reciente: " . date('d/m/Y', strtotime($fecha_mas_reciente)) . "\n\n";
        
        if ($cantidad_deudas > 0) {
            $message .= "📋 DETALLES DE DEUDAS:\n";
            $message .= "─────────────────────────\n";
            
            // Agrupar por comprador
            $deudas_por_comprador = [];
            foreach ($detalles_deudas as $deuda) {
                $comprador = $deuda['comprador'];
                if (!isset($deudas_por_comprador[$comprador])) {
                    $deudas_por_comprador[$comprador] = 0;
                }
                $deudas_por_comprador[$comprador] += $deuda['debe'];
            }
            
            foreach ($deudas_por_comprador as $comprador => $monto) {
                $message .= "• $comprador: $" . number_format($monto, 2) . "\n";
            }
            
            $message .= "\n";
        }
        
        $message .= "💡 RECOMENDACIONES:\n";
        $message .= "• Revisa tus deudas pendientes\n";
        $message .= "• Contacta a los acreedores\n";
        $message .= "• Actualiza tu situación en el sistema\n\n";
        
        $message .= "🌐 ACCESO AL SISTEMA:\n";
        $message .= "jtabasco.com/gasto\n\n";
        
        $message .= "Fecha: " . date('d/m/Y H:i') . "\n";
        $message .= "Sistema de Gestión de Gastos";
    }

    $enviado = false;
    $metodo_usado = '';
    $detalles = '';
    
    if ($tipo_envio === 'mms') {
        // Verificar si se puede enviar MMS
        if (!$puede_enviar_mms) {
            $compania_nombre = $info_compania ? $info_compania['nombre'] : 'No asignada';
            $enviado = false;
            $metodo_usado = 'MMS bloqueado - Compañía inactiva';
        } else {
            // Enviar MMS con imagen
            $enviado = enviarMMS($tel, $subject, $message, $imagen_path);
            $metodo_usado = 'MMS a teléfono';
            
            // Si falla el MMS, intentar con email
            if (!$enviado) {
                $enviado = enviarMMS($email, $subject, $message, $imagen_path);
                $metodo_usado = 'MMS a email (fallback)';
            }
        }
    } else {
        // Enviar SMS simple
        $enviado = enviarSMS($tel, $subject, $message);
        $metodo_usado = 'SMS a teléfono';
        
        // Si falla el SMS, intentar con email
        if (!$enviado) {
            $enviado = enviarSMS($email, $subject, $message);
            $metodo_usado = 'SMS a email (fallback)';
        }
    }

    $response = [
        'success' => $enviado,
        'message' => $enviado ? 'Mensaje enviado correctamente' : 'Error al enviar mensaje',
        'destinatario' => $tel,
        'tipo' => $tipo_envio,
        'metodo_usado' => $metodo_usado,
        'mensaje_enviado' => $message,
        'asunto' => $subject,
        'imagen_adjunta' => ($imagen_path && file_exists($imagen_path)) ? 'Sí' : 'No',
        'compania_info' => [
            'puede_enviar_mms' => $puede_enviar_mms,
            'compania_nombre' => $info_compania ? $info_compania['nombre'] : 'No asignada',
            'compania_activa' => $info_compania ? ($info_compania['activo'] === 'Si') : false
        ],
        'detalles_adicionales' => [
            'total_deuda' => number_format($total_deuda, 2),
            'cantidad_deudas' => $cantidad_deudas,
            'dias_atraso' => $dias_atraso_total,
            'fecha_mas_antigua' => $fecha_mas_antigua ? date('d/m/Y', strtotime($fecha_mas_antigua)) : 'N/A',
            'fecha_mas_reciente' => $fecha_mas_reciente ? date('d/m/Y', strtotime($fecha_mas_reciente)) : 'N/A',
            'deudor' => $deudor
        ]
    ];

    echo json_encode($response);
    $conn->close();
?>