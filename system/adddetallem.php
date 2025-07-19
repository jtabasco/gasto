<?php 
	session_start();
	include "../conexion.php";
    $deudor=$_POST['deudor'];
    $pagado=$_POST['pago'];
    $fecha=date('Y-m-d');
    $familia_id = $_SESSION['familia'] ?? 0;
    $user_id = $_SESSION['idUser'] ?? 0;
   
    // Iniciar transacción para evitar condiciones de carrera
    $conn->begin_transaction();
    
    try {
        if ($deudor==0){
            // Añadir detalles de gastos con filtro de familia
            $sql = "INSERT INTO tdetalle_gasto (deudor)
                    SELECT id 
                    FROM usuarios 
                    WHERE activo='Si' AND familia_id = ? AND id != ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $familia_id, $user_id);
            $stmt->execute();
            
            // Actualizar importe por dividir
            $sql = "UPDATE tdetalle_gasto SET pagado=?, fecha=? 
                    WHERE deudor IN (SELECT id FROM usuarios WHERE familia_id = ? AND id != ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssii", $pagado, $fecha, $familia_id, $user_id);
            $stmt->execute();
        } else {
            // Verificar que el deudor pertenece a la misma familia
            $sql = "SELECT id FROM usuarios WHERE id = ? AND familia_id = ? AND activo = 'Si'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $deudor, $familia_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if($result->num_rows > 0) {
                $sql = "INSERT INTO tdetalle_gasto (deudor,pagado,fecha) VALUES(?,?,?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iss", $deudor, $pagado, $fecha);
                $stmt->execute();
            } else {
                throw new Exception("Usuario no válido para esta familia");
            }
        }
        
        // Confirmar transacción
        $conn->commit();
        echo json_encode('ok');
        
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();
        echo json_encode('No ok: ' . $e->getMessage());
    }
    
    $conn->close();
?>