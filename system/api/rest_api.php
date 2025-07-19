<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

session_start();
include "../../conexion.php";

class GastosAPI {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Autenticar usuario por token
     */
    public function autenticar($token) {
        $sql = "SELECT id, nombre, email, familia_id FROM usuarios WHERE token = ? AND activo = 'si'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return $result->fetch_object();
        }
        return null;
    }
    
    /**
     * Obtener gastos del usuario
     */
    public function obtenerGastos($usuario_id, $limit = 50) {
        $sql = "SELECT 
                    g.id,
                    g.detalle,
                    g.Monto,
                    g.fecha,
                    c.categoria,
                    u.nombre as comprador
                FROM Gastos g
                INNER JOIN categoria c ON g.idcat = c.id
                INNER JOIN usuarios u ON g.comprador = u.id
                WHERE g.comprador = ?
                ORDER BY g.fecha DESC
                LIMIT ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $gastos = [];
        while ($row = $result->fetch_object()) {
            $gastos[] = $row;
        }
        
        return $gastos;
    }
    
    /**
     * Obtener deudas del usuario
     */
    public function obtenerDeudas($usuario_id) {
        $sql = "SELECT 
                    dg.id,
                    dg.monto,
                    dg.fecha,
                    dg.pagado,
                    g.detalle,
                    u.nombre as comprador
                FROM detalle_gasto dg
                INNER JOIN Gastos g ON dg.idgasto = g.id
                INNER JOIN usuarios u ON g.comprador = u.id
                WHERE dg.deudor = ? AND dg.pagado = 'No'
                ORDER BY dg.fecha DESC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $deudas = [];
        while ($row = $result->fetch_object()) {
            $deudas[] = $row;
        }
        
        return $deudas;
    }
    
    /**
     * Obtener resumen de deudas
     */
    public function obtenerResumenDeudas($usuario_id) {
        $sql = "SELECT 
                    SUM(CASE WHEN dg.pagado = 'No' THEN dg.monto ELSE 0 END) as deuda_total,
                    COUNT(CASE WHEN dg.pagado = 'No' THEN 1 END) as cantidad_deudas,
                    SUM(CASE WHEN dg.pagado = 'Si' THEN dg.monto ELSE 0 END) as pagado_total
                FROM detalle_gasto dg
                WHERE dg.deudor = ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result->fetch_object();
    }
    
    /**
     * Registrar nuevo gasto
     */
    public function registrarGasto($datos) {
        // Validar datos requeridos
        if (empty($datos['detalle']) || empty($datos['monto']) || empty($datos['fecha']) || empty($datos['categoria_id'])) {
            return ['error' => 'Faltan datos requeridos'];
        }
        
        // Insertar gasto
        $sql = "INSERT INTO Gastos (comprador, detalle, Monto, fecha, idcat) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isdsi", 
            $datos['comprador_id'],
            $datos['detalle'],
            $datos['monto'],
            $datos['fecha'],
            $datos['categoria_id']
        );
        
        if ($stmt->execute()) {
            $gasto_id = $this->conn->insert_id;
            
            // Procesar deudores si se especifican
            if (!empty($datos['deudores'])) {
                $this->procesarDeudores($gasto_id, $datos['deudores'], $datos['monto']);
            }
            
            return ['success' => true, 'gasto_id' => $gasto_id];
        }
        
        return ['error' => 'Error al registrar gasto'];
    }
    
    /**
     * Procesar deudores para un gasto
     */
    private function procesarDeudores($gasto_id, $deudores, $monto_total) {
        $monto_por_persona = $monto_total / count($deudores);
        
        foreach ($deudores as $deudor_id) {
            $sql = "INSERT INTO detalle_gasto (idgasto, deudor, monto, fecha, pagado) VALUES (?, ?, ?, NOW(), 'No')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iid", $gasto_id, $deudor_id, $monto_por_persona);
            $stmt->execute();
        }
    }
    
    /**
     * Marcar deuda como pagada
     */
    public function marcarDeudaPagada($deuda_id) {
        $sql = "UPDATE detalle_gasto SET pagado = 'Si' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $deuda_id);
        
        if ($stmt->execute()) {
            return ['success' => true];
        }
        
        return ['error' => 'Error al actualizar deuda'];
    }
    
    /**
     * Obtener categorías
     */
    public function obtenerCategorias() {
        $sql = "SELECT id, categoria FROM categoria ORDER BY categoria";
        $result = $this->conn->query($sql);
        
        $categorias = [];
        while ($row = $result->fetch_object()) {
            $categorias[] = $row;
        }
        
        return $categorias;
    }
    
    /**
     * Obtener usuarios de la familia
     */
    public function obtenerUsuariosFamilia($familia_id) {
        $sql = "SELECT id, nombre, email FROM usuarios WHERE familia_id = ? AND activo = 'si' ORDER BY nombre";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $familia_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $usuarios = [];
        while ($row = $result->fetch_object()) {
            $usuarios[] = $row;
        }
        
        return $usuarios;
    }
}

// Procesar la solicitud
$api = new GastosAPI($conn);
$method = $_SERVER['REQUEST_METHOD'];
$endpoint = isset($_GET['endpoint']) ? $_GET['endpoint'] : '';

// Obtener token del header Authorization
$headers = getallheaders();
$token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

// Autenticar usuario
$usuario = $api->autenticar($token);
if (!$usuario) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

// Procesar endpoints
switch ($endpoint) {
    case 'gastos':
        if ($method === 'GET') {
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            $gastos = $api->obtenerGastos($usuario->id, $limit);
            echo json_encode(['success' => true, 'data' => $gastos]);
        } elseif ($method === 'POST') {
            $datos = json_decode(file_get_contents('php://input'), true);
            $datos['comprador_id'] = $usuario->id;
            $resultado = $api->registrarGasto($datos);
            echo json_encode($resultado);
        }
        break;
        
    case 'deudas':
        if ($method === 'GET') {
            $deudas = $api->obtenerDeudas($usuario->id);
            echo json_encode(['success' => true, 'data' => $deudas]);
        }
        break;
        
    case 'resumen':
        if ($method === 'GET') {
            $resumen = $api->obtenerResumenDeudas($usuario->id);
            echo json_encode(['success' => true, 'data' => $resumen]);
        }
        break;
        
    case 'pagar':
        if ($method === 'POST') {
            $datos = json_decode(file_get_contents('php://input'), true);
            $resultado = $api->marcarDeudaPagada($datos['deuda_id']);
            echo json_encode($resultado);
        }
        break;
        
    case 'categorias':
        if ($method === 'GET') {
            $categorias = $api->obtenerCategorias();
            echo json_encode(['success' => true, 'data' => $categorias]);
        }
        break;
        
    case 'usuarios':
        if ($method === 'GET') {
            $usuarios = $api->obtenerUsuariosFamilia($usuario->familia_id);
            echo json_encode(['success' => true, 'data' => $usuarios]);
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint no encontrado']);
        break;
}
?> 