<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



include '/home/u338215117/domains/jtabasco.com/public_html/gasto/conexion.php'; 
require_once '/home/u338215117/domains/jtabasco.com/public_html/gasto/utils/Mailer.php'; 



//include "../conexion.php";
//require_once '../utils/Mailer.php';

/**
 * Verifica si la hora actual está dentro del rango permitido para enviar correos
 * @param int $horaInicio Hora de inicio (formato 24h, ej: 8 para 8:00 AM)
 * @param int $horaFin Hora de fin (formato 24h, ej: 22 para 10:00 PM)
 * @return bool true si está dentro del rango permitido, false si no
 */
function estaEnRangoHorario($horaInicio = 8, $horaFin = 22) {
    $horaActual = (int)date('H');
    
    // Si el rango cruza la medianoche (ej: 22 a 6)
    if ($horaInicio > $horaFin) {
        return $horaActual >= $horaInicio || $horaActual < $horaFin;
    }
    
    // Rango normal (ej: 8 a 22)
    return $horaActual >= $horaInicio && $horaActual < $horaFin;
}

/**
 * Detecta si un correo es un MMS basándose en el formato del destinatario
 */
function esMMS($destinatario) {
    // Patrón para detectar MMS: número de teléfono + @ + dominio de compañía
    // Ejemplos: 1234567890@mms.att.net, 9876543210@tmomail.net, etc.
    $patron_mms = '/^\d{10,15}@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
    return preg_match($patron_mms, $destinatario);
}

// Configuración de horarios permitidos (en tu zona horaria local)
$horaInicioPermitida = 8;   // 8:00 AM (tu hora local)
$horaFinPermitida = 22;     // 10:00 PM (tu hora local)

// Verificar si estamos en horario permitido
if (!estaEnRangoHorario($horaInicioPermitida, $horaFinPermitida)) {
    echo "Fuera del horario permitido para envío de correos (" . $horaInicioPermitida . ":00 - " . $horaFinPermitida . ":00). Hora actual: " . date('H:i') . " (" . date_default_timezone_get() . ")\n";
    exit;
}

$result = $conn->query("SELECT * FROM correos_pendientes WHERE enviado = 0 LIMIT 10");
$mms_enviado = false; // Contador para MMS enviados en esta ejecución

while($row = $result->fetch_assoc()) {
    // Detectar si es MMS
    if (esMMS($row['destinatario'])) {
        // Solo enviar un MMS por ejecución
        if (!$mms_enviado) {
            $ok = Mailer::enviarMMS($row['destinatario'], $row['asunto'], $row['cuerpo']);
            if($ok) {
                $mms_enviado = true; // Marcar que ya se envió un MMS
                // Obtener la fecha y hora actual en la zona horaria configurada
                $fecha_envio = date('Y-m-d H:i:s');
                $conn->query("UPDATE correos_pendientes SET enviado = 1, fecha_envio = '{$fecha_envio}' WHERE id = {$row['id']}");
                echo "MMS enviado: " . $row['destinatario'] . "\n";
            } else {
                echo "Error enviando MMS: " . $row['destinatario'] . "\n";
                // No marcar como enviado si falló
            }
        } else {
            // Dejar pendiente para la próxima ejecución (NO marcar como enviado)
            echo "MMS pendiente para próxima ejecución: " . $row['destinatario'] . "\n";
        }
    } else {
        // Usar función normal para correos HTML (sin límite)
        $ok = Mailer::enviarCorreo($row['destinatario'], $row['asunto'], $row['cuerpo']);
        if($ok) {
            // Obtener la fecha y hora actual en la zona horaria configurada
            $fecha_envio = date('Y-m-d H:i:s');
            $conn->query("UPDATE correos_pendientes SET enviado = 1, fecha_envio = '{$fecha_envio}' WHERE id = {$row['id']}");
            echo "Correo enviado: " . $row['destinatario'] . "\n";
        } else {
            echo "Error enviando correo: " . $row['destinatario'] . "\n";
            // No marcar como enviado si falló
        }
    }
}

echo "Ejecutado - Hora: " . date('H:i');
?>