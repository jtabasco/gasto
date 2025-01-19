<?php 
    include "../con.php";   
    $email =$_POST['email'];
   // $token =$_POST['token'];
    $codigo =$_POST['codigo'];
    $res=$conn->query("select * from restablecer where 
        email='$email' and codigo=$codigo")or die($conexion->error);
    $correcto=false;
    if(mysqli_num_rows($res) > 0){
        $fila = mysqli_fetch_row($res);
        $fecha =$fila[3];
        $fecha_actual=date("Y-m-d h:m:s");
        $seconds = strtotime($fecha_actual) - strtotime($fecha);
        $minutos=$seconds / 60;
        $correcto=true;
        if($minutos > 1 ){
           
            $correcto=false;
        }else{
            
        }
        
    }else{
        $correcto=false;
    }
   
   

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar password </title>
    <!-- CSS only -->
    <?php include "../include/script.php"; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous"> -->
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <h2 class="mb-4">Restablecer Password</h2>
            <?php if($correcto){ ?>
                <form >
                    <div class="form-group">
                        <label for="np" class="form-label">Nuevo Password</label>
                        <input type="password" class="form-control" id="np" name="p1"> 
                    </div>
                    <div class="form-group">
                        <label for="cp" class="form-label">Confirmar Password</label>
                        <input type="password" class="form-control" id="cp" name="p2">
                        <input type="hidden" class="form-control" id="email" name="email" value="<?php echo $email?>">

                    </div>
                    <button type="button" id="camb" class="btn btn-primary">Cambiar</button> 
                    
                </form>
            <?php }else{ ?>
                <div class="alert alert-danger" >Código incorrecto o vencido</div>
            <?php } ?>

             </div>
        </div>
    </div>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.4/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.4/dist/sweetalert2.min.css">
       <script>
      
      $('#camb').click(function(){
        if ($('#np').val().length<=0||$('#cp').val().length<=0){
          toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
          toastr.error(" Rellene toos los campos", "");
         return;
        };
        if ($('#np').val()!=$('#cp').val()){
          toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
          toastr.error("No Coinciden", "");
         return;
        };
        // sequimos
        //email=$('#email').val();
        let parametros = {"p1": $('#np').val(),
                           "email": $('#email').val()};
        $.ajax({
                url: 'cambiarpassword.php',
                type:'POST',
                data: parametros,
                success: function(resp){
                  if (resp=='ok'){
                   Swal.fire({
                          title: "Contraseña cambiada con exito",
                          html: '<a href="https://jtabasco.com/gasto">Iniciar Sección</a>',
                          allowOutsideClick: false,
                          showConfirmButton: false,
                          icon: "success"
                        });
                   // $(location).attr("href","/gasto");
                    

                    };
                   
                  }
              });
      }); 
   

    </script>
    
</body>
</html>