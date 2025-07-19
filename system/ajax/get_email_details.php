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

// Verificar que se proporcionó un ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(['error' => 'ID de correo no válido']);
    exit;
}

$email_id = (int)$_GET['id'];



try {
    // Obtener detalles del correo (solo columnas que existen)
    $sql = "SELECT 
                id,
                destinatario,
                asunto,
                cuerpo,
                enviado,
                fecha_creacion,
                fecha_envio
            FROM correos_pendientes 
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $email_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Correo no encontrado'
        ]);
        exit;
    }
    
    $email = $result->fetch_object();
    
    // Formatear el contenido HTML si es necesario
    if (strpos($email->cuerpo, '<') !== false && strpos($email->cuerpo, '>') !== false) {
        // Es contenido HTML, mantenerlo como está
        $email->cuerpo_formateado = $email->cuerpo;
    } else {
        // Es texto plano, convertirlo a HTML
        $email->cuerpo_formateado = nl2br(htmlspecialchars($email->cuerpo));
    }
    
    // Si no hay fecha_creacion, usar una fecha aproximada basada en el ID
    if (!$email->fecha_creacion) {
        $email->fecha_creacion = date('Y-m-d H:i:s'); // Fecha actual como aproximación
    }
    
    echo json_encode([
        'success' => true,
        'email' => $email
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error obteniendo detalles del correo: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 