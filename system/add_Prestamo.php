<?php 
session_start();
include "../conexion.php";
//require_once '../utils/Mailer.php';

// Configurar zona horaria de México para que la fecha se guarde correctamente


$Comprador = $_SESSION['idUser'];
$Detalle = $_POST['Detalle'];
$Monto = $_POST['Monto'];
$Fecha = $_POST['Fecha'];
$Deudor = $_POST['Deudor'];
$Cat = 6;
$compra='N';
$pagado='No';
//insertamos duedor en tdetalle_gasto
$sql = "INSERT INTO  tdetalle_gasto (deudor,monto,pagado,fecha) 
				VALUES('$Deudor','$Monto','$pagado','$Fecha')";
				
		// Ejeuctar el SQL
		$conn->query($sql);

$sql = "INSERT INTO  Gastos (comprador,detalle,Monto,fecha,idcat,compra) 
        VALUES('$Comprador','$Detalle','$Monto','$Fecha','$Cat','$compra')";
        
if ($conn->query($sql)) {
    // Obtener el id del último gasto
    $sql = mysqli_query($conn, "SELECT MAX(id) as maxid FROM `Gastos` WHERE 1");
    $result = mysqli_fetch_array($sql);
    $maxid = $result['maxid'];
    
    // Actualizar y procesar detalles del gasto
    $sql = "UPDATE `tdetalle_gasto` SET `idgasto`=" . $maxid;
    $conn->query($sql);

    $sql = "INSERT INTO detalle_gasto (idgasto,deudor,monto,fecha,pagado)
            SELECT idgasto,deudor,monto,fecha,pagado
            FROM tdetalle_gasto";
    $conn->query($sql);

    // $sql = "UPDATE detalle_gasto c 
    //         INNER JOIN (SELECT id, deuda FROM deudaXcompra) x 
    //         ON c.idgasto = x.id
    //         SET c.monto=x.deuda";
    // $conn->query($sql);

    $sql = "DELETE FROM tdetalle_gasto";
    $conn->query($sql);

    // Incluir funciones helper para MMS
    include 'functions_mms.php';
    
    // Obtener información del comprador
    $sql = mysqli_query($conn, "SELECT Gastos.id, Gastos.detalle, categoria.categoria, Gastos.Monto, Gastos.fecha, 
            usuarios.nombre as comprador, usuarios.email 
            FROM `Gastos` 
            INNER JOIN usuarios 
            inner join categoria
            WHERE usuarios.id=Gastos.comprador and Gastos.idcat=categoria.id AND Gastos.id='$maxid'");
    $result = mysqli_fetch_array($sql);
    
    $comprador = $result['comprador'];
    $compradoremail = $result['email'];
    $datalle = $result['detalle'];
    $monto = $result['Monto'];
    $fecha = $result['fecha'];
    $categoria = $result['categoria'];

    // Obtener información de los deudores
    $sql = "SELECT detalle_gasto.idgasto, detalle_gasto.monto, 
            usuarios.nombre as deudor, usuarios.email, usuarios.tel, usuarios.compania_telefonica_id 
            FROM detalle_gasto 
            INNER JOIN usuarios 
            WHERE usuarios.id=detalle_gasto.deudor 
            AND detalle_gasto.idgasto='$maxid'";
    $query = $conn->query($sql);

    $to = "";
    $deudores = "";
    
    while($data = $query->fetch_object()) {
        $to .= $data->email . ",";
        $deudores .= $data->deudor . ", ";
        $deuda = $data->monto;

		$sqlo = mysqli_query($conn,"SELECT * FROM `deudas` WHERE duedor='".$data->deudor."'");
		$resultado = mysqli_fetch_array($sqlo);
		$tdeuda = $resultado['Deuda'];

        // Generar mensaje MMS enriquecido
        $datos_prestamo = [
            'categoria' => $categoria,
            'detalle' => $datalle,
            'monto_total' => $monto,
            'deuda_personal' => $deuda,
            'fecha' => $fecha,
            'comprador' => $comprador
        ];
        
        $messageMMS = generarMensajeMMSEnriquecido('prestamo', $data->deudor, $datos_prestamo, $conn);
        
        // Guardar MMS enriquecido en correos pendientes usando compañía
        $subject = "NUEVO PRESTAMO REGISTRADO";
        $mms_guardado = guardarMMSPendienteConCompania($data->tel, $data->compania_telefonica_id, $subject, $messageMMS, $conn);
        
        // Si no se pudo guardar MMS, registrar en log o manejar según necesidad
        if (!$mms_guardado) {
            // Opcional: Registrar que no se pudo enviar MMS por compañía inactiva
            error_log("No se pudo enviar MMS a {$data->deudor} - Compañía inactiva");
        }
        
        // Generar mensaje HTML personalizado para este deudor específico
        $messageDeudorIndividual = generarMensajeDeudorIndividual($data->deudor, $deuda, $datalle, $fecha, $categoria, $monto, $comprador, $conn);
        
        // Guardar correo individual para este deudor
        $fecha_creacion = date('Y-m-d H:i:s');
        $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES ('$data->email', 'Notificación de deuda nueva', '$messageDeudorIndividual', '$fecha_creacion')";
        $conn->query($sql);
    }

    $deudores = substr($deudores, 0, -2) . ".";
    $to = substr($to, 0, -1);

    // Mensaje HTML para el comprador
    $messageComprador = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Préstamo - Gastos Familiares</title>
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
                background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
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
                background: #F0F9FF;
                border-left: 4px solid #3B82F6;
                padding: 20px;
                margin: 25px 0;
                border-radius: 0 8px 8px 0;
            }
            .transaction-title {
                font-size: 1.1rem;
                color: #1E40AF;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .transaction-details {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 20px;
            }
            .detail-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #E5E7EB;
            }
            .detail-item:last-child {
                border-bottom: none;
            }
            .detail-label {
                color: #6B7280;
                font-weight: 500;
            }
            .detail-value {
                color: #1F2937;
                font-weight: 600;
            }
            .amount {
                color: #059669;
                font-weight: 600;
            }
            .participants-section {
                margin: 25px 0;
            }
            .section-title {
                font-size: 1rem;
                color: #374151;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .participant-list {
                background: #F9FAFB;
                border-radius: 8px;
                padding: 15px;
            }
            .participant-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid #E5E7EB;
            }
            .participant-item:last-child {
                border-bottom: none;
            }
            .participant-name {
                color: #1F2937;
                font-weight: 500;
            }
            .participant-role {
                color: #6B7280;
                font-size: 0.9rem;
            }
            .debt-summary {
                background: #ECFDF5;
                border: 1px solid #A7F3D0;
                border-radius: 8px;
                padding: 20px;
                margin: 25px 0;
            }
            .debt-summary h4 {
                color: #065F46;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .debt-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                color: #047857;
                border-bottom: 1px solid #D1FAE5;
            }
            .debt-item:last-child {
                border-bottom: none;
                font-weight: 600;
                font-size: 1.1rem;
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
                    ¡Hola '.$comprador.'! 👋
                </div>
                
                <p style="color: #374151; line-height: 1.6; margin-bottom: 20px;">
                    Se ha registrado un nuevo préstamo en tu grupo familiar. Aquí tienes todos los detalles:
                </p>

                <div class="transaction-info">
                    <div class="transaction-title">💳 Préstamo - '.$datalle.'</div>
                    <div class="transaction-details">
                        <div class="detail-item">
                            <span class="detail-label">Fecha:</span>
                            <span class="detail-value">'.$fecha.'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Categoría:</span>
                            <span class="detail-value">'.$categoria.'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Monto Total:</span>
                            <span class="detail-value">$'.number_format($monto, 2).'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Deudor:</span>
                            <span class="detail-value">'.$deudores.'</span>
                        </div>
                    </div>
                </div>

                <div class="participants-section">
                    <div class="section-title">👥 Participantes</div>
                    <div class="participant-list">
                        <div class="participant-item">
                            <div>
                                <div class="participant-name">'.$comprador.'</div>
                                <div class="participant-role">Prestamista</div>
                            </div>
                        </div>
                        <div class="participant-item">
                            <div>
                                <div class="participant-name">'.$deudores.'</div>
                                <div class="participant-role">Deudor</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="debt-summary">
                    <h4>📊 Resumen de Deudas Pendientes</h4>';
                    
                    // Obtener deudas que debe el prestamista a otros miembros de la familia
                    $sql_deudas_prestamista_debe = "SELECT 
                                                       dcd.comprador as acreedor_nombre,
                                                       dcd.total as total_deuda
                                                  FROM deudas_comprador_deudor dcd
                                                  INNER JOIN usuarios u ON u.nombre = dcd.comprador
                                                  INNER JOIN usuarios prestamista ON prestamista.nombre = '$comprador'
                                                  WHERE dcd.duedor = '$comprador' 
                                                  AND dcd.total > 0
                                                  AND u.familia_id = prestamista.familia_id
                                                  ORDER BY dcd.total DESC";
                    $query_deudas_prestamista_debe = $conn->query($sql_deudas_prestamista_debe);
                    
                    $total_deudas_prestamista_debe = 0;
                    if ($query_deudas_prestamista_debe->num_rows > 0) {
                        $messageComprador .= '
                    <div class="debt-item" style="font-weight: 600; color: #DC2626; border-bottom: 2px solid #FEE2E2;">
                        <span>🔴 DEBO:</span>
                        <span></span>
                    </div>';
                        while($deuda_data_prestamista_debe = $query_deudas_prestamista_debe->fetch_object()) {
                            $messageComprador .= '
                    <div class="debt-item">
                        <span>'.$deuda_data_prestamista_debe->acreedor_nombre.':</span>
                        <span class="amount" style="color: #DC2626;">$'.number_format($deuda_data_prestamista_debe->total_deuda, 2).'</span>
                    </div>';
                            $total_deudas_prestamista_debe += $deuda_data_prestamista_debe->total_deuda;
                        }
                        $messageComprador .= '
                    <div class="debt-item" style="font-weight: 600; border-bottom: 2px solid #FEE2E2;">
                        <span>Total que debo:</span>
                        <span class="amount" style="color: #DC2626;">$'.number_format($total_deudas_prestamista_debe, 2).'</span>
                    </div>';
                    }
                    
                    // Obtener deudas que le deben al prestamista
                    $sql_deudas_prestamista_cobrar = "SELECT 
                                                       dcd.duedor as deudor_nombre,
                                                       dcd.total as total_deuda
                                                  FROM deudas_comprador_deudor dcd
                                                  INNER JOIN usuarios u ON u.nombre = dcd.duedor
                                                  INNER JOIN usuarios prestamista ON prestamista.nombre = '$comprador'
                                                  WHERE dcd.comprador = '$comprador' 
                                                  AND dcd.total > 0
                                                  AND u.familia_id = prestamista.familia_id
                                                  ORDER BY dcd.total DESC";
                    $query_deudas_prestamista_cobrar = $conn->query($sql_deudas_prestamista_cobrar);
                    
                    $total_deudas_prestamista_cobrar = 0;
                    if ($query_deudas_prestamista_cobrar->num_rows > 0) {
                        $messageComprador .= '
                    <div class="debt-item" style="font-weight: 600; color: #059669; border-bottom: 2px solid #D1FAE5;">
                        <span>🟢 ME DEBEN:</span>
                        <span></span>
                    </div>';
                        while($deuda_data_prestamista_cobrar = $query_deudas_prestamista_cobrar->fetch_object()) {
                            $messageComprador .= '
                    <div class="debt-item">
                        <span>'.$deuda_data_prestamista_cobrar->deudor_nombre.':</span>
                        <span class="amount" style="color: #059669;">$'.number_format($deuda_data_prestamista_cobrar->total_deuda, 2).'</span>
                    </div>';
                            $total_deudas_prestamista_cobrar += $deuda_data_prestamista_cobrar->total_deuda;
                        }
                        $messageComprador .= '
                    <div class="debt-item" style="font-weight: 600; border-bottom: 2px solid #D1FAE5;">
                        <span>Total que me deben:</span>
                        <span class="amount" style="color: #059669;">$'.number_format($total_deudas_prestamista_cobrar, 2).'</span>
                    </div>';
                    }
                    
                    // Calcular neto
                    $neto_prestamista = $total_deudas_prestamista_cobrar - $total_deudas_prestamista_debe;
                    $messageComprador .= '
                    <div class="debt-item" style="font-weight: 700; font-size: 1.2rem; background: #F3F4F6; padding: 10px; border-radius: 5px; margin-top: 10px;">
                        <span>💰 NETO:</span>
                        <span class="amount" style="color: '.($neto_prestamista >= 0 ? '#059669' : '#DC2626').';">$'.number_format(abs($neto_prestamista), 2).' '.($neto_prestamista >= 0 ? '(A mi favor)' : '(En contra)').'</span>
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



    // Enviar correos con formato HTML con fecha de México
    //Mailer::enviarCorreo($compradoremail, "Confirmación de prestamo registrado", $messageComprador);
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
    $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES ('$compradoremail', 'Confirmación de prestamo registrado', '$messageComprador', '$fecha_creacion')";
    $conn->query($sql);

    echo json_encode('ok');
} else {
    echo json_encode('No ok');    
}

// Función para generar mensaje HTML individual para cada deudor
function generarMensajeDeudorIndividual($deudor_nombre, $deuda_personal, $datalle, $fecha, $categoria, $monto, $comprador, $conn) {
    $messageDeudor = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Préstamo - Gastos Familiares</title>
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
                background: linear-gradient(135deg, #1E40AF 0%, #3B82F6 100%);
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
                background: #F0F9FF;
                border-left: 4px solid #3B82F6;
                padding: 20px;
                margin: 25px 0;
                border-radius: 0 8px 8px 0;
            }
            .transaction-title {
                font-size: 1.1rem;
                color: #1E40AF;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .transaction-details {
                display: flex;
                flex-direction: column;
                gap: 12px;
                margin-bottom: 20px;
            }
            .detail-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 10px 0;
                border-bottom: 1px solid #E5E7EB;
            }
            .detail-item:last-child {
                border-bottom: none;
            }
            .detail-label {
                color: #6B7280;
                font-weight: 500;
            }
            .detail-value {
                color: #1F2937;
                font-weight: 600;
            }
            .amount {
                color: #059669;
                font-weight: 600;
            }
            .participants-section {
                margin: 25px 0;
            }
            .section-title {
                font-size: 1rem;
                color: #374151;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .participant-list {
                background: #F9FAFB;
                border-radius: 8px;
                padding: 15px;
            }
            .participant-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 0;
                border-bottom: 1px solid #E5E7EB;
            }
            .participant-item:last-child {
                border-bottom: none;
            }
            .participant-name {
                color: #1F2937;
                font-weight: 500;
            }
            .participant-role {
                color: #6B7280;
                font-size: 0.9rem;
            }
            .debt-summary {
                background: #ECFDF5;
                border: 1px solid #A7F3D0;
                border-radius: 8px;
                padding: 20px;
                margin: 25px 0;
            }
            .debt-summary h4 {
                color: #065F46;
                margin-bottom: 15px;
                font-weight: 600;
            }
            .debt-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                color: #047857;
                border-bottom: 1px solid #D1FAE5;
            }
            .debt-item:last-child {
                border-bottom: none;
                font-weight: 600;
                font-size: 1.1rem;
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
                    Se ha registrado un nuevo préstamo que requiere tu atención:
                </p>

                <div class="transaction-info">
                    <div class="transaction-title">💳 Préstamo - '.$datalle.'</div>
                    <div class="transaction-details">
                        <div class="detail-item">
                            <span class="detail-label">Fecha:</span>
                            <span class="detail-value">'.$fecha.'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Categoría:</span>
                            <span class="detail-value">'.$categoria.'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Monto Total:</span>
                            <span class="detail-value">$'.number_format($monto, 2).'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tu Deuda:</span>
                            <span class="detail-value amount">$'.number_format($deuda_personal, 2).'</span>
                        </div>
                    </div>
                </div>

                <div class="participants-section">
                    <div class="section-title">👥 Participantes</div>
                    <div class="participant-list">
                        <div class="participant-item">
                            <div>
                                <div class="participant-name">'.$comprador.'</div>
                                <div class="participant-role">Prestamista</div>
                            </div>
                        </div>
                        <div class="participant-item">
                            <div>
                                <div class="participant-name">'.$deudor_nombre.'</div>
                                <div class="participant-role">Deudor</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="debt-summary">
                    <h4>📊 Resumen de Deudas Pendientes</h4>';
                    
                    // Obtener deudas que debe el deudor a otros miembros de la familia
                    $sql_deudas_deudor_debe = "SELECT 
                                                   dcd.comprador as acreedor_nombre,
                                                   dcd.total as total_deuda
                                              FROM deudas_comprador_deudor dcd
                                              INNER JOIN usuarios u ON u.nombre = dcd.comprador
                                              INNER JOIN usuarios deudor_actual ON deudor_actual.nombre = '$deudor_nombre'
                                              WHERE dcd.duedor = '$deudor_nombre' 
                                              AND dcd.total > 0
                                              AND u.familia_id = deudor_actual.familia_id
                                              ORDER BY dcd.total DESC";
                    $query_deudas_deudor_debe = $conn->query($sql_deudas_deudor_debe);
                    
                    $total_deudas_deudor_debe = 0;
                    if ($query_deudas_deudor_debe->num_rows > 0) {
                        $messageDeudor .= '
                    <div class="debt-item" style="font-weight: 600; color: #DC2626; border-bottom: 2px solid #FEE2E2;">
                        <span>🔴 DEBO:</span>
                        <span></span>
                    </div>';
                        while($deuda_data_deudor_debe = $query_deudas_deudor_debe->fetch_object()) {
                            $messageDeudor .= '
                    <div class="debt-item">
                        <span>'.$deuda_data_deudor_debe->acreedor_nombre.':</span>
                        <span class="amount" style="color: #DC2626;">$'.number_format($deuda_data_deudor_debe->total_deuda, 2).'</span>
                    </div>';
                            $total_deudas_deudor_debe += $deuda_data_deudor_debe->total_deuda;
                        }
                        $messageDeudor .= '
                    <div class="debt-item" style="font-weight: 600; border-bottom: 2px solid #FEE2E2;">
                        <span>Total que debo:</span>
                        <span class="amount" style="color: #DC2626;">$'.number_format($total_deudas_deudor_debe, 2).'</span>
                    </div>';
                    }
                    
                    // Obtener deudas que le deben al deudor
                    $sql_deudas_deudor_cobrar = "SELECT 
                                                   dcd.duedor as deudor_nombre,
                                                   dcd.total as total_deuda
                                              FROM deudas_comprador_deudor dcd
                                              INNER JOIN usuarios u ON u.nombre = dcd.duedor
                                              INNER JOIN usuarios deudor_actual ON deudor_actual.nombre = '$deudor_nombre'
                                              WHERE dcd.comprador = '$deudor_nombre' 
                                              AND dcd.total > 0
                                              AND u.familia_id = deudor_actual.familia_id
                                              ORDER BY dcd.total DESC";
                    $query_deudas_deudor_cobrar = $conn->query($sql_deudas_deudor_cobrar);
                    
                    $total_deudas_deudor_cobrar = 0;
                    if ($query_deudas_deudor_cobrar->num_rows > 0) {
                        $messageDeudor .= '
                    <div class="debt-item" style="font-weight: 600; color: #059669; border-bottom: 2px solid #D1FAE5;">
                        <span>🟢 ME DEBEN:</span>
                        <span></span>
                    </div>';
                        while($deuda_data_deudor_cobrar = $query_deudas_deudor_cobrar->fetch_object()) {
                            $messageDeudor .= '
                    <div class="debt-item">
                        <span>'.$deuda_data_deudor_cobrar->deudor_nombre.':</span>
                        <span class="amount" style="color: #059669;">$'.number_format($deuda_data_deudor_cobrar->total_deuda, 2).'</span>
                    </div>';
                            $total_deudas_deudor_cobrar += $deuda_data_deudor_cobrar->total_deuda;
                        }
                        $messageDeudor .= '
                    <div class="debt-item" style="font-weight: 600; border-bottom: 2px solid #D1FAE5;">
                        <span>Total que me deben:</span>
                        <span class="amount" style="color: #059669;">$'.number_format($total_deudas_deudor_cobrar, 2).'</span>
                    </div>';
                    }
                    
                    // Calcular neto
                    $neto_deudor = $total_deudas_deudor_cobrar - $total_deudas_deudor_debe;
                    $messageDeudor .= '
                    <div class="debt-item" style="font-weight: 700; font-size: 1.2rem; background: #F3F4F6; padding: 10px; border-radius: 5px; margin-top: 10px;">
                        <span>💰 NETO:</span>
                        <span class="amount" style="color: '.($neto_deudor >= 0 ? '#059669' : '#DC2626').';">$'.number_format(abs($neto_deudor), 2).' '.($neto_deudor >= 0 ? '(A mi favor)' : '(En contra)').'</span>
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

    return $messageDeudor;
}

$conn->close();
?>