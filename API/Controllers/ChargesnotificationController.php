<?php
/*Controller Chargesnotification
$data=
    [
        Chargesnotification=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        Card=>'12345678',2
        Amount=>10,3
        AuthCode=>42JY9X0COG1AD61SIE,4
        TypeMov=>1,5
        concept=>'CArgo',6
    ]*/
class ChargesnotificationController
{
    static function GenericMethod( $data )
    {
        try
        {

            $PDO        = new PDOSS();

            if(sizeof($data)!=7)
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
                        $Monto_empresa=$company['fund'];
                        $AuthCode=$PDO->ValidateAuthCode($data[4],$data[2]); //Verifica el codigo de autorizacion sea unico
                        if(sizeof($AuthCode)>0)
                        {
                            $email        = new sendmail();
                            $mail_mensaje='<table>
                                <tr><td> Tarjeta </td><td> '.$data[2].'</td></tr>'.
                                '<tr><td> Monto del Cargo </td><td> '.$data[3].'</td></tr>'.
                                '<tr><td> Accion </td><td> '.$data[5].'</td></tr>'.
                                '<tr><td> Codigo de autorizacion </td><td> '.$data[4].'</td></tr>'.
                                '<tr><td> Motivo Error </td><td> '.'CODIGO DE AUTORIZACION REPETIDO'.'</td></tr></table>';
                            $email->enviarmail($mail_mensaje,'CODIGO DE AUTORIZACION REPETIDO');
                            $response=
                            [
                                'SystemTraceAuditNumber'=>date('ymdHis'),
                                'ResponseCode'=>'65',
                                'ErrorMesage'=>'AUTHCODE REPETIDO'
                            ];
                        }
                        else
                        {
                            if($data[5]==1) //Abono a tarjeta
                            {
                               $response=$PDO->insertFundstemp($data[2],$idcompany,$data[3],$data[6],$data[4],$data[5]);
                            }
                            elseif($data[5]==2) //Reverso a tarjeta(Cargo a tarjeta)
                            {
                                $response=$PDO->insertFundstemp($data[2],$idcompany,-1*$data[3],$data[6],$data[4],$data[5]);
                            }
                            else
                            {
                                $response=
                                [
                                    'SystemTraceAuditNumber'=>date('ymdHis'),
                                    'ResponseCode'=>'65',
                                    'ErrorMesage'=>'MODULO EN CREACION'
                                ];
                            }
                        }
                    }
                    else
                    {
                        $response=
                        [
                            'SystemTraceAuditNumber'=>date('ymdHis'),
                            'ResponseCode'=>'65',
                            'ErrorMesage'=>'LA TARJETA NO ESTA REGISTRADA'
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
