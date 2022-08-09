<?php

/* Controller Paymentnotification
$data=
    [
        inEmployeePlatform=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        Card=>89634567,2
        Amount=>100,3
    ]*/
class PaymentnotificationController
{

    static function GenericMethod( $data ) /*Generate Key*/
    {
        try
        {
            $PDO        = new PDOSS();
            if(sizeof($data)!=4)
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
            
                if(is_numeric($data[3]))
                {
                        
                    $datacard=$PDO->GetDataCard($data[2]); /* Get info CardHolder-Paynet, company,ComisionAdmin,IvaComisionAdmin,ComisionCompany,IvaComisionCompany*/
                    if(sizeof($datacard)>0)
                    {
                        $concept=$PDO->ConceptPaynetByCompany($data[2]);
                        if(sizeof($concept)>0)
                        {
                            $concept_payment=$concept[0];
                        }
                        else
                        {
                            $concept_payment='CARGO PAYNET';
                        }
                        /*Construir Info de paynet* */
                        $tarjeta=$data[2];
                        $montoPago=$data[3]; //Monto de pago referencia
                        $company=$datacard["company"];
                        $ComisionAdmin=$datacard["ComisionAdmin"]; /** Comision Admin */
                        $IvaComisionAdmin=$datacard["IvaComisionAdmin"];    /** Iva Comision Admin */
                        $ComisionCompany=$datacard["ComisionCompany"];  /**  Comision company */
                        $IvaComisionCompany=$datacard["IvaComisionCompany"];    /** Iva Comision Company */
                        
                        $ValueComisionAdmin=($montoPago*$ComisionAdmin)/100;    /** Value Comision admin */
                        $ValueIvaComisionAdmin=($ValueComisionAdmin*$IvaComisionAdmin)/100;     /** Value Iva Comision admin */
                        $ValueComisionCompany=($montoPago*$ComisionCompany)/100;    /** Value Comision Company */
                        $ValueIvaComisionCompany=($ValueComisionCompany*$IvaComisionCompany)/100;     /** Value Iva Comision Company */
                        
                        $comisiones=$ComisionAdmin+$ComisionCompany; //Suma de comisioness
                        $AmountPaynet=$ValueComisionAdmin+$ValueIvaComisionAdmin+$ValueComisionCompany+$ValueIvaComisionCompany; /* Monto a Cobrar por uso paynet * */
                        $des=$concept_payment;
                        if($data[3]<=100)
                        {
                            $AmountPaynet=15;// Si el monto es menor o igual a 100
                        }

                        //$Data=[empresa,comisioncobrar,tarjeta,montoreferencia,comisionadmin,ivacomisionadmin,comisioncompany,ivacomisioncompany,valuecomisionadmin,valueivacomisionadmin,]
                        $Data=
                        [
                            $company, //empresa
                            $comisiones, //comision a cobrar
                            $tarjeta, //Tarjeta
                            $montoPago, //Monto de pago referencia
                            $ComisionAdmin, //Comision admin
                            $IvaComisionAdmin, //Iva Comision admin
                            $ComisionCompany, // Comision company
                            $IvaComisionCompany, // Iva Comision Company
                            $ValueComisionAdmin,// Value Comision Admin
                            $ValueIvaComisionAdmin, // Value IVa Comision Admin
                            $ValueComisionCompany, // Value Comision Company
                            $ValueIvaComisionCompany,// Value Comision Admin
                            $comisiones, // suma de las comisiones
                            $AmountPaynet,//Cobro de paynet
                            $des //'Comision Paynet'
                        ];
                        $answer_API=$PDO->SaveDatapayment($Data);
                    }
                    else
                    {
                        $answer_API=
                        [
                            'SystemTraceAuditNumber'=>date('ymdHis'),
                            'ResponseCode'=>"64",
                            'ErrorMesage'=>'La tarjeta no es de multibank'
                        ];  
                    }
                }
                else
                {
                    $answer_API=
                    [
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>"64",
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
