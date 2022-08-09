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
include_once 'email.php';
$email= new sendmail();
$msg    = FALSE;
$msg_layout=FALSE;
$company=$app->getCompanyUser($_SESSION["USER"]);

if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt22') OR $app->optSearch($_SESSION[ 'PERMISOS' ],'opt23')) 
{
    $administradoraBalance  = ( $empresaMonto = $app->viewCompany( $company ) )? $empresaMonto[11] : 0;
    $Empleados              = $app->viewEmployees( $company );
}
elseif( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt17') )
{
    $masterBalance=$app->getAmountCountMaster();
    $administradoraBalance  = $app->abUpdate(floatval($masterBalance) );
    $Empresas               = $app->viewCompanys();
}

if ( isset( $_FILES['uploadfile'] ) )
{
    // Layout de carga
    $dir_subida = './uploads/';
    $comentario='Funding by layout';
    //$fichero_subido = $dir_subida . basename( $_FILES['uploadfile']['name'] );
    $fichero_subido = $dir_subida.'C'.date('dmyhis').'.csv';
    
    $csv_end = "\r\n";
    $csv_sep = ",";
    $csv = "";
    $csv .= 'idcard,amount,Answerd'. $csv_end; // formato de respuesta
    $FileName = "uploads/RC".date('dmyhis').".csv"; // CSV de respuesta
      
    $extension = strtolower(substr($_FILES['uploadfile']['name'], -4));
    $datacompany=$app->viewECompanys($_SESSION['EMPRESA']); 
    $datauser=$app->viewUsersByIde($_SESSION['USER']);

    $msg = ( move_uploaded_file( $_FILES['uploadfile']['tmp_name'], $fichero_subido ) )? TRUE: FALSE;

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
                        if(sizeof($datos)==2) // Debe traer tarjeta y monto a fondear en el layout
                        {
                            // Procesar los datos.
                            //idcard	amount	 
                            //424242	  1000
                            
                            $monto=$datos[1];
                            $datos[0]=str_pad($datos[0],8,"0",STR_PAD_LEFT);
                            $check_card=$app->getCardSystem($datos[0],$_SESSION['EMPRESA']);
                    
                            if($monto<=0)
                            {
                                $Respuesta='01AMOUNT NOT VALID';
                            }
                            elseif(strlen($datos[0])!=8)
                            {
                                $Respuesta='01IDCARD NOT VALID';
                            }
                            elseif(sizeof($check_card)==0)
                            {
                                $Respuesta='01THE CARD DO NOT REGISTER IN THE SYSTEM';
                            }
                            else
                            {
                                $tarjeta=str_pad($datos[0],8,"0",STR_PAD_LEFT);
                                $masterBalance=$app->getAmountCountMaster();
                                $new_MB  = floatval( $masterBalance) ;
                                $fechanueva=date('Y-m-d H:i:s');
                                $res=$app->getTransactionsCard($_SESSION['EMPRESA'],$datos[0],$datos[1],$fechanueva); // compara la tarjeta el monto y la fecha para saber si existe un registro en diferencia de 5 minutos
                                if(sizeof($res)>0)
                                {
                                    $Respuesta='01YOU CANNOT FUND THIS CARD CURRENTLY WAIT 5 MINUTES OR MODIFY THE AMOUNT';
                                }
                                else
                                {
                                    $administradoraBalance  = ( $empresaMonto = $app->viewCompany( $_SESSION[ 'EMPRESA' ] ) )? $empresaMonto[11] : 0;
                                    //$administradoraBalance obtiene el monto actual de la empresa
                                
                                    if ( $administradoraBalance >= $datos[1] AND $datos[1]<=$new_MB) // compara si existe fondos en la empresa y la cuenta maestra
                                    {
                                        $validar_tarjeta=$app->getCardEnabled($tarjeta,date('Y-m-d H:i:s'));
                                        if(sizeof($validar_tarjeta)>0)
                                        {
                                            $Respuesta='01YOU CANNOT FUND THIS CARD TRY AGAIN LATER';
                                        }
                                        else
                                        {
                                            $tarjeta_completa='55135300'.$tarjeta;
                                            $saldo_tarjeta=$app->check_amountCard($tarjeta_completa); // Verifica el saldo de la tarjeta
                                            $saldo_master=$app->getAmountCountMaster();

                                            $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($datacompany[1]).'</b></h3>-----------------------<br>';

                                            $table_mensaje='<table>
                                                <tr><td> Saldo Cuenta Maestra </td><td>$US '.number_format(substr($saldo_master,2),2,'.',',').'</td></tr>'.
                                                '<tr><td> Saldo Empresa </td><td>$US '.number_format($administradoraBalance,2,'.',',').'</td></tr>'.
                                                '<tr><td> Monto del fondeo </td><td>$US '.number_format($datos[1],2,'.',',').'</td></tr>'.
                                                '<tr><td> Tarjeta </td><td> ****-****-'.str_pad(substr($tarjeta, 0,-4),4, "**", STR_PAD_LEFT).'-'.substr($tarjeta, -4).'</td></tr>'.
                                                '<tr><td> Saldo Tarjeta </td><td>$US '.number_format($saldo_tarjeta,2,'.',',').'</td></tr>'.
                                                '<tr><td> Accion </td><td> FONDEAR</td></tr></table>';
                                            $msj_aplicacion_pagos.=$table_mensaje;
                                            //$Respuesta='00SIMULACION DE FONDEO CORRECTA';
                                            $Respuesta  = $app->applyFunds( $_SESSION['EMPRESA'] , $tarjeta, $datos[1],$saldo_tarjeta,$_SESSION['USER'],$saldo_master,$comentario );
                                            if(substr( $Respuesta, 0, 2 ) !== '00') // Error de respuesta en la api
                                            {
                                                $data_incident=[$_SESSION['EMPRESA'],$tarjeta,$datos[1],$Respuesta,$_SESSION['EMPRESA']];
                                                $idincident=$app->insertlogincidentsPays($data_incident);    
                                                $msj='TARJETA : '.$tarjeta.', Monto : $ '.$datos[1].' Error : '.$Respuesta.' EMPRESA : '.$_SESSION['EMPRESA'].' Accion: FONDEO';
                                                $respuesta_mail=$email->enviarmail($msj);
                                                $datalogmail=[$idincident,$msj,$respuesta_mail];
                                                $app->insertlogemailslIncidents($datalogmail);
                                                if($respuesta_mail!=202)
                                                {
                                                    $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                    $datalogmail=[$idincident,$msj_aplicacion_pagos,$respuesta_mail];
                                                    $app->insertlogemailslIncidents($datalogmail);
                                                }
                                                sleep(30); // Espera 30 segundos
                                                $saldo_tarjeta_new=$app->check_amountCard($tarjeta_completa);// Verifica el saldo de la tarjeta
                                                if($saldo_tarjeta_new==($saldo_tarjeta+$datos[1])) // Si el saldo actual es mayor al saldo anterior en la tarjeta
                                                {
                                                    $Respuesta  = $app->applyFundsVirtual( $_SESSION['EMPRESA'] , $tarjeta, $datos[1],$saldo_tarjeta,$_SESSION['USER'],$saldo_master,$comentario ); 
                                                    // ]Envia datos a la aplicacion de pagos virtual
                                                    if(substr( $Respuesta, 0, 2 ) === '00')
                                                    {
                                                        $app->UpdateCardIncidents($idincident,$tarjeta);
                                                        $titulo_correo='NOTIFICACION DE FONDEO A TARJETA GOCARD';
                                                        $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                        $datalogmail=[$idincident,$msj_aplicacion_pagos,$respuesta_mail];
                                                        $app->insertlogemailslIncidents($datalogmail); 
                                                        if($respuesta_mail!=202)
                                                        {
                                                            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                            $datalogmail=[$idincident,$msj_aplicacion_pagos,$respuesta_mail];
                                                            $app->insertlogemailslIncidents($datalogmail);
                                                        }
                                                    }
                                                }
                                            }
                                            else
                                            {
                                                $titulo_correo='NOTIFICACION DE FONDEO A TARJETA ENERGEX';
                                                $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                                $app->insertlogemailslIncidents($datalogmail);  
                                                if($respuesta_mail!=202)
                                                {
                                                    $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                                    $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                                    $app->insertlogemailslIncidents($datalogmail);
                                                }
                                            }
                                        }
                                        
                                    }
                                    else
                                    {
                                        $Respuesta='01THEY DO NOT HAVE ENOUGH FUNDS FOR THE FUNDING.';
                                    }
                                }
                            }
                            $csv.=$datos[0].$csv_sep.$datos[1].$csv_sep.substr( $Respuesta , 2 ).$csv_end;
                   
                            $Data=[$_SESSION['EMPRESA'],$datos[1],substr( $Respuesta , 2 ),$datos[0]];
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
                            $csv.=$datos[0].$csv_sep.$datos[1].',,,,'.'ERROR IN THE NUMBER OF FIELDS'.$csv_end;                
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
                $texto  = '00were transferred ' . $Depositos . ' deposits of ' . $Registros . ' register for a total of U$S ' . $Total;
            }
            else
            {
                $msg    = 'Error';
                $texto  = '01Not were transferred ' . $Registros_fallos . ' registrer, try again later.';
            }
        
            $Data_layout=[$_SESSION["EMPRESA"],$fichero_subido,$FileName];
            $app->insertMovementLayout($Data_layout);
        
            $msg_layout = 
            '<div class="alert alert-success">
                <span><b> Attention:</b><br>
                    <a class="btn btn-success btn-fill" href="' . $FileName . '" >Download Report
                    </a><br>
                    The file was successfully uploaded to the server.
                </span>
            </div>';
        }
        else
        {
            $msg    = 'Error';
            $texto  = '01The number of columns in the file is exceeded';
        }
    }
    else
    {
        $msg    = 'Error';
        $texto  = '01Invalid File Extension';
    
    }
    
    

}
elseif ( isset( $_POST['inputPerfil'] ) )
{
    $accion=$_POST["inputacciontarjeta"];
    $tarjeta=$_POST["inputPerfil"];
    $comentario=$_POST["comment"];
    $digitos_tarjeta=substr( $tarjeta, - 8 );
    if($accion=="FONDEAR")
    {
        $masterBalance=$app->getAmountCountMaster();
        $new_MB  = floatval($masterBalance);
        if ( $administradoraBalance >= $_POST["amount"] AND $_POST["amount"]<=$new_MB)  // si el monto asignado a la empresa es mayor al que se desea traspasar a una tarjeta
        {
            $fecha=date('Y-m-d H:i:s');
            $res=$app->getTransactionsCard($_SESSION['EMPRESA'],$digitos_tarjeta,$_POST['amount'],$fecha); // compara la tarjeta el monto y la fecha para saber si existe un registro en diferencia de 5 minutos
            if(sizeof($res)>0)
            {
                $texto='01YOU CANNOT FUND THIS CARD CURRENTLY WAIT 5 MINUTES OR MODIFY THE AMOUNT';
            }
            else
            {
                $validar_tarjeta=$app->getCardEnabled($digitos_tarjeta,date('Y-m-d H:i:s'));
                if(sizeof($validar_tarjeta)>0)
                {
                    $texto='01YOU CANNOT FUND THIS CARD CURRENTLY,TRY LATER';
                }
                else
                {
                    $saldo_tarjeta=$app->check_amountCard($tarjeta);
                    $saldo_master=$app->getAmountCountMaster();
                    $datacompany=$app->viewECompanys($_SESSION['EMPRESA']);
                    $datauser=$app->viewUsersByIde($_SESSION['USER']);

                    //CREACION DEL MENSAJE DEL CORREO

                    $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($datacompany[1]).'</b></h3>-----------------------<br>';
                             
                    $table_mensaje='<table>
                                <tr><td> Saldo Cuenta Maestra </td><td>$US '.number_format(substr($saldo_master,2),2,'.',',').'</td></tr>'.
                                '<tr><td> Saldo Empresa </td><td>$US '.number_format($administradoraBalance,2,'.',',').'</td></tr>'.
                                '<tr><td> Monto del fondeo </td><td>$US '.number_format($_POST['amount'],2,'.',',').'</td></tr>'.
                                '<tr><td> Tarjeta </td><td> ****-****-'.str_pad(substr($digitos_tarjeta, 0,-4),4, "**", STR_PAD_LEFT).'-'.substr($digitos_tarjeta, -4).'</td></tr>'.
                                '<tr><td> Saldo Tarjeta </td><td>$US '.number_format($saldo_tarjeta,2,'.',',').'</td></tr>'.
                                '<tr><td> Accion </td><td> '.$accion.'</td></tr></table>';
                    $msj_aplicacion_pagos.=$table_mensaje;
                    $titulo_correo='DEMO DE PAGOS A TARJETA ENERGEX';
                    //$respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);

                    // TERMINA CREACION DEL CORREO
                    $texto='00DEMO';
                    $texto = $app->applyFunds( $_SESSION['EMPRESA'], $digitos_tarjeta, $_POST['amount'],$saldo_tarjeta,$_SESSION['USER'],$saldo_master,$comentario );
                    if(substr( $texto, 0, 2 ) !== '00')
                    {
                        $data_incident=[$_SESSION['EMPRESA'],$digitos_tarjeta,$_POST['amount'],$texto,$_SESSION['EMPRESA']];
                        $idincident=$app->insertlogincidentsPays($data_incident);  
                        $titulo_correo='ERROR DE FONDEO DE TARJETA ENERGEX';  
                        $msj='Tarjeta : '.$digitos_tarjeta.', Monto : $US '.$_POST['amount'].' Error : '.$texto.' Empresa : '.$_SESSION['EMPRESA'].' Accion: '.$accion;
                        $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                        $datalogmail=[$idincident,$msj,$respuesta_mail];
                        $app->insertlogemailslIncidents($datalogmail);    
                        sleep(30); // Espera 30 segundos 
                        $saldo_tarjeta_new=$app->check_amountCard($tarjeta); // Verifica el saldo de la tarjeta

                        if($saldo_tarjeta_new==($saldo_tarjeta+$_POST['amount'])) // Si el saldo actual es mayor al saldo anterior en la tarjeta
                        {
                            $texto = $app->applyFundsVirtual( $_SESSION['EMPRESA'], $digitos_tarjeta, $_POST['amount'],$saldo_tarjeta,$_SESSION['USER'],$saldo_master,$comentario ); // ]Envia datos a la aplicacion de pagos virtual
                            if(substr( $texto, 0, 2 ) == '00')
                            {
                                $app->UpdateCardIncidents($idincident,$digitos_tarjeta);
                                $titulo_correo='NOTIFICACION DE PAGOS A TARJETA ENERGEX';
                                $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                $app->insertlogemailslIncidents($datalogmail);  
                                if($respuesta_mail!=202)
                                {
                                    $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                    $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                    $app->insertlogemailslIncidents($datalogmail);
                                }
                            }
                        }
                        else
                        {
                            $msj='Tarjeta : '.$digitos_tarjeta.', Monto : $ '.$_POST['amount'].' Saldo Tarjeta : '.$saldo_tarjeta_new.' Empresa : '.$_SESSION['EMPRESA'].' Accion: '.$accion;
                            $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                            $datalogmail=[$idincident,$msj,$respuesta_mail];
                            $app->insertlogemailslIncidents($datalogmail);
                            if($respuesta_mail!=202)
                            {
                                $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                                $datalogmail=[$idincident,$msj,$respuesta_mail];
                                $app->insertlogemailslIncidents($datalogmail);
                            }  
                        }
                    }
                    else
                    {
                        $titulo_correo='NOTIFICACION DE FONDEO A TARJETA ENERGEX';
                        $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                        $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                        $app->insertlogemailslIncidents($datalogmail);
                        if($respuesta_mail!=202)
                        {
                            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                            $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                            $app->insertlogemailslIncidents($datalogmail);
                        }  
                    }

                }
            }
            //$texto='00DEMO OK';
        }
        else
        {
            $texto='01THEY DO NOT HAVE ENOUGH FUNDS FOR THE FUNDING.';   
        }
    }
    elseif($accion="REVERSAR")
    {
        $texto='01DO NOT REVERSE THIS CARD';
        $monto=$app->check_amountCard($tarjeta); // checa el saldo de la tarjeta
        if($monto==FALSE) // verifica que devuelta dato correcto la api
        {
            $texto='01DO NOT REVERSE THIS CARD,TRY LATER';   
        }
        else
        {

            if($_POST["amount"]<=$monto) // si el reverso deseado no excede el saldo de la tarjeta
            {
                $montonuevo=-1*$_POST["amount"];
                $fechanueva=date('Y-m-d H:i:s');
                $res=$app->getTransactionsCard($_SESSION['EMPRESA'],$digitos_tarjeta,$montonuevo,$fechanueva); // verfica si no existe registro igual en menos de 5 minutos
                if(sizeof($res)>0)
                {
                    $texto='01DO NOT REVERSE THIS CARD ACTUALLY,WAIT 5 MINUTS OR CHANGE AMOUNT';
                }
                else
                {
                    $validar_tarjeta=$app->getCardEnabled($digitos_tarjeta,date('Y-m-d H:i:s')); // Verifica si no esta inhabilitada la tarjeta
                    if(sizeof($validar_tarjeta)>0)
                    {
                        $texto='01NDO NOT REVERSE THIS CARD ACTUALLY,TRY LATER';
                    }else
                    {
                        $saldo_tarjeta=$app->check_amountCard($tarjeta);
                        $saldo_master=$app->getAmountCountMaster();
                        $texto = $app->returnFunds( $company, $digitos_tarjeta, $montonuevo,$saldo_tarjeta,$_SESSION['USER'],$saldo_master,$comentario );
                        if(substr( $texto, 0, 2 ) !== '00')
                        {
                            $data_incident=[$_SESSION['EMPRESA'],$digitos_tarjeta,$_POST['amount'],$texto,$_SESSION['EMPRESA']];
                            $idincident=$app->insertlogincidentsPays($data_incident);    
                            $msj='Tarjeta : '.$digitos_tarjeta.', Monto : $US '.$_POST['amount'].' Error : '.$texto.' Empresa : '.$_SESSION['EMPRESA'].' Accion: '.$accion;
                            $titulo_correo='ERROR DE REVERSO A TARJETA ENERGEX';
                            $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                            $datalogmail=[$idincident,$msj,$respuesta_mail];
                            $app->insertlogemailslIncidents($datalogmail);  
                            if($respuesta_mail!=202)
                            {
                                $respuesta_mail=$email->enviarmail($msj,$titulo_correo);
                                $datalogmail=[$idincident,$msj,$respuesta_mail];
                                $app->insertlogemailslIncidents($datalogmail);  
                            }
                        }
                        else
                        {
                            $datacompany=$app->viewECompanys($_SESSION['EMPRESA']);
                            $datauser=$app->viewUsersByIde($_SESSION['USER']);
                            $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($datacompany[1]).'</b></h3>-----------------------<br>';
                            $table_mensaje='<table>
                                        <tr><td> Saldo Cuenta Maestra </td><td>$ '.number_format(substr($saldo_master,2),2,'.',',').'</td></tr>'.
                                        '<tr><td> Saldo Empresa </td><td>$ '.number_format($administradoraBalance,2,'.',',').'</td></tr>'.
                                        '<tr><td> Monto del reverso </td><td>$ '.number_format(-1*$_POST['amount'],2,'.',',').'</td></tr>'.
                                        '<tr><td> Tarjeta </td><td> ****-****-'.str_pad(substr($digitos_tarjeta, 0,-4),4, "**", STR_PAD_LEFT).'-'.substr($digitos_tarjeta, -4).'</td></tr>'.
                                        '<tr><td> Saldo Tarjeta </td><td>$ '.number_format($saldo_tarjeta,2,'.',',').'</td></tr>'.
                                        '<tr><td> Accion </td><td> '.$accion.'</td></tr></table>';
                            $msj_aplicacion_pagos.=$table_mensaje;            
                            $titulo_correo='NOTIFICACION DE REVERSO A TARJETA ENERGEX';
                            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                            $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                            $app->insertlogemailslIncidents($datalogmail);
                            if($respuesta_mail!=202)
                            {
                                $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                                $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                                $app->insertlogemailslIncidents($datalogmail);
                            }
                        }
                    }
                }
            }
            else
            {
                $texto='01THE CARD DOES NOT HAVE ENOUGH FUNDS FOR THE REVERSE.';
            }
        }

        
    }
    $Data=[$company,$_POST["amount"],substr( $texto , 2 ),substr( $digitos_tarjeta , 2 )];
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
elseif ( isset( $_POST['inputEmpresa'] ) && isset( $_POST['amount'] ) ) // reverso y fondeo a empresas
{
    $dataadmin=$app->viewECompanys($_SESSION['USER']);
    $datacompany=$app->viewECompanys($_POST['inputEmpresa']);
    $datauser=$app->viewUsersByIde($_SESSION['USER']);
    $comentario='';
    if($_POST["inputAccion"]=='FONDEAR')
    {
        //$comentario='Funding company';
        if ( $administradoraBalance >= $_POST['amount']  )
        {
            $saldo_master=$app->getAmountCountMaster();
            $texto = $app->applyFundsCompany( $administradoraBalance, $_POST['inputEmpresa'], $_POST['amount'], 'COMPANY FUNDING',$_SESSION['USER'],$saldo_master,$comentario );   
            $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($dataadmin[1]).'</b></h3>-----------------------<br>';
            $table_mensaje='<table>
                        <tr><td> Saldo Cuenta Maestra </td><td>$ '.number_format(substr($saldo_master,2),2,'.',',').'</td></tr>'.
                        '<tr><td> Saldo Administrador </td><td>$ '.number_format($administradoraBalance,2,'.',',').'</td></tr>'.
                        '<tr><td> Monto del fondeo </td><td>$ '.number_format($_POST['amount'],2,'.',',').'</td></tr>'.
                        '<tr><td> Empresa fondeada</td><td>'. strtoupper($datacompany[1]).'</td></tr>'.
                        '<tr><td> Saldo Empresa </td><td>$ '.number_format($datacompany[5],2,'.',',').'</td></tr>'.
                        '<tr><td> Accion </td><td> '.$_POST["inputAccion"].'</td></tr></table>';
            $msj_aplicacion_pagos.=$table_mensaje;            
            $titulo_correo='NOTIFICACION DE FONDEO A EMPRESA ENERGEX';
            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
            $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
            $app->insertlogemailslIncidents($datalogmail);
            if($respuesta_mail!=202)
            {
                $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                $app->insertlogemailslIncidents($datalogmail);
            }
        }
        else
        {
            $texto='01THE FUND TO BE MADE EXCEED THE EXISTING AMOUNT';
        }

        if ( substr( $texto, 0, 2 ) === '00' )
        {
            $msg = "Hecho!";
        }
        else
        {
            $msg = "Error";
        }
    
    }
    elseif($_POST["inputAccion"]=='REVERSAR')
    {
        $dataCompany=$app->viewCompany($_POST["inputEmpresa"]);
        //$comentario='Funding company';
        if($_POST["amount"]<=$dataCompany[11])
        {
            $saldo_master=$app->getAmountCountMaster();
            $texto=$app->ReturnFundsCompany($_POST["inputEmpresa"],-1*$_POST["amount"],'COMPANY REVERSING',$_SESSION['USER'],$saldo_master,$comentario);
            //$texto='00MODULO DE REVERSO'; 
            $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($dataadmin[1]).'</b></h3>-----------------------<br>';
            $table_mensaje='<table>
                        <tr><td> Saldo Cuenta Maestra </td><td>$ '.number_format(substr($saldo_master,2),2,'.',',').'</td></tr>'.
                        '<tr><td> Saldo Administrador </td><td>$ '.number_format($administradoraBalance,2,'.',',').'</td></tr>'.
                        '<tr><td> Monto del fondeo </td><td>$ '.number_format(-1*$_POST['amount'],2,'.',',').'</td></tr>'.
                        '<tr><td> Empresa fondeada</td><td>'. strtoupper($datacompany[1]).'</td></tr>'.
                        '<tr><td> Saldo Empresa </td><td>$ '.number_format($datacompany[5],2,'.',',').'</td></tr>'.
                        '<tr><td> Accion </td><td> '.$_POST["inputAccion"].'</td></tr></table>';
            $msj_aplicacion_pagos.=$table_mensaje;            
            $titulo_correo='NOTIFICACION DE REVERSO A EMPRESA ENERGEX';
            $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
            $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
            $app->insertlogemailslIncidents($datalogmail);
            if($respuesta_mail!=202)
            {
                $respuesta_mail=$email->enviarmail($msj_aplicacion_pagos,$titulo_correo);
                $datalogmail=[1,$msj_aplicacion_pagos,$respuesta_mail];
                $app->insertlogemailslIncidents($datalogmail);
            } 
        }
        else
        {
            $texto='01THE REVERSE CANNOT MAKE THE REQUESTED AMOUNT EXCEED THE EXISTING IN THE COMPANY';
        }

        if ( substr( $texto, 0, 2 ) === '00' )
        {
            $msg = "Hecho!";
        }
        else
        {
            $msg = "Error";
        }
      
    }
}


if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt22') OR $app->optSearch($_SESSION[ 'PERMISOS' ],'opt23') ) 
{
    $company=$app->getCompanyUser($_SESSION["USER"]);
    $administradoraBalance  = ( $empresaMonto = $app->viewCompany( $company ) )? $empresaMonto[11] : 0;
    $Empleados              = $app->viewEmployees( $company);
}
elseif( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt17'))
{
    $masterBalance=$app->getAmountCountMaster();
    $administradoraBalance  = $app->abUpdate(floatval($masterBalance) );
    $Empresas               = $app->viewCompanys();
}

include 'header.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="card ">
                    <div class="header">
                        <h1 class="title"><i class="pe pe-7s-safe pe-2x pull-left pe-border"></i></h1>
                        <p class="category"><br><span style="font-size:18px;"><b>&nbsp;&nbsp;u$s <?php echo number_format( floatval( $administradoraBalance ), 2, '.', ',' ); ?></b></span><br><span style="font-size:18px;">Available Total</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><?php echo 'FUNDS SUCCESFULL #' . substr( $texto , 2 ); ?></span>
            </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
            <div class="alert alert-danger">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><b> Error - </b> <?php echo substr( $texto, 2 ); ?></span>
            </div>
            <?php } ?>
            <?php if (isset( $msg_layout ) ) {
                echo $msg_layout;} ?> 
            
            
            <?php if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt23') ) { ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Massive funding to cards</h4>
                        <p>Bulk funding by loading format(maximum 50 records per load).</p>
                    </div>
                    <div class="content">
                        <form action="reload_funds?scr=4" method="POST" enctype="multipart/form-data" id="form1" onsubmit="return checkSubmitBlock('btsubmit3');">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Upload File</label>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                        <input type="file" name="uploadfile" class="form-control" required >
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btsubmit3" class="btn btn-info btn-fill pull-right" form="form1">Fund</button>
                            <a class="btn btn-default btn-fill pull-left" href="./downloads/layout_founds.csv">Download Layout</a>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt22')) { // Fondeo individual a las ?>
            <div class="col-md-6">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Individual Funding of Cards.</h4>
                        <p>Fund the card individually.</p>
                    </div>
                    <div class="content">
                        <form action="reload_funds?scr=4" method="POST" id="form2" onsubmit="return checkSubmitBlock('btsubmit');">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputPerfil">User</label>
                                        <select class="form-control selectednew2" name="inputPerfil" required >
                                        <?php for ( $i = 0; $i <= count( $Empleados ); $i += 3 ) {
                                            if ( isset( $Empleados[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empleados[ $i + 2 ]; ?>"><?php echo strtoupper(utf8_encode($Empleados[ $i + 1 ])) . ' - ' . '****-****-'.str_pad(substr($Empleados[$i+2], 8,-4),4, "**", STR_PAD_LEFT).'-'.substr($Empleados[$i+2], -4); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputAccion">Action</label>
                                        <select class="form-control optionnew2" name="inputacciontarjeta" required>
                                            <option value=""></option>
                                            <option value="FONDEAR">FUND</option>
                                            <option value="REVERSAR">REVERSE </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputPerfil">Amount</label>
                                        <input class="form-control" name="inputEmpresa" type="hidden" value="<?php echo $_SESSION['EMPRESA']; ?>" required >
                                        <input class="form-control" type="number" max="999" min="1" name="amount" id="amountcard" value="" required />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputObservaciones">COMMENT</label>
                                        <textarea rows="5" class="form-control" name="comment" maxlength="100" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btsubmit" onclick="confirm_form2('amountcard');" class="btn btn-info btn-fill pull-right" form="form2">Fund</button>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
     
        <?php } ?>
        
        <?php if (  $app->optSearch($_SESSION[ 'PERMISOS' ],'opt17') ) {?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Funds to company</h4>
                        <p>Funding companies individually.</p>
                    </div>
                    <div class="content">
                        <form action="reload_funds?scr=4" method="POST" id="form3" onsubmit="return checkSubmitBlock('btsubmit2');">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputEmpresa">Company</label>
                                        <select class="form-control selectednew" name="inputEmpresa" required >
                                        <?php for ( $i = 0; $i <= count( $Empresas ); $i += 7 ) {
                                            if ( isset( $Empresas[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empresas[ $i ]; ?>"><?php echo strtoupper($Empresas[ $i + 1 ]); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputAccion">Action</label>
                                        <select class="form-control optionnew" name="inputAccion" required>
                                            <option value=""></option>
                                            <option value="FONDEAR">FUND</option>
                                            <option value="REVERSAR">REVERSE </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputPerfil">AMOUNT</label>
                                        <input class="form-control" type="number" min="1" id="amount" name="amount" value="" required /> <!-- Checa que el monto anotado sea menor o igual al que tiene la empresa. -->
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" id="btsubmit2" onclick="confirm_form('amount');" class="btn btn-info btn-fill pull-right" form="form3">Fund</button>
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
echo "<script type='text/javascript'> $('.selectednew').select2();</script>";
echo "<script type='text/javascript'> $('.optionnew').select2();</script>";
echo "<script type='text/javascript'> $('.selectednew2').select2();</script>";
echo "<script type='text/javascript'> $('.optionnew2').select2();</script>";

include 'footer.php';