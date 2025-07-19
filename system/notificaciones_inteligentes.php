<?php
// session_start(); // Comentado porque ya se inicia en panel_notificaciones.php

// Verificar si la clase Database ya está definida
if (!class_exists('Database')) {
    include "../conexion.php";
} else {
    // Si ya está definida, solo obtener la conexión
    global $conn;
}

class NotificacionesInteligentes {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Verifica pagos pendientes y envía notificaciones
     */
    public function verificarPagosPendientes() {
        // Obtener deudas pendientes de más de 7 días
        $sql = "SELECT 
                    dg.id,
                    dg.deudor,
                    dg.monto,
                    dg.fecha,
                    g.detalle,
                    u.nombre as deudor_nombre,
                    u.email,
                    u.tel,
                    u.compania_telefonica_id,
                    DATEDIFF(CURDATE(), dg.fecha) as dias_pendiente
                FROM detalle_gasto dg
                INNER JOIN Gastos g ON dg.idgasto = g.id
                INNER JOIN usuarios u ON dg.deudor = u.id
                WHERE dg.pagado = 'No' 
                AND DATEDIFF(CURDATE(), dg.fecha) >= 7
                ORDER BY dg.fecha ASC";
        
        $result = $this->conn->query($sql);
        $notificaciones = [];
        
        while ($row = $result->fetch_object()) {
            $notificaciones[] = $this->crearNotificacionPagoPendiente($row);
        }
        
        return $notificaciones;
    }
    
    /**
     * Verifica deudas que exceden un monto límite
     */
    public function verificarDeudasAltas($limite = 1000) {
        $sql = "SELECT 
                    u.id,
                    u.nombre,
                    u.email,
                    u.tel,
                    u.compania_telefonica_id,
                    SUM(dg.monto) as total_deuda
                FROM usuarios u
                INNER JOIN detalle_gasto dg ON u.id = dg.deudor
                WHERE dg.pagado = 'No'
                GROUP BY u.id, u.compania_telefonica_id
                HAVING total_deuda > $limite";
        
        $result = $this->conn->query($sql);
        $notificaciones = [];
        
        while ($row = $result->fetch_object()) {
            $notificaciones[] = $this->crearNotificacionDeudaAlta($row);
        }
        
        return $notificaciones;
    }
    
    /**
     * Genera resumen semanal de gastos
     */
    public function generarResumenSemanal() {
        $sql = "SELECT 
                    u.nombre,
                    SUM(dg.monto) as total_gastos,
                    COUNT(*) as cantidad_gastos
                FROM detalle_gasto dg
                INNER JOIN usuarios u ON dg.deudor = u.id
                INNER JOIN Gastos g ON dg.idgasto = g.id
                WHERE g.fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY u.id, u.nombre";
        
        $result = $this->conn->query($sql);
        $resumen = [];
        
        while ($row = $result->fetch_object()) {
            $resumen[] = $row;
        }
        
        return $resumen;
    }
    
    /**
     * Crea notificación de pago pendiente
     */
    private function crearNotificacionPagoPendiente($deuda) {
        // Incluir función helper para compañías si no está incluida
        if (!function_exists('construirEmailMMS')) {
            include 'functions_companias.php';
        }
        
        $mensaje = "Hola {$deuda->deudor_nombre}, tienes un pago pendiente de $" . 
                   number_format($deuda->monto, 2) . " por: {$deuda->detalle}. " .
                   "Han pasado {$deuda->dias_pendiente} días desde el {$deuda->fecha}.";
        
        return [
            'tipo' => 'pago_pendiente',
            'usuario_id' => $deuda->deudor,
            'email' => $deuda->email,
            'telefono' => construirEmailMMS($deuda->tel, $deuda->compania_telefonica_id, $this->conn),
            'asunto' => 'Pago Pendiente - ' . $deuda->dias_pendiente . ' días',
            'mensaje' => $mensaje,
            'prioridad' => $deuda->dias_pendiente > 30 ? 'alta' : 'media'
        ];
    }
    
    /**
     * Crea notificación de deuda alta
     */
    private function crearNotificacionDeudaAlta($deuda) {
        // Incluir función helper para compañías si no está incluida
        if (!function_exists('construirEmailMMS')) {
            include 'functions_companias.php';
        }
        
        $mensaje = "Hola {$deuda->nombre}, tu deuda total ha alcanzado $" . 
                   number_format($deuda->total_deuda, 2) . ". " .
                   "Te recomendamos revisar tus pagos pendientes.";
        
        return [
            'tipo' => 'deuda_alta',
            'usuario_id' => $deuda->id,
            'email' => $deuda->email,
            'telefono' => construirEmailMMS($deuda->tel, $deuda->compania_telefonica_id, $this->conn),
            'asunto' => 'Deuda Alta - $' . number_format($deuda->total_deuda, 2),
            'mensaje' => $mensaje,
            'prioridad' => 'alta'
        ];
    }
    
    /**
     * Guarda notificación en la base de datos
     */
    public function guardarNotificacion($notificacion) {
        $sql = "INSERT INTO correos_pendientes 
                (destinatario, asunto, cuerpo, tipo, prioridad, usuario_id, fecha_creacion) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssssi", 
            $notificacion['email'], 
            $notificacion['asunto'], 
            $notificacion['mensaje'],
            $notificacion['tipo'],
            $notificacion['prioridad'],
            $notificacion['usuario_id']
        );
        
        return $stmt->execute();
    }
    
    /**
     * Ejecuta todas las verificaciones de notificaciones
     */
    public function ejecutarVerificaciones() {
        $notificaciones = [];
        
        // Verificar pagos pendientes
        $notificaciones = array_merge($notificaciones, $this->verificarPagosPendientes());
        
        // Verificar deudas altas
        $notificaciones = array_merge($notificaciones, $this->verificarDeudasAltas());
        
        // Guardar todas las notificaciones
        foreach ($notificaciones as $notificacion) {
            $this->guardarNotificacion($notificacion);
        }
        
        return count($notificaciones);
    }
}

// Si se ejecuta directamente, ejecutar verificaciones
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $notificaciones = new NotificacionesInteligentes($conn);
    $cantidad = $notificaciones->ejecutarVerificaciones();
    echo json_encode(['notificaciones_generadas' => $cantidad]);
}
?> 