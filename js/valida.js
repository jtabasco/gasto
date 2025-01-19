$("#frmAcceso").on('submit', function(e)
{
  e.preventDefault();
  logina=$("#username").val();
  clavea=$("#password").val();

  $.post("ajax/login.php",
        {"logina":logina, "clavea":clavea},
        function(data)
        {
          
           // if (data!="null")
          if (data=="¡Bienvenido!")
            {
             
              $(location).attr("href","/gasto/system/index.php");
              //alert("bienvenido");
            }else{
              toastr.options = {"positionClass": "toast-bottom-right","preventDuplicates": true};
              toastr.error("Usuario y/o Password incorrectos", "");
            //  toastr.options.positionClass = toast-bottom-right;
              //alert("Usuario y/o Password incorrectos");
              
            }
        });
})


