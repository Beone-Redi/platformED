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
$company=$_SESSION["EMPRESA"];

$urlToken       = $app->urlToken();
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
$data     = $app->viewUsersByCompany($company);

include 'header.php';

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Usuarios</h4> -->
                        <h4 class="title">Users</h4>
                        <!-- <p class="category">Vista General de los usuarios de la compañia.</p> -->
                        <p class="category">General view of company users.</p>
                        <p class="category">
                            </br>
                            <?php if ( $app->optSearch($permisos,'opt30')) { // opt de crear Usuarios nivel empresa?>
                           
                            <a href="create_users_profile_company?scr=14" class="btn btn-default btn-sm" >
                                <!-- <i class="pe-7s-plus" style="font-size:16px;"></i> Crear usuario -->
                                <i class="pe-7s-plus" style="font-size:16px;"></i> Create user
                            </a>
                            <?php } ?>
                           
                        </p>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table class="table table-hover table-striped" id="myTable">
                            <thead>
                                <th>Name</th>
                                <th>Logo</th>
                                <th>Telephone</th>
                                <th>Profile</th>
                                <th>Status</th>
                                <th>Options</th>
                            </thead>
                            <tbody>
                                <?php for ($i = 0; $i < count( $data ); $i += 6) 
                                {
                                    
                                ?>
                                <tr>
                                    <td><?php echo $data[$i+2]; ?></td>
                                    <td> <img src="assets/img/<?php echo $data[$i]; ?>" alt="" height="13" width="60"></td>
                                    <td><?php echo $data[$i + 3]; ?></td>
                                    <td><?php echo strtoupper($data[$i + 5]); ?></td>
                                    <td>
                                        <?php 
                                            $dato='INACTIVE';
                                            if ($data[$i+4]==='1' ){
                                             $dato='ACTIVE'; 
                                            }
                                            echo $dato;
                                        ?>
                                    </td>
                                    <?php if ( $app->optSearch($permisos,'opt31')) 
                                    { // opt de editar usuario nivel empresa?>
                           
                                    <td>
                                        <a href="edit_users_company?scr=14&idu=<?php echo $data[$i+1]; ?>" rel="tooltip" title="Update" class="btn btn-default btn-sm">
                                            <i class="pe-7s-search" style="font-size:18px;"></i>
                                        </a>
                                        <?php 
                                            if( $data[$i + 4] === '1' )
                                            {
                                        ?>
                                                <a href="users_company?scr=14&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $data[$i+1]; ?>&sta=<?php echo $data[$i + 4]; ?>" rel="tooltip" title="Inactive" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-close-circle" style="font-size:18px;"></i>
                                                </a>
                                        <?php
                                            } 
                                            else
                                            {
                                        ?>
                                                <a href="users_company?scr=14&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $data[$i+1]; ?>&sta=<?php echo $data[$i + 4]; ?>" rel="tooltip" title="Active" class="btn btn-default btn-sm">
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
                                        echo '<td></td>';
                                    } 
                                    ?>
                           
                                </tr>
                            <?php      
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