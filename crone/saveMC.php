<?php 
/**
 * Se define el área geografica.
 */
date_default_timezone_set( 'America/Monterrey' );

include "core.php";
$app = new app();
    $Data_Masters_Acounts=$app->getProductsPlatform(); // Obtiene los datos de las cuentas de la plataforma(Nombre,AgregamentId,ProductId)
    $Masters_Acounts=[];
    $masterBalance=$sumaMasters=$diffMasterAdmin=0;
    $j=0;
    $fecha=date('Y-m-d H:i:s');
    $Resp=$app->LastIdreport();
    $idmov=$Resp+1;
    
    for($i=0;$i<sizeof($Data_Masters_Acounts);$i+=5)
    {
        $saldo_master=$app->getAmountMasterAcount($Data_Masters_Acounts[$i+1],$Data_Masters_Acounts[$i+2]); // Get Balance MasterAcount Obtiene el saldo de la cuenta maestra y el nombre de la misma 
        array_push($Masters_Acounts,$Data_Masters_Acounts[$i+4],$saldo_master);
        $masterBalance+=$saldo_master;
            $sumaMasters+=$saldo_master;
            $data=[
                $Data_Masters_Acounts[$i+3],
                $Data_Masters_Acounts[$i],
                $saldo_master,
                $idmov,
                $fecha,
                ''
            ];
            $app->new_Register($data);
        
        
    }
  
    $administradoraBalance  = $app->SumAllFundcompany(); //Balance Admin
    $diffMasterAdmin=$sumaMasters-$administradoraBalance;
    $databanco=
    [
        1000,
        'Saldo Bancos',
        $sumaMasters,
        $idmov,
        $fecha,
        ''
    ];
    $app->new_Register($databanco);
    
    $datae=
    [
        1000,
        'Saldo empresas',
        $administradoraBalance,
        $idmov,
        $fecha,
        ''
    ];
    $app->new_Register($datae);
    
    $datadif=
    [
        1001,
        'Diferencia saldos',
        $diffMasterAdmin,
        $idmov,
        $fecha,
        ''
    ];
    $app->new_Register($datadif);
    



?>