<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019") 
{
    header( "Location: login" );
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
$Empresa=$_SESSION['EMPRESA'];
$permisos=$_SESSION['PERMISOS'];

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
<!--	<link rel="icon" type="image/png" href="assets/img/favicon.ico"> -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>Energex Pass Corp.</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>

    <!--  CSS -->
    <link href="assets/css/demo.css" rel="stylesheet" /> 

    <!--     Fonts and icons     -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

    <!-- Grafica -->
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <!-- <script src="assets/js/funciones.js"></script> -->
 
    <!-- Fancybox -->
    <link  href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
    
    <!-- Need to use datatables.net -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
      
    <!-- Mini-extra style to be apply to tables with the dataTable plugin  -->
    <!-- Funciones en javascript -->
    <script type='text/javascript' src="assets/js/funciones.js?filever=<?=filesize('assets/js/funciones.js')?>"></script>
    
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    
    <!-- Selected 2 -->
    <!-- JS Files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
     
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.7/js/select2.min.js"></script>
   <!-- selected 2-->


    <script type="text/javascript">
        $(window).load(function() {
            $(".loader").fadeOut("slow");
        });
        drawBar(1);
    </script>

</head>

<body>
<div class="loader"></div>
<div class="wrapper">
    <div class="sidebar" data-color="azure" data-image="<?php echo $SRC;?>">

    	<div class="sidebar-wrapper">
            <div class="logo">
                <a href="" class="simple-text">
                    <img src="./assets/img/<?php echo $dEmpresas[9]; ?>" alt="" height="%50" width="60%">
                </a>
            </div>

            <ul class="nav">
                
                <li <?php if ( $SCRN == 6 ) { echo $active; } ?>>
                    <a href="dashboard?scr=6" onclick="checkSubmitBlock('btnsubmit');">
                        <i class="pe-7s-graph"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <?php if ( $subApp->optSearch($permisos,'opt10')) { //Ver compañias ?> 
                <li <?php if ( $SCRN == 2 ) { echo $active; } ?>>
                    <a href="companys?scr=2" onclick="checkSubmitBlock('btnsubmit');">
                        <i class="pe-7s-global"></i>
                        <!-- <p>Empresas</p> -->
                        <p>Companys</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $subApp->optSearch($permisos,'opt14')) { //Ver Tarjeta habientes ?> 
                <li <?php if ( $SCRN == 9 ) { echo $active; } ?>>
                    <a href="clients?scr=9" onclick="checkSubmitBlock('btnsubmit');">
                        <i class="pe-7s-credit"></i>
                        <!-- <p>Tarjeta Habientes</p> -->
                        <p>Card Holders</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $subApp->optSearch($permisos,'opt13')) { // ver usuarios a nivel admin?>          
                <li <?php if ( $SCRN == 13 ) { echo $active; } ?>>
                    <a href="users_admin?scr=13"  onclick="return checkSubmitBlock('crear_company');">
                        <i class="pe-7s-users"></i>
                        <!-- <p>Usuarios</p> -->
                        <p>Users</p>
                    </a>
                </li>
                <?php }?>

                <?php if ( $subApp->optSearch($permisos,'opt17')) { // ver usuarios nivel empresa?>      
                <li <?php if ( $SCRN == 14 ) { echo $active; } ?>>
                    <a href="users_company?scr=14"  onclick="return checkSubmitBlock('crear_company');">
                        <i class="pe-7s-users"></i>
                        <!-- <p>Usuarios</p> -->
                        <p>Users</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $subApp->optSearch($permisos,'opt67')) { // Ver Reportes?>
                <li <?php if ( $SCRN == 9 ) { echo $active; } ?> class="nav-item" >
                    <a class="nav-link collapsed" href="#submenu1"  data-toggle="collapse"><i class="pe-7s-credit"></i>
                        <p>Card Holders</p></a>               
                        <div class="collapse" id="submenu1" aria-expanded="true ">
                        <ul class="nav">          
                            <?php if ( $subApp->optSearch($permisos,'opt68')){//opt21 Fondeo empresas ?>
                                <li <?php if ( $SCRN == 10 ) { echo $active; } ?>>
                                    <a href="create_user_IT?scr=9" onclick="checkSubmitBlock('btnsubmit');">
                                        <p>Cardholder registration</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ( $subApp->optSearch($permisos,'opt66')){//opt21 Fondeo empresas ?>
                                <li <?php if ( $SCRN == 10 ) { echo $active; } ?>>
                                    <a href="changes_cards?scr=9" onclick="checkSubmitBlock('btnsubmit');">
                                        <p>Card reassignment</p>
                                    </a>
                                </li>
                            <?php } ?>
                            <?php if ( $subApp->optSearch($_SESSION[ 'PERMISOS' ],'opt64')) { // Ver Reportes de pagos paynet?>
                                <li <?php if ( $SCRN == 10 ) { echo $active; } ?>>
                                    <a class="nav-link py-0" href="cards_view_users?scr=9" onclick="checkSubmitBlock('btnsubmit');">
                                        <p>Card-Member / User Query</p>
                                    </a>
                                </li>
                            <?php } // Ver Reportes?>
                        </ul>
                    </div>
                </li>
                <?php } ?>

                <?php if ( $subApp->optSearch($permisos,'opt11')){//opt21 Fondeo empresas ?>
                <li <?php if ( $SCRN == 4 ) { echo $active; } ?>>
                    <a href="reload_funds_company?scr=4" onclick="checkSubmitBlock('btnsubmit');">
                        <i class="pe-7s-cash"></i>
                        <!-- <p>Fondeo</p> -->
                        <p>Founds</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $subApp->optSearch($permisos,'opt15')){//opt21 Fondeo empresas ?>
                <li <?php if ( $SCRN == 4 ) { echo $active; } ?>>
                    <a href="reload_funds_cards?scr=4" onclick="checkSubmitBlock('btnsubmit');">
                        <i class="pe-7s-cash"></i>
                        <!-- <p>Fondeo</p> -->
                        <p>Founds</p>
                    </a>
                </li>
                <?php } ?>

                <?php if ( $subApp->optSearch($permisos,'opt12') OR $subApp->optSearch($permisos,'opt16')) { // Ver Reportes?>

                <li <?php if ( $SCRN == 55 ) { echo $active; } ?> class="nav-item" >
                    <a class="nav-link collapsed" href="#submenu2"  data-toggle="collapse"><i class="pe-7s-cash"></i>
                        <!-- <p>Reportes </p></a> -->
                        <p>Reports</p></a>                       
                        <div class="collapse" id="submenu2" aria-expanded="true ">
                            <ul class="nav">          
                                <!-- Sections reports Admin -->      
                                <?php if ( $subApp->optSearch($permisos,'opt40')) { // Ver Reportes De fondeo para empresas?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="transactions?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Fondeo</p> -->
                                            <p>Founds</p>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ( $subApp->optSearch($permisos,'opt41')) { // Ver Reportes De movimientos de tarjetas nivel admin?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="movements_cards?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Movimientos tarjetas</p> -->
                                            <p>Card movements</p>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ( $subApp->optSearch($permisos,'opt42')) { // Ver Reportes De fondeos a empresas y tarjetas?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="report_funds_company_admin?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Fondeo Empresas</p> -->
                                            <p>Founds companies</p>
                                        </a>
                                    </li>
                                <?php } ?>
                                <?php if ( $subApp->optSearch($_SESSION[ 'PERMISOS' ],'opt50')) { // Ver Reportes De movimientos en cuenta maestra?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="transactions_MA?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Reporte Cuenta Maestra</p> -->
                                            <p>Master account report</p>
                                        </a>
                                    </li>
                                <?php } // Ver Reportes?>
                                <?php if ( $subApp->optSearch($_SESSION[ 'PERMISOS' ],'opt48')) { // Ver Reportes de pagos paynet?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="report_paynet?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Reporte Paynets</p> -->
                                            <p>Paynets report</p>
                                        </a>
                                    </li>
                                <?php } // Ver Reportes?>
                                <!-- End sections reports Admin -->      
                                
                                <!-- Sections reports Company -->
                                <?php if ( $subApp->optSearch($permisos,'opt44')) { // Ver Reportes De fondeo nivel admin?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="transactions_clients?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Fondeo</p> -->
                                            <p>Founds</p>
                                        </a>
                                    </li>
                                <?php } // Ver Reportes?>
                                <?php if ( $subApp->optSearch($permisos,'opt45')) { // Ver Reportes De movimientos de tarjetas nivel admin?>
                                    <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                        <a class="nav-link py-0" href="movements_cards_company?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                            <!-- <p>Movimientos tarjetas</p> -->
                                            <p>Card movements</p>
                                        </a>
                                    </li>
                                <?php } // Ver Reportes?>

                        <?php if ( $subApp->optSearch($permisos,'opt46')) { // Ver Reportes De Fondeos recibidos de parte del administrador (nivel empresa)?>
                            <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                <a class="nav-link py-0" href="report_funds_company?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                    <!-- <p>Fondeos Recibidos</p> -->
                                    <p>Founds received</p>
                                </a>
                            </li>
                        <?php } // Ver Reportes?>

                        <?php if ( $subApp->optSearch($permisos,'opt47')) { // Ver Reportes De Fondeos recibidos de parte del administrador (nivel empresa)?>
                            <li <?php if ( $SCRN == 66 ) { echo $active; } ?>>
                                <a class="nav-link py-0" href="report_employee?scr=55" onclick="checkSubmitBlock('btnsubmit');">
                                    <!-- <p>Empleados</p> -->
                                    <p>Employees</p>
                                </a>
                            </li>
                        <?php } // Ver Reportes?>

                        </ul>
                    </div>
                </li>

                <?php } ?>
                
                <?php if ( $subApp->optSearch($_SESSION[ 'PERMISOS' ],'opt19')) { // Cargar Facturas de cuenta maestra ?>  
                <li <?php if ( $SCRN == 18 ) { echo $active; } ?>>
                    <a href="loadfound_MA?scr=18">
                        <i class="pe-7s-upload"></i>
                        <!-- <p>Cargar Fondeo A CM</p> -->
                        <p>Load founds to CM</p>
                    </a>
                </li>
                <?php } ?>
                <?php if ( $subApp->optSearch($permisos,'opt18')) {  // perfiles?>          
                <li <?php if ( $SCRN == 12 ) { echo $active; } ?>  onclick="return checkSubmitBlock('crear_company');">
                        <a href="profiles?scr=12">
                            <i class="pe-7s-settings"></i>
                            <!-- <p>Perfiles</p> -->
                            <p>Profiles</p>
                        </a>
                </li>
                <?php }?>

                <?php if ( $subApp->optSearch($permisos,'opt60') OR $subApp->optSearch($permisos,'opt62')) { // Manuales ?>  
                <li <?php if ( $SCRN == 7 ) { echo $active; } ?>>
                    <a href="user_manual?scr=7">
                        <i class="pe-7s-map"></i>
                        <!-- <p>Manuales</p> -->
                        <p>Manuals</p>
                    </a>
                </li>
                <?php } ?>  

                <?php unset( $subApp );?>
               
                
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
                </div>
                <div class="collapse navbar-collapse">
                    
                    <ul class="nav navbar-nav navbar-right">
                        <li><a><b><?php echo $_SESSION['NCOMPANY']; ?></b></a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                <p>
                                    Profiles
                                    <b class="caret"></b>
                                <p>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="change_password">Change password</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="logout">
                                <p>Exit</p>
                            </a>
                        </li>
						<li class="separator hidden-lg"></li>
                    </ul>
                </div>
            </div>
        </nav>