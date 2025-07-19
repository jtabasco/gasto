<?php 
include "../conexion.php";   
require_once '../utils/Mailer.php';

$email = $_POST['email'];

// Revisamos si el email existe
$res = $conn->query("SELECT email FROM usuarios WHERE email='".$email."'");

if(mysqli_num_rows($res) == 0){
    echo 'noexiste';
    exit;
}

// Si el correo existe, obtenemos el nombre del usuario
$sqluser = mysqli_query($conn,"SELECT email,nombre FROM usuarios WHERE email='".$email."'");
$row = mysqli_fetch_array($sqluser);
$usuario = $row['nombre'];

// Generamos código aleatorio de 4 dígitos
$codigo = rand(1000,9999);

// Preparamos el contenido del correo
$htmlBody = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4A90E2;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
            background-color: #ffffff;
        }
        .codigo {
            font-size: 45px;
            font-weight: bold;
            text-align: center;
            color: #4A90E2;
            padding: 20px;
            margin: 20px 0;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #666666;
            border-top: 1px solid #eeeeee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Restablecimiento de Contraseña</h2>
        </div>
        <div class="content">
            <p>Estimado/a '.$usuario.',</p>
            
            <p>Hemos recibido una solicitud para restablecer su contraseña. Para verificar su identidad, por favor utilice el siguiente código:</p>
            
            <div class="codigo">'.$codigo.'</div>
            
            <p>Por razones de seguridad, este código expirará en breve. Si usted no solicitó restablecer su contraseña, puede ignorar este mensaje.</p>
            
            <p><strong>Importante:</strong> No comparta este código con nadie.</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no responda a este mensaje.</p>
            <p>© '.date("Y").' jtabasco. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>';

// Configuración para enviar el correo
$asunto = 'Solicitud de restablecer contraseña';
$altBody = 'Su código de verificación es: '.$codigo;

// Enviamos el correo usando la función enviarCorreo
$enviado = Mailer::enviarCorreo($email, $asunto, $htmlBody);

if($enviado) {
    // Limpiamos registros anteriores y guardamos el nuevo código
    $conn->query("DELETE from restablecer WHERE email='$email'") or die($conn->error);
    $conn->query("INSERT INTO restablecer (email, codigo, fecha) 
                 VALUES ('$email', '$codigo', DATE_SUB(NOW(),INTERVAL 7 HOUR))") or die($conn->error);
    echo 'ok';
} else {
    echo 'error';
}

$conn->close();
?>