<?php
include "../conexion.php";
session_start();

// Headers para respuesta limpia
header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

// Obtener los datos del formulario
$username = $_POST['logina'];
$password = md5($_POST['clavea']);

// Debug: Ver qué datos llegan
error_log("Login attempt - Username: $username, Password MD5: $password");

// Usar consulta preparada
$stmt = $conn->prepare("SELECT usuarios.*, familia.nombre as familia_nombre 
                       FROM usuarios 
                       INNER JOIN familia ON usuarios.familia_id = familia.id 
                       WHERE (usuarios.nombre = ? OR usuarios.email = ?) 
                       AND usuarios.activo = 'Si' 
                       LIMIT 1");

if (!$stmt) {
    error_log("Error preparing statement: " . $conn->error);
    echo "Error de conexión";
    exit;
}

$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

error_log("Usuarios encontrados: " . $result->num_rows);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $stored_password = $row['pass'];
    
    error_log("Usuario encontrado - ID: " . $row['id'] . ", Password almacenado: " . $stored_password);

    // Verificar si la contraseña ingresada coincide con el hash almacenado
    if ($password == $stored_password) {  
        // Credenciales válidas, el usuario ha iniciado sesión correctamente
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $row['id'];
        $_SESSION['user'] = $row['nombre'];
        $_SESSION['rol'] = $row['rol_id'];
        $_SESSION['familia'] = $row['familia_id'];
        $_SESSION['familia_nombre'] = $row['familia_nombre'];
        
        error_log("Login exitoso para usuario: " . $row['nombre']);
        echo "¡Bienvenido!";
    } else {
        // Credenciales inválidas, mostrar un mensaje de error
        error_log("Password incorrecto - Ingresado: $password, Almacenado: $stored_password");
        echo "Credenciales incorrectas";
    }
} else {
    // Usuario no encontrado en la base de datos
    error_log("Usuario no encontrado: $username");
    echo "Credenciales incorrectas";
}

$stmt->close();
$conn->close();
?>
