<?php
include "../conexion.php";

$id = $_POST['id'];

$query = mysqli_query($conn, "SELECT * FROM familia WHERE id = $id");
$result = array();

while ($row = mysqli_fetch_array($query)) {
    $result[] = array(
        $row[0], // id
        $row[1], // nombre
        $row[2]  // descripcion
    );
}

echo json_encode($result);
$conn->close();
?> 