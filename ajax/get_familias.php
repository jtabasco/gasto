<?php
include "../conexion.php";

$query = mysqli_query($conn, "SELECT id, nombre FROM familia ORDER BY nombre ASC");
$result = array();

while ($row = mysqli_fetch_array($query)) {
    $result[] = array(
        $row[0], // id
        $row[1]  // nombre
    );
}

echo json_encode($result);
$conn->close();
?> 