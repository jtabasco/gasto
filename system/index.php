
<?php 
  session_start();
  
  if(empty($_SESSION['active'])){
    header('location: ../');
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<title>GASTOS</title> 
  
</head>
<body  >
	<?php include "../include/nav.php";?>
  <?php include "../include/footer.php"; ?>
  <?php  include "resumenGastosP.php"; ?>
   
  
 
    
</body>
</html>

