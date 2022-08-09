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


$msg = '';
include 'header.php';
if ( isset($_POST["razonsocial"]) && isset($_POST["contacto"]))
{

    $inputestado    =  $_POST['estado'] ;
    $datacompany=[utf8_decode($_POST["razonsocial"]),utf8_decode($_POST["contacto"]),$_POST["telefono"],utf8_decode($_POST["direccion"]),utf8_decode($_POST["ciudad"]),$_POST["cp"],utf8_decode($_POST["observaciones"]),utf8_decode($_POST["estado"]),$_POST["comision"],$_POST["mail_notificaciones"],$_GET["idu"]];
    $datauser=[utf8_decode($_POST["contacto"]),utf8_decode($_POST["direccion"]),utf8_decode($_POST["ciudad"]),$_POST["cp"],utf8_decode($_POST["observaciones"]),$_POST["telefono"],utf8_decode($_POST["estado"]),$_POST["correo"]];
    if ( $app->updateDataCompany($datacompany) AND $app->updateUserCompany($datauser))
    {
        $msg = 'Hecho!';
    }
    else 
    {
        $msg = 'Error';
    }    
    
}
elseif(isset($_POST["comision"]))
{
    echo "modulo en creacion";
}

$dEmpresas = $app->viewCompany($_GET['idu']);
$states=$app->GetAllState();

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
        <?php if ( strlen( $msg ) == 6 ) { ?> 
            <div class="alert alert-success">
                <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Successful - </b> It was updated successfully.</span>
            </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
            <div class="alert alert-danger">
                <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> Could not update please check the data and try later.</span>
            </div>
        <?php } ?>
            <div class="col-md-8">
                <div class="card">
                    <div class="header">
                        <h4 class="title">View Company</h4>
                    </div>
                    <div class="content">
                        <form action="view_company?scr=2&idu=<?php echo $_GET['idu'];?>" method="POST" onsubmit="return checkSubmit('btsubmit');">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label>Empresa</label> -->
                                        <label>Company</label>
                                        <input type="text" class="form-control" disabled placeholder="" value="<?php echo utf8_encode($dEmpresas[0]); ?>">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <!-- <label for="exampleInputEmail1">Razón social</label> -->
                                        <label for="exampleInputEmail1">Business name</label>
                                        <input type="text" class="form-control" placeholder="" name="razonsocial" value="<?php echo utf8_encode($dEmpresas[1]); ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Contacto</label>
                                        <label>Contact</label>
                                        <input type="text" class="form-control" placeholder="" name="contacto" value="<?php echo utf8_encode($dEmpresas[2]); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="exampleInputEmail1">Email</label> -->
                                        <label for="exampleInputEmail1">E-mail</label>
                                        <input type="email" class="form-control" placeholder="" disabled value="<?php echo $dEmpresas[3]; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label for="exampleInputEmail1">Telefono</label> -->
                                        <label for="exampleInputEmail1">Telephone</label>
                                        <input type="number" min="2400000000" max="9999999999" class="form-control" placeholder="" name="telefono" value="<?php echo $dEmpresas[4]; ?>" maxlength="10" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label>Dirrección</label> -->
                                        <label>Address</label>
                                        <input type="text" class="form-control" placeholder="" name="direccion" value="<?php echo utf8_encode($dEmpresas[5]); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                
                                                            
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label>Estado</label> -->
                                        <label>State</label>
                                        <select class="form-control" name="estado" required onChange="getCiudades(this.value)">
                                            <option value="<?php echo utf8_encode($dEmpresas[14]);?>"><?php echo utf8_encode($dEmpresas[14]);?></option>
                                        <?php for($i=0;$i<sizeof($states);$i++)
                                        {?>
                                            <option value="<?php echo utf8_encode($states[$i]); ?>"><?php echo utf8_encode($states[$i]);?></option>
                                        <?php
                                        }?>
                                        </select>
                                         
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label>Ciudad</label> -->
                                        <label>City</label>
                                        <select class="form-control" name="ciudad" id="ciudades" required="">
                                            <option value="<?php echo utf8_encode($dEmpresas[6]); ?>"><?php echo utf8_encode($dEmpresas[6]); ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <!-- <label>Código Postal</label> -->
                                        <label>Postal Code</label>
                                        <input type="number" min="10000" max="99998" class="form-control" placeholder="" name="cp" value="<?php echo $dEmpresas[7]; ?>" maxlength="5" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <?php if($dEmpresas[15]=='')
                                {
                                    
                                ?>
                               <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="inputCodigoPostal">Commission to collect</label>
                                        <input type="number" min="0" max="100" class="form-control" placeholder="0.0" name="comision" step=".1" value="" maxlength="3" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    </div>
                                </div>
                                <?php           
                                }
                                else
                                {
                                    
                                ?>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="inputCodigoPostal">Commission to collect</label>
                                        <input type="number" class="form-control" value="<?php echo $dEmpresas[15];?>" disabled>
                                        <input type="hidden" value="<?php echo $dEmpresas[15];?>" name="comision">
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                                <div class="col-md-5">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">KeyCompany</label>
                                        <input type="text" disabled class="form-control" value="<?php echo $dEmpresas[16]; ?>" maxlength="10" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <?php if($dEmpresas[17]=='')
                                {
                                    
                                ?>
                               <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="inputCodigoPostal">E-mail Notifications</label>
                                        <input type="email" class="form-control" name="mail_notificaciones">
                                    </div>
                                </div>
                                <?php           
                                }
                                else
                                {
                                    
                                ?>
                                <div class="col-md-7">
                                    <div class="form-group">
                                        <label for="inputCodigoPostal">Email Notifications</label>
                                        <input type="email" class="form-control" value="<?php echo $dEmpresas[17];?>" disabled>
                                        <input type="hidden" value="<?php echo $dEmpresas[17];?>" name="mail_notificaciones">
                                    </div>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Observations</label>
                                        <textarea rows="5" class="form-control" name="observaciones" placeholder="" ><?php echo utf8_encode($dEmpresas[8]); ?></textarea>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" class="form-control" name="correo" value="<?php echo $dEmpresas[3]; ?>">
                                   
                            <input id="btsubmit" type = "submit" class = "btn btn-info btn-fill pull-right" value="Update profile" />
                            <a href="companys?scr=2" class="btn btn-default btn-sm" >
                                <i class="pe-7s-back" style="font-size:16px;"></i> Return
                            </a>
                            <div class="clearfix"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="image">
                        <img src="assets/img/<?php echo $dEmpresas[9]; ?>" alt="..."/>
                    </div>
                    <div class="content">
                        <p class="text-center">
                            <br>Funds<br>
                            <span style='font-size:32px;'><?php echo '$ ' . $dEmpresas[11]; ?></span>

                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
include 'footer.php';