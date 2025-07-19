<?php
session_start();
include "../conexion.php";

if(isset($_POST['deudor_id'])) {
    $deudor_id = $_POST['deudor_id'];
    
    // Consulta para obtener las deudas del deudor
    $sql = "SELECT g.id, g.fecha, g.detalle, g.Monto, g.pago 
            FROM Gastos g 
            INNER JOIN deudas_comprador_deudor dcd ON g.id = dcd.gasto_id 
            INNER JOIN usuarios u ON u.id = dcd.duedor 
            WHERE u.id = ? AND g.comprador = ? AND g.pago = 'No'
            ORDER BY g.fecha DESC";
    $sql = "SELECT detalle_gasto.*,Gastos.detalle,Gastos.comprador,usuarios.nombre as comprador FROM `detalle_gasto`
            inner join Gastos on Gastos.id=detalle_gasto.idgasto 
            inner join usuarios on usuarios.id=Gastos.comprador 
            WHERE pagado='No' and deudor= ? and usuarios.nombre= ? ";
    

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $deudor_id, $_SESSION['user']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $deudas = array();
    while($row = $result->fetch_assoc()) {
        $deudas[] = array(
            "id" => $row['id'],
            "fecha" => $row['fecha'],
            "detalle" => $row['detalle'],
            "monto" => $row['monto'],
            "estado" => $row['pagado']
        );
    }
    
    echo json_encode(array("data" => $deudas));
    $conn->close();
} else {
    echo json_encode(array("error" => "No se proporcionó ID de deudor"));
}
?> 