<?php 
  include "functions.php";
  include "script.php";
?>

<nav class="navbar bg-primary mp-5 fixed-top navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand "
     href="#">GASTOS FAMILIARES </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="padding-right: 10px;">
        <li class="nav-item">
          <a class="nav-link active"  aria-current="page" href="index.php">Home</a>
        </li>
        <?php if($_SESSION['rol'] == 1) {?>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="lista_deudas.php">Deudas</a>
          </li>  
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="lista_usuarios.php">Usuarios</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="lista_gastos.php">Gastos</a>
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