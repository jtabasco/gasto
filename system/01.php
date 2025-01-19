<?php 
 require '../../vendor/autoload.php';
 use PHPMailer\PHPMailer\PHPMailer;

 $to='jtabascoh@gmail.com';
 $asunto='Prueba de correo en fuiones';
 $mensaje='
            <table width="100%" border="0" cellspacing="10" cellpadding="0">
                <tr>
                    <td>Estimado, '.$to.'</td>
                </tr>
                <tr>
                    <td>Usted ha solicitado restablecer su contraseña para eso utilice el siguiente codigo para verificar que es usted.</td>
                </tr>
                <tr>
                    <td><b><h1>'. $to.'</h1><b></td>
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
 $exito=enviacorreo($to,$asunto,$mensaje);
 echo $exito;


function enviacorreo($to,$subj,$mensaje) {
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
           $mail->addAddress($to);
           //$mail->addAddress($to1, 'Joel');
           $mail->Subject = $subj;
           $mail->msgHTML(file_get_contents('message.html'), __DIR__);
           $htmlBody=$mensaje;
      	   $mail->Body = $htmlBody; 
           if (!$mail->send()) {
               //echo 'Mailer Error: ' . $mail->ErrorInfo;
               $enviado=false;
           } else {
               //echo 'ok';
               $enviado=true;
           };       
				return $enviado;    
	}

 ?>