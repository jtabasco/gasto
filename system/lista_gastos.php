<?php 
	session_start();
	if(empty($_SESSION['active']) || $_SESSION['rol'] != 1) {
		header('location: ../');
	}
	
	include "../con.php";	
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gastos</title>
</head>
<body>
<?php include "../include/nav.php"; ?>
<?php include "../include/footer.php"; ?>

<div class="row mt-5 ms-3 me-3"></div>	

<div class="row mt-5 ms-3 me-3">
	<div class="col-md-2"></div>
	<div class="col-sm-8 mt-3">
		<div class="card shadow">
			<div class="card-title bg-primary fw-bold text-white shadow rounded p-1">
				Gastos
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover justify-content-center display" id="mytable" style="width:100%">
						<thead>
							<th class="dt-head-center bg-primary text-white">Comprador</th>
							<th class="dt-head-center bg-primary text-white">Fecha</th>
							<th class="dt-head-center bg-primary text-white">Detalle</th>
							<th class="dt-head-center bg-primary text-white">Monto</th>
							<th class="dt-head-center bg-primary text-white"></th>
						</thead>
						<tbody>
							<?php
							$consulta = $conn->query("SELECT Gastos.id,usuarios.nombre as comprador,Gastos.fecha,Gastos.detalle,Gastos.Monto FROM Gastos inner join usuarios on usuarios.id = Gastos.comprador order by id desc");
							while ($row = mysqli_fetch_assoc($consulta)) {
								echo '<tr>
									<td>'.$row['comprador'].'</td>
									<td>'.$row['fecha'].'</td>
									<td>'.$row['detalle'].'</td>
									<td>'.$row['Monto'].'</td>
									<td>
										<span class="edit text-danger text-center"><i class="bi bi-trash" onclick="eliminarGasto('.$row['id'].')"></i></span>
									</td>
								</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-2"></div>
</div>

<div class="row mb-5 ms-3 me-3"></div>	

<!-- Modal para confirmar eliminación -->
<div class="modal shadow" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-mb modal-dialog modal-dialog-centered ">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar este gasto?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let gastoId; // Variable para almacenar el ID del gasto a eliminar

    function eliminarGasto(id) {
        gastoId = id; // Guardar el ID del gasto
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
               
                location.reload();
                toastr.success('Gasto eliminado con éxito');
            }
        });
    });
</script>

</body>
</html> 