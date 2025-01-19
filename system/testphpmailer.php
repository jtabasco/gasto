<?php
   require '../../vendor/autoload.php';
   use PHPMailer\PHPMailer\PHPMailer;
   $codigo = rand(1000,9999);
   $to='jtabascoh@gmail.com';
   $to1='jtabasco41@gmail.com';
   $mail->CharSet = 'UTF-8';
   $mail->Encoding = 'base64';
  
   $mail = new PHPMailer;
   $mail->isSMTP();
   $mail->SMTPDebug = 0;
   $mail->Host = 'smtp.hostinger.com';
   $mail->Port = 587;
   $mail->SMTPAuth = true;
   $mail->Username = 'administrador@jtabasco.com';
   $mail->Password = 'HUAOn$wX&3';
   $mail->setFrom('administrador@jtabasco.com', 'Administrador');
   $mail->addReplyTo('administrador@jtabasco.com', 'Administrdor');
   $mail->addAddress($to, 'Joel');
   $mail->addAddress($to1, 'Joel');
   $mail->Subject = 'Se solicita verificacion de su correo';
   $mail->msgHTML(file_get_contents('message.html'), __DIR__);
   // $mail->Body = 'El contenido de tu correo en HTML.<br> Los elementos en <b><h1>'.$codigo. '</h1></b> también están permitidos.';
   $htmlBody='
    <table width="100%" border="0" cellspacing="10" cellpadding="0">
        <tr>
            <td>Estimado usuario:'. $codigo.'</td>
        </tr>
        <tr>
            <td>Usted ha solicitado una recuperación de contraseña para su usuario por lo que anexamos al siguiente correo la contraseña correspondiente para su inicio de sesión:</td>
        </tr>
        <tr>
            <td>CONTRASEÑA |'. $codigo.'</td>
        </tr>
        <tr>
            <td>
                <p>Recuerde que el siguiente correo eelectrónico es totalmente confidencial, por lo tanto agradecemos su no divulgación.</p>
                <p>No responda a este correo electrónico.</p>
                <p>El correo electrónico ha sido enviado a través de nuestro robot en línea.</p>
                <p>&nbsp;</p>
                <p>Atentamente.</p>
                <p>Plataforma Automatizada.</p>
            </td>
        </tr>
    </table>';

$mail->Body = $htmlBody; 
   if (!$mail->send()) {
       echo 'Mailer Error: ' . $mail->ErrorInfo;
   } else {
       echo 'The email message was sent.';
   }
  ?> 