<?php
/*
 * Login
 * Version: 19.0319
 * Autor:   Sergio Marquez
 * 
 */

/* establecer el limitador de caché a 'private' */
session_cache_limiter( 'private' );
$cache_limiter = session_cache_limiter();

/* establecer la caducidad de la caché a 30 minutos */
session_cache_expire( 60 * 18 );
$cache_expire = session_cache_expire();

// Inicio la variables de sesion.
if (!isset($_SESSION)) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) ) 
{
    header( 'Location: dashboard?scr=6' );
}

include "include/coredata.php";
$app                    = new app();
$Empresas               = $app->viewCompanys();
$_SESSION["mensaje"]    = '';

if ( $_POST ) 
{

    // Limpieza de variables Post.
    $id         = filter_var( $_POST['fieldID'], FILTER_SANITIZE_STRING );
    $clave      = filter_var( $_POST['fieldClave'], FILTER_SANITIZE_STRING );
    $capChalote = filter_var( $_POST['capchalote'], FILTER_SANITIZE_STRING );

    if ( $capChalote == $_SESSION['capchacal'] ) 
    {
        $aUser = [ $id, $clave ];
        $valido = $app->login( $aUser );

        if ( strlen( $valido[0] ) > 0 ) 
        {
            $_SESSION['EMPRESA']    = $valido[1];
            $_SESSION['ACTIVO']     = "2019";
            $_SESSION['TIEMPOIN']   = date("Y-m-d H:i:s");
            $_SESSION['USER']       = $valido[0];
            $_SESSION['PERFIL']     = $valido[2];
            
            $_SESSION['PERMISOS'] = $app->getoptionsUser($valido[2]);
            
            // Envia al cliente al Dashboard.
            header( 'Location:dashboard' );
        } 
        else 
        {
            // Primer error de captura.
            $_SESSION['mensaje'] = "En su intento anterior su Usuario o Clave no eran correctos.";
            header( 'Location:login' );
        }
    } 
    else 
    {
        $_SESSION['mensaje'] = 'Pregunta de seguridad no valida.';
        header( 'Location:login' );
    }
}

// Genero el capChacal antiRobot.
function GRS( $length = 1 ) 
{
    return substr( str_shuffle( '0123456789' ), 0, $length );
}

$casa = $_SESSION['capchacal'] = GRS();

?>

<!DOCTYPE html>
<html >
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Ener</title>
        <link rel="stylesheet" href="assets/css/stylelogin.css">
        <!--Google Font - Work Sans-->
        <link href='https://fonts.googleapis.com/css?family=Work+Sans:400,300,700' rel='stylesheet' type='text/css'>
        <link rel="import" href="https://www.polymer-project.org/0.5/components/paper-ripple/paper-ripple.html">
    </head>
    <body>
        <div class="container">
            <div class="profile">
                <button id="toggleProfile">
                    <img height="100" width="279" src="assets/img/recargas.png" alt="Recargas" /> 
                </button>
                <div class="profile__form">
                    <div class="profile__fields">
                        <form action="login_backend" method="POST" />
                        
                            <div class="field">
                                <input type="text" id="fieldID" name="fieldID" class="input" required pattern=.*\S.* />
                            </div>
                            <div class="field">
                                <label style="margin-left: 1%" for="fieldID" class="label">User</label>
                            </div>
                            <div class="field">
                                <input type="password" id="fieldClave" name="fieldClave" class="input" required pattern=.*\S.* />
                            </div>
                            <div class="field">
                                <label style="margin-left: 1%" for="fieldClave" class="label">Password</label>
                            </div>
                            <div class="field">
                                <input type="type" id="fieldcapchalote" name="capchalote" class="input" required />
                            </div>
                            <div class="field">
                                <label style="margin-left: 1%" for="fieldcapchalote" class="label"><?php echo "Enter this number: " . $casa; ?></label>
                            </div>
                            <div class="field">
                                <p><br><?php echo $_SESSION["mensaje"];?></p>
                            </div>
                            <div class="profile__footer">
                                <input class="btn btn-round center" style="cursor: pointer;" type="submit" id="summit" value="Login" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script src="assets/js/login.js"></script>
    </body>
</html>