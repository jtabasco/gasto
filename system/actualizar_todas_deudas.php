<?php
// Incluir la conexión a la base de datos
include "../conexion.php";
//require_once '../utils/Mailer.php';

// Verificar si se recibieron los parámetros necesarios
if (isset($_POST['deudorId'])) {
    $deudorNombre = $_POST['deudorId'];
    
    // Buscar el ID del deudor en la tabla usuarios
    $sqlBuscarId = "SELECT id, email FROM usuarios WHERE nombre = '$deudorNombre'";
    $resultBuscarId = mysqli_query($conn, $sqlBuscarId);
    
    if ($resultBuscarId && mysqli_num_rows($resultBuscarId) > 0) {
        $row = mysqli_fetch_assoc($resultBuscarId);
        $deudorId = $row['id'];
        $deudorEmail = $row['email'];
        
        // Obtener información sobre las deudas antes de actualizar
        $sqlDeudas = "SELECT dg.idgasto, dg.monto, g.detalle, g.fecha
                      FROM detalle_gasto dg 
                      INNER JOIN Gastos g ON dg.idgasto = g.id 
                      WHERE dg.deudor = '$deudorId' AND dg.pagado = 'No'";
        
        $resultDeudas = mysqli_query($conn, $sqlDeudas);
        
        // Actualizar todas las deudas del deudor usando su ID
        $sql = "UPDATE detalle_gasto SET pagado = 'Si' WHERE deudor = '$deudorId' AND pagado = 'No'";
        $result = mysqli_query($conn, $sql);
        
        if ($result) {
            // Preparar el mensaje HTML para el correo
            $htmlBody = '
            <!DOCTYPE html>
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #4CAF50; color: white; padding: 15px; text-align: center; }
                    .content { padding: 20px; background: #f9f9f9; }
                    .details { margin: 20px 0; }
                    .amount { color: #4CAF50; font-weight: bold; }
                    .footer { text-align: center; padding: 15px; background: #f1f1f1; }
                    .deuda-item { 
                        background-color: #ffffff; 
                        border: 1px solid #dee2e6; 
                        border-radius: 10px; 
                        padding: 15px; 
                        margin-bottom: 15px;
                    }
                    .deuda-header {
                        background-color: #f8f9fa;
                        padding: 10px;
                        border-radius: 5px;
                        margin-bottom: 10px;
                    }
                    .deuda-details {
                        display: grid;
                        grid-template-columns: 1fr 1fr;
                        gap: 10px;
                        padding: 10px;
                    }
                    .deuda-label {
                        font-weight: bold;
                        color: #6c757d;
                    }
                    .deuda-value {
                        color: #212529;
                    }
                    .total-section {
                        background-color: #e9ecef;
                        padding: 15px;
                        border-radius: 5px;
                        margin-top: 20px;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Actualización de Pagos</h2>
                    </div>
                    <div class="content">
                        <p>Hola <strong>'.$deudorNombre.'</strong>,</p>
                        <p>Se han actualizado todas tus deudas pendientes a estado "Pagado".</p>
                        <div style="margin-top: 20px;">';
            
            $totalDeudas = 0;
            while ($deuda = mysqli_fetch_assoc($resultDeudas)) {
                $htmlBody .= '
                    <div class="deuda-item">
                        <div class="deuda-header">
                            <strong>'.$deuda['detalle'].'</strong>
                        </div>
                        <div class="deuda-details">
                            <div>
                                <span class="deuda-label">Monto:</span>
                                <span class="deuda-value">$'.number_format($deuda['monto'], 2).'</span>
                            </div>
                            <div>
                                <span class="deuda-label">Fecha:</span>
                                <span class="deuda-value">'.$deuda['fecha'].'</span>
                            </div>
                        </div>
                    </div>';
                $totalDeudas += $deuda['monto'];
            }
            
            $htmlBody .= '
                        </div>
                        <div class="total-section">
                            <h3>Total de Deudas Actualizadas</h3>
                            <p style="font-size: 24px; color: #28a745; margin: 10px 0;">$'.number_format($totalDeudas, 2).'</p>
                        </div>
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 12px; color: #6c757d; margin-top: 20px;">
                            <p>Este es un mensaje automático, por favor no responda a este correo.</p>
                            <p>Sistema de Gestión de Gastos</p>
                        </div>
                    </div>
                </div>
            </body>
            </html>';
            
            // Enviar correo al deudor
            $asunto = "Actualización de Pagos - Todas sus deudas han sido marcadas como pagadas";
            //Mailer::enviarCorreo($deudorEmail, $asunto, $htmlBody);
            $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo) VALUES ('$deudorEmail', '$asunto', '$htmlBody')";
            $conn->query($sql);
            

            echo json_encode([
                'status' => 'success',
                'message' => 'Todas las deudas han sido actualizadas correctamente'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al actualizar las deudas'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No se encontró el deudor especificado'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'No se recibió el ID del deudor'
    ]);
}

// Cerrar la conexión
mysqli_close($conn);
?> 