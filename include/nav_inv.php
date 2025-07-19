<?php 
  //session_start();
    

  include "include/functions.php";
 ?>
<nav class="navbar mp-5 fixed-top navbar-expand-lg navbar-dark" style="background-color:  #E60616 ;">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Sistema Control de FINCIMEX</a>
    <span class="navbar-text">
       Las Tunas, <?php echo fechaC(); ?>
    </span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="navbar-nav ms-auto">
    
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="padding-right: 10px;">
        <li class="nav-item dropdown"> <!-- DEVOLUCIONES -->
          <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Devoluciones
          </a>
          <ul class="dropdown-menu" aria-labelledby="navbarDropdown" style="background-color:  #E60616">
            <li><a class="dropdown-item navbar-text white" href="lista_dev.php">Listar Devolucines</a></li>
          </ul>
        </li>
      </ul>
      <a href="salir.php"><img class="close" src="img/pwd.png" alt="Salir del sistema" title="Salir"></a>
      
    
    </div>
  </div>
  </div>
</nav>

  
 
