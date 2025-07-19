<?php
// Conectar a la base de datos
include "../conexion.php";

// Crear una variable para almacenar los datos
$data_array = array();

// SQL para obtener los datos
$sql = "SELECT 
            Gastos.id,
            usuarios.nombre AS comprador,
            Gastos.fecha,
            categoria.categoria,
            Gastos.detalle,
            Gastos.Monto
        FROM Gastos
        INNER JOIN categoria ON categoria.id = Gastos.idcat
        INNER JOIN usuarios ON usuarios.id = Gastos.comprador
        ORDER BY Gastos.id DESC";

// Ejecutar el SQL
$query = mysqli_query($conn, $sql);

// Recorrer los resultados
while($data = mysqli_fetch_array($query)){
    // Poner los datos en un array en el orden de los campos de la tabla
    $id = $data[0];
    $comprador = $data[1];
    $fecha = $data[2];
    $categoria = $data[3];
    $detalle = $data[4];
    $monto = $data[5];

    $botones = '<div class="text-center">
                    <button class="btn btn-danger btn-sm" onclick="eliminarGasto('.$id.')" title="Eliminar Gasto">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>';

    $data_array[] = array(
        $comprador,
        $fecha,
        $categoria,
        $detalle,
        '$'.number_format($monto, 2),
        $botones
    );	
}

// crear un array con el array de los datos, importante que esten dentro de : data
$new_array = array("data" => $data_array);

// crear el JSON apartir de los arrays
echo json_encode($new_array);
$conn->close();
?> 