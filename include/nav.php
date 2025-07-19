<?php 
  include "functions.php";
  include "script.php";
?>

<style>
.navbar-gradient {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%) !important;
    color: #fff !important;
}
.navbar-gradient .navbar-brand,
.navbar-gradient .nav-link,
.navbar-gradient .navbar-toggler-icon,
.navbar-gradient .navbar-nav .nav-link,
.navbar-gradient .navbar-nav .nav-link.active,
.navbar-gradient .navbar-nav .nav-link:focus,
.navbar-gradient .navbar-nav .nav-link:hover {
    color: #fff !important;
}
</style>

<nav class="navbar navbar-gradient mp-5 fixed-top navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand " href="#">GASTOS FAMILIARES </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="padding-right: 10px;">
        <li class="nav-item">
          <a class="nav-link active"  aria-current="page" href="index.php">
            <i class="bi bi-house-door"></i> Home
          </a>
        </li>
        <?php if($_SESSION['rol'] == 1) {?>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="dashboard_admin.php">
              <i class="bi bi-graph-up"></i> Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="lista_deudas.php">
              <i class="bi bi-cash-coin"></i> Deudas
            </a>
          </li>  
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="lista_usuarios.php">
              <i class="bi bi-people"></i> Usuarios
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="lista_gastos.php">
              <i class="bi bi-receipt"></i> Gastos
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="correos_enviados.php">
              <i class="bi bi-envelope"></i> Correos Enviados
            </a>
          </li>
         <?php } ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active btn btn-danger text-white" href="logout.php">
          <i class="bi bi-box-arrow-right"></i>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>