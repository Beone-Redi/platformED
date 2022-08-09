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

    if ( isset($_POST["inputNombreCompleto"]))
    {
        $ComisionAdmin=$app->GetComisionCompanyByIde($_SESSION['EMPRESA']);
        $comisionCompany=0.00;
        $ivaComisionCompany=0.00;
        $bandera=0; // el perfil por default es empleado
        $fDirectory = 'assets/img/';
        $img_perfil=$fDirectory.'empleado.png'; // ruta por default de imagen   
        $inputEmpresa           = $_SESSION['EMPRESA']; //0
        $inputNombreCompleto    = $app->validoInput( $_POST['inputNombreCompleto'] ); // 2
        $inputEmail             = $app->validoInput( $_POST['inputEmail'] ); // 3 
        $inputTel               = $app->validoInput( $_POST['inputTel'] ); //4
        $inputDireccion         = $app->validoInput( $_POST['inputDireccion'] );//5
        $inputCodigoPostal      = $app->validoInput( $_POST['inputCodigoPostal'] );
        $inputCiudad            = $app->validoInput( $_POST['inputCiudad'] );
        $inputEstado            = $app->validoInput( $_POST['inputEstado'] );
        $inputPerfil            = $app->validoInput( $_POST['inputPerfil'] );
        $inputObservaciones     = $app->validoInput( $_POST['inputObservaciones'] );
        $inputClave1            = $app->validoInput( $_POST['inputPass1']);
        $inputClave2            = $app->validoInput( $_POST['inputPass2']);
        $update                 = date('Y-m-d H:i:s');    
        $inputCard              = $app->validoInput( $_POST['inputCard'] );
        $inputMes               = $app->validoInput( $_POST['inputMes'] );
        $inputAno               = $app->validoInput( $_POST['inputAno'] );
        
        if($inputClave1===$inputClave2)
        {
            if ( isset( $_FILES[ 'inputImagenPerfil' ][ 'name' ]) && !empty( $_FILES[ 'inputImagenPerfil' ][ 'name' ]) ) // si se carga el archivo
            {
                $fileTmpPath1   = $_FILES['inputImagenPerfil']['tmp_name'];
                $fileName1      = $_FILES['inputImagenPerfil']['name'];
                $uFile      = $fDirectory . basename( $_FILES[ 'inputImagenPerfil' ][ 'name' ] );
                $extension_file1 = strtolower(substr($fileName1, -4));
                if($extension_file1=='.png' OR $extension_file1=='.jpg' OR $extension_file1=='jpeg')
                {
                    if ( move_uploaded_file( $_FILES[ 'inputImagenPerfil' ][ 'tmp_name' ], $uFile ) ) // si se guardo en la ruta la imagen de perfil
                    {
                        $img_perfil = $fDirectory.$fileName1;
                    }
                    else
                    {
                        $Resultado1='01NO SE GUARDO EL ARCHIVO';
                        $bandera=1; 
                    }
                }
                else 
                {
                    $Resultado1='01EL FORMATO DE IMAGEN NO ES VALIDA';
                    $bandera=1;
                }   
            }

            if($bandera==0) // si no ocurrio un error al cargar la imagen de perfil
            {
                $aCompany =
                [
                    utf8_decode($inputEmpresa),          //0
                    utf8_decode($inputNombreCompleto),   //1
                    $inputEmail,            //2
                    $inputTel,              //3
                    utf8_decode($inputDireccion),        //4
                    $inputCodigoPostal,     //5
                    utf8_decode($inputCiudad),           //6
                    utf8_decode($inputEstado),           //7
                    $inputPerfil,           //8
                    utf8_decode($inputObservaciones),    //9
                    $update,                //10
                    $inputClave1,            //11 //
                    'EMPLEADO', //12
                    substr($_POST['inputCard'],-8) //13
                ];
                $dEmpresas = $app->viewCompany($inputEmpresa);
                $KC=$dEmpresas[16];
                
                $Resultado1 = $app->NewCardHolder( $aCompany,$_SESSION['USER'],$img_perfil,$KC );
                if(substr( $Resultado1, 0, 2 ) <> '01') // si se creo correctamente el usuario
                {
                    $IdUserAPI=substr($Resultado1,2);
                    $IdCard=$_POST['inputCard'];
                    $Month=$_POST['inputMes'];
                    $Anio=$_POST['inputAno'];
                    $Company=$_SESSION['EMPRESA'];
                    $Usuario=$inputEmail;
                    $City=utf8_decode($inputCiudad);

                    $dataComision=[$ComisionAdmin[0],$ComisionAdmin[1],0.00,0.00];//Comisienadmin,/IvAcomisionAdmin,
                    $Data=[$IdUserAPI,$IdCard,$Month,$Anio,$Company,$Usuario,$City,$aCompany[1]];//Idcard,mes,anio,compañia,usuario,ciudad
                    $Resultado1='00USUARIO CREADO CORRECTAMENTE';
                    $Resultado2=$app->NewCard( $Data,$dataComision ,$KC); 
                }
            }
        }
        else
        {
            $Resultado1='01LAS CONTRASEÑAS NO COINCIDEN';
        }
        if ( substr( $Resultado1, 0, 2 ) <> '01' )
        {
            $msg    = 'Hecho!';
            $texto  = $Resultado1;
        }
        else
        {
            $msg    = 'Error';
            $texto  = $Resultado1;
        }
        if ( substr( $Resultado2, 0, 2 ) <> '01' )
        {
            $msg2    = 'Hecho!';
            $texto2  = $Resultado2;
        }
        else
        {
            $msg2    = 'Error';
            $texto2  = $Resultado2;
        }   
        if((substr($Resultado1, 0, 2) <> '00') OR (substr($Resultado2, 0, 2) <> '00') )
        {
            $value_nombre=$inputNombreCompleto;
            $value_correo=$inputEmail;
            $value_tel=$inputTel;
            $value_observa=$inputObservaciones;
            $value_pass1=$inputClave1;
            $value_pass2=$inputClave2;
            $value_estado=$inputEstado;
            $value_ciudad=$inputCiudad;
            $value_cp=$inputCodigoPostal;
            $value_card=$inputCard;
            $value_anio=$inputAno;
            $value_mes=$inputMes;
            $value_direccion=$inputDireccion;
        }
    }
    elseif(isset($_POST["layoutcarga"]))
    {
        $empresa=$_SESSION['EMPRESA'];
        $img_perfil='assets/img/empleado.png';
        $data_company=$app->viewCompany($empresa);
        $ComisionAdmin=$app->GetComisionCompanyByIde($_SESSION['EMPRESA']);
        $KC=$data_company[16];
        
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
                                            $inputEmail=substr($datos[0],-8).'@ener.mx';
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
                                                $Respuesta1 = $app->NewCardHolderLayout( $aCompany,$_SESSION['USER'],$img_perfil,$KC );
                                                if(substr( $Respuesta1, 0, 2 ) <> '01') // si se creo correctamente el usuario
                                                {
                                                    $IdUserAPI=substr($Respuesta1,2);
                                                    $IdCard=$datos[0];
                                                    $Month=str_pad($datos[2],2,"0",STR_PAD_LEFT);;
                                                    $Anio=$datos[3];
                                                    $Usuario=$user;
                                                    $City=utf8_decode($inputCiudad);
                                                    $Data=[$IdUserAPI,$IdCard,$Month,$Anio,$Company,$Usuario,$City,$aCompany[1]];//Idcard,mes,anio,compañia,usuario,ciudad
                                                    $dataComision=[$ComisionAdmin[0],$ComisionAdmin[1],0.00,0.00];//Comisienadmin,/IvAcomisionAdmin,
                                                    $Respuesta1='00USUARIO CREADO CORRECTAMENTE';
                                                    $Respuesta2=$app->NewCard( $Data,$dataComision,$KC ); 
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

            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Cardholder registration</h4>
                    </div>
                    <div class="content">
                        <form action="create_user?scr=9" method="POST" enctype="multipart/form-data" onsubmit="return checkSubmitBlock('btsubmit');" />

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputNombreCompleto">Full Name</label>
                                        <input type="text" class="form-control" name="inputNombreCompleto" value="<?php echo $value_nombre; ?>" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="inputEmail">E-mail</label>
                                        <input type="email" class="form-control" name="inputEmail" value="<?php echo $value_correo; ?>" required />
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="inputTel">Telephone</label>
                                        <input type="number" class="form-control" min="2400000000" max="9999999999" name="inputTel" value="<?php echo $value_tel; ?>" maxlength="10" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" />
                                    </div>
                                </div>
                            </div>
                                     
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="inputPass1">Password</label>
                                        <input type="password" class="form-control" minlength="8" maxlength="21" name="inputPass1" id="inputPass1" value="<?php echo $value_pass1; ?>" required >
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="inputPass2">Repeat Password</label>
                                        <input type="password" class="form-control" minlength="8" maxlength="21" name="inputPass2" id="inputPass2" value="<?php echo $value_pass2; ?>" required >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="for-group">
                                        <label for="inputPass2">View Password</label>
                                        <button class="btn btn-primary" type="button" onclick="mostrarContrasena('inputPass1','inputPass2');"><i class="pe-7s-look" style="font-size:18px;"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputDireccion">Address</label>
                                        <input type="text" class="form-control" name="inputDireccion" value="<?php echo $value_direccion; ?>" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row">        
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputEstado">State</label>
                                        <select class="form-control" name="inputEstado" required onChange="getCiudades(this.value)">
                                        <option value="<?php echo $value_estado; ?>"><?php echo $value_estado; ?></option>
                                        <?php for($i=0;$i<sizeof($states);$i++)
                                        {?>
                                            <option value="<?php echo utf8_encode($states[$i]); ?>"><?php echo utf8_encode($states[$i]);?></option>
                                        <?php
                                        }?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCiudad">City</label>
                                        <select class="form-control" name="inputCiudad" id="ciudades" required="">
                                            <option value="<?php echo $value_ciudad; ?>"><?php echo $value_ciudad; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCodigoPostal">Postal code</label>
                                        <input type="number" min="10000" max="99998" class="form-control" name="inputCodigoPostal" value="<?php echo $value_cp; ?>" maxlength="5" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" />
                                    </div>
                                </div>
                                
                            </div>

                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="inputPerfil">Profile</label>
                                        <select class="form-control" name="inputPerfil" required />
                                            <option value="<?php echo $value_empleado; ?>"><?php echo $value_empleado; ?></option>
                                        
                                        <?php for ($i=0;$i<sizeof($perfiles_empleados);$i++)
                                            {
                                            ?>
                                                <option value="<?php echo $perfiles_empleados[$i];?>"><?php echo $perfiles_empleados[$i];?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="inputImagenPerfil">Profile Picture</label>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                        <input class="form-control" name="inputImagenPerfil" type="file"/>
                                    </div>
                                </div>
                            </div>
                           

                            <div class="row">
                                <div class="col-md-6">
                                    <label for="inputCard">Card</label>
                                    <input type="number" minlength="16" maxlength="16" min="4111111111111111" max="5999999999999999" class="form-control" name="inputCard" value="<?php echo $value_card; ?>" maxlength="16" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                </div>
                                <div class="col-md-2">
                                    <label for="inputMes">Month</label>
                                    <select class="form-control" name="inputMes" required />
                                        <option value="<?php echo $value_mes;?>"><?php echo $value_mes;?></option>
                                        <?php for($i=0;$i<sizeof($meses);$i+=2)
                                        {?>
                                        <option value="<?php echo $meses[$i];?>"><?php echo $meses[$i+1];?></option>
                                        <?php
                                    }?>
                                    </select>

                                </div>
                                <div class="col-md-2">
                                    <label for="inputAno">Year</label>
                                    <select class="form-control" name="inputAno" required />
                                        <option value="<?php echo $value_anio;?>"><?php echo $value_anio;?></option>
                                        <?php for($i=0;$i<sizeof($anios);$i++)
                                        {?>
                                        <option value="<?php echo $anios[$i];?>"><?php echo $anios[$i];?></option>
                                        <?php
                                    }?>
                                    </select>

                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="inputObservaciones">Observaciones</label>
                                        <textarea rows="5" class="form-control" name="inputObservaciones" ><?php echo $value_observa;?></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right">Save</button>
                            &nbsp;
                            <a href="clients?scr=9" class="btn btn-default btn-sm" >
                                <i class="pe-7s-back" style="font-size:16px;"></i> Back
                            </a>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
     
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Create Users by Layout (Maximum 10 for load)</h4>
                    </div>
                    <div class="content">     
                        <form action="create_user?src=9" method="POST" id="form2" enctype="multipart/form-data" onsubmit="return checkSubmitBlock('btsubmit3');">
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
}
else
{
    include 'header.php';
?>
    <div class="alert alert-success">
        <span><b> You do not have access to this module </b> </span>
    </div>
<?php
}

include 'footer.php';