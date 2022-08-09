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
    $fecha_fin=$_POST["inputFechaFinal"].' 23:59:59';
    $dEmpresasI = $app->GetfoundMA($fecha_ini, $fecha_fin );
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

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Successful - Requested ranges: <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?></span>
                </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span>No records were found with the criteria: <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?>.</span>
                </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Reporte de fondeos Cuenta Maestra</h4> -->
                        <h4 class="title">Master Account Funding Report</h4>
                        <!-- <p class="category">Vista general de reporte fondeos recibidos en la cuenta maestra.</p> -->
                        <p class="category">Report overview of funds received in the master account.</p>
                        <br>
                        <br>
                        
                        <form action="transactions_MA?scr=55" method="POST" onsubmit="checkSubmit('btsubmit');">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <!-- <label for="inputCodigoPostal">Fecha Inicio</label> -->
                                        <label for="inputCodigoPostal">Start date</label>
                                        <input type="date" class="form-control" max="<?php echo $maxDate; ?>" name="inputFechaInicio" value="<?php echo $fechaI;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <!-- <label for="inputCiudad">Fecha Final</label> -->
                                        <label for="inputCiudad">Finish date</label>
                                        <input type="date" class="form-control" max="<?php echo $maxDate; ?>" name="inputFechaFinal" value="<?php echo $fechaF;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="inputCiudad">&nbsp;</label>
                                        <input type="submit" class="form-control" id="btsubmit" value="Consult">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="content table-responsive ">
                        <table id="Founds" class="table table-hover table-striped display">        
                            <thead>
                                <th>#</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Description</th>
                                <th>Voucher</th>
                            </thead>
                            <tbody>
                                <?php for ( $i = 0; $i <= count( $dEmpresasI ); $i += 5 ) 
                                {
                                    if ( isset( $dEmpresasI[ $i ] ) ) 
                                    {
                                ?>
                                <tr>
                                    <td><?php echo $dEmpresasI[$i]; ?></td>
                                    <td><?php echo substr($dEmpresasI[ $i + 1 ],8,2).'/'.substr($dEmpresasI[ $i + 1 ],5,2).'/'.substr($dEmpresasI[ $i + 1 ],0,4).substr($dEmpresasI[ $i + 1 ],10,9); ?></td>
                                    <td><?php echo number_format($dEmpresasI[$i + 2],2,'.',','); ?></td>
                                    <td><?php echo $dEmpresasI[$i + 3]; ?></td>
                                    <?php
                                    if($dEmpresasI[$i + 4]!=='')
                                    {
                                    ?>
                                    <td>
                                        <a href="<?php echo $dEmpresasI[$i + 4];?>" class="btn btn-primary" target="_black"> 
                                            <i class="pe-7s-download" style="font-size:14px;"></i>
                                        </a>      
                                    </td>
                                    <?php        
                                    }else
                                    {
                                    ?>
                                    <td></td>
                                    <?php        
                                    }?>
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

echo "<script type='text/javascript'> $('.transactions2').select2();</script>";

include 'footer.php';