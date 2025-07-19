<?php
session_start();

// Verificar si la clase Database ya está definida
if (!class_exists('Database')) {
    include "../conexion.php";
} else {
    // Si ya está definida, solo obtener la conexión
    global $conn;
}

// Verificar si la clase NotificacionesInteligentes ya está definida
if (!class_exists('NotificacionesInteligentes')) {
    include "notificaciones_inteligentes.php";
}

class PanelNotificaciones {
    private $conn;
    private $notificaciones;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->notificaciones = new NotificacionesInteligentes($conn);
    }
    
    /**
     * Obtiene notificaciones para el usuario actual
     */
    public function obtenerNotificacionesUsuario($usuario_id) {
        $sql = "SELECT 
                    cp.id,
                    cp.asunto,
                    cp.cuerpo,
                    cp.tipo,
                    cp.prioridad,
                    cp.fecha_creacion,
                    cp.leida
                FROM correos_pendientes cp
                WHERE cp.usuario_id = ? 
                AND cp.fecha_creacion >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                ORDER BY cp.fecha_creacion DESC
                LIMIT 10";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notificaciones = [];
        while ($row = $result->fetch_object()) {
            $notificaciones[] = $row;
        }
        
        return $notificaciones;
    }
    
    /**
     * Marca notificación como leída
     */
    public function marcarComoLeida($notificacion_id) {
        $sql = "UPDATE correos_pendientes SET leida = 1 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $notificacion_id);
        return $stmt->execute();
    }
    
    /**
     * Obtiene estadísticas de notificaciones
     */
    public function obtenerEstadisticas($usuario_id) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN leida = 0 THEN 1 ELSE 0 END) as no_leidas,
                    SUM(CASE WHEN prioridad = 'alta' AND leida = 0 THEN 1 ELSE 0 END) as urgentes
                FROM correos_pendientes 
                WHERE usuario_id = ? 
                AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_object();
    }
    
    /**
     * Genera HTML del panel de notificaciones
     */
    public function generarHTML($usuario_id) {
        $notificaciones = $this->obtenerNotificacionesUsuario($usuario_id);
        $stats = $this->obtenerEstadisticas($usuario_id);
        
        $html = '<div class="notifications-panel">';
        
        // Header con estadísticas
        $html .= '<div class="notifications-header">';
        $html .= '<h5><i class="bi bi-bell"></i> Notificaciones';
        if ($stats->no_leidas > 0) {
            $html .= '<span class="badge bg-danger ms-2">' . $stats->no_leidas . '</span>';
        }
        $html .= '</h5>';
        $html .= '</div>';
        
        // Lista de notificaciones
        if (empty($notificaciones)) {
            $html .= '<div class="no-notifications">';
            $html .= '<p class="text-muted">No hay notificaciones nuevas</p>';
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
                $html .= '<small class="notification-time">' . $this->formatearTiempo($notif->fecha_creacion) . '</small>';
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
    
    /**
     * Formatea el tiempo de la notificación
     */
    private function formatearTiempo($fecha) {
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
}

// Si se ejecuta directamente, devolver notificaciones en JSON
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $panel = new PanelNotificaciones($conn);
    
    if (isset($_GET['action'])) {
        switch ($_GET['action']) {
            case 'get_notifications':
                $notificaciones = $panel->obtenerNotificacionesUsuario($_SESSION['idUser']);
                echo json_encode($notificaciones);
                break;
                
            case 'mark_read':
                if (isset($_POST['id'])) {
                    $resultado = $panel->marcarComoLeida($_POST['id']);
                    echo json_encode(['success' => $resultado]);
                }
                break;
                
            case 'get_stats':
                $stats = $panel->obtenerEstadisticas($_SESSION['idUser']);
                echo json_encode($stats);
                break;
                
            default:
                echo json_encode(['error' => 'Acción no válida']);
        }
    } else {
        // Devolver HTML del panel
        echo $panel->generarHTML($_SESSION['idUser']);
    }
}
?> 