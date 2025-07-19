<?php
// Funciones helper para generar mensajes MMS enriquecidos

/**
 * Genera un mensaje MMS enriquecido para notificaciones de gastos/préstamos
 */
function generarMensajeMMSEnriquecido($tipo, $deudor, $datos, $conn) {
    $message = "";
    
    if ($tipo === 'gasto') {
        $message = "Hola $deudor,\n\n";
        $message .= "📊 DETALLES DE LA COMPRA:\n";
        $message .= "• Categoría: " . $datos['categoria'] . "\n";
        $message .= "• Concepto: " . $datos['detalle'] . "\n";
        $message .= "• Monto total: $" . number_format($datos['monto_total'], 2) . "\n";
        $message .= "• Tu deuda: $" . number_format($datos['deuda_personal'], 2) . "\n";
        $message .= "• Fecha: " . date('d/m/Y', strtotime($datos['fecha'])) . "\n";
        $message .= "• Comprador: " . $datos['comprador'] . "\n\n";
        
        // Obtener información adicional de deudas
        $total_deuda = 0;
        $cantidad_deudas = 0;
        $fecha_mas_antigua = null;
        $fecha_mas_reciente = null;
        
        if ($conn) {
            $sql_deudas = mysqli_query($conn, "
                SELECT 
                    dd.duedor,
                    dd.comprador,
                    dd.debe,
                    dd.fecha,
                    DATEDIFF(CURDATE(), dd.fecha) as dias_atraso
                FROM deudas_detalladas dd
                WHERE dd.duedor = '$deudor'
                ORDER BY dd.fecha DESC
            ");
            if ($sql_deudas) {
                while ($deuda = mysqli_fetch_array($sql_deudas)) {
                    $total_deuda += $deuda['debe'];
                    $cantidad_deudas++;
                    if (!$fecha_mas_antigua || $deuda['fecha'] < $fecha_mas_antigua) {
                        $fecha_mas_antigua = $deuda['fecha'];
                    }
                    if (!$fecha_mas_reciente || $deuda['fecha'] > $fecha_mas_reciente) {
                        $fecha_mas_reciente = $deuda['fecha'];
                    }
                }
            }
        }
        
        $message .= "📋 TU SITUACIÓN:\n";
        $message .= "• Total de deudas: $" . number_format($total_deuda, 2) . "\n";
        $message .= "• Cantidad de deudas: $cantidad_deudas\n";
        $message .= "• F más antigua: " . ($fecha_mas_antigua ? date('d/m/Y', strtotime($fecha_mas_antigua)) : 'N/A') . "\n";
        $message .= "• F más reciente: " . ($fecha_mas_reciente ? date('d/m/Y', strtotime($fecha_mas_reciente)) : 'N/A') . "\n\n";
        
    } elseif ($tipo === 'prestamo') {
        $message = "Hola $deudor,\n\n";
        $message .= "📊 DETALLES DEL PRÉSTAMO:\n";
        $message .= "• Categoría: " . $datos['categoria'] . "\n";
        $message .= "• Concepto: " . $datos['detalle'] . "\n";
        $message .= "• Monto total: $" . number_format($datos['monto_total'], 2) . "\n";
        $message .= "• Tu deuda: $" . number_format($datos['deuda_personal'], 2) . "\n";
        $message .= "• Fecha: " . date('d/m/Y', strtotime($datos['fecha'])) . "\n";
        $message .= "• Prestador: " . $datos['comprador'] . "\n\n";
        
        // Obtener información adicional de deudas
        $total_deuda = 0;
        $cantidad_deudas = 0;
        $fecha_mas_antigua = null;
        $fecha_mas_reciente = null;
        
        if ($conn) {
            $sql_deudas = mysqli_query($conn, "
                SELECT 
                    dd.duedor,
                    dd.comprador,
                    dd.debe,
                    dd.fecha,
                    DATEDIFF(CURDATE(), dd.fecha) as dias_atraso
                FROM deudas_detalladas dd
                WHERE dd.duedor = '$deudor'
                ORDER BY dd.fecha DESC
            ");
            if ($sql_deudas) {
                while ($deuda = mysqli_fetch_array($sql_deudas)) {
                    $total_deuda += $deuda['debe'];
                    $cantidad_deudas++;
                    if (!$fecha_mas_antigua || $deuda['fecha'] < $fecha_mas_antigua) {
                        $fecha_mas_antigua = $deuda['fecha'];
                    }
                    if (!$fecha_mas_reciente || $deuda['fecha'] > $fecha_mas_reciente) {
                        $fecha_mas_reciente = $deuda['fecha'];
                    }
                }
            }
        }
        
        $message .= "📋 TU SITUACIÓN:\n";
        $message .= "• Total de deudas: $" . number_format($total_deuda, 2) . "\n";
        $message .= "• Cantidad de deudas: $cantidad_deudas\n";
        $message .= "• F más antigua: " . ($fecha_mas_antigua ? date('d/m/Y', strtotime($fecha_mas_antigua)) : 'N/A') . "\n";
        $message .= "• F más reciente: " . ($fecha_mas_reciente ? date('d/m/Y', strtotime($fecha_mas_reciente)) : 'N/A') . "\n\n";
    }
    
    $message .= "🌐 ACCESO AL SISTEMA:\n";
    $message .= "jtabasco.com/gasto\n\n";
    $message .= "Fecha: " . date('d/m/Y H:i') . "\n";
    $message .= "Sistema de Gestión de Gastos";
    return $message;
}

/**
 * Genera un mensaje MMS enriquecido para deudas netas
 */
function generarMensajeMMSDeudasNetas($datos, $conn) {
    $deudor = $datos['deudor'];
    $acreedor = $datos['acreedor'];
    $por_pagar = $datos['por_pagar'];
    $por_cobrar = $datos['por_cobrar'];
    $neto = $datos['neto'];
    $neto_texto = $datos['neto_texto'];
    
    $message = "💰 RESUMEN DE DEUDAS NETAS - SISTEMA DE GASTOS\n\n";
    $message .= "Hola $acreedor,\n\n";
    $message .= "📊 RESUMEN DE DEUDAS CON $deudor:\n";
    $message .= "• Por pagar a ti: $" . number_format($por_pagar, 2) . "\n";
    $message .= "• Por cobrar de ti: $" . number_format($por_cobrar, 2) . "\n";
    $message .= "• Neto: $" . number_format(abs($neto), 2) . "\n";
    $message .= "• Estado: $neto_texto\n";
    $message .= "• Fecha: " . date('d/m/Y', strtotime($datos['fecha'])) . "\n\n";
    
    // Obtener información adicional de deudas entre ambos
    $total_deudas_entre_ellos = 0;
    $cantidad_deudas_entre_ellos = 0;
    $fecha_mas_antigua = null;
    $fecha_mas_reciente = null;
    
    if ($conn) {
        $sql_deudas = mysqli_query($conn, "
            SELECT 
                dd.duedor,
                dd.comprador,
                dd.debe,
                dd.fecha,
                DATEDIFF(CURDATE(), dd.fecha) as dias_atraso
            FROM deudas_detalladas dd
            WHERE (dd.duedor = '$deudor' AND dd.comprador = '$acreedor')
               OR (dd.duedor = '$acreedor' AND dd.comprador = '$deudor')
            ORDER BY dd.fecha DESC
        ");
        if ($sql_deudas) {
            while ($deuda = mysqli_fetch_array($sql_deudas)) {
                $total_deudas_entre_ellos += $deuda['debe'];
                $cantidad_deudas_entre_ellos++;
                if (!$fecha_mas_antigua || $deuda['fecha'] < $fecha_mas_antigua) {
                    $fecha_mas_antigua = $deuda['fecha'];
                }
                if (!$fecha_mas_reciente || $deuda['fecha'] > $fecha_mas_reciente) {
                    $fecha_mas_reciente = $deuda['fecha'];
                }
            }
        }
    }
    
    $message .= "📋 RESUMEN DE DEUDAS ENTRE USTEDES:\n";
    $message .= "• Total de deudas: $" . number_format($total_deudas_entre_ellos, 2) . "\n";
    $message .= "• Ctdad de deudas: $cantidad_deudas_entre_ellos\n";
    $message .= "• F más antigua: " . ($fecha_mas_antigua ? date('d/m/Y', strtotime($fecha_mas_antigua)) : 'N/A') . "\n";
    $message .= "• F más reciente: " . ($fecha_mas_reciente ? date('d/m/Y', strtotime($fecha_mas_reciente)) : 'N/A') . "\n\n";
    
    $message .= "💡 RECOMENDACIONES:\n";
    $message .= "• Revisa los detalles en el sistema\n";
    $message .= "• Contacta a $deudor para coordinar pagos\n";
    $message .= "• Actualiza el estado de las deudas\n\n";
    $message .= "🌐 ACCESO AL SISTEMA:\n";
    $message .= "jtabasco.com/gasto\n\n";
    $message .= "Fecha: " . date('d/m/Y H:i') . "\n";
    $message .= "Sistema de Gestión de Gastos";
    
    return $message;
}

/**
 * Guarda un mensaje MMS en la tabla de correos pendientes
 */
function guardarMMSPendiente($destinatario, $asunto, $mensaje, $conn) {
    $fecha_creacion = date('Y-m-d H:i:s');
    $sql = "INSERT INTO correos_pendientes (destinatario, asunto, cuerpo, fecha_creacion) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $destinatario, $asunto, $mensaje, $fecha_creacion);
    $resultado = $stmt->execute();
    $stmt->close();
    
    return $resultado;
}

/**
 * Guarda un mensaje MMS en la tabla de correos pendientes usando teléfono y compañía
 */
function guardarMMSPendienteConCompania($telefono, $compania_id, $asunto, $mensaje, $conn) {
    // Incluir función helper para compañías
    include 'functions_companias.php';
    
    // Verificar si se puede enviar MMS
    if (!puedeEnviarMMS($compania_id, $conn)) {
        return false; // No se puede enviar MMS si la compañía no está activa
    }
    
    $destinatario = construirEmailMMS($telefono, $compania_id, $conn);
    
    if ($destinatario === null) {
        return false; // No se pudo construir el email MMS
    }
    
    return guardarMMSPendiente($destinatario, $asunto, $mensaje, $conn);
}

/**
 * Obtiene información detallada de un gasto/préstamo
 */
function obtenerInformacionDetallada($id_gasto, $conn) {
    $sql = mysqli_query($conn, "
        SELECT 
            g.id, 
            g.detalle, 
            c.categoria, 
            g.Monto as monto_total, 
            g.fecha, 
            u.nombre as comprador, 
            u.email 
        FROM Gastos g
        INNER JOIN usuarios u ON u.id = g.comprador 
        INNER JOIN categoria c ON g.idcat = c.id 
        WHERE g.id = '$id_gasto'
    ");
    
    return mysqli_fetch_array($sql);
}

/**
 * Obtiene información de los deudores de un gasto/préstamo
 */
function obtenerDeudores($id_gasto, $conn) {
    $sql = "
        SELECT 
            dg.idgasto, 
            dg.monto as deuda_personal, 
            u.nombre as deudor, 
            u.email, 
            u.tel 
        FROM detalle_gasto dg
        INNER JOIN usuarios u ON u.id = dg.deudor 
        WHERE dg.idgasto = '$id_gasto'
    ";
    
    return $conn->query($sql);
}
?> 