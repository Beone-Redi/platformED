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

    include 'header.php';

    ?>

    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">  
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Cardholder / User Information </h4><br>
                            <!-- <h4 class="title">Información de tarjetahabiente/usuario </h4><br> -->
                            <p class="category">General view of cardholder / user information.</p>
                            <!-- <p class="category">Vista general de información de tarjetahabientes/usuario.</p> -->
                        </div>
                        <div class="content">
                            <form action="cards_view_users?scr=9" method="POST" onsubmit="return checkSubmitBlock('btnsubmit');">
                                
                                <div class="row">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Full name</label>
                                            <!-- <label>Nombre Completo</label> -->
                                            <input type="text" class="form-control" name="inputNombreCompleto" id="inputNombreCompleto" value="" >
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Cardholder</label>
                                            <!-- <label>Tarjeta</label> -->
                                            <input type="number" class="form-control" name="inputCard" id="inputCard" max="8" value="" >
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right"> Search </button>
                                <!-- <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right"> Buscar </button> -->
                                &nbsp;
                               
                                <div class="clearfix"></div>
                            </form>
                        </div>

                        <div class="content table-responsive table-full-width" id="datos"></div>
    
                        </div>
                </div>
            </div>
        </div>
    </div>
<?php

include 'footer.php';