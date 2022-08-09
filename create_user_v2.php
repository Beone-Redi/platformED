<?php
// Inicio la variables de sesion.

/**
 * Generar codigos postales.
 * https://api-codigos-postales.herokuapp.com/v2/codigo_postal/66436
 * $data = json_decode(file_get_contents('https://api-codigos-postales.herokuapp.com/v2/codigo_postal/66436'), true);
 * 
 * <input type="text" id="fname" onfocusout="myFunction()">
 * <script>
 *  function myFunction() {
 *   var x = document.getElementById("fname");
 *   x.value = x.value.toUpperCase();
 *  }
 * </script>
 */
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" ) 
{
    header( 'Location: login' );
}

require_once( 'include/coredata.php' ); 
$app    = new app();
$msg    = $msg2  = '';
$texto  = $texto = '';
$msg_layout=FALSE;
$meses=['01',1,'02',2,'03',3,'04',4,'05',5,'06',6,'07',7,'08',8,'09',9,'10',10,'11',11,'12',12];
$anio_actual=date('y');
$states=$app->GetAllState();
$Resultado2='01NO SE LOGRO DAR DE ALTA LA TARJETA';
$Resultado1='01NO SE LOGRO DAR DE ALTA EL USUARIO';
$anios=[$anio_actual,($anio_actual+1),($anio_actual+2),($anio_actual+3),($anio_actual+4),($anio_actual+5)];
if ( $_SESSION['PERFIL'] === 'ADMIN' )
{
    $Empresas = $app->viewCompanys();
}
else 
{
    $Empresas = $app->viewECompanys( $_SESSION['EMPRESA'] );
}

if(isset($_POST["layoutcarga"]))
{
    $inputLogo='assets/img/empleado.png';
    $data_company=$app->viewDataAllCompany($Empresas[0]);
    $inputEmpresa           = $app->validoInput( $Empresas[0] );
    $inputTel               = $app->validoInput( $data_company[4] );
    $inputDireccion         = $app->validoInput( $data_company[5] );
    $inputCodigoPostal      = $app->validoInput( $data_company[7] );
    $inputCiudad            = $app->validoInput( $data_company[6] );
    $inputEstado            = utf8_encode($data_company[14]);//$app->validoInput( $data_company[14] );
    $inputPerfil            = $app->validoInput('EMPLOYEE');
    $inputObservaciones     = $app->validoInput( 'EMPLOYEE OF '.$data_company[1] );
    $update                 = date('Y-m-d H:i:s');
    $email_company = $data_company[3];
    $arreglo_email=explode("@",$data_company[3]);

    $dir_subida = './uploads/';
    $fichero_subido = $dir_subida.'CU'.date('dmyhis').'.csv';
    
    $csv_end = "\r\n"; // FIN DEL CSV
    $csv_sep = ",";// SEPARADOR CSV
    $csv = ""; // INICIO ARCHIVO CSV
    $csv .= 'Tarjeta,Nombre,MV,AV,correo,Contraseña,Usuario,Respuesta 1,Respuesta 2'. $csv_end; // formato de respuesta
    $FileName = "uploads/RCU".date('dmyhis').".csv"; // CSV de respuesta
    $NumFilas=0;
    $num_registros=0;
    $error_registros=0;
    $Company=$_SESSION['EMPRESA'];
                                                    
    if ($_FILES['inputLayout']['name']) // checa si fue declarado
    {
        if ($_FILES['inputLayout']["error"] > 0) 
        {
            $msg="Error";
            $Resultado1='00Error Generico';
        }
        else
        {
            $extension = strtolower(substr($_FILES['inputLayout']['name'], -4));
            if ($extension == ".csv") 
            {     
                $msg = ( move_uploaded_file( $_FILES['inputLayout']['tmp_name'], $fichero_subido ) )? TRUE: FALSE;
                $fila=0;
                if (($gestor = fopen($fichero_subido, "r")) !== FALSE) 
                {  
                    while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) 
                    {
                        $fila++; // numero de filas del archivo csv
                    }
                }
                if($fila>1 and $fila<16)
                {
                    if ( ( $fichero = fopen( $fichero_subido, 'r' ) ) !== FALSE ) 
                    {
                        //$Respuesta1='01ERROR DE APERTURA';
                        while ( ( $datos = fgetcsv( $fichero, 1000 ) ) !== FALSE ) 
                        {
                            if ($NumFilas>0) 
                            {
                                if(sizeof($datos)==7)
                                {
                                    $user='';
                                    $correo_csv='';
                                    $password_csv='';
                                    $inputAno=$datos[3];
                                    $inputMes=$datos[2];
                                    if(strlen($datos[0])!=16)
                                    {
                                        $Respuesta1=$Respuesta2='01INVALID CARD NUMBER SIZE';
                                    }
                                    elseif(floatval($datos[0])<=4111111111111111)
                                    {
                                        $Respuesta1=$Respuesta2='01INVALID CARD NUMBER';               
                                    }
                                    elseif(floatval($datos[0])>=5999999999999999)
                                    {
                                        $Respuesta1=$Respuesta2='01INVALID CARD NUMBER';
                                    }
                                    elseif(strlen($datos[1])<3)
                                    {
                                        $Respuesta1=$Respuesta2='01INVALID NAME';
                                    }
                                    elseif(strlen($datos[2])<=0)
                                    {
                                        $Respuesta1=$Respuesta2='01CHAIN ​​SIZE MONTH OF EXPIRATION NOT VALID';
                                    }
                                    elseif(strlen($datos[2])>2)
                                    {
                                        $Respuesta1=$Respuesta2='01CHAIN ​​SIZE MONTH OF EXPIRATION NOT VALID';                                            
                                    }
                                    elseif($datos[2]<=0)
                                    {
                                        $Respuesta1=$Respuesta2='01MONTH NOT VALID';
                                    }
                                    elseif($datos[2]>12)
                                    {
                                        $Respuesta1=$Respuesta2='01MONTH NOT VALID';
                                    }
                                    elseif(strlen($datos[3])<=0)
                                    {
                                        $Respuesta1=$Respuesta2='01CHAIN ​​SIZE YEAR OF EXPIRATION NOT VALID';
                                    }
                                    elseif(strlen($datos[3])>2)
                                    {
                                        $Respuesta1=$Respuesta2='01CHAIN ​​SIZE YEAR OF EXPIRATION NOT VALID';                                            
                                    }
                                    elseif($datos[3]<date('y'))
                                    {
                                        $Respuesta1=$Respuesta2='01YEAR NOT VALID';
                                    }
                                    elseif($datos[3]>$anios[5])
                                    {
                                        $Respuesta1=$Respuesta2='01YEAR NOT VALID';
                                    }
                                    else
                                    {
                                        $band_correo=0;
                                        $password=random_int(11111111, 99999999);
                                        $inputEmail=substr($datos[0],-8).'@energex.mx';
                                        $user=substr($datos[0],-8);
                                        if(strlen($datos[4])!=0)
                                        {
                                            if(!filter_var($datos[4], FILTER_VALIDATE_EMAIL)) 
                                            {
                                                $band_correo=1;
                                                $Respuesta1=$Respuesta2='01INVALID EMAIL FORMAT';
                                            }
                                            else
                                            {
                                                $inputEmail=$datos[4];
                                            }
                                        }

                                        if(strlen($datos[5])>0)
                                        {
                                            $password=$app->validoInput($datos[5]);
                                        }
                                        if(strlen($datos[6])>0)
                                        {
                                            $user=$app->validoInput($datos[6]);
                                        }

                                        if($band_correo==0)
                                        {
                                            $aCompany =
                                            [
                                                utf8_decode($inputEmpresa),          //0
                                                utf8_decode($datos[1]),   //1
                                                $inputEmail,            //2 Variable
                                                $inputTel,              //3
                                                utf8_decode($inputDireccion),        //4
                                                $inputCodigoPostal,     //5
                                                utf8_decode($inputCiudad),           //6
                                                utf8_decode($inputEstado),           //7
                                                $inputPerfil,           //8
                                                utf8_decode($inputObservaciones),    //9
                                                $update,                //10
                                                $password,              //11
                                                'EMPLOYEE',             //12
                                                $user                   //13    
                                            ];

                                            $Respuesta2='01THE CARD COULD NOT BE REGISTERED';
                                            $Respuesta1 = $app->NewCardHolderLayoutV2( $aCompany, $_SESSION['USER'], $inputLogo, substr( $datos[0], -8) );

                                            if(substr( $Respuesta1, 0, 2 ) <> '01') // si se creo correctamente el usuario
                                            {
                                                $IdUserAPI=$Respuesta1;
                                                $IdCard=$datos[0];
                                                $Month=str_pad($datos[2],2,"0",STR_PAD_LEFT);;
                                                $Anio=$datos[3];
                                                $Usuario=$user;
                                                $City=utf8_decode($inputCiudad);
                                                $Data=[$IdUserAPI,$IdCard,$Month,$Anio,$Company,$Usuario,$City];//Idcard,mes,anio,compañia,usuario,ciudad
                                                $Respuesta1='00USER CREATED CORRECTLY';
                                                $Respuesta2=$app->NewCardLayout( $Data ); 
                                            }

                                            $data=[$inputEmpresa,substr($datos[0],-8),$user,$password,$inputEmail];
                                            $app->insertlogUserLayout($data);
                                            //$Respuesta='00Simulacion correcta';
                                            $correo_csv=$inputEmail;
                                            $password_csv=$password;
                                        }
                                    }
                                      
                                    if ( substr( $Respuesta1, 0, 2 ) == '00' OR substr( $Respuesta2, 0, 2 ) == '00')
                                    {
                                        $csv.=$datos[0].$csv_sep.$datos[1].$csv_sep.$inputMes.$csv_sep.$inputAno.$csv_sep.$correo_csv.$csv_sep.$password_csv.$csv_sep.$user.$csv_sep.substr($Respuesta1,2).$csv_sep.substr($Respuesta2,2).$csv_sep;
                                        $num_registros++;
                                    }
                                    else
                                    {
                                        $csv.=$datos[0].$csv_sep.$datos[1].$csv_sep.$datos[2].$csv_sep.$datos[3].$csv_sep.$datos[4].$csv_sep.$datos[5].$csv_sep.$datos[6].$csv_sep.substr($Respuesta1,2).$csv_sep.substr($Respuesta2,2).$csv_sep;                                    
                                        $error_registros++;
                                    }
                                }
                                else
                                {
                                    $msg="Error";
                                    $Resultado1='01Error in number of columns';
                                    $csv.='0,0,0,0,0,0,0,0,ERROR IN THE NUMBER OF FIELDS'.$csv_end;
                                }  
                            }
                            $NumFilas++;
                        }
                    }
                    else
                    {
                        $msg="Error";
                        $Resultado1='01Error reading the file';
                    }
                }
                else
                {
                    $msg="Error";
                    $Resultado1='01File exceeds number of columns';
                }
            }
            else
            {
                $msg="Error";
                $Resultado1='01Invalid file format';
            }
            if (!$handle = fopen($FileName, "w")) {
                echo "Cannot open file.";
                exit;
            }
        
            if (fwrite($handle, utf8_decode($csv)) === FALSE) {
                echo "Cannot write to file.";
                exit;
            }
            fclose($handle);

            if ( $num_registros > 0 )
            {
                $msg    = 'Hecho!';
                $Resultado1  = '00Were created ' . $num_registros . ' users';
            }
            //var_dump($texto);
            else
            {
                $msg    = 'Error';
                $Resultado1  = '01No user was created.';
            }
            $Data_layout=[$_SESSION["EMPRESA"],$fichero_subido,$FileName];
            $app->insertMovementLayout($Data_layout);
 
            $msg_layout = 
                '<div class="alert alert-success">
                    <span><b> Atenci&oacute;n:</b><br>
                        <a class="btn btn-success btn-fill" href="' . $FileName . '" >Load report
                        </a><br>
                        The file was successfully uploaded to the server.
                    </span>
            </div>';
        }
    }
}
$perfiles_empleados=$app->getnamesProfile_employess();
include 'header.php';
?>
<div class="content">
    <div class="container-fluid">
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Successful - </b> <?php echo substr( $Resultado1, 2 ); ?></span>
                </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                <div class="alert alert-danger">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> <?php echo substr( $Resultado1, 2 ); ?></span>
                </div>
            <?php } ?>
            <?php if ( strlen( $msg2 ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Successful - </b> <?php echo substr( $Resultado2, 2 ); ?></span>
                </div>
            <?php } elseif ( strlen( $msg2 ) == 5 ) { ?>
                    <div class="alert alert-danger">
                        <button type="button" aria-hidden="true" class="close">×</button>
                        <span><b> Error - </b> <?php echo substr( $Resultado2, 2 ); ?></span>
                    </div>
            <?php } ?>

            <?php if (isset( $msg_layout ) ) {
                echo $msg_layout;} ?>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Create Users by Layout (Maximum 10 per upload)</h4>
                    </div>
                    <div class="content">     
                        <form action="create_user_v2?src=9" method="POST" id="form2" enctype="multipart/form-data" onsubmit="return checkSubmitBlock('btsubmit3');">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="labelinputLayout">Card file (.csv)</label>
                                        <input type="hidden" name="layoutcarga">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                        <input class="form-control" name="inputLayout" type="file" required />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btsubmit3" class="btn btn-info btn-fill pull-right" form="form2">Load</button>
                            <a class="btn btn-default btn-fill pull-left" href="./downloads/layout_users.csv">Download Layout</a>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
