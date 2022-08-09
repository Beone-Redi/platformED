<?php 

/**
 * Se define el área geografica.
 */
date_default_timezone_set( 'America/Monterrey' );


class app 
{
    protected $UrlBonec;
    protected $clientKey;

    function __construct()
    {
        $this->UrlBonec     = "https://bigonec.westus.cloudapp.azure.com/secure/integrador/API";

    }
   
    //Update 18/09/20 
 
    /**Conector base de datos */
    public function conecto() 
    {
        $usuario    = 'admin';
        $contraseña = '{h{47U}*m0coY2(jj';

        try 
        {
            $mbd = new PDO( 'mysql:host=localhost;dbname=ener', $usuario, $contraseña, [ PDO::MYSQL_ATTR_LOCAL_INFILE => true ] );
            $mbd->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            return $mbd;
        } 
        catch ( PDOException $e ) 
        {
            print "¡Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

    /** * API BONEC.*/
    public function apiBonec( $API, $postData ) //ASXWZ7AJ3SRFDSK1KISW66
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_POST, TRUE );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
        curl_setopt( $ch, CURLOPT_URL, $this->UrlBonec );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt( $ch, CURLOPT_TIMEOUT, '200' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $postData );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, [ 'Content-Type:application/json' ] );
        $Data = curl_exec( $ch );
        curl_close( $ch );

        $Bonec = json_decode( $Data, TRUE );
        switch ( $API ) 
        {
            case 'keyTrans':
                $Return = ( isset( $Bonec[ 'AuthorizationCode' ] ) ) ? '00' . $Bonec[ 'AuthorizationCode' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'maBalance':
                $Return = ( isset( $Bonec[ 'TicketMessage' ] ) ) ? '00' . $Bonec[ 'TicketMessage' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'applyPay':
                $Return = ( isset( $Bonec[ 'Auth_Code' ] ) ) ? '00' . $Bonec[ 'Auth_Code' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            
            default:
                $Return = FALSE;
        }
        
        return $Return;
    }
    
    
    /*Obtiene la llave para transacion con LA API* */
    public function get_KeyAPI() 
    {
        try 
        {
            $con = app::conecto();
            $Key_API = '';
            $sql = $con->prepare( "SELECT Key_API FROM Keys_API" );
            $sql->execute();
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Key_API= $row['Key_API'] ;
            }
            $con = $sql = NULL;
            return $Key_API;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get All product Platform(Carnet,Mastercard)*/
    public function getProductsPlatform()
    {
        try 
        {
            $con = app::conecto();
            $Products = [];
            $sql = $con->prepare( "SELECT Product,AgreementId,ProductId,Id,label_convenio FROM products_platform WHERE  `Status`=1  " );
            $sql->execute();
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Products, $row["Product"], $row["AgreementId"], $row["ProductId"],$row["Id"],$row["label_convenio"]);
        
            }
            $con = $sql = NULL;
            return $Products;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*geT AmountMasterAcount Obtiene el saldo de la cuenta maestra con el agrementId y PRoduct Id*/
    public function getAmountMasterAcount($AgreementId,$ProductId) 
    {
        try 
        {
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API2 = 'maBalance';
                $postData2 = json_encode( 
                    [
                        'maBalance'     => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),//TOKEN OBTENIDO
                        'AgreementId'   => $AgreementId,
                        'ProductId'     => $ProductId
                    ] 
                );    
                $Return     = app::apiBonec( $API2, $postData2 );    
                $Return=substr( $Return, 2 );
            }
            else
            {
                $Return = 0;
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    public function SumAllFundcompany()
    {
        try 
        {
            $con        = app::conecto();
            $sql        = $con->prepare( "SELECT SUM(`fund`) AS 'total' FROM `companys`" );
            $sql->execute();
            $res        = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Resp = $row["total"];
            }
            $con = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    

    public function LastIdreport()
    {
        try 
        {
            $con        = app::conecto();
            $sql        = $con->prepare( "SELECT MAX(`Id_Mov`) AS 'Id' FROM `Saldos_MA_BC`" );
            $sql->execute();
            $res        = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Resp = $row["Id"];
            }
            $con = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }


    /*Inserta nuevo registro de fondeo a cuenta maestra* */
    public function new_Register($Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `Saldos_MA_BC` ( `Id`,`Id_Convenio`, `Concept`, `Saldo`,`Id_Mov`,`Fecha`,`Observaciones`) 
                VALUES (NULL,?,?,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2],$Data[3],$Data[4],$Data[5]] );
            $con = $sql = NULL;
            return TRUE;
            
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get DataPayment(Carnet,Mastercard)*/
    public function getDataPayNet($Mounth)
    {
        try 
        {
            $con = app::conecto();
            $Products = [];
            $sql = $con->prepare( "SELECT `Id`,`Card`,`AmountPaynet`,`Concepto`,`Company` FROM `paynet_comision` WHERE  `Status`=1 and `Date` LIKE ?" );
            $sql->execute([$Mounth."%"]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Products, $row["Id"], $row["Card"], $row["AmountPaynet"],$row["Concepto"],$row["Company"]);
            }
            $con = $sql = NULL;
            return $Products;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get DataIntebntPayment(Carnet,Mastercard)*/
    public function getNumbersIntentPayment($Card,$Idmov)
    {
        try 
        {
            $con = app::conecto();
            $Products = 0;
            $sql = $con->prepare( "SELECT COUNT(`Id`) AS 'Intentos' FROM `IntentsPayments` WHERE  `Status`=1 AND `Card`=? AND `IdMov`=?" );
            $sql->execute([$Card,$Idmov]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Products =$row["Intentos"];
          
            }
            $con = $sql = NULL;
            return $Products;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Inserta nuevo registro de fondeo Intento de Pago* */
    public function newRegisterIntentPayment($Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `IntentsPayments` ( `Id`,`Card`, `IdMov`, `Amount`,`DatePayment`,`Status`,`msg`) 
                VALUES (NULL,?,?,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2],$Data[3],$Data[4],$Data[5]] );
            $con = $sql = NULL;
            return TRUE;
            
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

     /*geT AmountMasterAcount Obtiene el saldo de la cuenta maestra con el agrementId y PRoduct Id*/
   
    
    public function ApplyCharges($Card,$Monto,$Concept,$idMov,$Company) 
    {
        try 
        {
            $con    = app::conecto();
            
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API2 = 'applyPay';
                $postData2 = json_encode( 
                    [
                        'applyPay'     => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),//TOKEN OBTENIDO
                        'bindCard'   => $Card,
                        'Amount'     => $Monto,
                        'Description'     => $Concept
                        
                    ] 
                );    
                
                $dataIntentPayment=
                [
                    $Card,//0
                    $idMov,//1
                    $Monto,//2
                    date('Y-m-d H:i:s'),//3
                    '1'//4
                ];
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                    VALUES (NULL,?,?,?,?,?,'')" );
                $sql->execute( [ $Company, $Card, $Monto, date('Y-m-d H:i:s'),1 ] );
                
                $Return     = app::apiBonec( $API2, $postData2 );  
                $Res1=substr( $Return, 2 );
                
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                    VALUES (NULL,?,?,?,?,?,?)" );
                $sql->execute( [ $Company, $Card, $Monto, date('Y-m-d H:i:s'),1,$Res1 ] );
                  
                if ( substr( $Return, 0, 2) <> '01' )
                {
                    $dataIntentPayment[4]=2;
                }
                $dataIntentPayment[5]=$Return;
 
                app::newRegisterIntentPayment($dataIntentPayment);
                return $Return;

            }
            else
            {
                $Return = 0;
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Actualizar la contraseña*/
    public function updateStatusPayNet( $Id,$Status,$Id_Transaction ) 
    {
        try
        {
            $fecha=date('Y-m-d H:i:s');
            $con    = app::conecto();
            $sql    = $con->prepare("UPDATE paynet_comision SET Status=?,Id_Transactions=?,`DatePayment`=? WHERE Id=?" );
            $sql->execute( [ $Status,$Id_Transaction,$fecha,$Id ] );
            $con    = $sql = NULL;
            return TRUE;
        }   
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Get DataPayment(Carnet,Mastercard)*/
    public function getDataPayment($Mounth)
    {
        try 
        {
            $con = app::conecto();
            $Products = [];
            $sql = $con->prepare( "SELECT `Id`,`Card`,`Amount`,`Concept`,`AuthCode`,`Company`,`IdType` FROM `Funds_Temp` WHERE  `Status`=1 and `DateInsert` LIKE ?" );
            $sql->execute([$Mounth."%"]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Products, $row["Id"], $row["Card"], $row["Amount"],$row["Concept"],$row["AuthCode"],$row["Company"],$row["IdType"]);
            }
            $con = $sql = NULL;
            return $Products;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Validate Exist AuthCode* */
    public function ValidateAuthCode( $Code)
    {
        try
        {
            $Result='';
            $con    = app::conecto();
            $sql    = $con->prepare("SELECT `ide` FROM `analisis_cards` WHERE `Respuesta` = ?");
            $sql->execute( [ $Code] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Result =$row["ide"];
          
            }
            $con = $sql = NULL;
            return  $Result;
        }
        catch ( PDOException $e )
        {
            return $e->getMessage();
        }
    }

    /** Inserta la informacion de una aplicacion de fondeo */
    public function applyFundsAPI( $Card, $company, $Amount, $Concept,$authCode) 
    {
        try 
        {
      
            $user=1;
            $found_by=2;
            $Res='';
            $now=date('Y-m-d H:i:s');
            $con = app::conecto();
     
            //insert analisis
            $sql = $con->prepare("INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`, `Usuario`, `Respuesta`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");          
            $sql->execute( [ $company, $Card,$Amount,$now,$user,$Res] );
                  
            $sql = $con->prepare("INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`, `Usuario`, `Respuesta`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");          
            $sql->execute( [ $company, $Card,$Amount,$now,$user,$authCode] );
                   
            /*Registro de movimientos* */
            $sql                    = $con->prepare( "CALL applyPayCardAPI(?,?,?,?,?,?,?)" );
            $Return=$sql->execute( [ $Card, $Amount,$company,$now,$user,$Concept,$found_by ] );
                           
            if($Return )
            {
                $Result='00Datos guardados correctamete';
            }
            else
            {
                $Result='01No se guardo toda la informacion';
            }
                  
            return $Result;
        } 
        catch ( Exception $e ) 
        {
            return $e->getMessage();
        }
    }

    /** Inserta la informacion de un reverso de tarjeta */
    public function reverseFundsAPI( $Card, $company, $Amount, $Concept,$authCode) 
    {
   
        try 
        {
            $user=1;
           $found_by=2;
           $Res='';
            $now=date('Y-m-d H:i:s');

            $con = app::conecto();
   
            //insert analisis
           $sql = $con->prepare("INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`, `Usuario`, `Respuesta`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");          
           $sql->execute( [ $company, $Card,$Amount,$now,$user,$Res] );
                
            $sql = $con->prepare("INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`, `Usuario`, `Respuesta`) VALUES (NULL, ?, ?, ?, ?, ?, ?)");          
           $sql->execute( [ $company, $Card,$Amount,$now,$user,$authCode] );
                 
            /*Registro de movimientos* */
            $sql                    = $con->prepare( "CALL reversePayCardAPI(?,?,?,?,?,?,?)" );
            $Return=$sql->execute( [ $Card, $Amount,$company,$now,$user,$Concept,$found_by ] );
            //$sql->execute( [ $newAmountcompany, $company] );
           if($Return)
            {
                $Result='00Datos guardados correctamete';
            }
            else
            {
               $Result='01No se guardo toda la informacion';
            }    
            return $Result;
        } 
        catch ( Exception $e ) 
        {
            return $e->getMessage();
        }
    }
 
    
    /*Actualizar el estatus del pago en caso de ser nuevo*/
    public function updateStatusPayment( $Id) 
    {
        try
        {
            $fecha=date('Y-m-d H:i:s');
            $con    = app::conecto();
            $sql    = $con->prepare("UPDATE Funds_Temp SET Status=2,DateUpdate=? WHERE Id=?" );
            $sql->execute( [ $fecha,$Id ] );
            $con    = $sql = NULL;
            return TRUE;
        }   
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    } 

    /*Actualizar el estatus del pago en caso de ser duplicado*/
    public function updateStatusPaymentDuplied( $Id) 
    {
        try
        {
            $fecha=date('Y-m-d H:i:s');
            $con    = app::conecto();
            $sql    = $con->prepare("UPDATE Funds_Temp SET Status=3,DateUpdate=? WHERE Id=?" );
            $sql->execute( [ $fecha,$Id ] );
            $con    = $sql = NULL;
            return TRUE;
        }   
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    


}
?>