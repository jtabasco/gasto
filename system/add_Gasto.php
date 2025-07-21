<?php 
session_start();
include "../conexion.php";
//require_once '../utils/Mailer.php';

// Función para dividir importe con precisión
function dividirImporte(float $importe, int $partes): array {
    if ($partes <= 0) {
        return [];
    }
    
    // Convertir a centimos para precisión
    $total_centimos = (int) round($importe * 100);
    
    // Calcular valor base y resto
    $base_centimos = (int) ($total_centimos / $partes);
    $resto = $total_centimos % $partes;
    
    $resultado = [];
    // Distribuir: los primeros reciben centavo extra (para que el último sea el menor)
    for ($i = 0; $i < $partes; $i++) {
        $centimos_actuales = $base_centimos;
        if ($i < $resto) {
            $centimos_actuales += 1; // Centavo extra para los primeros
        }
        $resultado[] = number_format($centimos_actuales / 100, 2, '.', '');
    }
    
    return $resultado;
}

// Configurar zona horaria de México para que la fecha se guarde correctamente

$Comprador = $_SESSION['idUser'];
$Detalle = $_POST['Detalle'];
$Monto = $_POST['Monto'];
$Fecha = $_POST['Fecha'];
$Cat = $_POST['Cat'];

// Iniciar transacción para evitar condiciones de carrera
$conn->begin_transaction();

try {
    $sql = "INSERT INTO  Gastos (comprador,detalle,Monto,fecha,idcat) 
            VALUES(?,?,?,?,?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isdsi", $Comprador, $Detalle, $Monto, $Fecha, $Cat);
    $stmt->execute();
    
    // Obtener el id del último gasto
    $maxid = $conn->insert_id;
    
    // Actualizar y procesar detalles del gasto de forma atómica
    $sql = "UPDATE `tdetalle_gasto` SET `idgasto`=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maxid);
    $stmt->execute();
    error_log("UPDATE tdetalle_gasto affected rows: " . $stmt->affected_rows);

    $sql = "INSERT INTO detalle_gasto (idgasto,deudor,monto,fecha,pagado)
            SELECT idgasto,deudor,monto,fecha,pagado
            FROM tdetalle_gasto";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    error_log("INSERT detalle_gasto affected rows: " . $stmt->affected_rows);

    // Calcular monto dividido equitativamente
    $sql = "SELECT COUNT(*) as total_deudores FROM detalle_gasto WHERE idgasto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maxid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $total_deudores = $row['total_deudores']+1;
    
    if ($total_deudores > 0) {
        // Usar la función dividirImporte para obtener montos precisos
        $montos_divididos = dividirImporte($Monto, $total_deudores);

        // Obtener los registros de detalle_gasto (tantos como deudores)
        $sql = "SELECT id FROM detalle_gasto WHERE idgasto=? ORDER BY id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maxid);
        $stmt->execute();
        $result_registros = $stmt->get_result();

        $registros = [];
        while ($row_registro = $result_registros->fetch_assoc()) {
            $registros[] = $row_registro['id'];
        }

        // Actualizar cada registro con su monto correspondiente
        foreach ($registros as $index => $id_registro) {
            $monto_actual = $montos_divididos[$index];
            $sql = "UPDATE detalle_gasto SET monto=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("di", $monto_actual, $id_registro);
            $stmt->execute();
        }
    }

    // Solo eliminar registros procesados
    $sql = "DELETE FROM tdetalle_gasto WHERE idgasto=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $maxid);
    $stmt->execute();
    error_log("DELETE tdetalle_gasto affected rows: " . $stmt->affected_rows);

    // Confirmar transacción
    $conn->commit();
    
    // Continuar con el resto del código (envío de correos, etc.)
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
        $datos_gasto = [
            'categoria' => $categoria,
            'detalle' => $datalle,
            'monto_total' => $monto,
            'deuda_personal' => $deuda,
            'fecha' => $fecha,
            'comprador' => $comprador
        ];
        
        $messageMMS = generarMensajeMMSEnriquecido('gasto', $data->deudor, $datos_gasto, $conn);
        
        // Guardar MMS enriquecido en correos pendientes usando compañía
        $subject = "NUEVA COMPRA REGISTRADA";
        $mms_guardado = guardarMMSPendienteConCompania($data->tel, $data->compania_telefonica_id, $subject, $messageMMS, $conn);
        
        // Si no se pudo guardar MMS, registrar en log o manejar según necesidad
        if (!$mms_guardado) {
            // Opcional: Registrar que no se pudo enviar MMS por compañía inactiva
            error_log("No se pudo enviar MMS a {$data->deudor} - Compañía inactiva");
        }
    }

    $deudores = substr($deudores, 0, -2) . ".";
    $to = substr($to, 0, -1);
    $montoComprador = $monto;
    // Mensaje HTML para el comprador
    $messageComprador = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nueva Transacción - Gastos Familiares</title>
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
                    Se ha registrado una nueva transacción en tu grupo familiar. Aquí tienes todos los detalles:
                </p>

                <div class="transaction-info">
                    <div class="transaction-title">🛒 Compra Compartida - '.$datalle.'</div>
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
                            <span class="detail-label">División:</span>
                            <span class="detail-value">Equitativa ('.(count(explode(',', $deudores)) + 1).' personas)</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tu Cuota:</span>
                            <span class="detail-value amount">$'.number_format($deuda, 2).'</span>
                        </div>
                    </div>
                </div>

                <div class="participants-section">
                    <div class="section-title">👥 Participantes</div>
                    <div class="participant-list">
                        <div class="participant-item">
                            <div>
                                <div class="participant-name">'.$comprador.'</div>
                                <div class="participant-role">Comprador</div>
                            </div>
                            <span class="amount">$'.number_format($monto, 2).'</span>
                        </div>
                    </div>
                    <div class="participant-list" style="margin-top: 10px;">
                        <div class="section-title" style="font-size:0.95rem; color:#ffffff; margin-bottom:6px;">Deudores (división equitativa: $'.number_format($deuda, 2).' c/u)</div>';
                        
                        // Generar lista de deudores
                        $deudores_array = explode(',', $deudores);
                        foreach($deudores_array as $deudor) {
                            $deudor = trim($deudor);
                            if($deudor && $deudor != '.') {
                                $messageComprador .= '
                        <div class="participant-item">
                            <div class="participant-name">'.$deudor.'</div>
                        </div>';
                            }
                        }
                        
                        $messageComprador .= '
                    </div>
                </div>

                <div class="debt-summary">
                    <h4>📊 Resumen de Deudas Pendientes</h4>';
                    
                    // Obtener deudas detalladas que tiene el comprador con todos los miembros de su familia
                    // Usar la misma lógica que el MMS pero agrupando por deudor
                    $total_deuda = 0;
                    $cantidad_deudas = 0;
                    $fecha_mas_antigua = null;
                    $fecha_mas_reciente = null;
                    $deudas_por_persona = [];
                    
                    $sql_deudas = mysqli_query($conn, "
                        SELECT 
                            dd.duedor,
                            dd.comprador,
                            dd.debe,
                            dd.fecha,
                            DATEDIFF(CURDATE(), dd.fecha) as dias_atraso
                        FROM deudas_detalladas dd
                        WHERE dd.comprador = '$comprador'
                        ORDER BY dd.fecha DESC
                    ");
                    
                    if ($sql_deudas) {
                        while ($deuda_data = mysqli_fetch_array($sql_deudas)) {
                            $deudor = $deuda_data['duedor'];
                            $total_deuda += $deuda_data['debe'];
                            $cantidad_deudas++;
                            
                            // Agrupar por deudor
                            if (!isset($deudas_por_persona[$deudor])) {
                                $deudas_por_persona[$deudor] = 0;
                            }
                            $deudas_por_persona[$deudor] += $deuda_data['debe'];
                            
                            if (!$fecha_mas_antigua || $deuda_data['fecha'] < $fecha_mas_antigua) {
                                $fecha_mas_antigua = $deuda_data['fecha'];
                            }
                            if (!$fecha_mas_reciente || $deuda_data['fecha'] > $fecha_mas_reciente) {
                                $fecha_mas_reciente = $deuda_data['fecha'];
                            }
                        }
                    }
                    
                    // Mostrar desglose por persona
                    foreach ($deudas_por_persona as $deudor => $monto) {
                        if ($monto > 0) {
                            $messageComprador .= '
                    <div class="debt-item">
                        <span>'.$deudor.' debe:</span>
                        <span class="amount">$'.number_format($monto, 2).'</span>
                    </div>';
                        }
                    }
                    
                    $messageComprador .= '
                    <div class="debt-item">
                        <span>Total Pendiente:</span>
                        <span class="amount">$'.number_format($total_deuda, 2).'</span>
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

    // Mensaje HTML para los deudores
    $messageDeudores = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nueva Transacción - Gastos Familiares</title>
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
                    ¡Hola '.$data->deudor.'! 👋
            </div>
                
                <p style="color: #374151; line-height: 1.6; margin-bottom: 20px;">
                    Se ha registrado una nueva transacción en tu grupo familiar. Aquí tienes todos los detalles:
                </p>

                <div class="transaction-info">
                    <div class="transaction-title">🛒 Compra Compartida - '.$datalle.'</div>
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
                            <span class="detail-value">$'.number_format($montoComprador, 2).'</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">División:</span>
                            <span class="detail-value">Equitativa ('.(count(explode(',', $deudores)) + 1).' personas)</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Tu Cuota:</span>
                            <span class="detail-value amount">$'.number_format($deuda, 2).'</span>
                        </div>
                    </div>
                </div>

                <div class="participants-section">
                    <div class="section-title">👥 Participantes</div>
                    <div class="participant-list">
                        <div class="participant-item">
                            <div>
                                <div class="participant-name">'.$comprador.'</div>
                                <div class="participant-role">Comprador</div>
                            </div>
                            <span class="amount">$'.number_format($monto, 2).'</span>
                        </div>
                    </div>
                    <div class="participant-list" style="margin-top: 10px;">
                        <div class="section-title" style="font-size:0.95rem; color:#ffffff; margin-bottom:6px;">Deudores (división equitativa: $'.number_format($deuda, 2).' c/u)</div>';
                        
                        // Generar lista de deudores
                        $deudores_array = explode(',', $deudores);
                        foreach($deudores_array as $deudor) {
                            $deudor = trim($deudor);
                            if($deudor && $deudor != '.') {
                                $messageDeudores .= '
                        <div class="participant-item">
                            <div class="participant-name">'.$deudor.'</div>
                        </div>';
                            }
                        }
                        
                        $messageDeudores .= '
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
    //Mailer::enviarCorreo($compradoremail, "Confirmación de compra registrada", $messageComprador);
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
    $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES ('$compradoremail', 'Confirmación de compra registrada', '$messageComprador', '$fecha_creacion')";
    $conn->query($sql);
    //Mailer::enviarCorreo($to, "Notificación de deuda nueva", $messageDeudores);
    $fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual en zona horaria de México
    $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES ('$to', 'Notificación de deuda nueva', '$messageDeudores', '$fecha_creacion')";
    $conn->query($sql);
    echo json_encode([
        'status' => 'ok',
        'texto' => $texto
    ]);
} catch (Exception $e) {
    // Si hay un error, revertir la transacción
    $conn->rollback();
    error_log("Error al registrar gasto: " . $e->getMessage());
    echo json_encode('error');
}
$conn->close();
?>