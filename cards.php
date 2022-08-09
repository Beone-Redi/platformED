	<?php
// Cargo la base de datos.
include "include/coredata.php";
$app = new app();
if($_GET['id']=='TODOS')
{
    $cards=$app->viewCards();
}
else
{
    $cards=$app->viewCardsByCompany($_GET["id"]);
}

?>
<?php
for ($i = 0; $i < sizeof($cards); $i+=2) 
{
?>
    <option value="<?php echo $cards[$i]; ?>"><?php echo strtoupper(utf8_encode($cards[ $i + 1 ])). '    ***-****-' . substr($cards[$i],0,4) . '-' . substr($cards[$i],-4);; ?>
    </option>
    <?php   
}
