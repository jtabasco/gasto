<?php
session_start();
include "../../conexion.php";

header('Content-Type: application/json');

// Solo admin puede eliminar
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    $conn->close();
    exit;
}

$id = $_POST['id'] ?? $_GET['id'] ?? null;
if (!is_numeric($id)) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    $conn->close();
    exit;
}
$id = intval($id);

$sql = "DELETE FROM correos_pendientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close(); 