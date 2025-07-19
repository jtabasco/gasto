<?php 
	session_start();
	  if(empty($_SESSION['active']) ){
		    header('location: ../');
  	  }
		
	include "../conexion.php";	
 ?>
<?php // include "../logr.php";?>
<?php // escribelog($conection,$_SESSION['user'],"Listado de Usuarios"); ?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Deudas</title>
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
	<!-- Bootstrap Icons -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<!-- DataTables RowGroup CSS -->
	<link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">
	<!-- DataTables RowGroup JS -->
	<script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>
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
		
		/* Grupos de deudas mejorados */
		tr.dtrg-group {
			background: linear-gradient(45deg, #667eea, #764ba2) !important;
			color: white !important;
			font-weight: bold;
			font-size: 1.1em;
			text-shadow: 0 1px 2px rgba(0,0,0,0.3);
		}
		
		tr.group td {
			background: linear-gradient(45deg, #667eea, #764ba2) !important;
			color: white !important;
			font-weight: bold;
			font-size: 1.1em;
			border-bottom: 2px solid #90caf9;
			text-shadow: 0 1px 2px rgba(0,0,0,0.3);
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
			
			.table-responsive {
				overflow-x: auto;
			}
			
			#tabladeudasNetas th, #tabladeudasNetas td {
				white-space: nowrap;
				font-size: 0.95em;
				padding: 6px 8px;
			}
			
			tr.group td {
				font-size: 1em;
				padding: 8px 8px;
			}
		}
		
		/* Estados de deuda */
		.deuda-alta {
			background: linear-gradient(45deg, #dc3545, #fd7e14);
			color: white;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 0.8rem;
			font-weight: 600;
		}
		
		.deuda-media {
			background: linear-gradient(45deg, #ffc107, #ffca2c);
			color: #212529;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 0.8rem;
			font-weight: 600;
		}
		
		.deuda-baja {
			background: linear-gradient(45deg, #28a745, #20c997);
			color: white;
			padding: 4px 12px;
			border-radius: 20px;
			font-size: 0.8rem;
			font-weight: 600;
		}
	</style>
</head>
<body>
<?php include "../include/nav.php";?>

<div class="main-container">
	<!-- Sección de Deudas Netas -->
	<div class="section-title">
		<h3><i class="bi bi-cash-stack me-2"></i>Deudas Netas por Familia</h3>
	</div>
	
	<div class="table-container">
		<div class="table-responsive">
			<table class="table table-custom table-hover" id="tabladeudasNetas" style="width:100%">
				<thead>
					<tr>
						<th class="text-center"><i class="bi bi-person-fill icon-cell"></i>Deudor</th>
						<th class="text-center"><i class="bi bi-person-check-fill icon-cell"></i>Acreedor</th>
						<th class="text-center"><i class="bi bi-cash-coin icon-cell"></i>Total</th>
						<th class="text-center"><i class="bi bi-people-fill icon-cell"></i>Familia</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
	
	<!-- Sección de Deudas Detalladas -->
	<div class="section-title mt-5">
		<h3><i class="bi bi-list-ul me-2"></i>Detalle de Deudas</h3>
	</div>
	
	<div class="table-container">
		<div class="table-responsive">
			<table class="table table-custom table-hover" id="mytable" style="width:100%">
				<thead>
					<tr>
						<th class="text-center"><i class="bi bi-person-fill icon-cell"></i>Deudor</th>
						<th class="text-center"><i class="bi bi-cash-coin icon-cell"></i>T/Comp.</th>
						<th class="text-center"><i class="bi bi-cash-coin icon-cell"></i>Debe</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<?php include "../include/footer.php"; ?>

<script type="text/javascript" >
	
	$(document).ready(function(){
		var groupColumn = 3; // Familia es la columna 3
		var table = $("#tabladeudasNetas").DataTable({ 
			columnDefs: [{ visible: false, targets: groupColumn },
				{ targets: [0,1,2], className: 'dt-body-center' }
			],
			order: [[groupColumn, 'asc']],
			paging: false,
			searching: false,
			info: false,
			language: {
				"url": "json/spanish.json"
			},
			stateSave: false,
			ajax: {
				"url": "deudas_netasT.php",
				"type": 'POST'
			},
			drawCallback: function (settings) {
				var api = this.api();
				var rows = api.rows({ page: 'current' }).nodes();
				var last = null;
				api.column(groupColumn, { page: 'current' })
					.data()
					.each(function (group, i) {
						if (last !== group) {
							$(rows)
								.eq(i)
								.before(
									'<tr class="group"><td colspan="3" style="background:linear-gradient(45deg, #667eea, #764ba2);color:white;font-weight:bold;text-shadow:0 1px 2px rgba(0,0,0,0.3);">' +
										'<i class="bi bi-people-fill me-2"></i>' + group +
									'</td></tr>'
								);
							last = group;
						}
					});
			}
		});
		
		// Permitir ordenar por grupo al hacer click en la fila de grupo
		$('#tabladeudasNetas tbody').on('click', 'tr.group', function () {
			var currentOrder = table.order()[0];
			if (currentOrder[0] === groupColumn && currentOrder[1] === 'asc') {
				table.order([[groupColumn, 'desc']]).draw();
			}
			else {
				table.order([[groupColumn, 'asc']]).draw();
			}
		});
		
		$("#mytable").DataTable({ //tabla de la izquierda
			ordering:false,
			searching:false,
			info:false,
			columnDefs: [{targets: [0,1,2],
				className: 'dt-body-center',},
				{
					className: 'dtr-control',
					orderable: false,
					target: 0
				}],
			"paging": false,
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"../ajax/load_deudas.php",
				   "type": 'POST',
				}
			});
	});

	// Obtener datos filtrados por Id
	function enviarcorreo(id,debe,dias) {
		 var parametros = {"id": id,"debe":debe,"dias":dias};
		 $.ajax({
			url: 'enviacorreo.php',
			type:'POST',
			data: parametros,
			dataType: 'json',
			success : function(response) {
				if (response.success){
					iziToast.success({
						title: '¡Éxito!',
						message: response.message + ' - ' + response.metodo_usado,
						position: 'bottomRight'
					});
				}else{
					iziToast.error({
						title: 'Error',
						message: response.message || 'Error al enviar mensaje',
						position: 'bottomRight'
					});
				}
			},
			error: function(xhr, status, error) {
				iziToast.error({
					title: 'Error de Conexión',
					message: 'No se pudo conectar con el servidor',
					position: 'bottomRight'
				});
			}
			});
	}   
</script>

</body>
</html>
