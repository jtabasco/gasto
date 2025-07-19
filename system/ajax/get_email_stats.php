<?php
session_start();
include "../../conexion.php";

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['idUser'])) {
    echo json_encode(['error' => 'Usuario no autenticado']);
    exit;
}

// Verificar si el usuario es administrador (rol_id = 1)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    echo json_encode(['error' => 'Acceso denegado']);
    exit;
}

// Establecer zona horaria local


try {
    // Total de correos
    $sql_total = "SELECT COUNT(*) as total FROM correos_pendientes";
    $result_total = $conn->query($sql_total);
    $total = $result_total->fetch_object()->total;
    
    // Correos enviados
    $sql_sent = "SELECT COUNT(*) as sent FROM correos_pendientes WHERE enviado = 1";
    $result_sent = $conn->query($sql_sent);
    $sent = $result_sent->fetch_object()->sent;
    
    // Correos pendientes
    $sql_pending = "SELECT COUNT(*) as pending FROM correos_pendientes WHERE enviado = 0";
    $result_pending = $conn->query($sql_pending);
    $pending = $result_pending->fetch_object()->pending;
    
    // Correos de hoy (considerando fecha_creacion NULL como hoy)
    $hoy = date('Y-m-d');
    $sql_today = "SELECT COUNT(*) as today FROM correos_pendientes WHERE DATE(fecha_creacion) = ? OR (fecha_creacion IS NULL AND DATE(NOW()) = ?)";
    $stmt_today = $conn->prepare($sql_today);
    $stmt_today->bind_param("ss", $hoy, $hoy);
    $stmt_today->execute();
    $result_today = $stmt_today->get_result();
    $today = $result_today->fetch_object()->today;
    
    echo json_encode([
        'success' => true,
        'total' => $total,
        'sent' => $sent,
        'pending' => $pending,
        'today' => $today
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error obteniendo estadísticas: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 