<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" or $_SESSION['EMPRESA'] === '0' ) 
{
    header( "Location: login" );
}
include "include/coredata.php";
include 'header.php';
$app        = new app();
$result=[];
$cards=$app->viewDataCardsByCompany($_SESSION['EMPRESA']);
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Reporte de empleados de la empresa</h4> -->
                        <h4 class="title">Company employee report</h4>
                        <!-- <p class="category">Vista general de reporte de empleados.</p> -->
                        <p class="category">Employee report overview.</p>
                    </div>
                    <div class="content table-responsive ">
                        <table id="myTable" class="table table-hover table-striped display">
                            <thead>
                                <th>Name of the employee</th>                            
                                <th>Card</th>
                                <th>Update date</th>
                                <th>User APP</th>  
                            </thead>
                            <tbody>
                            <?php for ($i=0; $i < sizeof($cards); $i+=4) 
                                {

                            ?>
                                <tr>
                                    <td><?php echo strtoupper(utf8_encode($cards[$i+1]));?></td>            
                                    <td><?php echo '****-****-'.substr($cards[$i], 0,4).'-'.substr($cards[$i], -4);?></td>
                                    <td><?php echo substr($cards[$i+3],8,2).'/'.substr($cards[$i+3],5,2).'/'.substr($cards[$i+3],0,4); ?></td>                                  
                                    <td><?php echo utf8_encode($cards[$i+2]);;?></td>
                                    
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