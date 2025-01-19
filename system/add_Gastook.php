<?php 
session_start();
include "../con.php";
require_once '../utils/Mailer.php';

$Comprador = $_SESSION['idUser'];
$Detalle = $_POST['Detalle'];
$Monto = $_POST['Monto'];
$Fecha = $_POST['Fecha'];
$Cat = $_POST['Cat'];

$sql = "INSERT INTO  Gastos (comprador,detalle,Monto,fecha,idcat) 
        VALUES('$Comprador','$Detalle','$Monto','$Fecha','$Cat')";
        
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

    $sql = "UPDATE detalle_gasto c 
            INNER JOIN (SELECT id, deuda FROM deudaXcompra) x 
            ON c.idgasto = x.id
            SET c.monto=x.deuda";
    $conn->query($sql);

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
        $messageSMS = 'Hola ' . $data->deudor . ', se ha registrado una compra de $' . $monto . 
                     ' en la cual debes $' . $deuda . ' Total de Deudas $' . $tdeuda . 
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
                <h2>Confirmación de Compra Registrada</h2>
            </div>
            <div class="content">
                <p>Hola <strong>'.$comprador.'</strong>,</p>
                <p>Se ha registrado exitosamente su compra con los siguientes detalles:</p>
                
                <div class="details">
                    <p><strong>Concepto:</strong> '.$datalle.'</p>
                    <p><strong>Categoria:</strong> '.$categoria.'</p>
                    <p><strong>Monto Total:</strong> <span class="amount">$'.$monto.'</span></p>
                    <p><strong>Fecha:</strong> '.$fecha.'</p>
                </div>

                <p>La compra ha sido repartida entre los siguientes deudores:</p>
                <p>Monto por persona: <span class="amount">$'.$deuda.'</span></p>
                <p><strong>Deudores:</strong> '.$deudores.'</p>
            </div>
            <div class="footer">
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
                <p>Se ha registrado una nueva compra que requiere tu atención:</p>
                
                <div class="details">
                    <p><strong>Comprador:</strong> '.$comprador.'</p>
                    <p><strong>Concepto:</strong> '.$datalle.'</p>
                    <p><strong>Categoria:</strong> '.$categoria.'</p>
                    <p><strong>Monto Total:</strong> <span class="amount">$'.$monto.'</span></p>
                    <p><strong>Tu parte:</strong> <span class="amount">$'.$deuda.'</span></p>
                    <p><strong>Fecha:</strong> '.$fecha.'</p>
                </div>

                <p>Esta compra ha sido repartida entre: '.$deudores.' y '.$comprador.'</p>
                
                <center>
                    <a href="https://jtabasco.com/gasto" class="action-button">Ver Detalles</a>
                </center>
            </div>
            <div class="footer">
                <p>Sistema de Gestión de Gastos</p>
            </div>
        </div>
    </body>
    </html>';

    // Enviar correos con formato HTML
    Mailer::enviarCorreo($compradoremail, "Confirmación de compra registrada", $messageComprador);
    Mailer::enviarCorreo($to, "Notificación de deuda nueva", $messageDeudores);

    echo json_encode('ok');
} else {
    echo json_encode('No ok');    
}
?>