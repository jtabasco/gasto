<?php 
session_start();
include "../con.php";
require_once '../utils/Mailer.php';

$Comprador = $_SESSION['idUser'];
$Detalle = $_POST['Detalle'];
$Monto = $_POST['Monto'];
$Fecha = $_POST['Fecha'];
$Deudor = $_POST['Deudor'];
$Cat = 6;
$compra='N';
$pagado='No';
//insertamos duedor en tdetalle_gasto
$sql = "INSERT INTO  tdetalle_gasto (deudor,monto,pagado,fecha) 
				VALUES('$Deudor','$Monto','$pagado','$Fecha')";
				
		// Ejeuctar el SQL
		$conn->query($sql);

$sql = "INSERT INTO  Gastos (comprador,detalle,Monto,fecha,idcat,compra) 
        VALUES('$Comprador','$Detalle','$Monto','$Fecha','$Cat','$compra')";
        
if ($conn->query($sql)) {
    // Obtener el id del último gasto
    $sql = mysqli_query($conn, "SELECT MAX(id) as maxid FROM `Gastos` WHERE 1");
    $result = mysqli_fetch_array($sql);
    $maxid = $result['maxid'];
    
    // Actualizar y procesar detalles del gasto
    $sql = "UPDATE `tdetalle_gasto` SET `idgasto`=" . $maxid;
    $conn->query($sql);

    $sql = "INSERT INTO detalle_gasto (idgasto,deudor,monto,fecha,pagado)
            SELECT idgasto,deudor,monto,fecha,pagado
            FROM tdetalle_gasto";
    $conn->query($sql);

    // $sql = "UPDATE detalle_gasto c 
    //         INNER JOIN (SELECT id, deuda FROM deudaXcompra) x 
    //         ON c.idgasto = x.id
    //         SET c.monto=x.deuda";
    // $conn->query($sql);

    $sql = "DELETE FROM tdetalle_gasto";
    $conn->query($sql);

    // Obtener información del comprador
    $sql = mysqli_query($conn, "SELECT Gastos.id, Gastos.detalle, categoria.categoria, Gastos.Monto, Gastos.fecha, 
            usuarios.nombre as comprador, usuarios.email 
            FROM `Gastos` 
            INNER JOIN usuarios 
            inner join categoria
            WHERE usuarios.id=Gastos.comprador and Gastos.idcat=categoria.id AND Gastos.id='$maxid'");
    $result = mysqli_fetch_array($sql);
    
    $comprador = $result['comprador'];
    $compradoremail = $result['email'];
    $datalle = $result['detalle'];
    $monto = $result['Monto'];
    $fecha = $result['fecha'];
    $categoria = $result['categoria'];

    // Obtener información de los deudores
    $sql = "SELECT detalle_gasto.idgasto, detalle_gasto.monto, 
            usuarios.nombre as deudor, usuarios.email, usuarios.tel 
            FROM detalle_gasto 
            INNER JOIN usuarios 
            WHERE usuarios.id=detalle_gasto.deudor 
            AND detalle_gasto.idgasto='$maxid'";
    $query = $conn->query($sql);

    $to = "";
    $deudores = "";
    
    while($data = $query->fetch_object()) {
        $to .= $data->email . ",";
        $deudores .= $data->deudor . ", ";
        $deuda = $data->monto;
        $tel = $data->tel . '@tmomail.net';

		$sqlo = mysqli_query($conn,"SELECT * FROM `deudas` WHERE duedor='".$data->deudor."'");
		$resultado = mysqli_fetch_array($sqlo);
		$tdeuda = $resultado['Deuda'];

        // Enviar SMS
        $subject = "DEUDA NUEVA";
        $messageSMS = 'Hola ' . $data->deudor . ', se ha registrado un prestamo de $' . $monto . 
                     ' Total de Deudas $' . $tdeuda . 
                     ' detalles jtabasco.com/gasto';
        Mailer::enviarCorreo($tel, $subject, $messageSMS);
    }

    $deudores = substr($deudores, 0, -2) . ".";
    $to = substr($to, 0, -1);

    // Mensaje HTML para el comprador
    $messageComprador = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #4CAF50; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .details { margin: 20px 0; }
            .amount { color: #4CAF50; font-weight: bold; }
            .footer { text-align: center; padding: 15px; background: #f1f1f1; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Confirmación de prestamo Registrado</h2>
            </div>
            <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h3 style="color: #28a745;">Detalles del Prestamo</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Categoria:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">'.$categoria.'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Concepto:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">'.$datalle.'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Monto Total:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">$'.number_format($monto, 2).'</td>
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
        </div>
    </body>
    </html>';

    // Mensaje HTML para los deudores
    $messageDeudores = '
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #FF5722; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .details { margin: 20px 0; }
            .amount { color: #FF5722; font-weight: bold; }
            .footer { text-align: center; padding: 15px; background: #f1f1f1; }
            .action-button { 
                background: #FF5722; 
                color: white; 
                padding: 10px 20px; 
                text-decoration: none; 
                border-radius: 5px; 
                display: inline-block; 
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>Nueva Deuda Registrada</h2>
            </div>
            <div class="content">
                <p>Hola <strong>'.$data->deudor.'</strong>,</p>
                <p>Se ha registrado un nuevo prestamo que requiere tu atención:</p>
                 <div style="background-color: #ffffff; border: 1px solid #dee2e6; border-radius: 10px; padding: 20px; margin-bottom: 20px;">
                <h3 style="color: #28a745;">Detalles del Prestamo</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Categoria:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">'.$categoria.'</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;"><strong>Concepto:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">'.$datalle.'</td>
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
                

                
                
                <center>
                    <a href="https://jtabasco.com/gasto" class="action-button">Ver Detalles</a>
                </center>
            </div>
            <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; font-size: 12px; color: #6c757d;">
                <p>Este es un mensaje automático, por favor no responda a este correo.</p>
                <p>Sistema de Gestión de Gastos</p>
            </div>
        </div>
    </body>
    </html>';

    // Enviar correos con formato HTML
    Mailer::enviarCorreo($compradoremail, "Confirmación de prestamo registrado", $messageComprador);
    Mailer::enviarCorreo($to, "Notificación de deuda nueva", $messageDeudores);

    echo json_encode('ok');
} else {
    echo json_encode('No ok');    
}
?>