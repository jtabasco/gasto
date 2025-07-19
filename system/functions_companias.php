<?php
/**
 * Obtiene el dominio MMS de una compañía telefónica
 */
if (!function_exists('obtenerDominioMMS')) {
function obtenerDominioMMS($compania_id, $conn) {
    if (!$compania_id) {
        return null; // No hay compañía asignada
    }
    
    $sql = "SELECT dominio_mms FROM companias_telefonicas WHERE id = ? AND activo = 'Si'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $compania_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['dominio_mms'];
    }
    
    return null; // La compañía no está activa o no existe
}

/**
 * Obtiene todas las compañías telefónicas activas
 */
if (!function_exists('obtenerCompaniasTelefonicas')) {
function obtenerCompaniasTelefonicas($conn) {
    $sql = "SELECT id, nombre, dominio_mms FROM companias_telefonicas WHERE activo = 'Si' ORDER BY nombre";
    $result = $conn->query($sql);
    
    $companias = [];
    while ($row = $result->fetch_assoc()) {
        $companias[] = $row;
    }
    
    return $companias;
}
}

/**
 * Construye el email MMS completo con el dominio correcto
 */
if (!function_exists('construirEmailMMS')) {
function construirEmailMMS($telefono, $compania_id, $conn) {
    $dominio = obtenerDominioMMS($compania_id, $conn);
    
    if ($dominio === null) {
        return null; // No se puede enviar MMS si la compañía no está activa
    }
    
    return $telefono . $dominio;
}
}

/**
 * Verifica si se puede enviar MMS a un usuario basado en su compañía telefónica
 */
if (!function_exists('puedeEnviarMMS')) {
function puedeEnviarMMS($compania_id, $conn) {
    if (!$compania_id) {
        return false; // No hay compañía asignada
    }
    
    $sql = "SELECT id FROM companias_telefonicas WHERE id = ? AND activo = 'Si'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $compania_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0; // Retorna true si la compañía está activa
}
}

/**
 * Obtiene información de la compañía telefónica de un usuario
 */
if (!function_exists('obtenerInfoCompaniaUsuario')) {
function obtenerInfoCompaniaUsuario($compania_id, $conn) {
    if (!$compania_id) {
        return null;
    }
    
    $sql = "SELECT id, nombre, dominio_mms, activo FROM companias_telefonicas WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $compania_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row;
    }
    
    return null;
}
}
}
?> 