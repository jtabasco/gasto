<?php
	    // session_start();
	    // hacemos calculos
    // Eliminar los warning
		error_reporting(0);
		include "../con.php";	 
		//include "../include/script.php";
		//include "include/nav.php";
		//filtar todas las deudas del usuario

		$sql = mysqli_query($conn,"SELECT Deuda FROM deudas WHERE duedor='".$_SESSION['user']."'");
		$result = mysqli_fetch_array($sql);
		$debes = $result['Deuda'];

		//filtar que le deben al  usuario
		$sql = mysqli_query($conn,"SELECT deben FROM deben WHERE comprador=".$_SESSION['idUser']);
		$result = mysqli_fetch_array($sql);
		$tedeben = $result['deben'];
    if (is_null($tedeben)){
    	$tedeben=" 0.00"; 
    }
    if (is_null($debes)){
    	$debes=" 0.00"; 
    }

		
		

	?>		


<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
	
</head>

<body>
 	<div class="row mt-5 ms-3 me-3">
 	</div>	
 	<div class="row mt-5 ms-3 me-3">
	 <div class="col-sm-3 p-1 fw-bold">
      	¡Hola,  <?php echo $_SESSION['user'] ?>! 
		<button type="button" class="btn btn-success f-strong" data-bs-toggle="modal" data-bs-target="#preguntaModal" id="addAceptar">+</button>
		<button type="button" class="btn btn-info f-strong ms-3" data-bs-toggle="modal" data-bs-target="#verreporte" id="reporte">Reporte</button>
      </div>
     
  </div>  	
 

 	<div class="row mb-2 ms-3 me-3">
 			<div class="col-sm-3 mt-3 ">
        <div class="card shadow" >
          <div class="card-body">
          	<div class="card-title bg-primary fw-bold text-white shadow rounded p-1"  >Por Pagar  $<?php echo $debes ?></div>
            <!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
								<table class="table table-responsive table-bordered table-hover justify-content-center display nowrap" id="tabladuedasT" style="width:100%">
									<thead class="text-white" >
									  <th class=" dt-head-center" data-dt-order="disable">Comprador</th>
									  <th class=" dt-head-center">Total</th>
									</thead>
								</table>
          </div>
         </div>   
      </div>

      <div class="col-sm-9 mt-3 ">
        <div class="card shadow" >
          <div class="card-body">
          	<div class="card-title bg-primary  fw-bold text-white shadow rounded p-1"  >Detallado </div>
            <!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
								<table class="table table-responsive table-bordered table-hover justify-content-center display " id="tabladuedas"  style="width:100%">
									<thead class="text-white">
										<th class=" dt-head-center">comprador</th>
										<th class=" dt-head-center">Detalle</th>
										<th class=" dt-head-center">Importe</th>
										<th class=" dt-head-center">Fecha</th>
										<th class=" dt-head-center">Deuda</th>
									</thead>
								</table>
          </div>
         </div>   
      </div>
      
 	</div>
 	<div class="row mt-2 mb-5 ms-3 me-3">
 		  <div class="col-sm-3 mt-3 ">
        <div class="card shadow" >
          <div class="card-body">
          	<div id="tedeben" class="card-title bg-primary  fw-bold text-white shadow rounded p-1"  >Por Cobrar   $<?php echo $tedeben ?></div>
            <!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
								<table class="table table-responsive table-bordered table-hover justify-content-center display nowrap" id="tabladebenT"  style="width:100%">
									<thead class="text-white" >
									  <th class=" dt-head-center" data-dt-order="disable">Deudor</th>
									  <th class=" dt-head-center">Total</th>
									</thead>
								</table>
          </div>
         </div>   
      </div>

      <div class="col-sm-9 mt-3 ">
        <div class="card shadow" >
          <div class="card-body">
          	<div class="card-title bg-primary  fw-bold text-white shadow rounded p-1"  >Detallado</div>
            <!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
								<table class="table table-responsive display  table-bordered table-hover justify-content-center" id="tabladeben" style="width:100%">
									<thead class="text-white">
									 	<tr>
									 		<th rowspan="2" class="dt-head-center">Deudor</th>
										  <th rowspan="2" class="dt-head-center">Deuda</th>
									 		<th colspan="3" class=" dt-head-center" data-dt-order="disable">Datos de la compra</th>
									 	</tr>	
										  
										
											
											<th class=" dt-head-center">Detalle</th>
											<th class=" dt-head-center">Importe</th>
											<th class=" dt-head-center">Fecha</th>
											
											
									</thead>
								</table>
          </div>
         </div>   
      </div>
      
 	</div>
 	<div class="row mb-5 ms-3 me-3">
 	</div>
<!-- Modal de Pregunta -->
<div class="modal fade" id="preguntaModal" tabindex="-1" role="dialog" aria-labelledby="preguntaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="dialog"> <!-- Corregido el atributo role -->
        <div class="modal-content">
            <div class="modal-header justify-content-center">
                <h5 class="modal-title" id="preguntaModalLabel">¿En que utilzaste tu dinero?</h5> <!-- Corregido "que" a "Qué" -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center"> <!-- Agregado 'text-center' para centrar -->
                <button type="button" class="btn btn-success btn-block" id="respuestaNo">Préstamo</button> <!-- Corregido "Prestamo" a "Préstamo" -->
                <button type="button" class="btn btn-danger btn-block" id="respuestaSi">Compra</button>
            </div>
        </div>
    </div>
</div>
     <!-- Reporte gasto x mes y ano -->
<div class="modal shadow" id="verreporte" tabindex="-1" aria-labelledby="averreporte" aria-hidden="true">
  <div class="modal-mb modal-dialog modal-dialog-centered modal-dialog-scrollable" >
    <div class="modal-content">
      <div class="modal-header" ><h3>Reporte de compra</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<div class="card-title text-center bg-primary  text-white shadow rounded p-1">
						<label>Año:</label>
						<select id="ano" name="selectano">
						  <option value=2024>2024</option>
						  <option value=2025>2025</option>
						  <option value=2026>2026</option>
						  <option value=2026>2027</option>
						  <option value=2026>2028</option>
						</select>
						<span></span>
						<label>Mes:</label>
						<select id="mes" name="selectmes">
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

      	<div class="row">
      		<div class="col-12 text-center fw-bold bg-transparent  text-black shadow rounded p-1">
      			<label>Total:</label>
      			<label id="imptotal">Importe</label>
      		</div>
      	</div>
      		<div class="row">
      			<div class="col-12">
							<table class="table table-responsive table-bordered table-hover" id="mytableXanoXmes" style="width:100%"  style="background-color:  #f7d0b7 ;" >
								<thead class="text-white" style="background-color:  #E60616 ;">
									<th>Comprador</th>
									<th>Importe</th>
									
								</thead>
							</table>	
						</div>
      			
      		</div>
      		<div class="row">
      			<div class="col-12">
					<table class="table table-responsive table-bordered table-hover" id="mytableXanoXmesXcategoria" style="width:100%"  style="background-color:  #f7d0b7 ;" >
						<thead class="text-white" style="background-color:  #E60616 ;">
							<th>Categoria</th>
							<th>Importe</th>
							<th>%</th>
						</thead>
					</table>	
				</div>
      		</div>
			<div class="row">
      		<div class="col-12 text-center fw-bold bg-transparent  text-black shadow rounded p-1">
      			<label>Pagado:</label>
      			<label id="pagtotal">ImportePagado</label>
      		</div>
      	</div>
			  <div class="row">
      			<div class="col-12">
					<table class="table table-responsive table-bordered table-hover" id="mytableXanoXmesXcategoriaXactivo" style="width:100%"  style="background-color:  #f7d0b7 ;" >
						<thead class="text-white" style="background-color:  #E60616 ;">
							<th>Categoria</th>
							<th>Importe</th>
							<th>%</th>
						</thead>
					</table>	
				</div>
      		</div>
      	</div>
      </div>
    </div>
  </div>
 </div>
</div>

     <!-- Entrada de gasto -->
<div class="modal shadow" id="compra" tabindex="-1" aria-labelledby="aGasto" aria-hidden="true">
  <div class="modal-mb modal-dialog modal-dialog-centered modal-dialog-scrollable" >
    <div class="modal-content">
      <div class="modal-header" ><h3>Compra pagada por <?php echo $_SESSION['user'] ?></h3>
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
							<input class="form-control" type="text" name="detalle" required="" id="aDetalle"  >
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
							<input class="form-control" type="number" name="monto" required="" id="aMonto"  >
					</div>
					<div class="col-6">
							<label class="form-label" for="fecha">Fecha*</label>
							<input class="form-control" type="date"  name="fecha" required="" id="aFecha">
					</div>
					<div class="col-12">		
							<label class="form-label" for="ubi">Deudor</label>
							<select name="Ubi" id="adeudor" class="form-select" required="">
									<option selected></option>
									<option value='0'>Todos</option>
									
							</select>

					</div>
					
					<!-- <div class="col-3">
							<button type="button" class="btn btn-primary my-4 p-2" id="Anadir">+</button>
						</div> -->

					<div class="row">
						<div class="col-12">
							<table class="table table-responsive table-bordered table-hover" id="mytableg" style="width:100%"  style="background-color:  #f7d0b7 ;" >
								<thead class="text-white" style="background-color:  #E60616 ;">
									<th>Deudor</th>
									<th></th>
									
								</thead>
							</table>	
						</div>
					</div>
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
  <div class="modal-mb modal-dialog modal-dialog-centered modal-dialog-scrollable" >
    <div class="modal-content">
      <div class="modal-header">
      	<input class="form-control modal-title fs-5 bg-transparent border-0" type="text" name="deudor" disabled  id="edeuda" >
        <!-- <h1 class="modal-title fs-5" id="ActualizasPago">Actualiza Pago <input id='edeuda'></h1> -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      	<div class="row">
					<div class="col-12 text-wrap h-auto">
						<label class="form-label" for="detalle">Compra de: </label>
						<TEXTAREA class="form-control bg-transparent border-0" name="obs" disabled rows="3" id="edetalle"></TEXTAREA>
						<input class="form-control bg-transparent border-0 text-wrap" type="text" name="detalle" disabled  id="edetalle" >
						<input class="form-control"  name="id" type="hidden"   id="eid"  >
					</div>
				</div>	
				<div class="row border-top border-primary mt-3 ">
					<div class="col-8">
						<input class="form-control bg-transparent border-0 text-end" type="text" name="deudor" disabled  id="edeudor" >
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






<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.4/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.4/dist/sweetalert2.min.css">

<script type="text/javascript" >

/////////////////////////

$(document).ready(function(){
		$('#compra').modal({backdrop:'static'}); //para que no se cierre con click fuera
		$('#ActualizaPago').modal({backdrop:'static'}); //para que no se cierre con click fuera
			toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
			 toastr.success("!!!BIENVENIDO !!!", "");
			 $('#respuestaSi').click(function() {
           		$('#preguntaModal').modal('hide');
           		$('#compra').modal('show');
        	}); 
			$('#respuestaNo').click(function() {
           		$('#preguntaModal').modal('hide');
           		$('#prestamo').modal('show');
        	}); 
			$.ajax({url: 'filtro_usuarios.php',type:'POST',
			   			   success: function(resp){
			   			   	//actualizo el ontrol
			   			   	$('#adeudor option').remove();
			   			   	$('#adeudor').append(resp);
			   			   }
			 					});
			$.ajax({url: 'filtro_plan.php',type:'POST',
			   			   success: function(resp){
			   			   	//actualizo el ontrol
			   			   	$('#aplan option').remove();
			   			   	$('#aplan').append(resp);
			   			   }
			 					});
			$.ajax({url: 'categoria.php',type:'POST',
			   			   success: function(resp){
			   			   	//actualizo el ontrol
			   			   	$('#acat option').remove();
			   			   	$('#acat').append(resp);
			   			   }
			 					});




		let fecha = new Date();
		$("#ano").val(fecha.getFullYear());
		$("#mes").val(fecha.getMonth()+1);
        let ano = $('#ano').val();
        let mes = $('#mes').val();
        let parametros = {"ano": ano,
        				  "mes": mes};
     // aquii tenemos para filtarpor ano y mes   				  
       $.ajax({url: 'totalmesano.php',type:'POST',data: parametros,
			   			   success: function(resp){
					   			   	//actualizo el control
					   			   	$('#imptotal').text(resp);
				   			   }
			 					});
		$.ajax({url: 'totalmesanopagado.php',type:'POST',data: parametros,
			   			   success: function(resp){
					   			   	//actualizo el control
					   			   	$('#pagtotal').text(resp);
				   			   }
			 					});
								

$("#mytableXanoXmes").DataTable({ //tabla de la izquierda
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
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			ajax: {"url":"suma_gastosXanoXmes.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});

$("#mytableXanoXmesXcategoria").DataTable({ //tabla de la izquierda
					//respsive
			    order: [1, 'desc'],
			    responsive: {
			        details: {
			            type: 'column',
			            target: 'tr'
			        }
			    }, // hast aqui

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			ajax: {"url":"suma_gastosXanoXmesXcategoria.php",
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

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2],
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
			ajax: {"url":"suma_gastosXanoXmesXcategoriaXactivo.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});				



			$("#tabladuedas").DataTable({ //tabla de la izquierda
			

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
			});
			$("#tabladuedasT").DataTable({ //tabla de la izquierda
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
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			ajax: {"url":"por_pagart.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});
			$("#tabladeben").DataTable({ //
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
			});
			$("#tabladebenT").DataTable({ //tabla de la izquierda
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
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			stateSave: false,
			ajax: {"url":"por_cobrart.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});
		//rellenamos mytableg
		$("#mytableg").DataTable({ //tabla de la izquierda
			searching:false,
				info:false,
			order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],className: 'dt-body-center',},
                			//{targets: 1,render: DataTable.render.datetime('DD-MM-YYYY'),},
										  ],
	    "paging": false,
      		"dom":'<lf<t>ip>',
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: true,
			ajax: {"url":"load_detallesm.php",
				   "type": 'POST'
				}
			});

		});

// Boton +
	$('#addAceptar').click(function(){
		
				$('#mytableg').DataTable().ajax.reload(null,false);
				//borramos tdatalle_gastos
				$.ajax({url: 'del_temp.php',type:'POST'});
				$('#mytableg').DataTable().ajax.reload(null,false);
				$.ajax({url: 'filtro_usuarios.php',type:'POST',
			   			   success: function(resp){
			   			   	//actualizo el ontrol
			   			   	$('#adeudor option').remove();
			   			   	$('#adeudor').append(resp);
			   			   }
			 					});
				
	});	
// boton reporte
	$('#reporte').click(function(){
		let fecha = new Date();
		$("#ano").val(fecha.getFullYear());
		$("#mes").val(fecha.getMonth()+1);
        let ano = $('#ano').val();
        let mes = $('#mes').val();
        let parametros = {"ano": ano,
        				  "mes": mes};
     // aquii tenemos para filtarpor ano y mes   				  
       $.ajax({url: 'totalmesano.php',type:'POST',data: parametros,
			   			   success: function(resp){
					   			   	//actualizo el control
					   			   	$('#imptotal').text(resp);
				   			   }
			 					});
		$.ajax({url: 'totalmesanopagado.php',type:'POST',data: parametros,
			   			   success: function(resp){
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

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			ajax: {"url":"suma_gastosXanoXmes.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});
$("#mytableXanoXmesXcategoria").DataTable({ //tabla de la izquierda
					//respsive
			    order: [1, 'desc'],
			    responsive: {
			        details: {
			            type: 'column',
			            target: 'tr'
			        }
			    }, // hast aqui

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2],
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
			ajax: {"url":"suma_gastosXanoXmesXcategoria.php",
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

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2],
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
			ajax: {"url":"suma_gastosXanoXmesXcategoriaXactivo.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});			
	});
// Boton aceptar
	$('#Aceptarm').click(function(){
			// chequemos que parametros son obligatorios
			if ($('#aDetalle').val().length<=0||$('#aMonto').val().length<=0||$('#aFecha').val().length<=0||$('#acat').val()==0){
					toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 toastr.error(" COMPLETE DATOS OBLIGATORIOS", "");
				 

				return;
			}
			///////// preguntar si no tiene deudores///
			 $.ajax({url: 'hay_dudores.php',type:'POST',
			   			   success: function(resp){
			   			   		if(resp=='ok'){ // hat deudores
			   			   			$('#Aceptarm').hide();
							        $('#spin1').removeAttr('hidden');
							        $('#spin1').show();
			   			   			let parametros = {
													"Detalle": $('#aDetalle').val(),
													"Monto": $('#aMonto').val(),
													"Cat": $('#acat').val(),
													"Fecha": $('#aFecha').val()
											
												};
												$.ajax({
													url: 'add_Gasto.php',
													type:'POST',
													data: parametros,
													success: function(resp){
														$('#tabladuedas').DataTable().ajax.reload(null,false);
														$('#tabladuedasT').DataTable().ajax.reload(null,false);
														$('#tabladeben').DataTable().ajax.reload(null,false);
														$('#tabladebenT').DataTable().ajax.reload(null,false);
														$('#mytableg').DataTable().ajax.reload(null,false);
														$('#compra').modal('hide');
														$('#Aceptarm').show();
										        $('#spin1').hide();
														// limpiamos
														$('.form-control').val("");
														$('.form-select').val("");
														/*Swal.fire({
													  			title: 'Aviso',
													  			type: "success",
													  			text: 'Chapilla cambiada con Exito',
													  			confirmButtonText: 'Aceptar'
														});*/
														toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
														 toastr.success("!!!GASTO REGISTRADO!!! ","");
														 // actualizamos el subtotal de por cobrar
														 $.ajax({url: 'subtotalPorCobrar.php',type:'POST',
			   													   success: function(resp){
																   			   	//actualizo el control
																   			   	$('#tedeben').text(resp);
													   			   }
													 					});
														 //////////
														 

														
													}
												})
			   			   		}else{
			   			   			toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 							toastr.error("Complete los deudores..", "");
											return;
			   			   		};

			   			   }
			 					});



			
	});	

//


// Accion de añadir deudores
	$('#adeudor').on('change', function() {
	//$('#Anadir').click(function(){
			// chequemos que parametros son obligatorios
			if ($('#adeudor').val().length<=0){
				 toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 toastr.error("COMPLETE DATOS", "");
				

				return;
			}
			let parametros = {
												"deudor": $('#adeudor').val(),	
												"pago": 'No',
												
											  };
		
		$.ajax({
			url: 'adddetallem.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#mytableg').DataTable().ajax.reload(null,false);
				
				//actuaizamos lista de usuarios
			   $.ajax({url: 'filtro_usuarios.php',type:'POST',
			   			   success: function(resp){
			   			   	//actualizo el ontrol
			   			   	$("#adeudor option").remove();
			   			   	$("#adeudor").append(resp);
			   			   }
			 					});
				 // toastr.success("AÑADIDO CORRECTAMENTE DETALLE", "CLIENTE "+$('#clientem').val());
				//$('#addModalPosd').modal('hide');
				// limpiamos
				$('#clientem').val("");
				//$('.form-select').val("");
			}
		});
	});	

// Anadirplantilla
	$('#aPlantilla').click(function(){
			// chequemos que parametros son obligatorios
			if ($('#aDetalle').val().length<=0||$('#aMonto').val().length<=0||$('#acat').val().length<=0){
					toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 toastr.error("COMPLETE DATOS OBLIGATORIOS", "Para añadir plantilla");
				 

				return;
			}
			///////// preguntar si no tiene deudores///
			 $.ajax({url: 'hay_dudores.php',type:'POST',
			   			   success: function(resp){
			   			   		if(resp=='ok'){ // hat deudores
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
													type:'POST',
													data: parametros,
													success: function(resp){
														// $('#tabladuedas').DataTable().ajax.reload(null,false);
														// $('#tabladuedasT').DataTable().ajax.reload(null,false);
														// $('#tabladeben').DataTable().ajax.reload(null,false);
														// $('#tabladebenT').DataTable().ajax.reload(null,false);
														 $('#mytableg').DataTable().ajax.reload(null,false);
														//$('#aGasto').modal('hide');
														$('#Aceptarm').show();
										        $('#spin1').hide();
														// limpiamos
														$('.form-control').val("");
														$('.form-select').val("");
														/// REFRESCAMOS EL SELECT DE PLANTILLAS
														 	$.ajax({url: 'filtro_plan.php',type:'POST',
														   			   success: function(resp){
														   			   	//actualizo el ontrol
														   			   	$('#aplan option').remove();
														   			   	$('#aplan').append(resp);
														   			   }
														 					});

														toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
														toastr.success("!!!PLANTILLA AGREGADA!!! ","");
														}
												})
			   			   		}else{
			   			   			toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 							toastr.error("COMPLETE DEUDORES..", "");
											return;
			   			   		};

			   			   }
			 					});



			
	});	

// Anadirplantilla
	$('#dPlantilla').click(function(){
			// chequemos que parametros son obligatorios
			$idplan=$('#aplan').val();
			if ($('#aplan').val()==0){
					toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 toastr.error("Debe haber un plan seleccionado", "");
				 

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
									type:'POST',
									data: parametros,
									success: function(resp){
										
										/// REFRESCAMOS EL SELECT DE PLANTILLAS
										 	$.ajax({url: 'filtro_plan.php',type:'POST',
										   			   success: function(resp){
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
			if ($('#aplan').val()==0){
				 toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				 toastr.error("Seleccione o Agrage planificados", "");
				

				return;
			}
			let parametros = {
												"plan": $('#aplan').val(),	
											  };
		
		$.ajax({
			url: 'addplan_gasto.php',
			type:'POST',
			data: parametros,
			success : function(respta) {
				let pln=JSON.parse(respta);
				$('#aDetalle').val(pln[0][0]);
				$('#aMonto').val(pln[0][1]);
				$('#aFecha').val(pln[0][2]);
				$('#acat').val(pln[0][3]);

				$('#mytableg').DataTable().ajax.reload(null,false);
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
  let parametros = {"ano": nano,
        				  "mes": nmes};
   $.ajax({url: 'totalmesano.php',type:'POST',data: parametros,
			   			   success: function(resp){
					   			   	//actualizo el control
					   			   	$('#imptotal').text(resp);
				   			   }
			 					});
	$.ajax({url: 'totalmesanopagado.php',type:'POST',data: parametros,
			   			   success: function(resp){
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

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			ajax: {"url":"suma_gastosXanoXmes.php",
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

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1],
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
			ajax: {"url":"suma_gastosXanoXmesXcategoria.php",
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

			searching:false,
			info:false,
			//order: [[0,'asc']],
     		 columnDefs: [{targets: [0,1,2],
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
			ajax: {"url":"suma_gastosXanoXmesXcategoriaXactivo.php",
				   "type": 'POST',
				   "data": parametros,
				}
			});				

}); 





/////////////////////////







	function DelDeudor(bus) {
		var id= bus;
		 var parametros = {"id": id};
		 $.ajax({
			url: 'DelDeudor.php',
			type:'POST',
			data: parametros,
			success : function(dato) {
				$('#mytableg').DataTable().ajax.reload(null,false);
				//actualizo la lista de deudores
						$.ajax({url: 'filtro_usuarios.php',type:'POST',
			   			   success: function(resp){
			   			   	//actualizo el ontrol
			   			   	$('#adeudor option').remove();
			   			   	$('#adeudor').append(resp);
			   			   }
			 					});
			}
			});
			} 
		

	// Obtener datos  filtardos por id 

	function GetDataGasto(bus) {
		var id= bus;
		 var parametros = {"id": id};
		 $.ajax({
			url: 'getDataGasto.php',
			type:'POST',
			data: parametros,
			success : function(dato) {
				let datos=JSON.parse(dato);
				$('#eid').val(datos[0][0]);
			//	$('#ecomprador').val(datos[0][1]);
				$('#edetalle').val(datos[0][2]+" con importe de $"+datos[0][3]+" el dia "+datos[0][4]);
				$('#emonto').val(datos[0][3]);
				$('#efecha').val(datos[0][4]);
				$('#edeudor').val("debe $"+datos[0][7]+" Pago?");
				$('#epago').val(datos[0][6]);
				$('#edeuda').val("Actualiza el Pago de "+datos[0][5]);
			}
			});
			} 
	
// Accion al editar
	$('#EAceptar').click(function(){
		  $('#EAceptar').hide();
        $('#spin0').removeAttr('hidden');
        $('#spin0').show();
			let parametros = {
			"id": $('#eid').val(),
			"pago": $('#epago').val()	
		};
		$.ajax({
			url: 'updatepago.php',
			type:'POST',
			data: parametros,
			success: function(resp){
				$('#tabladeben').DataTable().ajax.reload(null,false);
				$('#tabladebenT').DataTable().ajax.reload(null,false);
				/*Swal.fire({
			  			title: 'Aviso',
			  			type: "success",
			  			text: 'Chapilla cambiada con Exito',
			  			confirmButtonText: 'Aceptar'
				});*/
				toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				toastr.success("!!!PAGO ACTUALIZADO...!!!!", "");
				// actualizamos el subtotal de por cobrar
				 $.ajax({url: 'subtotalPorCobrar.php',type:'POST',
								   success: function(resp){
						   			   	//actualizo el control
						   			   	$('#tedeben').text(resp);
			   			   }
			 					});
				$('#ActualizaPago').modal('hide');
				$('#EAceptar').show();
        $('#spin0').hide();
			}
		})
	});	



function Actualiza_tablas(){
	$('#tabladuedas').DataTable().ajax.reload(null,false);
	$('#tabladuedasT').DataTable().ajax.reload(null,false);
	$('#tabladeben').DataTable().ajax.reload(null,false);
	$('#tabladebenT').DataTable().ajax.reload(null,false);
	$('#mytableg').DataTable().ajax.reload(null,false);
		
	}			


</script>
</body>

</html>