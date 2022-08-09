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
$app        = new app();
$msg        = '';
$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );

$Consulta   = false;
$fechaI  = $maxDate; 
$fechaF  = $maxDate;

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
    if(!isset($_POST[ 'ConsultaF' ]))
    {
        $Consulta=true;
        $fechaI  = $_POST["inputFechaInicio"]; 
        $fechaF  = $_POST["inputFechaFinal"];
    }

    $fecha_ini=$_POST["inputFechaInicio"].' 00:00:00';
    $fecha_fin=$_POST["inputFechaFinal"].' 23:59:49';

    $dEmpresasI = $app->GetFundstoCompany( $_SESSION['EMPRESA'], $fecha_ini, $fecha_fin );
    if ( $dEmpresasI )
    {
        $msg    = 'Hecho!';
    }
    else
    {
        $msg    = 'Error';
    }
}
else
{
    $dEmpresasI  = [];
}

include 'header.php';
//var_dump($dEmpresasI);
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Succesfulll - </b> Requested ranges: <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?></span>
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
                        <h4 class="title">Report movements cards</h4>
                        <!-- <p class="category">Vista general de reporte de los fondeos recibidos en la empresa.</p> -->
                        <p class="category">General View report of movevements cards.</p>
                        <p class="category"></p>
                        <form action="report_funds_company?scr=55" method="POST" onsubmit="checkSubmit('btsubmit');">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="inputFechaInicio">Fecha Inicio</label> -->
                                        <label for="inputFechaInicio">Start date</label>
                                        <input type="date" class="form-control" max="<?php echo $maxDate; ?>" name="inputFechaInicio" value="<?php echo $fechaI;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="inputFechaFinal">Fecha Final</label> -->
                                        <label for="inputFechaFinal">Finish date</label>
                                        <input type="date" class="form-control" max="<?php echo $maxDate; ?>" name="inputFechaFinal" value="<?php echo $fechaF;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputFechaFinal">&nbsp;</label>
                                        <input type="submit" class="form-control" id="btsubmit" name="ConsultaF" value="Consult">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="content table-responsive ">
                        <table id="myTable" class="table table-hover table-striped display">        <thead>
                                <th>#</th>
                                <th>Company</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Date</th>
                            </thead>
                            <tbody>
                                <?php for ( $i = 0; $i < count( $dEmpresasI ); $i += 5 ) 
                                {
                                    if ( isset( $dEmpresasI[ $i ] ) ) 
                                    {
                                ?>
                                <tr>
                                    <td><?php echo $dEmpresasI[$i]; ?></td>
                                    <td><?php echo strtoupper(utf8_encode($dEmpresasI[$i + 1])); ?></td>
                                    <td><?php echo '$ '.number_format($dEmpresasI[$i + 2],2,'.',',');; ?></td>
                                    <td><?php echo $dEmpresasI[$i + 3]; ?></td>
                                    <td><?php echo substr($dEmpresasI[ $i + 4 ],8,2).'/'.substr($dEmpresasI[ $i + 4 ],5,2).'/'.substr($dEmpresasI[ $i + 4 ],0,4).substr($dEmpresasI[ $i + 4 ],10,9); ?></td>
                                    
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
echo "<script type='text/javascript'> $('.companias2').select2();</script>";
include 'footer.php';
?>