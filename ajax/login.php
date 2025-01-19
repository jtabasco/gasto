<?php
include "../con.php";

 session_start();


// Obtener los datos del formulario
$username = $_POST['logina'];
$password = md5($_POST['clavea']);
//$password = $_POST['password'];

// Consultar la base de datos para verificar las credenciales
$sql = "SELECT * FROM usuarios WHERE (nombre='$username' or email='$username') AND activo='Si'";
$result = $conn->query($sql);

//$conn->query($sql);

if ($result->num_rows == 1) {
  $row = $result->fetch_assoc();
  $stored_password = $row['pass'];

  // Verificar si la contraseña ingresada coincide con el hash almacenado
  //if (password_verify($password, $stored_password)) {
  if ($password==$stored_password) {  
    // Credenciales válidas, el usuario ha iniciado sesión correctamente
        $_SESSION['active'] = true;
        $_SESSION['idUser'] = $row['id'];
        $_SESSION['user'] = $row['nombre'];
        $_SESSION['rol']    = $row['rol_id'];
        //$_SESSION['rolN']    = $data['rol'];
         //header('location: /system/');
    echo "¡Bienvenido!";
    // Aquí podrías redirigir al usuario a una página de inicio o realizar otras acciones
  } else {
    // Credenciales inválidas, mostrar un mensaje de error
    echo "Nombre de usuario o contraseña incorrectos";
  }
} else {
  // Usuario no encontrado en la base de datos
  echo "Nombre de usuario o contraseña incorrectos";
}

$conn->close();





?>
