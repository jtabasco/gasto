<?php 
session_start();
include "../conexion.php";
require_once '../utils/Mailer.php';

$id = $_POST['id'];
$pago = $_POST['pago'];
$dia = date('Y-m-d');

$sql = "UPDATE detalle_gasto SET 
        pagado='$pago',
        fecha='$dia'
        where id='$id'";

$query = $conn->query($sql);

if ($query) {
    if ($pago == 'Si') {
        // Obtener datos del deudor
        $sql = mysqli_query($conn, "SELECT detalle_gasto.idgasto, usuarios.nombre, usuarios.email, detalle_gasto.monto 
                                  FROM `detalle_gasto` 
                                  INNER JOIN usuarios 
                                  WHERE detalle_gasto.deudor=usuarios.id 
                                  AND detalle_gasto.id='$id'");
        $result = mysqli_fetch_array($sql);
        $deudor = $result['nombre'];
        $email = $result['email'];
        $deuda = $result['monto'];
        $idgasto = $result['idgasto'];
        
        // Obtener detalles de la compra
        $sql = mysqli_query($conn, "SELECT Gastos.id, Gastos.detalle, Gastos.Monto, Gastos.fecha,categoria.categoria, 
                                  usuarios.nombre as comprador, usuarios.email,Gastos.compra  
                                  FROM `Gastos` 
                                  INNER JOIN usuarios 
                                  inner join categoria
                                  WHERE usuarios.id=Gastos.comprador and Gastos.idcat=categoria.id
                                  AND Gastos.id='$idgasto'");
        $result = mysqli_fetch_array($sql);
        $comprador = $result['comprador'];
        $detalle = $result['detalle'];
        $monto = $result['Monto'];
        $fecha = $result['fecha'];
        $categoria = $result['categoria'];
        $compra = $result['compra'];
        if ($compra == 'S') {
            $compra = 'a una compra';
        } else {
            $compra = 'a un prestamo';
        }   

        // Template HTML mejorado
        $htmlBody = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;">
            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
                <h2 style="color: #0056b3; margin-bottom: 20px;">Actualización de Pago</h2>
                <p style="font-size: 16px; color: #333;">Hola, <strong>'.$deudor.'</strong></p>
                <p style="font-size: 16px; color: #333;">Se ha registrado el pago de su deuda correspondiente '.$compra.'.</p>
            </div>
            
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h3 style="color: #28a745;">Detalles de la Compra</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Categoria:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">'.$categoria.'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Concepto:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">'.$detalle.'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Monto Total:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">$'.number_format($monto, 2).'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Su Deuda:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">$'.number_format($deuda, 2).'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px;"><strong>Fecha:</strong></td>
                        <td style="padding: 8px;">'.$fecha.'</td>
                    </tr>
                </table>
            </div>
            
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 12px; color: #6c757d;">
                <p>Este es un mensaje automático, por favor no responda a este correo.</p>
                <p>Sistema de Gestión de Gastos</p>
            </div>
        </div>';

        // Usar la función enviarCorreo del Mailer
        $asunto = "Confirmación de Pago Registrado";
        $enviado = Mailer::enviarCorreo($email, $asunto, $htmlBody);
    }

    echo json_encode('ok');
} else {
    echo json_encode('No ok');    
}
$conn->close();
?>