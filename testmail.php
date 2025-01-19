<?php
    //aleatoria
        $codigo = rand(1000,9999);


    ini_set( 'display_errors', 1 );
    error_reporting( E_ALL );
    $from = "administrador@jtabasco.com";
    $to = "jtabasco41@gmail.com";
    $subject = "Se solicita verificacion de su correo.";
    //$message = "PHP mail works just fine";
    $message = 'Hola,'.chr(13).'Su codigo de vericación es '.$codigo.chr(13).'Haga click en el siguiente enlase para activar su cuenta'.chr(13).chr(10).'http://localhost/logingrijalva/Sistema-de-login-y-registro-con-auth-token/confirm.php?email='.$to.chr(13).chr(10).'Atentamente '.chr(13).chr(10).'administrador del sistema.';
    $headers = "From:" . $from;

    // Enviarlo
    $enviado=false;
    if (mail($to,$subject,$message, $headers)){
        $enviado=true;
        };

    echo $enviado;    
?>        
