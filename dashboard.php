<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" ) 
{
    header( "Location: login.php" );
}

    include "include/coredata.php";

    $app = new app();
    $Company=$_SESSION['EMPRESA'];
    $permisos=$_SESSION[ 'PERMISOS' ];
    if ( $app->optSearch($permisos,'opt01') ) // usuarios nivel admin
    {
        $Data_Masters_Acounts=$app->getProductsPlatform(); // Obtiene los datos de las cuentas de la plataforma(Nombre,AgregamentId,ProductId)
        $Masters_Acounts=[];
        $masterBalance=0;
        for($i=0;$i<sizeof($Data_Masters_Acounts);$i+=4)
        {
            $saldo_master=$app->getAmountMasterAcount($Data_Masters_Acounts[$i+1],$Data_Masters_Acounts[$i+2]); // Get Balance MasterAcount Obtiene el saldo de la cuenta maestra y el nombre de la misma 
            array_push($Masters_Acounts,$Data_Masters_Acounts[$i],$saldo_master);
            $masterBalance+=$saldo_master;
        }
        $administradoraBalance  = 0; //Balance Admin
        $FoundsCompany=$app->TransFoundsCompanyByMonth('FONDEO EMPRESA');  //Suma de Fondeos de Empresas basado en un concepto en el mes
        $ReverseCompany=$app->TransFoundsCompanyByMonth('REVERSO EMPRESA'); ////Suma de Reversos a Empresas basado en un concepto en el mes
        $transFondosMouth=$transFondos = $FoundsCompany+$ReverseCompany; // transacciones del mes
    
    }
    elseif($app->optSearch($permisos,'opt02'))
    {
        $administradoraBalance  = ( $empresaMonto = $app->viewCompany( $Company ) )? $empresaMonto[11] : 0;
        $FoundsCompany=$app->TransFoundsCompanyByMonth('FONDEO EMPRESA');  //Suma de Fondeos de Empresas basado en un concepto en el mes
        $ReverseCompany=$app->TransFoundsCompanyByMonth('REVERSO EMPRESA'); ////Suma de Reversos a Empresas basado en un concepto en el mes
        $transFondosMouth=$transFondos = $FoundsCompany+$ReverseCompany; // transacciones del mes
    }
    elseif($app->optSearch($permisos,'opt03')) 
    {
        
        $masterBalance          = 000;
        $administradoraBalance  = ( $empresaMonto = $app->viewCompany( $Company ) )? $empresaMonto[11] : 0;
        $transFondosMouth            = $app->aTransFondsCards();
    }
    include 'header.php';
?>
    <style type="text/css">
        #scroll1 
        {
	        height: 200px;
	        overflow-y: scroll;
        }
    </style>
    
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                
                <?php if ( $app->optSearch($permisos,'opt01')) 
                    { ?>
                        <?php 
                        for($i=0;$i<sizeof($Masters_Acounts);$i+=2) { ?>    
                        <div class="col-md-3">
                            <div class="card ">
                                <div class="header">
                                    <h1 class="title"><i class="pe pe-7s-culture pe-2x pull-left pe-border"></i></h1>
                                    <p class="category"><br><span style="font-size:18px;"><b>&nbsp;$ <?php echo number_format( floatval( substr( $Masters_Acounts[$i+1] , 2 ) ), 2, '.', ',' ); ?></b></span><br><span style="font-size:18px;">Funds <?php echo $Masters_Acounts[$i];?></span></p>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    <?php } ?>
                    
                    <?php if ( $app->optSearch($permisos,'opt02') OR $app->optSearch($permisos,'opt03')) { ?>
                    
                    <div class="col-md-3">
                        <div class="card ">
                            <div class="header">
                                <h1 class="title"><i class="pe pe-7s-safe pe-2x pull-left pe-border"></i></h1>
                                <p class="category"><br><span style="font-size:18px;"><b>&nbsp;&nbsp;$ <?php echo number_format( floatval( $administradoraBalance ), 2, '.', ',' ); ?></b></span><br><span style="font-size:18px;">Total Available</p>
                            </div>
                        </div>
                    </div>

                    <?php } ?>
                    
                    <?php if ( $app->optSearch($permisos,'opt02') OR $app->optSearch($permisos,'opt03')) { ?>

                    <div class="col-md-3">
                        <div class="card ">
                            <div class="header">
                                <h1 class="title"><i class="pe pe-7s-credit pe-2x pull-left pe-border"></i></h1>
                                <p class="category"><br><span style="font-size:18px;"><b>&nbsp;&nbsp;$ <?php echo number_format( floatval( $transFondosMouth ), 2, '.', ',' ); ?></b></span><br><span style="font-size:18px;">Total Trans / Month</p>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                
                <div class="row">
                <?php if ( $app->optSearch($permisos,'opt04')) { ?>
                    <input type="hidden" value="0,0,0,0,0,0,0,0,0,0,<?php echo $administradoraBalance;?>,0"; id="fondos">
                    <input type="hidden" value="0,0,0,0,0,0,0,0,0,0,<?php echo $transFondosMouth;?>,0"; id="transacciones">
                    <div class="col-md-5">
                        <div class="card ">
                            <div class="header">
                                <h4 class="title">2021</h4>
                                <p class="category">Funds versus Transfers</p>
                            </div>
                            <div class="content">
                               <div class="ct-chart" id="chartActivity"></div>
                                <div class="footer">
                                    <div class="legend">
                                        <i class="fa fa-circle text-info"></i> Funds
                                        <i class="fa fa-circle text-danger"></i> Transfers
                                    </div>
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-check"></i> Data calculated up to the time of login.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                    <div class="col-md-7">
                        <div class="card ">
                            <div class="header">
                                <h4 class="title">Movement Log</h4>
                                <p class="category">Recent activities</p>
                            </div>
                            <div class="content">
                                <div class="table-full-width">
                                    <?php if ( $app->optSearch($permisos,'opt05')) { ?>
                                        <div id="scroll1">
                                            <div class="content">
                                                <table class="table" style="font-size:10px;">
                                                    <tbody>
                                                        <tr>
                                                            <th>Company</th>
                                                            <th>Amount</th>
                                                            <th>Date</th>
                                                            <th>Descripcion</th>
                                                        </tr>
                                                        
                                                    <?php 
                                                    $Bitacora=$app->BitacoraCompanys('EMPRESA');
                                                    for ( $i = 0; $i < sizeof( $Bitacora ); $i += 4 ) 
                                                    {
                                                    ?>
                                                        <tr>                           
                                                            <td><?php echo strtoupper($Bitacora[$i]);?></td>                                                    
                                                            <td><?php echo number_format($Bitacora[ $i+1],2,'.',',');?></td>
                                                            <td><?php echo substr($Bitacora[ $i + 2 ],8,2).'/'.substr($Bitacora[ $i + 2 ],5,2).'/'.substr($Bitacora[ $i + 2 ],0,4).substr($Bitacora[ $i + 2 ],10,9);?></td>
                                                            <td><?php echo strtoupper($Bitacora[$i+3]);?></td>                                                    
                                                        </tr>                                         
                                                    <?php 
                                                    }                                    
                                                    ?>
                                                    </tbody>
                                                </table>
                                            <div>
                                        </div>
                                    <?php 
                                    }                                    
                                    ?>
                                    <?php if ( $app->optSearch($permisos,'opt06')) { ?>
                                        <div id="scroll1">
                                            <div class="content">
                                                <table class="table" style="font-size:10px;">
                                                <tbody>
                                                    <tr>
                                                        <th>Card</th>
                                                        <th>Card Holder</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Descripcion</th>
                                                    </tr>
                                                    <?php 
                                                    $Bitacora=$app->BitacoraCards($Company,'TARJETA');
                                                    for ( $i = 0; $i < sizeof( $Bitacora ); $i += 4 ) 
                                                    {
                                                        $nombre=$app->getNameCardHolder($Bitacora[$i]);
                                                    ?>
                                                    <tr>
                                                        <td><?php echo '****-****-'.substr($Bitacora[$i],0,4).'-'.substr($Bitacora[ $i],-4);?></td>
                                                        <td><?php echo strtoupper(utf8_encode($nombre));?></td>                                   
                                                        <td><?php echo number_format($Bitacora[ $i+1],2,'.',',');?></td>
                                                        <td><?php echo substr($Bitacora[ $i + 2 ],8,2).'/'.substr($Bitacora[ $i + 2 ],5,2).'/'.substr($Bitacora[ $i + 2 ],0,4).substr($Bitacora[ $i + 2 ],10,9);?></td>
                                                        <td><?php echo strtoupper($Bitacora[$i+3]);?></td>                                                    
                                                    </tr>                                         
                                                    <?php 
                                                    }                                    
                                                    ?>
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <?php }?>    
                                    
                                </div>

                                <div class="footer">
                                    <hr>
                                    <div class="stats">
                                        <i class="fa fa-history"></i> Updated
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
<?php
include 'footer.php';