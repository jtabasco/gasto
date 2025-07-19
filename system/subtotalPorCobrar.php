<?php
session_start();
include "../conexion.php";

function obtenerDeudaPorCobrar($conn, $idUsuario) {
    // Usar consulta preparada para prevenir inyección SQL
    $query = "SELECT COALESCE(SUM(deben), 0.00) as total_deuda 
              FROM deben 
              WHERE comprador = ?";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $idUsuario);
    mysqli_stmt_execute($stmt);
    
    $resultado = mysqli_stmt_get_result($stmt);
    $fila = mysqli_fetch_assoc($resultado);
    
    mysqli_stmt_close($stmt);
    
    return number_format($fila['total_deuda'], 2);
}

// Verificar que existe la sesión
if (!isset($_SESSION['idUser'])) {
    echo "Error: Usuario no identificado";
    exit;
}

try {
    $deudaPorCobrar = obtenerDeudaPorCobrar($conn, $_SESSION['idUser']);
    echo "Por Cobrar $" . $deudaPorCobrar;
} catch (Exception $e) {
    error_log("Error al obtener deuda por cobrar: " . $e->getMessage());
    echo "Error al procesar la solicitud";
}

mysqli_close($conn);
?>