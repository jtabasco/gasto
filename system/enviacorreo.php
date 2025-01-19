<?php 

	session_start();
	include "../con.php";
	require '../../vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;

    $id = $_POST['id'];
    $debe = $_POST['debe'];
    $dias = $_POST['dias'];

    // Validar entradas
    if (!isset($id, $debe, $dias)) {
        echo json_encode(false);
        exit;
    }

    $sql = mysqli_query($conn, "SELECT tel, nombre, email FROM usuarios WHERE id='$id'");
    $result = mysqli_fetch_array($sql);
    $tel = $result['tel'] . '@tmomail.net';
    $deudor = $result['nombre'];
    $email = $result['email'];

    // Función para enviar correo
    function enviarCorreo($destinatario, $asunto, $mensaje, $from) {
        $headers = "From:" . $from;
        return mail($destinatario, $asunto, $mensaje, $headers);
    }

    // Configuración del correo
    $from = "administrador@jtabasco.com";
    $subject = "Recordatorio de Deuda";
    $message = 'Hola ' . $deudor . ', tiene deudas pendientes con más de ' . $dias . ' días por un monto total de $' . $debe . '  Visita jtabasco.com/gasto para detalles.';

    // Enviar SMS
    $enviado = enviarCorreo($tel, $subject, $message, $from);

    // Enviar correo electrónico
    if (!$enviado) {
        $enviado = enviarCorreo($email, $subject, $message, $from);
    }

    echo json_encode($enviado);
?>