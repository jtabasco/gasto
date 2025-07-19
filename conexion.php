<?php
// Configuración global de zona horaria
if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Mexico_City');
}

class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        $servername = "jtabasco.com";
        $username = "u338215117_joelgasto";
        $password = "c4C~=ns+L=";
        $dbname = "u338215117_gastos";
        
        $this->conn = new mysqli($servername, $username, $password, $dbname);
        if ($this->conn->connect_error) {
            die("Error en la conexión: " . $this->conn->connect_error);
        }
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    public function prepare($query) {
        return $this->conn->prepare($query);
    }
    
    public function query($query) {
        return $this->conn->query($query);
    }
    
    public function beginTransaction() {
        $this->conn->begin_transaction();
    }
    
    public function commit() {
        $this->conn->commit();
    }
    
    public function rollback() {
        $this->conn->rollback();
    }
}

// Obtener la instancia de la base de datos
$db = Database::getInstance();
$conn = $db->getConnection();
?>