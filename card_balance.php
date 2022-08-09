<?php
// Cargo la base de datos.
include "include/coredata.php";
$app = new app();
$Saldo_tarjeta=$app->check_amountCard($_GET['id']);//obtiene los datos de la tarjeta
if(substr($Saldo_tarjeta, 0, 2) <> '00')
{
    $Saldo_tarjeta=$app->check_amountCard($_GET['id']);//obtiene los datos de la tarjeta
}
echo number_format($Saldo_tarjeta,2,'.',',');

?>