<?php
include "../conexion.php";

$debtor = $_POST['debtor'];

$query = "SELECT creditor, SUM(net) as total_net 
          FROM deudas_netas 
          WHERE debtor = ? 
          GROUP BY creditor";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $debtor);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
$total_general = 0;
while ($row = $result->fetch_assoc()) {
    // Get creditor name from usuarios table
    $creditor_query = "SELECT nombre FROM usuarios WHERE nombre = ?";
    $stmt2 = $conn->prepare($creditor_query);
    $stmt2->bind_param("s", $row['creditor']);
    $stmt2->execute();
    $creditor_result = $stmt2->get_result();
    $creditor_name = $creditor_result->fetch_assoc()['nombre'];
    $stmt2->close();
    $creditor_result->free();
    
    // Icono de correo dentro de la celda del acreedor
    $acreedor_html = '<span style="display:flex;align-items:center;justify-content:space-between;">'
        . htmlspecialchars($creditor_name) .
        '<i class="bi bi-envelope-fill" style="cursor:pointer;color:#007bff;font-size:1.1em;margin-left:8px;" onclick="enviarCorreoDeudas(\''.$debtor.'\', \''.$creditor_name.'\')"></i>'
        . '</span>';
    
    $data[] = array(
        $acreedor_html,
        number_format($row['total_net'], 2)
    );
    
    $total_general += $row['total_net'];
}

$response = array(
    "data" => $data,
    "total_general" => number_format($total_general, 2)
);

echo json_encode($response);
$conn->close(); 