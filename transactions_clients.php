<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" or $_SESSION['EMPRESA'] === '0' ) 
{
    header( "Location: login" );
}

include "include/coredata.php";
$app        = new app();
$msg        = FALSE;
$Empleados=$app->viewCardsByCompany($_SESSION['EMPRESA']);

$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );

$Consulta   = false;
$fechaI  = $maxDate;
$fechaF  = $maxDate;
$card    = $value_card ='';

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
    $fecha_ini=$_POST["inputFechaInicio"].' 00:00:00';
    $fecha_fin=$_POST["inputFechaFinal"].' 23:59:59';
      
     if(!isset($_POST[ 'ConsultaF' ]))
    {
        $Consulta=true;
        $fechaI  = $_POST["inputFechaInicio"]; 
        $fechaF  = $_POST["inputFechaFinal"];
        $value_card = $_POST["inputPerfil"];
    }

    if($_POST["inputPerfil"]=="TODOS")
    {
        $card=$value_card;
        $dEmpresasI = $app->layout_anchor( $_SESSION[ 'EMPRESA' ],$fecha_ini,$fecha_fin );
        if(sizeof($dEmpresasI)>0)
        {
            $msg    = 'Hecho!';
        }else
        {
            $msg    = 'Error';
        }
    }
    else
    {
        $dEmpresasI=$app->get_FoundsCardByEmployee($_SESSION[ 'EMPRESA' ],$_POST['inputPerfil'],$fecha_ini,$fecha_fin);
          $nombre_CH=$app->getNameCardHolder($_POST['inputPerfil']);
        $tarjeta=$_POST['inputPerfil'];
        $card=strtoupper(utf8_encode($nombre_CH)) . '    ****-****-'.str_pad(substr($tarjeta, 0,4),4, "0", STR_PAD_LEFT).'-'.substr($tarjeta, -4);
      
        if(sizeof($dEmpresasI)>0)
        {
            $msg    = 'Hecho!';
        }else
        {
            $msg    = 'Error';
        }
    }
}
else 
{
    $dEmpresasI = [];    
}
$datacompany  = $app->viewCompany( $_SESSION[ 'EMPRESA' ] );


include 'header.php';

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">

        <?php if ( strlen( $msg ) == 6 ) { ?> 
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><b> Successful - </b> Requested ranges: <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?></span>
            </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span> No information was found with the requested ranges: <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?>.</span>
            </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Reporte de la empresa</h4> -->
                        <h4 class="title">Report of Company</h4>
                        <!-- <p class="category">Vista general de reporte de la actividad de las empresas.</p> -->
                        <p class="category">Verview of the company's activity report.</p>
                        <form action="transactions_clients?scr=55" method="POST" >
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="inputCodigoPostal">Fecha Inicio</label> -->
                                        <label for="inputCodigoPostal">Start Date</label>
                                        <input type="date" class="form-control" name="inputFechaInicio" max="<?php echo $maxDate; ?>" value="<?php echo $fechaI;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="inputCiudad">Fecha Final</label> -->
                                        <label for="inputCiudad">Finish date</label>
                                        <input type="date" class="form-control" name="inputFechaFinal" max="<?php echo $maxDate; ?>" value="<?php echo $fechaF;?>" required >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <!-- <label for="inputPerfil">Usuario</label> -->
                                        <label for="inputPerfil">Users</label>
                                        <select class="form-control supervisores2" name="inputPerfil" required >
                                            <option value="<?php echo $value_card;?>"><?php echo $card;?></option>
                                        
                                            <?php if(sizeof($Empleados)>0)
                                            {?>
                                            <option value="TODOS">TODOS</option>
                                            <?php 
                                            }
                                            ?>
                                        <?php for ($i = 0; $i <= count( $Empleados ); $i += 2) {
                                            if ( isset( $Empleados[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empleados[ $i ]; ?>"><?php echo strtoupper(utf8_encode($Empleados[ $i+1 ])) . '    ****-****-'.str_pad(substr($Empleados[$i], 0,4),4, "0", STR_PAD_LEFT).'-'.substr($Empleados[$i], -4); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCiudad">&nbsp;</label>
                                        <input type="submit" class="form-control" value="Consult">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="content table-responsive ">
                        <table id="myTable" class="table table-hover table-striped display">
                            <thead>
                                <th>ID</th>
                                <th>Company</th>
                                <th>Card issuer</th>
                                <th>Card</th>
                                <th>Amount</th>
                                <th>Type</th>
                                <th>Comment</th>
                                <th>Date</th>
                            </thead>
                            <tbody>
                            <?php for ( $i = 0; $i <= count( $dEmpresasI ); $i += 7 ) {
                                if ( isset( $dEmpresasI[$i] ) ) {
                            ?>
                                <tr>
                                    <td><?php echo $dEmpresasI[$i]; ?></td>
                                    <td><img src="./assets/img/<?php echo $datacompany[9]; ?>" height="30" width="80"></td>
                                    <td><img src="assets/img/mc_vrt_opt_pos_46_1x.png" alt="MASTERCARD" ></td>
                                    <td><?php echo '****-****-'.str_pad(substr($dEmpresasI[$i+2], 0,-4),4, "0", STR_PAD_LEFT).'-'.substr($dEmpresasI[$i+2], -4);?></td>
                                    <td><?php echo '$ '.number_format($dEmpresasI[$i + 3],2,'.',',');; ?></td>
                                    <td><?php echo $dEmpresasI[$i + 4]; ?></td>
                                    <td><?php echo $dEmpresasI[$i + 6]; ?></td>
                                    <td><?php echo substr($dEmpresasI[ $i + 5 ],8,2).'/'.substr($dEmpresasI[ $i + 5 ],5,2).'/'.substr($dEmpresasI[ $i + 5 ],0,4).substr($dEmpresasI[ $i + 5 ],10,9); ?></td>
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

echo "
<script type='text/javascript'>
        $('.supervisores2').select2();
    </script>";
include 'footer.php';