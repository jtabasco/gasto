<?php
session_start();
include "../conexion.php";
include "../system/functions_companias.php";
require '../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Habilitar reporte de errores para debug pero no mostrar en output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Asegurar que no hay output antes del JSON
ob_clean();

$id = $_POST['id'];

// Verificar que el ID existe
if (!$id) {
    echo json_encode([
        'success' => false,
        'message' => 'ID de usuario no proporcionado'
    ]);
    exit;
}

$sql = mysqli_query($conn, "SELECT tel, nombre, email, compania_telefonica_id FROM usuarios WHERE id='$id'");
$result = mysqli_fetch_array($sql);

if ($result) {
    // Verificar si se puede enviar MMS
    $puede_enviar_mms = puedeEnviarMMS($result['compania_telefonica_id'], $conn);
    $info_compania = obtenerInfoCompaniaUsuario($result['compania_telefonica_id'], $conn);
    
    $tel = construirEmailMMS($result['tel'], $result['compania_telefonica_id'], $conn);
    $nombre = $result['nombre'];
    
    // Configuración de correo
    $config = include '../config/mail_config.php';
    
    // Función para enviar MMS usando PHPMailer
    function enviarMMSTest($destinatario, $asunto, $mensaje) {
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
            
            // Enviar correo
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Error enviando MMS: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    // Verificar si se puede enviar MMS
    if (!$puede_enviar_mms) {
        $compania_nombre = $info_compania ? $info_compania['nombre'] : 'No asignada';
        echo json_encode([
            'success' => false,
            'message' => 'No se puede enviar MMS - Compañía "' . $compania_nombre . '" no está activa',
            'destinatario' => $tel,
            'compania_info' => [
                'puede_enviar_mms' => $puede_enviar_mms,
                'compania_nombre' => $compania_nombre,
                'compania_activa' => $info_compania ? ($info_compania['activo'] === 'Si') : false
            ],
            'debug' => [
                'id' => $id,
                'nombre' => $nombre,
                'telefono' => $result['tel'],
                'compania_id' => $result['compania_telefonica_id'],
                'email_mms' => $tel
            ]
        ]);
        exit;
    }
    
    // Enviar mensaje de prueba
    $subject = "Test MMS - Sistema de Gastos";
    $message = "Hola $nombre, este es un mensaje de prueba del sistema de gastos.";
    
    $enviado = enviarMMSTest($tel, $subject, $message);
    
    if ($enviado) {
        echo json_encode([
            'success' => true,
            'message' => 'Mensaje de prueba enviado a ' . $tel,
            'destinatario' => $tel,
            'compania_info' => [
                'puede_enviar_mms' => $puede_enviar_mms,
                'compania_nombre' => $info_compania ? $info_compania['nombre'] : 'No asignada',
                'compania_activa' => $info_compania ? ($info_compania['activo'] === 'Si') : true
            ],
            'debug' => [
                'id' => $id,
                'nombre' => $nombre,
                'telefono' => $result['tel'],
                'compania_id' => $result['compania_telefonica_id'],
                'email_mms' => $tel
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar el mensaje de prueba',
            'destinatario' => $tel,
            'compania_info' => [
                'puede_enviar_mms' => $puede_enviar_mms,
                'compania_nombre' => $info_compania ? $info_compania['nombre'] : 'No asignada',
                'compania_activa' => $info_compania ? ($info_compania['activo'] === 'Si') : true
            ],
            'debug' => [
                'id' => $id,
                'nombre' => $nombre,
                'telefono' => $result['tel'],
                'compania_id' => $result['compania_telefonica_id'],
                'email_mms' => $tel
            ]
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Usuario no encontrado con ID: ' . $id
    ]);
}
?> 