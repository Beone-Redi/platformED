<?php

/* Controller inEmployeePlatform
$data=
    [
        inEmployeePlatform=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        KeyCompany=>OBAKQINM7RA4,2
        KeyBIONE=>42JY9X0COG1AD61SIE,3
        userCard=>demouser,4
        passCard=>987654321abc0,5
        emailCard=>demouser@myemail.com,6
        idUserBIONE=>1234,7
        nameUser=>Empleado 1,8

    ]*/
class inEmployeePlatformController
{
    static function GenericMethod( $data )
    {
        try
        {

            $PDO        = new PDOSS();
            /*if(sizeof($data)!=9)
            {
                $response=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'64',
                    'ErrorMesage'=>'PARAMETROS NO COMPLETOS'
                ];        
            }
            else
            {*/
                $customCamp=date('YmdHis');
            if(array_key_exists(9,$data))
            {
                $customCamp=$data[9];
            }
                $user_register = $PDO->GetUserRegister( $data[4]);
            
                if(sizeof($user_register)==0)
                {
                    $data_company=$PDO->GetDataCompany($data[2]);
                    if(sizeof($data_company)>0)
                    {
                        $data_user=
                        [
                            $data[4],
                            $data_company['ide'],
                            $data[8],
                            $data_company['address'],
                            $data_company['city'],
                            $data_company['zip'],
                            'EMPLEADO DE '.strtoupper($data_company['company']),
                            'assets/img/empleado.png',
                            0,
                            'EMPLEADO',
                            date('Y-m-d'),
                            1,
                            $data[5],
                            $data_company['telephone'],
                            $data_company['state'],
                            $data[6],
                            'EMPLEADO',
                            $data[7],
                            2,//Valor para definir un usuario creado por API BIONE
                            $customCamp
                        ];
                        $response=$PDO->RegisterUser( $data_user);
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
                        'ResponseCode'=>'64',
                        'ErrorMesage'=>'USUARIO YA REGISTRADO'
                    ];
                }
            //}
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
