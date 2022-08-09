<?php 
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['ACTIVO']) <> "2019") {
    header("Location: login");
}

include "include/coredata.php";
$app = new app();
$msg = '';

if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" )
{
    $inputEmail                 =$app->validoInput($_POST['inputEmail']);
    $inputContraseñaActual      =$app->validoInput($_POST['inputPass1']);
    $inputContraseñaNueva       =$app->validoInput($_POST['inputPass2']);
    $inputContraseñaRepNueva    =$app->validoInput($_POST['inputPass3']);
    $aCompany =
    [
        $inputEmail,             // 0
        $inputContraseñaActual,  // 1
        $inputContraseñaNueva,   // 2
        $_SESSION['USER']        // 3
    ];
    
    //Validar la contraseña se igual
    if ($app->getPassExist($aCompany) != '') 
    {
      
        if ($inputContraseñaNueva === $inputContraseñaRepNueva) 
        {
            if ( $app->change_password( $aCompany ) )
            {
                $msg = 'Hecho!';
                $Resultado = '00CONTRASEÑA ACTUALIZADA.';
            }
            else 
            {
                $msg = 'Error';
                $Resultado = '01NO SE PUDO ACTUALIZAR LA CONTRASEÑA';
            }
        }
        else 
        {
            $msg = 'Error';
            $Resultado = '01LAS CONTRASEÑAS NUEVAS NO COINCIDEN';
        }
    } 
    else 
    {
        $msg = 'Error';
        $Resultado = '01LA CONTRASEÑA ACTUAL NO COINCIDE';
    }
}

$dUsuario = $app->viewEmailUser( $_SESSION['USER'] );
include 'header.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!--Mensajes de las operaciones Exitoso o Error-->
            <?php if (strlen($msg) == 6) { ?>
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Exitoso - </b> <?php echo substr($Resultado, 2); ?>.</span>
                </div>
            <?php } elseif (strlen($msg) == 5) { ?>
                <div class="alert alert-danger">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> <?php echo substr($Resultado, 2); ?>.</span>
                </div>
            <?php } ?>

            <div class="col-md-10">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Cambiar contraseña</h4> -->
                        <h4 class="title">Change password</h4>
                    </div>
                    <div class="content">
                    <form action="change_password" method="POST" enctype="multipart/form-data" onsubmit="return checkSubmit('change_password');">

                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <!-- <label for="inputEmail">Email</label> -->
                                    <label for="inputEmail">E-mail</label>
                                    <input type="email" class="form-control" name="inputEmail" value="<?php echo $dUsuario[0]; ?>"readonly="readonly" >
                                </div>
                            </div>
                        
                            <div class="col-md-5">
                                <div class="form-group">
                                    <!-- <label for="inputPass1">Contraseña actual </label> -->
                                    <label for="inputPass1">Current password </label>
                                    <input type="password" class="form-control" name="inputPass1" id="inputPass1" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <div class="form-group">
                                    <!-- <label for="inputPass2">Nueva contraseña </label> -->
                                    <label for="inputPass2">New password </label>
                                    <input type="password" class="form-control" minlength="8" maxlength="21" name="inputPass2" id="inputPass2" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <!-- <label for="inputPass3">Repetir nueva contraseña </label> -->
                                    <label for="inputPass3">Repeat new password </label>
                                    <input type="password" class="form-control" minlength="8" maxlength="21" name="inputPass3" id="inputPass3" required>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="for-group">
                                    <!-- <label for="inputPass4">Ver contraseña </label> -->
                                    <label for="inputPass4">View Password </label>
                                    <button class="btn btn-primary" type="button" onclick="mostrarContrasena('inputPass2','inputPass3');"><i class="pe-7s-look" style="font-size:18px;"></i></button>
                                </div>
                            </div>
                        </div>
                
                        <input type="submit" id="change_password" value="Save" name="act"  class="btn btn-info btn-fill pull-right" />
                            <a href="dashboard?scr=6" class="btn btn-default btn-sm">
                                <!-- <i class="pe-7s-back" style="font-size:16px;"></i> Volver -->
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
?>