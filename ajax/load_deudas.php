<?php
// Conectar a la base de datos
include "../conexion.php";
// Crear una variable para almacenar los datos
$data_array = array();
// SQL para obtener los datos
$sql = "SELECT usuarios.id,duedor,sum(debe) as deuda,max(DATEDIFF(CURDATE(),fecha)) as dias FROM deudas_detalladas inner join usuarios where usuarios.nombre=deudas_detalladas.duedor GROUP by duedor";
// Ejeuctar el SQL
$query = $conn->query($sql);
// Recorrer los resultados
while($data = $query->fetch_object()){
	// Poner los datos en un array en el orden de los campos de la tabla
  //$data_array[] = array($data->org, $data->comercio, $data->moneda, $data->nombre_pv, $data->chapilla, $data->ip, $data->mascara, "<a href='lo.php?".$data->getway."'>".$data->getway."</a>");
  //$data_array[] = array('<a  data-bs-toggle="modal" data-bs-target="#editModalPos" onclick=GetDataUser('.$data->id.')'.'>'.$data->nombre.'</a>', $data->rol,$data->email,$data->tel,$data->activo);	
    $data_array[] = array($data->duedor, $data->deuda,$data->dias.' <span class="edit"><i class="bi bi-messenger" onclick=enviarcorreo('.$data->id.','.$data->deuda.','.$data->dias.')></i></span>');
    //creamos conexion con la otra consulta
         $sql1 = "SELECT comprador,sum(debe) as deuda FROM deudas_detalladas WHERE duedor='$data->duedor'  group by comprador";
        // Ejeuctar el SQL
        $query1 = $conn->query($sql1);
        // Recorrer los resultados
        while($data1 = $query1->fetch_object()){
                $data_array[] = array('',$data1->comprador, $data1->deuda);
        }


}
// crear un array con el array de los datos, importante que esten dentro de : data
$new_array  = array("data"=>$data_array);
// crear el JSON apartir de los arrays

echo json_encode($new_array);
$conn->close();
?>