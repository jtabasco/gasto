<?php
include "../conexion.php";

$query = "SELECT creditor, debtor, SUM(net) as total_net 
          FROM deudas_netas 
          GROUP BY creditor, debtor";

$result = $conn->query($query);

$data = array();
while ($row = $result->fetch_assoc()) {
    // Get creditor name
    $creditor_query = "SELECT nombre FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($creditor_query);
    $stmt->bind_param("s", $row['creditor']);
    $stmt->execute();
    $creditor_result = $stmt->get_result();
    $creditor_name = $creditor_result->fetch_assoc()['nombre'];
    $creditor_result->free();
    $stmt->close();
    
    // Get debtor name and family
    $debtor_query = "SELECT nombre, familia_id FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($debtor_query);
    $stmt->bind_param("s", $row['debtor']);
    $stmt->execute();
    $debtor_result = $stmt->get_result();
    $debtor_row = $debtor_result->fetch_assoc();
    $debtor_name = $debtor_row['nombre'];
    $familia_id = $debtor_row['familia_id'];
    $debtor_result->free();
    $stmt->close();
    
    // Get family name
    $familia_nombre = '';
    if ($familia_id) {
        $familia_query = "SELECT nombre FROM familia WHERE id = ?";
        $stmt = $conn->prepare($familia_query);
        $stmt->bind_param("i", $familia_id);
        $stmt->execute();
        $familia_result = $stmt->get_result();
        $familia_row = $familia_result->fetch_assoc();
        $familia_nombre = $familia_row ? $familia_row['nombre'] : '';
        $familia_result->free();
        $stmt->close();
    }
    
    $data[] = array(
        $debtor_name,
        $creditor_name,
        number_format($row['total_net'], 2),
        $familia_nombre
    );
}

$response = array(
    "data" => $data
);

echo json_encode($response);
$conn->close();