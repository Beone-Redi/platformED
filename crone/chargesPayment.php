<?php 
/**
 * Se define el área geografica.
 */
date_default_timezone_set( 'America/Monterrey' );
include 'email.php';
include "core.php";
    $email= new sendmail();
    $app = new app();
    $mes=date('Y-m');
    //var_dump($mes);
    $DataCards=$app->getDataPayNet($mes);
    /*
    $DataCards=[
        idmov,card,monto,concepto
    ]
    */
    if(sizeof($DataCards)>0)
    {
        for($i=0;$i<sizeof($DataCards);$i+=5)
        {
            $numIntentosPayment=$app->getNumbersIntentPayment($DataCards[$i+1],$DataCards[$i]);
            if($numIntentosPayment<=4)
            {
                $res=$app->ApplyCharges($DataCards[$i+1],$DataCards[$i+2],$DataCards[$i+3],$DataCards[$i],$DataCards[$i+4]);
                if ( substr( $res, 0, 2) <> '01' )
                {
                    $app->updateStatusPayNet($DataCards[$i],'2',$res);
                }
            }
            else
            {
                $mail_mensaje='<table>
                <tr><td> Tarjeta </td><td> '.$DataCards[$i+1].'</td></tr>'.
                '<tr><td> Monto del cobro </td><td> '.$DataCards[$i+2].'</td></tr>'.
                '<tr><td> IdMovement </td><td> '.$DataCards[$i].'</td></tr>'.
                '<tr><td> Motivo Error </td><td> Cuatro Intentos de Cobro sin exito Comision Paynet</td></tr></table>';
                $email->enviarmail($mail_mensaje,'ERROR DE COBRO PAYNET ENERGEX DOLARES');
                echo "enviar correo";
            }
        }
    }
    else
    {
        echo "no entro";
    }
    

?>