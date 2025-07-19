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
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($desde ?? $config['from'], 'Gastos Familiares');
            
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

    /**
     * Función específica para enviar MMS que preserva los saltos de línea
     */
    public static function enviarMMS($para, $asunto, $mensaje, $desde = null) {
        $config = self::getConfig();
        $mail = new PHPMailer(true);
        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['port'];
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($desde ?? $config['from'], 'Sistema de Gastos');
            
            // Si $para es una lista de correos separados por coma
            $correos = explode(',', $para);
            foreach($correos as $correo) {
                if (!empty(trim($correo))) {
                    $mail->addAddress(trim($correo));
                }
            }
            
            // Configuración idéntica a enviacorreo.php
            $mail->isHTML(true);
            $mail->Subject = $asunto;
            $mail->Body = $mensaje;
            $mail->AltBody = strip_tags($mensaje);

            return $mail->send();
        } catch (Exception $e) {
            // Podrías agregar un log del error aquí
            return false;
        }
    }
} 