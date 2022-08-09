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
$Empresas   = $app->viewAllCompanysByPerfil('EMPRESA');
$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );

$Consulta   = false;
$fechaI  = $maxDate; 
$fechaF  = $maxDate;
$result=[];
if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{

      if(!isset($_POST[ 'ConsultaF' ]))
    {
        $Consulta=true;
        $fechaI  = $_POST["inputFechaInicio"]; 
        $fechaF  = $_POST["inputFechaFinal"];
    }

    $fecha_ini=$_POST["inputFechaInicio"];
    $fecha_fin=$_POST["inputFechaFinal"];
    $card=substr($_POST["inputCard"],-8);
    $ide_empresa=$_SESSION['EMPRESA'];
   
    $diferencia = (strtotime($_POST["inputFechaFinal"]) - strtotime($_POST["inputFechaInicio"]))/86400; // diferencia entre fecha final e inicial 
    if($diferencia<0) // si se invirtieron la fecha minima y maxima
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
                $msg    = 'Hecho!';
                $result=$data['TicketMessage'];
            }elseif(isset($data['ErrorMessage']))
            {
                $mensaje=$data['ErrorMessage'];
                $app->insertlogEstadoCuenta($ide_empresa,$card,1,$mensaje);   
                $mensaje='No se encontraron movimientos con fechas : '.substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' y ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4);
                $msg    = 'Error';
            }
            else
            {
                $mensaje='01NO ESTA DECLARADO TICKETMESSAGE Y ERRORMESSAGE';
                $app->insertlogEstadoCuenta($ide_empresa,$card,1,$mensaje); 
                $mensaje='No se encontraron movimientos con fechas : '.substr($fecha_ini,8,2).'/'.substr($fecha_ini,5,2).'/'.substr($fecha_ini,0,4) . ' y ' . substr($fecha_fin,8,2).'/'.substr($fecha_fin,5,2).'/'.substr($fecha_fin,0,4);
                $msg    = 'Error';
            }
    }
} 
else 
{
    $result = [];    
}

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
                <span> <?php echo $mensaje;?>.</span>
            </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Reporte de movimientos de las tarjetas </h4> -->
                        <h4 class="title">Card movements report </h4>
                        <!-- <p class="category">Vista general de reporte de movimientos de tarjeta habientes.</p> -->
                        <p class="category">General view of cardholder movements report.</p>
                        <form action="movements_cards?scr=55" method="POST" onsubmit="return checkSubmitBlock('btnsubmit');">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <!-- <label for="inputEmpresa">Empresa</label> -->
                                        <label for="inputEmpresa">Company</label>
                                        <select class="form-control transactions2" name="inputEmpresa" required onChange="getCards(this.value)">
                                        <option value=""></option>
                                        <option value="TODOS">TODOS</option>
                                        
                                        <?php for ($i = 0; $i < count( $Empresas ); $i += 7) {
                                            if ( isset( $Empresas[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empresas[ $i ]; ?>"><?php echo utf8_encode(strtoupper($Empresas[ $i + 1 ])); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                    <!-- <label for="inputCiudad">Tarjeta</label> -->
                                    <label for="inputCiudad">Card</label>
                                        <select class="form-control cards2" name="inputCard" id="cards" required=""></select>
                       
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
                        <table id="CardsMovements" class="table table-hover table-striped display">
                            <thead>
                                <th>Card</th>
                                <th>Date</th>
                                <th>Authorization</th>
                                <th>Amount</th>
                                <th>Concept</th>
                                
                            </thead>
                            <tbody>
                            <?php foreach( $result as $arreglo ) 
                                {
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
                           
                                    $AMonto = '$ '.number_format( $AMonto, 2, '.', ',' );
                                    $ACard  = '****-****-'.str_pad(substr($card, 0,-4),4, "**", STR_PAD_LEFT).'-'.substr($card, -4);
                                    $AFecha = substr($arreglo['Date'],8,2).'/'.substr($arreglo['Date' ],5,2).'/'.substr($arreglo[ 'Date' ],0,4).substr($arreglo[ 'Date' ],10,9);
                                    
                                    echo '<tr><td>' . $ACard . '</td>
                                    <td>' . $AFecha . '</td>
                                    <td>' . $arreglo['Auth_Code'] . '</td>
                                    <td>' . $AMonto . '</td>
                                    <td>' . $arreglo['Merchant'].'</td></tr>';
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
echo "<script type='text/javascript'> $('.cards2').select2();</script>";


include 'footer.php';