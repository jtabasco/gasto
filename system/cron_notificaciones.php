<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Script iniciado correctamente", 3, "/home/u338215117/domains/jtabasco.com/public_html/gasto/system/cron_error.log");


// Script para ejecutar automáticamente las notificaciones
// Se puede configurar en cron para ejecutarse diariamente
// Ejemplo: 0 9 * * * php /path/to/cron_notificaciones.php

// Configurar zona horaria




// Incluir conexión
//include "../conexion.php";
include '/home/u338215117/domains/jtabasco.com/public_html/gasto/conexion.php';


// Log para debugging
$log_file = "notificaciones_cron.log";
$log_message = "[" . date('Y-m-d H:i:s') . "] Iniciando generación automática de notificaciones\n";
//file_put_contents($log_file, $log_message, FILE_APPEND);

/**
 * Verifica pagos pendientes y crea notificaciones
 */
function verificarPagosPendientes($conn) {
    $sql = "SELECT 
                u.id as deudor_id,
                u.nombre as deudor_nombre,
                u.email,
                u.tel,
                COUNT(dg.id) as cantidad_deudas,
                SUM(dg.monto) as total_pendiente,
                MIN(dg.fecha) as fecha_mas_antigua,
                MAX(DATEDIFF(CURDATE(), dg.fecha)) as dias_max_pendiente
            FROM usuarios u
            INNER JOIN detalle_gasto dg ON u.id = dg.deudor
            INNER JOIN Gastos g ON dg.idgasto = g.id
            WHERE dg.pagado = 'No' 
            AND DATEDIFF(CURDATE(), dg.fecha) >= 7
            GROUP BY u.id, u.nombre, u.email, u.tel
            ORDER BY fecha_mas_antigua ASC";
    
    $result = $conn->query($sql);
    $notificaciones = 0;
    
    while ($row = $result->fetch_object()) {
        // Obtener el detalle de todas las deudas pendientes
        $sql_detalles = "SELECT 
                            g.detalle,
                            dg.monto,
                            dg.fecha,
                            DATEDIFF(CURDATE(), dg.fecha) as dias_pendiente
                        FROM detalle_gasto dg
                        INNER JOIN Gastos g ON dg.idgasto = g.id
                        WHERE dg.deudor = ? 
                        AND dg.pagado = 'No' 
                        AND DATEDIFF(CURDATE(), dg.fecha) >= 7
                        ORDER BY dg.fecha ASC";
        
        $stmt_detalles = $conn->prepare($sql_detalles);
        $stmt_detalles->bind_param("i", $row->deudor_id);
        $stmt_detalles->execute();
        $result_detalles = $stmt_detalles->get_result();
        
        $detalles_html = '';
        while ($detalle = $result_detalles->fetch_object()) {
            $detalles_html .= '
            <div style="background: #fff; border: 1px solid #e9ecef; border-radius: 6px; padding: 15px; margin-bottom: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                    <strong style="color: #333;">'.$detalle->detalle.'</strong>
                    <span style="color: #FF5722; font-weight: bold; font-size: 1.1em;">$'.number_format($detalle->monto, 2).'</span>
                </div>
                <div style="font-size: 0.9em; color: #666;">
                    <div style="margin-bottom: 4px;">Fecha: '.$detalle->fecha.'</div>
                    <div style="color: #e74c3c;">'.$detalle->dias_pendiente.' días pendiente</div>
                </div>
            </div>';
        }
        
        $mensaje = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 0; 
                    background: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: #fff; 
                    border-radius: 12px; 
                    overflow: hidden; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
                }
                .header { 
                    background: #FF5722; 
                    color: white; 
                    padding: 24px 0; 
                    text-align: center; 
                }
                .header h2 { 
                    margin: 0; 
                    font-size: 1.8em; 
                }
                .content { 
                    padding: 32px 24px; 
                }
                .alert-box { 
                    background: #fff3cd; 
                    border: 1px solid #ffeaa7; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin-bottom: 24px; 
                }
                .amount { 
                    color: #FF5722; 
                    font-size: 1.8em; 
                    font-weight: bold; 
                    margin: 10px 0; 
                }
                .summary { 
                    background: #f8f9fa; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin: 20px 0; 
                }
                .summary-item { 
                    display: flex; 
                    justify-content: space-between; 
                    margin: 8px 0; 
                    padding: 5px 0; 
                }
                .action-button { 
                    background: #FF5722; 
                    color: white; 
                    padding: 12px 24px; 
                    text-decoration: none; 
                    border-radius: 6px; 
                    display: inline-block; 
                    margin: 20px 0; 
                    font-weight: bold; 
                }
                .footer { 
                    background: #f8f9fa; 
                    color: #6c757d; 
                    padding: 20px; 
                    text-align: center; 
                    font-size: 0.9em; 
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>⚠️ Pagos Pendientes</h2>
                </div>
                <div class="content">
                    <p>Hola <strong>'.$row->deudor_nombre.'</strong>,</p>
                    
                    <div class="alert-box">
                        <p>Tienes <strong>'.$row->cantidad_deudas.' pagos pendientes</strong> que requieren tu atención:</p>
                        <div class="amount">$'.number_format($row->total_pendiente, 2).'</div>
                        <p>El pago más antiguo tiene <strong>'.$row->dias_max_pendiente.' días</strong> de retraso.</p>
                    </div>
                    
                    <div class="summary">
                        <h3 style="color: #333; margin-top: 0;">Resumen</h3>
                        <div class="summary-item">
                            <span><strong>Total pendiente:</strong></span>
                            <span style="color: #FF5722; font-weight: bold;">$'.number_format($row->total_pendiente, 2).'</span>
                        </div>
                        <div class="summary-item">
                            <span><strong>Cantidad de deudas:</strong></span>
                            <span style="color: #FF5722; font-weight: bold;">'.$row->cantidad_deudas.'</span>
                        </div>
                        <div class="summary-item">
                            <span><strong>Días máximo pendiente:</strong></span>
                            <span style="color: #e74c3c; font-weight: bold;">'.$row->dias_max_pendiente.' días</span>
                        </div>
                    </div>
                    
                    <h3 style="color: #333; margin: 24px 0 16px 0;">Detalle de Deudas Pendientes</h3>
                    '.$detalles_html.'
                    
                    <div style="text-align: center;">
                        <a href="https://jtabasco.com/gasto" class="action-button">Ver Detalles y Pagar</a>
                    </div>
                </div>
                <div class="footer">
                    <p>Este es un mensaje automático del Sistema de Gestión de Gastos.<br>
                    Por favor, no responda a este correo.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $asunto = "Pagos Pendientes - $" . number_format($row->total_pendiente, 2) . " (" . $row->cantidad_deudas . " deudas) mayores de 7 dias";
        
        // Verificar si ya existe esta notificación hoy para este usuario
        $check_sql = "SELECT id FROM correos_pendientes WHERE destinatario = ? AND asunto LIKE ? AND enviado = 0";
        $check_stmt = $conn->prepare($check_sql);
        $asunto_pattern = "Pagos Pendientes - $%";
        $check_stmt->bind_param("ss", $row->email, $asunto_pattern);
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
        
        // Cerrar las conexiones preparadas
        $check_stmt->close();
        $stmt_detalles->close();
    }
    
    return $notificaciones;
}

/**
 * Verifica deudas que exceden un monto límite
 */
function verificarDeudasAltas($conn, $limite = 100) {
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
        $mensaje = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0; 
                    padding: 0; 
                    background: #f5f5f5; 
                }
                .container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background: #fff; 
                    border-radius: 12px; 
                    overflow: hidden; 
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
                }
                .header { 
                    background: #FF9800; 
                    color: white; 
                    padding: 24px 0; 
                    text-align: center; 
                }
                .header h2 { 
                    margin: 0; 
                    font-size: 1.8em; 
                }
                .content { 
                    padding: 32px 24px; 
                }
                .alert-box { 
                    background: #fff3e0; 
                    border: 1px solid #ffcc80; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin-bottom: 24px; 
                }
                .amount { 
                    color: #FF9800; 
                    font-size: 1.8em; 
                    font-weight: bold; 
                    margin: 10px 0; 
                }
                .details { 
                    background: #f8f9fa; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin: 20px 0; 
                }
                .action-button { 
                    background: #FF9800; 
                    color: white; 
                    padding: 12px 24px; 
                    text-decoration: none; 
                    border-radius: 6px; 
                    display: inline-block; 
                    margin: 20px 0; 
                    font-weight: bold; 
                }
                .footer { 
                    background: #f8f9fa; 
                    color: #6c757d; 
                    padding: 20px; 
                    text-align: center; 
                    font-size: 0.9em; 
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>📋 Recordatorio de Pagos</h2>
                </div>
                <div class="content">
                    <p>Hola <strong>'.$row->nombre.'</strong>,</p>
                    
                    <div class="alert-box">
                        <p>Tienes pagos pendientes que requieren tu atención:</p>
                        <div class="amount">$'.number_format($row->total_deuda, 2).'</div>
                        <p>Te recomendamos revisar y actualizar tus pagos pendientes cuando sea posible.</p>
                    </div>
                    
                    <div class="details">
                        <h3 style="color: #333; margin-top: 0;">Resumen de Pagos</h3>
                        <p><strong>Total pendiente:</strong> $'.number_format($row->total_deuda, 2).'</p>
                        <p><strong>Estado:</strong> <span style="color: #FF9800; font-weight: bold;">Pendiente de revisión</span></p>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="https://jtabasco.com/gasto" class="action-button">Revisar Pagos Pendientes</a>
                    </div>
                </div>
                <div class="footer">
                    <p>Este es un mensaje automático del Sistema de Gestión de Gastos.<br>
                    Por favor, no responda a este correo.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $asunto = "Recordatorio de Pagos - $" . number_format($row->total_deuda, 2) . " - " . $row->nombre;
        
        // Verificar si ya existe esta notificación hoy
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
        
        // Cerrar la conexión preparada
        $check_stmt->close();
    }
    
    return $notificaciones;
}

/**
 * Genera resumen semanal de gastos
 */
function generarResumenSemanal($conn) {
    $sql = "SELECT 
    u.id,
    u.nombre,
    u.email,
    COALESCE(deudor_gastos.total, 0) + COALESCE(comprador_gastos.total, 0) as total_gastos,
    COALESCE(deudor_gastos.cantidad, 0) + COALESCE(comprador_gastos.cantidad, 0) as cantidad_gastos,
    COALESCE(deudor_gastos.pendiente, 0) + COALESCE(comprador_gastos.pendiente, 0) as total_pendiente
FROM usuarios u
LEFT JOIN (
    -- Gastos como deudor
    SELECT 
        dg.deudor as user_id,
        SUM(dg.monto) as total,
        COUNT(DISTINCT dg.idgasto) as cantidad,
        SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as pendiente
    FROM detalle_gasto dg
    INNER JOIN Gastos g ON dg.idgasto = g.id
    WHERE g.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    AND g.fecha <= CURDATE()
    AND g.compra = 'S'
    GROUP BY dg.deudor
) deudor_gastos ON u.id = deudor_gastos.user_id

LEFT JOIN (
    -- Gastos como comprador (solo su parte)
    SELECT 
        g.comprador as user_id,
        SUM(g.Monto / (SELECT COUNT(*) + 1 FROM detalle_gasto dg2 WHERE dg2.idgasto = g.id)) as total,
        COUNT(*) as cantidad,
        0 as pendiente
    FROM Gastos g
    WHERE g.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) 
    AND g.fecha <= CURDATE()
    AND g.compra = 'S'
    AND NOT EXISTS (
        SELECT 1 FROM detalle_gasto dg3 
        WHERE dg3.idgasto = g.id AND dg3.deudor = g.comprador
    )
    GROUP BY g.comprador
) comprador_gastos ON u.id = comprador_gastos.user_id

WHERE COALESCE(deudor_gastos.total, 0) + COALESCE(comprador_gastos.total, 0) > 0
ORDER BY total_gastos DESC;";
    
    $result = $conn->query($sql);
    $notificaciones = 0;
    
    while ($row = $result->fetch_object()) {
        if ($row->total_gastos > 0) {
            $mensaje = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 0; 
                        padding: 0; 
                        background: #f5f5f5; 
                    }
                    .container { 
                        max-width: 600px; 
                        margin: 0 auto; 
                        background: #fff; 
                        border-radius: 12px; 
                        overflow: hidden; 
                        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
                    }
                    .header { 
                        background: #219653; 
                        color: white; 
                        padding: 24px 0; 
                        text-align: center; 
                    }
                    .header h2 { 
                        margin: 0; 
                        font-size: 1.8em; 
                    }
                    .content { 
                        padding: 32px 24px; 
                    }
                    .summary-box { 
                        background: #f0f9ff; 
                        border: 1px solid #bae6fd; 
                        border-radius: 8px; 
                        padding: 20px; 
                        margin-bottom: 24px; 
                    }
                    .amount { 
                        color: #219653; 
                        font-size: 1.8em; 
                        font-weight: bold; 
                        margin: 10px 0; 
                    }
                    .stats { 
                        background: #f8f9fa; 
                        border-radius: 8px; 
                        padding: 20px; 
                        margin: 20px 0; 
                    }
                    .stat-item { 
                        display: flex; 
                        justify-content: space-between; 
                        margin: 10px 0; 
                        padding: 8px 0; 
                        border-bottom: 1px solid #e9ecef; 
                    }
                    .action-button { 
                        background: #219653; 
                        color: white; 
                        padding: 12px 24px; 
                        text-decoration: none; 
                        border-radius: 6px; 
                        display: inline-block; 
                        margin: 20px 0; 
                        font-weight: bold; 
                    }
                    .footer { 
                        background: #f8f9fa; 
                        color: #6c757d; 
                        padding: 20px; 
                        text-align: center; 
                        font-size: 0.9em; 
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>📊 Resumen Semanal</h2>
                    </div>
                    <div class="content">
                        <p>Hola <strong>'.$row->nombre.'</strong>,</p>
                        
                        <div class="summary-box">
                            <p>Aquí tienes tu resumen semanal de gastos:</p>
                            <div class="amount">$'.number_format($row->total_gastos, 2).'</div>
                            <p>Período: Lunes a Domingo (últimos 7 días)</p>
                        </div>
                        
                        <div class="stats">
                            <h3 style="color: #333; margin-top: 0;">Estadísticas</h3>
                            <div class="stat-item">
                                <span><strong>Total involucrado:</strong></span>
                                <span style="color: #219653; font-weight: bold;">$'.number_format($row->total_gastos, 2).'</span>
                            </div>
                            <div class="stat-item">
                                <span><strong>Cantidad de gastos:</strong></span>
                                <span style="color: #219653; font-weight: bold;">'.$row->cantidad_gastos.'</span>
                            </div>';
            
            if ($row->cantidad_gastos > 0) {
                $mensaje .= '
                            <div class="stat-item">
                                <span><strong>Promedio por gasto:</strong></span>
                                <span style="color: #219653; font-weight: bold;">$'.number_format($row->total_gastos / $row->cantidad_gastos, 2).'</span>
                            </div>';
            }
            
            if ($row->total_pendiente > 0) {
                $mensaje .= '
                            <div class="stat-item" style="border-bottom: 2px solid #e74c3c; background: #fdf2f2; border-radius: 4px; padding: 10px; margin-top: 15px;">
                                <span><strong>Total pendiente por pagar:</strong></span>
                                <span style="color: #e74c3c; font-weight: bold; font-size: 1.1em;">$'.number_format($row->total_pendiente, 2).'</span>
                            </div>';
            }
            
            $mensaje .= '
                        </div>
                        
                        <div style="text-align: center;">
                            <a href="https://jtabasco.com/gasto" class="action-button">Ver Detalles Completos</a>
                        </div>
                    </div>
                    <div class="footer">
                        <p>Este es un mensaje automático del Sistema de Gestión de Gastos.<br>
                        Por favor, no responda a este correo.</p>
                    </div>
                </div>
            </body>
            </html>';
            
            $asunto = "Resumen Semanal de Gastos";
            
            // Solo enviar resumen semanal los lunes
            if (date('N') == 1) { // 1 = lunes
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
                
                // Cerrar la conexión preparada
                $check_stmt->close();
            }
        }
    }
    
    return $notificaciones;
}

/**
 * Envía notificaciones a administradores sobre usuarios con deudas críticas
 */
function notificarAdministradores($conn) {
    // Obtener usuarios con deudas de más de 30 días
    $sql_30_dias = "SELECT 
                        u.id,
                        u.nombre,
                        u.email,
                        u.tel,
                        COUNT(dg.id) as cantidad_deudas,
                        SUM(dg.monto) as total_pendiente,
                        MAX(DATEDIFF(CURDATE(), dg.fecha)) as dias_max_pendiente
                    FROM usuarios u
                    INNER JOIN detalle_gasto dg ON u.id = dg.deudor
                    INNER JOIN Gastos g ON dg.idgasto = g.id
                    WHERE dg.pagado = 'No' 
                    AND DATEDIFF(CURDATE(), dg.fecha) >= 30
                    GROUP BY u.id, u.nombre, u.email, u.tel
                    ORDER BY total_pendiente DESC";
    
    $result_30_dias = $conn->query($sql_30_dias);
    $usuarios_30_dias = [];
    while ($row = $result_30_dias->fetch_object()) {
        $usuarios_30_dias[] = $row;
    }
    
    // Obtener usuarios con deudas altas (más de $100)
    $sql_deudas_altas = "SELECT 
                            u.id,
                            u.nombre,
                            u.email,
                            u.tel,
                            COUNT(dg.id) as cantidad_deudas,
                            SUM(dg.monto) as total_pendiente,
                            MAX(DATEDIFF(CURDATE(), dg.fecha)) as dias_max_pendiente
                        FROM usuarios u
                        INNER JOIN detalle_gasto dg ON u.id = dg.deudor
                        INNER JOIN Gastos g ON dg.idgasto = g.id
                        WHERE dg.pagado = 'No'
                        GROUP BY u.id, u.nombre, u.email, u.tel
                        HAVING total_pendiente > 100
                        ORDER BY total_pendiente DESC";
    
    $result_deudas_altas = $conn->query($sql_deudas_altas);
    $usuarios_deudas_altas = [];
    while ($row = $result_deudas_altas->fetch_object()) {
        $usuarios_deudas_altas[] = $row;
    }
    
    // Solo enviar si hay usuarios con deudas críticas
    if (empty($usuarios_30_dias) && empty($usuarios_deudas_altas)) {
        return 0;
    }
    
    // Generar tabla HTML para usuarios con más de 30 días
    $tabla_30_dias = '';
    if (!empty($usuarios_30_dias)) {
        $tabla_30_dias = '
        <h3 style="color: #e74c3c; margin: 20px 0 10px 0;">⚠️ Usuarios con Deudas de Más de 30 Días</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #e74c3c; color: white;">
                    <th style="padding: 12px; text-align: left; border: none;">Usuario</th>
                    <th style="padding: 12px; text-align: left; border: none;">Email</th>
                    <th style="padding: 12px; text-align: left; border: none;">Teléfono</th>
                    <th style="padding: 12px; text-align: right; border: none;">Deudas</th>
                    <th style="padding: 12px; text-align: right; border: none;">Total</th>
                    <th style="padding: 12px; text-align: right; border: none;">Días Máx</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($usuarios_30_dias as $usuario) {
            $tabla_30_dias .= '
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px; border: none;"><strong>'.$usuario->nombre.'</strong></td>
                    <td style="padding: 12px; border: none;">'.$usuario->email.'</td>
                    <td style="padding: 12px; border: none;">'.$usuario->tel.'</td>
                    <td style="padding: 12px; text-align: right; border: none;">'.$usuario->cantidad_deudas.'</td>
                    <td style="padding: 12px; text-align: right; border: none; color: #e74c3c; font-weight: bold;">$'.number_format($usuario->total_pendiente, 2).'</td>
                    <td style="padding: 12px; text-align: right; border: none; color: #e74c3c; font-weight: bold;">'.$usuario->dias_max_pendiente.' días</td>
                </tr>';
        }
        
        $tabla_30_dias .= '
            </tbody>
        </table>';
    }
    
    // Generar tabla HTML para usuarios con deudas altas
    $tabla_deudas_altas = '';
    if (!empty($usuarios_deudas_altas)) {
        $tabla_deudas_altas = '
        <h3 style="color: #f39c12; margin: 20px 0 10px 0;">💰 Usuarios con Deudas Altas (>$100)</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <thead>
                <tr style="background: #f39c12; color: white;">
                    <th style="padding: 12px; text-align: left; border: none;">Usuario</th>
                    <th style="padding: 12px; text-align: left; border: none;">Email</th>
                    <th style="padding: 12px; text-align: left; border: none;">Teléfono</th>
                    <th style="padding: 12px; text-align: right; border: none;">Deudas</th>
                    <th style="padding: 12px; text-align: right; border: none;">Total</th>
                    <th style="padding: 12px; text-align: right; border: none;">Días Máx</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($usuarios_deudas_altas as $usuario) {
            $tabla_deudas_altas .= '
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 12px; border: none;"><strong>'.$usuario->nombre.'</strong></td>
                    <td style="padding: 12px; border: none;">'.$usuario->email.'</td>
                    <td style="padding: 12px; border: none;">'.$usuario->tel.'</td>
                    <td style="padding: 12px; text-align: right; border: none;">'.$usuario->cantidad_deudas.'</td>
                    <td style="padding: 12px; text-align: right; border: none; color: #f39c12; font-weight: bold;">$'.number_format($usuario->total_pendiente, 2).'</td>
                    <td style="padding: 12px; text-align: right; border: none; color: #f39c12; font-weight: bold;">'.$usuario->dias_max_pendiente.' días</td>
                </tr>';
        }
        
        $tabla_deudas_altas .= '
            </tbody>
        </table>';
    }
    
    // Calcular totales
    $total_30_dias = array_sum(array_column($usuarios_30_dias, 'total_pendiente'));
    $total_deudas_altas = array_sum(array_column($usuarios_deudas_altas, 'total_pendiente'));
    $cantidad_usuarios_30_dias = count($usuarios_30_dias);
    $cantidad_usuarios_deudas_altas = count($usuarios_deudas_altas);
    
    $mensaje = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                margin: 0; 
                padding: 0; 
                background: #f5f5f5; 
            }
            .container { 
                max-width: 800px; 
                margin: 0 auto; 
                background: #fff; 
                border-radius: 12px; 
                overflow: hidden; 
                box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
            }
            .header { 
                background: #2c3e50; 
                color: white; 
                padding: 24px 0; 
                text-align: center; 
            }
            .header h2 { 
                margin: 0; 
                font-size: 1.8em; 
            }
            .content { 
                padding: 32px 24px; 
            }
            .summary-box { 
                background: #ecf0f1; 
                border: 1px solid #bdc3c7; 
                border-radius: 8px; 
                padding: 20px; 
                margin-bottom: 24px; 
            }
            .stats-grid { 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 20px; 
                margin: 20px 0; 
            }
            .stat-card { 
                background: #fff; 
                border-radius: 8px; 
                padding: 20px; 
                border: 2px solid #e74c3c; 
                text-align: center; 
            }
            .stat-card.orange { 
                border-color: #f39c12; 
            }
            .stat-number { 
                font-size: 2em; 
                font-weight: bold; 
                margin: 10px 0; 
            }
            .stat-number.red { 
                color: #e74c3c; 
            }
            .stat-number.orange { 
                color: #f39c12; 
            }
            .action-button { 
                background: #2c3e50; 
                color: white; 
                padding: 12px 24px; 
                text-decoration: none; 
                border-radius: 6px; 
                display: inline-block; 
                margin: 20px 0; 
                font-weight: bold; 
            }
            .footer { 
                background: #f8f9fa; 
                color: #6c757d; 
                padding: 20px; 
                text-align: center; 
                font-size: 0.9em; 
            }
            table th, table td { 
                font-size: 0.9em; 
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>🚨 Reporte de Deudas Críticas</h2>
            </div>
            <div class="content">
                <p>Hola <strong>Administrador</strong>,</p>
                
                <div class="summary-box">
                    <p>Se han detectado usuarios con deudas que requieren atención inmediata:</p>
                </div>
                
                <div class="stats-grid">';
    
    if (!empty($usuarios_30_dias)) {
        $mensaje .= '
                    <div class="stat-card">
                        <h3 style="color: #e74c3c; margin-top: 0;">Usuarios con >30 días</h3>
                        <div class="stat-number red">'.$cantidad_usuarios_30_dias.'</div>
                        <p>Total: $'.number_format($total_30_dias, 2).'</p>
                    </div>';
    }
    
    if (!empty($usuarios_deudas_altas)) {
        $mensaje .= '
                    <div class="stat-card orange">
                        <h3 style="color: #f39c12; margin-top: 0;">Usuarios con Deudas Altas</h3>
                        <div class="stat-number orange">'.$cantidad_usuarios_deudas_altas.'</div>
                        <p>Total: $'.number_format($total_deudas_altas, 2).'</p>
                    </div>';
    }
    
    $mensaje .= '
                </div>
                
                '.$tabla_30_dias.'
                '.$tabla_deudas_altas.'
                
                <div style="text-align: center;">
                    <a href="https://jtabasco.com/gasto/system/dashboard_admin.php" class="action-button">Acceder al Panel de Administración</a>
                </div>
            </div>
            <div class="footer">
                <p>Este es un mensaje automático del Sistema de Gestión de Gastos.<br>
                Generado el '.date('d/m/Y H:i:s').'</p>
            </div>
        </div>
    </body>
    </html>';
    
    $asunto = "🚨 Reporte de Deudas Críticas - " . count($usuarios_30_dias) . " usuarios con >30 días, " . count($usuarios_deudas_altas) . " con deudas altas";
    
    // Obtener emails de administradores desde la tabla usuarios (id=1)
    $sql_admin = "SELECT email FROM usuarios WHERE id = 1";
    $result_admin = $conn->query($sql_admin);
    $emails_admin = [];
    
    if ($result_admin && $result_admin->num_rows > 0) {
        while ($admin = $result_admin->fetch_object()) {
            $emails_admin[] = $admin->email;
        }
    }
    
    // Si no hay administradores configurados, usar un email por defecto
    if (empty($emails_admin)) {
        $emails_admin = ['admin@jtabasco.com']; // Email por defecto
    }
    
    $notificaciones = 0;
    foreach ($emails_admin as $email_admin) {
        // Verificar si ya existe esta notificación hoy
        $check_sql = "SELECT id FROM correos_pendientes WHERE destinatario = ? AND asunto LIKE ? AND enviado = 0";
        $check_stmt = $conn->prepare($check_sql);
        $asunto_pattern = "Reporte de Deudas Críticas%";
        $check_stmt->bind_param("ss", $email_admin, $asunto_pattern);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows == 0) {
            // Insertar nueva notificación con fecha de México
            $fecha_creacion = date('Y-m-d H:i:s');
            $insert_sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES (?, ?, ?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $email_admin, $asunto, $mensaje, $fecha_creacion);
            $insert_stmt->execute();
            $notificaciones++;
        }
        
        $check_stmt->close();
    }
    
    return $notificaciones;
}

// Ejecutar todas las verificaciones
$total_notificaciones = 0;
$pagos_pendientes = verificarPagosPendientes($conn);
$deudas_altas = verificarDeudasAltas($conn);
$resumen_semanal = generarResumenSemanal($conn);
$notificaciones_admin = notificarAdministradores($conn);

$total_notificaciones = $pagos_pendientes + $deudas_altas + $resumen_semanal + $notificaciones_admin;


// Devolver respuesta en formato JSON
$response = array(
    'success' => true,
    'message' => 'Notificaciones generadas correctamente',
    'notificaciones_generadas' => $total_notificaciones,
    'pagos_pendientes' => $pagos_pendientes,
    'deudas_altas' => $deudas_altas,
    'resumen_semanal' => $resumen_semanal,
    'notificaciones_admin' => $notificaciones_admin
);

echo json_encode($response);

// Cerrar la conexión principal
$conn->close();
?> 