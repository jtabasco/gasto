<?php 
	session_start();
	if(empty($_SESSION['active']) || $_SESSION['rol'] != 1) {
		header('location: ../');
	}
	
	include "../conexion.php";	
?>

<!DOCTYPE html lang="es">
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gastos</title>
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
		
		/* Botones de acción */
		.action-btn {
			border-radius: 50px;
			padding: 6px 15px;
			font-size: 0.85rem;
			font-weight: 600;
			transition: all 0.3s ease;
			border: none;
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
			background: linear-gradient(45deg, #dc3545, #fd7e14);
			color: white;
			border-radius: 20px 20px 0 0;
			border: none;
		}
		
		.modal-footer {
			border-top: 1px solid #e9ecef;
			padding: 20px;
		}
		
		/* Responsive */
		@media (max-width: 768px) {
			.main-container {
				margin: 5px;
				padding: 15px;
				margin-top: 4rem;
			}
			
			.section-title {
				flex-direction: column;
				gap: 15px;
				text-align: center;
				padding: 10px 15px;
			}
			
			.section-title h3 {
				font-size: 1.2rem;
			}
			
			.table-container {
				padding: 15px;
				margin: 15px 0;
			}
			
			.table-custom th {
				padding: 8px 4px;
				font-size: 0.8rem;
			}
			
			.table-custom td {
				padding: 8px 4px;
				font-size: 0.85rem;
			}
			
			.icon-cell {
				font-size: 1rem;
				margin-right: 0.3rem;
			}
			
			/* Ocultar columnas menos importantes en móvil */
			.table-custom th:nth-child(3),
			.table-custom td:nth-child(3) {
				display: none;
			}
			
			/* Hacer el modal más compacto */
			.modal-dialog {
				margin: 10px;
			}
			
			.modal-body {
				padding: 15px;
			}
			
			.modal-footer {
				padding: 15px;
			}
		}
		
		@media (max-width: 576px) {
			.main-container {
				margin: 2px;
				padding: 10px;
			}
			
			.section-title {
				padding: 8px 12px;
			}
			
			.section-title h3 {
				font-size: 1.1rem;
			}
			
			.table-container {
				padding: 10px;
			}
			
			.table-custom th {
				padding: 6px 2px;
				font-size: 0.75rem;
			}
			
			.table-custom td {
				padding: 6px 2px;
				font-size: 0.8rem;
			}
			
			.icon-cell {
				font-size: 0.9rem;
				margin-right: 0.2rem;
			}
			
			/* Ocultar más columnas en pantallas muy pequeñas */
			.table-custom th:nth-child(2),
			.table-custom td:nth-child(2) {
				display: none;
			}
			
			/* Hacer botones más pequeños */
			.btn-sm {
				padding: 4px 8px;
				font-size: 0.75rem;
			}
		}

		/* Alinear buscador y paginación de DataTables a la derecha */
		.dataTables_filter {
			float: right !important;
			text-align: right !important;
		}
		.dataTables_paginate {
			float: right !important;
			text-align: right !important;
		}
		.dataTables_length {
			float: left !important;
		}
	</style>
</head>
<body>

<?php include "../include/nav.php"; ?>

<div class="main-container">
	<!-- Sección de Gastos -->
	<div class="section-title">
		<h3><i class="bi bi-cart-check me-2"></i>Gestión de Gastos</h3>
	</div>
	
	<div class="table-container">
		<table class="table table-custom table-hover" id="mytable" style="width:100%">
			<thead>
				<tr>
					<th class="text-center"><i class="bi bi-person-fill icon-cell"></i>Comprador</th>
					<th class="text-center"><i class="bi bi-calendar-event icon-cell"></i>Fecha</th>
					<th class="text-center"><i class="bi bi-tags-fill icon-cell"></i>Categoría</th>
					<th class="text-center"><i class="bi bi-card-text icon-cell"></i>Detalle</th>
					<th class="text-center"><i class="bi bi-cash-coin icon-cell"></i>Monto</th>
					<th class="text-center"><i class="bi bi-gear-fill icon-cell"></i>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<!-- Modal para confirmar eliminación -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar este gasto?</p>
                <div id="detalleGastoEliminar" class="mt-3 p-3 bg-light rounded"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger action-btn delete-btn" id="confirmDeleteButton">
                    <i class="bi bi-trash me-1"></i>Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script type="text/javascript">
    let gastoId; // Variable para almacenar el ID del gasto a eliminar

    function eliminarGasto(id) {
        gastoId = id; // Guardar el ID del gasto

        // Obtener la fila correspondiente desde DataTables
        var table = $('#mytable').DataTable();
        var fila = $('button[onclick="eliminarGasto(' + id + ')"]').closest('tr');
        var data = table.row(fila).data();

        // Los datos están en el array data en el orden: [comprador, fecha, categoria, detalle, monto, botones]
        var comprador = data[0];
        var fecha = data[1];
        var categoria = data[2];
        var detalle = data[3];
        var monto = data[4].replace('$', ''); // Quitar el símbolo $ para mostrar correctamente

        // Construir el mensaje de detalle
        var mensaje = `
            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <strong>Comprador:</strong><br>
                    <span class="text-primary">${comprador}</span>
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <strong>Fecha:</strong><br>
                    <span class="text-info">${fecha}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-6 mb-2">
                    <strong>Categoría:</strong><br>
                    <span class="text-success">${categoria}</span>
                </div>
                <div class="col-12 col-md-6 mb-2">
                    <strong>Monto:</strong><br>
                    <span class="text-danger fw-bold">$${monto}</span>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <strong>Detalle:</strong><br>
                    <span class="text-muted">${detalle}</span>
                </div>
            </div>
        `;

        // Mostrar el mensaje en el modal
        $('#detalleGastoEliminar').html(mensaje);

        // Mostrar el modal
        $('#confirmDeleteModal').modal('show');
    }

    // Evento para el botón de eliminar en el modal
    $('#confirmDeleteButton').on('click', function() {
        $.ajax({
            url: '../ajax/eliminar_gasto.php',
            type: 'POST',
            data: { id: gastoId },
            success: function(response) {
                // Elimina la fila de la tabla sin recargar la página
                $('#mytable').DataTable().row($('button[onclick="eliminarGasto(' + gastoId + ')"]').parents('tr')).remove().draw();
                $('#confirmDeleteModal').modal('hide');
                iziToast.success({
                    title: 'Éxito',
                    message: 'Gasto eliminado con éxito',
                    position: 'bottomRight',
                    timeout: 5000
                });
            },
            error: function() {
                iziToast.error({
                    title: 'Error',
                    message: 'Error al eliminar el gasto',
                    position: 'bottomRight',
                    timeout: 5000
                });
            }
        });
    });

    $(document).ready(function() {
        $('#mytable').DataTable({
            order: [[1, 'desc']],
            responsive: {
                details: {
                    type: 'column',
                    target: 'tr'
                }
            },
            searching: true,
            info: true,
            columnDefs: [
                {
                    targets: [0,1,2,3,4,5],
                    className: 'dt-body-center',
                },
                {
                    targets: [3], // Columna detalle
                    responsivePriority: 1
                },
                {
                    targets: [4], // Columna monto
                    responsivePriority: 2
                },
                {
                    targets: [2], // Columna categoría
                    responsivePriority: 3
                },
                {
                    targets: [1], // Columna fecha
                    responsivePriority: 4
                }
            ],
            "paging": true,
            "pageLength": 10,
            "lengthMenu": [[5, 10, 25, 50], [5, 10, 25, 50]],
            "language":{
                "url":"json/spanish.json"
            },
            stateSave: false,
            ajax: {"url":"../ajax/load_gastos.php",
                   "type": 'POST',
                },
            // Configuración específica para móvil
            "scrollX": true,
            "scrollCollapse": true,
            "autoWidth": false
        });
    });
</script>

<?php
$conn->close();
?>

</body>
</html> 