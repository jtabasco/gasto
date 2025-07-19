<?php
include "../conexion.php";

// Configurar zona horaria


$query = mysqli_query($conn, "SELECT * FROM familia ORDER BY nombre ASC");
$result = array();
$result['data'] = array();

while ($row = mysqli_fetch_array($query)) {
    $id = $row[0];
    $nombre = $row[1];
    $descripcion = $row[2];
    $fecha = $row[3];

    // Formatear la fecha en la zona horaria local
    if ($fecha) {
        $fecha_obj = new DateTime($fecha);
        $fecha_formateada = $fecha_obj->format('d/m/Y H:i');
    } else {
        $fecha_formateada = 'N/A';
    }

    $botones = '<div class="text-center">
                    <button class="btn btn-warning btn-sm action-btn edit-btn" onclick="GetDataFamilia('.$id.')" data-bs-toggle="modal" data-bs-target="#editFamilia" title="Editar Familia">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </div>';

    $result['data'][] = array(
        $nombre,
        $descripcion,
        $fecha_formateada,
        $botones
    );
}

echo json_encode($result);
$conn->close();
?> 