<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
// Load Composer's autoloader
class msjs_mails
{

    public function Found_card($Data)
    {
        //Data=[empresa,accion,Tarjeta,monto,descripcion]
        $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($Data[0]).'</b></h3>-----------------------<br>';
        $table_mensaje='<table>
                    <tr><td> Accion </td><td> '.$Data[1].'</td></tr>'.
                    '<tr><td> Tarjeta </td><td> ****-****-'.substr($Data[2], 0,-4).'-'.substr($Data[2], -4).'</td></tr>'.
                    '<tr><td> Monto </td><td>$ '.number_format($Data[3],2,'.',',').'</td></tr>'.
                    '<tr><td> Descripcion </td><td> '.$Data[4].'</td></tr></table>';
        $msj_aplicacion_pagos.=$table_mensaje; // Creacion del mensaje a enviar por correo
        return  $msj_aplicacion_pagos;
    }

    
    /*Construccion de mensaje de fondeo a empresa* */
    public function Found_company($Data) //Constr
    {
        $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($Data[0]).'</b></h3>-----------------------<br>';
        
                    $table_mensaje='<table>
                    <tr><td> Monto del fondeo </td><td>$ '.number_format($Data[3],2,'.',',').'</td></tr>'.
                    '<tr><td> Empresa fondeada</td><td>'. strtoupper($Data[4]).'</td></tr>'.
                    '<tr><td> Saldo Empresa </td><td>$ '.number_format($Data[5],2,'.',',').'</td></tr>'.
                    '<tr><td> Accion </td><td> '.$Data[6].'</td></tr></table>';
        
        $msj_aplicacion_pagos.=$table_mensaje;    
        return  $msj_aplicacion_pagos;         
    }

    /*Construccion de mensaje de notificacion empresa fondeada* */
    public function Found_company_Notifications($Data)
    {
        //Data=[empresa,saldo empresa,Monto fondeo,Nuevo saldo empresa,Accion]
        $msj_aplicacion_pagos='<br> <b><h3>EMPRESA '.strtoupper($Data[0]).'</b></h3>-----------------------<br>';
        $table_mensaje='<table>
                    <tr><td> Saldo Empresa </td><td>$ '.number_format($Data[1],2,'.',',').'</td></tr>'.
                    '<tr><td> Monto del fondeo </td><td>$ '.number_format($Data[2],2,'.',',').'</td></tr>'.
                    '<tr><td> Nuevo Saldo </td><td>$ '. number_format($Data[3],2,'.',',').'</td></tr>'.
                    '<tr><td> Accion </td><td> '.$Data[4].'</td></tr></table>';
        $msj_aplicacion_pagos.=$table_mensaje;    
        return  $msj_aplicacion_pagos;         
    }

    /*Construccion del mensaje de notificacion de cuenta maestra* */
    public function Found_MasterAcount($Data)
    {
        $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($Data[0]).'</b></h3>-----------------------<br>';
        $table_mensaje='<table>
                    <tr><td> Saldo Administrador antes del fondeo </td><td>$ '.number_format($Data[1],2,'.',',').'</td></tr>'.
                    '<tr><td> Monto del fondeo </td><td>$ '.number_format($Data[2],2,'.',',').'</td></tr>'.
                    '<tr><td> Saldo Final Administrador</td><td>$ '.number_format($Data[3],2,'.',',').'</td></tr></table>';
        $msj_aplicacion_pagos.=$table_mensaje;    
        return  $msj_aplicacion_pagos;         
    }

    public function Error_Found_card($Data)
    {
        //Data=[empresa,accion,Tarjeta,monto,descripcion]
        $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($Data[0]).'</b></h3>-----------------------<br>';
        $table_mensaje='<table>
                     <tr><td> Accion </td><td> '.$Data[1].'</td></tr>'.
                     '<tr><td> Tarjeta </td><td> ****-****-'.substr($Data[2], 0,-4).'-'.substr($Data[2], -4).'</td></tr>'.
                     '<tr><td> Monto </td><td>$ '.number_format($Data[3],2,'.',',').'</td></tr>'.
                     '<tr><td> Descripcion </td><td> '.$Data[4].'</td></tr></table>';
        $msj_aplicacion_pagos.=$table_mensaje; // Creacion del mensaje a enviar por correo
        return  $msj_aplicacion_pagos; 
    }

    public function Error_Found_MA($Data)
    {
        //Data=[empresa,accion,Tarjeta,monto,descripcion]
        $msj_aplicacion_pagos='<br> <b><h3>Empresa '.strtoupper($Data[0]).'</b></h3>-----------------------<br>';
        $table_mensaje='<table>
                     <tr><td> Accion </td><td> '.$Data[1].'</td></tr>'.
                     '<tr><td> Saldo cuenta maestra </td><td>$ '.number_format($Data[5],2,'.',',').'</td></tr>'.
                     '<tr><td> Tarjeta </td><td> ****-****-'.substr($Data[2], 0,-4).'-'.substr($Data[2], -4).'</td></tr>'.
                     '<tr><td> Monto </td><td>$ '.number_format($Data[3],2,'.',',').'</td></tr>'.
                     '<tr><td> Descripcion </td><td> '.$Data[4].'</td></tr></table>';
        $msj_aplicacion_pagos.=$table_mensaje; // Creacion del mensaje a enviar por correo
        return  $msj_aplicacion_pagos; 
    }
}
