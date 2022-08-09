<?php
/**
 * Script:      coredata.php
 * Descripción: Contine la clase APP y sus funciones provee la inteligencia de la app.
 * Version:     18.0613
 * Autor:       Sergio Marquez
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

/**
 * Activa las variables de $_SESSION.
 */
ini_set( 'memory_limit', '-1' );

if ( !isset( $_SESSION ) ) 
{
    session_start();
}

/**
 * Se define el área geografica.
 */
date_default_timezone_set( 'America/Monterrey' );

/**
 * clase administradora 
 */
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
            case 'inCardPlatform':
                $Return = ( isset( $Bonec[ 'TicketMessage' ] ) ) ? '00' . $Bonec[ 'TicketMessage' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'inRegisterPlatform':
                $Return = ( isset( $Bonec[ 'idUser' ] ) ) ? '00' . $Bonec[ 'idUser' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'maBalance':
                $Return = ( isset( $Bonec[ 'TicketMessage' ] ) ) ? '00' . $Bonec[ 'TicketMessage' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'inLogin':
                $Return = ( isset( $Bonec[ 'idUser' ] ) ) ? '00' . $Bonec[ 'idUser' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
             /*Aplica un cargo a la tarjeta (Reverso tarjeta)*/
             case 'applyPay':
                $Return = ( isset( $Bonec[ 'Auth_Code' ] ) ) ? '00'.$Bonec[ 'Auth_Code' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            /*Aplica un abono a la tarjeta (Fondeo tarjeta)*/
            case 'reversePay':
                $Return = ( isset( $Bonec[ 'Auth_Code' ] ) ) ? '00'. $Bonec[ 'Auth_Code' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'inCardBalance':
                $Return = ( isset( $Bonec[ 'Balance' ] ) ) ? '00' . $Bonec[ 'Balance' ] : '01' . $Bonec[ 'ErrorMessage' ];
                break;
            case 'cardTrans':
                $Return = $Bonec;
                break;
            default:
                $Return = FALSE;
        }
        
        return $Return;
    }
 
    /*Logo plataform Nivel admin 18/09/20 */
    public function getlogoPlatform() 
    {
        try 
        {
            $con= app::conecto();
            $logo ='';
            $sql=$con->prepare("SELECT picture FROM `companys` WHERE perfil='ADMIN'");
            $sql->execute([]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                $logo=$row["picture"];
            }
            return $logo;
            $con = $sql = NULL;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Login user */
    public function login( $aUser ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql        = $con->prepare( "CALL `LogUser` (?, ?)" );
            $sql->execute( [ $aUser[0], $aUser[1] ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $compania   = [ $row['ID'], $row['IDCOMPANY'], $row['NAMECOMPANY'], $row['NAMEPERFIL'], $row['FULLNAMEUSER'], $row['PERFILOPTIONS'] ];
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Check if user accept terms and conditions*/ 
    public function validate_terms_conditions( $IdUser ) 
    {
        try 
        {
            $con = app::conecto();
            $Status = '';
            $sql = $con->prepare( "SELECT Id_user FROM log_terms_conditions WHERE Id_user=?" );
            $sql->execute( [ $IdUser ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Status= $row['Id_user'];
            }
            $con = $sql = NULL;
            return $Status;

        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Obtiene los datos de permisos* */
    public function getoptionsUser( $profile ) 
    {
        try 
        {
            $con = app::conecto();
            $data = '';
            $sql = $con->prepare( "SELECT Options FROM `profiles` WHERE Name=? AND Status='ACTIVE'" );
            $sql->execute( [ $profile ] ); 
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $data=$row["Options"];
            }
            $con = $sql = NULL;
            return $data;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Insert user accept terms*/
    public function new_User_Term_Conditions($User) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `log_terms_conditions` ( `Id`,`Id_user`, `Status`, `Date`) VALUES (NULL,?,?,?)" );
            $sql->execute( [ $User, 'ACEPTADO',date('Y-m-d H:i:s')] );
            $con = $sql = NULL;
            return TRUE;            
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Comparar la variable de sesion con un opt de permiso* */
    public function optSearch($opciones,$num_opt)
	{
		try
		{
        	$respuesta=1;
        	$pos=strpos($opciones,$num_opt);
        	if ($pos === FALSE) {
                $respuesta = 0;
            }
            return $respuesta;
        }
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
        
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
            $sql = $con->prepare( "SELECT Product,AgreementId,ProductId,Id FROM products_platform" );
            $sql->execute();
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Products, $row["Product"], $row["AgreementId"], $row["ProductId"],$row["Id"]);
        
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

    /*** Actualiza el saldo del administrador  */
    public function UpdateFoundAdmin( $masterBalance )
    {
        try 
        {
            $con        = app::conecto();
            $DCompany   = app::sumFundsCompany();
            $fundsAdmin = app::admFundsCompany();
            $FundsCompany=$DCompany-$fundsAdmin;
            $Resp       = 0;
            $newAmount=$masterBalance-$FundsCompany;
            $sql        = $con->prepare( "UPDATE `companys` SET `fund` = ? WHERE `ide` = ?" );
            $sql->execute( [ $newAmount, '1' ] );
            $Resp       = $newAmount;
            $con        = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Suma de los fondos de todas las empresas incluido el administrador. */
    public function sumFundsCompany() 
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

     /** * aTransFonds in cards*/
    public function aTransFondsCards() 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date( 'Y-m' );
            $sql        = $con->prepare( "SELECT SUM(`fund`) AS 'total' FROM `funds` WHERE `up_date` LIKE '" . $now . "%' AND `description` LIKE '%TARJETA' AND `company` = ?" );
            $sql->execute( [ $_SESSION['EMPRESA'] ] );
            $res        = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Resp = $row["total"];
            }
            $con        = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Monto del administrador* */
    public function admFundsCompany() 
    {
        try 
        {
            $con        = app::conecto();
            $sql        = $con->prepare( "SELECT `fund` FROM `companys` WHERE `ide` = '1'" );
            $sql->execute();
            $res        = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Resp = $row["fund"];
            }
            $con        = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Transacciones del mes en base a una descripcion y empresa */
    public function TransFoundsCompanyByMonth($description) 
    {
        try 
        {
            $con        = app::conecto();
            $Month        = date( 'Y-m' );
            $sql        = $con->prepare( "SELECT SUM(`fund`) AS 'total' FROM `funds` WHERE `up_date` LIKE '" . $Month . "%' AND `description`=?" );
            $sql->execute([$description]);
            $res        = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $Resp = $row["total"];
            }
            $con        = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Bitacora nivel administrador* */
    public function BitacoraCompanys($description) 
    {
        try 
        {
            $con = app::conecto();
            $Bitacora = [];
            $sql = $con->prepare( "SELECT B.`social_reason`,A.`fund`,A.`up_date`,A.`description` FROM `funds` AS A INNER JOIN `companys` AS B ON B.ide=A.company WHERE A.`description` LIKE ? ORDER BY A.ide DESC LIMIT 10" );
            $sql->execute(['%' . $description . '%']);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Bitacora, $row["social_reason"], $row["fund"], $row["up_date"], $row["description"] );
            }
            $con = $sql = NULL;
            return $Bitacora;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Bitacora nivel empresa* */
    public function BitacoraCards($idCompany,$description) 
    {
        try 
        {
            $con = app::conecto();
            $Bitacora = [];
            $sql = $con->prepare( "SELECT `idcard`,`fund`,`up_date`,`description` FROM `funds` WHERE `description` LIKE ? AND `company`=? ORDER BY ide DESC LIMIT 10" );
            $sql->execute(['%' . $description . '%',$idCompany]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Bitacora, $row["idcard"], $row["fund"], $row["up_date"], $row["description"] );
            }
            $con = $sql = NULL;
            return $Bitacora;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Ver que el password exista*/
    public function getPassExist( $pass ) 
    {
        try 
        {
            $con = app::conecto();
            $data = '';
            $sql = $con->prepare( "SELECT ide FROM `users` WHERE idkey=? AND ide=? " );
            $sql->execute( [ $pass[1], $pass[3] ] );         
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $data = $row["ide"];
            }
            $con = $sql = NULL;
            return $data;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualizar la contraseña*/
    public function change_password( $passw ) 
    {
        try
        {
            $con    = app::conecto();
            $sql    = $con->prepare("UPDATE `users` SET `idkey` = ? WHERE `ide` = ?" );
            $sql->execute( [ $passw[2],$passw[3] ] );
            $con    = $sql = NULL;
            return TRUE;
        }   
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    } 
    
    /*Ver el correo del usuario */
    public function viewEmailUser( $uID ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare("SELECT email, ide  FROM `users` WHERE ide=?");
            $sql->execute( [ $uID ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, $row["email"], $row["ide"]);
            }  
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Ver la info de la empresa* */
    public function viewCompany( $Company ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `company`, `social_reason`, `fullname`, `email`, `telephone`, `address`, `city`, `zip`, `aboutme`, `picture`, `perfil`, `fund`, `up_date`, `active`, `state`,`Id_Comision`,`Key_Company`,`mail_notifications` FROM `companys`  WHERE `ide` = ?" );
            $sql->execute( [ $Company ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["company"], $row["social_reason"], $row["fullname"], $row["email"], $row["telephone"], $row["address"], $row["city"], $row["zip"], $row["aboutme"], $row["picture"], $row["perfil"], $row["fund"], $row["up_date"], $row["active"],$row["state"],$row["Id_Comision"],$row["Key_Company"],$row["mail_notifications"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Cadena Peseudo Random. */
    public function urlToken($longitud = 100 )
    {
        $key        = '';
        $pattern    = '1234567890abcdefghijklmnopqrstuvwxyz';
        $max        = strlen( $pattern ) - 1;
        for( $i=0; $i < $longitud; $i++ ) 
        {
            $key .= $pattern 
            { 
                mt_rand( 0, $max )
            };
        }
        return $key;
    }

    /* Actualiza el estatus de la compañia y el usuario administrador de la misma*/
    public function updateStatusCompany( $idu, $sta,$email )
    {
        try 
        {
            $con    = app::conecto();
            $Status = ( $sta <> 1 ) ? 1:0;
            $sql    = $con->prepare( "UPDATE `companys` SET `active` = ? WHERE `ide` = ?" );
            $sql->execute( [ $Status, $idu ] );
            $Status = ( $sta <> 1 ) ? 1:0;
            $sql    = $con->prepare( "UPDATE `users` SET `active` = ? WHERE `email` = ?" );
            $sql->execute( [ $Status, $email ] );
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Search data company with perfil*/
    public function viewAllCompanysByPerfil($perfil) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `ide`, `company`, `social_reason`, `telephone`, `picture`, `fund`, `active` FROM `companys` WHERE `perfil`=? ORDER BY company ASC");
            $sql->execute([$perfil]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["company"], $row["social_reason"], $row["telephone"], $row["picture"], $row["fund"], $row["active"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Get All State in Mexico*/
    public function GetAllState() 
    {
        try 
        {
            $con = app::conecto();
            $state = [];
            $sql = $con->prepare( "SELECT `nombre` FROM `estados`" );
            $sql->execute();
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $state, $row["nombre"]);
            }
            $con = $sql = NULL;
            return $state;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /**Get cities correspondiente al estado */
    public function GetCitiesByState($state) 
    {
        try 
        {
            $con = app::conecto();
            $state2 = [];
            $sql = $con->prepare( "SELECT A.`nombre` as 'name' FROM `ciudades` AS A INNER JOIN `estados` AS B ON A.`estado_id`=B.`id` WHERE B.`nombre`=?" );
            $sql->execute([$state]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $state2, utf8_encode($row["name"]));
            }
            $con = $sql = NULL;
            return $state2;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Validar Input */
    public function validoInput( $dato )
    {
        $dato = trim( $dato );
        $dato = stripslashes( $dato );
        $dato = htmlspecialchars( $dato );
        return $dato;
    }

    /* Create User in Company and User* */
    public function createCompany( $aCompany, $image1, $image2,$usuario ) 
    {
        try 
        {
            $con        = app::conecto();
            $cadena_base	=  'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		    $cadena_base	.= '0123456789' ;
		    $keycompany 		= '';
		    $limite 		= strlen( $cadena_base ) - 1;
    		for ( $i = 0; $i <=12; $i++ )
	        {
			    $keycompany	.= $cadena_base[ rand( 0, $limite ) ];
		    }
            $sql = $con->prepare( "INSERT INTO `companys` (`ide`, `company`, `social_reason`, `fullname`, `email`, `telephone`, `address`, 
            `city`, `zip`, `aboutme`, `picture`, `perfil`, `fund`, `up_date`, `active`, `clientKey`,`state`,`RFC`,`CURP`,`INE`,`Id_Comision`,`Key_Company`,`mail_notifications`,`ComisionPaynet`,`IvaComisionPaynet`,`IdCardsUser`) 
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1', '1',?,?,?,?,?,?,?,?,?,?)" );
            $sql->execute( [ $aCompany[0], $aCompany[1], $aCompany[2], $aCompany[3], $aCompany[4], $aCompany[5], $aCompany[7], $aCompany[6], 
            $aCompany[10], $image1, $aCompany[9], '0', $aCompany[12] ,$aCompany[8],$aCompany[13],$aCompany[14],$aCompany[15],$aCompany[16],$keycompany,$aCompany[17],$aCompany[18],$aCompany[19],$aCompany[20]] );
            $empresa    = $con->lastInsertId();// Id empresa creada
            $sql = $con->prepare( "INSERT INTO `users`( `ide`, `email`, `company`, `fullname`, `address`, `city`, `zip`, `aboutme`, 
            `picture`,`idcard`, `perfil`, `up_date`, `active`, `idkey`,`phone`,`state`,`email2`,`perfil_TH`,`IdUser_API`) 
            VALUES (NULL,?,?,?,?,?,?,?,?,?,?,SUBSTRING(NOW(),1,10),1,?,?,?,?,?,?)" );
            $sql->execute( [ $aCompany[3], $empresa, $aCompany[2], $aCompany[5], $aCompany[7], $aCompany[6], 
                $aCompany[10], 'assets/img/' . $image2, '0', $aCompany[9], $aCompany[11],$aCompany[4],$aCompany[8],$aCompany[3],'','' ] );
            $sql = $con->prepare( "INSERT INTO `log_users` (`id`, `company`, `date`, `email`,`id_card`,`id_user_created`,`status`)
                VALUES (NULL,?,?,?,?,?,?)" );
            $sql->execute( [$empresa,date('Y-m-d H:i:s'),$aCompany[3],'',$usuario,'OK' ] );
            $con = $sql = NULL;
            return TRUE; 
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualiza la informacion de la compañia* */
    public function updateDataCompany( $Datos )
    {
        try 
        {
            $con    = app::conecto();
            $sql    = $con->prepare( "UPDATE `companys` SET `social_reason` = ?, `fullname` = ?, `telephone` = ?, `address` = ?
                , `city` = ?, `zip` = ?, `aboutme` = ?,`state` = ?,`Id_Comision` = ?,`mail_notifications` = ? WHERE `ide` = ?" );
            $sql->execute( [ $Datos[0], $Datos[1], $Datos[2], $Datos[3], $Datos[4], $Datos[5], $Datos[6], $Datos[7],$Datos[8],$Datos[9],$Datos[10] ] );
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Actualiza la informacion del usuario administrador de la empresa* */
    public function updateUserCompany( $Datos )
    {
        try 
        {
            $con    = app::conecto();
            $sql    = $con->prepare( "UPDATE `users` SET `fullname` = ?, `address` = ?, `city` = ?
                , `zip` = ?, `aboutme` = ?, `up_date` = ?,`phone` = ?,`state` = ? WHERE `email` = ?" );
            $sql->execute( [ $Datos[0], $Datos[1], $Datos[2], $Datos[3],$Datos[4], date('Y-m-d'), $Datos[5],$Datos[6],$Datos[7] ] );
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualiza la informacion del usuario en base al Ide* */
    public function updateStatusUser( $idu, $sta )
    {
        try 
        {
            $con    = app::conecto();
            $Status = ( $sta <> 1 ) ? 1:0;
            $sql    = $con->prepare( "UPDATE `users` SET `active` = ? WHERE `ide` = ?" );
            $sql->execute( [ $Status, $idu ] );
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /* Crear un usuario en base al perfil de empresa o administrador* */
    public function createUserProfile( $aPerfil, $image1,$usuario ) 
    {
        try 
        {
            $con            = app::conecto();
            $fileName       = $image1;
            $Return         = "01LA IMAGEN DE PERFIL NO FUE CARGADA.";
            if( isset( $fileName ) )
            {
                // Valida el Email.
                $sql = $con->prepare( "SELECT `ide` FROM `users` WHERE `email` = ?" );
                $sql->execute( [ $aPerfil[3] ] );
                $res = $sql->fetchAll();
                foreach ( $res as $row ) 
                {
                    $ExisteEmail = $row["ide"];
                }   
                if ( isset( $ExisteEmail ) )
                {
                    $Return = '01EL CORREO YA SE ENCUENTRA REGISTRADO EN EL SISTEMA.';
                    return $Return;
                    exit();
                }
                else
                {
                    $sql = $con->prepare( "INSERT INTO `users`
                    (`ide`, `email`, `company`, `fullname`, `address`, `city`, `zip`, `aboutme`, `picture`, `idcard`, `perfil`, `up_date`, `active`, `idkey`,`phone`,`state`,`email2`,`perfil_TH`) 
                    VALUES (NULL,?,?,?,?,?,?,?,?,?,?,SUBSTRING(NOW(),1,10),1,?,?,?,?,?)" );
                    $sql->execute( [ $aPerfil[3], $aPerfil[0], $aPerfil[2], $aPerfil[5], $aPerfil[7], $aPerfil[6], 
                    $aPerfil[11], $fileName, '0', $aPerfil[9], $aPerfil[13],$aPerfil[4],$aPerfil[8],$aPerfil[3],'' ] );                           
                    $sql = $con->prepare( "INSERT INTO `log_users` (`id`, `company`, `date`, `email`,`id_card`,`id_user_created`,`status`)
                    VALUES (NULL,?,?,?,?,?,?)" );
                    $sql->execute( [$aPerfil[0],date('Y-m-d H:i:s'),$aPerfil[3],'',$usuario,'OK' ] );
                    $Return2='00Usuario creado correctamente';
                    $con        = $sql = NULL;
                    return $Return2;   
                }
            }
            else
            {
                return $Return;
            }
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /** obtiene los perfiles a nivel admin o empresa */
    public function getNameProfilesbyLevel($level) // 
    {
        try 
        {
            $con        = app::conecto();
            $perfiles   = [];
            $query = $con->prepare("SELECT Name FROM `profiles` WHERE Level =?");
            $query->execute( [$level] );            
            $res = $query->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push($perfiles,$row["Name"]);
            }
            $con = $res = NULL;
            return $perfiles;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    } 

    /**Data User */ 
    public function viewUser( $uID ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT `ide`,`email`,`fullname`,`address`,`city`,`zip`,`aboutme`,`picture`,`perfil`,`phone`,`state`,`company`
                FROM `users` WHERE `ide` = ?" );
            $sql->execute( [ $uID ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, $row["ide"], $row["email"], $row["fullname"],$row["address"], $row["city"], $row["zip"], $row["aboutme"], $row["picture"], $row["perfil"], $row["phone"],$row["state"],$row["company"]);
            }     
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualiza info del usuario* */
    public function updateUser( $Datos )
    {
        try 
        {
            $con    = app::conecto();
            $sql    = $con->prepare( "UPDATE `users` SET `fullname` = ?, `address` = ?, `city` = ?
                , `zip` = ?, `aboutme` = ?,`phone` = ?,`state` = ?,`up_date` = ? WHERE `ide` = ?" );
            $sql->execute( [ $Datos[0], $Datos[3], $Datos[5], $Datos[4], $Datos[7],$Datos[2], $Datos[8], $Datos[9],$Datos[10] ] );
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Ver los datos de la empresa por busqueda de id. */
    public function viewDataCompanysById( $Empresa ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `ide`, `company`, `social_reason`, `telephone`, `picture`, `fund`, `active` FROM `companys` WHERE `ide` = ?" );
            $sql->execute( [ $Empresa ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["company"], $row["social_reason"], $row["telephone"], $row["picture"], $row["fund"], $row["active"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Inserta en el log de emails* */
    public function insertlogemail( $data) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date( 'Y-m-d H:i:s' );
            // Registra el movimiento.
           $sql        = $con->prepare( "INSERT INTO `data_mails`(`Id`,`Ide_incident`, `Mensage`, `Date`, `Respuesta`) 
                            VALUES ( NULL, ?, ?, ?, ?)" );
            $sql->execute( [ $data[0],$data[1],$now, $data[2]] );
            $con = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Apply Fund Company and update Amounts company and Admin* */
    public function applyFundsCompany( $CompanyIn, $CompanyOut, $amount,$user,$comentario,$transferencia,$comision,$porcentajecomision,$ivacomision ) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date( 'Y-m-d H:i:s' );
            // Actualiza los Fondos de la Cuenta Maestra.
            $sql        = $con->prepare( "CALL applyPayCompany(?,?,?,?,?,?,?,?,?,?)" );
            $Ret=$sql->execute( [ $CompanyIn, $CompanyOut,$now,$user,$comentario,$transferencia,$porcentajecomision,$comision,$ivacomision,$amount ] );
            // Actualiza los Fondos de la Empresa.
            $con = $sql = NULL;
            if ( $Ret )
            {
                $Return = '00EL MONTO FUE TRANSFERIDO CORRECTAMENTE A LA EMPRESA.';
            }
            else
            {
                $Return = '01NO FUE POSIBLE REALIZAR EL MOVIMIENTO,.';
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Reverse Fund Company and update Amounts company and Admin* */
    public function ReturnFundsCompany( $CompanyIn,$CompanyOut, $amount, $user,$comentario) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date( 'Y-m-d H:i:s' );
            $sql        = $con->prepare( "CALL reversePayCompany(?,?,?,?,?,?)" );
            $Ret=$sql->execute( [ $CompanyIn, $CompanyOut,$now,$user,$comentario,$amount ] );
            $con = $sql = NULL;
            if ( $Ret )
            {
                $Return = '00EL MONTO FUE TRANSFERIDO CORRECTAMENTE.';
            }
            else
            {
                $Return = '01NO ES POSIBLE REALIZAR EL MOVIMIENTO, INTENTE MAS TARDE.';
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Ver el correo de notificaciones de la empresa por busqueda de id. */
    public function GetMailsNotificationsByIdCompany( $IdCompany ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = '';
            $sql = $con->prepare( "SELECT `mail_notifications` FROM `companys` WHERE `ide` = ?" );
            $sql->execute( [ $IdCompany ] ); 
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $compania = $row["mail_notifications"];
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Obtiene los fondeos de todas las empresas incluidas las tarjetas* */
    public function getAllFundsCompanys( $dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT A.`ide`, B.`company`, A.`fund`, A.`up_date`, A.`description`,A.`idcard` FROM `funds` AS A INNER JOIN `companys` AS B ON B.`ide` = A.`company` WHERE STR_TO_DATE(A.`up_date`,'%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?"); //AND (A.`description` LIKE '%EMPRESA' OR A.`description` LIKE 'COMPANY%')" );
            $sql->execute( [ $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["company"], $row["fund"], $row["description"], $row["up_date"],$row["idcard"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * Obtiene fondeo de una empresa tambien incluye tarjetas */
    public function getFundsCompanysbyIde( $Company, $dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT A.`ide`, B.`company`, A.`fund`, A.`up_date`, A.`description`,A.`idcard` FROM `funds` AS A INNER JOIN `companys` AS B ON B.`ide` = A.`company` WHERE A.`company` = ? AND STR_TO_DATE(A.`up_date`,'%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?" );
            $sql->execute( [ $Company, $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["company"], $row["fund"], $row["description"], $row["up_date"],$row["idcard"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Obtener los fondeos a todas las empresas* */
    public function GetFundsAllCompany( $dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT A.`ide`, B.`company`, A.`fund`, A.`up_date`, A.`description` FROM `funds` AS A INNER JOIN `companys` AS B ON B.`ide` = A.`company` WHERE (A.`up_date` BETWEEN ? AND ?) AND A.`description` LIKE '%EMPRESA'" );
            $sql->execute( [ $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["company"], $row["fund"], $row["description"], $row["up_date"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** Obtener los fondeos y reversos a la compañia */
    public function GetFundstoCompany( $Company, $dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT A.`ide`, B.`company`, A.`fund`, A.`up_date`, A.`description` FROM `funds` AS A INNER JOIN `companys` AS B ON B.`ide` = A.`company` WHERE A.`company` = ? AND (A.`up_date` BETWEEN ? AND ?) AND A.`description` LIKE '%EMPRESA'" );
            $sql->execute( [ $Company, $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["company"], $row["fund"], $row["description"], $row["up_date"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Obtiene todos los nombres de los perfiles de la plataforma* */
    public function getAllNameProfiles() 
    {
        try 
        {
            $con        = app::conecto();
            $perfiles   = [];
            $sql = $con->prepare("SELECT Name FROM `profiles`");
            $sql->execute();            
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push($perfiles,$row["Name"]);
            }
            $con = $sql = NULL;
            return $perfiles;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Obtiene todos los Perfiles*/
    public function getAllProfiles() 
    {
        try 
        {
            $con= app::conecto();
            $Profile =[];
            $sql=$con->prepare("SELECT * FROM `profiles`");
            $sql->execute();
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                array_push($Profile,$row["Id"],$row["Name"],$row["Options"],$row["Level"],$row["Status"]);
            }
            $con = $sql = NULL;
            return $Profile;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Actualiza el estatus de un perfil del sistema* */
    public function updateStatusProfile($sta,$id )
    {
        try 
        {
            $con    = app::conecto();
            $Status = ( $sta <> 'ACTIVE' ) ? 'ACTIVE':'INACTIVE';
            $sql    = $con->prepare( "UPDATE `profiles` SET `Status` = ? WHERE `Id` = ?" );
            $sql->execute( [ $Status, $id ] );
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Valida el nombre de un perfil* */
    public function getnameProfile($name) 
    {
        try 
        {
            $con= app::conecto();
            $Profile ='';
            $sql=$con->prepare("SELECT Name FROM `profiles` WHERE Name=?");
            $sql->execute([$name]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                $Profile=$row["Name"];
            }
            $con    = $sql = NULL;
            return $Profile;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
        die();
        }
    }

    /*Añade  un nuevo perfil al sistema* */
    public function new_Profile($Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `profiles` ( `Id`,`Name`, `Options`, `Level`, `Status`) 
                VALUES (NULL,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2], $Data[3]] );
            $con = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Obtiene la informacion del perfil con el Id* */
    public function getProfilebyId($id) 
    {
        try 
        {
            $con= app::conecto();
            $Profile =[];
            $sql=$con->prepare("SELECT * FROM `profiles` WHere Id=?");
            $sql->execute([$id]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                array_push($Profile,$row["Id"],$row["Name"],$row["Options"],$row["Level"],$row["Status"]);
            }
            $con = $sql = NULL;
            return $Profile;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualiza los permisos del perfil* */
    public function UpdateprofilebyId($opciones,$id) 
    {
        try 
        {
            $con = app::conecto();
            $sql    = $con->prepare( "UPDATE `profiles` SET `Options` = ? WHERE `Id` = ?" );
            $sql->execute( [ $opciones, $id ] );
            $con = $sql = NULL;
            return TRUE;        
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Inserta nuevo registro de fondeo a cuenta maestra* */
    public function new_FoundMA($Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `founds_MA` ( `Id`,`Concept`, `Amount`, `Date_upload`,`Date_found`,`User`,`url_file`,`Comision`,`Saldo_MA`,`BalanceAdmin`,`Cuenta`) 
                VALUES (NULL,?,?,?,?,?,?,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2],$Data[3],$Data[4],$Data[5],$Data[6],$Data[7],$Data[8],$Data[9]] );
            $con = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Get All product Platform(Carnet,Mastercard) by Id*/
    public function getProductPlatformbyId($Id)
    {
        try 
        {
            $con = app::conecto();
            $Products = [];
            $sql = $con->prepare( "SELECT Product,AgreementId,ProductId,Id FROM products_platform WHERE Id=?" );
            $sql->execute([$Id]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $Products, $row["Product"], $row["AgreementId"], $row["ProductId"],$row["Id"]);
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

    /*Obtener los fondeos a la cuenta maestra por fechas* */
    public function GetfoundMA($dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `Id`,`Date_upload`,`Amount`,`Concept`,`url_file` FROM `founds_MA` WHERE `Date_upload` BETWEEN ? AND ?" );
            $sql->execute( [ $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["Id"], $row["Date_upload"], $row["Amount"], $row["Concept"], $row["url_file"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
      
    /** * ViewCardHolders */
    public function viewCardHolders( $idCompany ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT `ide`,`fullname`,`email` FROM `users` WHERE `company` = ? AND `active` = '1' AND `perfil` = 'EMPLEADO'" );
            $sql->execute( [ $idCompany ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, $row["ide"], $row["fullname"],$row["email"] );
            }
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** * ver las tarjetas por usuario */
    public function viewCardsByUsers($idCompany,$user ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT `ide`,`idcard` FROM `cards` WHERE `company` = ? and `user`=? ");
            $sql->execute( [ $idCompany,$user ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, $row["ide"], substr($row["idcard"],-8) );
            }
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get data cardholder By IdCard* */
    public function GetDataCHByCard( $IdCard ) 
    {
        try 
        {
            $con        = app::conecto();
            $usuarios   = [];
            $sql=$con->prepare("SELECT a.ide as 'idepersona', a.fullname, b.picture as 'empresa', a.perfil, a.active, a.email, a.picture,c.idcard 
                FROM `users` AS a LEFT JOIN `companys` AS b ON a.company = b.ide LEFT JOIN `cards` AS c ON c.user = a.email WHERE c.ide= ?");
            $sql->execute([$IdCard]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuarios, $row["idepersona"], $row["fullname"], $row["empresa"],substr($row["idcard"],-8), app::check_amountCard($row["idcard"]), $row["active"], $row["email"], $row["picture"] );
            }
            $con = $sql = NULL;
            return $usuarios;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /**get info de user */
    public function GetDataCH( $Ide ) 
    {
        try 
        {
            $con        = app::conecto();
            $usuarios   = [];
            $sql=$con->prepare("SELECT a.ide as 'idepersona', a.fullname, b.picture as 'empresa', a.perfil, a.active, a.email, a.picture 
                FROM `users` AS a LEFT JOIN `companys` AS b ON a.company = b.ide WHERE a.ide= ?");
            $sql->execute([$Ide]); 
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuarios, $row["idepersona"], $row["fullname"], $row["empresa"],0, 0, $row["active"], $row["email"], $row["picture"] );
            }
            $con = $sql = NULL;
            return $usuarios;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /**Se inserta un log cuando no se puede obtener el reporte de movimientos desde la API */
    public function insertlogEstadoCuenta( $company,$card,$intento,$msg) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date( 'Y-m-d H:i:s' );
            // Registra el movimiento.
           $sql        = $con->prepare( "INSERT INTO `log_movements`(`Id`,`id_company`, `card`, `date`, `intento`, `msg`) 
                            VALUES ( NULL, ?, ?, ?, ?, ? )" );
            $Ret         = $sql->execute( [ $company, $card, $now, $intento,$msg ] );
            $con = $sql = NULL;
            if ( $Ret )
            {
                $Return = '00SE TRANSFIRIERON LOS MONTOS DE FORMA EXITOSA.';
            }
            else
            {
                $Return = '01NO ES POSIBLE REALIZAR MOVIMIENTOS AHORA, INTENTE MAS TARDE.';
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
        
    /*Da de alta un usuario* */
    public function NewCardHolder( $aPerfil,$usuario,$fileName,$KC ) 
    {
        try 
        {
            $con            = app::conecto();
            //$fileName       = $image1;
            $now            = date( 'Y-m-d' );
            $Return         = '01FALTA ARCHIVO DE IMAGEN DEL USUARIO.';
            $sql = $con->prepare( "INSERT INTO `log_users` (`id`, `company`, `date`, `email`,`id_card`,`id_user_created`,`status`,`type_upload`)
                VALUES (NULL,?,?,?,?,?,?,?)" );
            $sql->execute( [$aPerfil[2],date('Y-m-d H:i:s'),$aPerfil[2],$aPerfil[13],$usuario,'PENDIENTE','1' ] );
            $idlog  = $con->lastInsertId();
            if( isset( $fileName ) )
            {
                // Valida el Email.
                $sql = $con->prepare( "SELECT `ide` FROM `users` WHERE `email` = ?" );
                $sql->execute( [ $aPerfil[2] ] );
                $res = $sql->fetchAll();
                foreach ( $res as $row ) 
                {
                    $ExisteEmail = $row["ide"];
                }
                if ( isset( $ExisteEmail ) )
                {
                    $Return = '01LA CUENTA DE CORREO YA FUE REGISTRADA EN EL SISTEMA.';
                    return $Return;
                    exit();
                }
                else
                {
                    $Accion1 = TRUE;
                }
                if ( $Accion1 )
                {
                    $Key_API=app::get_KeyAPI();
                    $API        = 'keyTrans';
                    $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
                    $Token1     = app::apiBonec( $API, $postData );
                    if ( substr( $Token1, 0, 2 ) <> '01' )
                    {
                        $API        = 'inRegisterPlatform';
                        $postData   = json_encode
                        ( 
                            [
                                'inRegisterPlatform'        => TRUE,
                                'clientKey'         => $Key_API,
                                'clientToken'       => substr( $Token1, 2 ),
                                'userCard'          => $aPerfil[2],
                                'passCard'          => $aPerfil[11],
                                'emailCard'         => $aPerfil[2],
                                'custonCamp'  => date('ymdHis'),//$lidInput1
                                'keyCompany'  => $KC
                            ]
                        );
                        $Accion2    = app::apiBonec( $API, $postData );
                        //$Accion2='00Exitoso';
                        if ( substr( $Accion2, 0, 2) <> '01' )
                        {
                            $Return     = $Accion2;
                            $sql = $con->prepare( "UPDATE `users` SET  `fullname`=?, `address`=?, `city`=?, `zip`=?, `aboutme`=?, `picture`=?, `phone`=?,`state`=?,`perfil_TH`=?,created_by=? WHERE `IdUser_API`=?");
                            $sql->execute( [  $aPerfil[1], $aPerfil[4], $aPerfil[6], $aPerfil[5], 
                            $aPerfil[9], $fileName, $aPerfil[3],$aPerfil[7],$aPerfil[8],1,substr( $Return, 2 )] );
                            $sql=$con->prepare("UPDATE `log_users` SET `status`='OK' WHERE `id`=?");
                            $sql->execute([$idlog]);
                            $con        = $sql = NULL;
                            //$Return = '00SE CREO CORRECTAMENTE EL USUARIO.';
                        }
                        else
                        {
                            $Return = $Accion2;
                        }
                    }
                    else
                    {
                        $Return = '01NO SE CREARON LOS TOKENS REPORTAR AL ADMINISTRADOR.';
                    }
                }
                else
                {
                    $Return = '01NO ES POSIBLE AGREGAR USUARIO INTENTE MAS TARDE.';
                }
                return $Return;
            }
            else
            {
                return $Return;
            }
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Alta de usuario con carga masiva* */
    public function NewCardHolderLayout( $aPerfil,$usuario,$fileName,$KC ) 
    {
        try 
        {
            $con            = app::conecto();
            //$fileName       = $image1;
            $Return         = '01FALTA ARCHIVO DE IMAGEN DEL USUARIO.';
            $sql = $con->prepare( "INSERT INTO `log_users` (`id`, `company`, `date`, `email`,`id_card`,`id_user_created`,`status`,`type_upload`)
                VALUES (NULL,?,?,?,?,?,?,?)" );
            $sql->execute( [$aPerfil[2],date('Y-m-d H:i:s'),$aPerfil[13],$aPerfil[14],$usuario,'PENDIENTE','2' ] );
            $idlog  = $con->lastInsertId();
            if( isset( $fileName ) )
            {
                // Valida el Email.
                $sql = $con->prepare( "SELECT `ide` FROM `users` WHERE `email` = ?" );
                $sql->execute( [ $aPerfil[13] ] );
                $res = $sql->fetchAll();
                foreach ( $res as $row ) 
                {
                    $ExisteEmail = $row["ide"];
                }
                if ( isset( $ExisteEmail ) )
                {
                    $Return = '01LA CUENTA DE CORREO YA FUE REGISTRADA EN EL SISTEMA.';
                    return $Return;
                    exit();
                }
                else
                {
                    $Accion1 = TRUE;
                }
                if ( $Accion1 )
                {
                    $Key_API=app::get_KeyAPI();
                    $API        = 'keyTrans';
                    $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
                    $Token1     = app::apiBonec( $API, $postData );
                    if ( substr( $Token1, 0, 2 ) <> '01' )
                    {
                        $API        = 'inRegisterPlatform';
                        $postData   = json_encode
                        ( 
                            [
                                'inRegisterPlatform'        => TRUE,
                                'clientKey'         => $Key_API,
                                'clientToken'       => substr( $Token1, 2 ),
                                'userCard'          => $aPerfil[13],
                                'passCard'          => $aPerfil[11],
                                'emailCard'         => $aPerfil[2],
                                'custonCamp'  => date('ymdHis'),//$lidInput1
                                'keyCompany'  => $KC
                            ]
                        );
                        $Accion2    = app::apiBonec( $API, $postData );
                        //$Accion2='00Exitoso';
                        if ( substr( $Accion2, 0, 2) <> '01' )
                        {
                            $Return     = $Accion2;
                            $sql = $con->prepare( "UPDATE `users` SET fullname=?,created_by=? WHERE `IdUser_API`=?");
                            $sql->execute( [  $aPerfil[1],1,substr( $Return, 2 )] );
                            $sql=$con->prepare("UPDATE `log_users` SET `status`='OK' WHERE `id`=?");
                            $sql->execute([$idlog]);
                            $con        = $sql = NULL;
                            //$Return = '00SE CREO CORRECTAMENTE EL USUARIO.';
                        }
                        else
                        {
                            $Return = $Accion2;
                        }
                    }
                    else
                    {
                        $Return = '01NO SE CREARON LOS TOKENS REPORTAR AL ADMINISTRADOR.';
                    }
                }
                else
                {
                    $Return = '01NO ES POSIBLE AGREGAR USUARIO INTENTE MAS TARDE.';
                }
                return $Return;
            }
            else
            {
                return $Return;
            }
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Alta de una nueva tarjeta* */
    public function NewCard( $Data,$DataComision,$KC ) 
    {
        try 
        {
            $con            = app::conecto();
            $now            = date( 'Y-m-d' );
            // Valida la tarjeta.
            $sql = $con->prepare( "SELECT `ide` FROM `cards` WHERE `idcard` = ?" ); // verifica si existe la tarjeta en el sistema
            $sql->execute( [ $Data[1] ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $ExisteCard = $row["ide"];
            } 
            if ( isset( $ExisteCard ) ) // TARJETA EXISTENTE
            {
                $Return = '01LA TARJETA YA EXISTE EN EL SISTEMA.';
                return $Return;
                exit();
            }      
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'inCardPlatform';
                $postData   = json_encode
                ( 
                    [
                        'inCardPlatform'        => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),
                        'idUser'        => $Data[0],
                        'userCard'      => $Data[5],
                        'binCard'       => $Data[1],
                        'moyeCard'      => $Data[2].$Data[3],
                        'keyCompany'    => $KC
                    ] 
                );          
                $Accion2    = app::apiBonec( $API, $postData );        
                if ( substr( $Accion2, 0, 2) <> '01' )
                {   
                    $IdProduct=app::getIdProduct(substr($Data[1],0,8));                         
                    $Return     = $Accion2;
                    $sql        = $con->prepare( "UPDATE `cards` SET `created_by`=?,`Id_Product`=? WHERE `idcard`=?");
                    $sql->execute( [ 1, $IdProduct, substr($Data[1],-8) ] );
                    $Return='00ALTA DE TARJETA CORRECTA';
                    $con = $sql = NULL;
                }
                else
                {
                    $Return = $Accion2;
                }
            }
            else
            {
                $Return = '01NO SE CREO EL TOKEN REPORTAR AL ADMINISTRADOR.';
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Inserta un registro de carga por layout* */
    public function insertMovementLayout( $Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `layout_uploads` ( `id`,`ide`, `file_upload`, `file_download`, `date_mov`) 
                VALUES (NULL,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2], date('Y-m-d H:i:s')] );
            $con = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Obtien los perfiles para los tarjeta habientes* */
    public function getnamesProfileemployess() 
    {
        try 
        {
            $con= app::conecto();
            $Profile =[];
            $sql=$con->prepare("SELECT Name_profile FROM `profile_employes`");
            $sql->execute([]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                array_push($Profile,$row["Name_profile"]);
            }
            //$query = $this->dbm = NULL;
            return $Profile;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Get Iduser_api, and Key para dar de alta una tarjeta nueva * */
    public function IdAPIByUser( $Ide ) 
    {
        try 
        {
            $con        = app::conecto();
            $IdAPI   = [];
            $sql=$con->prepare("SELECT IdUser_API,idkey FROM `users` WHERE ide= ?");
            $sql->execute([$Ide]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push($IdAPI,$row["IdUser_API"],$row["idkey"]);
            }
            $con = $sql = NULL;
            return $IdAPI;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Realiza un Login en la APi para obtener el IdUser_API para el alta de tarjetas* */
    public function LoginUserAPI( $usuario,$pass ) 
    {
        try 
        {
            $con            = app::conecto();
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'inLogin';
                $postData = json_encode 
                    (
                        [
                            'inLogin'       => TRUE,
                            'clientKey'     => $Key_API,
                            'clientToken'   => substr( $Token1, 2 ),
                            'userCard'      => $usuario,
                            'passCard'      => $pass
                        ]
                    );
                $Accion1    = app::apiBonec( $API, $postData );
                if ( substr( $Accion1, 0, 2) <> '01' )
                {
                    $Return    = substr($Accion1,2);
                    $sql=$con->prepare("UPDATE `users` SET `IdUser_API`=? WHERE `email`=?");
                    $sql->execute([$Return,$usuario]);
                    $con        = $sql = NULL;
                    //$Return = '00SE CREO CORRECTAMENTE EL USUARIO.';
                }
                else
                {
                    $Return     = $Accion1;
                }
            }
            else
            {
                $Return = '01NO SE CREARON LOS TOKENS REPORTAR AL ADMINISTRADOR.';
            }
            return $Return;  
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    } 
    
    /*Obtiene el saldo de la tarjeta* */
    public function check_amountCard( $idcard ) 
    {
        try 
        {
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'inCardBalance';
                $postData   = json_encode( 
                    [
                        'inCardBalance'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ), 
                        'binCard'       => $idcard
                    ]
                );
                $Return     = app::apiBonec( $API, $postData );
                if ( substr( $Return, 0, 2) <> '01' )
                {
                    $Return=$Return;
                }
                else
                {
                    $Return =$Return;// substr( $Return, 2 );
                }  
            }
            else
            {
                $Return = substr( $Token1, 2 );
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*** ver las tarjetas por compañia */
    public function viewCardsByCompany($idCompany ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT A.`idcard`,B.`fullname` FROM `cards` AS A INNER JOIN `users` AS B ON A.user=B.email WHERE A.`company` = ? ");
            $sql->execute( [ $idCompany ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, substr($row["idcard"],-8), $row["fullname"] );
            }
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*** ver All card System */
    public function viewCards() 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT A.`idcard`,B.`fullname` FROM `cards` AS A INNER JOIN `users` AS B ON A.user=B.email");
            $sql->execute();
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, substr($row["idcard"],-8), $row["fullname"] );
            }
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /**Get Id product card*/
    public function getIdProductByCard($Card) 
    {
        try 
        {
            $con= app::conecto();
            $Profile =[];
            $sql=$con->prepare("SELECT B.Product,B.Id,B.AgreementId,B.ProductId FROM `cards` as A INNER JOIN products_platform AS B ON A.Id_Product=B.Id_Product WHERE  substr(A.idcard,-8)=?");
            $sql->execute([$Card]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                array_push($Profile,$row["Id"],$row["Product"],$row["AgreementId"],$row["ProductId"]);
            }
            //$query = $this->dbm = NULL;
            return $Profile;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get cards disabled* */
    public function getCardDisable($tarjeta) // Verifica que el registro no exista con el mismo monto tarjeta.
    {
        try 
        {
            $con = app::conecto();
            $res = 0;
            $fecha1=date('Y-m-d');
            $sql = $con->prepare( "SELECT count(id) as 'num' FROM `log_incidents_pays` WHERE `card`=? and `Status`=1 and substr(`date`,1,10)=?" );
            $sql->execute( [ $tarjeta,$fecha1 ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $res=$row["num"];
            }    
            $con = $sql = NULL;
            return $res;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Valida si existe una transaccion con el mismo monto, y tarjeta en una diferencia de 10 minutos* */
    public function getTransactionsCard( $Company,$tarjeta,$monto,$fecha) // Verifica que el registro no exista con el mismo monto tarjeta.
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `ide`,`up_date`,`idcard` FROM `layout_anchor` WHERE `idecompany`=? AND `idcard`=? AND `fund`=? AND TIMESTAMPDIFF(MINUTE,`up_date`,?) <=10" );
            $sql->execute( [ $Company,$tarjeta,$monto,$fecha ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["up_date"], $row["idcard"]);
            }    
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** Inserta la informacion de una aplicacion de fondeo */
    public function applyFunds( $company, $idcard, $amount, $concepto, $comentario, $user) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date('Y-m-d H:i:s');
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'reversePay';
                $postData   = json_encode( 
                    [
                        'reversePay'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),
                        'binCard'       => $idcard,
                        'Amount'        => $amount,
                        'Description'   => $concepto
                    ]
                );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                    VALUES (NULL,?,?,?,?,?,'')" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user ] );
                $Return     = app::apiBonec( $API, $postData );
                $Res1=substr( $Return, 2 );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                    VALUES (NULL,?,?,?,?,?,?)" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user,$Res1 ] );
                if ( substr( $Return, 0, 2) <> '01' )
                {
                    $respuesta_api=$Return;
                    if($respuesta_api>0)
                    {
                        $sql                    = $con->prepare( "CALL applyPayCard(?,?,?,?,?,?)" );
                        $Return=$sql->execute( [ $idcard, $amount,$company,$now,$user,$comentario ] );
                        if ( $Return )
                        {
                            $Return = '00FONDEO A TARJETA EXITOSO.';
                        }
                        else //cancelacion del fondeo
                        {
                            $API        = 'keyTrans';
                            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
                            $Token2     = app::apiBonec( $API, $postData );
                            $API        = 'applyPay';
                            $postData   = json_encode( 
                                [
                                    'applyPay'      => TRUE,
                                    'clientKey'     => $Key_API,
                                    'clientToken'   => substr( $Token2, 2 ),
                                    'binCard'       => $idcard,
                                    'Amount'        => $amount,
                                    'Description'   => 'CANCELACION|'.$concepto
                                ]
                            );
                            $Return     = app::apiBonec( $API, $postData );
                            $Res1=substr( $Return, 2 );
                            $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                                VALUES (NULL,?,?,?,?,?,?)" );
                            $sql->execute( [ $company, $idcard, -1*$amount, date('Y-m-d H:i:s'),$user,$Res1 ] );
                            $Return = '01NO SE LOGRO EL FONDEO, NOTIFIQUE AL ADMINISTRADOR.';
                        }
                    }
                    else
                    {
                        $Return = '01NO SE LOGRO EL FONDEO, NOTIFIQUE AL ADMINISTRADOR.';
                    }
                }
                else
                {
                    $Return = substr( $Return, 2 );
                }
            }
            else
            {
                $Return = substr( $Token1, 2 );
            }
            $con = $sql = NULL;
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*inserta en el log de incidente de fondeos o reversos* */
    public function insertlogincidentsPays( $data) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date( 'Y-m-d H:i:s' );
            // Registra el movimiento.
            $sql        = $con->prepare( "INSERT INTO `log_incidents_pays`(`Id`,`id_company`, `card`, `date`, `monto`, `mensage`,`User`,`Status`) 
                            VALUES ( NULL, ?, ?, ?, ?, ?,?,? )" );
            $sql->execute( [ $data[0],$data[1],$now, $data[2], $data[3], $data[4],'1'] );
            $lastid  = $con->lastInsertId();
            $con = $sql = NULL;
            return $lastid;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Log de fondeo 1=form,2=layout* */
    public function insertMovementAcount( $Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `log_funds` ( `id`,`ide`, `mount`, `date`, `status`, `IdCard`,`type`) 
                VALUES (NULL,?,?,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1], date('Y-m-d H:i:s'), $Data[2],$Data[3],$Data[4] ] );
            $con = $sql = NULL;
            return TRUE;  
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** Inserta la informacion virtual de una aplicacion de fondeo */
    public function applyFundsVirtual( $company, $idcard, $amount, $concepto, $comentario, $user) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date('Y-m-d H:i:s');
            $Key_API=app::get_KeyAPI();
            $sql                    = $con->prepare( "CALL applyPayCard(?,?,?,?,?,?)" ); //Execute applyPayCard
            $Return=$sql->execute( [ $idcard, $amount,$company,$now,$user,$comentario ] );               
            if ( $Return )
            {
                $Return = '00FONDEO A TARJETA EXITOSO.';
            }
            else //cancelacion del fondeo
            {
                $API        = 'keyTrans';
                $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
                $Token2     = app::apiBonec( $API, $postData );
                $API        = 'applyPay';
                $postData   = json_encode( 
                    [
                        'applyPay'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token2, 2 ),
                        'binCard'       => $idcard,
                        'Amount'        => $amount,
                        'Description'   => 'CANCELACION|'.$concepto
                    ]
                );
                $Return     = app::apiBonec( $API, $postData );
                $Res1=substr( $Return, 2 );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                    VALUES (NULL,?,?,?,?,?,?)" );
                $sql->execute( [ $company, $idcard, -1*$amount, date('Y-m-d H:i:s'),$user,$Res1 ] );
                $Return = '01NO SE LOGRO EL FONDEO, NOTIFIQUE AL ADMINISTRADOR.';
            }
            $con = $sql = NULL;
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*actualiza informacion de tarjeta*/
    public function UpdateCardIncidents($idincident,$Card) 
    {
        try 
        {
            $con        = app::conecto();
            // Actualiza la tarjeta del incidente.
            $sql                    = $con->prepare( "UPDATE `incidents_pays` SET `Status` = 2 WHERE `Id` = ? AND `card` = ?" );
            $Resp                   = $sql->execute( [ $idincident,$Card ] );
            $con = $sql = NULL;
            return $Resp;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** Inserta la informacion de una aplicacion de fondeo */
    public function reverseFunds( $company, $idcard, $amount, $concepto, $comentario, $user) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date('Y-m-d H:i:s');
            $Key_API=app::get_KeyAPI();
            $amountAPI=-1*$amount;
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'applyPay';
                $postData   = json_encode( 
                    [
                        'applyPay'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),
                        'binCard'       => $idcard,
                        'Amount'        => $amountAPI,
                        'Description'   => $concepto
                    ]
                );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                VALUES (NULL,?,?,?,?,?,'')" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user ] );
                $Return     = app::apiBonec( $API, $postData );
                $Res1=substr( $Return, 2 );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                VALUES (NULL,?,?,?,?,?,?)" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user,$Res1 ] ); 
                if ( substr( $Return, 0, 2) <> '01' )
                {
                    $respuesta_api=$Return;
                    if($respuesta_api>0)
                    {
                        $sql                    = $con->prepare( "CALL reversePayCard(?,?,?,?,?,?)" );
                        $Return=$sql->execute( [ $idcard, $amount,$company,$now,$user,$comentario ] );
                        if ( $Return )
                        {
                            $Return = '00REVERSO A TARJETA EXITOSO.';
                        }
                        else
                        {
                            $Return = '01REVERSO EXITOSO, REGISTRO INVALIDO NOTIFICAR AL ADMINISTRADOR.';
                        }
                    }
                    else
                    {
                        $Return = '01NO SE LOGRO EL FONDEO, NOTIFIQUE AL ADMINISTRADOR.';
                    }
                }
                else
                {
                    $Return = substr( $Return, 2 );
                }   
            }
            else
            {
                $Return = substr( $Token1, 2 );
            }
            $con = $sql = NULL;
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

        /** Inserta la informacion virtual de una aplicacion de fondeo */
    public function reverseFundsVirtual( $company, $idcard, $amount, $concepto, $comentario, $user) 
    {
        try 
        {
            $con        = app::conecto();
            $now        = date('Y-m-d H:i:s');
           $sql                    = $con->prepare( "CALL reversePayCard(?,?,?,?,?,?)" );
            $Return=$sql->execute( [ $idcard, $amount,$company,$now,$user,$comentario ] );              
            if ( $Return )
            {
            $Return = '00FONDEO A TARJETA EXITOSO.';
            }
            else
            {
                $Return = '01FONDEO EXITOSO, REGISTRO INVALIDO NOTIFICAR AL ADMINISTRADOR.';
            }
            $con = $sql = NULL;
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get Movement BY Card* */
    public function getMovementsCard( $idcard,$start_date,$end_date ) 
    {
        try 
        {
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'cardTrans';
                $postData   = json_encode( 
                    [
                        'cardTrans'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),
                        'binCard'       => $idcard,
                        'startDate'     => $start_date,
                        'endDate'       => $end_date    
                    ]
                );
                $Return     = app::apiBonec( $API, $postData );
                return $Return;
            }
            else
            {
                $Return = substr( $Token1, 2 );
            }
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Valida que la tarjeta pertenesca a la empresa* */
    public function getCardSystem($tarjeta,$company) // Verifica que exista la tarjeta.
    {
        try 
        {
            $con = app::conecto();
            $id_card = [];
            $sql = $con->prepare( "SELECT `idcard` FROM `cards` WHERE SUBSTRING(`idcard`, -8) =?  AND `company`=?" );
            $sql->execute( [ $tarjeta,$company ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $id_card, $row["idcard"] );
            }    
            $con = $sql = NULL;
            return $id_card;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* Usuarios para el administrador*/
    public function viewUsersByAdmin($company) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT A.`picture` as 'logo_company',B.`ide` as 'ideuser',B.`fullname` as 'namef',B.`phone` as 'telephone',B.`active` as 'status',B.perfil as 'Perfil' FROM `companys` AS A INNER JOIN `users` AS B ON A.ide = B.company AND A.ide=? AND B.perfil<>'EMPLEADO' AND B.perfil<>'ADMIN' AND B.perfil<>'IT'");
            $sql->execute([$company]);            
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["logo_company"], $row["ideuser"], $row["namef"], $row["telephone"], $row["status"],$row["Perfil"] );
            }           
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /* valida correo existente*/
    public function getMailExist( $email ) 
    {
        try 
        {
            $con = app::conecto();
            $data = '';
            $sql = $con->prepare( "SELECT email FROM `users` WHERE email=? OR email2=?" );
            $sql->execute( [ $email,$email ] );         
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $data=$row["email"];
            }
            $con = $sql = NULL;
            return $data;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /**Get Users para las empresaq */
    public function viewUsersByCompany($company) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT A.`picture` as 'logo_company',B.`ide` as 'ideuser',B.`fullname` as 'namef',B.`phone` as 'telephone',B.`active` as 'status',B.`perfil` as 'Perfil' FROM `companys` AS A INNER JOIN `users` AS B ON A.ide = B.company AND A.ide=? AND B.perfil<>'EMPLEADO' AND B.perfil<>'EMPRESA'");
            $sql->execute([$company]);
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["logo_company"], $row["ideuser"], $row["namef"], $row["telephone"], $row["status"],$row["Perfil"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Obtener los fondeos de todas las tarjeta por empresa y fechas* */
    public function layout_anchor( $Company,$star_date,$finish_date ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `ide`, `idecompany`, `idcard`, `fund`, `idtype`, `up_date`,`Comment` FROM `layout_anchor` WHERE `idecompany`=? AND `up_date` BETWEEN ? AND ?" );
            $sql->execute( [ $Company,$star_date,$finish_date ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["idecompany"], $row["idcard"], $row["fund"], $row["idtype"], $row["up_date"],$row["Comment"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Obtener los fondeos por tarjeta, empresa y fechas* */
    public function get_FoundsCardByEmployee( $Company,$idEmployee,$fechai,$fechaf) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `ide`, `idecompany`, `idcard`, `fund`, `idtype`, `up_date`,`Comment` FROM `layout_anchor` WHERE `idecompany`=? AND `idcard`=? AND `up_date` BETWEEN ? AND ?" );
            $sql->execute( [ $Company,$idEmployee,$fechai,$fechaf ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ide"], $row["idecompany"], $row["idcard"], $row["fund"], $row["idtype"], $row["up_date"],$row["Comment"] );
            }    
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /**obtiene el Id de producto en base a sus 8 primeros digitos */
    public function getIdProduct($digitos) 
    {
        try 
        {
            $con= app::conecto();
            $Profile ='';
            $sql=$con->prepare("SELECT Id_Product FROM products_platform WHERE Digitos_tarjeta =?");
            $sql->execute([$digitos]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                $Profile=$row["Id_Product"];
            }
            return $Profile;
            $con = $sql = NULL;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get name User By card*/
    public function getNameCardHolder($card) 
    {
        try 
        {
            $con= app::conecto();
            $Ide ='';
            $sql=$con->prepare("SELECT a.fullname FROM `users` as a INNER JOIN `cards` as b ON a.email=b.user WHere substr(b.idcard,-8)=?");
            $sql->execute([$card]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                $Ide=$row["fullname"];
            }
            $con = $sql = NULL;
            return $Ide;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Obtiene la direccion de guardado de la factura* */
    public function get_FacturabyAuthCode( $AuthCode ) 
    {
        try 
        {
            $con = app::conecto();
            $url = '';
            $sql = $con->prepare( "SELECT Url_factura FROM `facturas_cards` WHERE Auth_Code=?" );
            $sql->execute( [ $AuthCode ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $url= $row['Url_factura'];
            }
            $con = $sql = NULL;
            return $url;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Da de alta una nueva factura* */
    public function new_Factura($Data) 
    {
        try 
        {
            $con = app::conecto();
            $sql = $con->prepare( "INSERT INTO `facturas_cards` ( `Id`,`Card`, `Auth_Code`, `Url_factura`,`Fecha_alta`,`User`) 
                VALUES (NULL,?,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2],date('Y-m-d H:i:s'),$Data[3]] );
            $con = $sql = NULL;
            return TRUE;   
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Elimina el registro de una factura* */
    public function delete_factura( $idfactura ) 
    {
        try
        {
            $con    = app::conecto();
            $sql    = $con->prepare("DELETE FROM `facturas_cards` WHERE  `Auth_Code` = ?" );
            $sql->execute( [ $idfactura ] );
            $con    = $sql = NULL;
            return TRUE;
        }   
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*** ver las tarjetas por compañia*/
    public function viewDataCardsByCompany($idCompany ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT A.`idcard`,B.`fullname`,B.`email`,B.`up_date` FROM `cards` AS A INNER JOIN `users` AS B ON A.user=B.email WHERE A.`company` = ? ");
            $sql->execute( [ $idCompany ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, substr($row["idcard"],-8), $row["fullname"],$row["email"],$row["up_date"] );
            }
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** Obtener los fondeos y reversos por compañia */
    public function GetPaynets( $Company, $dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `Id`,`Card`, `Amount_Charge`,`Amount`,`Date`,`Comision_apply`,`Company`,`Concepto` FROM `paynet_comision` WHERE `Company` = ? AND (`Date` BETWEEN ? AND ?) and Status=2" );
            $sql->execute( [ $Company, $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["Id"], $row["Card"], $row["Amount_Charge"], $row["Amount"], $row["Date"], $row["Comision_apply"],$row["Company"],$row["Concepto"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /** Obtener los fondeos y reversos de todas las empresas */
    public function GetAllPaynets( $dateIn, $dateOut ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `Id`,`Card`, `Amount_Charge`,`Amount`,`Date`,`Comision_apply`,`Company`,`Concepto` FROM `paynet_comision` WHERE (`Date` BETWEEN ? AND ?) and Status=2" );
            $sql->execute( [ $dateIn, $dateOut ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["Id"], $row["Card"], $row["Amount_Charge"], $row["Amount"], $row["Date"], $row["Comision_apply"],$row["Company"],$row["Concepto"] );
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /**Fondeo manual de tarjetas* */
    public function applyFundsManual( $company, $idcard, $amount,$user,$concepto,$motivo ) 
    {
        try 
        {
            $con        = app::conecto();
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            $sql = $con->prepare( "INSERT INTO `Fund_manuals`(`Id`, `Card`, `Monto`, `Motivo`, `Fecha`,`Status`,`Company`,`Accion`) 
            VALUES (NULL,?,?,?,?,?,?,?)" );
            $sql->execute( [ $idcard, $amount,$motivo,date('Y-m-d H:i:s'),'2',$company,$concepto ] );
            $idlog  = $con->lastInsertId();
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'reversePay';
                $postData   = json_encode
                ( 
                    [
                        'reversePay'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),
                        'binCard'       =>  $idcard,
                        'Amount'        => $amount,
                        'Description'   => $concepto
                    ]
                );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                VALUES (NULL,?,?,?,?,?,'')" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user ] );
                $Return     = app::apiBonec( $API, $postData );
                $Res1=substr( $Return, 2 );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                VALUES (NULL,?,?,?,?,?,?)" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user,$Res1 ] );
                if ( substr( $Return, 0, 2) <> '01' )
                {
                    $respuesta_api=$Return;
                    if($respuesta_api>0)
                    {                
                        $sql=$con->prepare("UPDATE `Fund_manuals` SET `Status`='1' WHERE `Id`=?");
                        $sql->execute([$idlog]);    
                        $Return = '00FONDEO CORRECTO DE LA TARJETA.';    
                    }
                    else
                    {
                        $Return = '01NO SE LOGRO EL FONDEO INTENTE NUEVAMENTE.';   
                    }
                }
                else
                {
                    $Return = substr( $Return, 2 );
                }     
            }
            else
            {
                $Return = substr( $Token1, 2 );
            }           
            $con = $sql = NULL;
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
  
    /**Reverso manual de tarjetas* */
    public function applyReverseManual( $company, $idcard, $amount,$user,$concepto,$motivo ) 
    {
        try 
        {
            $con        = app::conecto();
            $Key_API=app::get_KeyAPI();
            $API        = 'keyTrans';
            $postData   = json_encode( [ 'keyTrans' => TRUE, 'clientKey' => $Key_API ] );
            $Token1     = app::apiBonec( $API, $postData );
            $amountAPI=-1*$amount;
            $sql = $con->prepare( "INSERT INTO `Fund_manuals`(`Id`, `Card`, `Monto`, `Motivo`, `Fecha`,`Status`,`Company`,`Accion`) 
            VALUES (NULL,?,?,?,?,?,?,?)" );
            $sql->execute( [ $idcard, $amount,$motivo,date('Y-m-d H:i:s'),'2',$company,$concepto ] );
            $idlog  = $con->lastInsertId();
            if ( substr( $Token1, 0, 2 ) <> '01' )
            {
                $API        = 'applyPay';
                $postData   = json_encode
                ( 
                    [
                        'applyPay'      => TRUE,
                        'clientKey'     => $Key_API,
                        'clientToken'   => substr( $Token1, 2 ),
                        'binCard'       =>  $idcard,
                        'Amount'        => $amountAPI,
                        'Description'   => $concepto
                    ]
                );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                VALUES (NULL,?,?,?,?,?,'')" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user ] );
                $Return     = app::apiBonec( $API, $postData );
                $Res1=substr( $Return, 2 );
                $sql = $con->prepare( "INSERT INTO `analisis_cards`(`ide`, `idecompany`, `idcard`, `fund`, `up_date`,`Usuario`,`Respuesta`) 
                VALUES (NULL,?,?,?,?,?,?)" );
                $sql->execute( [ $company, $idcard, $amount, date('Y-m-d H:i:s'),$user,$Res1 ] );
                if ( substr( $Return, 0, 2) <> '01' )
                {
                    $respuesta_api=$Return;
                    if($respuesta_api>0)
                    {              
                        $sql=$con->prepare("UPDATE `Fund_manuals` SET `Status`='1' WHERE `Id`=?");
                        $sql->execute([$idlog]);    
                        $Return = '00REVERO CORRECTO DE LA TARJETA.';                    
                    }
                    else
                    {
                        $Return = '01NO SE LOGRO EL REVERSO INTENTE NUEVAMENTE.';   
                    }
                }
                else
                {
                    $Return = substr( $Return, 2 );
                }     
            }
            else
            {
                $Return = substr( $Token1, 2 );
            }           
            $con = $sql = NULL;
            return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Alta de usuario con carga masiva solo en la plataforma* */
    public function NewCardHolderLayoutManual( $aPerfil,$usuario,$fileName ) 
    {
        try 
        {
            $con            = app::conecto();
            //$fileName       = $image1;
            $Return         = '01FALTA ARCHIVO DE IMAGEN DEL USUARIO.';
            $sql = $con->prepare( "INSERT INTO `log_users` (`id`, `company`, `date`, `email`,`id_card`,`id_user_created`,`status`,`type_upload`)
                VALUES (NULL,?,?,?,?,?,?,?)" );
            $sql->execute( [$aPerfil[0],date('Y-m-d H:i:s'),$aPerfil[13],$aPerfil[14],$usuario,'PENDIENTE','2' ] );
            $idlog  = $con->lastInsertId();
            if( isset( $fileName ) )
            {
                // Valida el Email.
                $sql = $con->prepare( "SELECT `ide` FROM `users` WHERE `email` = ?" );
                $sql->execute( [ $aPerfil[13] ] );
                $res = $sql->fetchAll();
                foreach ( $res as $row ) 
                {
                    $ExisteEmail = $row["ide"];
                }
                if ( isset( $ExisteEmail ) )
                {
                    $Return = '01LA CUENTA DE CORREO YA FUE REGISTRADA EN EL SISTEMA.';
                    return $Return;
                    exit();
                }
                else
                {
                    $Accion1 = TRUE;
                }
                if ( $Accion1 )
                {   
                    $sql = $con->prepare( "INSERT INTO `users`
                    (`ide`, `email`, `company`, `fullname`, `address`, `city`, `zip`, `aboutme`, `picture`, `idcard`, `perfil`, `up_date`, `active`, `idkey`,`phone`,`state`,`email2`,`perfil_TH`,`IdUser_API`) 
                    VALUES (NULL,?,?,?,?,?,?,?,?,?,?,SUBSTRING(NOW(),1,10),1,?,?,?,?,?,?)" );
                    $Accion1    = $sql->execute( [ $aPerfil[13], $aPerfil[0], $aPerfil[1], $aPerfil[4], $aPerfil[6], $aPerfil[5], 
                    $aPerfil[9], $fileName, 0, $aPerfil[12], $aPerfil[11],$aPerfil[3],$aPerfil[7],$aPerfil[2],$aPerfil[8],''] );                
                    $sql=$con->prepare("UPDATE `log_users` SET `status`='OK' WHERE `id`=?");
                    $sql->execute([$idlog]);
                    $con        = $sql = NULL;
                    $Return='00USUARIO CREADO EXITOSAMENTE';
                }
                else
                {
                    $Return = '01NO ES POSIBLE AGREGAR USUARIO INTENTE MAS TARDE.';
                }
                return $Return;
            }
            else
            {
                return $Return;
            }
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Alta de una nueva tarjeta Solo plataforma* */
    public function NewCardManual( $Data,$DataComision ) 
    {
        try 
        {
            $con            = app::conecto();
            $now            = date( 'Y-m-d' );
            // Valida la tarjeta.
            $sql = $con->prepare( "SELECT `ide` FROM `cards` WHERE `idcard` = ?" ); // verifica si existe la tarjeta en el sistema
            $sql->execute( [ $Data[1] ] ); 
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $ExisteCard = $row["ide"];
            }
            if ( isset( $ExisteCard ) ) // TARJETA EXISTENTE
            {
                $Return = '01LA TARJETA YA EXISTE EN EL SISTEMA.';
                return $Return;
                exit();
            }         
            $IdProduct=app::getIdProduct(substr($Data[1],0,8));                         
            $sql        = $con->prepare( "INSERT INTO `cards`(`ide`, `idcard`, `company`, `city`, `picture`, `up_date`, `active`,`user`,`created_by`,`Id_Product`) 
            VALUES ( NULL, ? ,? ,? ,? ,? , '1',?,'1',?)" );
            $sql->execute( [ substr($Data[1],-8),$Data[4], $Data[6], 'c190319112221.jpg', $now,$Data[5],$IdProduct ] );
            $lastidcard=$con->lastInsertId();
            $fecha=date('Y-m-d H:i:s');
            $sql        = $con->prepare( "INSERT INTO `paynet_cards`(`Id`,`Company`, `IdCard`, `ComisionAdmin`, `IvaComisionAdmin`, `ComisionCompany`,`IvaComisionCompany`,`RegisterDate`,`UpdateDate`) 
            VALUES ( NULL,? ,? , ?, ?, ?, ?, ?, ?)" );
            $sql->execute( [ $Data[4], $lastidcard, $DataComision[0],$DataComision[1],$DataComision[2],$DataComision[3], $fecha,$fecha ] );
            $Return='00ALTA DE TARJETA CORRECTA';
            $con = $sql = NULL;
            return $Return; 
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get company By card*/
    public function getCompanyByCardHolder($card) 
    {
        try 
        {
            $con= app::conecto();
            $Ide ='';
            $sql=$con->prepare("SELECT company FROM `cards` WHere substr(idcard,-8)=?");
            $sql->execute([$card]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                $Ide=$row["company"];
            }
            $con = $sql = NULL;
            return $Ide;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
    
    /*Reverse Fund Company and update Amounts company and Admin* */
    public function UpdateFundsAdmin( $company,$amount ) 
    {
        try 
        {
            $con        = app::conecto();
            // Actualiza los Fondos de la Empresa.
            $sql        = $con->prepare( "UPDATE `companys` SET `fund` = ? WHERE `ide` = ?" );
            $sql->execute( [ $amount, $company ] );
            $con = $sql = NULL;
            $Return='00Actualizacion Correcta';
             return $Return;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Ver la info de la empresa* */
    public function GetComisionCompanyByIde( $Company ) 
    {
        try 
        {
            $con = app::conecto();
            $compania = [];
            $sql = $con->prepare( "SELECT `ComisionPaynet`, `IvaComisionPaynet` FROM `companys`  WHERE `ide` = ?" );
            $sql->execute( [ $Company ] );
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $compania, $row["ComisionPaynet"], $row["IvaComisionPaynet"]);
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Get All product Platform modulo unico gocard (Carnet,Mastercard)*/
    public function getAllProductsPlatform()
    {
        try 
        {
            $con = app::conecto();
            $Products = [];
            $sql = $con->prepare( "SELECT Product,AgreementId,ProductId,Id,label_convenio FROM products_platform" );
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

    /**Get Label product By Id modulo unico gocard*/
    public function getlabelProductById($Id) 
    {
        try 
        {
            $con= app::conecto();
            $Profile ='';
            $sql=$con->prepare("SELECT Product FROM products_platform WHERE  Id_Product=?");
            $sql->execute([$Id]);
            $res = $sql->fetchAll();
            foreach ($res as $row) 
            {
                $Profile=$row["Product"];
            }
              //$query = $this->dbm = NULL;
            return $Profile;
        } 
        catch (Exception $ex) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

     /*Ver las tarjetas de la empresa Modulo unico Gocard* */
    public function GetCardsCompanyByIde( $Company ) 
    {
        try 
       {
            $con = app::conecto();
            $compania = '';
            $sql = $con->prepare( "SELECT `IdCardsUse` FROM `companys`  WHERE `ide` = ?" );
            $sql->execute( [ $Company ] );  
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                $compania= $row["IdCardsUse"];
            }
            $con = $sql = NULL;
            return $compania;
        } 
        catch ( Exception $ex ) 
        {
           print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Saldo de todoas las empresa activas*/
    public function SumAllFundcompany()
    {
        try 
        {
            $con        = app::conecto();
            $sql        = $con->prepare( "SELECT SUM(`fund`) AS 'total' FROM `companys` WHERE perfil<>'ADMIN' AND active=1" );
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



    //Reasigancion de tarjeta
    /*** ver las tarjetas por compañia*/
    public function viewDataByCard($Card ) 
    {
        try 
        {
            $con = app::conecto();
            $usuario = [];
            $sql = $con->prepare( "SELECT A.`ide` AS 'idecard', B.`ide` as 'iduser' FROM `cards` AS A INNER JOIN `users` AS B ON A.user=B.email WHERE substr(A.`idcard`,-8) = ?" );
            
            $sql->execute( [ $Card ] );
            
            $res = $sql->fetchAll();
            foreach ( $res as $row ) 
            {
                array_push( $usuario, $row["idecard"], $row["iduser"]);
            }
            
            $con = $sql = NULL;
            return $usuario;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualiza la empresa de una tarjeta* */
    public function updateCompanyUser( $Name,$Obs,$Company,$IdeUser )
    {
        try 
        {
            $con    = app::conecto();
            $sql    = $con->prepare( "UPDATE `users` SET `fullname` = ?, `aboutme` = ?, `company` = ? WHERE `ide` = ? " );
            $sql->execute( [ $Name, $Obs, $Company, $IdeUser] );
            
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    /*Actualiza la empresa de una tarjeta* */
    public function updateCompanyCard( $Company,$IdCard )
    {
        try 
        {
            $con    = app::conecto();
            $sql    = $con->prepare( "UPDATE `cards` SET `company` = ? WHERE `ide` = ?" );
            $sql->execute( [ $Company,$IdCard ] );
            
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    public function updateCompanyCardPaynet( $Company,$IdCard )
    {
        try 
        {
            $con    = app::conecto();
            $sql    = $con->prepare( "UPDATE `paynet_cards` SET `company` = ? WHERE `IdCard` = ?" );
            $sql->execute( [ $Company,$IdCard ] );
            
            $con    = $sql = NULL;
            return TRUE;
        } 
        catch ( Exception $ex ) 
        {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    public function new_ChargeCard($Data) 
    {
        try 
        {
           $con = app::conecto();
           $sql = $con->prepare( "INSERT INTO `cards_changes` ( `Id`, `Company`, `Card`, `Reason`, `CompanyNew`, `DateR`,`UserR`) 
               VALUES (NULL,?,?,?,?,?,?)" );
            $sql->execute( [ $Data[0], $Data[1],$Data[2],$Data[3],date('Y-m-d H:i:s'),$Data[4]] );
           $con = $sql = NULL;
           return TRUE;        
        } 
        catch ( Exception $ex ) 
        {
           print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }

    //Buscar una tarjeta y usuario/pass
    public function GetInfUser($Dato)
    {
        try {
            $con        = app::conecto();

            $sql = $con->prepare("SELECT a.ide AS 'idepersona', a.fullname, b.company AS 'empresa', 
            a.perfil, a.active, a.email, d.Product, substr(c.idcard, -8) AS 'tarjeta', a.idkey AS 'pass' 
            FROM users AS a 
            INNER JOIN companys AS b ON a.company = b.ide 
            INNER JOIN cards AS c ON c.user = a.email
            INNER JOIN products_platform AS d ON c.Id_Product = d.Id
            WHERE a.fullname LIKE ?
            OR c.idcard LIKE ? 
            AND c.idcard >= '0' ");
            $sql->execute(['%' . $Dato . '%','%' . $Dato. '%']);

            $res = $sql->fetchAll();
            
            $con = $sql = NULL;
            return $res;

        } catch (Exception $ex) {
            print "¡Error!: " . $ex->getMessage() . "<br/>";
            die();
        }
    }
}