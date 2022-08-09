<?php

/*Controller SaveCardPlatform
$data=
    [
        SaveCardPlatform=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        KeyCompany=>OBAKQINM7RA4,2
        KeyBIONE=>42JY9X0COG1AD61SIE,3
        idCardTemp=>200611013602,4
        Status=>1,5
    ]*/

class SaveCardPlatformController
{
    static function GenericMethod( $data )
    {
        try
        {

            if(sizeof($data)!=6)
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
                $PDO        = new PDOSS();
           
                $cardtemp = $PDO->GetCardTempRegister($data[4]);
                $status=$data[5];
                if($status==1)
                {
                    $response=$cardtemp;

                    if(sizeof($cardtemp)>0)
                    {
                        $dataCT=$PDO->GetDataCardTemp($data[4]);
                        if(sizeof($dataCT)>0)
                        {

                            $data_card=
                            [
                                $dataCT['idcard'],
                                $dataCT['company'],
                                $dataCT['city'],
                                $dataCT['picture'],
                                $dataCT['up_date'],
                                $dataCT['active'],
                                $dataCT['user'],
                                2,
                                $dataCT['Id_Product']                    
                            ];
                            $response=$PDO->RegisterCard($data_card);
                        }
                        else
                        {
                            $response=
                            [
                                'SystemTraceAuditNumber'=>date('ymdHis'),
                                'ResponseCode'=>'66',
                                'ErrorMesage'=>'DATACARD TEMP NO OBTENIDO'
                            ];    
                        }
                    }
                    else
                    {
                        $response=
                        [
                            'SystemTraceAuditNumber'=>date('ymdHis'),
                            'ResponseCode'=>'66',
                            'ErrorMesage'=>'IDCARDTEMP NO ES VALIDO'
                        ];
                    }
                }
                else
                {
                    $response=
                    [
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>'66',
                        'ErrorMesage'=>'LA TARJETA NO TIENE UN STATUS NO VALIDO'
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
