<?php
session_start();

// Verificar si la clase Database ya está definida
if (!class_exists('Database')) {
    include "../conexion.php";
} else {
    // Si ya está definida, solo obtener la conexión
    global $conn;
}

/**
 * Obtiene notificaciones para el usuario actual
 */
function obtenerNotificacionesUsuario($conn, $usuario_id) {
    // Obtener el email del usuario actual
    $sql_usuario = "SELECT email FROM usuarios WHERE id = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $usuario_id);
    $stmt_usuario->execute();
    $result_usuario = $stmt_usuario->get_result();
    $usuario = $result_usuario->fetch_object();
    
    if (!$usuario) {
        return [];
    }
    
    // Obtener notificaciones específicas del usuario
    $sql = "SELECT 
                cp.id,
                cp.asunto,
                cp.cuerpo,
                cp.destinatario,
                cp.enviado
            FROM correos_pendientes cp
            WHERE cp.destinatario = ? AND cp.enviado = 0
            ORDER BY cp.id DESC
            LIMIT 10";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario->email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $notificaciones = [];
    while ($row = $result->fetch_object()) {
        // Agregar campos por defecto para compatibilidad
        $row->tipo = 'general';
        $row->prioridad = 'normal';
        $row->fecha_creacion = date('Y-m-d H:i:s'); // Usar fecha actual como aproximación
        $row->leida = 0; // Por defecto no leída
        $notificaciones[] = $row;
    }
    
    return $notificaciones;
}

/**
 * Obtiene estadísticas de notificaciones para el usuario actual
 */
function obtenerEstadisticas($conn, $usuario_id) {
    // Obtener el email del usuario actual
    $sql_usuario = "SELECT email FROM usuarios WHERE id = ?";
    $stmt_usuario = $conn->prepare($sql_usuario);
    $stmt_usuario->bind_param("i", $usuario_id);
    $stmt_usuario->execute();
    $result_usuario = $stmt_usuario->get_result();
    $usuario = $result_usuario->fetch_object();
    
    if (!$usuario) {
        return (object)['total' => 0, 'no_leidas' => 0, 'urgentes' => 0];
    }
    
    // Contar notificaciones del usuario
    $sql = "SELECT 
                COUNT(*) as total,
                COUNT(*) as no_leidas,
                0 as urgentes
            FROM correos_pendientes 
            WHERE destinatario = ? AND enviado = 0";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario->email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_object();
}

/**
 * Formatea el tiempo de la notificación
 */
function formatearTiempo($fecha) {
    $ahora = new DateTime();
    $notif = new DateTime($fecha);
    $diff = $ahora->diff($notif);
    
    if ($diff->days > 0) {
        return 'Hace ' . $diff->days . ' día' . ($diff->days > 1 ? 's' : '');
    } elseif ($diff->h > 0) {
        return 'Hace ' . $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    } elseif ($diff->i > 0) {
        return 'Hace ' . $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
    } else {
        return 'Ahora mismo';
    }
}

/**
 * Genera HTML del panel de notificaciones
 */
function generarHTML($conn, $usuario_id) {
    $notificaciones = obtenerNotificacionesUsuario($conn, $usuario_id);
    $stats = obtenerEstadisticas($conn, $usuario_id);
    
    $html = '<div class="notifications-panel">';
    
    // Header con estadísticas
    $html .= '<div class="notifications-header">';
    $html .= '<h5><i class="bi bi-bell"></i> Mis Notificaciones';
    if ($stats->no_leidas > 0) {
        $html .= '<span class="badge bg-danger ms-2">' . $stats->no_leidas . '</span>';
    }
    $html .= '</h5>';
    $html .= '</div>';
    
    // Lista de notificaciones
    if (empty($notificaciones)) {
        $html .= '<div class="no-notifications">';
        $html .= '<p class="text-muted">No tienes notificaciones nuevas</p>';
        $html .= '</div>';
    } else {
        $html .= '<div class="notifications-list">';
        foreach ($notificaciones as $notif) {
            $clase = $notif->leida ? 'notification-read' : 'notification-unread';
            $prioridad = $notif->prioridad == 'alta' ? 'priority-high' : 'priority-normal';
            
            $html .= '<div class="notification-item ' . $clase . ' ' . $prioridad . '" data-id="' . $notif->id . '">';
            $html .= '<div class="notification-content">';
            $html .= '<h6 class="notification-title">' . htmlspecialchars($notif->asunto) . '</h6>';
            $html .= '<p class="notification-text">' . htmlspecialchars($notif->cuerpo) . '</p>';
            $html .= '<small class="notification-time">' . formatearTiempo($notif->fecha_creacion) . '</small>';
            $html .= '</div>';
            if (!$notif->leida) {
                $html .= '<button class="btn btn-sm btn-outline-primary mark-read" data-id="' . $notif->id . '">';
                $html .= '<i class="bi bi-check"></i>';
                $html .= '</button>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

// Si se ejecuta directamente, devolver notificaciones en JSON
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_notifications':
                $notificaciones = obtenerNotificacionesUsuario($conn, $_SESSION['idUser']);
                echo json_encode($notificaciones);
                break;
                
            case 'mark_read':
                if (isset($_POST['id'])) {
                    // Como no tenemos columna leida, marcamos como enviado
                    $sql = "UPDATE correos_pendientes SET enviado = 1 WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $_POST['id']);
                    $resultado = $stmt->execute();
                    echo json_encode(['success' => $resultado]);
                }
                break;
                
            case 'get_stats':
                $stats = obtenerEstadisticas($conn, $_SESSION['idUser']);
                echo json_encode($stats);
                break;
                
            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    } else {
        // Devolver HTML del panel
        echo generarHTML($conn, $_SESSION['idUser']);
    }
}
?> 