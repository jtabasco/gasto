<?php
include "../conexion.php";

$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

$query = mysqli_query($conn, "INSERT INTO familia (nombre, descripcion) VALUES ('$nombre', '$descripcion')");

if ($query) {
    echo 1;
} else {
    echo 0;
}

echo json_encode($response);
$conn->close();
?> 