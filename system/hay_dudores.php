<?php 
    include "../con.php";   
    
    
    // revisamos si la tabla timem registros
    $res=$conn->query("SELECT * FROM tdetalle_gasto");
    
    if(mysqli_num_rows($res) > 0){// correo escrito bien
         echo 'ok';
        }else{
         echo 'Nook';   
        };
   


?>