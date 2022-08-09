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
$Empresas     = $app->viewAllCompanysByPerfil('EMPRESA');

$msg        = '';
$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );
$value_company=$empresa='';

$Consulta   = false;
$fechaI  = $maxDate; 
$fechaF  = $maxDate;
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{

    if(!isset($_POST[ 'ConsultaF' ]))
    {
        $Consulta=true;
    }
    $fechaI  = $_POST["inputFechaInicio"]; 
    $fechaF  = $_POST["inputFechaFinal"];

    $fecha_ini=$_POST["inputFechaInicio"].' 00:00:00';
    $fecha_fin=$_POST["inputFechaFinal"].' 23:59:59';
    $value_company=$_POST["inputEmpresa"]; // id de empresa   
    
    if($_POST["inputEmpresa"]=='TODOS')
    {

        $dEmpresasI = $app->getAllFundsCompanys( $fecha_ini, $fecha_fin );
        $empresa='TODOS';
    }
    else
    {
        $dEmpresasI = $app->getFundsCompanysbyIde( $_POST['inputEmpresa'], $fecha_ini, $fecha_fin );
        $data= $app->viewCompany($_POST['inputEmpresa']);
        $empresa=utf8_encode($data[0]);

    }
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
                    <span><b> Successful - </b>Company : <?php echo strtoupper($empresa);?> Requested ranges: <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?></span>
                </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span>No records were found with the criteria: Company : <?php echo strtoupper($empresa).' Date: ';?> <?php echo substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' and ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4); ?>.</span>
                </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Reporte de la empresa</h4> -->
                        <h4 class="title">Company report</h4>
                        <!-- <p class="category">Vista general de reporte de la actividad de las empresas.</p> -->
                        <p class="category">General view of the report of the activity of the companies.</p>
                        <p class="category"></p>
                        <form action="transactions?scr=55" method="POST" onsubmit="checkSubmit('btsubmit');">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="inputCodigoPostal">Fecha Inicio</label> -->
                                        <label for="inputCodigoPostal">Start date</label>
                                        <input type="date" class="form-control" max="<?php echo $maxDate; ?>" name="inputFechaInicio" value="<?php echo $fechaI;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="inputCiudad">Fecha Final</label> -->
                                        <label for="inputCiudad">Finish date</label>
                                        <input type="date" class="form-control" max="<?php echo $maxDate; ?>" name="inputFechaFinal" value="<?php echo $fechaF;?>" required >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <!-- <label for="inputEmpresa">Empresa</label> -->
                                        <label for="inputEmpresa">Company</label>
                                        <select class="form-control transactions2" name="inputEmpresa" required >
                                            <option value="<?php echo $value_company;?>"><?php echo strtoupper($empresa);?></option>
                                        <option value="TODOS">TODOS</option>

                                        <?php for ($i = 0; $i <= count( $Empresas ); $i += 7) {
                                            if ( isset( $Empresas[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empresas[ $i ]; ?>"><?php echo strtoupper(utf8_encode($Empresas[ $i + 1 ])); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCiudad">&nbsp;</label>
                                        <input type="hidden" name="ConsultaF" value="1">
                                        <button type="submit" class="form-control btn btn-primary" id="btnsubmit"><b><i style="font-size: 21px;" class="pe-7s-search"></i></b></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="content table-responsive ">
                        <table id="Founds" class="table table-hover table-striped display">        <thead>
                                <th>#</th>
                                <th>Company</th>
                                <th>Amount</th>
                                <th>Descripción</th>
                                <th>Date</th>
                                <th>Card</th>
                            </thead>
                            <tbody>
                                <?php for ( $i = 0; $i <= count( $dEmpresasI ); $i += 6 ) 
                                {
                                    if ( isset( $dEmpresasI[ $i ] ) ) 
                                    {
                                ?>
                                <tr>
                                    <td><?php echo $dEmpresasI[$i]; ?></td>
                                    <td><?php echo strtoupper(utf8_encode($dEmpresasI[$i + 1])); ?></td>
                                    <td><?php echo $dEmpresasI[$i + 2]; ?></td>
                                    <td><?php echo $dEmpresasI[$i + 3]; ?></td>
                                    <td><?php echo substr($dEmpresasI[ $i + 4 ],8,2).'/'.substr($dEmpresasI[ $i + 4 ],5,2).'/'.substr($dEmpresasI[ $i + 4 ],0,4).substr($dEmpresasI[ $i + 4 ],10,9); ?></td>
                                    <?php if(!(strpos($dEmpresasI[$i + 3], 'EMPRESA')))
                                    {
                                        if(strlen($dEmpresasI[ $i + 5 ])==0)
                                        {

                                        ?>
                                        <td><?php echo '****-****-####-####'; ?></td>
                                        <?php 
                                        }
                                        else
                                        {?>
                                        <td><?php echo '****-****-'.str_pad(substr($dEmpresasI[ $i + 5 ], 0,-4),4, "**", STR_PAD_LEFT).'-'.substr($dEmpresasI[ $i + 5 ], -4); ?></td>
                                        <?php 
                                        }

                                    }else{?>
                                    <td></td>
                                    <?php }?>
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