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
include 'header.php';

$app    = new app();
$msg    = FALSE;
$msg_layout=FALSE;
include_once 'email.php';
$email= new sendmail();
include_once 'msjs_mails.php';
$msjs_mails=new msjs_mails;        
$id_empresa=$_SESSION[ 'EMPRESA' ];

if ( isset( $_FILES['uploadfile'] ) )
{
    // Layout de carga
    $dir_subida = './uploads/';

    //$fichero_subido = $dir_subida . basename( $_FILES['uploadfile']['name'] );
    $fichero_subido = $dir_subida.'C'.date('dmyhis').'.csv';
    
    $csv_end = "\r\n";
    $csv_sep = ",";
    $csv = "";
    $csv .= 'tarjeta,monto,Respuesta'. $csv_end; // formato de respuesta
    $FileName = "uploads/RC".date('dmyhis').".csv"; // CSV de respuesta    
    
    $msg = ( move_uploaded_file( $_FILES['uploadfile']['tmp_name'], $fichero_subido ) )? TRUE: FALSE;
    $extension = strtolower(substr($_FILES['uploadfile']['name'], -4));
    $iduser=$Usuario=$_SESSION['USER'];
    $idempresa=$_SESSION['EMPRESA'];
    $accion="FONDEAR";

    if($extension=='.csv')
    {
        $fila=0;
        if (($gestor = fopen($fichero_subido, "r")) !== FALSE) 
        {  
            while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) 
            {
                $fila++; // numero de filas del archivo csv
            }
        }
        if($fila>1 and $fila<52)
        {
            if ( ( $fichero = fopen( $fichero_subido, 'r' ) ) !== FALSE ) 
            {
                $Depositos  = 0;
                $Total      = 0;
                $Registros  = 0;
                $Registros_fallos=0;
                $NumFilas=0;
                while ( ( $datos = fgetcsv( $fichero, 1000 ) ) !== FALSE ) 
                {
                    if ($NumFilas>0) 
                    {
                        if(sizeof($datos)==2) // Si solo trae el credito
                        {
                            // idcard	amount	
                            //42424242	  1
                            $monto=$datos[1];
                            $check_card=$app->getCardSystem($datos[0],$idempresa); // Verifica que la tarjeta sea de la empresa
                            if($monto<=0)
                            {
                                $Respuesta='01MONTO NO VALIDO';
                            }
                            elseif(strlen($datos[0])!=8)
                            {
                                $Respuesta='01TARJETA NO VALIDA';
                            }
                            elseif(sizeof($check_card)==0)
                            {
                                $Respuesta='01LA TARJETA NO ESTA REGISTRADA EN EL SISTEMA';
                            }
                            
                            else
                            {
                                $fecha=date('Y-m-d H:i:s');
                                $tarjeta=$datos[0];
                                $monto_fondeo=$datos[1];
                                $saldo_tarjeta=$app->check_amountCard($tarjeta);  // Obtiene el saldo de la tarjeta
                                $Type_product=$app->getIdProductByCard($tarjeta); //get data MasterAcount
                                $saldo_master=$app->getAmountMasterAcount($Type_product[2],$Type_product[3]); // Get Balance MasterAcount Obtiene el saldo de la cuenta maestra y el nombre de la misma        
                                $datacompany=$app->viewDataCompanysById($idempresa); // obtiene los datos de la empresa
                                $validar_tarjeta=$app->getCardDisable($tarjeta); // Verifica que la tarjeta no este inhabilitada
                                $saldo_empresa=$datacompany[5];
                                
                                if ( $saldo_empresa >= $datos[1] )
                                {   // compara la tarjeta el monto y la fecha para saber si existe un registro en diferencia de 5 minutos
                                    $res=$app->getTransactionsCard($idempresa,$tarjeta,$monto_fondeo,$fecha); // compara la tarjeta el monto y la fecha para saber si existe un registro en diferencia de 5 minutos
                                    if ( floatval($saldo_master)>=$datos[1])  // si el monto cuenta maestra es mayor al que se desea traspasar a una tarjeta
                                    {
                                        if(sizeof($res)>0)
                                        {
                                            $Respuesta='01NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE ESPERE 5 MINUTOS O MODIFIQUE EL MONTO';
                                        }
                                        else
                                        {
                                            if( $validar_tarjeta>2 )
                                            {
                                                $Respuesta='01NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE INTENTE MAS TARDE';
                                                $texto='01NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE INTENTE MAS TARDE';
                                                $descripcion='TRES INTENTOS DE FONDEO A TARJETA ENERGEX NO COMPLETADOS';   
                                                $titulo_correo='NOTIFICACION DE FONDEOS A TARJETA ENERGEX NO COMPLETADOS'; 
                                                $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                
                                                $msj=$msjs_mails->Error_Found_card($dataerror); // construye el mensaje a enviar  
                                                $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                                                $datalogmail=[1,$msj,$respuesta_mail];
                                                $app->insertlogemail($datalogmail);
                                            }
                                            else
                                            {
                                                $concepto='FONDEO A TARJETA';
                                                $comentario='FONDEO POR LAYOUT';
                                                //Data=[empresa,saldo cuenta maestra,saldo empresa,Monto fondeo,Tarjeta,saldo_tarjeta,Accion]
                                                $Data=[$datacompany[1], $accion, $tarjeta, $monto_fondeo, $concepto];                    
                                                $msj_aplicacion_pagos=$msjs_mails->Found_card($Data); // construye el mensaje a enviar
                                                $Respuesta = $app->applyFunds( $idempresa, $tarjeta, $monto_fondeo, $concepto, $comentario, $iduser );
                                                
                                                if(substr( $Respuesta, 0, 2 ) !== '00') // Error de respuesta de la API
                                                {
                                                    $data_incident=[$idempresa,$tarjeta,$monto_fondeo,$Respuesta,$iduser];
                                                    $idincident=$app->insertlogincidentsPays($data_incident);  // inserta la tarjeta a la tabla de incidentes
                                                    sleep(60); // espera30 segundos para verificar el saldo
                                                    $saldo_tarjeta_new=$app->check_amountCard($tarjeta); // Verifica el saldo de la tarjeta
                                                    if($saldo_tarjeta_new==($saldo_tarjeta+$monto_fondeo)) // Si el saldo actual es mayor al saldo anterior en la tarjeta
                                                    {
                                                        $Respuesta = $app->applyFundsVirtual( $idempresa, $tarjeta, $monto_fondeo, $concepto, $comentario, $iduser );
                                                        $app->UpdateCardIncidents($idincident,$tarjeta); // actualiza el estatus para liberar la tarjeta
                                                        $titulo_correo='NOTIFICACION DE PAGOS LAYOUT A TARJETA ENERGEX';
                                                        $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                        $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                                        $app->insertlogemail($datalogmail);  
                                                    }
                                                    else
                                                    {
                                                        $descripcion='INTENTO DE FONDEO LAYOUT A TARJETA NO COMPLETADO';
                                                        $titulo_correo='NOTIFICACION DE FONDEO LAYOUT A TARJETA ENERGEX NO EFECTUADO'; 
                                                        //Data=[empresa,Monto,Tarjeta,Accion,concepto]
                                                        $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                    
                                                        $msj=$msjs_mails->Error_Found_card($dataerror); // construye el mensaje a enviar
                                                        $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                                                        $datalogmail=[$idincident,$msj,$respuesta_mail];
                                                        $app->insertlogemail($datalogmail);
                                                        $Respuesta='01NO ES POSIBLE FONDEAR ESTA TARJETA ACTUALMENTE';
                                                    }
                                                }
                                                else
                                                {
                                                    $titulo_correo='NOTIFICACION DE FONDEO LAYOUT A TARJETA ENERGEX';
                                                    $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                    $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                                    $app->insertlogemail($datalogmail);  
                                                }
                                            }    
                                        }
                                    }
                                    else
                                    {
                                        $Respuesta='01NO ES POSIBLE FONDEAR INTENTE MAS TARDE.';
                                        $descripcion='NO SE CUENTAN CON FONDOS EN LA CUENTA MAESTRA';   
                                        $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                     
                                        $titulo_correo='FONDOS INSUFICIENTES CUENTA MAESTRA '.$Type_product[1]; 
                                        $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion,$saldo_master];                                                     
                                        $msj=$msjs_mails->Error_Found_MA($dataerror); // construye el mensaje a enviar  
                                        $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                                        $datalogmail=[1,$msj,$respuesta_mail];
                                        $app->insertlogemail($datalogmail);
                                    }
                                }
                                else
                                {
                                    $Respuesta='01NO SE CUENTAN CON SUFICIENTES FONDOS PARA EL FONDEO.';
                                }
                            }
                            $csv.=$datos[0].$csv_sep.$datos[1].$csv_sep.substr( $Respuesta , 2 ).$csv_end;
                            $Data=[$idempresa,$monto_fondeo,substr( $Respuesta , 2 ), $tarjeta,2];
                            $app->insertMovementAcount($Data);
    
                            if ( substr( $Respuesta, 0, 2 ) == '00' )
                            {
                                $Depositos  = $Depositos + 1;
                                $Total      = $Total + $datos[1];
                                $Registros  = $Registros + 1;
                            }
                            else
                            {
                                $Registros_fallos  = $Registros_fallos + 1;
                            }   
                        }
                        else
                        {
                            $csv.=$datos[0].$csv_sep.$datos[1].','.'ERROR EN EL NUMERO DE CAMPOS'.$csv_end;                
                        }
                
                    }
                    $NumFilas++;
                }  
            }

            if (!$handle = fopen($FileName, "w")) {
                echo "Cannot open file.";
                exit;
            }
        
            if (fwrite($handle, utf8_decode($csv)) === FALSE) {
                echo "Cannot write to file.";
                exit;
            }
            fclose($handle);
            
            if ( $Total > 0 )
            {
                $msg    = 'Hecho!';
                $texto  = '00Se transfirieron ' . $Depositos . ' depositos de los ' . $Registros . ' registros por un total de $ ' . number_format( floatval( $Total ), 2, '.', ',' );
            }
            else
            {
                $msg    = 'Error';
                $texto  = '01No se transfirieron ' . $Registros_fallos . ' registros, intente mas tarde.';
            }
        
            $Data_layout=[$idempresa,$fichero_subido,$FileName];
            $app->insertMovementLayout($Data_layout);
        
            $msg_layout = 
            '<div class="alert alert-success">
                <span><b> Atenci&oacute;n:</b><br>
                    <a class="btn btn-success btn-fill" href="' . $FileName . '" >Reporte de Carga
                    </a><br>
                        El archivo se subio de forma exitosa al servidor.
                </span>
            </div>';
        }
        else
        {
            $msg    = 'Error';
            $texto  = '01Se excede el numero de columnas del archivo'; 
        }

    }else
    {
        $msg    = 'Error';
        $texto  = '01Extensión de archivo no valido';
    }
}
elseif ( isset( $_POST['inputPerfil'] ) AND $_POST['inputPerfil']>0 AND isset($_SESSION["EMPRESA"]) AND !is_null($_SESSION["EMPRESA"]))
{
    $accion         = $_POST["inputacciontarjeta"]; // accion seleccionado
    $tarjeta        = substr($_POST["inputPerfil"],-8); // tarjeta a 8 digitos
    $comentario     = ''; // comentario
    $monto_fondeo   = $_POST['amount']; // monto del fondeo
    $idempresa      = $_SESSION['EMPRESA'];
    $iduser         = $_SESSION['USER'];
    $fecha=date('Y-m-d H:i:s');

    if(isset($_POST['comentario']))
    {
        $comentario=$_POST['comentario'];
    }

    $saldo_tarjeta=$app->check_amountCard($tarjeta);  // Obtiene el saldo de la tarjeta
    $Type_product=$app->getIdProductByCard($tarjeta); //get data MasterAcount
    $saldo_master=$app->getAmountMasterAcount($Type_product[2],$Type_product[3]); // Get Balance MasterAcount Obtiene el saldo de la cuenta maestra y el nombre de la misma        
    $datacompany=$app->viewDataCompanysById($idempresa); // obtiene los datos de la empresa
    $validar_tarjeta=$app->getCardDisable($tarjeta); // Verifica que la tarjeta no este inhabilitada     
    $saldo_empresa  = $datacompany[5];
            
    if($accion=="FONDEAR")
    {
        if ( $saldo_empresa >= $monto_fondeo )  // si el monto asignado a la empresa es mayor al que se desea traspasar a una tarjeta
        {
            if ( floatval($saldo_master)>=$monto_fondeo)  // si el monto cuenta maestra es mayor al que se desea traspasar a una tarjeta
            {
                $res=$app->getTransactionsCard($idempresa,$tarjeta,$monto_fondeo,$fecha); // compara la tarjeta el monto y la fecha para saber si existe un registro en diferencia de 5 minutos
                if(sizeof($res)>0)
                {
                    $texto='01NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE ESPERE 5 MINUTOS O MODIFIQUE EL MONTO';
                }
                else
                {
                    if( $validar_tarjeta>=3 ) // 3 intentos fallidos con una sola tarjeta
                    {
                        $texto='01NO SE PUEDE FONDEAR ESTA TARJETA ACTUALMENTE INTENTE MAS TARDE';
                        $descripcion='TRES INTENTOS DE FONDEO A TARJETA ENERGEX NO COMPLETADOS';   
                        $titulo_correo='NOTIFICACION DE FONDEOS A TARJETA ENERGEX NO COMPLETADOS'; 
                        $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                
                        $msj=$msjs_mails->Error_Found_card($dataerror); // construye el mensaje a enviar  
                        $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                        $datalogmail=[1,$msj,$respuesta_mail];
                        $app->insertlogemail($datalogmail);
                    }
                    else
                    {
                        $concepto='FONDEO A TARJETA';
                        //Data=[empresa,Monto,Tarjeta,Accion,concepto]
                        $Data=[$datacompany[1], $accion, $tarjeta, $monto_fondeo, $concepto];                    
                        $msj_aplicacion_pagos=$msjs_mails->Found_card($Data); // construye el mensaje a enviar
                        $texto = $app->applyFunds( $idempresa, $tarjeta, $monto_fondeo, $concepto, $comentario, $iduser );
                        if(substr( $texto, 0, 2 ) !== '00') // si ocurre algun error en la aplicacion del pago
                        {
                            $data_incident=[$idempresa,$tarjeta,$monto_fondeo,$texto,$iduser];
                            $idincident=$app->insertlogincidentsPays($data_incident);  // inserta la tarjeta a la tabla de incidentes
                            sleep(60); // espera30 segundos para verificar el saldo
                            $saldo_tarjeta_new=$app->check_amountCard($tarjeta); // Verifica el saldo de la tarjeta
                            if($saldo_tarjeta_new==($saldo_tarjeta+$monto_fondeo)) // Si el saldo actual es mayor al saldo anterior en la tarjeta
                            {
                                $texto = $app->applyFundsVirtual( $idempresa, $tarjeta, $monto_fondeo, $concepto, $comentario, $iduser );
                                $app->UpdateCardIncidents($idincident,$tarjeta); // actualiza el estatus para liberar la tarjeta
                                $titulo_correo='NOTIFICACION DE PAGOS A TARJETA ENERGEX';
                                $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                $app->insertlogemail($datalogmail);  
                            }
                            else
                            {
                                $descripcion='INTENTO DE FONDEO A TARJETA NO COMPLETADO';
                                $titulo_correo='NOTIFICACION DE FONDEO A TARJETA ENERGEX NO EFECTUADO';
                                //Data=[empresa,Monto,Tarjeta,Accion,concepto]
                                $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                
                                $msj=$msjs_mails->Error_Found_card($dataerror); // construye el mensaje a enviar                                  
                                $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                                $datalogmail=[$idincident,$msj,$respuesta_mail];
                                $app->insertlogemail($datalogmail);
                                $texto='02NO SE APLICO EL FONDEO, INTENTE MAS TARDE';
                            }
                            
                        }
                        else
                        {
                            $titulo_correo='NOTIFICACION DE FONDEO A TARJETA ENERGEX';
                            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                            $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                            $app->insertlogemail($datalogmail);
                        }
                    }
                }
            }
            else // No se cuentan con fondos en la cuenta maestra para el fondeo
            {
                $texto='02NO ES POSIBLE FONDEAR INTENTE MAS TARDE.';
                $descripcion='NO SE CUENTAN CON FONDOS EN LA CUENTA MAESTRA';   
                $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                     
                $titulo_correo='FONDOS INSUFICIENTES CUENTA MAESTRA '.$Type_product[1]; 
                $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion,$saldo_master];                                                     
                $msj=$msjs_mails->Error_Found_MA($dataerror); // construye el mensaje a enviar  
                $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                $datalogmail=[1,$msj,$respuesta_mail];
                $app->insertlogemail($datalogmail);
            }    
        }
        else
        {
            $texto='01NO SE CUENTAN CON SUFICIENTES FONDOS PARA EL FONDEO.';   
        }
    }
    elseif($accion=="REVERSAR")
    {
        if( $monto_fondeo <= $saldo_tarjeta )
        {
            $monto_fondeo   = -1*$monto_fondeo;
            $res            = $app->getTransactionsCard($idempresa,$tarjeta,$monto_fondeo,$fecha); // compara la tarjeta el monto y la fecha para saber si existe un registro en diferencia de 5 minutos
            // verifica que no exista un registro duplicado en un termino de 5 minutos
            if(sizeof($res)>0)
            {
                $texto='01NO SE PUEDE REVERSAR ESTA TARJETA ACTUALMENTE ESPERE 5 MINUTOS O MODIFIQUE EL MONTO';
            }
            else
            {
                if( $validar_tarjeta >= 3)
                {
                    $texto='01NO SE PUEDE REVERSAR ESTA TARJETA ACTUALMENTE INTENTELO MAS TARDE';
                    $descripcion='TRES INTENTOS DE REVERSO A TARJETA ENERGEX NO COMPLETADOS';   
                    $titulo_correo='NOTIFICACION DE REVERSOS A TARJETA ENERGEX NO COMPLETADOS'; 
                    $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                
                    $msj=$msjs_mails->Error_Found_card($dataerror); // construye el mensaje a enviar  
                    $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                    $datalogmail=[1,$msj,$respuesta_mail];
                    $app->insertlogemail($datalogmail);
                }
                else
                {
                    $concepto='REVERSO A TARJETA';
                    //Data=[empresa,saldo cuenta maestra,saldo empresa,Monto fondeo,Tarjeta,saldo_tarjeta,Accion]
                    $Data=[$datacompany[1], $accion, $tarjeta, $monto_fondeo, $concepto];
                    $msj_aplicacion_pagos=$msjs_mails->Found_card($Data); // construye el mensaje a enviar
                    $texto = $app->reverseFunds( $idempresa, $tarjeta, $monto_fondeo, $concepto, $comentario, $iduser );
                    if(substr( $texto, 0, 2 ) !== '00')
                    {
                        $data_incident=[$idempresa,$tarjeta,$monto_fondeo,$texto,$iduser];
                        $idincident=$app->insertlogincidentsPays($data_incident);  // inserta la tarjeta a la tab$saldo_tarjeta,la de incidentes
                        sleep(60); // espera80 segundos para verificar el saldo
                        $saldo_tarjeta_new=$app->check_amountCard($tarjeta); // Verifica el saldo de la tarjeta
                        if( $saldo_tarjeta_new == ($saldo_tarjeta+$monto_fondeo) ) // Si el saldo actual es mayor al saldo anterior en la tarjeta
                        {
                            $texto = $app->reverseFundsVirtual( $idempresa, $tarjeta, $monto_fondeo, $concepto, $comentario, $iduser );
                            $app->UpdateCardIncidents($idincident,$tarjeta); // actualiza el estatus para liberar la tarjeta
                            $titulo_correo='NOTIFICACION DE REVERSO A TARJETA ENERGEX';
                            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                            $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                            $app->insertlogemail($datalogmail);  
                        }
                        else
                        {
                            $descripcion='INTENTO DE REVERSO A TARJETA NO COMPLETADO';
                            $titulo_correo='NOTIFICACION DE REVERSO A TARJETA ENERGEX NO EFECTUADO';
                            //Data=[empresa,Monto,Tarjeta,Accion,concepto]
                            $dataerror=[$datacompany[1],$accion,$tarjeta,$monto_fondeo,$descripcion];                                            
                            $msj=$msjs_mails->Error_Found_card($dataerror); // construye el mensaje a enviar                              
                            $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                            $datalogmail=[$idincident,$msj,$respuesta_mail];
                            $app->insertlogemail($datalogmail);
                            $texto='02NO SE APLICO EL REVERSO, INTENTE MAS TARDE'; 
                        }
                    }
                    else
                    {
                        $titulo_correo='NOTIFICACION DE REVERSO A TARJETA ENERGEX';
                        $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                        $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                        $app->insertlogemail($datalogmail);
                    }
                }
            }
        }
        else
        {
            $texto='01NO SE CUENTAN CON SUFICIENTES FONDOS PARA EL REVERSO.';
        }
    }
    $Data=[$idempresa,$monto_fondeo,substr( $texto , 2 ), $tarjeta,1];
    $app->insertMovementAcount($Data);
    // se guardar el movimiento junto con su respuesta por parte del sistema
    if ( substr( $texto, 0, 2 ) === '00' )
    {
        $msg = "Hecho!";
    }
    elseif(substr( $texto, 0, 2 ) === '02')
    {
        $msg = "Warning";
    }
    else
    {

        $msg = "Error";
    }
    
}

if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt28') OR $app->optSearch($_SESSION[ 'PERMISOS' ],'opt29') ) 
{
    $administradoraBalance  = ( $empresaMonto = $app->viewDataCompanysById( $id_empresa ) )? $empresaMonto[5] : 0;
}
$Empleados=$app->viewCardsByCompany($id_empresa);

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card ">
                    <div class="header">
                        <h1 class="title"><i class="pe pe-7s-safe pe-2x pull-left pe-border"></i></h1>
                        <p class="category"><br><span style="font-size:18px;"><b>&nbsp;&nbsp;$ <?php echo number_format( floatval( $administradoraBalance ), 2, '.', ',' ); ?></b></span><br><span style="font-size:18px;">Total Disponible</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><?php echo 'SUCCESSFUL #' . substr( $texto , 2 ); ?></span>
            </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
            <div class="alert alert-danger">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><b> Error - </b> <?php echo substr( $texto, 2 ); ?></span>
            </div>
            <?php } elseif ( strlen( $msg ) == 7 ) { ?>
            <div class="alert alert-warning">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><b> Alerta - </b> <?php echo substr( $texto, 2 ); ?></span>
            </div>
            <?php } ?>
            <?php if (isset( $msg_layout ) ) {
                echo $msg_layout;} ?> 
            
            
            <?php if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt29')) { // Fondeo por layout?>
            <div class="col-md-6">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Fondeo Masivo de Tarjetas</h4> -->
                        <h4 class="title">Massive Card Funding</h4>
                        <!-- <p>Fondea de forma masiva mediante un formato de carga(Max. 50 registros por carga).</p> -->
                        <p>Fund in bulk using an upload format (Max. 50 register for upload).</p>
                    </div>
                    <div class="content">
                        <form action="reload_funds_cards?scr=4" method="POST" enctype="multipart/form-data" id="form1" onsubmit="return checkSubmitBlock('btsubmit3');">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label>Cargar Archivo</label> -->
                                        <label>File upload</label>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                        <input type="file" name="uploadfile" class="form-control" required >
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btsubmit3" class="btn btn-info btn-fill pull-right" form="form1">Found</button>
                            <a class="btn btn-default btn-fill pull-left" href="./downloads/layout_founds.csv">Download Layout</a>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt28')) { // Fondeo individual a las tarjetas?>
            <div class="col-md-6">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Fondeo Individual de Tarjetas.</h4> -->
                        <h4 class="title">Individual Funding of Cards.</h4>
                        <!-- <p>Fondea la tarjeta de forma individual.</p> -->
                        <p>Fund the card individually.</p>
                    </div>
                    <div class="content">
                        <form action="reload_funds_cards?scr=4" method="POST" id="form2" onsubmit="checkSubmitBlock('btsubmit');">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label for="inputPerfil">Usuario</label> -->
                                        <label for="inputPerfil">User</label>
                                        <select class="form-control usuarios2" name="inputPerfil" required onChange="mostrarsaldo(this.value)">
                                        <option value=""></option>
                                        <?php 
                                        for ($i = 0; $i < sizeof( $Empleados ); $i += 2) 
                                        {
                                            if ( isset( $Empleados[ $i ] ) ) 
                                            {
                                        ?>  
                                            <option value="<?php echo $Empleados[$i]; ?>"><?php echo strtoupper(utf8_encode($Empleados[ $i + 1 ])). '    ****-****-' . substr($Empleados[$i],0,4) . '-' . substr($Empleados[$i],-4);; ?></option>
                                        <?php                                                         
                                            } 
                                        } 
                                        ?>               
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label for="inputSaldo">Saldo de la tarjeta</label> -->
                                        <label for="inputSaldo">Card balance</label>
                                        <input class="form-control" type="text" id="saldotarjeta1" value="0.00" disabled/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label for="inputAccion">Acción a realizar</label> -->
                                        <label for="inputAccion">Action</label>
                                        <select class="form-control" name="inputacciontarjeta" required>
                                            <option value=""></option>
                                            <option value="FONDEAR">FUND</option>
                                            <option value="REVERSAR">REVERSE</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputPerfil">Amount</label>
                                        <input class="form-control" name="inputEmpresa" type="hidden" value="<?php echo $_SESSION['EMPRESA']; ?>">
                                        <input class="form-control" name="saldoEmpresa" type="hidden" value="<?php echo $administradoraBalance; ?>">
                                        <input class="form-control" type="number" min="1" id="amount" name="amount" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputObservaciones">COMMENT</label>
                                        <textarea rows="5" class="form-control" name="comentario" maxlength="100"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" id="btsubmit" onclick="confirm_form('amount');" class="btn btn-info btn-fill pull-right" form="form2">Fund</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
     
        <?php } ?>
        
    </div>
</div>

<?php
echo "<script type='text/javascript'> $('.companias2').select2();</script>";

echo "<script type='text/javascript'> $('.usuarios2').select2();</script>";
include 'footer.php';