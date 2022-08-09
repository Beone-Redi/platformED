<?php

// Inicio la variables de sesion.
if (!isset($_SESSION)) 
{
    session_start();
}

if (isset($_SESSION['ACTIVO']) <> "2019") 
{
    header("Location: login.php");
}

include "include/coredata.php";
$app = new app();

$msg = '';

if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" )
{
    $inputNombreCompleto    = $app->validoInput( $_POST['inputNombreCompleto'] );
    $inputEmail             = $app->validoInput( $_POST['inputEmail'] );
    $inputTel               = $app->validoInput( $_POST['inputTelefono'] );
    $inputDireccion         = $app->validoInput( $_POST['inputDireccion'] );
    $inputCodigoPostal      = $app->validoInput( $_POST['inputCodigoPostal'] );
    $inputCiudad            = $app->validoInput( $_POST['inputCiudad'] );
    $inputPerfil            = $app->validoInput( $_POST['inputPerfil'] );
    $inputObservaciones     = $app->validoInput( $_POST['inputObservaciones'] );
    $update                 = date( 'Y-m-d H:i:s' );

    $aCompany =
    [
        $inputNombreCompleto,   // 0
        $inputEmail,            // 1
        $inputTel,              // 2
        $inputDireccion,        // 3
        $inputCodigoPostal,     // 4
        $inputCiudad,           // 5
        $inputPerfil,           // 6
        $inputObservaciones,    // 7
        $update,                // 8
        $_GET['idu']            // 9
    ];

    if ( $app->updateUser( $aCompany ) )
    {
        $msg = 'Hecho!';
    }
    else 
    {
        $msg = 'Error';
    }
}

/**
 * Variables de intercambio.
 *  a.`ide`, a.`email`, c.`company`, a.`fullname`, a.`address`, a.`city`, a.`zip`, a.`aboutme`, a.`picture`, b.`idcard`, a.`perfil`
 */
$dUsuario = $app->viewUser( $_SESSION['USER'] );

include 'header.php';

?>

<div class="content">
            <div class="container-fluid">
                <div class="row">

                    <?php if ( strlen( $msg ) == 6 ) { ?> 
                        <div class="alert alert-success">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Exitoso - </b> Se actualizo de forma exitosa.</span>
                        </div>
                    <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                        <div class="alert alert-danger">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Error - </b> Se se a podido actualizar verifique los datos e intente mas tarde.</span>
                        </div>
                    <?php } ?>

                    <div class="col-md-8">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Ver Perfil</h4>
                            </div>
                            <div class="content">
                                <form method="POST" action="view_perfil.php?scr=3&idu=<?php echo $_GET['idu']; ?>">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Compania</label>
                                                <input type="text" class="form-control" disabled value="<?php echo $dUsuario[2]; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Nombre Completo</label>
                                                <input type="text" class="form-control" name="inputNombreCompleto" value="<?php echo $dUsuario[3]; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Email</label>
                                                <input type="email" class="form-control" name="inputEmail" value="<?php echo $dUsuario[1]; ?>">
                                            </div>
                                        </div>    
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="inputPerfil">Perfil</label>
                                                <select class="form-control" name="inputPerfil">
                                                    <option value="EMPRESA">Empresa</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Dirección</label>
                                                <input type="text" class="form-control" name="inputDireccion" value="<?php echo $dUsuario[4]; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Codigo Postal</label>
                                                <input type="number" min="10000" max="99998" class="form-control" name="inputCodigoPostal" value="<?php echo $dUsuario[6]; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Ciudad</label>
                                                <input type="text" class="form-control" name="inputCiudad" value="<?php echo $dUsuario[5]; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Observaciones</label>
                                                <textarea rows="5" class="form-control" name="inputObservaciones" placeholder="" ><?php echo $dUsuario[7]; ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-info btn-fill pull-right">Actualizar Perfil</button>
                                    &nbsp;
                                    <a href="perfiles.php?scr=3" class="btn btn-default btn-sm" >
                                        <i class="pe-7s-back" style="font-size:16px;"></i> Volver
                                    </a>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-user">
                            <div class="image">
                                <img src="./assets/img/<?php echo $dEmpresas[9]; ?>" alt="..."/>
                            </div>
                            <div class="content">
                                <div class="author">
                                    <a href="#">
                                        <img class="avatar border-gray" src="<?php echo $dUsuario[8]; ?>" alt="..."/>
                                        <h4 class="title"><?php echo $dUsuario[3]; ?><br />
                                            <small><?php echo $dUsuario[10]; ?></small>
                                        </h4>
                                    </a>
                                </div>
                                <p class="description text-center"></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

<?php
include 'footer.php';