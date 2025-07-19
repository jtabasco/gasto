<?php
session_start();
include "../conexion.php";

if(empty($_SESSION['active'])){
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Función para obtener datos de deudas por familia
function getDeudasPorFamilia() {
    global $conn;
    $mes_actual = date('Y-m');
    
    $sql = "SELECT 
                f.nombre as familia,
                COUNT(DISTINCT u.id) as usuarios_activos,
                SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deudas_pendientes,
                SUM(CASE WHEN dg.pagado = 'Si' THEN dg.monto ELSE 0 END) as pagado_mes,
                COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas
            FROM familia f
            LEFT JOIN usuarios u ON f.id = u.familia_id AND u.activo = 'Si'
            LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
            LEFT JOIN Gastos g ON dg.idgasto = g.id
            WHERE (g.fecha LIKE '$mes_actual%' OR g.fecha IS NULL)
            GROUP BY f.id, f.nombre
            ORDER BY deudas_pendientes DESC";
    
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Función para obtener datos de mora
function getDeudasPorMora() {
    global $conn;
    
    $sql = "SELECT 
                CASE 
                    WHEN DATEDIFF(CURDATE(), g.fecha) <= 7 THEN '1-7 días'
                    WHEN DATEDIFF(CURDATE(), g.fecha) <= 15 THEN '8-15 días'
                    WHEN DATEDIFF(CURDATE(), g.fecha) <= 30 THEN '16-30 días'
                    ELSE 'Más de 30 días'
                END as rango_dias,
                COUNT(*) as cantidad,
                SUM(dg.monto) as monto_total
            FROM detalle_gasto dg
            INNER JOIN Gastos g ON dg.idgasto = g.id
            WHERE dg.pagado = 'No'
            GROUP BY rango_dias
            ORDER BY FIELD(rango_dias, '1-7 días', '8-15 días', '16-30 días', 'Más de 30 días')";
    
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Función para obtener usuarios con más deudas
function getUsuariosConDeudas() {
    global $conn;
    
    $sql = "SELECT 
                u.nombre,
                f.nombre as familia,
                SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deuda_total,
                COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas,
                MAX(CASE WHEN dg.pagado = 'No' THEN DATEDIFF(CURDATE(), g.fecha) ELSE 0 END) as dias_mora_max,
                AVG(CASE WHEN dg.pagado = 'No' THEN DATEDIFF(CURDATE(), g.fecha) ELSE 0 END) as dias_mora_promedio
            FROM usuarios u
            LEFT JOIN familia f ON u.familia_id = f.id
            LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
            LEFT JOIN Gastos g ON dg.idgasto = g.id
            WHERE u.activo = 'Si'
            GROUP BY u.id, u.nombre, f.nombre
            HAVING deuda_total > 0
            ORDER BY deuda_total DESC
            LIMIT 10";
    
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Función para obtener alertas críticas
function getAlertasCriticas() {
    global $conn;
    
    $sql = "SELECT 
                u.nombre,
                f.nombre as familia,
                SUM(dg.monto) as deuda_total,
                MAX(DATEDIFF(CURDATE(), g.fecha)) as dias_mora,
                COUNT(*) as cantidad_deudas
            FROM usuarios u
            LEFT JOIN familia f ON u.familia_id = f.id
            INNER JOIN detalle_gasto dg ON u.id = dg.deudor
            INNER JOIN Gastos g ON dg.idgasto = g.id
            WHERE dg.pagado = 'No'
            GROUP BY u.id, u.nombre, f.nombre
            HAVING deuda_total > 1000 OR dias_mora > 30
            ORDER BY deuda_total DESC, dias_mora DESC";
    
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

// Función para obtener resumen general
function getResumenGeneral() {
    global $conn;
    $mes_actual = date('Y-m');
    
    $sql = "SELECT 
                COUNT(DISTINCT u.id) as total_usuarios,
                COUNT(DISTINCT f.id) as total_familias,
                SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as total_deudas_pendientes,
                SUM(CASE WHEN dg.pagado = 'Si' THEN dg.monto ELSE 0 END) as total_pagado_mes,
                COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas_pendientes
            FROM usuarios u
            LEFT JOIN familia f ON u.familia_id = f.id
            LEFT JOIN detalle_gasto dg ON u.id = dg.deudor
            LEFT JOIN Gastos g ON dg.idgasto = g.id
            WHERE u.activo = 'Si' 
            AND (g.fecha LIKE '$mes_actual%' OR g.fecha IS NULL)";
    
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Procesar la solicitud
$action = $_GET['action'] ?? 'all';

try {
    switch($action) {
        case 'resumen':
            $data = getResumenGeneral();
            break;
        case 'familias':
            $data = getDeudasPorFamilia();
            break;
        case 'mora':
            $data = getDeudasPorMora();
            break;
        case 'usuarios':
            $data = getUsuariosConDeudas();
            break;
        case 'alertas':
            $data = getAlertasCriticas();
            break;
        case 'all':
        default:
            $data = [
                'resumen' => getResumenGeneral(),
                'familias' => getDeudasPorFamilia(),
                'mora' => getDeudasPorMora(),
                'usuarios' => getUsuariosConDeudas(),
                'alertas' => getAlertasCriticas()
            ];
            break;
    }
    
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error interno del servidor: ' . $e->getMessage()]);
}

$conn->close();
?> 