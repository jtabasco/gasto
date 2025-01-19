<?php 
	session_start();
	  if(empty($_SESSION['active']) ){
		    header('location: ../');
  	  }
		
	include "../con.php";	
 ?>
<?php // include "../logr.php";?>
<?php// escribelog($conection,$_SESSION['user'],"Listado de Usuarios"); ?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Deudas</title>
	

	
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
	        	Deudas 
	        	</div>
	          <div class="card-body">
	          	<!-- <div class="card-title bg-primary  fw-bold text-white shadow rounded p-1"  >Detallado </div> -->
	            <!-- CREAR UNA TABLA PARA MOSTRAR LOS DATOS -->
									<table class="table table-responsive table-bordered table-hover justify-content-center display " id="mytable"  style="width:100%">
										<thead>
											<th class=" dt-head-center bg-primary text-white">Deudor</th>
											<th class=" dt-head-center bg-primary text-white">T/Comp.</th>
											<th class=" dt-head-center bg-primary text-white">Debe</th>
										</thead>
									</table>
	          </div>
	         </div>
	      </div>    

	<div class="col-md-2"></div>
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
			   // order: [[1, 'asc'],
			   //  				[4,'desc']],
			   //  responsive: {
			   //      details: {
			   //          type: 'column',
			   //          target: 'tr'
			   //      }
			   //  }, // hast aqui

			ordering:false,
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
      		//"dom":'<lf<t>ip>',
			"language":{
				"url":"json/spanish.json"
			},
			stateSave: false,
			ajax: {"url":"../ajax/load_deudas.php",
				   "type": 'POST',
				}
			});
	});





// Obtener datos  filtardos por Id

	function enviarcorreo(id,debe,dias) {
		 var parametros = {"id": id,"debe":debe,"dias":dias};
		 $.ajax({
			url: 'enviacorreo.php',
			type:'POST',
			data: parametros,
			success : function(dato) {
				toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
				if (dato==="true"){
			 			toastr.success("!!!SMS ENVIADO !!!", "");
				}else{
						toastr.error("!!!SMS NO ENVIADO !!!", "");
				}
				
				
			}
			});
			}   
</script>

</body>
</html>