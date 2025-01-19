<?php 
if(isset($_GET['email'] )){
    $email = $_GET['email'] ;
   
}else{
    header('Location: ../index.html');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifiar Cuenta</title>
    <!-- CSS only -->
    <?php include "../include/script.php"; ?>
<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous"> -->
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-md-center">
            <div class="col-md-6">
                <h2 class="mb-4">Verificar Cuenta</h2>
                    <form action="verificartoken.php" method="POST">
                
                
                        <label for="c" class="form-label">Código de Verificación</label>
                        <input type="number" class="form-control" id="c" name="codigo">
                        <input type="hidden" class="form-control" id="email" name="email" value="<?php echo $email;?>">
               
                        <button type="submit" class="btn btn-primary">Verificar</button>
                    </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JavaScript Bundle with Popper -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script> -->
    <script type="text/javascript">
        toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
        toastr.success("Verifica tu email para restablecer tu contraseña", "");
    </script>
</body>
</html>