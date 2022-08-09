<?php
include "include/coredata.php";
$app = new app();
//$idfactura=$_POST['idfactura'];
$idfactura=$_GET['id'];
// file name
$url=$app->get_FacturabyAuthCode($idfactura);

if(unlink($url))
{
	if($app->delete_factura($idfactura))			//$response = $location;
	{
		$response="Registro eliminado correctamente";
	}
	else			//$response = $location;
	{
		$response="No se logro eliminar el registro";
	}
}
else
{
	$response="No se logro eliminar el archivo";
}
//$response=$_POST['idfactura'];
echo $response;
