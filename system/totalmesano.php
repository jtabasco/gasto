<?php

session_start();
include "../conexion.php";

$ano = $_POST['ano'];
$mes = $_POST['mes'];

//$data_array = array();
//$data_array = [];
// SQL para obtener los datos
$sql = "SELECT sum(Monto) as importe 
        FROM `Gastos` 
        inner join usuarios on usuarios.id=Gastos.comprador 
        where usuarios.familia_id = '" . $_SESSION['familia'] . "' 
        and month(fecha)='$mes' 
        and year(fecha)='$ano'";

// Ejeuctar el SQL
$query = $conn->query($sql);

$result = mysqli_query($conn, $sql);
$cont = mysqli_num_rows($result);
if ($cont == 0) { // no hay regitos lleva el total
  echo '$ 0.00';
};

// Recorrer los resultados

while ($data = $query->fetch_object()) {
  // Poner los datos en un array en el orden de los campos de la tabla

  //$data_array[] = array($data->id,$data->comprador, $data->detalle, $data->Monto,$data->fecha, $data->duedor, $data->pagado, $data->debe);
  echo '$ ' . $data->importe;
}
// crear un array con el array de los datos, importante que esten dentro de : data

$conn->close();
?>
