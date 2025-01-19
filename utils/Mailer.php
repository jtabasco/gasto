<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {
    private static function getConfig() {
        return include __DIR__ . '/../config/mail_config.php';
    }

    public static function enviarCorreo($para, $asunto, $mensaje, $desde = null) {
        $config = self::getConfig();
        $mail = new PHPMailer(true);
        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isSMTP();
            $mail->SMTPDebug = 0;
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];

            $mail->setFrom($desde ?? $config['from']);
            
            // Si $para es una lista de correos separados por coma
            $correos = explode(',', $para);
            foreach($correos as $correo) {
                if (!empty(trim($correo))) {
                    $mail->addAddress(trim($correo));
                }
            }
            
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;

            return $mail->send();
        } catch (Exception $e) {
            // Podrías agregar un log del error aquí
            return false;
        }
    }
} 