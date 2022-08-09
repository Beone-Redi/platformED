<?php 
/**
 * Se define el área geografica.
 */
date_default_timezone_set( 'America/Monterrey' );
include 'email.php';
include "core.php";
    $app = new app();
    $email= new sendmail();
    
    $mes=date('Y-m');
    $DataPayment=$app->getDataPayment($mes); // Obtiene los datos de las cuentas de la plataforma(Nombre,AgregamentId,ProductId)
    /*$DataPayment=
    [ide,tarjeta,monto,concepto,authcode,company,idtype]
    */
    //var_dump($DataPayment);
    for($i=0;$i<sizeof($DataPayment);$i+=7)
    {
        sleep(2); // tiempo 2 segundos
        if($app->ValidateAuthCode($DataPayment[$i+4])=='') // si no existe el codigo de autorizacion
        {
            if($DataPayment[$i+6]==1) //abono a tarjeta
            {   //abono a tarjeta
                $Res=$app->applyFundsAPI($DataPayment[$i+1],$DataPayment[$i+5],$DataPayment[$i+2],$DataPayment[$i+3],$DataPayment[$i+4]);

                if(substr( $Res, 0, 2 ) == '00')
                {
                    
                    $Res2=$app->updateStatusPayment($DataPayment[$i]);
                }
                else
                {
                    $mail_mensaje='<table>
                    <tr><td> Plataforma </td><td> GNT</td></tr>'.
                    '<tr><td> IdMovement </td><td> '.$DataPayment[$i].'</td></tr>'.
                    '<tr><td> Motivo Error </td><td> Falla registro Movimiento API ENERGEX DOLARES </td></tr></table>';
                    $email->enviarmail($mail_mensaje,'ERROR DE REGISTRO ENERGEX DOLARES FONDEO TARJETA');
                }
            }
            elseif($DataPayment[$i+6]=2) //cargo a tarjeta(Reverso a tarjeta)
            {
                $Res=$app->reverseFundsAPI($DataPayment[$i+1],$DataPayment[$i+5],$DataPayment[$i+2],$DataPayment[$i+3],$DataPayment[$i+4]);
                if(substr( $Res, 0, 2 ) == '00')
                {
                    $Res2=$app->updateStatusPayment($DataPayment[$i]);
                }
                else
                {
                    $mail_mensaje='<table>
                    <tr><td> Plataforma </td><td> GNT</td></tr>'.
                    '<tr><td> IdMovement </td><td> '.$DataPayment[$i].'</td></tr>'.
                    '<tr><td> Motivo Error </td><td> Falla registro Movimiento API ENERGEX DOLARES </td></tr></table>';
                    $email->enviarmail($mail_mensaje,'ERROR DE REGISTRO ENERGEX DOLARES REVERSO TARJETA');
                }
            }
        }
        else
        {
            $Res2=$app->updateStatusPaymentDuplied($DataPayment[$i]);
            echo "codigo repetido";
        }
    }
       
?>