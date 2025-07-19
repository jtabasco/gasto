<?php 
    session_start();
    include "../conexion.php";   
    
    // Validación más robusta que considera la familia del usuario
    $familia_id = $_SESSION['familia'] ?? 0;
    $user_id = $_SESSION['idUser'] ?? 0;
    
    // Consulta mejorada que filtra por familia y excluye al comprador
    $sql = "SELECT COUNT(*) as total FROM tdetalle_gasto td 
            INNER JOIN usuarios u ON td.deudor = u.id 
            WHERE u.familia_id = ? AND u.id != ? AND u.activo = 'Si'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $familia_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if($row['total'] > 0){
        echo 'ok';
    } else {
        echo 'Nook';   
    }
    
    $stmt->close();
    $conn->close();
?>