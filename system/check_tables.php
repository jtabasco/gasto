<?php
session_start();
include "../conexion.php";

// Verificar tablas existentes
header('Content-Type: application/json');

$debug_info = [
    'timestamp' => date('Y-m-d H:i:s'),
    'existing_tables' => [],
    'table_structures' => []
];

try {
    // Obtener lista de tablas
    $sql = "SHOW TABLES";
    $result = $conn->query($sql);
    
    while($row = $result->fetch_array()) {
        $table_name = $row[0];
        $debug_info['existing_tables'][] = $table_name;
        
        // Verificar estructura de tablas importantes
        if (in_array($table_name, ['Gastos', 'detalle_gasto', 'tdetalle_gasto', 'deudaXcompra', 'usuarios'])) {
            $sql_structure = "DESCRIBE $table_name";
            $result_structure = $conn->query($sql_structure);
            $structure = [];
            while($row_structure = $result_structure->fetch_assoc()) {
                $structure[] = $row_structure;
            }
            $debug_info['table_structures'][$table_name] = $structure;
        }
    }
    
    // Verificar si deudaXcompra existe como vista
    $sql = "SELECT COUNT(*) as total FROM information_schema.views WHERE table_schema = DATABASE() AND table_name = 'deudaXcompra'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $debug_info['deudaXcompra_exists_as_view'] = ($row['total'] > 0);
    
    // Si existe como vista, verificar su estructura
    if ($debug_info['deudaXcompra_exists_as_view']) {
        $sql = "SHOW CREATE VIEW deudaXcompra";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $debug_info['deudaXcompra_view_definition'] = $row['Create View'];
        
        // Verificar si la vista tiene datos
        $sql = "SELECT COUNT(*) as total FROM deudaXcompra";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $debug_info['deudaXcompra_has_data'] = ($row['total'] > 0);
    }
    
    // Verificar si deudaXcompra existe
    $sql = "SELECT COUNT(*) as total FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'deudaXcompra'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $debug_info['deudaXcompra_exists'] = ($row['total'] > 0);
    
    // Si no existe, verificar qué tablas tienen datos de deuda
    if (!$debug_info['deudaXcompra_exists']) {
        $sql = "SELECT COUNT(*) as total FROM detalle_gasto WHERE monto IS NULL OR monto = 0";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $debug_info['detalle_gasto_null_monto'] = $row['total'];
    }
    
} catch (Exception $e) {
    $debug_info['error'] = $e->getMessage();
}

echo json_encode($debug_info, JSON_PRETTY_PRINT);
$conn->close();
?> 