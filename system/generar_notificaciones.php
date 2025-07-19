<?php
session_start();

// Configurar zona horaria de México para que la fecha se guarde correctamente


// Verificar si la clase Database ya está definida
if (!class_exists('Database')) {
    include "../conexion.php";
} else {
    // Si ya está definida, solo obtener la conexión
    global $conn;
}

/**
 * Verifica pagos pendientes y crea notificaciones
 */
function verificarPagosPendientes($conn) {
    $sql = "SELECT 
                dg.id,
                dg.deudor,
                dg.monto,
                dg.fecha,
                g.detalle,
                u.nombre as deudor_nombre,
                u.email,
                u.tel,
                DATEDIFF(CURDATE(), dg.fecha) as dias_pendiente
            FROM detalle_gasto dg
            INNER JOIN Gastos g ON dg.idgasto = g.id
            INNER JOIN usuarios u ON dg.deudor = u.id
            WHERE dg.pagado = 'No' 
            AND DATEDIFF(CURDATE(), dg.fecha) >= 7
            ORDER BY dg.fecha ASC";
    
    $result = $conn->query($sql);
    $notificaciones = 0;
    
    while ($row = $result->fetch_object()) {
        $mensaje = "Hola {$row->deudor_nombre}, tienes un pago pendiente de $" . 
                   number_format($row->monto, 2) . " por: {$row->detalle}. " .
                   "Han pasado {$row->dias_pendiente} días desde el {$row->fecha}.";
        
        $asunto = "Pago Pendiente - " . $row->dias_pendiente . " días";
        
        // Verificar si ya existe esta notificación
        $check_sql = "SELECT id FROM correos_pendientes WHERE destinatario = ? AND asunto = ? AND enviado = 0";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $row->email, $asunto);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows == 0) {
            // Insertar nueva notificación con fecha de México
            $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
            $insert_sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $row->email, $asunto, $mensaje, $fecha_creacion);
            $insert_stmt->execute();
            $notificaciones++;
        }
    }
    
    return $notificaciones;
}

/**
 * Verifica deudas que exceden un monto límite
 */
function verificarDeudasAltas($conn, $limite = 1000) {
    $sql = "SELECT 
                u.id,
                u.nombre,
                u.email,
                u.tel,
                SUM(dg.monto) as total_deuda
            FROM usuarios u
            INNER JOIN detalle_gasto dg ON u.id = dg.deudor
            WHERE dg.pagado = 'No'
            GROUP BY u.id
            HAVING total_deuda > $limite";
    
    $result = $conn->query($sql);
    $notificaciones = 0;
    
    while ($row = $result->fetch_object()) {
        $mensaje = "Hola {$row->nombre}, tu deuda total ha alcanzado $" . 
                   number_format($row->total_deuda, 2) . ". " .
                   "Te recomendamos revisar tus pagos pendientes.";
        
        $asunto = "Deuda Alta - $" . number_format($row->total_deuda, 2);
        
        // Verificar si ya existe esta notificación
        $check_sql = "SELECT id FROM correos_pendientes WHERE destinatario = ? AND asunto = ? AND enviado = 0";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $row->email, $asunto);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows == 0) {
            // Insertar nueva notificación con fecha de México
            $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
            $insert_sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $row->email, $asunto, $mensaje, $fecha_creacion);
            $insert_stmt->execute();
            $notificaciones++;
        }
    }
    
    return $notificaciones;
}

// Ejecutar verificaciones
$total_notificaciones = 0;
$total_notificaciones += verificarPagosPendientes($conn);
$total_notificaciones += verificarDeudasAltas($conn);

echo json_encode(['notificaciones_generadas' => $total_notificaciones]);
?> 