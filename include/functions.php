<?php 
	date_default_timezone_set('America/Guatemala'); 

////////////////////////////////////////////////////
// Convierte fecha de mysql a español
////////////////////////////////////////////////////
function ConvFecha($fecha){
	if ($fecha<>"") {
    preg_match( '/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/', $fecha, $mifecha);
    $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
} else $lafecha="";
    return $lafecha;
}


////////////////////////////////////////////////////
// retorne fecha en castellano 
////////////////////////////////////////////////////


	function fechaC(){
		$mes = array("","Enero", 
					  "Febrero", 
					  "Marzo", 
					  "Abril", 
					  "Mayo", 
					  "Junio", 
					  "Julio", 
					  "Agosto", 
					  "Septiembre", 
					  "Octubre", 
					  "Noviembre", 
					  "Diciembre");
		return date('d')." de ". $mes[date('n')] . " de " . date('Y');
	}

function dias($d){
   $dia=array("dom","Lun","Mar","Mie","Jue","Vie","Sab","dom");
   return $d." ".$dia[date('N',strtotime($d))];


}

	function IniFin($TotalP,$pag) {
		switch($pag){
	    case (($pag >= 1) && ($pag <= 3)):
	    	$ini=1;
	    	if ($TotalP<=5){
	    		$fin=$TotalP;
	    	}else{
	    		$fin=5;
	    	}
	        return [$ini, $fin];
	    break;

	    case ($pag >=$TotalP-2):
	        $ini=$TotalP-4;
	    	$fin=$TotalP;
	        return [$ini, $fin];
	    break;

	    case (($pag > 3) && ($pag < $TotalP-2 )):
	    	$ini=$pag-2;
	    	$fin=$pag+2;
	        return [$ini, $fin];
	    break;

	    default:
		}    
	}


	
?>







