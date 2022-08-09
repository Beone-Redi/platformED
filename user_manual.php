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
                        <h4 class="title">User manual and references</h4>
                            <p class="category">Overview of the tool usage manuals.</p>
                                <br/>
                                
                                <!--<div id="exTab2" class="container">-->
                                <div class="row">
                                    <div class="col-md-12">
                                
                                        <ul class="nav nav-tabs">
                                        
			                                <li>
                                                <a href="#1" data-toggle="tab" ><i class="pe-7s-plus" style="font-size:16px;"></i> New Companies</a>
                                            </li>
                                            <li>
                                                <a href="#2" data-toggle="tab"><i class="pe-7s-plus" style="font-size:16px;"></i> New Employees</a>
                                            </li>
                                            <li>
                                                <a href="#3" data-toggle="tab"><i class="pe-7s-plus" style="font-size:16px;"></i> Founding Companys</a>
                                            </li>
                                            <li>
                                                <a href="#4" data-toggle="tab"><i class="pe-7s-plus" style="font-size:16px;"></i> Founding Employee</a>
                                            </li>
                                            <li>
                                                <a href="#5" data-toggle="tab"><i class="pe-7s-plus" style="font-size:16px;"></i> Report Admin</a>
                                            </li>
                                            <li>
                                                <a href="#6" data-toggle="tab"><i class="pe-7s-plus" style="font-size:16px;"></i> Report Companies</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                    
			                        <div class="tab-content ">
                                        <div class="tab-pane" id="1" >
                                            <video width="520" height="440" controls>
                                                <source src="./downloads/crearusuarios.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="tab-pane" id="2">
                                            <video width="520" height="440" controls>
                                                <source src="./downloads/crearempresas.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="tab-pane" id="3">
                                            <video width="520" height="440" controls>
                                                <source src="./downloads/fondearempresas.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="tab-pane" id="4">
                                            <video width="520" height="440" controls>
                                                <source src="./downloads/fondeartarjetas.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="tab-pane" id="5">
                                            <video width="520" height="440" controls>
                                                <source src="./downloads/reporteadmin.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="tab-pane" id="6">
                                            <video width="520" height="440" controls>
                                                <source src="./downloads/reportempresas.mp4" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
			                        </div>
                                <!--</div>-->
                                       
                            </p>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>
<?php
include 'footer.php';