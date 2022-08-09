<?php
include "include/coredata.php";
$app = new app();


$idfactura=$_POST['idfactura'];
$card=$_POST['card'];
$response=1;
$url=$app->get_FacturabyAuthCode($idfactura);
if($url==='')
{
	if(isset($_FILES['file']['name'])) // si se carga el archivo 
	{
		$filename=$_FILES['file']['name'];
	// Location
		$location = 'facturas/'.$idfactura.'.pdf';
		$extension = strtolower(substr($filename, -4));
		if($extension==='.pdf')
		{
			// Upload file
			if(move_uploaded_file($_FILES['file']['tmp_name'],$location))
			{
				$data=[$card,$idfactura,$location,$_SESSION['USER']];
				$url=$app->new_Factura($data);
				
				//$response = $location;
				$response="Archivo subido correctamente";
			}
			else
			{
				$response="no se subio el archivo";
			}
		}
		else
		{
			$response="extension no valida";
		}
	}
	else
	{
		$response="No se cargo el archivo";
	}
}
else
{
	$response="Factura cargada anteriormente";
}
//$response=$_POST['idfactura'];
echo $response;
