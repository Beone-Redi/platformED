	<?php
// Cargo la base de datos.
include "include/coredata.php";
$app = new app();

//cambiar por ViewCompany
$data_company=$app->viewCompany($_GET['id']);//obtiene los datos de la empresa
echo $data_company[15];
