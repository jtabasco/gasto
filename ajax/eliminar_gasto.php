<?php
include "../conexion.php"; // Incluir la conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']); // Obtener el ID del gasto a eliminar

    // Preparar la consulta para eliminar el gasto
    $consulta = $conn->prepare("DELETE FROM Gastos WHERE id = ?");
    $consulta->bind_param("i", $id);

    if ($consulta->execute()) {
        echo "Gasto eliminado con éxito.";
    } else {
        echo "Error al eliminar el gasto: " . $conn->error;
    }

    $consulta->close();
}

echo json_encode($response);
$conn->close(); // Cerrar la conexión a la base de datos
?>