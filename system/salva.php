<?php
// Datos de conexión a la base de datos
include "../conexion.php";
$host = 'jtabasco.com';
$user = 'u338215117_joelgasto';
$password = 'c4C~=ns+L=a';
$database = 'u338215117_gastoss';
$backup_file = 'backup_' . date('Ymd_His') . '.sql'; // Nombre del archivo de respaldo

// Comando mysqldump
$command = "mysqldump -h $host -u $user -p$password $database > $backup_file";

// Ejecutar el comando
exec($command, $output, $return_var);

if ($return_var === 0) {
    echo "Copia de seguridad realizada correctamente: $backup_file";
} else {
    echo "Error al realizar la copia de seguridad";
}
?>;