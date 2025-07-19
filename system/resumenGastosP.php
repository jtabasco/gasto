<?php
// session_start();
// hacemos calculos
// Eliminar los warning
error_reporting(0);
include "../conexion.php";
//include "../include/script.php";
//include "include/nav.php";
//filtar todas las deudas del usuario

$sql = mysqli_query($conn, "SELECT Deuda FROM deudas WHERE duedor='" . $_SESSION['user'] . "'");
$result = mysqli_fetch_array($sql);
$debes = $result['Deuda'];

//filtar que le deben al  usuario
$sql = mysqli_query($conn, "SELECT deben FROM deben WHERE comprador=" . $_SESSION['idUser']);
$result = mysqli_fetch_array($sql);
$tedeben = $result['deben'];
if (is_null($tedeben)) {
	$tedeben = " 0.00";
}
if (is_null($debes)) {
	$debes = " 0.00";
}




?>


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<!-- Bootstrap CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<!-- Bootstrap JS -->
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<!-- iziToast CSS -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
	<!-- iziToast JS -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
	<style>
		#cambios {
			animation: pulse 1.5s infinite;
			border-width: 3px !important;
			box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
		}

		@keyframes pulse {
			0% {
				box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
			}

			70% {
				box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
			}

			100% {
				box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
			}
		}
		
		/* Estilos para tablas modernas */
		.table-custom {
			background: #fff;
			border-radius: 12px;
			overflow: hidden;
			box-shadow: 0 2px 10px rgba(0,0,0,0.08);
		}
		.table-custom th {
			background: transparent;
			color: #333;
			font-weight: 600;
			vertical-align: middle;
			border: none;
		}
		.table-custom td {
			vertical-align: middle;
		}
		.icon-cell {
			font-size: 1.2rem;
			margin-right: 0.5rem;
			color: #764ba2;
		}
		
		/* Estilos responsive para móviles */
		@media (max-width: 768px) {
			/* Reducir tamaño de botones en móviles */
			.btn-lg.rounded-circle {
				width: 40px !important;
				height: 40px !important;
				font-size: 1.1rem !important;
			}
			
			/* Ajustar espaciado en móviles */
			.gap-2 {
				gap: 0.5rem !important;
			}
			
			/* Hacer que los botones se envuelvan mejor */
			.flex-wrap {
				flex-wrap: wrap !important;
			}
			
			/* Ajustar márgenes en móviles */
			.mb-2 {
				margin-bottom: 0.75rem !important;
			}
			
			/* Reducir padding en móviles */
			.ms-3, .me-3 {
				margin-left: 0.5rem !important;
				margin-right: 0.5rem !important;
			}
			
			/* Ajustar tamaño de texto del saludo */
			.fw-bold {
				font-size: 0.9rem;
			}
		}
		
		@media (max-width: 576px) {
			/* Para pantallas muy pequeñas */
			.btn-lg.rounded-circle {
				width: 36px !important;
				height: 36px !important;
				font-size: 1rem !important;
			}
			
			.gap-2 {
				gap: 0.25rem !important;
			}
			
			/* Centrar mejor en pantallas pequeñas */
			.justify-content-center {
				justify-content: center !important;
			}
		}
		
		/* Estilos para tablas responsive */
		.table-responsive {
			overflow-x: auto;
			-webkit-overflow-scrolling: touch;
		}
		
		/* Ajustar columnas en móviles */
		@media (max-width: 768px) {
			.table th, .table td {
				font-size: 0.85rem;
				padding: 0.5rem 0.25rem;
			}
			
			/* Hacer que las cards ocupen todo el ancho en móviles */
			.col-sm-3, .col-sm-9 {
				width: 100% !important;
				margin-bottom: 1rem;
			}
			
			/* Ajustar espaciado de cards */
			.card-body {
				padding: 1rem 0.75rem;
			}
			
			/* Ajustar títulos de cards */
			.card-title {
				font-size: 0.9rem;
				padding: 0.5rem 0.75rem;
			}
		}
		
		@media (max-width: 576px) {
			.table th, .table td {
				font-size: 0.8rem;
				padding: 0.4rem 0.2rem;
			}
			
			.card-body {
				padding: 0.75rem 0.5rem;
			}
			
			.card-title {
				font-size: 0.85rem;
				padding: 0.4rem 0.5rem;
			}
		}
	</style>
</head>

<body>
	<div class="row mt-5 ms-3 me-3" style="margin-top: 4rem !important;">
		<div class="col-sm-3 p-1 fw-bold">
			<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 20px;">
				<div style="display: flex; align-items: center; margin-bottom: 8px;">
					<i class="bi bi-person-circle" style="font-size: 1.5em; margin-right: 10px;"></i>
					<span style="font-size: 1.3em; font-weight: 600;">¡Hola, <?php echo $_SESSION['user'] ?>!</span>
				</div>
				<div style="display: flex; align-items: center; opacity: 0.9;">
					<i class="bi bi-house-heart" style="font-size: 1.2em; margin-right: 8px;"></i>
					<span style="font-size: 1.1em;">Familia <?php echo $_SESSION['familia_nombre'] ?></span>
				</div>
			</div>
			
			<!-- Botones principales - Responsive -->
			<div class="mt-2">
				<!-- Primera fila: Botones principales -->
				<div class="d-flex justify-content-center gap-2 mb-1 flex-wrap">
					<button class="btn btn-success btn-lg rounded-circle shadow" id="btnPrestamo" title="Registrar Préstamo" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-cash-coin"></i>
					</button>
					<button class="btn btn-primary btn-lg rounded-circle shadow" id="btnCompra" title="Registrar Compra" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-cart-plus"></i>
					</button>
					<button type="button" class="btn btn-danger btn-lg rounded-circle shadow" data-bs-toggle="modal" data-bs-target="#verCambios" id="cambios" title="Ver Cambios" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-lightning-fill"></i>
					</button>
					<button type="button" class="btn btn-info btn-lg rounded-circle shadow" data-bs-toggle="modal" data-bs-target="#verreporte" id="reporte" title="Reporte" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-bar-chart"></i>
					</button>
				<!--</div>-->
				<!-- Segunda fila: Notificaciones y utilidades -->
				<!--<div class="d-flex justify-content-center gap-2 flex-wrap">
					<button type="button" class="btn btn-warning btn-lg rounded-circle shadow" data-bs-toggle="modal" data-bs-target="#notificacionesModal" id="btnNotificaciones" title="Notificaciones" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-bell"></i>
						<span id="notificacionesBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display:none;">
							0
						</span>
					</button>-->
					<?php if($_SESSION['rol'] == 1) { ?>
					<button type="button" class="btn btn-secondary btn-lg rounded-circle shadow" id="btnGenerarNotificacionesAuto" title="Generar Notificaciones Automáticas" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-gear"></i>
					</button>
					<?php } ?>
					<a href="https://donate.stripe.com/7sI14Y91yge7bVmbIN" target="_blank" class="btn btn-success btn-lg rounded-circle shadow" title="Donar" style="width:44px; height:44px; display:flex; align-items:center; justify-content:center; font-size:1.3rem;">
						<i class="bi bi-heart-fill"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="row mb-2 ms-3 me-3" style="margin-top:0 !important;">
		<div class="col-sm-3 mt-3 " id="deudasNetasSection" style="display:none;">
			<div class="card shadow">
				<div class="card-body">
					<div class="card-title bg-danger fw-bold text-white shadow rounded p-1"><i class="bi bi-cash-stack me-2"></i>Deudas Netas (new)</div>
					<!-- Nueva tabla para deudas netas -->
					<div class="table-responsive">
						<table class="table table-custom table-bordered table-hover justify-content-center display nowrap" id="tabladeudasNetas" style="width:100%">
							<thead>
								<th class="dt-head-center" data-dt-order="disable"><i class="bi bi-person-check-fill icon-cell"></i>Acreedor</th>
								<th class="dt-head-center"><i class="bi bi-cash-coin icon-cell"></i>Total</th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row  ms-3 me-3">
		<div class="col-sm-3 mt-3 " id="porPagarSection" <?php echo (floatval($debes) == 0 ? 'style="display:none;"' : ''); ?>>
			<div class="card shadow">
				<div class="card-body">
					<div class="card-title bg-primary fw-bold text-white shadow rounded p-1 d-flex align-items-center justify-content-between">
						<span><i class="bi bi-credit-card-2-front me-2"></i>Por Pagar $<?php echo $debes ?></span>
						<button id="togglePorPagar" class="btn btn-outline-light btn-sm ms-2" title="Ver detalles"><i class="bi bi-eye"></i></button>
					</div>
					<!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
					<div class="table-responsive">
						<table class="table table-custom table-bordered table-hover justify-content-center display nowrap" id="tabladuedasT" style="width:100%">
							<thead>
								<th class="dt-head-center" data-dt-order="disable"><i class="bi bi-person-fill icon-cell"></i>Comprador</th>
								<th class="dt-head-center"><i class="bi bi-cash-coin icon-cell"></i>Total</th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-9 mt-3 " id="porPagarDetalle" style="display:none;">
			<div class="card shadow">
				<div class="card-body">
					<div class="card-title bg-primary  fw-bold text-white shadow rounded p-1">Detallado </div>
					<div class="table-responsive">
						<table class="table table-custom table-bordered table-hover justify-content-center display nowrap" id="tabladuedas" style="width:100%">
							<thead>
								<th class="dt-head-center"><i class="bi bi-person-fill icon-cell"></i>Comprador</th>
								<th class="dt-head-center"><i class="bi bi-card-text icon-cell"></i>Detalle</th>
								<th class="dt-head-center"><i class="bi bi-cash-coin icon-cell"></i>Importe</th>
								<th class="dt-head-center"><i class="bi bi-calendar-event icon-cell"></i>Fecha</th>
								<th class="dt-head-center"><i class="bi bi-cash-stack icon-cell"></i>Deuda</th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="row mt-2 mb-5 ms-3 me-3">
		<div class="col-sm-3 mt-3 " id="porCobrarSection" <?php echo (floatval($tedeben) == 0 ? 'style="display:none;"' : ''); ?>>
			<div class="card shadow">
				<div class="card-body">
					<div id="tedeben" class="card-title bg-primary  fw-bold text-white shadow rounded p-1 d-flex align-items-center justify-content-between">
						<span id="porCobrarMonto"><i class="bi bi-wallet2 me-2"></i>Por Cobrar $<?php echo $tedeben ?></span>
						<button id="togglePorCobrar" class="btn btn-outline-light btn-sm ms-2" title="Ver detalles"><i class="bi bi-eye"></i></button>
					</div>
					<!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
					<div class="table-responsive">
						<table class="table table-custom table-bordered table-hover justify-content-center display nowrap" id="tabladebenT" style="width:100%">
							<thead>
								<th class="dt-head-center" data-dt-order="disable"><i class="bi bi-person-fill icon-cell"></i>Deudor</th>
								<th class="dt-head-center"><i class="bi bi-cash-coin icon-cell"></i>Total</th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>

		<div class="col-sm-9 mt-3 " id="porCobrarDetalle" style="display:none;">
			<div class="card shadow">
				<div class="card-body">
					<div class="card-title bg-primary  fw-bold text-white shadow rounded p-1">Detallado</div>
					<div class="table-responsive">
						<table class="table table-custom table-bordered table-hover justify-content-center display nowrap" id="tabladeben" style="width:100%">
							<thead>
								<tr>
									<th rowspan="2" class="dt-head-center"><i class="bi bi-person-fill icon-cell"></i>Deudor</th>
									<th rowspan="2" class="dt-head-center"><i class="bi bi-cash-stack icon-cell"></i>Deuda</th>
									<th colspan="3" class="dt-head-center" data-dt-order="disable"><i class="bi bi-cart-check icon-cell"></i>Datos de la compra</th>
								</tr>
								<th class="dt-head-center"><i class="bi bi-card-text icon-cell"></i>Detalle</th>
								<th class="dt-head-center"><i class="bi bi-cash-coin icon-cell"></i>Importe</th>
								<th class="dt-head-center"><i class="bi bi-calendar-event icon-cell"></i>Fecha</th>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
	<div class="row mb-5 ms-3 me-3">
	</div>

	<!-- Reporte gasto x mes y ano -->
	<div class="modal shadow" id="verreporte" tabindex="-1" aria-labelledby="averreporte" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h3 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>Reporte de compra</h3>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="bg-light p-3 rounded mb-3 d-flex flex-column align-items-center justify-content-center">
						<div class="d-flex flex-row gap-3 w-100 justify-content-center">
							<div class="d-flex flex-column align-items-center">
								<label class="mb-1" style="font-weight:500;"><i class="bi bi-calendar me-1"></i> Año:</label>
								<select id="ano" name="selectano" class="form-select text-center" style="min-width:110px; font-size:1.2rem;">
									<option value=2024>2024</option>
									<option value=2025>2025</option>
									<option value=2026>2026</option>
									<option value=2027>2027</option>
									<option value=2028>2028</option>
								</select>
							</div>
							<div class="d-flex flex-column align-items-center">
								<label class="mb-1" style="font-weight:500;"><i class="bi bi-calendar2-month me-1"></i> Mes:</label>
								<select id="mes" name="selectmes" class="form-select text-center" style="min-width:130px; font-size:1.2rem;">
									<option value=1>Enero</option>
									<option value=2>Febrero</option>
									<option value=3>Marzo</option>
									<option value=4>Abril</option>
									<option value=5>Mayo</option>
									<option value=6>Junio</option>
									<option value=7>Julio</option>
									<option value=8>Agosto</option>
									<option value=9>Septiembre</option>
									<option value=10>Octubre</option>
									<option value=11>Noviembre</option>
									<option value=12>Diciembre</option>
								</select>
							</div>
						</div>
					</div>

					<div class="row g-3 mb-3 justify-content-center">
						<div class="col-md-6 col-12">
							<div class="card shadow-sm border-0 bg-light-blue text-center">
								<div class="card-body p-3">
									<div class="d-flex align-items-center justify-content-center mb-2">
										<i class="bi bi-cash-coin fs-2 text-primary me-2"></i>
										<div class="text-start">
											<div class="fw-semibold text-primary">Total</div>
											<div class="fs-4 fw-bold" id="imptotal">$ 0.00</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-12">
							<div class="card shadow-sm border-0 bg-light-green text-center">
								<div class="card-body p-3">
									<div class="d-flex align-items-center justify-content-center mb-2">
										<i class="bi bi-check-circle fs-2 text-success me-2"></i>
										<div class="text-start">
											<div class="fw-semibold text-success">Pagado</div>
											<div class="fs-4 fw-bold" id="pagtotal">$ 0.00</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="mb-2 mt-3">
						<div class="section-title mt-4 mb-2" style="font-size:1.1rem; font-weight:bold;"><i class="bi bi-person-circle icon-title"></i>Por Comprador</div>
						<div class="table-responsive mb-4">
							<table class="table table-custom table-bordered table-striped" id="mytableXanoXmes">
								<thead>
									<tr>
										<th><i class="bi bi-person-fill icon-cell"></i>Comprador</th>
										<th><i class="bi bi-cash-coin icon-cell"></i>Importe</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>

					<div class="mb-2 mt-3">
						<div class="section-title mb-2" style="font-size:1.1rem; font-weight:bold;"><i class="bi bi-tags icon-title"></i>Por Categoría</div>
						<div class="table-responsive mb-4">
							<table class="table table-custom table-bordered table-striped" id="mytableXanoXmesXcategoria">
								<thead>
									<tr>
										<th><i class="bi bi-tags-fill icon-cell"></i>Categoría</th>
										<th><i class="bi bi-cash-coin icon-cell"></i>Importe</th>
										<th><i class="bi bi-percent icon-cell"></i>%</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>

					<div class="mb-2 mt-3">
						<div class="section-title mb-2" style="font-size:1.1rem; font-weight:bold;"><i class="bi bi-cash-stack icon-title"></i>Pagado por Categoría</div>
						<div class="table-responsive mb-4">
							<table class="table table-custom table-bordered table-striped" id="mytableXanoXmesXcategoriaXactivo">
								<thead>
									<tr>
										<th><i class="bi bi-tags-fill icon-cell"></i>Categoría</th>
										<th><i class="bi bi-cash-coin icon-cell"></i>Importe</th>
										<th><i class="bi bi-percent icon-cell"></i>%</th>
									</tr>
								</thead>
							</table>
						</div>
					</div>

					<!-- Botón para generar PDF -->
					<div class="d-flex justify-content-center mt-4 mb-3">
						<button type="button" class="btn btn-success btn-lg" id="generarPDF" title="Generar PDF del reporte">
							<i class="bi bi-file-earmark-pdf me-2"></i>Generar PDF
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<style>
		.bg-light-blue {
			background: #e0f2fe;
		}

		.bg-light-green {
			background: #dcfce7;
		}

		.table thead th {
			vertical-align: middle;
		}

		.table-bordered {
			border-radius: 0.5rem;
			overflow: hidden;
		}
	</style>

	<!-- Entrada de gasto -->
	<div class="modal fade shadow" id="compra" tabindex="-1" aria-labelledby="aGasto" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h3>Compra pagada por <?php echo $_SESSION['user'] ?></h3>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<select name="Ubi0" id="aplan" class="form-select" required="">
								<option selected></option>
								<option value='0'>Seleccione planificado</option>
							</select>
						</div>

					</div>
					<div class="row">
						<div class="col-12">
							<label class="form-label p-2" for="clientem">Detalle del Gasto* <button type="button" class="btn btn-primary text-white  ms-3" id="aPlantilla"><i class="bi bi-save2-fill"></i></button><button type="button" class="btn btn-danger text-white ms-1" id="dPlantilla"><i class="bi bi-trash-fill"></i></button>
							</label>
							<input class="form-control" type="text" name="detalle" required="" id="aDetalle">
						</div>
						<div class="col-12">
							<label class="form-label" for="cat`">Categoria*</label>
							<select name="cat" id="acat" class="form-select" required="">
								<option selected></option>
								<option value='0'>Seleccione categoria</option>

							</select>
						</div>
						<div class="col-6">
							<label class="form-label" for="clientem">Monto*</label>
							<input class="form-control" type="number" name="monto" required="" id="aMonto">
						</div>
						<div class="col-6">
							<label class="form-label" for="fecha">Fecha*</label>
							<input class="form-control" type="date" name="fecha" required="" id="aFecha">
						</div>
						<div class="col-12">
							<!-- Agrupar select y tabla en un mismo contenedor para igualar el ancho -->
							<div class="deudor-table-wrapper" style="max-width:400px; margin:auto;">
								<select name="Ubi" id="adeudor" class="form-select mb-2" required style="width:100%;">
									<option selected></option>
									<option value='0'>Todos</option>
								</select>
								<div class="table-responsive">
									<table class="table table-custom table-responsive table-bordered table-hover" id="mytableg" style="width:100%">
										<thead>
											<th><i class="bi bi-person-fill icon-cell"></i>Deudor</th>
											<th><i class="bi bi-gear-fill icon-cell"></i>Acciones</th>
										</thead>
									</table>
								</div>
							</div>
						</div>

						<!-- <div class="col-3">
							<button type="button" class="btn btn-primary my-4 p-2" id="Anadir">+</button>
						</div> -->

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="Aceptarm">Aceptar</button>
						<button class="btn btn-primary" type="button" id="spin1" hidden>
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
							<span class="sr-only">Aceptar</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>




	<!-- Modal EDIT para actualizar el pago-->
	<div class="modal" id="ActualizaPago" tabindex="-1" aria-labelledby="ActualizasPago" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<input class="form-control modal-title fs-5 bg-transparent border-0" type="text" name="deudor" disabled id="edeuda">
					<!-- <h1 class="modal-title fs-5" id="ActualizasPago">Actualiza Pago <input id='edeuda'></h1> -->
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12 text-wrap h-auto">
							<label class="form-label" for="detalle">Detalles: </label>
							<TEXTAREA class="form-control bg-transparent border-0" name="obs" disabled rows="3" id="edetalle"></TEXTAREA>
							<input class="form-control bg-transparent border-0 text-wrap" type="text" name="detalle" disabled id="edetalle">
							<input class="form-control" name="id" type="hidden" id="eid">
						</div>
					</div>
					<div class="row border-top border-primary mt-3 ">
						<div class="col-8">
							<input class="form-control bg-transparent border-0 text-end" type="text" name="deudor" disabled id="edeudor">
						</div>
						<!-- <div class="col-4"> -->
						<!-- <label class="form-label" for="deuda">Deuda</label>
						<input class="form-control bg-transparent border-0" type="text" name="deuda" disabled id="edeuda"> -->
						<!-- </div>	 -->
						<div class="col-4">
							<select class="form-select" name="pago" required="" id="epago">
								<option value="Si">Si</option>
								<option value="No">No</option>
							</select>
						</div>
					</div>


					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="EAceptar">Aceptar</button>
						<button class="btn btn-primary" type="button" id="spin0" hidden>
							<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
							<span class="sr-only">Aceptar</span>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal a para préstamo -->
	<div class="modal fade shadow" id="prestamo" tabindex="-1" aria-labelledby="aPrestamo" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h3>Préstamo de <?php echo $_SESSION['user'] ?></h3>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<label class="form-label p-2" for="detallePrestamo">Detalle del Préstamo*

							</label>
							<input class="form-control" type="text" name="detallePrestamo" required="" id="aDetallePrestamo">
						</div>
						<div class="col-6">
							<label class="form-label" for="montoPrestamo">Monto*</label>
							<input class="form-control" type="number" name="montoPrestamo" required="" id="aMontoPrestamo">
						</div>
						<div class="col-6">
							<label class="form-label" for="fechaPrestamo">Fecha*</label>
							<input class="form-control" type="date" name="fechaPrestamo" required="" id="aFechaPrestamo">
						</div>
						<div class="col-12">
							<label class="form-label" for="deudorPrestamo">Deudor</label>
							<select name="UbiPrestamo" id="adeudorPrestamo" class="form-select" required="">
								<option selected value='0'>Seleccione deudor</option>
								<?php
								$consulta = $conn->query("select * from usuarios where activo='si' and familia_id='" . $_SESSION['familia'] . "' and id not in (" . $_SESSION['idUser'] . ")");
								while ($row = mysqli_fetch_assoc($consulta)) {
									echo '<option value="' . $row['id'] . '">' . $row['nombre'] . '</option>';
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
					<button type="button" class="btn btn-primary" id="AceptarPrestamo">Aceptar</button>
					<button class="btn btn-primary" type="button" id="spinPrestamo" hidden>
						<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
						<span class="sr-only">Aceptar</span>
					</button>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal para actualizar todas las deudas de un deudor -->
	<div class="modal fade" id="actualizarTodasDeudasModal" tabindex="-1" role="dialog" aria-labelledby="actualizarTodasDeudasModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="dialog">
			<div class="modal-content">
				<div class="modal-header justify-content-center">
					<h5 class="modal-title" id="actualizarTodasDeudasModalLabel">Seleccionar deudas a pagar de <span id="nombreDeudor"></span></h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="table-responsive">
						<table class="table table-bordered table-hover align-middle mb-0" id="tablaDeudas">
							<thead class="bg-primary text-white">
								<tr>
									<th style="width:40px"><input type="checkbox" id="seleccionarTodos"></th>
									<th>Fecha</th>
									<th>Detalle</th>
									<th>Monto</th>
								</tr>
							</thead>
							<tbody id="cuerpoTablaDeudas">
								<!-- Las deudas se cargarán aquí dinámicamente -->
							</tbody>
						</table>
					</div>
					<div class="row mt-3 g-2">
						<div class="col-12 col-md-6 d-flex align-items-center justify-content-center justify-content-md-start mb-2 mb-md-0">
							<h5 class="mb-0">Total seleccionado: <span id="totalSeleccionado" class="text-primary">$0.00</span></h5>
						</div>
						<div class="col-12 col-md-6 d-flex flex-column flex-md-row justify-content-center justify-content-md-end gap-2">
							<button type="button" class="btn btn-success w-100 w-md-auto" id="actualizarTodasSi">Actualizar seleccionadas</button>
							<button type="button" class="btn btn-danger w-100 w-md-auto" id="actualizarTodasNo">Cancelar</button>
							<button class="btn btn-primary w-100 w-md-auto" type="button" id="spinActualizarTodas" hidden>
								<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
								<span class="sr-only">Actualizando...</span>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.4/dist/sweetalert2.all.min.js"></script>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.4/dist/sweetalert2.min.css">

	<script type="text/javascript">
		/////////////////////////

		$(document).ready(function() {
			//para que no se cierre con click fuera
			$('#compra').modal({
				backdrop: 'static'
			});
			$('#prestamo').modal({
				backdrop: 'static'
			});
			$('#ActualizaPago').modal({
				backdrop: 'static'
			}); //para que no se cierre con click fuera
			$('#actualizarTodasDeudasModal').modal({
				backdrop: 'static'
			}); //para que no se cierre con click fuera

			// Mensaje de bienvenida
			iziToast.success({
				title: 'Bienvenido',
				message: '!!!BIENVENIDO !!!',
				position: 'bottomRight',
				timeout: 5000
			});


			$.ajax({
				url: 'filtro_usuarios.php',
				type: 'POST',
				success: function(resp) {
					//actualizo el ontrol
					$('#adeudor option').remove();
					$('#adeudor').append(resp);

				}
			});
			$.ajax({
				url: 'filtro_plan.php',
				type: 'POST',
				success: function(resp) {
					//actualizo el ontrol
					$('#aplan option').remove();
					$('#aplan').append(resp);
				}
			});
			$.ajax({
				url: 'categoria.php',
				type: 'POST',
				success: function(resp) {
					//actualizo el ontrol
					$('#acat option').remove();
					$('#acat').append(resp);
				}
			});




			let fecha = new Date();
			$("#ano").val(fecha.getFullYear());
			$("#mes").val(fecha.getMonth() + 1);
			let ano = $('#ano').val();
			let mes = $('#mes').val();
			let parametros = {
				"ano": ano,
				"mes": mes
			};
			// aquii tenemos para filtarpor ano y mes   				  
			$.ajax({
				url: 'totalmesano.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					//actualizo el control
					$('#imptotal').text(resp);
				}
			});
			$.ajax({
				url: 'totalmesanopagado.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					//actualizo el control
					$('#pagtotal').text(resp);
				}
			});


			$("#mytableXanoXmes").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],
				/*responsive: {
				     details: {
				         type: 'column',
				         target: 'tr'
				     }
				 }, */ // hast aqui*/

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
					targets: [0, 1],
					className: 'dt-body-center'
				}],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmes.php",
					"type": 'POST',
					"data": parametros,
				}
			});

			$("#mytableXanoXmesXcategoria").DataTable({ //tabla de la izquierda
				//respsive
				order: [1, 'desc'],
				/* responsive: {
				     details: {
				         type: 'column',
				         target: 'tr'
				     }
				 }, */ // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmesXcategoria.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			$("#mytableXanoXmesXcategoriaXactivo").DataTable({ //tabla de la izquierda
				//respsive
				order: [1, 'desc'],
				/* responsive: {
				     details: {
				         type: 'column',
				         target: 'tr'
				     }
				 }, */ // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1, 2],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmesXcategoriaXactivo.php",
					"type": 'POST',
					"data": parametros,
				}
			});



			/*	$("#tabladuedas").DataTable({ //tabla de la izquierda
			

				//respsive
    order: [0, 'asc'],
    responsive: {
        details: {
            type: 'column',
            target: 'tr'
        }
    }, // hast aqui
			searching:false,
				info:false,
			order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2,3,4],
                className: 'dt-body-center',},
					{
			            className: 'dtr-control',
			            orderable: false,
			            target: 0
			        	}],
			"paging": false,
      		"dom":'<lf<t>ip>',
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: true,
			ajax: {"url":"por_pagar.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});*/
			$("#tabladuedasT").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],
				responsive: {
					details: {
						type: 'column',
						target: 'tr'
					}
				}, // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "por_pagart.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			/*$("#tabladeben").DataTable({ //
				//respsive
    order: [0, 'asc'],
    responsive: {
        details: {
            type: 'column',
            target: 'tr'
        }
    }, // hast aqui


			searching:true,
				info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2,3,4],
                className: 'dt-body-center'},
                {
			            className: 'dtr-control',
			            orderable: false,
			            target: 0
			        	}],
						              // {targets: 5,visible: false}

			"paging": false,
      		"dom":'<lf<t>ip>',
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"por_cobrar.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});*/
			$("#tabladebenT").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],
				responsive: {
					details: {
						type: 'column',
						target: 'tr'
					}
				}, // hast aqui
				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: false,
				ajax: {
					"url": "por_cobrart.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			//rellenamos mytableg
			$("#mytableg").DataTable({ //tabla de la izquierda
				searching: false,
				info: false,
				order: [
					[0, 'asc']
				],
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					//{targets: 1,render: DataTable.render.datetime('DD-MM-YYYY'),},
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "load_detallesm.php",
					"type": 'POST'
				}
			});

			// Añade esto después de la inicialización de las otras tablas
			$("#tabladeudasNetas").DataTable({
				order: [0, 'asc'],
				searching: false,
				info: false,
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "deudas_netas.php",
					"type": 'POST',
					"data": function() {
						return {
							"debtor": '<?php echo $_SESSION['user']; ?>'
						};
					},
					"dataSrc": function(json) {
						// Mostrar u ocultar la sección basado en si hay datos
						if (json.data && json.data.length > 0) {
							$('#deudasNetasSection').show();
							// Actualizar el título con el total general
							if (json.total_general) {
								$('#deudasNetasSection .card-title').html('<i class="bi bi-cash-stack me-2"></i>Deudas Netas $' + json.total_general);
							}
						} else {
							$('#deudasNetasSection').hide();
						}
						return json.data;
					}
				}
			});

			// Función para enviar correo de deudas
			window.enviarCorreoDeudas = function(deudor, acreedor) {
				iziToast.question({
					timeout: 20000,
					close: false,
					overlay: true,
					displayMode: 'once',
					id: 'question',
					zindex: 999,
					title: '¿Confirmar?',
					message: '¿Ya realizaste el pago del neto? Solo confirma si ya pagaste.',
					position: 'center',
					buttons: [
						['<button><b>Sí, ya pagué</b></button>', function (instance, toast) {
							instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
							// Aquí sí enviamos el correo
							$.ajax({
								url: 'enviar_correo_deudas_netas.php',
								type: 'POST',
								data: {
									deudor: deudor,
									acreedor: acreedor
								},
								dataType: 'json',
								success: function(response) {
									if (response.success) {
										iziToast.success({
											title: 'Éxito',
											message: 'Correo programado para envío',
											position: 'bottomRight',
											timeout: 5000
										});
									} else {
										iziToast.error({
											title: 'Error',
											message: response.message || 'Error al enviar el correo',
											position: 'bottomRight',
											timeout: 5000
										});
									}
								},
								error: function() {
									iziToast.error({
										title: 'Error',
										message: 'Error al procesar la solicitud',
										position: 'bottomRight',
										timeout: 5000
									});
								}
							});
						}, true],
						['<button>No</button>', function (instance, toast) {
							instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
							// No hacemos nada
						}]
					]
				});
			};

			// Agregar evento de clic a la tabla tabladebenT
			$("#tabladebenT").on('click', 'tbody tr', function() {
				var data = $("#tabladebenT").DataTable().row(this).data();
				if (data) {
					var deudor = data[0]; // El nombre del deudor está en la primera columna

					// Obtener el ID del deudor desde la fila
					var deudorId = $(this).find('td:first').text();

					// Mostrar el modal con el nombre del deudor
					$("#nombreDeudor").text(deudor);
					$("#deudorId").val(deudor);
					$("#actualizarTodasDeudasModal").modal('show');
				}
			});
			// Agregar evento de clic a la tabla tabladebenT
			$("#tabladeben").on('click', 'tbody tr', function() {
				var data = $("#tabladeben").DataTable().row(this).data();
				if (data) {
					var deudor = data[5]; // 
					// con el id llamamos getdatagasto
					GetDataGasto(deudor); // Llamar a GetDataGasto con el ID
					$('#ActualizaPago').modal('show');
					// Obtener el ID del deudor desde la fila
					//var deudorId = $(this).find('td:first').text();

					//alert(deudor);
					//alert(deudorId);
				}
			});








			// Manejar clic en el botón Actualizar
			/*$("#actualizarTodasSi").click(function() {
				var deudorId = $("#deudorId").val();

				// Mostrar spinner
				$(this).hide();
				$("#actualizarTodasNo").hide();
				$("#spinActualizarTodas").removeAttr('hidden');
				$("#spinActualizarTodas").show();

				// Llamada AJAX para actualizar todas las deudas
				$.ajax({
					url: 'actualizar_todas_deudas.php',
					type: 'POST',
					data: {
						deudorId: deudorId
					},
					dataType: 'json',
					success: function(response) {
						// Actualizar las tablas
						//$('#tabladeben').DataTable().ajax.reload(null, false);
						//$('#tabladebenT').DataTable().ajax.reload(null, false);
						$('#porCobrarSection').hide();
						// Actualizar el subtotal de por cobrar
						$.ajax({
							url: 'subtotalPorCobrar.php',
							type: 'POST',
							success: function(resp) {
								$('#porCobrarMonto').text(resp);
							}
						});

						// Cerrar el modal
						$("#actualizarTodasDeudasModal").modal('hide');

						// Mostrar mensaje de éxito
						iziToast.success({
							title: 'Éxito',
							message: response.message || '¡Deudas actualizadas correctamente!',
							position: 'bottomRight',
							timeout: 5000
						});

						// Restaurar botones
						$("#actualizarTodasSi").show();
						$("#actualizarTodasNo").show();
						$("#spinActualizarTodas").hide();
					},
					error: function(xhr, status, error) {
						// Mostrar mensaje de error
						iziToast.error({
							title: 'Error',
							message: 'Error al actualizar las deudas: ' + error,
							position: 'bottomRight',
							timeout: 5000
						});

						// Restaurar botones
						$("#actualizarTodasSi").show();
						$("#actualizarTodasNo").show();
						$("#spinActualizarTodas").hide();
					}
				});
			});*/

			// Manejar clic en el botón Cancelar
			$("#actualizarTodasNo").click(function() {
				$("#actualizarTodasDeudasModal").modal('hide');
			});

		});

		// Boton +
		$('#addAceptar').click(function() {

			$('#mytableg').DataTable().ajax.reload(null, false);
			//borramos tdatalle_gastos
			$.ajax({
				url: 'del_temp.php',
				type: 'POST'
			});
			$('#mytableg').DataTable().ajax.reload(null, false);
			$.ajax({
				url: 'filtro_usuarios.php',
				type: 'POST',
				success: function(resp) {
					//actualizo el ontrol
					$('#adeudor option').remove();
					$('#adeudor').append(resp);
				}
			});

		});
		// boton reporte
		$('#reporte').click(function() {
			let fecha = new Date();
			$("#ano").val(fecha.getFullYear());
			$("#mes").val(fecha.getMonth() + 1);
			let ano = $('#ano').val();
			let mes = $('#mes').val();
			let parametros = {
				"ano": ano,
				"mes": mes
			};
			// aquii tenemos para filtarpor ano y mes   				  
			$.ajax({
				url: 'totalmesano.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					//actualizo el control
					$('#imptotal').text(resp);
				}
			});
			$.ajax({
				url: 'totalmesanopagado.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					//actualizo el control
					$('#pagtotal').text(resp);
				}
			});

			// borro tabla y actualizo
			$('#mytableXanoXmes').DataTable().destroy();
			$('#mytableXanoXmesXcategoria').DataTable().destroy();
			$('#mytableXanoXmesXcategoriaXactivo').DataTable().destroy();
			//$('#mytableXanoXmes').DataTable().ajax.reload(); 			
			$("#mytableXanoXmes").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],
				/*responsive: {
				    details: {
				        type: 'column',
				        target: 'tr'
				    }
				},*/ // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
					targets: [0, 1],
					className: 'dt-body-center'
				}],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmes.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			$("#mytableXanoXmesXcategoria").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
					targets: [0, 1],
					className: 'dt-body-center'
				}],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmesXcategoria.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			$("#mytableXanoXmesXcategoriaXactivo").DataTable({ //tabla de la izquierda
				//respsive
				order: [1, 'desc'],
				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
					targets: [0, 1],
					className: 'dt-body-center'
				}],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmesXcategoriaXactivo.php",
					"type": 'POST',
					"data": parametros,
				}
			});
		});
		// Boton aceptar
		$('#Aceptarm').click(function() {
			// chequemos que parametros son obligatorios
			if ($('#aDetalle').val().length <= 0 || $('#aMonto').val().length <= 0 || $('#aFecha').val().length <= 0 || $('#acat').val() == 0) {
				iziToast.error({
					title: 'Error',
					message: 'COMPLETE DATOS OBLIGATORIOS',
					position: 'bottomRight',
					timeout: 5000
				});
				return;
			}
			///////// preguntar si no tiene deudores///
			$.ajax({
				url: 'hay_dudores.php',
				type: 'POST',
				success: function(resp) {
					if (resp == 'ok') { // hat deudores
						$('#Aceptarm').hide();
						$('#spin1').removeAttr('hidden');
						$('#spin1').show();
						let parametros = {
							"Detalle": $('#aDetalle').val(),
							"Monto": $('#aMonto').val(),
							"Cat": $('#acat').val(),
							"Fecha": $('#aFecha').val()
						};
						registrarGasto(parametros, 'add_Gasto.php', '#Aceptarm', '#spin1', '#compra', '!!!COMPRA REGISTRADA!!!','No se pudo registrar la compra');
					} else {
						iziToast.error({
							title: 'Error',
							message: 'Complete los deudores..',
							position: 'bottomRight',
							timeout: 5000
						});
						return;
					};
				}
			});




		});



		// Accion de añadir deudores
		$('#adeudor').on('change', function() {
			// chequemos que parametros son obligatorios
			if ($('#adeudor').val().length <= 0) {
				iziToast.error({
					title: 'Error',
					message: 'COMPLETE DATOS',
					position: 'bottomRight',
					timeout: 5000
				});
				return;
			}
			let parametros = {
				"deudor": $('#adeudor').val(),
				"pago": 'No',
			};

			$.ajax({
				url: 'adddetallem.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					$('#mytableg').DataTable().ajax.reload(null, false);

					//actuaizamos lista de usuarios
					$.ajax({
						url: 'filtro_usuarios.php',
						type: 'POST',
						success: function(resp) {
							//actualizo el ontrol
							$("#adeudor option").remove();
							$("#adeudor").append(resp);
						}
					});
					// limpiamos
					$('#clientem').val("");
				}
			});
		});

		// Anadirplantilla
		$('#aPlantilla').click(function() {
			// chequemos que parametros son obligatorios
			if ($('#aDetalle').val().length <= 0 || $('#aMonto').val().length <= 0 || $('#acat').val().length <= 0) {
				iziToast.error({
					title: 'Error',
					message: 'COMPLETE DATOS OBLIGATORIOS',
					position: 'bottomRight',
					timeout: 5000
				});
				return;
			}
			///////// preguntar si no tiene deudores///
			$.ajax({
				url: 'hay_dudores.php',
				type: 'POST',
				success: function(resp) {
					if (resp == 'ok') { // hat deudores
						$('#Aceptarm').hide();
						$('#spin1').removeAttr('hidden');
						$('#spin1').show();
						let parametros = {
							"Detalle": $('#aDetalle').val(),
							"Cat": $('#acat').val(),
							"Monto": $('#aMonto').val()
						};
						$.ajax({
							url: 'add_Plantilla.php',
							type: 'POST',
							data: parametros,
							success: function(resp) {
								$('#mytableg').DataTable().ajax.reload(null, false);
								$('#plantilla').modal('hide');
								$('#Aceptarm').show();
								$('#spin1').hide();
								// Limpiar campos
								$('.form-control').val("");
								$('.form-select').val("");
								iziToast.success({
									title: 'Éxito',
									message: '!!!PLANTILLA AGREGADA!!!',
									position: 'bottomRight',
									timeout: 5000
								});
							}
						})
					} else {
						iziToast.error({
							title: 'Error',
							message: 'COMPLETE DEUDORES..',
							position: 'bottomRight',
							timeout: 5000
						});
						return;
					};
				}
			});
		});

		// Anadirplantilla
		$('#dPlantilla').click(function() {
			// chequemos que parametros son obligatorios
			$idplan = $('#aplan').val();
			if ($('#aplan').val() == 0) {
				iziToast.error({
					title: 'Error',
					message: 'Debe haber un plan seleccionado',
					position: 'bottomRight',
					timeout: 5000
				});


				return;
			}
			//seguro borar?

			const swalWithBootstrapButtons = Swal.mixin({
				customClass: {
					confirmButton: "btn btn-success",
					cancelButton: "btn btn-danger"
				},
				buttonsStyling: false
			});
			swalWithBootstrapButtons.fire({
				title: "Seguro de borrar?",
				// text: "You won't be able to revert this!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: " Si ",
				cancelButtonText: " No ",
				reverseButtons: true
			}).then((result) => {
				if (result.isConfirmed) {
					let parametros = {
						"idplan": $('#aplan').val(),
					};
					$.ajax({
						url: 'del_Plantilla.php',
						type: 'POST',
						data: parametros,
						success: function(resp) {

							/// REFRESCAMOS EL SELECT DE PLANTILLAS
							$.ajax({
								url: 'filtro_plan.php',
								type: 'POST',
								success: function(resp) {
									//actualizo el ontrol
									$('#aplan option').remove();
									$('#aplan').append(resp);
								}
							});

							// toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
							// toastr.success("!!!PLANTILLA AGREGADA!!! ",$('#aplan').val());
						}
					})
					swalWithBootstrapButtons.fire({
						title: "Plantilla Borrado",
						//text: "Your file has been deleted.",
						icon: "success"
					});
				} else if (
					/* Read more about handling dismissals below */
					result.dismiss === Swal.DismissReason.cancel
				) {
					// swalWithBootstrapButtons.fire({
					//   title: "Cancelado",
					//   //text: "Your imaginary file is safe :)",
					//   icon: "error"
					// });
				}
			});

		});



		// añado registro seleccionado a gastos
		$('#aplan').on('change', function() {
			//$('#AnadirP').click(function(){
			// chequemos que parametros son obligatorios
			if ($('#aplan').val() == 0) {
				iziToast.error({
					title: 'Error',
					message: 'Seleccione o Agrage planificados',
					position: 'bottomRight',
					timeout: 5000
				});


				return;
			}
			let parametros = {
				"plan": $('#aplan').val(),
			};

			$.ajax({
				url: 'addplan_gasto.php',
				type: 'POST',
				data: parametros,
				success: function(respta) {
					let pln = JSON.parse(respta);
					$('#aDetalle').val(pln[0][0]);
					$('#aMonto').val(pln[0][1]);
					$('#aFecha').val(pln[0][2]);
					$('#acat').val(pln[0][3]);

					$('#mytableg').DataTable().ajax.reload(null, false);
				}
			});

		});




		// cuando se pincha en ficheros
		// $("#tabladeben").on('click', 'tr', function(e) {
		//   e.preventDefault();

		//   var renglon = $(this);
		//   var campo,campo1;

		//   $(this).children("td").each(function(i) {
		//     switch (i) {
		//       case 5:
		//         campo = $(this).text();

		//       break;       
		//     }

		//   })
		//   alert(campo);
		// });



		//evento  al cambiar el mes y el año
		$('#mes, #ano').on('change', function() {
			let nmes = $('#mes').val();
			let nano = $('#ano').val();
			let parametros = {
				"ano": nano,
				"mes": nmes
			};
			$.ajax({
				url: 'totalmesano.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					//actualizo el control
					$('#imptotal').text(resp);
				}
			});
			$.ajax({
				url: 'totalmesanopagado.php',
				type: 'POST',
				data: parametros,
				success: function(resp) {
					//actualizo el control
					$('#pagtotal').text(resp);
				}
			});
			// borro tabla y actualizo
			$('#mytableXanoXmes').DataTable().destroy();
			$('#mytableXanoXmesXcategoria').DataTable().destroy();
			$('#mytableXanoXmesXcategoriaXactivo').DataTable().destroy();

			//$('#mytableXanoXmes').DataTable().ajax.reload(); 			
			$("#mytableXanoXmes").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],
				responsive: {
					details: {
						type: 'column',
						target: 'tr'
					}
				}, // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmes.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			$("#mytableXanoXmesXcategoria").DataTable({ //tabla de la izquierda
				//respsive
				order: [0, 'asc'],
				responsive: {
					details: {
						type: 'column',
						target: 'tr'
					}
				}, // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmesXcategoria.php",
					"type": 'POST',
					"data": parametros,
				}
			});
			$("#mytableXanoXmesXcategoriaXactivo").DataTable({ //tabla de la izquierda
				//respsive
				order: [1, 'desc'],
				responsive: {
					details: {
						type: 'column',
						target: 'tr'
					}
				}, // hast aqui

				searching: false,
				info: false,
				//order: [[0,'asc']],
				columnDefs: [{
						targets: [0, 1, 2],
						className: 'dt-body-center',
					},
					{
						className: 'dtr-control',
						orderable: false,
						target: 0
					}
				],
				"paging": false,
				"dom": '<lf<t>ip>',
				"language": {
					"url": "json/spanish.json"
				},
				stateSave: true,
				ajax: {
					"url": "suma_gastosXanoXmesXcategoriaXactivo.php",
					"type": 'POST',
					"data": parametros,
				}
			});

		});





		/////////////////////////







		function DelDeudor(bus) {
			var id = bus;
			var parametros = {
				"id": id
			};
			$.ajax({
				url: 'DelDeudor.php',
				type: 'POST',
				data: parametros,
				success: function(dato) {
					$('#mytableg').DataTable().ajax.reload(null, false);
					//actualizo la lista de deudores
					$.ajax({
						url: 'filtro_usuarios.php',
						type: 'POST',
						success: function(resp) {
							//actualizo el ontrol
							$('#adeudor option').remove();
							$('#adeudor').append(resp);
							$('#adeudorPrestamo option').remove();
							$('#adeudorPrestamo').append(resp);
						}
					});
				}
			});
		}


		// Obtener datos  filtardos por id 

		function GetDataGasto(bus) {
			var id = bus;
			var parametros = {
				"id": id
			};
			$.ajax({
				url: 'getDataGasto.php',
				type: 'POST',
				data: parametros,
				success: function(dato) {
					let datos = JSON.parse(dato);
					$('#eid').val(datos[0][0]);
					//	$('#ecomprador').val(datos[0][1]);
					$('#edetalle').val(datos[0][2] + " con importe de $" + datos[0][3] + " el dia " + datos[0][4]);
					$('#emonto').val(datos[0][3]);
					$('#efecha').val(datos[0][4]);
					$('#edeudor').val("debe $" + datos[0][7] + " Pago?");
					$('#epago').val(datos[0][6]);
					$('#edeuda').val("Actualiza el Pago de " + datos[0][5]);
				}
			});
		}

		// Accion al editar
		$('#EAceptar').click(function() {
			$('#EAceptar').hide();
			$('#spin0').removeAttr('hidden');
			$('#spin0').show();
			let parametros = {
				"id": $('#eid').val(),
				"pago": $('#epago').val()
			};
			registrarGasto(parametros, 'updatepago.php', '#EAceptar', '#spin0', '#ActualizaPago', '!!!PAGO ACTUALIZADO...!!!!','No se pudo actualizar el pago');
			//

			/*$.ajax({
			url: 'updatepago.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				//$('#tabladeben').DataTable().ajax.reload(null,false);
				$('#tabladebenT').DataTable().ajax.reload(null,false);
				
				iziToast.success("!!!PAGO ACTUALIZADO...!!!!", "");
				// actualizamos el subtotal de por cobrar
				 $.ajax({url: 'subtotalPorCobrar.php',type:'POST',
								   success: function(resp){
						   			   	//actualizo el control
						   			   	$('#porCobrarMonto').text(resp);
			   			   }
			 					});
				$('#ActualizaPago').modal('hide');
				$('#EAceptar').show();
        $('#spin0').hide();
			}
		})*/ // fin de ajax
		});



		function Actualiza_tablas() {
			$('#tabladuedas').DataTable().ajax.reload(null, false);
			$('#tabladuedasT').DataTable().ajax.reload(null, false);
			$('#tabladeben').DataTable().ajax.reload(null, false);
			$('#tabladebenT').DataTable().ajax.reload(null, false);
			$('#mytableg').DataTable().ajax.reload(null, false);
			$('#tabladeudasNetas').DataTable().ajax.reload(null, false);
		}

		$('#cambios').click(function() {
			$.ajax({
				url: 'leer_cambios.php',
				type: 'POST',
				success: function(resp) {
					$('#cambiosRecientes').html(resp);
				}
			});
		});

		// Evento para abrir modal de préstamo
		$('#btnPrestamo').click(function() {
			// Limpiar tabla temporal antes de abrir el modal
			$.ajax({
				url: 'del_temp.php',
				type: 'POST',
				success: function() {
					// Actualizar lista de usuarios (sin opción "Todos" para préstamo)
					$.ajax({
						url: 'filtro_usuarios.php',
						type: 'POST',
						data: { tipo: 'prestamo' },
						success: function(resp) {
							// Limpiar completamente los selectores
							$('#adeudor').empty();
							$('#adeudorPrestamo').empty();
							// Agregar las nuevas opciones
							$('#adeudor').html(resp);
							$('#adeudorPrestamo').html(resp);
							// Actualizar la DataTable mytableg
							$('#mytableg').DataTable().ajax.reload(null, false);
							// Abrir modal después de actualizar usuarios
							$('#prestamo').modal('show');
						}
					});
				},
				error: function() {
					iziToast.error({
						title: 'Error',
						message: 'Error al limpiar datos temporales',
						position: 'bottomRight',
						timeout: 5000
					});
				}
			});
		});

		// Evento para abrir modal de compra
		$('#btnCompra').click(function() {
			// Limpiar tabla temporal antes de abrir el modal
			$.ajax({
				url: 'del_temp.php',
				type: 'POST',
				success: function() {
					// Actualizar lista de usuarios (con opción "Todos" para compra)
					$.ajax({
						url: 'filtro_usuarios.php',
						type: 'POST',
						data: { tipo: 'compra' },
						success: function(resp) {
							// Limpiar completamente los selectores
							$('#adeudor').empty();
							$('#adeudorPrestamo').empty();
							// Agregar las nuevas opciones
							$('#adeudor').html(resp);
							$('#adeudorPrestamo').html(resp);
							// Actualizar la DataTable mytableg
							$('#mytableg').DataTable().ajax.reload(null, false);
							// Abrir modal después de actualizar usuarios
							$('#compra').modal('show');
						}
					});
				},
				error: function() {
					iziToast.error({
						title: 'Error',
						message: 'Error al limpiar datos temporales',
						position: 'bottomRight',
						timeout: 5000
					});
				}
			});
		});

		function registrarGasto(parametros, url, btnAceptar, btnSpin, modal, mensaje,mensaje2) {
			$(btnAceptar).hide();
			$(btnSpin).show();

			$.ajax({
				url: url,
				type: 'POST',
				data: parametros,
				success: function(resp) {
					if (resp.includes('ok')) {
						iziToast.success({
							title: 'Éxito',
							message: mensaje,
							position: 'bottomRight',
							timeout: 5000
						});
						// Limpiar campos
						$('.form-control').val("");
						$('.form-select').val("");
						$(modal).modal('hide');

						// Actualizar el subtotal de por cobrar y mostrar la sección
						$.ajax({
							url: 'subtotalPorCobrar.php',
							type: 'POST',
							success: function(resp) {
								$('#porCobrarMonto').text(resp);
								// Extraer el valor numérico de la respuesta
								let valorNumerico = resp.replace(/[^0-9.-]+/g, "");
								if (parseFloat(valorNumerico) > 0) {
									$('#porCobrarSection').show();
									$('#porCobrarDetalle').hide();
								} else {
									$('#porCobrarSection').hide();
									$('#porCobrarDetalle').hide();

								}
								$('#tabladebenT').DataTable().ajax.reload(null, false);
								$('#tabladuedasT').DataTable().ajax.reload(null, false);
								$('#tabladeudasNetas').DataTable().ajax.reload(null, false);
								$('#tabladuedas').DataTable().ajax.reload(null, false);

							}
						});
					} else {
						iziToast.error({
							title: 'Error',
							message: mensaje2,
							position: 'bottomRight',
							timeout: 5000
						});
					}
					$(btnAceptar).show();
					$(btnSpin).hide();

				},
				error: function() {
					iziToast.error({
						title: 'Error',
						message: 'Error de conexión',
						position: 'bottomRight',
						timeout: 5000
					});
					$(btnAceptar).show();
					$(btnSpin).hide();
				}

			});
		}

		// Evento para generar PDF del reporte
		$('#generarPDF').on('click', function() {
			let nmes = $('#mes').val();
			let nano = $('#ano').val();
			
			// Abrir el PDF en una nueva ventana
			window.open('generar_pdf_reporte.php?ano=' + nano + '&mes=' + nmes, '_blank');
		});

		/////////////////////////
	</script>

	<!-- Modal para ver cambios -->
	<div class="modal shadow" id="verCambios" tabindex="-1" aria-labelledby="verCambios" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Cambios Recientes</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="cambiosRecientes">
						<!-- El contenido se cargará dinámicamente -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
				</div>
			</div>
		</div>
	</div>

	<script>
		document.addEventListener('DOMContentLoaded', function() {
			function cargarCambios() {
				$.ajax({
					url: 'leer_cambios.php',
					type: 'POST',
					success: function(resp) {
						$('#cambiosRecientes').html(resp);
					}
				});
			}

			// 1. Pedir la fecha más reciente de cambios al backend
			$.ajax({
				url: 'leer_cambios.php',
				type: 'POST',
				data: {
					solo_fecha: 1
				},
				dataType: 'json',
				success: function(data) {
					if (data.fecha) {
						const cambiosKey = 'cambiosLeidos_' + data.fecha;
						if (!localStorage.getItem(cambiosKey)) {
							// Mostrar el modal y marcar como leído para esa fecha
							var cambiosModal = new bootstrap.Modal(document.getElementById('verCambios'));
							cambiosModal.show();
							localStorage.setItem(cambiosKey, 'true');
							cargarCambios();
						}
					}
				}
			});

			// También cargar los cambios cuando se abre el modal manualmente
			$('#verCambios').on('show.bs.modal', function() {
				cargarCambios();
			});
		});

		$('#AceptarPrestamo').click(function() {
			// Validar campos obligatorios
			if (
				$('#aDetallePrestamo').val().length <= 0 ||
				$('#aMontoPrestamo').val().length <= 0 ||
				$('#aFechaPrestamo').val().length <= 0 ||
				$('#adeudorPrestamo').val() == 0
			) {
				iziToast.error({
					title: 'Error',
					message: 'COMPLETE DATOS OBLIGATORIOS',
					position: 'bottomRight',
					timeout: 5000
				});
				return;
			}

			// Mostrar spinner y ocultar botón
			$('#AceptarPrestamo').hide();
			$('#spinPrestamo').removeAttr('hidden').show();

			let parametros = {
				"Detalle": $('#aDetallePrestamo').val(),
				"Monto": $('#aMontoPrestamo').val(),
				"Fecha": $('#aFechaPrestamo').val(),
				"Deudor": $('#adeudorPrestamo').val()
			};

			registrarGasto(parametros, 'add_Prestamo.php', '#AceptarPrestamo', '#spinPrestamo', '#prestamo', '¡Préstamo registrado!','No se pudo registrar el préstamo');
		}); // fin dek AceptarPrestamo

		// Mostrar/ocultar detalles de Por Pagar y cargar datos por AJAX solo si el importe es mayor que 0
		let porPagarLoaded = false;
		$('#togglePorPagar').click(function() {
			$('#porPagarDetalle').toggle();
			var icon = $(this).find('i');
			icon.toggleClass('bi-eye bi-eye-slash');
			if ($('#porPagarDetalle').is(':visible')) {
				// Destruir DataTable si ya existe
				if ($.fn.DataTable.isDataTable('#tabladuedas')) {
					$('#tabladuedas').DataTable().destroy();
					$('#tabladuedas').empty();
					$('#tabladuedas').html(
						'<thead class="text-white">' +
						'<th class="dt-head-center">comprador</th>' +
						'<th class="dt-head-center">Detalle</th>' +
						'<th class="dt-head-center">Importe</th>' +
						'<th class="dt-head-center">Fecha</th>' +
						'<th class="dt-head-center">Deuda</th>' +
						'</thead>'
					);
				}
				$('#tabladuedas').DataTable({
					order: [0, 'asc'],
					responsive: true,
					searching: false,
					info: false,
					paging: false,
					language: {
						"url": "json/spanish.json"
					},
					stateSave: false,
					ajax: {
						"url": "por_pagar.php",
						"type": 'POST'
					}
				});
				porPagarLoaded = true;
			}
		});

		// Mostrar/ocultar detalles de Por Cobrar y cargar datos por AJAX solo si el importe es mayor que 0
		let porCobrarLoaded = false;
		$('#togglePorCobrar').click(function() {
			$('#porCobrarDetalle').toggle();
			var icon = $(this).find('i');
			icon.toggleClass('bi-eye bi-eye-slash');
			if ($('#porCobrarDetalle').is(':visible')) {
				if ($.fn.DataTable.isDataTable('#tabladeben')) {
					$('#tabladeben').DataTable().destroy();
					$('#tabladeben').empty();
					$('#tabladeben').html(
						'<thead class="text-white">' +
						'<tr>' +
						'<th rowspan="2" class="dt-head-center">Deudor</th>' +
						'<th rowspan="2" class="dt-head-center">Deuda</th>' +
						'<th colspan="3" class="dt-head-center" data-dt-order="disable">Datos de la compra</th>' +
						'</tr>' +
						'<th class="dt-head-center">Detalle</th>' +
						'<th class="dt-head-center">Importe</th>' +
						'<th class="dt-head-center">Fecha</th>' +
						'</thead>'
					);
				}
				$('#tabladeben').DataTable({
					order: [0, 'asc'],
					responsive: true,
					searching: true,
					info: false,
					paging: false,
					language: {
						"url": "json/spanish.json"
					},
					stateSave: false,
					ajax: {
						"url": "por_cobrar.php",
						"type": 'POST'
					}
				});
				porCobrarLoaded = true;
			}
		});

		// Fix: limpiar modal-open solo si no hay ningún modal visible
		$(document).on('hidden.bs.modal', function() {
			if ($('.modal.show').length === 0) {
				$('body').removeClass('modal-open');
			}
		});

		$('#compra').on('hidden.bs.modal', function() {
			$('.modal-backdrop').remove();
			$('body').removeClass('modal-open');
			$('body').css('overflow', '');
			$('body').css('padding-right', '');
		});
		$('#prestamo').on('hidden.bs.modal', function() {
			$('.modal-backdrop').remove();
			$('body').removeClass('modal-open');
			$('body').css('overflow', '');
			$('body').css('padding-right', '');
		});
	</script>

	<script>
	// Agregar esto después de la inicialización de las otras tablas
	$(document).ready(function() {
		// Variable para almacenar el ID del deudor actual
		let deudorIdActual = null;
		
		// Función para cargar las deudas del deudor
		function cargarDeudasDeudor(deudorId) {
			deudorIdActual = deudorId;
			$.ajax({
				url: 'obtener_deudas_deudor.php',
				type: 'POST',
				data: { deudor_id: deudorId },
				success: function(response) {
					const deudas = JSON.parse(response).data;
					const tbody = $('#cuerpoTablaDeudas');
					tbody.empty();
					
					deudas.forEach(deuda => {
						tbody.append(`
							<tr>
								<td><input type="checkbox" class="deuda-checkbox" data-monto="${deuda.monto}" data-id="${deuda.id}"></td>
								<td>${deuda.fecha}</td>
								<td>${deuda.detalle}</td>
								<td>$${parseFloat(deuda.monto).toFixed(2)}</td>
							</tr>
						`);
					});
					
					actualizarTotalSeleccionado();
				}
			});
		}
		
		// Función para actualizar el total seleccionado
		function actualizarTotalSeleccionado() {
			let total = 0;
			$('.deuda-checkbox:checked').each(function() {
				total += parseFloat($(this).data('monto'));
			});
			$('#totalSeleccionado').text('$' + total.toFixed(2));
		}
		
		// Evento para seleccionar/deseleccionar todas las deudas
		$('#seleccionarTodos').change(function() {
			$('.deuda-checkbox').prop('checked', $(this).prop('checked'));
			actualizarTotalSeleccionado();
		});
		
		// Evento para actualizar el total cuando se selecciona una deuda individual
		$(document).on('change', '.deuda-checkbox', function() {
			actualizarTotalSeleccionado();
			// Actualizar el checkbox "seleccionar todos"
			const totalCheckboxes = $('.deuda-checkbox').length;
			const checkedCheckboxes = $('.deuda-checkbox:checked').length;
			$('#seleccionarTodos').prop('checked', totalCheckboxes === checkedCheckboxes);
		});
		
		// Modificar el evento de clic en la tabla tabladebenT
		$("#tabladebenT").on('click', 'tbody tr', function() {
			var data = $("#tabladebenT").DataTable().row(this).data();
			if (data) {
				var deudor = data[0]; // El nombre del deudor está en la primera columna
				var deudorId = data[2]; // El ID del deudor está en la tercera columna
				
				// Mostrar el modal con el nombre del deudor
				$("#nombreDeudor").text(deudor);
				$("#deudorId").val(deudorId);
				
				// Cargar las deudas del deudor
				cargarDeudasDeudor(deudorId);
				
				// Mostrar el modal
				$("#actualizarTodasDeudasModal").modal('show');
			}
		});
		
		// Manejar clic en el botón Actualizar seleccionadas
		$("#actualizarTodasSi").click(function() {
			const deudasSeleccionadas = [];
			$('.deuda-checkbox:checked').each(function() {
				deudasSeleccionadas.push($(this).data('id'));
			});
			
			if (deudasSeleccionadas.length === 0) {
				iziToast.warning({
					title: 'Advertencia',
					message: 'Por favor, seleccione al menos una deuda para actualizar',
					position: 'bottomRight'
				});
				return;
			}
			
			// Mostrar spinner
			$(this).hide();
			$("#actualizarTodasNo").hide();
			$("#spinActualizarTodas").removeAttr('hidden');
			$("#spinActualizarTodas").show();
			
			// Llamada AJAX para actualizar las deudas seleccionadas
			$.ajax({
				url: 'actualizar_deudas_seleccionadas.php',
				type: 'POST',
				data: {
					deudor_id: deudorIdActual,
					deudas: deudasSeleccionadas
				},
				success: function(response) {
					// Actualizar el subtotal de por cobrar
					$.ajax({
						url: 'subtotalPorCobrar.php',
						type: 'POST',
						success: function(resp) {
							$('#porCobrarMonto').text(resp);
							    console.log('AJAX interno success', resp);
								// Extraer el valor numérico de la respuesta
								let valorNumerico = resp.replace(/[^0-9.-]+/g, "");
								if (parseFloat(valorNumerico) > 0) {
									$('#porCobrarSection').show();
									$('#porCobrarDetalle').hide();
								} else {
									$('#porCobrarSection').hide();
									$('#porCobrarDetalle').hide();

								}
								$('#tabladebenT').DataTable().ajax.reload(null, false);
								$('#tabladuedasT').DataTable().ajax.reload(null, false);
								$('#tabladeudasNetas').DataTable().ajax.reload(null, false);
								$('#tabladuedas').DataTable().ajax.reload(null, false);
						},
						error: function(xhr, status, error) {
							console.error('Error AJAX interno:', error);
						}
					});
					
					// Cerrar el modal
					$("#actualizarTodasDeudasModal").modal('hide');
					
					// Mostrar mensaje de éxito
					iziToast.success({
						title: 'Éxito',
						message: 'Deudas actualizadas correctamente',
						position: 'bottomRight'
					});
					
					// Restaurar botones
					$("#actualizarTodasSi").show();
					$("#actualizarTodasNo").show();
					$("#spinActualizarTodas").hide();
				},
				error: function() {
					iziToast.error({
						title: 'Error',
						message: 'Error al actualizar las deudas',
						position: 'bottomRight'
					});
					
					// Restaurar botones
					$("#actualizarTodasSi").show();
					$("#actualizarTodasNo").show();
					$("#spinActualizarTodas").hide();
				}
			});
		});
		
		// Manejar clic en el botón Cancelar
		$("#actualizarTodasNo").click(function() {
			$("#actualizarTodasDeudasModal").modal('hide');
		});
	});
	</script>

	<!-- Modal de Notificaciones -->
	<div class="modal fade" id="notificacionesModal" tabindex="-1" aria-labelledby="notificacionesModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="notificacionesModalLabel">
						<i class="bi bi-bell"></i> Notificaciones Inteligentes
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="panelNotificaciones">
						<!-- El contenido se cargará dinámicamente -->
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
					<button type="button" class="btn btn-primary" id="btnGenerarNotificaciones">
						<i class="bi bi-lightning"></i> Generar Notificaciones
					</button>
				</div>
			</div>
		</div>
	</div>

	<style>
		/* Estilos para el panel de notificaciones */
		.notifications-panel {
			max-height: 500px;
			overflow-y: auto;
		}
		
		.notifications-header {
			border-bottom: 2px solid #007bff;
			padding-bottom: 10px;
			margin-bottom: 15px;
		}
		
		.notification-item {
			border: 1px solid #dee2e6;
			border-radius: 8px;
			padding: 15px;
			margin-bottom: 10px;
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
			transition: all 0.3s ease;
		}
		
		.notification-unread {
			background-color: #f8f9fa;
			border-left: 4px solid #007bff;
		}
		
		.notification-read {
			background-color: #ffffff;
			opacity: 0.7;
		}
		
		.priority-high {
			border-left-color: #dc3545;
		}
		
		.priority-normal {
			border-left-color: #ffc107;
		}
		
		.notification-content {
			flex: 1;
		}
		
		.notification-title {
			font-weight: bold;
			margin-bottom: 5px;
			color: #333;
		}
		
		.notification-text {
			color: #666;
			margin-bottom: 5px;
		}
		
		.notification-time {
			color: #999;
			font-size: 0.85em;
		}
		
		.no-notifications {
			text-align: center;
			padding: 40px 20px;
			color: #666;
		}
		
		.mark-read {
			margin-left: 10px;
		}
	</style>

	<script>
		// Funcionalidad del panel de notificaciones
		$(document).ready(function() {
			// Cargar notificaciones al abrir el modal
			$('#notificacionesModal').on('show.bs.modal', function() {
				cargarNotificaciones();
				actualizarBadgeNotificaciones();
			});
			
			// Función para cargar notificaciones
			function cargarNotificaciones() {
				$.ajax({
					url: 'panel_notificaciones_simple.php',
					type: 'GET',
					success: function(response) {
						$('#panelNotificaciones').html(response);
					},
					error: function() {
						$('#panelNotificaciones').html('<p class="text-danger">Error al cargar notificaciones</p>');
					}
				});
			}
			
			// Función para actualizar el badge de notificaciones
			function actualizarBadgeNotificaciones() {
				$.ajax({
					url: 'panel_notificaciones_simple.php?action=get_stats',
					type: 'GET',
					success: function(response) {
						const stats = JSON.parse(response);
						const badge = $('#notificacionesBadge');
						
						if (stats.no_leidas > 0) {
							badge.text(stats.no_leidas).show();
						} else {
							badge.hide();
						}
					}
				});
			}
			
			// Marcar notificación como leída
			$(document).on('click', '.mark-read', function() {
				const id = $(this).data('id');
				const item = $(this).closest('.notification-item');
				
				$.ajax({
					url: 'panel_notificaciones_simple.php?action=mark_read',
					type: 'POST',
					data: { id: id },
					success: function(response) {
						const result = JSON.parse(response);
						if (result.success) {
							item.removeClass('notification-unread').addClass('notification-read');
							$(this).remove();
							actualizarBadgeNotificaciones();
						}
					}
				});
			});
			
			// Generar notificaciones manualmente
			$('#btnGenerarNotificaciones').click(function() {
				$(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Generando...');
				
				$.ajax({
					url: 'generar_notificaciones.php',
					type: 'GET',
					success: function(response) {
						const result = JSON.parse(response);
						iziToast.success({
							title: 'Éxito',
							 message: `Se generaron ${result.notificaciones_generadas} notificaciones`,
							position: 'bottomRight'
						});
						cargarNotificaciones();
						actualizarBadgeNotificaciones();
					},
					error: function() {
						iziToast.error({
							title: 'Error',
							message: 'Error al generar notificaciones',
							position: 'bottomRight'
						});
					},
					complete: function() {
						$('#btnGenerarNotificaciones').prop('disabled', false).html('<i class="bi bi-lightning"></i> Generar Notificaciones');
					}
				});
			});
			
			// Generar notificaciones automáticamente (botón del header)
			$('#btnGenerarNotificacionesAuto').click(function() {
				$(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');
				
				$.ajax({
					url: 'cron_notificaciones.php',
					type: 'GET',
					success: function(response) {
						const result = JSON.parse(response);
						iziToast.success({
							title: 'Notificaciones Generadas',
							message: `Se generaron ${result.notificaciones_generadas} notificaciones automáticamente`,
							position: 'bottomRight'
						});
						//actualizarBadgeNotificaciones();
					},
					error: function() {
						iziToast.error({
							title: 'Error',
							message: 'Error al generar notificaciones automáticas',
							position: 'bottomRight'
						});
					},
					complete: function() {
						$('#btnGenerarNotificacionesAuto').prop('disabled', false).html('<i class="bi bi-gear"></i>');
					}
				});
			});
			
			// Actualizar badge al cargar la página
			actualizarBadgeNotificaciones();
			
			// Actualizar cada 5 minutos
			setInterval(actualizarBadgeNotificaciones, 300000);
		});
	</script>
</body>

</html>