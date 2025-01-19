<?php 
session_start();
include "../con.php";

// Validación de datos de entrada
if (!isset($_SESSION['idUser']) || empty($_POST['Detalle']) || empty($_POST['Monto']) || empty($_POST['Cat'])) {
    echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
    exit;
}

// Sanitización de datos
$comprador = $_SESSION['idUser'];
$detalle = $_POST['Detalle'];
$monto = floatval($_POST['Monto']);
$categoria = $_POST['Cat'];

// Insertar plantilla usando prepared statement
$sql = "INSERT INTO plantilla_gastos (comprador, detalle, Monto, idcat) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isdi", $comprador, $detalle, $monto, $categoria);

try {
    if ($stmt->execute()) {
        $maxid = $conn->insert_id; // Mejor que hacer una consulta adicional
        
        // Actualizar y copiar detalles de gastos
        $conn->begin_transaction();
        
        $sql = "UPDATE tdetalle_gasto SET idgasto = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $maxid);
        $stmt->execute();
        
        // Copiar detalles
        $sql = "INSERT INTO plantilla_detalle_gasto (idgasto, deudor, monto, pagado)
                SELECT idgasto, deudor, monto, pagado FROM tdetalle_gasto";
        $conn->query($sql);
        
        // Limpiar tabla temporal
        $conn->query("DELETE FROM tdetalle_gasto");
        
        $conn->commit();
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception("Error en la inserción");
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>