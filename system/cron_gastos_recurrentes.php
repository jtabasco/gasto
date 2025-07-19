<?php
// Configurar zona horaria de México para que la fecha se guarde correctamente


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
error_log("Script de gastos recurrentes iniciado", 3, "/home/u338215117/domains/jtabasco.com/public_html/gasto/system/cron_error.log");

// Incluir conexión
include '/home/u338215117/domains/jtabasco.com/public_html/gasto/conexion.php';

/**
 * Genera gastos recurrentes desde las plantillas
 */
function generarGastosRecurrentes($conn) {
    // Obtener todas las plantillas de gastos
    $sql_plantillas = "SELECT 
                        pg.id,
                        pg.comprador,
                        pg.detalle,
                        pg.Monto,
                        pg.idcat,
                        u.nombre as comprador_nombre,
                        u.email as comprador_email
                    FROM plantilla_gastos pg
                    INNER JOIN usuarios u ON pg.comprador = u.id;";
    
    $result_plantillas = $conn->query($sql_plantillas);
    $gastos_generados = 0;
    
    while ($plantilla = $result_plantillas->fetch_object()) {
        // Verificar si ya se generó este gasto este mes
        $sql_check = "SELECT id FROM Gastos 
                     WHERE detalle = ? 
                     AND comprador = ? 
                     AND MONTH(fecha) = MONTH(CURDATE()) 
                     AND YEAR(fecha) = YEAR(CURDATE())";
        
        $check_stmt = $conn->prepare($sql_check);
        $check_stmt->bind_param("si", $plantilla->detalle, $plantilla->comprador);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows == 0) {
            // Insertar el gasto principal
            $sql_insert_gasto = "INSERT INTO Gastos (comprador, detalle, Monto, idcat, fecha, compra) 
                                VALUES (?, ?, ?, ?, CURDATE(), 'S')";
            
            $insert_stmt = $conn->prepare($sql_insert_gasto);
            $insert_stmt->bind_param("isdi", $plantilla->comprador, $plantilla->detalle, $plantilla->Monto, $plantilla->idcat);
            
            if ($insert_stmt->execute()) {
                $gasto_id = $conn->insert_id;
                
                // Obtener los detalles de la plantilla
                $sql_detalles = "SELECT 
                                    pdg.deudor,
                                    pdg.monto,
                                    u.nombre as deudor_nombre,
                                    u.email as deudor_email
                                FROM plantilla_detalle_gasto pdg
                                INNER JOIN usuarios u ON pdg.deudor = u.id
                                WHERE pdg.idgasto = ? 
                                AND pdg.deudor != ?";  // Excluir al comprador
                
                $detalles_stmt = $conn->prepare($sql_detalles);
                $detalles_stmt->bind_param("ii", $plantilla->id, $plantilla->comprador);
                $detalles_stmt->execute();
                $result_detalles = $detalles_stmt->get_result();
                
                // Insertar los detalles del gasto (solo deudores, no el comprador)
                while ($detalle = $result_detalles->fetch_object()) {
                    $sql_insert_detalle = "INSERT INTO detalle_gasto (idgasto, deudor, monto, pagado, fecha) 
                                         VALUES (?, ?, ?, 'No', CURDATE())";
                    
                    $detalle_stmt = $conn->prepare($sql_insert_detalle);
                    $detalle_stmt->bind_param("iid", $gasto_id, $detalle->deudor, $detalle->monto);
                    $detalle_stmt->execute();
                    
                    // Enviar notificación al deudor
                   enviarNotificacionGastoRecurrente($conn, $detalle->deudor_nombre, $detalle->deudor_email, $plantilla->detalle, $detalle->monto, false);
                }
                
                // Enviar notificación al comprador (confirmación de compra registrada)
                enviarNotificacionGastoRecurrente($conn, $plantilla->comprador_nombre, $plantilla->comprador_email, $plantilla->detalle, $plantilla->Monto, true);
                
                $gastos_generados++;
                
                // Cerrar statements
                $detalles_stmt->close();
            }
            
            $insert_stmt->close();
        }
        
        $check_stmt->close();
    }
    
    return $gastos_generados;
}

/**
 * Envía notificación por gasto recurrente generado
 */
function enviarNotificacionGastoRecurrente($conn, $nombre, $email, $detalle, $monto, $es_comprador = false) {
    if ($es_comprador) {
        // Correo para el comprador
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
                    background: #2196F3; 
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
                .info-box { 
                    background: #e3f2fd; 
                    border: 1px solid #2196F3; 
                    border-radius: 8px; 
                    padding: 20px; 
                    margin-bottom: 24px; 
                }
                .amount { 
                    color: #2196F3; 
                    font-size: 1.8em; 
                    font-weight: bold; 
                    margin: 10px 0; 
                }
                .action-button { 
                    background: #2196F3; 
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
                    <h2>✅ Gasto Recurrente Registrada</h2>
                </div>
                <div class="content">
                    <p>Hola <strong>'.$nombre.'</strong>,</p>
                    
                    <div class="info-box">
                        <p>Se ha registrado automáticamente tu Gasto recurrente:</p>
                        <div class="amount">$'.number_format($monto, 2).'</div>
                        <p><strong>Concepto:</strong> '.$detalle.'</p>
                        <p><strong>Tu rol:</strong> Comprador</p>
                        <p><strong>Fecha:</strong> '.date('d/m/Y').'</p>
                        <p style="margin-top: 15px; padding: 10px; background: #fff; border-radius: 4px; border-left: 4px solid #2196F3;">
                            <strong>Nota:</strong> Los deudores han sido notificados automáticamente de sus partes correspondientes.
                        </p>
                    </div>
                    
                    <div style="text-align: center;">
                        <a href="https://jtabasco.com/gasto" class="action-button">Ver Detalles</a>
                    </div>
                </div>
                <div class="footer">
                    <p>Este es un mensaje automático del Sistema de Gestión de Gastos.<br>
                    Por favor, no responda a este correo.</p>
                </div>
            </div>
        </body>
        </html>';
        
        $asunto = "Gasto Recurrente Registrada - " . $detalle;
    } else {
        // Correo para el deudor
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
                .info-box { 
                    background: #fff3e0; 
                    border: 1px solid #FF9800; 
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
                    <h2>🔄 Gasto Recurrente Generado</h2>
                </div>
                <div class="content">
                    <p>Hola <strong>'.$nombre.'</strong>,</p>
                    
                    <div class="info-box">
                        <p>Se ha generado automáticamente un gasto recurrente que te involucra:</p>
                        <div class="amount">$'.number_format($monto, 2).'</div>
                        <p><strong>Concepto:</strong> '.$detalle.'</p>
                        <p><strong>Tu rol:</strong> Deudor</p>
                        <p><strong>Fecha:</strong> '.date('d/m/Y').'</p>
                        <p style="margin-top: 15px; padding: 10px; background: #fff; border-radius: 4px; border-left: 4px solid #FF9800;">
                            <strong>Acción requerida:</strong> Por favor, actualiza el pago cuando sea posible.
                        </p>
                    </div>
                    
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
        
        $asunto = "Gasto Recurrente - " . $detalle;
    }
    
    // Insertar en la tabla de correos pendientes con fecha de México
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
    $insert_sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES (?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("ssss", $email, $asunto, $mensaje, $fecha_creacion);
    $insert_stmt->execute();
    $insert_stmt->close();
}

// Ejecutar la generación de gastos recurrentes
$gastos_generados = generarGastosRecurrentes($conn);

// Devolver respuesta en formato JSON
$response = array(
    'success' => true,
    'message' => 'Gastos recurrentes procesados correctamente',
    'gastos_generados' => $gastos_generados,
    'fecha_ejecucion' => date('Y-m-d H:i:s')
);

echo json_encode($response);

// Cerrar la conexión principal
$conn->close();
?> 