<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset($_SESSION['ACTIVO'] ) <> "2019" ) 
{
    header("Location: login.php");
}

include "include/coredata.php";
$app = new app();

$dUsuarios  = $app->viewUsers( $_SESSION['PERFIL'] );
$urlToken   = $app->urlToken();

if ( isset( $_GET['urlToken'] ) && isset( $_SESSION['urlToken'] ) && isset( $_GET['idu'] ) && isset( $_GET['sta'] ) )
{
    if ( $_SESSION['urlToken'] === $_GET['urlToken'] )
    {
        $app->updateStatusUser( $_GET['idu'], $_GET['sta'] );
        $_SESSION['urlToken'] = $urlToken;
    }
    else 
    {
        $_SESSION['urlToken'] = $urlToken;
    }
}
else 
{
    $_SESSION['urlToken'] = $urlToken;
}

include 'header.php';
?>

<div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Administración de Usuarios</h4>
                                <p class="category">Vista generar de los usuarios activos e inactivos del sistema.</p>
                                <p class="category">
                                    </br>
                                    <a href="create_user.php?scr=3" class="btn btn-default btn-sm" >
                                        <i class="pe-7s-add-user" style="font-size:16px;"></i>
                                    </a>
                                </p>
                            </div>
                            <div class="content table-responsive table-full-width">
                                <table class="table table-hover table-striped">
                                    <thead>
                                        <th>ID</th>
                                        <th>Foto</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                    	<th>Empresa</th>
                                    	<th>Perfil</th>
                                        <th>Estatus</th>
                                        <th>Opciones</th>
                                    </thead>
                                    <tbody>
                                        <?php for ( $i = 0; $i <= count( $dUsuarios ); $i += 8 ) {
                                            if ( isset( $dUsuarios[$i] ) ) {
                                        ?>
                                        <tr>
                                            <td><?php echo $dUsuarios[$i]; ?></td>
                                            <td><img src="<?php echo $dUsuarios[$i + 7]; ?>" alt="<?php echo $dUsuarios[$i + 1]; ?>" height="30" width="30"></td>
                                            <td><?php echo $dUsuarios[$i + 1]; ?></td>
                                        	<td><?php echo $dUsuarios[$i + 6]; ?></td>
                                        	<td><img src="assets/img/<?php echo $dUsuarios[$i + 2]; ?>" alt="<?php echo $dUsuarios[$i + 2]; ?>" height="20" width="60"></td>
                                        	<td><?php echo $dUsuarios[$i + 4]; ?></td>
                                            <td>
                                                <?php 
                                                    if ( $dUsuarios[$i + 5] == '1' )
                                                    {
                                                        echo 'ACTIVO';
                                                    } 
                                                    else
                                                    {
                                                        echo 'INACTIVO';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="view_perfil.php?scr=3&idu=<?php echo $dUsuarios[$i]; ?>" rel="tooltip" title="Editar" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-search" style="font-size:18px;"></i>
                                                </a>
                                                <?php
                                                    if ( $dUsuarios[$i + 5] == '1' ) 
                                                    {
                                                ?>
                                                <a  href="companys.php?scr=2&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $dUsuarios[$i]; ?>&sta=<?php echo $dUsuarios[$i + 5]; ?>" class="btn btn-default btn-sm" rel="tooltip" title="Desactivar" >
                                                    <i class="pe-7s-close-circle" style="font-size:18px;"></i>
                                                </a>
                                                <?php
                                                    } 
                                                    else
                                                    {
                                                ?>
                                                <a  href="companys.php?scr=2&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $dUsuarios[$i]; ?>&sta=<?php echo $dUsuarios[$i + 5]; ?>" class="btn btn-default btn-sm" rel="tooltip" title="Desactivar" >
                                                    <i class="pe-7s-refresh" style="font-size:18px;"></i>
                                                </a>
                                                <?php
                                                    }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php 
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

<?php
include 'footer.php';