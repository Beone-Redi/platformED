<?php
// Inicio la variables de sesion.

if (!isset($_SESSION)) 
{
    session_start();
}

if (isset($_SESSION['ACTIVO']) <> "2019") 
{
    header("Location: login");
}

include "include/coredata.php";
$app = new app();
$msg = '';
$states=$app->GetAllState();
$tarjetas=$app->getAllProductsPlatform();

$value_empresa=$value_rs=$value_curp=$value_rfc=$value_nombre=$value_correo=$value_tel=$value_direccion=$value_cp='';
$value_estado=$value_ciudad=$value_observa=$value_pass1=$value_pass2=$value_comision=$value_correo_notificaciones='';
$value_comision=0;

if ( isset($_POST['inputEmpresa']) && isset($_POST['inputEmpresa']))
{
    $stringcards='';
    $var=$_POST['inputCards'];
    for($i=0;$i<sizeof($var);$i++)
    {   
        $stringcards.=$var[$i].'-';
    }
    
    $stringcards=substr($stringcards, 0, -1);
 
    $bnadera=0;   
    $inputEmpresa           = $app->validoInput( $_POST['inputEmpresa'] );
    $inputRazonSocial       = $app->validoInput( $_POST['inputRazonSocial'] );
    $inputNombreCompleto    = $app->validoInput( $_POST['inputNombreCompleto'] );
    $inputEmail             = $app->validoInput( $_POST['inputEmail'] );
    $inputTel               = $app->validoInput( $_POST['inputTelefono'] );
    $inputDireccion         = $app->validoInput( $_POST['inputDireccion'] );
    $inputCodigoPostal      = $app->validoInput( $_POST['inputCodigoPostal'] );
    $inputCiudad            = $app->validoInput( $_POST['inputCiudad'] );
    $inputEstado            = $app->validoInput($_POST["inputEstado"]);
    $inputPerfil            = 'EMPRESA';
    $inputObservaciones     = $app->validoInput( $_POST['inputObservaciones'] );
    $inputPass1             = $app->validoInput( $_POST['inputPass1'] );
    $inputPass2             = $app->validoInput( $_POST['inputPass2'] );
    $comision=$_POST["comision"];
    $comisionPaynet=2.5;
    $ivaComisionPaynet=16;
    
    $update                 = date( 'Y-m-d H:i:s' );
    $RFC=$CURP=$dest_INE=$inputEmailNotifications='';
    $bandera=0;
    if(isset($_POST['inputCurp']))
    {
        $CURP=$_POST['inputCurp'];
    }
    if(isset($_POST['inputRfc']))
    {
        $RFC=$_POST['inputRfc'];
    }

    if(isset($_POST['inputEmailNotifications']))
    {
        $inputEmailNotifications=$_POST['inputEmailNotifications'];
    }
    $aCompany =
    [
        utf8_decode($inputEmpresa),          // 0
        utf8_decode($inputRazonSocial),      // 1
        utf8_decode($inputNombreCompleto),   // 2
        $inputEmail,            // 3
        $inputTel,              // 4
        utf8_decode($inputDireccion),        // 5
        $inputCodigoPostal,     // 6
        utf8_decode($inputCiudad),           // 7
        utf8_decode($inputEstado),           // 8
        $inputPerfil,           // 9
        utf8_decode($inputObservaciones),    // 10
        $inputPass1,            // 11
        $update,                 // 12
        $RFC,                      //13
        $CURP,                  // 14
        $dest_INE,                //15
        $comision,               //16     
        $inputEmailNotifications,  //17
        $comisionPaynet,  //18
        $ivaComisionPaynet, //19
        $stringcards //20
    ];

    
    if($app->getMailExist($inputEmail)=='')
    {
        if ( $inputPass1 === $inputPass2)
        {
            $fileTmpPath1   = $_FILES['inputLogo']['tmp_name'];
            $fileName1      = $_FILES['inputLogo']['name'];
            $fileTmpPath2   = $_FILES['inputUserPicture']['tmp_name'];
            $fileName2      = $_FILES['inputUserPicture']['name'];
            $uploadFileDir  = './assets/img/';
            $dest_path1     = $uploadFileDir . $fileName1;
            $dest_path2     = $uploadFileDir . $fileName2;

            $uploadFileDir = 'assets/img/';
            $img_default = 'empleado.png';
            $uFile = $img_default;
        
            if (isset($fileName2) && !empty($fileName2)) 
            {
                if ($_FILES['inputUserPicture']['error'] > 0) 
                {
                    $msg = 'Error';
                    $Respuesta = '01ERROR AL SUBIR LA IMAGEN DE PERFIL, EL TAMAÑO NO ES VALIDO';
                    $bandera = 1;        
                }
                else 
                {
                    $extension_file2 = strtolower(substr($fileName2, -4));
                    if($extension_file2=='.png' OR $extension_file2=='.jpg' OR $extension_file2=='jpeg')
                    { 
                        $miarchivo = $uploadFileDir . $fileName2;
                        if (move_uploaded_file($fileTmpPath2, $miarchivo)) 
                        {
                            $uFile = $fileName2;
                        } else 
                        {
                            $msg = 'Error';
                            $Respuesta = '01NO SE PUDO CARGAR LA IMAGEN DE PERFIL';
                            $bandera = 1;
                        }
                    }
                    else 
                    {
                        $msg = 'Error';
                        $Respuesta = '01EL FORMATO DE LA IMAGEN DE PERFIL NO ES VALIDA';
                        $bandera = 1;
                    }
                }
            }

            if ($bandera == 0) 
            {
                if (isset($fileName1) && !empty($fileName1)) 
                {
                    if ($_FILES['inputLogo']['error'] > 0) 
                    {
                        $msg = 'Error';
                        $Respuesta = '01ERROR AL SUBIR EL LOGO DE LA EMPRESA - EL TAMAÑO NO ES VALIDO';
                        $bandera = 1;
                    }
                    else 
                    {
                        //Validacion de la img del logo
                        $extension_file1 = strtolower(substr($fileName1, -4));
                        if ($extension_file1 == '.png' or $extension_file1 == '.jpg' or $extension_file1 == 'jpeg') 
                        {
                           if (move_uploaded_file($fileTmpPath1, $dest_path1)) 
                            {
                                $dest_path1 = $fileName1;
                                $app->createCompany($aCompany, $dest_path1, $uFile, $_SESSION['USER']);
                                $msg = 'Hecho!';
                                $Respuesta = '00COMPAÑIA CREADA DE FORMA CORRECTA';
                            } 
                            else 
                            {
                                $msg = 'Error';
                                $Respuesta = '01NO SE AH LOGRADO CARGAR LA IMAGEN DE PERFIL, LOGO';
                            }    
                        } 
                        else
                        {
                            $msg = 'Error';
                            $Respuesta = '01EL FORMATO DEL LOGO NO ES VALIDA';
                        }
                    }
                }
                else 
                {
                    $msg = 'Error';
                    $Respuesta = '01NO SE CARGO EL LOGO';
                }
            }

        }
        else 
        {
            $msg = 'Error';
            $Respuesta='01LAS CONTRASEÑAS NO COINCIDEN';
        }
    }
    else
    {
        $msg = 'Error';
        $Respuesta='01EL CORREO UTILIZADO YA EXISTE';
    }

    if(substr($Respuesta, 0, 2) <> '00')
    {
        $value_empresa=$inputEmpresa;
        $value_rs=$inputRazonSocial;
        $value_curp=$CURP;
        $value_rfc=$RFC;

        $value_nombre=$inputNombreCompleto;
        $value_correo=$inputEmail;
        $value_tel=$inputTel;

        $value_cp=$inputCodigoPostal;
        $value_direccion=$inputDireccion;
        $value_estado=$inputEstado;
        $value_ciudad=$inputCiudad;

        $value_pass1=$inputPass1;
        $value_pass2=$inputPass2;
        $value_observa=$inputObservaciones;
        $value_comision=$comision;
        $value_correo_notificaciones=$inputEmailNotifications;
    }
}

include 'header.php';
?>

<div class="content">
            <div class="container-fluid">
                <div class="row">

                    <?php if ( strlen( $msg ) == 6 ) { ?> 
                        <div class="alert alert-success">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Successful - </b> <?php echo substr( $Respuesta , 2 );?>.</span>
                        </div>
                    <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                        <div class="alert alert-danger">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Error - </b> <?php echo substr( $Respuesta , 2 );?>.</span>
                        </div>
                    <?php } ?>
                    
                    <div class="col-md-9">
                        <div class="card">
                            <div class="header">
                                <!-- <h4 class="title">Crear Empresa</h4> -->
                                <h4 class="title">Create Company</h4>
                            </div>
                            <div class="content">
                                <form action="create_company?scr=2" method="POST" enctype="multipart/form-data" onsubmit="return checkSubmitBlock('crear_company');">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <!-- <label for="inputEmpresa">Empresa</label> -->
                                                <label for="inputEmpresa">Company</label>
                                                <input type="text" class="form-control" name="inputEmpresa" value="<?php echo $value_empresa; ?>" required >
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <!-- <label for="inputRazonSocial">Razon Social</label> -->
                                                <label for="inputRazonSocial">Business name</label>
                                                <input type="text" class="form-control" name="inputRazonSocial" value="<?php echo $value_rs; ?>" required >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <!-- <label for="inputNombreCompleto">Nombre Completo del responsable/dueño/administrador</label> -->
                                                <label for="inputNombreCompleto">Manager</label>
                                                <input type="text" class="form-control" name="inputNombreCompleto" value="<?php echo $value_nombre; ?>" required >
                                            </div>
                                        </div>
                                       
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <!-- <label for="inputEmail">Email</label> -->
                                                <label for="inputEmail">E-mail</label>
                                                <input type="email" class="form-control" name="inputEmail" value="<?php echo $value_correo; ?>" required >
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <!-- <label for="inputTelefono">Teléfono</label> -->
                                                <label for="inputTelefono">Telephone</label>
                                                <input type="number" class="form-control" min="2400000000" value="<?php echo $value_tel; ?>" max="9999999999" name="inputTelefono" required maxlength="10" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <!-- <label for="inputPass1">Contraseña</label> -->
                                                <label for="inputPass1">Password</label>
                                                <input type="password" class="form-control" name="inputPass1" id="inputPass1" value="<?php echo $value_pass1; ?>" required >
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <!-- <label for="inputPass2">Re - Contraseña</label> -->
                                                <label for="inputPass2">Repeat - Password</label>
                                                <input type="password" class="form-control" name="inputPass2" id="inputPass2" value="<?php echo $value_pass2; ?>" required >
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="for-group">
                                                <!-- <label for="inputPass2">Ver Contraseña</label> -->
                                                <label for="inputPass2">View Password</label>
                                                <button class="btn btn-primary" type="button" onclick="mostrarContrasena('inputPass1','inputPass2');"><i class="pe-7s-look" style="font-size:18px;"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <!-- <label for="inputDireccion">Dirección</label> -->
                                                <label for="inputDireccion">Address</label>
                                                <input type="text" class="form-control" name="inputDireccion" value="<?php echo $value_direccion; ?>" required >
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <!-- <label for="inputEstado">Estado</label> -->
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
                                                <!-- <label for="inputCiudad">Ciudad</label> -->
                                                <label for="inputCiudad">City</label>
                                                <select class="form-control" name="inputCiudad" id="ciudades" required="">
                                                    <option value="<?php echo $value_ciudad; ?>"><?php echo $value_ciudad; ?></option>
                                                </select>
                                             
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <!-- <label for="inputCodigoPostal">Codigo Postal</label> -->
                                                <label for="inputCodigoPostal">Postal Code</label>
                                                <input type="number" min="10000" max="99998" class="form-control" name="inputCodigoPostal" value="<?php echo $value_cp; ?>" maxlength="5" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <!-- <label for="inputCodigoPostal">Comision a cobrar</label> -->
                                                <label for="inputCodigoPostal">Commission to collect</label>
                                                <input type="number" min="0" max="100" class="form-control" required placeholder="0.0" name="comision" step=".1" value="<?php echo $value_comision; ?>" maxlength="3" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <!-- <label for="inputEmail">Email notificaciones</label> -->
                                                <label for="inputEmail">E-mail notifications</label>
                                                <input type="email" class="form-control" name="inputEmailNotifications" value="<?php echo $value_correo_notificaciones; ?>" >
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="inputCards">Cards To use</label>
                                               
                                                <select class="form-control tarjetas" name="inputCards[]" required multiple>
                                                    <option value=""></option>
                                                    <?php for($i=0;$i<sizeof($tarjetas);$i+=5)
                                                    {?>
                                                        <option value="<?php echo utf8_encode($tarjetas[$i+3]); ?>"><?php echo utf8_encode($tarjetas[$i]);?></option>
                                                    <?php
                                                    }?>
                                                </select> 
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <!-- <label for="inputLogo">Logo Empresa</label> -->
                                                <label for="inputLogo">Logo Company</label>
                                                <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                                <input class="form-control" name="inputLogo" type="file" required />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <!-- <label for="inputLogo">Imagen Perfil</label> -->
                                                <label for="inputLogo">Profile Image</label>
                                                <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                                <input class="form-control" name="inputUserPicture" type="file" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <!-- <label for="inputObservaciones">Observaciones</label> -->
                                                <label for="inputObservaciones">Observations</label>
                                                <textarea rows="5" class="form-control" name="inputObservaciones" ><?php echo $value_observa; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <input type="submit" id="crear_company" value="Save" class="btn btn-info btn-fill pull-right"/>
                                    <a href="companys?scr=2" class="btn btn-default btn-sm" >
                                        <i class="pe-7s-back" style="font-size:16px;"></i> Return
                                    </a>

                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<?php
echo "<script type='text/javascript'> $('.tarjetas').select2();</script>";

include 'footer.php';