<?php


session_start();
include "../conexion.php";
//require_once '../utils/Mailer.php';

try {
    if(isset($_POST['deudor_id']) && isset($_POST['deudas'])) {
        $deudor_id = $_POST['deudor_id'];
        $deudas = $_POST['deudas'];
        
        // Convertir el array de deudas a una cadena para la consulta
        $deudas_str = implode(',', array_map('intval', $deudas));
        
        // Actualizar las deudas seleccionadas
        $sql = "UPDATE detalle_gasto 
                SET pagado = 'Si' 
                WHERE id IN ($deudas_str)";
                
        $stmt = $conn->query($sql);
        
        // Debug information
        $debug_info = [
            'sql' => $sql,
            'stmt' => $stmt,
            'affected_rows' => $conn->affected_rows,
            'error' => $conn->error
        ];
        
        // Agregar verificación de errores
        if ($stmt === false) {
            echo json_encode(array(
                "success" => false, 
                "message" => "Error en la consulta: " . $conn->error,
                "debug" => $debug_info
            ));
            exit;
        }
        
        // Verificar si se actualizaron registros
        if ($stmt !== false && $conn->affected_rows > 0) {
            // 1. Obtener email y nombre del deudor
            $sql_user = "SELECT nombre, email FROM usuarios WHERE id = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("i", $deudor_id);
            $stmt_user->execute();
            $result_user = $stmt_user->get_result();
            $user = $result_user->fetch_assoc();
            $deudor_nombre = $user['nombre'];
            $deudor_email = $user['email'];

            // 2. Obtener resumen de las deudas pagadas
            $sql_detalle = "SELECT Gastos.detalle, detalle_gasto.monto, detalle_gasto.fecha FROM detalle_gasto
                            inner join Gastos on Gastos.id=detalle_gasto.idgasto 
                            WHERE detalle_gasto.id IN ($deudas_str)";
            $result_detalle = $conn->query($sql_detalle);

            $resumen = "";
            $total_pagado = 0;
            while($row = $result_detalle->fetch_assoc()) {
                $resumen .= "
                <div style='background:#fff;border:1px solid #eee;border-radius:12px;padding:18px 20px;margin-bottom:18px;'>
                    <div style='font-weight:bold;font-size:1.1em;margin-bottom:6px;'>{$row['detalle']}</div>
                    <div style='color:#444;'><b>Monto:</b> $".number_format($row['monto'],2)."</div>
                    <div style='color:#444;'><b>Fecha:</b> {$row['fecha']}</div>
                </div>
                ";
                $total_pagado += $row['monto'];
            }

            // 3. Calcular el monto restante
            $sql_restante = "SELECT SUM(monto) as restante FROM detalle_gasto WHERE deudor = ? AND pagado = 'No'";
            $stmt_restante = $conn->prepare($sql_restante);
            $stmt_restante->bind_param("i", $deudor_id);
            $stmt_restante->execute();
            $result_restante = $stmt_restante->get_result();
            $restante = $result_restante->fetch_assoc()['restante'];
            if(!$restante) $restante = 0;

            // 4. Generar el correo con la plantilla moderna
            $subject = "Confirmación de Pagos - Gastos Familiares";
            $body = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Confirmación de Pagos - Gastos Familiares</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                background: #f8fafc;
                line-height: 1.6;
            }
            .email-container {
                max-width: 600px;
                margin: 0 auto;
                background: white;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.08);
                overflow: hidden;
            }
            .email-header {
                background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .logo {
                font-size: 2rem;
                font-weight: 700;
                margin-bottom: 10px;
                letter-spacing: -1px;
            }
            .logo-subtitle {
                font-size: 0.9rem;
                opacity: 0.9;
                font-weight: 300;
            }
            .email-content {
                padding: 40px 30px;
            }
            .greeting {
                font-size: 1.2rem;
                color: #1F2937;
                margin-bottom: 25px;
                font-weight: 500;
            }
            .transaction-info {
                background: #ECFDF5;
                border-left: 4px solid #10B981;
                padding: 20px;
                margin: 25px 0;
                border-radius: 0 8px 8px 0;
            }
            .transaction-title {
                font-size: 1.1rem;
                color: #065F46;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .payment-summary {
                background: #F9FAFB;
                border-radius: 8px;
                padding: 20px;
                margin: 25px 0;
            }
            .payment-item {
                background: #FFFFFF;
                border: 1px solid #E5E7EB;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 12px;
            }
            .payment-item:last-child {
                margin-bottom: 0;
            }
            .payment-title {
                font-size: 1rem;
                color: #1F2937;
                margin-bottom: 8px;
                font-weight: 600;
            }
            .payment-details {
                display: flex;
                justify-content: space-between;
                align-items: center;
                color: #6B7280;
                font-size: 0.9rem;
            }
            .amount {
                color: #059669;
                font-weight: 600;
            }
            .total-section {
                background: #ECFDF5;
                border: 1px solid #A7F3D0;
                border-radius: 8px;
                padding: 20px;
                margin: 25px 0;
                text-align: center;
            }
            .total-title {
                color: #065F46;
                margin-bottom: 10px;
                font-weight: 600;
            }
            .total-amount {
                font-size: 2rem;
                color: #059669;
                font-weight: 700;
                margin-bottom: 10px;
            }
            .remaining-amount {
                color: #6B7280;
                font-size: 0.9rem;
            }
            .cta-section {
                text-align: center;
                margin: 30px 0;
            }
            .cta-button {
                display: inline-block;
                background: linear-gradient(135deg, #10B981 0%, #059669 100%);
                color: white;
                padding: 12px 30px;
                border-radius: 25px;
                text-decoration: none;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            }
            .cta-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            }
            .email-footer {
                background: #F3F4F6;
                padding: 25px;
                text-align: center;
                color: #6B7280;
                font-size: 0.9rem;
            }
            .footer-link {
                color: #3B82F6;
                text-decoration: none;
                font-weight: 500;
            }
            .footer-link:hover {
                text-decoration: underline;
            }
            @media (max-width: 768px) {
                .email-content {
                    padding: 20px 15px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-container">
            <div class="email-header">
                <div class="logo">💰 Gastos Familiares</div>
                <div class="logo-subtitle">Gestión Financiera Inteligente</div>
            </div>
            
            <div class="email-content">
                <div class="greeting">
                    ¡Hola '.$deudor_nombre.'! 👋
                </div>
                
                <p style="color: #374151; line-height: 1.6; margin-bottom: 20px;">
                    Se han confirmado los siguientes pagos en tu cuenta. Aquí tienes el resumen completo:
                </p>

                <div class="transaction-info">
                    <div class="transaction-title">✅ Pagos Confirmados</div>
                    <p style="color: #065F46; margin: 0;">
                        Se han procesado exitosamente los pagos de las siguientes deudas:
                    </p>
                </div>

                <div class="payment-summary">
                    <div style="font-size: 1rem; color: #374151; margin-bottom: 15px; font-weight: 600;">
                        📋 Detalle de Pagos
                    </div>';
                    
                    // Generar lista de pagos
                    $result_detalle->data_seek(0); // Resetear el puntero del resultado
                    while($row = $result_detalle->fetch_assoc()) {
                        $body .= '
                    <div class="payment-item">
                        <div class="payment-title">'.$row['detalle'].'</div>
                        <div class="payment-details">
                            <span>Fecha: '.$row['fecha'].'</span>
                            <span class="amount">$'.number_format($row['monto'], 2).'</span>
                        </div>
                    </div>';
                    }
                    
                    $body .= '
                </div>

                <div class="total-section">
                    <div class="total-title">💰 Total Pagado</div>
                    <div class="total-amount">$'.number_format($total_pagado, 2).'</div>
                    <div class="remaining-amount">
                        <strong>Monto restante:</strong> $'.number_format($restante, 2).'
                    </div>
                </div>

                <div class="cta-section">
                    <a href="https://jtabasco.com/gasto" class="cta-button">
                        📱 Ver Detalles en la App
                    </a>
                </div>

                <p style="color: #6B7280; font-size: 0.9rem; margin-top: 20px; line-height: 1.5;">
                    💡 <strong>Consejo:</strong> Mantén tus pagos al día para evitar acumular deudas. 
                    Puedes realizar pagos parciales en cualquier momento.
                </p>
            </div>

            <div class="email-footer">
                <p>Este correo fue enviado automáticamente por el sistema de Gastos Familiares.</p>
                <p>Visita <a href="https://jtabasco.com/gasto" class="footer-link">Gastos Familiares</a> para más información</p>
            </div>
        </div>
    </body>
    </html>';

            // Escapar los valores para la consulta SQL
            $deudor_email = mysqli_real_escape_string($conn, $deudor_email);
            $subject = mysqli_real_escape_string($conn, $subject);
            $body = mysqli_real_escape_string($conn, $body);

            $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo) VALUES ('$deudor_email', '$subject', '$body')";
            $conn->query($sql);

            echo json_encode(array("success" => true, "message" => "Deudas actualizadas correctamente y correo enviado"));
        } else {
            echo json_encode(array("success" => false, "message" => "Datos incompletos"));
        }
    } else {
        echo json_encode(array("success" => false, "message" => "Datos incompletos"));
    }
} catch (Exception $e) {
    echo json_encode(array(
        "success" => false,
        "message" => "Error: " . $e->getMessage(),
        "debug" => [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]
    ));
}

// Cerrar la conexión al final del script
$conn->close();
?> 