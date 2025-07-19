<?php
session_start();
include "../conexion.php";

// Configurar zona horaria de México para que la fecha se guarde correctamente

// Incluir funciones helper para MMS
include 'functions_mms.php';

if (isset($_POST['deudor']) && isset($_POST['acreedor'])) {
    $deudor = $_POST['deudor'];
    $acreedor = $_POST['acreedor'];
    
    // 1. Lo que el deudor le debe al acreedor (Por Pagar)
    $query_pagar = "SELECT SUM(debe) as total_pagar FROM deudas_detalladas WHERE duedor = ? AND comprador = ?";
    $stmt = $conn->prepare($query_pagar);
    $stmt->bind_param("ss", $deudor, $acreedor);
    $stmt->execute();
    $result = $stmt->get_result();
    $row_pagar = $result->fetch_assoc();
    $por_pagar = floatval($row_pagar['total_pagar']);
    $result->free();
    $stmt->close();

    // 2. Lo que el acreedor le debe al deudor (Por Cobrar)
    $query_cobrar = "SELECT SUM(debe) as total_cobrar FROM deudas_detalladas WHERE duedor = ? AND comprador = ?";
    $stmt = $conn->prepare($query_cobrar);
    $stmt->bind_param("ss", $acreedor, $deudor);
    $stmt->execute();
    $result = $stmt->get_result();
    $row_cobrar = $result->fetch_assoc();
    $por_cobrar = floatval($row_cobrar['total_cobrar']);
    $result->free();
    $stmt->close();

    // 3. Neto (lo que realmente se debe entre ambos)
    $neto = $por_pagar - $por_cobrar;

    // Obtener email y teléfono del acreedor
    $email_query = "SELECT email, tel, compania_telefonica_id FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($email_query);
    $stmt->bind_param("s", $acreedor);
    $stmt->execute();
    $email_result = $stmt->get_result();
    $acreedor_data = $email_result->fetch_assoc();
    $email = $acreedor_data['email'];
    $tel = $acreedor_data['tel'];
    $compania_id = $acreedor_data['compania_telefonica_id'];
    $email_result->free();
    $stmt->close();

    // Texto para el neto
    if ($neto > 0) {
        $neto_texto = "Neto pagado a $acreedor";
        $neto_color = "#d32f2f";
    } elseif ($neto < 0) {
        $neto_texto = "$acreedor te debe";
        $neto_color = "#388e3c";
    } else {
        $neto_texto = "No hay deudas netas entre ustedes";
        $neto_color = "#333";
    }

    // Generar mensaje MMS enriquecido para deudas netas
    $datos_deudas_netas = [
        'deudor' => $deudor,
        'acreedor' => $acreedor,
        'por_pagar' => $por_pagar,
        'por_cobrar' => $por_cobrar,
        'neto' => $neto,
        'neto_texto' => $neto_texto,
        'fecha' => date('Y-m-d')
    ];
    
    $messageMMS = generarMensajeMMSDeudasNetas($datos_deudas_netas, $conn);
    
    // Guardar MMS enriquecido en correos pendientes usando compañía
    $subject = "RESUMEN DE DEUDAS NETAS";
    $mms_guardado = guardarMMSPendienteConCompania($tel, $compania_id, $subject, $messageMMS, $conn);
    
    // Si no se pudo guardar MMS, registrar en log o manejar según necesidad
    if (!$mms_guardado) {
        // Opcional: Registrar que no se pudo enviar MMS por compañía inactiva
        error_log("No se pudo enviar MMS de deudas netas a $acreedor - Compañía inactiva");
    }

    // Preparar el mensaje HTML con formato visual moderno y bordes
    $htmlBody = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
    </head>
    <body style="margin:0;padding:0;background:#181818;">
    <div style="max-width:600px;margin:0 auto;font-family:sans-serif;">
      <div style="background:#219653;color:#fff;padding:24px 0;text-align:center;font-size:2em;font-weight:bold;border-radius:12px 12px 0 0;">
        Resumen de deudas netas
      </div>
      <div style="padding:24px;background:#181818;">
        <div style="background:#fff;border-radius:12px;padding:18px 20px;margin-bottom:18px;border:2px solid #219653;">
          <div style="font-weight:bold;font-size:1.1em;color:#222;">Por Pagar a '.$acreedor.'</div>
          <div style="color:#d32f2f;font-size:1.3em;font-weight:bold;">$'.number_format($por_pagar, 2).'</div>
        </div>
        <div style="background:#fff;border-radius:12px;padding:18px 20px;margin-bottom:18px;border:2px solid #219653;">
          <div style="font-weight:bold;font-size:1.1em;color:#222;">Por Cobrar de '.$acreedor.'</div>
          <div style="color:#388e3c;font-size:1.3em;font-weight:bold;">$'.number_format($por_cobrar, 2).'</div>
        </div>
        <div style="background:#f5f5f5;border-radius:12px;padding:18px 20px;margin-bottom:18px;border:2px solid #219653;">
          <div style="font-weight:bold;font-size:1.1em;color:#222;">'.$neto_texto.'</div>
          <div style="color:'.$neto_color.';font-size:1.3em;font-weight:bold;">$'.number_format(abs($neto), 2).'</div>
        </div>
        <div style="margin-top:24px;font-size:1.1em;text-align:center;color:#fff;">
          Ya realicé el pago del neto. Por favor, actualiza el pago de las deudas en la página.
        </div>
      </div>
      <div style="background:#fafafa;color:#888;padding:12px;border-radius:0 0 12px 12px;font-size:0.95em;text-align:center;">
        Este es un mensaje automático, por favor no responda a este correo.<br>
        Sistema de Gestión de Gastos
      </div>
    </div>
    </body>
    </html>';

    // Guardar el correo en la tabla de correos pendientes con fecha de México
    $asunto = "Resumen de Deudas con " . $deudor;
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
    $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $asunto, $htmlBody, $fecha_creacion);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Correo y MMS programados para envío']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al programar el correo']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Faltan parámetros requeridos']);
}

$conn->close();
?> 