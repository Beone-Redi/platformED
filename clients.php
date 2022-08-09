<?php

// Inicio la variables de sesion.
if (!isset($_SESSION)) 
{
    session_start();
}

if (isset($_SESSION['ACTIVO']) <> "2019") 
{
    header("Location: login");
}

include "include/coredata.php";
$app = new app();
$urlToken   = $app->urlToken();

$value_CH=$name_CH='';
$dUsuariosI=[];
if ( isset( $_GET['urlToken'] ) && isset( $_SESSION['urlToken'] ) && isset( $_GET['idu'] ) && isset( $_GET['sta'] ) )
{
    if ( $_SESSION['urlToken'] === $_GET['urlToken'] )
    {
        $app->updateStatusUser( $_GET['idu'], $_GET['sta'] );
        $_SESSION['urlToken'] = $urlToken;
    }
    else 
    {
        $_SESSION['urlToken'] = $urlToken;
    }
}
elseif(isset(($_POST['inputPerfil'])))
{
    if(strpos($_POST['inputPerfil'],'-')!==FALSE)
    {
        $ids=explode("-",$_POST['inputPerfil']); //[0,1] 0 es id de usuario, 1 es id de tarjeta
        $dUsuariosI=$app->GetDataCHByCard($ids[1]);    //
        $name_CH=strtoupper(utf8_encode($dUsuariosI[1])). '    ****-****-' . substr($dUsuariosI[3],0,4) . '-' . substr($dUsuariosI[3],-4);
        
    }
    else
    {
        $dUsuariosI=$app->GetDataCH($_POST['inputPerfil']);
        $name_CH=strtoupper(utf8_encode($dUsuariosI[1]));     
    }
    $value_CH=$_POST['inputPerfil'];
}
else 
{
    $_SESSION['urlToken'] = $urlToken;
}
$Empleados=$app->viewCardHolders($_SESSION['EMPRESA']);

include 'header.php';
?>

<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <!-- <h4 class="title">Administración de tarjetahabientes</h4> -->
                                <h4 class="title">Cardholder management</h4>
                                <!-- <p class="category">Vista general de los tarjetahabientes activos e inactivos de la empresa.</p> -->
                                <p class="category">Overview of the company's active and inactive cardholders.</p>
                                <p class="category">
                                    </br>
                                    <?php if ( $app->optSearch($permisos,'opt26')) { // opt de crear Empresas?>
                            
                                    <a href="create_user?scr=9" class="btn btn-default btn-sm" >
                                        <i class="pe-7s-plus"></i>&nbsp;&nbsp;&nbsp;&nbsp;<label><b>Cardholder registration</b></label>
                                    </a>
                                    <?php } ?>
                        
                                    <br>
                                    <br>
                                </p>
                                <p class="category">
                                
                                <form action="clients?scr=9" method="POST" onsubmit="return checkSubmitBlock('busqueda');">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <!-- <label for="inputPerfil">Nombre de Empleado/ Tarjeta</label> -->
                                                <label for="inputPerfil">Employee / Card Name</label>
                                                <select class="form-control supervisores2" name="inputPerfil" required >
                                                     <option value="<?php echo $value_CH; ?>"><?php echo $name_CH; ?></option>
                                               
                                                <?php 
                                                    for ($i = 0; $i < sizeof( $Empleados ); $i += 3) 
                                                    {
                                                        if ( isset( $Empleados[ $i ] ) ) 
                                                        {
                                                            $tarjetas=$app->viewCardsByUsers($_SESSION['EMPRESA'],$Empleados[$i+2]); // Busca las tarjetas que tiene el usuario
                                                            if(sizeof($tarjetas)>0) // si existe al menos una tarjeta para usuario
                                                            {            
                                                                for($j=0;$j<sizeof($tarjetas);$j+=2) // ciclo para recorrer las tarjetas
                                                                {      
                                                ?>  
                                                                    <option value="<?php echo $Empleados[ $i ].'-'.$tarjetas[$j]; ?>"><?php echo strtoupper(utf8_encode($Empleados[ $i + 1 ])). '    ****-****-' . substr($tarjetas[$j + 1],0,4) . '-' . substr($tarjetas[$j + 1],-4);; ?></option>
                                                                <?php 
                                                                }
                                                            }
                                                            else // si no tiene asignada ni una tarjeta se le asignara
                                                            {
                                                ?>  
                                                                <option value="<?php echo $Empleados[ $i ]; ?>"><?php echo strtoupper(utf8_encode($Empleados[ $i + 1 ])); ?></option>
                                                <?php 
                                                            }
                                                        

                                                ?>
                                                <?php   } 
                                                    } 
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputCiudad">&nbsp;</label>
                                                <input type="submit" class="form-control" id="busqueda" value="Search">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                </p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped" id="CH">
                                    <thead>
                                        <th>ID</th>
                                        <th>Photo</th>
                                    	<th>Name</th>
                                    	<th>Company</th>
                                    	<th>Card</th>
                                        <th>Balance</th>
                                        <th>Status</th>
                                        <th>Options</th>
                                    </thead>
                                    <tbody>
                                        <?php for ( $i = 0; $i <= count( $dUsuariosI ); $i += 8 ) {
                                            if ( isset( $dUsuariosI[$i] ) ) {
                                        ?>
                                        <tr>
                                            <td><?php echo $dUsuariosI[$i]; ?></td>
                                            <td><img src="<?php echo $dUsuariosI[$i + 7]; ?>" alt="<?php echo $dUsuariosI[$i + 1]; ?>" height="30" width="30"></td>
                                        	<td><?php echo substr(strtoupper(utf8_encode($dUsuariosI[$i + 1])),0,30); ?></td>
                                        	<td><img src="assets/img/<?php echo $dUsuariosI[$i + 2]; ?>" alt="<?php echo $dUsuariosI[$i + 2]; ?>" height="13" width="60"></td>
                                            <?php if($dUsuariosI[$i+3]!==0)
                                            {
                                            ?>
                                            <td><?php echo '****-****-' . substr($dUsuariosI[$i + 3],0,4) . '-' . substr($dUsuariosI[$i + 3],-4); ?></td>
                                            <td><?php echo'$ '.number_format($dUsuariosI[$i + 4],2,'.',','); ?></td>
                                            
                                            <?php
                                            }
                                            else
                                            {
                                            ?>
                                                <td></td>
                                                <td></td>
                                            <?php
                                            }
                                            ?>
                                            <td>
                                                <?php 
                                                    if($dUsuariosI[$i + 5] == '1')
                                                    {
                                                        echo 'ACTIVE';
                                                    } 
                                                    else
                                                    {
                                                        echo 'INACTIVE';
                                                    }
                                                ?>
                                            </td>
                                            <?php if ( $app->optSearch($permisos,'opt27')) 
                                            {
                                            ?>
                                            <td>
                                                <a href="view_perfil?scr=9&idu=<?php echo $dUsuariosI[$i]; ?>" rel="tooltip" title="Edit" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-search" style="font-size:18px;"></i>
                                                </a>
                                                <?php
                                                    if ( $dUsuariosI[$i + 5] == '1' ) 
                                                    {
                                                ?>
                                                <a  href="clients?scr=9&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $dUsuariosI[$i]; ?>&sta=<?php echo $dUsuariosI[$i + 5]; ?>" class="btn btn-default btn-sm" rel="tooltip" title="Inactive" >
                                                    <i class="pe-7s-close-circle" style="font-size:18px;"></i>
                                                </a>
                                                <?php
                                                    } 
                                                    else
                                                    {
                                                ?>
                                                <a  href="clients?scr=9&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $dUsuariosI[$i]; ?>&sta=<?php echo $dUsuariosI[$i + 5]; ?>" class="btn btn-default btn-sm" rel="tooltip" title="Active" >
                                                    <i class="pe-7s-refresh" style="font-size:18px;"></i>
                                                </a>
                                                <?php
                                                    }
                                                ?>
     
                                            </td>
                                            <?php 
                                            }else
                                            {
                                                echo '<td></td>';
                                            }
                                            ?>
                                        </tr>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

<?php
echo "<script type='text/javascript'> $('.supervisores2').select2(); </script>";


include 'footer.php';