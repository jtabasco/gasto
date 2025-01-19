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
	<title>Usuarios</title>
	

	
</head>
<body >
<?php include "../include/nav.php";?>
<?php include "../include/footer.php"; ?>
     
  
<div class="row mt-5 ms-3 me-3"></div>	
	
<div class="row mt-5 ms-3 me-3">
	<div class="col-md-2"></div>
	<div class="col-sm-8 mt-3 ">
	        <div class="card shadow" >
	        	<div class="card-title bg-primary  fw-bold text-white shadow rounded p-1"  >
	        	Usuarios<button type="button" class="btn btn-success f-strong ms-3" data-bs-toggle="modal" data-bs-target="#aUsuario" id="addAceptar">+</button> 
	        	</div>
	          <div class="card-body">
	          	<!-- <div class="card-title bg-primary  fw-bold text-white shadow rounded p-1"  >Detallado </div> -->
	            <!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
									<table class="table table-responsive table-bordered table-hover justify-content-center display " id="mytable"  style="width:100%">
										<thead>
											<th class=" dt-head-center bg-primary text-white">Usuario</th>
											<th class=" dt-head-center bg-primary text-white">Rol</th>
											<th class=" dt-head-center bg-primary text-white">email</th>
											<th class=" dt-head-center bg-primary text-white">Telefono</th>
											<th class=" dt-head-center bg-primary text-white">Activo</th>
										</thead>
									</table>
	          </div>
	         </div>
	      </div>    

	<div class="col-md-2"></div>
</div>

     <!-- Entrada de usuario -->
<div class="modal" id="aUsuario" tabindex="-1" aria-labelledby="addusers" aria-hidden="true">
  <div class="modal-md modal-dialog modal-dialog-centered modal-dialog-scrollable" >
    <div class="modal-content" s>
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="addusers">Añadir Usuario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
       	<label class="form-label" for="Usuario">Usuario</label>
		<input class="form-control" type="text" name="Usuario" required="" id="aUsu" >
		<label class="form-label" for="Rol">Rol</label>
			<select class="form-select" name="Rol" id="aRol" required>
			        <?php
			         $consulta=$conn->query("SELECT * FROM rol"); 
			         while ($valores=mysqli_fetch_row($consulta))  
			          {
			            echo '<option value="'.$valores[0].'">'.$valores[1].'</option>';
			          }
			        ?>
			</select>  
		<label class="form-label" for="sentai">email</label>
		<input class="form-control" type="text" name="email" required="" id="aemail" >				  
		<label class="form-label" for="Telefono">Telefono</label>
		<input class="form-control" type="text" name="Telefono" required="" id="aTel" >				  
		
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="aAceptar">Aceptar</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal EDIT -->
<div class="modal" id="editModalPos" tabindex="-1" aria-labelledby="editModalsPos" aria-hidden="true">
  <div class="modal-md modal-dialog modal-dialog-centered modal-dialog-scrollable" >
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="editModalsPos">Modificar Usuario</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<input hidden="" class="form-control" type="text" name="Id" required="" id="eid"  >
		<label class="form-label" for="Usuario">Usuario</label>
		<input class="form-control" type="text" name="Usuario" required="" id="eUsu" >
		<label for="Rol">Rol</label>
			<select class="form-select" name="usuario" id="eRol" required>
			        <?php
			         $consulta=$conn->query("SELECT * FROM rol"); 
			         while ($valores=mysqli_fetch_row($consulta))  
			          {
			            echo '<option value="'.$valores[0].'">'.$valores[1].'</option>';
			          }
			        ?>
			</select>
		<label class="form-label" for="email">Email</label>
		<input class="form-control" type="text" name="email" required="" id="eemail" >	
		<label class="form-label" for="telefono">Telefono</label>
		<input class="form-control" type="text" name="telefono" required="" id="eTel" >			
		<label class="form-label" for="activo">Activo</label>
		<select class="form-select" name="eactivo" required="" id="eactivo">
						 	<option value="Si">Si</option>
							<option value="No">No</option>
						</select>		
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" id="EAceptar">Aceptar</button>
      </div>
    </div>
  </div>
</div>


			


<script type="text/javascript" >
	
	$(document).ready(function(){
	//	$('#editModalPos').modal({backdrop:'static'}); //para que no se cierre con click fuera
	//	$('#addModalPos').modal({backdrop:'static'}); //para que no se cierre con click fuera
		// $("#mytable").DataTable({
		// 	"pageLength": 25,
		// 	"order": [[1,'asc']],
		// 	"language":{
		// 		"url":"json/spanish.json"
		// 	},
		// 	ajax: {"url":"../ajax/ajax/load_user.php",
		// 		   "type": 'POST',
		// 		}
		
		// });
		$("#mytable").DataTable({ //tabla de la izquierda
					//respsive
			   order: [[1, 'asc'],
			    				[4,'desc']],
			    responsive: {
			        details: {
			            type: 'column',
			            target: 'tr'
			        }
			    }, // hast aqui

			searching:true,
			info:true,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2,3,4],
                className: 'dt-body-center',},
								{
			            className: 'dtr-control',
			            orderable: false,
			            target: 0
			        	}],
			"paging": true,
      		//"dom":'<lf<t>ip>',
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"../ajax/load_user.php",
				   "type": 'POST',
				}
			});
	});
// Accion de añadir
	$('#aAceptar').click(function(){
			// chequemos que parametros son obligatorios
			if ($('#aUsu').val().length<=0||$('#aRol').val().length<=0||$('#aTel').val().length<=0){
				// Swal.fire({
			  // 			title: 'Aviso',
			  // 			type: "error",
			  // 			text: 'Rellene los campos',
			  // 			confirmButtonText: 'Aceptar'
				// });
				return;
			}
			let parametros = {
			"Usuario": $('#aUsu').val(),
			"Rol": $('#aRol').val(),
			"Email": $('#aemail').val(),
			"Tel": $('#aTel').val(),
			"Activo": "Si",
		};
		$.ajax({
			url: '../ajax/add_user.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#mytable').DataTable().ajax.reload(null,false);
				/*Swal.fire({
			  			title: 'Aviso',
			  			type: "success",
			  			text: 'Chapilla cambiada con Exito',
			  			confirmButtonText: 'Aceptar'
				});*/
				 toastr.success("AÑADIDO CORRECTAMENTE", "");
				$('#aUsuario').modal('hide');
				// limpiamos
				$('.form-control').val("");
				$('.form-select').val("");
			}
		})
	});	


// Accion al editar
	$('#EAceptar').click(function(){
			let parametros = {
			"Id": $('#eid').val(),
			"Usuario": $('#eUsu').val(),
			"Rol": $('#eRol').val(),
			"Email": $('#eemail').val(),
			"Tel": $('#eTel').val(),
			"Activo": $('#eactivo').val(),
			
		};
		$.ajax({
			url: '../ajax/update_user.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#mytable').DataTable().ajax.reload(null,false);
				/*Swal.fire({
			  			title: 'Aviso',
			  			type: "success",
			  			text: 'Chapilla cambiada con Exito',
			  			confirmButtonText: 'Aceptar'
				});*/
				toastr.success("ACTUALIZADA CORRECTAMENTE...", "Evento ");
				$('#editModalPos').modal('hide');
			}
		})
	});	

// Obtener datos  filtardos por Id

	function GetDataUser(id) {
		 var parametros = {"id": id};
		 $.ajax({
			url: '../ajax/getDataUser.php',
			type:'POST',
			data: parametros,
			success : function(dato) {
				let datos=JSON.parse(dato);
				$('#eid').val(datos[0][0]);
				$('#eUsu').val(datos[0][1]);
				$('#eRol').val(datos[0][2]);
				$('#eemail').val(datos[0][3]);
				$('#eactivo').val(datos[0][4]);
				$('#eTel').val(datos[0][5]);
				
				
			}
			});
			}   
</script>

</body>
</html>