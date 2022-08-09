<?php
    /*Controller Chargesnotification
    $data=
    [
        ChangeUser=>TRUE,0
        clientKey=>1AASXWZ723AQWHJUSK1KIST55SDVR455,1
        User=>'101186bfg',2
        Card=>42424242
    ]*/
class ChangeUserController
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
                    $company = $PDO->ValidateCardExist($data[3]); //Verifica que la tarjeta este en la plataforma
                    if(sizeof($company)>0)
                    {
                        $idcompanyP=$company['idcompany'];
                        $previoususer=$company['IdUser'];
                        $CP=$PDO->GetIdCompanyByUser($data[2]);
                        $CompanyUs=$CP[0];
                        $response=$PDO->SetChangeUser($idcompanyP,$previoususer,$CompanyUs,$data[2],$data[3]);
                    
                    }
                    else
                    {
                        $response=
                        [
                            'SystemTraceAuditNumber'=>date('ymdHis'),
                            'ResponseCode'=>'02',
                            'ErrorMesage'=>'NO EXISTE LA TARJETA'
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
