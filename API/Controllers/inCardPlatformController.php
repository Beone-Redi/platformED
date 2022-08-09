<?php
/*Controller inCardPlatform
$data=
    [
        inCardPlatform=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        KeyCompany=>OBAKQINM7RA4,2
        KeyBIONE=>42JY9X0COG1AD61SIE,3
        userCard=>demouser,4
        binCard=>1234567890123456,5
    ]*/
class inCardPlatformController
{
    static function GenericMethod( $data )
    {
        try
        {

            $PDO        = new PDOSS();

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
                $user_register = $PDO->GetUserRegister( $data[4]);
                if(sizeof($user_register)>0)
                {
                    $card_register = $PDO->GetCardRegister( substr($data[5],-8));    
                    $Idproduct = $PDO->GetproductCard( substr($data[5],0,8));    
                    
                    if(sizeof($card_register)==0)
                    {
                        $data_company=$PDO->GetDataCompany($data[2]);
                        if(sizeof($data_company)>0)
                        {
                            $data_card=
                            [
                                substr($data[5],-8),
                                $data_company['ide'],
                                $data_company['city'],
                                'c190319112221.jpg',
                                date('Y-m-d'),
                                1,
                                $data[4],
                                $Idproduct[0]
                            ];
                            $response=$PDO->RegisterCardTemp( $data_card);
                        }
                        else
                        {
                            $response=
                            [
                                'SystemTraceAuditNumber'=>date('ymdHis'),
                                'ResponseCode'=>'64',
                                'ErrorMesage'=>'KEY COMPANY NO VALIDO'
                            ];
                        }
                    }
                    else
                    {
                        $response=
                        [
                            'SystemTraceAuditNumber'=>date('ymdHis'),
                            'ResponseCode'=>'65',
                            'ErrorMesage'=>'LA TARJETA FUE REGISTRADA ANTERIORMENTE'
                        ];
                    }
                }
                else
                {
                    $response=
                    [
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>'65',
                        'ErrorMesage'=>'EL USUARIO NO ESTA REGISTRADO'
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
