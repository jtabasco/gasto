<?php 
  include "include/functions.php";
 ?>


<nav class="navbar mp-5 fixed-top navbar-expand-lg navbar-dark" style="background-color:  #e3f2fd ;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Sistema Control de GASTOS FAMILIARES</a>
    <span class="navbar-text">
      Colorado, <?php echo fechaC(); ?>
    </span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav ms-auto">
    <!-- AQUI COMIENZA EL NAV -->
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="padding-right: 10px;">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="index.php">Inicio</a>
        </li>
        <?php if($_SESSION['rol'] == 3 or $_SESSION['rol'] == 0 or $_SESSION['rol'] == 1 or $_SESSION['rol'] == 4) {?>
        <li class="nav-item dropdown"> <!-- CONTRATOS -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Combustible
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #e3f2fd">
            <li><a class="dropdown-item navbar-text white" href="lista_clientes.php">Listar Clientes</a></li>
            <li><a class="dropdown-item navbar-text white" href="anonimas.php">Listar Anonimas</a></li>
            <?php if($_SESSION['rol'] == 3 or $_SESSION['rol'] == 1 or $_SESSION['rol'] == 4) {?>
            <li class="dropdown-divider"></li>
            <li><a class="dropdown-item navbar-text white" href="banco_sentai.php">Cuadre Banco-Sentai</a></li>
            <?php } ?>
            <?php if($_SESSION['rol'] == 0 or $_SESSION['rol'] == 3) {?>
            <li class="dropdown-divider"></li>
            <li><a class="dropdown-item navbar-text white" href="Estado_cuenta.php">Estado de cuenta</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <?php if($_SESSION['rol'] == 3 or $_SESSION['rol'] == 2 or $_SESSION['rol'] == 4) {?>
        <li class="nav-item dropdown"> <!-- POS -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Pos
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #e3f2fd">
            <li><a class="dropdown-item navbar-text white" href="lista_pos.php">Listar Pos</a></li>
            <li class="dropdown-divider"></li>
            <li><a class="dropdown-item navbar-text white" href="lista_inventario.php">Inventario de POS</a></li>
          </ul>
        </li>
    
        <li class="nav-item dropdown"> <!-- ROLLOS -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Rollos
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #e3f2fd">
            <li><a class="dropdown-item navbar-text white" href="lista_papel.php">Listar Despachos</a></li>
          </ul>
        </li>
    
        <li class="nav-item dropdown"> <!-- DEVOLUCIONES -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Devoluciones
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #e3f2fd">
            <li><a class="dropdown-item navbar-text white" href="lista_dev.php">Listar Devolucines</a></li>
          </ul>
        </li>
        <?php } ?>
        <?php if($_SESSION['rol'] == 3 or $_SESSION['rol'] == 2 or $_SESSION['rol'] == 1 or $_SESSION['rol'] == 4) {?>
        <li class="nav-item dropdown"> <!-- USUARIOS -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Usuario
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #e3f2fd">
            <li><a class="dropdown-item navbar-text white" href="cambiar_clave.php">Cambiar Clave</a></li>
            <?php if($_SESSION['rol'] == 3) {?>
            <li><a class="dropdown-item navbar-text white" href="lista_usuarios.php">Listar Usuarios</a></li>
            <?php } ?>
          </ul>
        </li>
        <?php } ?>
        <?php if($_SESSION['rol'] == 3) {?>
        <li class="nav-item dropdown"> <!-- UTILES -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Utiles
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #e3f2fd">
            <li><a class="dropdown-item navbar-text white" href="log.php">Log</a></li>
            <li><a class="dropdown-item navbar-text white" href="respaldo.php">Salva</a></li>
            <li><a class="dropdown-item navbar-text white" href="lista_eventos.php">Listar Eventos</a></li>
          </ul>
        </li>
      <?php } ?>


      </ul>
      <a href="salir.php"><img class="close" src="img/pwd.png" alt="Salir del sistema" title="Salir"></a>
      
    
    </div>
  </div>
  </div>
</nav>