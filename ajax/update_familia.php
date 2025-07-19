<?php
include "../conexion.php";

$id = $_POST['id'];
$nombre = $_POST['nombre'];
$descripcion = $_POST['descripcion'];

$query = mysqli_query($conn, "UPDATE familia SET nombre = '$nombre', descripcion = '$descripcion' WHERE id = $id");

if ($query) {
    echo 1;
} else {
    echo 0;
}

echo json_encode($response);
$conn->close();
?> 