<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');


include "include/coredata.php";

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019") 
{
    header( "Location: login.php" );
}

$active = 'class="active"';

if ( isset( $_GET["scr"] ) )
{
    $SCRN   = $_GET["scr"];
    $SRC    = 'assets/img/sidebar-' . $SCRN . '.jpg';
}
else 
{
    $SCRN   = 6;
    $SRC    = 'assets/img/sidebar-6.jpg';
}

$subApp     = new app();
$dEmpresas  = $subApp->viewCompany( $_SESSION[ 'EMPRESA' ] );

if ( $_SESSION['EMPRESA'] === '1' )
{
    $Reports    = 'transactions.php?scr=55';
}
else
{
    $Reports    = 'transactions_clients.php?scr=55';
}

unset( $subApp );

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" or $_SESSION['EMPRESA'] === '0' ) 
{
    header( "Location: login.php" );
}

$app        = new app();
$msg        = FALSE;
$Empleados  = $app->viewEmployees( $_SESSION['EMPRESA'] );
$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
    $dEmpresasI = $app->en_layout_anchor( $_SESSION[ 'EMPRESA' ] );
}
else 
{
    $dEmpresasI = [];    
}

?>
<!DOCTYPE html>
<head>
	<meta charset="utf-8" />
	<link rel="icon" type="image/png" href="assets/img/favicon.ico">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>ENERGETICOS</title>
	
	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

	<!-- Need to use datatables.net -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<!-- Mini-extra style to be apply to tables with the dataTable plugin  -->

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>

    <!--  CSS -->
    <link href="assets/css/demo.css" rel="stylesheet" />

    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

    <!-- Grafica -->
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>

    <style>
        .dataTable table tr {
            border: solid 1px black;
        }
    </style>
	
	<script type="text/javascript">
        $(window).load(function() {
            $(".loader").fadeOut("slow");
        });
    </script>
</head>
<body>
<div class="loader"></div>
<div class="wrapper">
    <div class="sidebar" data-color="blue" data-image="<?php echo $SRC;?>">

    	<div class="sidebar-wrapper">
            <div class="logo">
                <a href="" class="simple-text">
                    <img src="./assets/img/<?php echo $dEmpresas[9]; ?>" alt="" height="34" width="160">
                </a>
            </div>

            <ul class="nav">
                
                <li <?php if ( $SCRN == 6 ) { echo $active; } ?>>
                    <a href="dashboard.php?scr=6">
                        <i class="pe-7s-graph"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ( $_SESSION['PERFIL'] === 'ADMIN' ) { ?>
                <li <?php if ( $SCRN == 2 ) { echo $active; } ?>>
                    <a href="companys.php?scr=2">
                        <i class="pe-7s-global"></i>
                        <p>Empresas</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $_SESSION['PERFIL'] === 'ADMIN' ) { ?>
                <li <?php if ( $SCRN == 3 ) { echo $active; } ?>>
                    <a href="perfiles.php?scr=3">
                        <i class="pe-7s-id"></i>
                        <p>Usuarios</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $_SESSION['PERFIL'] === 'EMPRESA' ) { ?>
                <li <?php if ( $SCRN == 9 ) { echo $active; } ?>>
                    <a href="clients.php?scr=9">
                        <i class="pe-7s-credit"></i>
                        <p>Tarjeta Habientes</p>
                    </a>
                </li>
                <?php } ?>

                <li <?php if ( $SCRN == 4 ) { echo $active; } ?>>
                    <a href="reload_funds.php?scr=4">
                        <i class="pe-7s-cash"></i>
                        <p>Fondeo</p>
                    </a>
                </li>

                <li <?php if ( $SCRN == 55 ) { echo $active; } ?>>
                    <a href="<?php echo $Reports; ?>">
                        <i class="pe-7s-display1"></i>
                        <p>Reportes</p>
                    </a>
                </li>

                <li <?php if ( $SCRN == 7 ) { echo $active; } ?>>
                    <a href="user_manual.php?scr=7">
                        <i class="pe-7s-map"></i>
                        <p>Manuales</p>
                    </a>
                </li>

                <li <?php if ( $SCRN == 8 ) { echo $active; } ?>>
                    <a href="politics.php?scr=8">
                        <i class="pe-7s-news-paper"></i>
                        <p>Políticas de la información</p>
                    </a>
                </li>
                
            </ul>
    	</div>
    </div>

    <div class="main-panel">
        <nav class="navbar navbar-default navbar-fixed">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navigation-example-2">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Dashboard</a>
                </div>
                <div class="collapse navbar-collapse">
                    <ul class="nav navbar-nav navbar-left">
                        <li>
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-dashboard"></i>
								<p class="hidden-lg hidden-md">Dashboard</p>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <i class="fa fa-globe"></i>
                                <b class="caret hidden-lg hidden-md"></b>
							    <p class="hidden-lg hidden-md">
									1 Notificaciones
								    <b class="caret"></b>
								</p>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Notificación 1</a></li>
                            </ul>
                        </li>
                    </ul>

                    <ul class="nav navbar-nav navbar-right">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <p>
									Perfil
									<b class="caret"></b>
								</p>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><?php echo '<b>Empresa ID #' . $_SESSION['EMPRESA']; ?></b></a></li>
                                <li><a href="#"><?php echo '<b>' . $_SESSION['PERFIL']; ?></b></a></li>
                                <li class="divider"></li>
                                <li><a href="my_perfil.php">Editar</a></li>
                                <li class="divider"></li>
                                <li><a href="option.php">Configuración</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="logout.php">
                                <p>Salir</p>
                            </a>
                        </li>
						<li class="separator hidden-lg"></li>
                    </ul>
                </div>
            </div>
        </nav>
<div class="container">
<div class="container-fluid">
        <div class="row">

            <?php if ( strlen( $msg ) == 6 ) { ?> 
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span>Resultado de la consulta exitosamente.</span>
            </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
            <div class="alert alert-danger">
                <button type="button" aria-hidden="true" class="close">×</button>
                <span><b> Error - </b> No se pudo realizar la consulta intente mas tarde.</span>
            </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Reporte de la empresa</h4>
                        <p class="category">Vista general de reporte de la actividad de las empresas.</p>
                        <form action="transactions_clients.php?scr=55" method="POST" >
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCodigoPostal">Fecha Inicio</label>
                                        <input type="date" class="form-control" name="inputFechaInicio" min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>" value="" required >
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCiudad">Fecha Final</label>
                                        <input type="date" class="form-control" name="inputFechaFinal" min="<?php echo $minDate; ?>" max="<?php echo $maxDate; ?>" value="" required >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="inputPerfil">Usuario</label>
                                        <select class="form-control" name="inputPerfil" required >
                                        <?php for ($i = 0; $i <= count( $Empleados ); $i += 3) {
                                            if ( isset( $Empleados[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empleados[ $i ]; ?>"><?php echo $Empleados[ $i + 1 ] . ' - ****-****-**' . substr( $Empleados[ $i + 2 ], 10, 2 ) . '-' . substr( $Empleados[ $i + 2 ], 12, 4 ); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCiudad">&nbsp;</label>
                                        <input type="submit" class="form-control" value="Consultar">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="content table-responsive table-full-width">
                        <table id="myTable" class="table table-hover table-striped display">
                            <thead>
                                <th>ID</th>
                                <th>Compania</th>
                                <th>Emisora</th>
                                <th>Tarjeta</th>
                                <th>Monto</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                            </thead>
                            <tbody>
                            <?php for ( $i = 0; $i <= count( $dEmpresasI ); $i += 6 ) {
                                if ( isset( $dEmpresasI[$i] ) ) {
                            ?>
                                <tr>
                                    <td><?php echo $dEmpresasI[$i]; ?></td>
                                    <td><img src="./assets/img/logo-small.png" alt="" height="16" width="60"></td>
                                    <td><img src="assets/img/mc_vrt_opt_pos_46_1x.png" alt="MASTERCARD" ></td>
                                    <td><?php echo '****-****-**' . substr( $dEmpresasI[$i + 2], 0, 2 ) . '-' . substr( $dEmpresasI[$i + 2], -4 ); ?></td>
                                    <td><?php echo $dEmpresasI[$i + 3]; ?></td>
                                    <td><?php echo $dEmpresasI[$i + 4]; ?></td>
                                    <td><?php echo $dEmpresasI[$i + 5]; ?></td>
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
	
<footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                    <ul>
                        <li>
                            <a href="#">
                                Home
                            </a>
                        </li>
                    </ul>
                </nav>
                <p class="copyright pull-right">
                    &copy; <script>document.write(new Date().getFullYear())</script> ENERGETICO
                </p>
            </div>
        </footer>

    </div>
</div>


</body>
    <!--   Core JS Files   -->
    <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>
    
    <!-- Light Bootstrap Table Core javascript and methods -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

	<!-- Light Bootstrap -->
	<script src="assets/js/demo.js"></script>

    
	<script type="text/javascript">
    	$(document).ready(function(){

        	demo.initChartist();
            /*
        	$.notify({
            	icon: 'pe-7s-gift',
            	message: "<b>Dashboard</b>"

            },{
                type: 'info',
                timer: 4000
            });
            */
        });
        
        $(document).ready(function() {
            $('#tablesearch').DataTable();
        } );
	</script>
    
	<script  src="assets/js/index.js"></script>
	
	<!-- Need to use datatables.net -->
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function(){
    
    //Apply the datatables plugin to your table
    $('#myTable').DataTable();
    
});
</script>

</html>