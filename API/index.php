<?php

define( 'DEBUG'         , TRUE);
define( 'CONTROLLERS'   , 'Controllers/' );
define( 'CONTROLLER'    , 'Controller' );
define( 'CONFIG'        , 'Config/' );
define( 'EXT'           , '.php');

if ( DEBUG )
{
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

$REQ    = $_SERVER["REQUEST_URI"]; // URL Full
//$HTTPS  = $_SERVER['HTTPS'];

require_once CONFIG . 'PDO' . EXT;
include_once CONFIG . 'Answer'. EXT;
include_once 'email'. EXT;

$answer= new Answer();
$PDO= new PDOSS();
$email=new sendmail();

/**
 * Defined Json Answer
 */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: POST');
header("Content-Type: application/json");

/**
 * JSON file receive in API
 */
$JSON_File = json_decode(file_get_contents('php://input'), true);

/**
 * Validate JSON is NULL
 */
if(empty($JSON_File))
{
    $answer_API=
    [
        'SystemTraceAuditNumber'=>date('ymdHis'),
        'ResponseCode'=>'71',
        'ErrorMesage'=>'REFERENCIA DE SERVICIO INVALIDA 1'
    ];
    
    $answer->response($answer_API,[]);       
}


    /*
    Controller inEmployeePlatform
    $data=
        [
            inEmployeePlatform=>TRUE,
            clientKey=>ASXWZ7AJ3SRFDSK1KISW66,
            KeyCompany=>OBAKQINM7RA4,
            KeyBIONE=>42JY9X0COG1AD61SIE,
            userCard=>demouser,
            passCard=>987654321abc0,
            emailCard=>demouser@myemail.com,
            idUserBIONE=>1234,
            nameUser=>Empleado 1
        ]

    Controller inCardPlatform
    $data=
        [
            inCardPlatform=>TRUE,
            clientKey=>ASXWZ7AJ3SRFDSK1KISW66,
            KeyCompany=>OBAKQINM7RA4,
            KeyBIONE=>42JY9X0COG1AD61SIE,
            userCard=>demouser,
            binCard=>1234567890123456
        ]

     Controller SaveCardPlatform
    $data=
        [
            SaveCardPlatform=>TRUE,
            clientKey=>ASXWZ7AJ3SRFDSK1KISW66,
            KeyCompany=>OBAKQINM7RA4,
            KeyBIONE=>42JY9X0COG1AD61SIE,
            idCardTemp=>200611013602,
            Status=>1
        ]
    */

    /*Controller Chargesnotification
    $data=
    [
        Chargesnotification=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        Card=>'12345678',2
        Amount=>10,3
        AuthCode=>123456,4
        TypeMov=>1,5,"1" cargo a tarjeta(reverso a tarjerta),"2" abono a tarjeta (aplicacion a tarjeta)
        Concept=>'Cargo a tarjeta',6
    ]
    Controller Paymentnotification
    $data=
    [
        Paymentnotification=>TRUE,
        Clientkey=>'ASXWZ7AJ3SRFDSK1KISW66',
        Card=>42424242,
        Amount=>120.00
    ]

     Controller BalancePlatform
    $data=
    [
        BalancePlatform=>TRUE,0
        clientKey=>ASXWZ7AJ3SRFDSK1KISW66,1
        TypeProduct=>1
    ]

    */


/** 
 * Process array for get name Function to process
 */
$JSON_name_key=array_keys($JSON_File);
$values_array=array_values($JSON_File);
    
    if($values_array[1]=='ASXWZ7AJ3SRFDSK1KISW67') //llave
    {
        if ( file_exists( CONTROLLERS . $JSON_name_key[0] . CONTROLLER . EXT ) ) // validate exist FileController 
        {
            include_once CONTROLLERS . $JSON_name_key[0] . CONTROLLER . EXT;
            $class1=$JSON_name_key[0].CONTROLLER;// Name Class Controller
            if (class_exists($class1)) // Validate Exist Class
            {
                $classexecute = new $class1;
                $respuesta=$classexecute->GenericMethod( $values_array );
                $answer->response($respuesta,$JSON_File);
            }
            else
            {
                $answer_API=
                [
                    'SystemTraceAuditNumber'=>date('ymdHis'),
                    'ResponseCode'=>"63",
                    'ErrorMesage'=>"CLASS DO NOT EXIST"
                ];
                    $answer->response($answer_API,$JSON_File);
            }
        }
        else
        {
            $answer_API=
            [
                'SystemTraceAuditNumber'=>date('ymdHis'),
                'ResponseCode'=>"63",
                'ErrorMesage'=>"CONTROLLER DO NOT EXIST"
            ];
            $answer->response($answer_API,$JSON_File);
        }
    }
    else
    {
        $answer_API=
        [
            'SystemTraceAuditNumber'=>date('ymdHis'),
            'ResponseCode'=>'64',
            'ErrorMesage'=>'CLIENTKEY NO ES DE ENERGEX DOLARES'
        ];
        $answer->response($answer_API,$JSON_File);
  
    }

/*} 
else 
{
    $answer_API['SystemTraceAuditNumber']=date('ymdHis');
    $answer_API['ResponseCode']="72";
    $answer_API['ErrorMesage']="CONEXION NO SEGURA";
    $answer->response($answer_API,$answer_API['ResponseCode'],$JSON_File);       

}*/