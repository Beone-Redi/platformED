<?php

/* Controller BalancePlatform
$data=
    [
        BalancePlatform=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        TypeProduct=>1
    ]*/
class BalancePlatformController
{

    static function GenericMethod( $data ) /*Generate Key*/
    {
        try
        {
            $PDO        = new PDOSS();
            if(sizeof($data)!=3)
            {
                $answer_API=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'64',
                    'ErrorMesage'=>'PARAMETROS NO COMPLETOS'
                ];        
            }
            else
            {

                $SumCompanys=$PDO->GetBalanceCompanys();
                if(sizeof($SumCompanys)>0)
                {
                    $answer_API=
                    [   
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>"00",
                        'Balance'=>$SumCompanys[0]
                    ]; 
                }
               
                else
                {
                    $answer_API=
                    [
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>"01",
                        'ErrorMesage'=>'EL MONTO NO ES NUMERICO'
                    ];  
               
                }
            }

            return $answer_API;
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
