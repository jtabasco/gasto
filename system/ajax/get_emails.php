<?php
include "../../conexion.php";
header('Content-Type: application/json');

$sql = "SELECT id, fecha_creacion, fecha_envio, destinatario, asunto, enviado FROM correos_pendientes ORDER BY fecha_creacion DESC";
$result = $conn->query($sql);

$data_array = array();
while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $fecha_creacion_original = $row['fecha_creacion'];
    $fecha_envio_original = $row['fecha_envio'];
    $fecha_creacion = date('d/m/Y H:i', strtotime($row['fecha_creacion']));
    $fecha_envio = $row['fecha_envio'] ? date('d/m/Y H:i', strtotime($row['fecha_envio'])) : '-';
    $destinatario = $row['destinatario'];
    $asunto = $row['asunto'];
    $enviado = $row['enviado'];
    
    // Estado del correo
    $estado = ($enviado == 1) ? '<span class="email-status status-sent">Enviado</span>' : '<span class="email-status status-pending">Pendiente</span>';
    
    // Botones de acción
    $botones = '<div class="text-center">
                    <button class="btn btn-sm btn-outline-primary btn-preview" onclick="previewEmail(\''.$id.'\')" title="Ver cuerpo"><i class="bi bi-eye"></i></button>
                    <button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteEmail(\''.$id.'\')" title="Eliminar"><i class="bi bi-trash"></i></button>
                </div>';
    
    // Usar fechas originales para ordenamiento, pero mostrar formato compacto
    $data_array[] = array(
        $fecha_creacion_original, // Para ordenamiento
        $fecha_envio_original,    // Para ordenamiento
        $destinatario,
        $asunto,
        $estado,
        $botones,
        $id  // Incluir el ID para que esté disponible
    );
}

echo json_encode(array("data" => $data_array));
?> 