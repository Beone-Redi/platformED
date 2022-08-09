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

$urlToken       = $app->urlToken();

if ( isset( $_GET['urlToken'] ) && isset( $_SESSION['urlToken'] ) && isset( $_GET['idu'] ) && isset( $_GET['sta'] ) )
{
    if ( $_SESSION['urlToken'] === $_GET['urlToken'] )
    {
        $datacompany = $app->viewCompany($_GET['idu']);
        $app->updateStatusCompany( $_GET['idu'], $_GET['sta'],$datacompany[3] );
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
$dEmpresasI     = $app->viewAllCompanysByPerfil('EMPRESA');

include 'header.php';
$permisos=$_SESSION['PERMISOS'];
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Administración de Empresas</h4>
                        <p class="category">Vista general de las empresas activas e inactivas del sistema.</p>
                        <p class="category">
                            </br>
                            <?php if ( $app->optSearch($permisos,'opt21')) { // opt de crear Empresas?>
                            <a href="create_company?scr=2" class="btn btn-default btn-sm" >
                                <i class="pe-7s-plus" style="font-size:16px;"></i> Crear Empresa
                            </a>
                            <?php } ?>
                        </p>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table class="table table-hover table-striped" id="Companys">
                            <thead>
                                <th>ID</th>
                                <th>Empresa</th>
                                <th>Razon Social</th>
                                <th>Teléfono</th>
                                <th>Logo</th>
                                <th>Fondos</th>
                                <th>Estatus</th>
                                <th>Opciones</th>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i <= count( $dEmpresasI ); $i += 7) {
                                    if ( isset( $dEmpresasI[$i] ) ) {
                                ?>
                                <tr>
                                    <td><?php echo utf8_encode($dEmpresasI[$i]); ?></td>
                                    <td><?php echo utf8_encode(substr(strtoupper($dEmpresasI[$i + 1]),0,30)); ?></td>
                                    <td><?php echo utf8_encode(substr(strtoupper($dEmpresasI[$i + 2]),0,30)); ?></td>
                                    <td><?php echo $dEmpresasI[$i + 3]; ?></td>
                                    <td> <img src="assets/img/<?php echo $dEmpresasI[$i + 4]; ?>" alt="" height="13" width="60"></td>
                                    <td><?php echo $dEmpresasI[$i + 5]; ?></td>
                                    <td>
                                        <?php 
                                            if($dEmpresasI[$i + 6] == '1')
                                            {
                                                echo 'ACTIVA';
                                            } 
                                            else
                                            {
                                                echo 'INACTIVA';
                                            }
                                        ?>
                                    </td>
                                    <?php if ( $app->optSearch($permisos,'opt22')) 
                                    { // opt de Editar Empresas?>
                            
                                    <td>
                                        <a href="view_company?scr=2&idu=<?php echo $dEmpresasI[$i]; ?>" rel="tooltip" title="Editar" class="btn btn-default btn-sm">
                                            <i class="pe-7s-search" style="font-size:18px;"></i>
                                        </a>
                                        <?php 
                                            if( $dEmpresasI[$i + 6] === '1' )
                                            {
                                        ?>
                                                <a href="companys?scr=2&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $dEmpresasI[$i]; ?>&sta=<?php echo $dEmpresasI[$i + 6]; ?>" rel="tooltip" title="Desactivar" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-close-circle" style="font-size:18px;"></i>
                                                </a>
                                        <?php
                                            } 
                                            else
                                            {
                                        ?>
                                                <a href="companys?scr=2&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $dEmpresasI[$i]; ?>&sta=<?php echo $dEmpresasI[$i + 6]; ?>" rel="tooltip" title="Activar" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-refresh" style="font-size:18px;"></i>
                                                </a>
                                        <?php
                                            }
                                        ?>
                                    </td>
                                    <?php 
                                    } 
                                    else
                                    {
                                    ?>
                                    <td>
                                    </td>
                                    <?php 
                                    }?>
                                </tr>
                                <?php   }   } ?>
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