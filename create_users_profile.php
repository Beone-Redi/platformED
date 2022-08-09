<?php
// Inicio la variables de sesion.

/**
 * Generar codigos postales.
 * https://api-codigos-postales.herokuapp.com/v2/codigo_postal/66436
 * $data = json_decode(file_get_contents('https://api-codigos-postales.herokuapp.com/v2/codigo_postal/66436'), true);
 * 
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
$msg    = '';
$texto  = '';
$states=$app->GetAllState();
$value_nombre=$value_pass1=$value_pass2=$value_perfil=$value_correo=$value_tel=$value_observa=$value_file='';


if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
    $bandera=0;
    $fDirectory = 'assets/img/';
    $img_default = 'empleado.png';
    $uFile = $fDirectory . $img_default;

    $fileTmpPath   = $_FILES['inputImagenPerfil']['tmp_name'];
    $fileName      = $_FILES['inputImagenPerfil']['name'];

    $company=$_SESSION["EMPRESA"];
    $data=$app->viewCompany($company);    
    $usuario=$_SESSION["USER"];


    $inputLogo='';
    $inputEmpresa           = $company;
    $inputRazonSocial       = $data[1];
    $inputNombreCompleto    = $app->validoInput( $_POST['inputNombreCompleto'] );
    $inputEmail             = $app->validoInput( $_POST['inputEmail'] );
    $inputTel               = $app->validoInput( $_POST['inputTel'] );
    $inputDireccion         = $data[5];
    $inputCodigoPostal      = $data[7];
    $inputCiudad            = $data[6];
    $inputEstado            = $data[14];
    $inputObservaciones     = $app->validoInput( $_POST['inputObservaciones'] );
    $inputClave1            = $app->validoInput( $_POST['inputPass1']);
    $inputClave2            = $app->validoInput( $_POST['inputPass2']);
    $update                 = date('Y-m-d H:i:s');
    $inputPerfil=$_POST["inputEmpresa"];
    
    $aCompany =
    [
        utf8_decode($inputEmpresa),          //0
        utf8_decode($inputRazonSocial),      //1
        utf8_decode($inputNombreCompleto),   //2
        $inputEmail,            //3
        $inputTel,              //4
        $inputDireccion,        //5
        $inputCodigoPostal,     //6
        $inputCiudad,           //7
        $inputEstado,           //8
        $inputPerfil,           //9
        $inputLogo,             //10
        utf8_decode($inputObservaciones),    //11
        $update,                //12
        $inputClave1            //13
    ];
    
 
    if ( $inputClave1 === $inputClave2)
    {
        $fileTmpPath1   = $_FILES['inputImagenPerfil']['tmp_name'];
        $fileName      = $_FILES['inputImagenPerfil']['name'];
        if($app->getMailExist($inputEmail)=='')
        {
            if (isset($fileName) && !empty($fileName)) 
            {
                if ($_FILES['inputImagenPerfil']['error'] > 0) 
                {
                    $msg = 'Error';
                    $Respuesta = '01ERROR AL SUBIR LA IMAGEN DE PERFIL, EL TAMAÑO NO ES VALIDO';
                    $bandera = 1;        
                }
                else
                {
                    $extension_file1 = strtolower(substr($fileName, -4));
                    if ($extension_file1 == '.png' or $extension_file1 == '.jpg' or $extension_file1 == 'jpeg') 
                    {
                        $archivo = $fDirectory . $fileName;
                        if (move_uploaded_file($fileTmpPath, $archivo)) 
                        {
                            $uFile = $fDirectory . $fileName;
                        } 
                        else 
                        {
                            $msg = 'Error';
                            $Respuesta = '01NO SE PUDO CARGAR LA IMAGEN DE PERFIL';
                            $bandera = 1;
                        }
                    }
                    else 
                    {
                        $msg = 'Error';
                        $Respuesta = '01EL FORMATO DE LA IMAGEN NO ES VALIDA';
                        $bandera = 1;
                    }
                }
            }
            if($bandera==0)
            {
                $Respuesta = $app->createUserProfile( $aCompany, $uFile,$usuario );
                $msg = 'Hecho!';
                $Respuesta='00USUARIO CREADO DE FORMA CORRECTA';
            }
          
        }
        else
        {
            $msg = 'Error';
            $Respuesta='01EL CORREO UTILIZADO YA EXISTE';
        }
    }
    else
    {
        $msg = 'Error';
        $Respuesta='01LAS CONTRASEÑAS NO COINCIDEN';
    }
    if(substr($Respuesta, 0, 2) <> '00')
    {
        $value_nombre=$inputNombreCompleto;
        $value_correo=$inputEmail;
        $value_tel=$inputTel;
        $value_observa=$inputObservaciones;
        $value_pass1=$inputClave1;
        $value_pass2=$inputClave2;
        $value_perfil=$inputPerfil;
    }
    $texto  = $Respuesta;

}
$perfiles_disponibles=$app->getNameProfilesbyLevel(3); // Nivel 3 es abajo de administrador
if ( $_SESSION['PERFIL']=='IT' )
{
    $perfiles_disponibles=$app->getAllNameProfiles(); // Nivel 3 es abajo de administrador
}
include 'header.php';
?>
<div class="content">
    <div class="container-fluid">
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Successful - </b> <?php echo substr( $texto, 2 ); ?></span>
                </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                <div class="alert alert-danger">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> <?php echo substr( $texto, 2 ); ?></span>
                </div>
            <?php } ?>

            <div class="col-md-9">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Crear usuario</h4> -->
                        <h4 class="title">Create user</h4>
                    </div>
                    <div class="content">
                        <form action="create_users_profile?scr=13" method="POST" enctype="multipart/form-data" onsubmit="return checkSubmit('btsubmit');" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <!-- <label for="inputEmpresa">Perfil</label> -->
                                        <label for="inputEmpresa">Profile</label>
                                        <select class="form-control" name="inputEmpresa">
                                            <option value="<?php echo $value_perfil; ?>"><?php echo $value_perfil; ?></option>
                                            <?php for($i=0;$i<sizeof($perfiles_disponibles);$i++)
                                            {
                                            ?>                            
                                            <option value="<?php echo $perfiles_disponibles[$i];?>"><?php echo $perfiles_disponibles[$i];?></option>
                                        <?php }?>
                                        </select>
                                    </div>
                                </div>  
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label for="inputNombreCompleto">Nombre completo</label> -->
                                        <label for="inputNombreCompleto">Full name</label>
                                        <input type="text" class="form-control" name="inputNombreCompleto" value="<?php echo $value_nombre; ?>" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <!-- <label for="inputEmail">Correo</label> -->
                                        <label for="inputEmail">E-mail</label>
                                        <input type="email" class="form-control" name="inputEmail" value="<?php echo $value_correo; ?>" required />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <!-- <label for="inputTel">Telefono</label> -->
                                        <label for="inputTel">Telephone</label>
                                        <input type="number" class="form-control" min="2400000000" max="9999999999" name="inputTel" value="<?php echo $value_tel; ?>" maxlength="10" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">           
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <!-- <label for="inputImagenPerfil">Imagen de perfil</label> -->
                                        <label for="inputImagenPerfil">Profile picture</label>
                                        <input type="hidden" name="MAX_FILE_SIZE" value="300000" />
                                        <input class="form-control" name="inputImagenPerfil" type="file"/>
                                    </div>
                                </div>
                            </div>
                                    
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <!-- <label for="inputPass1">Contraseña</label> -->
                                        <label for="inputPass1">Password</label>
                                        <input type="password" class="form-control" minlength="8" maxlength="21" name="inputPass1" id="inputPass1" value="<?php echo $value_pass1; ?>" required >
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <!-- <label for="inputPass2">Repertir contraseña</label> -->
                                        <label for="inputPass2">Repeat password</label>
                                        <input type="password" class="form-control" minlength="8" maxlength="21" name="inputPass2" id="inputPass2" value="<?php echo $value_pass2; ?>" required >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="for-group">
                                        <!-- <label for="inputPass2">Ver clave</label> -->
                                        <label for="inputPass2">View Password</label>
                                            <button class="btn btn-primary" type="button" onclick="mostrarContrasena('inputPass1','inputPass2');"><i class="pe-7s-look" style="font-size:18px;"></i></button>
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

                            <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right">Save</button>
                            &nbsp;
                            <a href="users_admin?scr=13" class="btn btn-default btn-sm" >
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
include 'footer.php';