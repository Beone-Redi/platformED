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

include 'header.php';

$app        = new app();
$msg        = FALSE;
$bandera    = FALSE;
$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );

$Consulta   = false;
$fechaI     = $maxDate;
$fechaF     = $maxDate;
$name_CH = $value_CH ='';

$result=[];
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
    $fecha_ini=$_POST["inputFechaInicio"].' 00:00:00';
    $fecha_fin=$_POST["inputFechaFinal"].' 23:59:59';
    if(!isset($_POST[ 'ConsultaF' ]))
    {
        $Consulta=true;
        $fechaI  = $_POST["inputFechaInicio"]; 
        $fechaF  = $_POST["inputFechaFinal"];
    }
    $value_CH=$card=substr($_POST["inputCard"],-8);
    $nombre_CH=$app->getNameCardHolder($card);
    $name_CH=strtoupper(utf8_encode($nombre_CH)) . '    ****-****-'.str_pad(substr($card, 0,4),4, "0", STR_PAD_LEFT).'-'.substr($card, -4);       
    $ide_empresa=$_SESSION['EMPRESA'];
    $diferencia = (strtotime($_POST["inputFechaFinal"]) - strtotime($_POST["inputFechaInicio"]))/86400; // diferencia entre fecha final e inicial 
    if($diferencia<0)
    {
        $msg    = 'Error';
        $dEmpresasI  = [];
        $mensaje='No se encontraron movimientos con fechas : '.substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' y ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4);
            
    }
    else
    {
            $data=$app->getMovementsCard($card,$fecha_ini,$fecha_fin);
            if(isset($data['TicketMessage']))
            {
                $bandera=TRUE;
                $msg    = 'Hecho!';
                $result=$data['TicketMessage'];
                
            }elseif(isset($data['ErrorMessage']))
            {
                $mensaje=$data['ErrorMessage'];
                $app->insertlogEstadoCuenta($ide_empresa,$card,1,$mensaje);  
                $msg    = 'Error';
                $mensaje='No se encontraron movimientos con fechas : '.substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' y ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4);
            }
            else
            {
                $mensaje='01NO ESTA DECLARADO TICKETMESSAGE Y ERRORMESSAGE';
                $app->insertlogEstadoCuenta($ide_empresa,$card,1,$mensaje);
                $msg    = 'Error';
                $mensaje='No se encontraron movimientos con fechas : '.substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' y ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4);
            }
    }
} 
else 
{
    $result = [];    
}
$cards=$app->viewCardsByCompany($_SESSION['EMPRESA']);

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
                <span> <?php echo $mensaje;?>.</span>
            </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Reporte de movimientos de las tarjetas </h4> -->
                        <h4 class="title">Report movements cards </h4>
                        <!-- <p class="category">Vista general de reporte de movimientos de tarjeta habientes.</p> -->
                        <p class="category">General View report of movevements cards.</p>
                        <form action="movements_cards_company?scr=55" method="POST" id="myform1" onsubmit="return checkSubmitBlock('btnsubmit');">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <!-- <label for="inputCodigoPostal">Fecha Inicio</label> -->
                                        <label for="inputCodigoPostal">Start date</label>
                                        <input type="date" class="form-control" name="inputFechaInicio" max="<?php echo $maxDate; ?>" value="<?php echo $fechaI;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <!-- <label for="inputCiudad">Fecha Final</label> -->
                                        <label for="inputCiudad">Finish date</label>
                                        <input type="date" class="form-control" name="inputFechaFinal" max="<?php echo $maxDate; ?>" value="<?php echo $fechaF;?>" required >
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <!-- <label for="inputEmpresa">Tarjeta</label> -->
                                        <label for="inputEmpresa">Card</label>
                                        <select class="form-control cards2" name="inputCard" required id="idcard">
                                              <option value="<?php echo $value_CH;?>"><?php echo $name_CH;?></option>
                                      
                                        <?php 
                                            for ($i = 0; $i < sizeof($cards); $i+=2) 
                                            {
                                        ?>
                                            <option value="<?php echo $cards[$i]; ?>"><?php echo strtoupper(utf8_encode($cards[ $i + 1 ])). '    ****-****-' . substr($cards[$i],0,4) . '-' . substr($cards[$i],-4);; ?>
                                            </option>
                                        <?php   
                                            }
                                        ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="inputCiudad">&nbsp;</label>
                                        <button type="submit" class="form-control btn btn-primary" id="btnsubmit" name="ConsultaF"><b><i style="font-size: 21px;" class="pe-7s-search"></i></b></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="content table-responsive ">
                        <table id="myTable" class="table table-hover table-striped display">
                            <thead>
                                <th>Card</th>
                                <th>Date</th>
                                <th>Authorization</th>
                                <th>Amount</th>
                                <th>Concept</th>
                                <th>Bill</th>
                                
                            </thead>
                            <tbody>
                            <?php foreach( $result as $arreglo ) 
                                {
                                    $url=$app->get_FacturabyAuthCode($arreglo['Auth_Code']);
                                    
                                    if ( strpos( $arreglo['Merchant'], 'DEVOLUCIÓN |' ) === FALSE and strpos( $arreglo['Merchant'], 'AJUSTE |' ) === TRUE and $arreglo['Type_Id'] == '21' )
                                    {
                                        $AMonto = $arreglo['Amount'] * -1;
                                        $ATipo  = 'Cargo'; //Paramostrar el tipo 
                                    }
                                    else
                                    {
                                        if ( ( $arreglo['Type_Id'] == '2' or ( strpos( $arreglo['Merchant'], 'DEVOLUCIÓN |' ) === TRUE and $arreglo['Type_Id'] == '21' ) ) )
                                        {
                                            $AMonto = $arreglo['Amount'] * -1;
                                            $ATipo  = 'Cargo'; //Paramostrar el tipo 
                                        }
                                        else
                                        {
                                            $AMonto = $arreglo['Amount'];
                                            $ATipo  = 'Abono'; //Paramostrar el tipo 
    
                                        } 
                                    }
                            ?>
                                <tr>
                                    <td><?php echo '****-****-'.str_pad(substr($card, 0,-4),4, "**", STR_PAD_LEFT).'-'.substr($card, -4);?></td>
                                    <td><?php echo substr($arreglo['Date'],8,2).'/'.substr($arreglo['Date' ],5,2).'/'.substr($arreglo[ 'Date' ],0,4).substr($arreglo[ 'Date' ],10,9); ?></td>
                                    <td class="cantidad"><?php echo $arreglo['Auth_Code'];?></td>             
                                    <td><?php echo '$ '.number_format($AMonto,2,'.',',');?></td>
                                    <td><?php echo $arreglo['Merchant'];?></td>
                                    <?php if($url!=='') 
                                    {
                                        if(substr($arreglo['Date'],0,8)!==$Mes)
                                        {
                                    ?>
                                    <td>
                                        <a href="<?php echo $url;?>" class="btn btn-primary" target="_blank"> 
                                            <i class="pe-7s-download" style="font-size:16px;"></i>
                                        </a>      
                                    </td>
                                    <?php
                                        }
                                        else
                                        {
                                            ?>
                                    <td>
                                        <a href="<?php echo $url;?>" class="btn btn-primary" target="_blank"> 
                                            <i class="pe-7s-download" style="font-size:16px;"></i>
                                        </a>      
                                        <button id="borrarfactura" class="btn btn-danger" type="button" onclick="errasefactura($(this).parents('tr').find('td.cantidad').html());"><i class="pe-7s-close-circle" style="font-size:16px;"></i></button>
                                              
                                  
                                    </td>
                                    
                                    <?php
                                            
                                        } 
                                    }
                                    else
                                    {
                                        if(substr($arreglo['Date'],0,8)==$Mes)
                                        { 
                                    ?>
                                            <td>
                                              <button class="btn btn-primary" type="button" onclick="demo_fancy($(this).parents('tr').find('td.cantidad').html());"><i class="pe-7s-cloud-upload" style="font-size:16px;"></i></button>
                                            </td>
                                        <?php 
                                        } 
                                        else{
                                            ?>
                                            <td></td>
                                        <?php    
                                        }
                                    }
                                    ?>
                                </tr>
                            <?php 
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    

        <div style="display: none;" id="hidden-content">
            <form method='post' action='' id="form1" enctype="multipart/form-data">
    	        <p>Authorization code </p>   
                <input type="text" id="codefactura" class="form-control" disabled>
                <input type="hidden" id="authcodefactura">
                Select file : <input type='file' name='file' id='file' class='form-control' ><br>
                <input type='button' class='btn btn-info' value='Load' id='btn_upload'>
                <br>           
            </form>
        <div>
    </div>

</div>

<script type='text/javascript'>
$(document).ready(function () {
    $("#btn_upload").click(function (event) {
    var ac=document.getElementById('authcodefactura').value;
    var card1=document.getElementById('idcard').value;
    var fd = new FormData();
    var files = $('#file')[0].files[0];
    fd.append('file',files);
    fd.append('idfactura',ac);
    fd.append('card',card1);
    var formulario = document.getElementById("myform1");            
        $.ajax({
            url: 'ajaxfile.php',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){    
                if(response=='Archivo subido correctamente')
                {
                    alert(response);
                    formulario.submit();
                    document.getElementById('btn_upload').value = "Enviando...";
                    document.getElementById('btn_upload').disabled = true;
                }   
                else
                {
                    alert(response);
                }    
                        
            }
        });
    return true;
    });
});
        </script>

<?php

echo "<script type='text/javascript'> $('.transactions2').select2();</script>";
echo "<script type='text/javascript'> $('.cards2').select2();</script>";


include 'footer.php';