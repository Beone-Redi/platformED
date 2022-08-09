<?php

/**
 * Se define el área geografica.
 */
date_default_timezone_set( 'America/Monterrey' );

class PDOSS
{
    protected $DBN      = 'ener'; /*bd* */
    protected $dbUser   = 'admin';
    protected $dbPass   = '{h{47U}*m0coY2(jj';
    protected $PDOCON   = NULL;

    public function __construct()
    {
        try 
        {
            $this->UrlBonec     = "https://bigonec.westus.cloudapp.azure.com/secure/integrador/API";
            $this->clientKey    = 'ASXWZ7AJ3SRFDSK1KISW67';  //key  
            $DSN            = 'mysql:host=localhost;dbname=' . $this->DBN;
            $DBH            = new PDO( $DSN, $this->dbUser, $this->dbPass );
            $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->PDOCON   = $DBH;
        } 
        catch ( PDOException $e )
        {
            echo $e->getMessage();
        }
    }

    public function GetUserRegister( $email)
    {
        try
        {
            $Query      = "SELECT `ide` FROM `users` WHERE `email` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $email );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function GetCardRegister( $card)
    {
        try
        {
            $Query      = "SELECT `ide` FROM `cards` WHERE `idcard` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $card );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function GetCardTempRegister( $card)
    {
        try
        {
            $Query      = "SELECT `Id` FROM `card_temp` WHERE `validator` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $card );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function GetDataCompany($Key_Company)
    {
        try
        {
            $Query      = "SELECT `ide`,`address`,`city`,`zip`,`state`,`telephone`,`company` FROM `companys` WHERE `Key_Company` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $Key_Company );
            $stmt->execute();
            $Result     = $stmt->fetch(PDO::FETCH_ASSOC);  
            $total=$stmt->rowCount();
            if($total<=0)// si no se encuentra registro
            {
                $Result=[];
            }
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function GetproductCard( $card)
    {
        try
        {
            $Query      = "SELECT `Id_Product` FROM `products_platform` WHERE `Digitos_tarjeta` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $card );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function GetDataCardTemp($idCardTemp)
    {
        try
        {
            $Query      = "SELECT `idcard`,`company`,`city`,`picture`,`up_date`,`active`,`user`,`Id_Product` FROM `card_temp` WHERE `validator` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $idCardTemp );
            $stmt->execute();
            $Result     = $stmt->fetch(PDO::FETCH_ASSOC);  
            $total=$stmt->rowCount();
            if($total<=0)// si no se encuentra registro
            {
                $Result=[];
            }
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function ValidateCardExist( $Card)
    {
        try
        {
            $Query      = "SELECT A.`company` as 'idcompany',B.fund,A.user as 'IdUser' FROM `cards` AS A INNER JOIN `companys` as B ON A.company=B.ide WHERE substr(A.`idcard`,-8) = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $Card );
            $stmt->execute();
            $Result     = $stmt->fetch(PDO::FETCH_ASSOC);
            $total=$stmt->rowCount();
            if($total<=0)// si no se encuentra registro
            {
                $Result=[];
            }  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /*Get Data Info CardHolder* */
    public function GetDataCard($Card)
    {
        try
        {
            $Query      = "SELECT A.`company`,B.`ComisionAdmin`,B.`IvaComisionAdmin`,B.`ComisionCompany`,B.`IvaComisionCompany` FROM `cards` AS A INNER JOIN paynet_cards AS B ON A.ide=B.IdCard WHERE substr(A.`idcard`,-8) = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $Card );
            $stmt->execute();
            $Result=$stmt->fetch(PDO::FETCH_ASSOC);
            $total=$stmt->rowCount();
            if($total<=0)// si no se encuentra registro
            {
                $Result=[];
            }  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function RegisterUser($DataUser)
    {
        try
        {            
            $fecha=date('Y-m-d H:i:s');
            $Query      = "INSERT INTO `users`(`ide`, `email`, `company`, `fullname`, `address`, `city`, `zip`, `aboutme`, `picture`, `idcard`, `perfil`, `up_date`, `active`,`idkey`,`phone`,`state`,`email2`,`perfil_TH`,`IdUser_API`,`created_by`,`CustomCamp`,`create_at`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";          
            $stmt       = $this->PDOCON->prepare( $Query );
             // Bind
            $stmt->bindParam(1, $DataUser[0]);
            $stmt->bindParam(2, $DataUser[1]);
            $stmt->bindParam(3, $DataUser[2]);
            $stmt->bindParam(4, $DataUser[3]);
            $stmt->bindParam(5, $DataUser[4]);
            $stmt->bindParam(6, $DataUser[5]);
            $stmt->bindParam(7, $DataUser[6]);
            $stmt->bindParam(8, $DataUser[7]);
            $stmt->bindParam(9, $DataUser[8]);
            $stmt->bindParam(10, $DataUser[9]);
            $stmt->bindParam(11, $DataUser[10]);
            $stmt->bindParam(12, $DataUser[11]);
            $stmt->bindParam(13, $DataUser[12]);
            $stmt->bindParam(14, $DataUser[13]);
            $stmt->bindParam(15, $DataUser[14]);
            $stmt->bindParam(16, $DataUser[15]);
            $stmt->bindParam(17, $DataUser[16]);
            $stmt->bindParam(18, $DataUser[17]);
            $stmt->bindParam(19, $DataUser[18]);
            $stmt->bindParam(20, $DataUser[19]);
            $stmt->bindParam(21, $fecha);

            // Excecute
            if($stmt->execute())
            {
                $Result=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'00'
                ];
             }
             else
             {
                $Result=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'24',
                    'ErrorMesage'=>'USUARIO NO PUDO SER CREADO'
                ];
             }
           // $Result     = $stmt->fetch(PDO::FETCH_ASSOC);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }
    
    public function RegisterCardTemp($DataCard)
    {
        try
        {
                        
            $fecha=date('ymdHis');
            $Query      = "INSERT INTO `card_temp`(`Id`, `idcard`, `company`, `city`, `picture`, `up_date`, `active`, `user`,`validator`,`Id_Product`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?,?,?)";          
            $stmt       = $this->PDOCON->prepare( $Query );
             // Bind
            $stmt->bindParam(1, $DataCard[0]);
            $stmt->bindParam(2, $DataCard[1]);
            $stmt->bindParam(3, $DataCard[2]);
            $stmt->bindParam(4, $DataCard[3]);
            $stmt->bindParam(5, $DataCard[4]);
            $stmt->bindParam(6, $DataCard[5]);
            $stmt->bindParam(7, $DataCard[6]);   
            $stmt->bindParam(8, $fecha);   
            $stmt->bindParam(9, $DataCard[7]);    
             // Excecute
            if($stmt->execute())
            {
                $Result=
                [
                    'idCardTemp'=>$fecha,
                    'ResponseCode'=>'00'
                ];
            }
            else
            {
                $Result=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'24',
                    'ErrorMesage'=>'USUARIO NO PUDO SER CREADO'
                ];
             }
           // $Result     = $stmt->fetch(PDO::FETCH_ASSOC);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /*Get Data Info CardHolder* */
    public function GetDataPaynetCompany($IdCompany)
    {
        try
        {
            $Query      = "SELECT `ComisionPaynet`,`IvaComisionPaynet` FROM `companys` WHERE `ide` = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $IdCompany );
            $stmt->execute();
            $Result=$stmt->fetch(PDO::FETCH_ASSOC);
            $total=$stmt->rowCount();
            if($total<=0)// si no se encuentra registro
            {
                $Result=[];
            }  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function RegisterCard($DataCard)
    {
        try
        {
            
            $DataPayNet=[0,0,0,0];
            $now=date('Y-m-d H:i:s');
            $Query      = "INSERT INTO `cards`(`ide`, `idcard`, `company`, `city`, `picture`, `up_date`, `active`, `user`,`created_by`,`Id_Product`,`create_at`) VALUES (NULL,? , ?, ?, ?, ?, ?, ?, ?,?,?)";          
            $stmt       = $this->PDOCON->prepare( $Query );
             // Bind
            $stmt->bindParam(1, $DataCard[0]);
            $stmt->bindParam(2, $DataCard[1]);
            $stmt->bindParam(3, $DataCard[2]);
            $stmt->bindParam(4, $DataCard[3]);
            $stmt->bindParam(5, $DataCard[4]);
            $stmt->bindParam(6, $DataCard[5]);
            $stmt->bindParam(7, $DataCard[6]);   
            $stmt->bindParam(8, $DataCard[7]);   
            $stmt->bindParam(9, $DataCard[8]);  
            $stmt->bindParam(10, $now);  
                 
             // Excecute
            if($stmt->execute())
            {
                $lastId = $this->PDOCON->lastInsertId();
       
                $ComisionCompany=PDOSS::GetDataPaynetCompany($DataCard[1]);
                if(sizeof($ComisionCompany)>0)
                {
                    $DataPayNet[0]=$ComisionCompany['ComisionPaynet'];
                    $DataPayNet[1]=$ComisionCompany['IvaComisionPaynet'];
                }
                
                
                $Query      = "INSERT INTO `paynet_cards`(`Id`,`Company`, `IdCard`, `ComisionAdmin`, `IvaComisionAdmin`, `ComisionCompany`, `IvaComisionCompany`,`RegisterDate`,`UpdateDate`) 
                VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?)";          
                $stmt       = $this->PDOCON->prepare( $Query );
                $stmt->bindParam(1, $DataCard[1]);
                $stmt->bindParam(2, $lastId);
                $stmt->bindParam(3, $DataPayNet[0]);
                $stmt->bindParam(4, $DataPayNet[1]);
                $stmt->bindParam(5, $DataPayNet[2]);
                $stmt->bindParam(6, $DataPayNet[3]);
                $stmt->bindParam(7, $now);
                $stmt->bindParam(8, $now);   

                if($stmt->execute())
                {
                    $Result=
                    [
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>'00'
                    ];
                }
                else
                {
                    $Result=
                    [
                        'SystemTraceAuditNumber'=>date('ymdHis'),
                        'ResponseCode'=>'24',
                        'ErrorMesage'=>'LA TARJETA NO PUDO SER DADA DE ALTA'
                    ];
    
                }
            }
            else
            {
                $Result=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'24',
                    'ErrorMesage'=>'USUARIO NO PUDO SER CREADO'
                ];
            }
           // $Result     = $stmt->fetch(PDO::FETCH_ASSOC);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function ValidateAuthCode( $Code,$Card)
    {
        try
        {
            $Result=[];
            $Query      = "SELECT `ide` FROM `analisis_cards` WHERE `Respuesta` = ? AND idcard=?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $Code );
            $stmt->bindParam( 1, $Card );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /** Inserta la informacion de una aplicacion de fondeo */
    public function insertFundstemp( $Card, $company, $Amount, $Concept,$authCode,$type) 
    {
        try 
        {
        $status='1';
        $now=date('Y-m-d H:i:s');
        $Query      = "INSERT INTO `Funds_Temp`(`Id`, `Card`, `Amount`, `Concept`, `AuthCode`, `Company`, `IdType`,`Status`, `DateInsert`,`DateUpdate`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?)";          
        $stmt       = $this->PDOCON->prepare( $Query );
         // Bind
        $stmt->bindParam(1, $Card);
        $stmt->bindParam(2, $Amount);
        $stmt->bindParam(3, $Concept);
        $stmt->bindParam(4, $authCode);
        $stmt->bindParam(5, $company);
        $stmt->bindParam(6, $type);
        $stmt->bindParam(7, $status);
        $stmt->bindParam(8, $now);
        $stmt->bindParam(9, $now);
        if($stmt->execute())
        {
            $Result=
            [
                'SystemTraceAuditNumber'=>date('ymdHis'),
                'ResponseCode'=>'00'
            ];
        }
        else
        {
            $Result=
            [
                'SystemTraceAuditNumber'=>date('ymdHis'),
                'ResponseCode'=>'24',
                'ErrorMesage'=>'REGISTRO INCOMPLETO'
            ];
        }
        return $Result;
        } 
        catch ( Exception $e ) 
        {
            return $e->getMessage();
        }
    }

    /* Validate Company With KeyCompany* */
    public function ConceptPaynetByCompany($Card)
    {
        try
        {
            $Query      = "SELECT B.`ConceptPaynet` FROM `cards` AS A INNER JOIN `companys` AS B ON A.company=B.ide WHERE substr(A.`idcard`,-8) = ?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $Card );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    public function GetRegisterDuplicated( $card,$amount,$hora)
    {
        try
        {
            $Query      = "SELECT `Id` FROM `paynet_comision` WHERE `Card` = ? AND Amount_Charge=? AND ((TIMESTAMPDIFF(SECOND,`Date`, ?))<60) AND `Status`=2";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $card );
            $stmt->bindParam( 2, $amount );
            $stmt->bindParam( 3, $hora );

            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /*Save Data Paynet**/
    public function InsertPaymentpay($Data)
    {
        try
        {
            $Query      = "INSERT INTO `paynet_comision`(`Id`, `Card`, `Amount_Charge`,`AmountPaynet`,`Date`, `Concepto`, `User`, `Company`, `Comision_apply`, `Status`,`ComisionAdmin`,`IvaComisionAdmin`,`AmountComisionAdmin`,`AmountIvaComisionAdmin`,`ComisionCompany`,`IvaComisionCompany`,`AmountComisionCompany`,`AmountIvaComisionCompany`,`DatePayment`) VALUES (NULL,?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?,?)";          
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam(1, $Data[0]);
            $stmt->bindParam(2, $Data[1]);
            $stmt->bindParam(3, $Data[2]);
            $stmt->bindParam(4, $Data[3]);
            $stmt->bindParam(5, $Data[4]);
            $stmt->bindParam(6, $Data[5]);
            $stmt->bindParam(7, $Data[6]);
            $stmt->bindParam(8, $Data[7]);
            $stmt->bindParam(9, $Data[8]);
            $stmt->bindParam(10, $Data[10]);
            $stmt->bindParam(11, $Data[11]);
            $stmt->bindParam(12, $Data[12]);
            $stmt->bindParam(13, $Data[13]);
            $stmt->bindParam(14, $Data[14]);
            $stmt->bindParam(15, $Data[15]);
            $stmt->bindParam(16, $Data[16]);
            $stmt->bindParam(17, $Data[17]);
            $stmt->bindParam(18, $Data[3]);


            $respuesta=0;
            if($stmt->execute())
            {
                $lastId = $this->PDOCON->lastInsertId();
                $respuesta=$lastId;      
            }
           
            return  $respuesta;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }

    }

    public function SaveDatapayment( $Data )
    {
        $email        = new sendmail();
        $fecha=date('Y-m-d H:i:s');
        $DataPayment=
        [
            $Data[2],//tarjeta
            $Data[3],//Montoreferencia
            $Data[13],//MontoPaynet
            $fecha,//DAte
            $Data[14],//concept
            '1',//User
            $Data[0],//Empresa
            $Data[1],//suma de comisiones
            '1',
            '',
            $Data[4], //comision admin
            $Data[5], //iva comision admin
            $Data[8], // value comision admin
            $Data[9], // value iva comision admin
            $Data[6], //comision company
            $Data[7], //iva comision company
            $Data[10], //value comision company
            $Data[11] // value iva comision company

        ];
        
        $movement_duplicated=PDOSS::GetRegisterDuplicated($Data[2],$Data[3],$fecha);
        if(sizeof($movement_duplicated)>0)
        {
            $answer_API=
            [
                'SystemTraceAuditNumber'=>date('ymdHis'),
                'ResponseCode'=>"01",
                'ErrorMesage'=>"POSIBLE INTENTO DE COBRO DUPLICADO"
            ];
            $mail_mensaje='<table>
            <tr><td> Tarjeta </td><td> '.$Data[2].'</td></tr>'.
            '<tr><td> Monto del deposito </td><td> '.$Data[3].'</td></tr>'.
            '<tr><td> Motivo Error </td><td> '.'POSIBLE INTENTO DE COBRO DUPLICADO'.'</td></tr></table>';
            $email->enviarmail($mail_mensaje,'INTENTO DE COBRO DOBLE PAYNET');

        }
        else
        {
            $Id_movement=PDOSS::InsertPaymentpay($DataPayment);
            $answer_API=
                [   
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>"00",
                    'AuthorizationCode'=>$Id_movement
                ];
            
        }
        return $answer_API;
    }

    /*Get Sum Amount Company* */
    public function GetBalanceCompanys()
    {
        try
        {
            $Query      = "SELECT SUM(`fund`)  AS 'suma' FROM `companys`";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /*Get Sum Amount Company* */
    public function GetBalanceCompanyByIde($IdCompany,$status,$type)
    {
        try
        {
            $Query      = "CALL `GetBalanceCompany` (?, ?, ?)";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam(1, $IdCompany);
            $stmt->bindParam(2, $status);
            $stmt->bindParam(3, $type);
            if($stmt->execute())
            {
                $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            }
            else
            {
                $Result[0]=0;
            }
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /*Get Sum Amount Company* */
    public function GetIdCompanyByUser($User)
    {
        try
        {
            $Query      = "SELECT company FROM `users` WHERE email=?";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam( 1, $User );
            $stmt->execute();
            $Result     = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);  
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /*Get Sum Amount Company* */
    public function SetChangeUser($CP,$UP,$CU,$User,$Card)
    {
        try
        {
            $now=date('Y-m-d H:i:s');
            $Query      = "CALL `ChangeUser` (?, ?, ?, ?, ?, ?)";
            $stmt       = $this->PDOCON->prepare( $Query );
            $stmt->bindParam(1, $CP);
            $stmt->bindParam(2, $UP);
            $stmt->bindParam(3, $CU);
            $stmt->bindParam(4, $User);
            $stmt->bindParam(5, $Card);
            $stmt->bindParam(6, $now);
            
            if($stmt->execute())
            {
                $Result=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'00'
                ];
            }
            else
            {
                $Result=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>'24',
                    'ErrorMesage'=>'REGISTRO INCOMPLETO'
                ];
            }
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    

}