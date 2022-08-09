<?php
// Inicio la variables de sesion.

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
$msg    = $msg2 ='';
$texto  = $texto2 = '';
$msg_layout=FALSE;
$meses=['01',1,'02',2,'03',3,'04',4,'05',5,'06',6,'07',7,'08',8,'09',9,'10',10,'11',11,'12',12];
$anio_actual=date('y');
$states=$app->GetAllState();
$Resultado2='01NO SE LOGRO DAR DE ALTA LA TARJETA';
$Resultado1='01NO SE LOGRO DAR DE ALTA EL USUARIO';
$anios=[$anio_actual,($anio_actual+1),($anio_actual+2),($anio_actual+3),($anio_actual+4),($anio_actual+5)];
$value_nombre=$value_pass1=$value_pass2=$value_correo=$value_tel=$value_observa=$value_direccion='';
$value_estado=$value_ciudad=$value_card=$value_mes=$value_anio=$value_cp=$value_empleado='';

if ( $app->optSearch($_SESSION[ 'PERMISOS' ],'opt26') ) // si tiene el permiso puede verlo si no no
{

    if(isset($_POST["layoutcarga"]))
    {
        $ComisionAdmin=$app->GetComisionCompanyByIde($_SESSION['EMPRESA']);
        
        $empresa=$_SESSION['EMPRESA'];
        $img_perfil='assets/img/empleado.png';
        $data_company=$app->viewCompany($empresa);
    
        $inputEmpresa           = $app->validoInput( $empresa );
        $inputTel               = $app->validoInput( $data_company[4] );
        $inputDireccion         = $data_company[5];
        $inputCodigoPostal      = $data_company[7];
        $inputCiudad            = $data_company[6] ;
        $inputEstado            = $data_company[14];
        $inputPerfil            ='EMPLEADO';
        $inputObservaciones     = $app->validoInput( 'EMPLEADO DE '.$data_company[1] );
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
                    if($fila>1 and $fila<12)
                    {
                        if ( ( $fichero = fopen( $fichero_subido, 'r' ) ) !== FALSE ) 
                        {
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
                                            $Respuesta1=$Respuesta2='01TAMAÑO DEL NUMERO DE TARJETA NO VALIDO';
                                        }
                                        elseif(floatval($datos[0])<=4111111111111111)
                                        {
                                            $Respuesta1=$Respuesta2='01NUMERO DE TARJETA NO VALIDO';               
                                        }
                                        elseif(floatval($datos[0])>=5999999999999999)
                                        {
                                            $Respuesta1=$Respuesta2='01NUMERO DE TARJETA NO VALIDO';
                                        }
                                        elseif(strlen($datos[1])<3)
                                        {
                                            $Respuesta1=$Respuesta2='01NOMBRE NO VALIDO';
                                        }
                                        elseif(strlen($datos[2])<=0)
                                        {
                                            $Respuesta1=$Respuesta2='01TAMAÑO DE LA CADENA MES DE VENCIMIENTO NO VALIDO';
                                        }
                                        elseif(strlen($datos[2])>2)
                                        {
                                            $Respuesta1=$Respuesta2='01TAMAÑO DE LA CADENA MES DE VENCIMIENTO NO VALIDO';                                            
                                        }
                                        elseif($datos[2]<=0)
                                        {
                                            $Respuesta1=$Respuesta2='01MES NO VALIDO';
                                        }
                                        elseif($datos[2]>12)
                                        {
                                            $Respuesta1=$Respuesta2='01MES NO VALIDO';
                                        }
                                        elseif(strlen($datos[3])<=0)
                                        {
                                            $Respuesta1=$Respuesta2='01TAMAÑO DE LA CADENA AÑO DE VENCIMIENTO NO VALIDO';
                                        }
                                        elseif(strlen($datos[3])>2)
                                        {
                                            $Respuesta1=$Respuesta2='01TAMAÑO DE LA CADENA AÑO DE VENCIMIENTO NO VALIDO';                                            
                                        }
                                        elseif($datos[3]<date('y'))
                                        {
                                            $Respuesta1=$Respuesta2='01AÑO NO VALIDO';
                                        }
                                        elseif($datos[3]>$anios[5])
                                        {
                                            $Respuesta1=$Respuesta2='01AÑO NO VALIDO';
                                        }
                                        else
                                        {
                                            $band_correo=0;
                                            $password=random_int(11111111, 99999999);
                                            $inputEmail=substr($datos[0],-8).'@hotpay.mx';
                                            $user=substr($datos[0],-8);
                                            if(strlen($datos[4])!=0)
                                            {
                                                if(!filter_var($datos[4], FILTER_VALIDATE_EMAIL)) 
                                                {
                                                    $band_correo=1;
                                                    $Respuesta1=$Respuesta2='01FORMATO DE CORREO NO VALIDO';
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
                                                    utf8_decode($datos[1]),   //1 // variable
                                                    $inputEmail,            //2 Variable
                                                    $inputTel,              //3
                                                    $inputDireccion,        //4
                                                    $inputCodigoPostal,     //5
                                                    $inputCiudad,           //6
                                                    $inputEstado,           //7
                                                    $inputPerfil,           //8
                                                    utf8_decode($inputObservaciones),    //9
                                                    $update,                //10
                                                    $password,            //11 // variable
                                                    'EMPLEADO', //12
                                                    $user,//13
                                                    substr($datos[0],-8)//14
                                                ];
                                                $Respuesta2='01LA TARJETA NO PUDO SER DADA DE ALTA';
                                                $Respuesta1 = $app->NewCardHolderLayoutManual( $aCompany,$_SESSION['USER'],$img_perfil );
                                                if(substr( $Respuesta1, 0, 2 ) <> '01') // si se creo correctamente el usuario
                                                {
                                                    $IdUserAPI=substr($Respuesta1,2);
                                                    $IdCard=$datos[0];
                                                    $Month=str_pad($datos[2],2,"0",STR_PAD_LEFT);;
                                                    $Anio=$datos[3];
                                                    $Usuario=$user;
                                                    $City=utf8_decode($inputCiudad);
                                                    $Data=[$IdUserAPI,$IdCard,$Month,$Anio,$Company,$Usuario,$City,$aCompany[1]];//Idcard,mes,anio,compañia,usuario,ciudad
                                                    $Respuesta1='00USUARIO CREADO CORRECTAMENTE';
                                                    $dataComision=[$ComisionAdmin[0],$ComisionAdmin[1],0.00,0.00];//Comisienadmin,/IvAcomisionAdmin,
                                                    
                                                    $Respuesta2=$app->NewCardManual( $Data,$dataComision ); 
                                                }                                    
                                                $correo_csv=$inputEmail;
                                                $password_csv=$password;
                                            }
                                        }
                                      
                                        if ( substr( $Respuesta1, 0, 2 ) == '00' OR substr( $Respuesta2, 0, 2 ) == '00' )
                                        {
                                            $csv.=$datos[0].$csv_sep.$datos[1].$csv_sep.$inputMes.$csv_sep.$inputAno.$csv_sep.$correo_csv.$csv_sep.$password_csv.$csv_sep.$user.$csv_sep.substr($Respuesta1,2).$csv_sep.substr($Respuesta2,2).$csv_end;
                                            $num_registros++;
                                        }
                                        else
                                        {
                                            $csv.=$datos[0].$csv_sep.$datos[1].$csv_sep.$datos[2].$csv_sep.$datos[3].$csv_sep.$datos[4].$csv_sep.$datos[5].$csv_sep.$datos[6].$csv_sep.substr($Respuesta1,2).$csv_sep.substr($Respuesta2,2).$csv_end;                                    
                                            $error_registros++;
                                        }
                                    }
                                    else
                                    {
                                        $msg="Error";
                                        $Resultado1='01Error en el numero de columnas';
                                        $csv.='0,0,0,0,0,0,0,0,ERROR EN EL NUMERO DE CAMPOS'.$csv_end;
                                    }  
                                }
                                $NumFilas++;    
                            }   
                        }
                        else
                        {
                            $msg="Error";
                            $Resultado1='01Error en la lectura del archivo';
                        }
                    }
                    else    
                    {
                        $msg="Error";
                        $Resultado1='01El archivo excede numero de columnas';
                    }
                }
                else
                {
                    $msg="Error";
                    $Resultado1='01Formato de archivo no valido';
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
                    $Resultado1  = '00Se crearon ' . $num_registros . ' usuarios';
                }
                else
                {
                    $msg    = 'Error';
                    $Resultado1  = '01No se creo ningun usuario.';
                }
                $Data_layout=[$_SESSION["EMPRESA"],$fichero_subido,$FileName];
                $app->insertMovementLayout($Data_layout);
                $msg_layout = 
                    '<div class="alert alert-success">
                        <span><b> Atenci&oacute;n:</b><br>
                            <a class="btn btn-success btn-fill" href="' . $FileName . '" >Reporte de Carga
                            </a><br>
                        El archivo se subio de forma exitosa al servidor.
                        </span>
                </div>';
            }
        }   
    }
include 'header.php';
$perfiles_empleados=$app->getnamesProfileemployess();

?>
<div class="content">
    <div class="container-fluid">
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Exitoso - </b> <?php echo substr( $Resultado1, 2 ); ?></span>
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
                    <span><b> Exitoso - </b> <?php echo substr( $Resultado2, 2 ); ?></span>
                </div>
            <?php } elseif ( strlen( $msg2 ) == 5 ) { ?>
                <div class="alert alert-danger">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> <?php echo substr( $Resultado2, 2 ); ?></span>
                </div>
            <?php } ?>

            <?php if (isset( $msg_layout ) ) {
                echo $msg_layout;} ?> 

     
     
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Crear Usuarios por Layout (Máximo diez por carga)</h4>
                    </div>
                    <div class="content">     
                        <form action="create_user_manual?scr=9" method="POST" id="form2" enctype="multipart/form-data" onsubmit="return checkSubmitBlock('btsubmit3');">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="labelinputLayout">Archivo de tarjetas (.csv)</label>
                                        <input type="hidden" name="layoutcarga">
                                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                        <input class="form-control" name="inputLayout" type="file" required />
                                    </div>
                                </div>
                            </div>
                            <button type="submit" id="btsubmit3" class="btn btn-info btn-fill pull-right" form="form2">Cargar</button>
                            <a class="btn btn-default btn-fill pull-left" href="./downloads/layout_users.csv">Descargar Layout</a>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            
        </div>

        
    </div>
</div>
<?php
}
else
{
    include 'header.php';
?>
    <div class="alert alert-success">
        <span><b> No tienes acceso a este modulo </b> </span>
    </div>
<?php
}

include 'footer.php';