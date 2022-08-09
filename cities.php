	<?php
// Cargo la base de datos.
include "include/coredata.php";
$app = new app();
$ciudades = $app->GetCitiesByState(utf8_decode($_GET["id"]));
?>
<option value="">OPCIONES</option>
<?php
for ($i = 0; $i < sizeof($ciudades); $i++) 
{
?>
    <option value="<?php echo (mb_strtoupper($ciudades[$i],'UTF-8')); ?>"><?php echo (mb_strtoupper($ciudades[$i],'UTF-8')); ?></option>
    <?php   
}
