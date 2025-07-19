<?php 
	session_start();
	  if(empty($_SESSION['active']) ){
		    header('location: ../');
  	  }
		
	include "../conexion.php";	
	include "../utils/cache.php";

	$cache = Cache::getInstance();
	$cacheKey = 'roles_familias_' . $_SESSION['familia'];

	// Intentar obtener roles y familias del caché
	$data = $cache->get($cacheKey);

	if ($data === null) {
		// Si no está en caché, obtener roles y familias
		$roles = $conn->query("SELECT id, rol as nombre FROM rol ORDER BY id");
		$familias = $conn->query("SELECT * FROM familia ORDER BY nombre");
		
		$data = [
			'roles' => $roles->fetch_all(MYSQLI_ASSOC),
			'familias' => $familias->fetch_all(MYSQLI_ASSOC)
		];
		
		// Guardar en caché por 1 hora
		$cache->set($cacheKey, $data, 3600);
	}

	$roles = $data['roles'];
	$familias = $data['familias'];
	
	// Obtener compañías telefónicas
	include 'functions_companias.php';
	$companias = obtenerCompaniasTelefonicas($conn);
?>
<?php // include "../logr.php";?>
<?php // escribelog($conection,$_SESSION['user'],"Listado de Usuarios"); ?>

<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Usuarios y Familias</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<!-- Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<!-- iziToast CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
	<!-- iziToast JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
	<style>
		/* Mejoras estéticas generales */
		body {
			background-color: #f8f9fa;
			min-height: 100vh;
		}
		
		.main-container {
			background: rgba(255, 255, 255, 0.95);
			border-radius: 20px;
			backdrop-filter: blur(10px);
			margin: 20px;
			padding: 30px;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
			margin-top: 5rem;
			margin-bottom: 5rem;
		}
		
		.section-title {
			background: linear-gradient(45deg, #667eea, #764ba2);
			color: white;
			padding: 15px 25px;
			border-radius: 15px;
			margin-bottom: 25px;
			box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		
		.section-title h3 {
			margin: 0;
			font-weight: 600;
			font-size: 1.4rem;
		}
		
		.add-btn {
			background: linear-gradient(45deg, #28a745, #20c997);
			border: none;
			border-radius: 50px;
			padding: 8px 20px;
			color: white;
			font-weight: 600;
			transition: all 0.3s ease;
			box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
		}
		
		.add-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
			color: white;
		}
		
		.table-custom {
			background: #fff;
			border-radius: 15px;
			overflow: hidden;
			box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
			border: none;
		}
		
		.table-container {
			background: #fff;
			border-radius: 20px;
			padding: 25px;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
			margin: 20px 0;
		}
		
		.table-custom th {
			background: #0d6efd;
			color: #fff;
			font-weight: 600;
			vertical-align: middle;
			border: none;
			padding: 15px 10px;
			font-size: 0.95rem;
		}
		
		.table-custom td {
			vertical-align: middle;
			padding: 12px 10px;
			border-bottom: 1px solid #f0f0f0;
		}
		
		.table-custom tbody tr:hover {
			background: linear-gradient(45deg, #f8f9ff, #f0f2ff);
			transform: scale(1.01);
			transition: all 0.2s ease;
		}
		
		.icon-cell {
			font-size: 1.3rem;
			margin-right: 0.8rem;
			color: #764ba2;
			opacity: 0.8;
		}
		
		/* Estados de usuario */
		.status-active {
			background: linear-gradient(45deg, #28a745, #20c997);
			color: white;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 0.8rem;
			font-weight: 600;
		}
		
		.status-inactive {
			background: linear-gradient(45deg, #dc3545, #fd7e14);
			color: white;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 0.8rem;
			font-weight: 600;
		}
		
		/* Botones de acción */
		.action-btn {
			border-radius: 50px;
			padding: 6px 15px;
			font-size: 0.85rem;
			font-weight: 600;
			transition: all 0.3s ease;
			border: none;
		}
		
		.edit-btn {
			background: linear-gradient(45deg, #ffc107, #ffca2c);
			color: #212529;
		}
		
		.delete-btn {
			background: linear-gradient(45deg, #dc3545, #fd7e14);
			color: white;
		}
		
		.action-btn:hover {
			transform: translateY(-1px);
			box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
		}
		
		/* Modales mejorados */
		.modal-content {
			border-radius: 20px;
			border: none;
			box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
		}
		
		.modal-header {
			background: linear-gradient(45deg, #667eea, #764ba2);
			color: white;
			border-radius: 20px 20px 0 0;
			border: none;
		}
		
		.modal-title {
			font-weight: 600;
		}
		
		.form-control, .form-select {
			border-radius: 10px;
			border: 2px solid #e9ecef;
			padding: 12px 15px;
			transition: all 0.3s ease;
		}
		
		.form-control:focus, .form-select:focus {
			border-color: #667eea;
			box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
		}
		
		.form-label {
			font-weight: 600;
			color: #495057;
			margin-bottom: 8px;
		}
		
		/* Responsive */
		@media (max-width: 768px) {
			.main-container {
				margin: 10px;
				padding: 20px;
			}
			
			.section-title {
				flex-direction: column;
				gap: 15px;
				text-align: center;
			}
		}
	</style>
</head>
<body>
<?php include "../include/nav.php";?>

<div class="main-container">
	<!-- Sección de Compañías Telefónicas -->
	<div class="section-title">
		<h3><i class="bi bi-phone-fill me-2"></i>Gestión de Compañías Telefónicas</h3>
		<button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#aCompania" id="addCompania">
			<i class="bi bi-plus-circle me-1"></i>Añadir Compañía
		</button>
	</div>
	
	<div class="table-container">
		<table class="table table-custom table-hover" id="companiasTable" style="width:100%">
			<thead>
				<tr>
					<th class="text-center"><i class="bi bi-building icon-cell"></i>Nombre</th>
					<th class="text-center"><i class="bi bi-envelope icon-cell"></i>Dominio MMS</th>
					<th class="text-center"><i class="bi bi-check-circle icon-cell"></i>Estado</th>
					<th class="text-center"><i class="bi bi-gear-fill icon-cell"></i>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>

	<!-- Sección de Familias -->
	<div class="section-title mt-5">
		<h3><i class="bi bi-people-fill me-2"></i>Gestión de Familias</h3>
		<button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#aFamilia" id="addFamilia">
			<i class="bi bi-plus-circle me-1"></i>Añadir Familia
		</button>
	</div>
	
	<div class="table-container">
		<table class="table table-custom table-hover" id="familiasTable" style="width:100%">
			<thead>
				<tr>
					<th class="text-center"><i class="bi bi-house-door-fill icon-cell"></i>Nombre</th>
					<th class="text-center"><i class="bi bi-card-text icon-cell"></i>Descripción</th>
					<th class="text-center"><i class="bi bi-calendar-event icon-cell"></i>Fecha Creación</th>
					<th class="text-center"><i class="bi bi-gear-fill icon-cell"></i>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>

	<!-- Sección de Usuarios -->
	<div class="section-title mt-5">
		<h3><i class="bi bi-person-lines-fill me-2"></i>Gestión de Usuarios</h3>
		<button type="button" class="add-btn" data-bs-toggle="modal" data-bs-target="#aUsuario" id="addAceptar">
			<i class="bi bi-plus-circle me-1"></i>Añadir Usuario
		</button>
	</div>
	
	<div class="table-container">
		<table class="table table-custom table-hover" id="mytable" style="width:100%">
			<thead>
				<tr>
					<th class="text-center"><i class="bi bi-person-fill icon-cell"></i>Usuario</th>
					<th class="text-center"><i class="bi bi-person-badge icon-cell"></i>Rol</th>
					<th class="text-center"><i class="bi bi-envelope-at-fill icon-cell"></i>Email</th>
					<th class="text-center"><i class="bi bi-telephone-fill icon-cell"></i>Teléfono</th>
					<th class="text-center"><i class="bi bi-phone-fill icon-cell"></i>Compañía</th>
					<th class="text-center"><i class="bi bi-people-fill icon-cell"></i>Familia</th>
					<th class="text-center"><i class="bi bi-check-circle-fill icon-cell"></i>Estado</th>
					<th class="text-center"><i class="bi bi-gear-fill icon-cell"></i>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<?php include "../include/footer.php"; ?>

<!-- Modal Añadir Compañía Telefónica -->
<div class="modal fade" id="aCompania" tabindex="-1" aria-labelledby="addCompania" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-phone-fill me-2"></i>Añadir Compañía Telefónica</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       	<div class="mb-3">
			<label class="form-label" for="nombre">Nombre de la Compañía</label>
			<input class="form-control" type="text" name="nombre" required id="aNombreCompania" placeholder="Ej: AT&T, T-Mobile, Verizon">
		</div>
		<div class="mb-3">
			<label class="form-label" for="dominio">Dominio MMS</label>
			<input class="form-control" type="text" name="dominio" required id="aDominioCompania" placeholder="Ej: @mms.att.net">
		</div>
		<div class="mb-3">
			<label class="form-label" for="activo">Estado</label>
			<select class="form-select" name="activo" id="aActivoCompania" required>
				<option value="Si">Activo</option>
				<option value="No">Inactivo</option>
			</select>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger action-btn" id="aAceptarCompania">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Compañía Telefónica -->
<div class="modal fade" id="editCompania" tabindex="-1" aria-labelledby="editCompania" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Modificar Compañía Telefónica</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<input hidden class="form-control" type="text" name="Id" required id="eidCompania">
		<div class="mb-3">
			<label class="form-label" for="nombre">Nombre de la Compañía</label>
			<input class="form-control" type="text" name="nombre" required id="eNombreCompania">
		</div>
		<div class="mb-3">
			<label class="form-label" for="dominio">Dominio MMS</label>
			<input class="form-control" type="text" name="dominio" required id="eDominioCompania">
		</div>
		<div class="mb-3">
			<label class="form-label" for="activo">Estado</label>
			<select class="form-select" name="activo" id="eActivoCompania" required>
				<option value="Si">Activo</option>
				<option value="No">Inactivo</option>
			</select>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger action-btn" id="eAceptarCompania">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Añadir Familia -->
<div class="modal fade" id="aFamilia" tabindex="-1" aria-labelledby="addFamilia" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-people-fill me-2"></i>Añadir Familia</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       	<div class="mb-3">
			<label class="form-label" for="nombre">Nombre de la Familia</label>
			<input class="form-control" type="text" name="nombre" required id="aNombre" placeholder="Ingrese el nombre de la familia">
		</div>
		<div class="mb-3">
			<label class="form-label" for="descripcion">Descripción</label>
			<textarea class="form-control" name="descripcion" id="aDescripcion" rows="3" placeholder="Descripción opcional de la familia"></textarea>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger action-btn" id="aAceptarFamilia">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Familia -->
<div class="modal fade" id="editFamilia" tabindex="-1" aria-labelledby="editFamilia" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Modificar Familia</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<input hidden class="form-control" type="text" name="Id" required id="eidFamilia">
		<div class="mb-3">
			<label class="form-label" for="nombre">Nombre de la Familia</label>
			<input class="form-control" type="text" name="nombre" required id="eNombre">
		</div>
		<div class="mb-3">
			<label class="form-label" for="descripcion">Descripción</label>
			<textarea class="form-control" name="descripcion" id="eDescripcion" rows="3"></textarea>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger action-btn" id="eAceptarFamilia">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Añadir Usuario -->
<div class="modal fade" id="aUsuario" tabindex="-1" aria-labelledby="addusers" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i>Añadir Usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       	<div class="mb-3">
			<label class="form-label" for="Usuario">Usuario</label>
			<input class="form-control" type="text" name="Usuario" required id="aUsu" placeholder="Nombre de usuario">
		</div>
		<div class="mb-3">
			<label class="form-label" for="Rol">Rol</label>
			<select class="form-select" name="Rol" id="aRol" required>
				<option value="">Seleccione un rol</option>
				<?php foreach ($roles as $rol): ?>
					<option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label" for="email">Email</label>
			<input class="form-control" type="email" name="email" required id="aemail" placeholder="correo@ejemplo.com">
		</div>
		<div class="mb-3">
			<label class="form-label" for="Telefono">Teléfono</label>
			<input class="form-control" type="tel" name="Telefono" required id="aTel" placeholder="+34 123 456 789">
		</div>
		<div class="mb-3">
			<label class="form-label" for="Compania">Compañía Telefónica</label>
			<select class="form-select" name="Compania" id="aCompania">
				<option value="">Seleccione compañía...</option>
				<?php foreach ($companias as $compania): ?>
					<option value="<?php echo $compania['id']; ?>"><?php echo htmlspecialchars($compania['nombre']); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label" for="Familia">Familia</label>
			<select class="form-select" name="Familia" id="aFamiliaSelect">
				<option value="">Sin Familia</option>
				<?php foreach ($familias as $familia): ?>
					<option value="<?php echo $familia['id']; ?>"><?php echo $familia['nombre']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger action-btn" id="aAceptar">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="editModalPos" tabindex="-1" aria-labelledby="editModalsPos" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-person-gear me-2"></i>Modificar Usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<input hidden class="form-control" type="text" name="Id" required id="eid">
		<div class="mb-3">
			<label class="form-label" for="Usuario">Usuario</label>
			<input class="form-control" type="text" name="Usuario" required id="eUsu">
		</div>
		<div class="mb-3">
			<label class="form-label" for="Rol">Rol</label>
			<select class="form-select" name="usuario" id="eRol" required>
				<option value="">Seleccione un rol</option>
				<?php foreach ($roles as $rol): ?>
					<option value="<?php echo $rol['id']; ?>"><?php echo $rol['nombre']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label" for="email">Email</label>
			<input class="form-control" type="email" name="email" required id="eemail">
		</div>
		<div class="mb-3">
			<label class="form-label" for="telefono">Teléfono</label>
			<input class="form-control" type="tel" name="telefono" required id="eTel">
		</div>
		<div class="mb-3">
			<label class="form-label" for="Compania">Compañía Telefónica</label>
			<select class="form-select" name="Compania" id="eCompania">
				<option value="">Seleccione compañía...</option>
				<?php foreach ($companias as $compania): ?>
					<option value="<?php echo $compania['id']; ?>"><?php echo htmlspecialchars($compania['nombre']); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label" for="Familia">Familia</label>
			<select class="form-select" name="Familia" id="eFamiliaSelect">
				<option value="">Sin Familia</option>
				<?php foreach ($familias as $familia): ?>
					<option value="<?php echo $familia['id']; ?>"><?php echo $familia['nombre']; ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="mb-3">
			<label class="form-label" for="activo">Estado</label>
			<select class="form-select" name="eactivo" required id="eactivo">
				<option value="Si">Activo</option>
				<option value="No">Inactivo</option>
			</select>
		</div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger action-btn" id="EAceptar">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" >
	
	$(document).ready(function(){
		// Tabla de Compañías Telefónicas
		$("#companiasTable").DataTable({
			order: [[0, 'asc']],
			responsive: {
				details: {
					type: 'column',
					target: 'tr'
				}
			},
			searching: true,
			info: true,
			columnDefs: [{
				targets: [0,1,2,3],
				className: 'dt-body-center',
			}],
			"paging": true,
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"../ajax/load_companias.php",
				   "type": 'POST',
				}
		});

		// Tabla de Familias
		$("#familiasTable").DataTable({
			order: [[0, 'asc']],
			responsive: {
				details: {
					type: 'column',
					target: 'tr'
				}
			},
			searching: true,
			info: true,
			columnDefs: [{
				targets: [0,1,2,3],
				className: 'dt-body-center',
			}],
			"paging": true,
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"../ajax/load_familias.php",
				   "type": 'POST',
				}
		});

		// Tabla de Usuarios
		$("#mytable").DataTable({
			order: [[1, 'asc'],
					[5,'desc']],
			responsive: {
				details: {
					type: 'column',
					target: 'tr'
				}
			},
			searching: true,
			info: true,
			columnDefs: [{
				targets: [0,1,2,3,4,5],
				className: 'dt-body-center',
			}],
			"paging": true,
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"../ajax/load_user.php",
				   "type": 'POST',
				}
		});

		// Recargar opciones de familia cuando se abre el modal de añadir usuario
		$('#addAceptar').click(function() {
			reloadFamilyOptions();
		});
	});

	// Función para recargar las opciones de familia
	function reloadFamilyOptions() {
		$.ajax({
			url: '../ajax/get_familias.php',
			type: 'POST',
			success: function(response) {
				var familias = JSON.parse(response);
				var options = '<option value="">Sin Familia</option>';
				familias.forEach(function(familia) {
					options += '<option value="' + familia[0] + '">' + familia[1] + '</option>';
				});
				$('#aFamiliaSelect, #eFamiliaSelect').html(options);
			}
		});
	}

	// Accion de añadir Compañía
	$('#aAceptarCompania').click(function(){
		if ($('#aNombreCompania').val().length<=0 || $('#aDominioCompania').val().length<=0){
			iziToast.error({
				title: 'Error',
				message: 'El nombre y dominio de la compañía son obligatorios',
				position: 'bottomRight',
				timeout: 5000
			});
			return;
		}
		let parametros = {
			"nombre": $('#aNombreCompania').val(),
			"dominio": $('#aDominioCompania').val(),
			"activo": $('#aActivoCompania').val()
		};
		$.ajax({
			url: '../ajax/add_compania.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#companiasTable').DataTable().ajax.reload(null,false);
				iziToast.success({
					title: 'Éxito',
					message: 'Compañía añadida correctamente',
					position: 'bottomRight',
					timeout: 5000
				});
				$('#aCompania').modal('hide');
				$('.form-control').val("");
				$('.form-select').val("Si");
			}
		})
	});

	// Accion de editar Compañía
	$('#eAceptarCompania').click(function(){
		let parametros = {
			"id": $('#eidCompania').val(),
			"nombre": $('#eNombreCompania').val(),
			"dominio": $('#eDominioCompania').val(),
			"activo": $('#eActivoCompania').val()
		};
		$.ajax({
			url: '../ajax/update_compania.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#companiasTable').DataTable().ajax.reload(null,false);
				iziToast.success({
					title: 'Éxito',
					message: 'Compañía actualizada correctamente',
					position: 'bottomRight',
					timeout: 5000
				});
				$('#editCompania').modal('hide');
			}
		})
	});

	// Accion de añadir Familia
	$('#aAceptarFamilia').click(function(){
		if ($('#aNombre').val().length<=0){
			iziToast.error({
				title: 'Error',
				message: 'El nombre de la familia es obligatorio',
				position: 'bottomRight',
				timeout: 5000
			});
			return;
		}
		let parametros = {
			"nombre": $('#aNombre').val(),
			"descripcion": $('#aDescripcion').val()
		};
		$.ajax({
			url: '../ajax/add_familia.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#familiasTable').DataTable().ajax.reload(null,false);
				reloadFamilyOptions(); // Recargar opciones después de añadir
				iziToast.success({
					title: 'Éxito',
					message: 'Familia añadida correctamente',
					position: 'bottomRight',
					timeout: 5000
				});
				$('#aFamilia').modal('hide');
				$('.form-control').val("");
			}
		})
	});

	// Accion de editar Familia
	$('#eAceptarFamilia').click(function(){
		let parametros = {
			"id": $('#eidFamilia').val(),
			"nombre": $('#eNombre').val(),
			"descripcion": $('#eDescripcion').val()
		};
		$.ajax({
			url: '../ajax/update_familia.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#familiasTable').DataTable().ajax.reload(null,false);
				reloadFamilyOptions(); // Recargar opciones después de editar
				iziToast.success({
					title: 'Éxito',
					message: 'Familia actualizada correctamente',
					position: 'bottomRight',
					timeout: 5000
				});
				$('#editFamilia').modal('hide');
			}
		})
	});

	// Obtener datos de Compañía
	function GetDataCompania(id) {
		var parametros = {"id": id};
		$.ajax({
			url: '../ajax/getDataCompania.php',
			type:'POST',
			data: parametros,
			success : function(dato) {
				let datos=JSON.parse(dato);
				$('#eidCompania').val(datos[0][0]);
				$('#eNombreCompania').val(datos[0][1]);
				$('#eDominioCompania').val(datos[0][2]);
				$('#eActivoCompania').val(datos[0][3]);
			}
		});
	}

	// Obtener datos de Familia
	function GetDataFamilia(id) {
		var parametros = {"id": id};
		$.ajax({
			url: '../ajax/getDataFamilia.php',
			type:'POST',
			data: parametros,
			success : function(dato) {
				let datos=JSON.parse(dato);
				$('#eidFamilia').val(datos[0][0]);
				$('#eNombre').val(datos[0][1]);
				$('#eDescripcion').val(datos[0][2]);
			}
		});
	}

// Accion de añadir Usuario
	$('#aAceptar').click(function(){
		if ($('#aUsu').val().length<=0||$('#aRol').val().length<=0||$('#aTel').val().length<=0){
			iziToast.error({
				title: 'Error',
				message: 'Rellene los campos obligatorios',
				position: 'bottomRight',
				timeout: 5000
			});
			return;
		}
		let parametros = {
			"Usuario": $('#aUsu').val(),
			"Rol": $('#aRol').val(),
			"Email": $('#aemail').val(),
			"Tel": $('#aTel').val(),
			"Compania": $('#aCompania').val(),
			"Familia": $('#aFamiliaSelect').val(),
			"Activo": "Si",
		};
		$.ajax({
			url: '../ajax/add_user.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#mytable').DataTable().ajax.reload(null,false);
				iziToast.success({
					title: 'Éxito',
					message: 'Usuario añadido correctamente',
					position: 'bottomRight',
					timeout: 5000
				});
				$('#aUsuario').modal('hide');
				$('.form-control').val("");
				$('.form-select').val("");
			}
		})
	});	

// Accion al editar Usuario
	$('#EAceptar').click(function(){
		let parametros = {
			"Id": $('#eid').val(),
			"Usuario": $('#eUsu').val(),
			"Rol": $('#eRol').val(),
			"Email": $('#eemail').val(),
			"Tel": $('#eTel').val(),
			"Compania": $('#eCompania').val(),
			"Familia": $('#eFamiliaSelect').val(),
			"Activo": $('#eactivo').val(),
		};
		$.ajax({
			url: '../ajax/update_user.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#mytable').DataTable().ajax.reload(null,false);
				iziToast.success({
					title: 'Éxito',
					message: 'Usuario actualizado correctamente',
					position: 'bottomRight',
					timeout: 5000
				});
				$('#editModalPos').modal('hide');
			}
		})
	});	

// Obtener datos de Usuario
	function GetDataUser(id) {
		// Primero recargamos las opciones de familia
		reloadFamilyOptions();
		
		// Luego obtenemos los datos del usuario
		$.ajax({
			url: '../ajax/getDataUser.php',
			type: 'POST',
			data: {
				id: id
			},
			success: function(response) {
				var data = JSON.parse(response);
				$('#eid').val(data[0][0]);
				$('#eUsu').val(data[0][1]);
				$('#eRol').val(data[0][3]);
				$('#eemail').val(data[0][4]);
				$('#eactivo').val(data[0][5]);
				$('#eTel').val(data[0][6]);
				$('#eCompania').val(data[0][9]); // compañía_id
				$('#eFamiliaSelect').val(data[0][7]);
				
				// Forzar la actualización del select de roles
				$('#eRol').trigger('change');
			}
		});
	}   

	// Función para test MMS
	function testMMS(userId) {
		// Mostrar toast de carga
		iziToast.info({
			title: 'Enviando MMS...',
			message: 'Preparando mensaje de prueba',
			position: 'bottomRight',
			timeout: 2000
		});
		
		$.ajax({
			url: '../ajax/test_mms_user.php',
			type: 'POST',
			data: { id: userId },
			dataType: 'json',
			success: function(response) {
				console.log('Respuesta completa:', response);
				
				if (response && response.success) {
					iziToast.success({
						title: '✅ MMS Enviado Exitosamente',
						message: response.message + '<br><small>Destinatario: ' + response.destinatario + '</small>',
						position: 'bottomRight',
						timeout: 8000,
						icon: 'fas fa-check-circle',
						buttons: [
							['<button>Ver Detalles</button>', function (instance, toast) {
								console.log('Debug info:', response.debug);
								instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
							}]
						]
					});
				} else {
					iziToast.error({
						title: '❌ Error al Enviar MMS',
						message: (response && response.message) ? response.message : 'Error desconocido',
						position: 'bottomRight',
						timeout: 8000,
						icon: 'fas fa-exclamation-circle'
					});
				}
			},
			error: function(xhr, status, error) {
				console.log('Error AJAX:', xhr.responseText);
				console.log('Status:', status);
				console.log('Error:', error);
				
				iziToast.error({
					title: '❌ Error de Conexión',
					message: 'No se pudo conectar con el servidor: ' + error,
					position: 'bottomRight',
					timeout: 8000,
					icon: 'fas fa-exclamation-triangle'
				});
			}
		});
	}

	// Botón para limpiar caché
	$('#clearCacheBtn').click(function() {
		$.ajax({
			url: 'clear_roles_cache.php',
			type: 'POST',
			dataType: 'json',
			success: function(resp) {
				if (resp.success) {
					iziToast.success({
						title: 'Éxito',
						message: 'Caché limpiado. Recargando...',
						position: 'bottomRight'
					});
					setTimeout(function() { location.reload(); }, 1000);
				} else {
					iziToast.error({
						title: 'Error',
						message: resp.msg || 'No se pudo limpiar el caché',
						position: 'bottomRight'
					});
				}
			}
		});
	});
</script>

</body>
</html>