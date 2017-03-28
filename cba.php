<?php
header('Content-Type: application/json');
include("apay/pay.api.php");
include("apay/iso4217.php");

if(isset($_REQUEST["ISO"]) || isset($_REQUEST["iso"]))
{	
	$iso = (isset($_REQUEST["ISO"])) ? strtoupper(htmlentities($_REQUEST["ISO"])) : strtoupper(htmlentities($_REQUEST["iso"]));
	if(in_array($iso,$iso4217))
	{
		echo json_encode(xe::cbaXE($iso));
	}else{
		echo "{\"error\":\"TRUE\",\"message\":\"NO SUCH ISO CODE\"}";
	}
}else{
	echo "USAGE Methods(GET,POST) Example:cba.php?ISO=USD"; 
}

?>