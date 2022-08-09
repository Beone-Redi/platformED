<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" ) 
{
    header( "Location: login" );
}

// Inicio el aplicativo.
include "include/coredata.php";
$app    = new app();
$msg    = FALSE;

if ( isset( $_POST['tarjeta'] ) AND isset($_SESSION["EMPRESA"]) AND !is_null($_SESSION["EMPRESA"]))
{
    $usuario=$_SESSION["USER"];
    $tarjeta=$_POST["tarjeta"];
    $monto=$_POST["amount"];
    $motivo=$_POST["motivo"];
    $empresa=$app->getCompanyByCardHolder($tarjeta);
    $saldo_tarjeta=$app->check_amountCard($tarjeta);
    $accion=$_POST["inputacciontarjeta"];
    if($accion=="FONDEAR")
    {
        $descripcion='FONDEO A TARJETA';
        $Type_product=$app->getIdProductByCard($tarjeta); //get data MasterAcount
        $saldo_master=$app->getAmountMasterAcount($Type_product[2],$Type_product[3]); // Get Balance MasterAcount Obtiene el saldo de la cuenta maestra y el nombre de la misma        
        if($saldo_master>$monto)
        {
            $texto = $app->applyFundsManual( $empresa, $tarjeta, $monto,$usuario,$descripcion,$motivo );
        }
        else
        {
            $texto='01NO SE CUENTAN CON FONDOS PARA EL FONDEO.';   
        }
    }
    elseif($accion=="REVERSAR")
    {

        $descripcion='REVERSO A TARJETA';
        if($monto<=$saldo_tarjeta)
        {
            $monto=-1*$monto; 
            $texto = $app->applyReverseManual( $empresa, $tarjeta, $monto,$usuario,$descripcion,$motivo );
        }
        else
        {
            $texto='01NO SE CUENTAN CON SUFICIENTES FONDOS PARA EL REVERSO EN LA TARJETA.';
        }   
    }

  

    $Data=[$empresa,$monto,$texto,$tarjeta,1 ];
    $app->insertMovementAcount($Data);
    // se guardar el movimiento junto con su respuesta por parte del sistema
    if ( substr( $texto, 0, 2 ) === '00' )
    {
        $msg = "Hecho!";
    }
    else
    {
        $msg = "Error";
    }
    
}
include 'header.php';
$cards=$app->viewCards();

?>

<div class="content">
    <div class="container-fluid">
       
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><?php echo 'FONDEO EXITOSO #' . substr( $texto , 2 ); ?></span>
            </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
            <div class="alert alert-danger">
                <button type="button" aria-hidden="true" class="close">×</button>
                <?php 
                if(substr( $texto, 0, 4 ) == 'CURL')
                    {
                        $texto='01NO SE PUEDE REALIZAR EL FONDEO INTENTE MAS TARDE';
                    }
                    ?>
                <span><b> Error - </b> <?php echo substr( $texto, 2 ); ?></span>
            </div>
            <?php } ?>
          
            
            <div class="col-md-6">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Fondeo Individual de Tarjetas.</h4>
                        <p>Fondea la tarjeta de forma individual.</p>
                    </div>
                    <div class="content">
                        <form action="reload_funds_manual?scr=4" method="POST" id="form2" onsubmit="return checkSubmitBlock('btsubmit');">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputPerfil">Usuario</label>
                                        <select class="form-control  usuarios2" name="tarjeta" required onChange="mostrarsaldo(this.value)" >
                                        <?php for ( $i = 0; $i <= count( $cards ); $i += 2 ) {
                                            if ( isset( $cards[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $cards[ $i]; ?>"><?php echo strtoupper(utf8_encode($cards[ $i + 1 ])) . ' - ' . '****-****-'.substr($cards[$i], 0,4).'-'.substr($cards[$i], -4);; ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputSaldo">Saldo de la tarjeta</label>
                                        <input class="form-control" type="text" id="saldotarjeta1" value="0.00" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputAccion">Acción a realizar</label>
                                        <select class="form-control" name="inputacciontarjeta" required>
                                            <option value=""></option>
                                            <option value="FONDEAR">FONDEAR</option>
                                            <option value="REVERSAR">REVERSAR </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputPerfil">Monto</label>
                                        <input class="form-control" type="number" min="1" id="montotarjeta" name="amount" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputObservaciones">Motivo</label>
                                        <textarea rows="5" class="form-control" name="motivo" maxlength="100"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right" onclick="confirm_val_company('montotarjeta');"form="form2">Fondear</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
     

    </div>
</div>

<?php
echo "<script type='text/javascript'> $('.companias2').select2();</script>";
echo "<script type='text/javascript'> $('.usuarios2').select2();</script>";

include 'footer.php';