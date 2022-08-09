<?php
    /*Controller Chargesnotification
    $data=
    [
        BalanceCompany=>TRUE,0
        clientKey=>1AASXWZ723AQWHJUSK1KIST55SDVR455,1
        Card=>'12345678',2
        Amount=>10
    ]*/
class BalanceCompanyController
{
    static function GenericMethod( $data )
    {
        try
        {

            $PDO        = new PDOSS();

            if(sizeof($data)!=4)
            {
                $response=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'64',
                    'ErrorMesage'=>'PARAMETROS NO COMPLETOS'
                ];
              
            }
            else
            {
                    $company = $PDO->ValidateCardExist($data[2]); //Verifica que la tarjeta este en la plataforma
                    if(sizeof($company)>0)
                    {
                        $idcompany=$company['idcompany'];
                        $status=1;
                        $Type=1;
                        $Balance=$PDO->GetBalanceCompanyByIde($idcompany,$status,$Type);
                        if(sizeof($Balance)>0)
                        {
                        
                            if($Balance[0]>=$data[3])
                            {
                                $response=
                                [   
                                    'SystemTraceAuditNumber'=>date('ymdHis'),
                                    'ResponseCode'=>"00",
                                    'Status'=>'APROVED'
                                ];
                            }
                            else
                            {
                                $response=
                                [   
                                    'SystemTraceAuditNumber'=>date('ymdHis'),
                                    'ResponseCode'=>"01",
                                    'ErrorMesage'=>'SALDO DE LA EMPRESA INSUFICIENTE'
                                ];
                            }
                        }
                        else
                        {
                            $response=
                            [
                                'SystemTraceAuditNumber'=>date('ymdHis'),
                                'ResponseCode'=>"01",
                                'ErrorMesage'=>'NO ES POSIBLE OBTENER EL SALDO DE LA EMPRESA'
                            ];  
                        }
                    }
                    else
                    {
                        $response=
                        [
                            'SystemTraceAuditNumber'=>date('ymdHis'),
                            'ResponseCode'=>'02',
                            'ErrorMesage'=>'DATOS INEXISTENTES'
                        ];
                    }               
            }
            return $response;
        }
        catch ( Exception $ERROR )
        {
            $answer_API['SystemTraceAuditNumber']=date('ymdHis');
            $answer_API['ResponseCode']="64";
            $answer_API['ErrorMesage']=$ERROR->getMessage();
            return $answer_API;
        }
    }

    
}
