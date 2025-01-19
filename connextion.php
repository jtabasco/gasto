<?php
// Conexión a la base de datos
$servername = "jtabasco.com";
$username = "u338215117_joelgasto";
//$password = "5Yv=3f/@m";
$password = "c4C~=ns+L=";
$dbname = "u338215117_gastos";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Error en la conexión: " . $conn->connect_error);
}
?>