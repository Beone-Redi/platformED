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
$msg=FALSE;
include "include/coredata.php";
$app = new app();

include 'header.php';
if(isset($_POST["clave_anterior"]) AND isset($_POST["pass1"]))
{
    if($_POST["pass1"]===$_POST["pass2"])
    {
        if(($_POST["clave_anterior"]==$_POST["passanterior"]))
        {
            $res=$app->updatePassword($_SESSION['USER'],$_POST["pass1"]);
            if($res)
            {
                $msg = 'Hecho!';
                $Resultado='00PASSWORD CHANGED SUCCESSFUL';
            }
            else
            {
                $msg = 'Error';
                $Resultado = '01IT IS NOT POSSIBLE CHANGED PASSWORD NOW, TRY LATER.';
            }
            
        }
        else
        {
            $msg = 'Error';
            $Resultado='01THE PREVIOUS PASSWORD DOES NOT MACHT';
        }
    }
    else
    {
        $msg = 'Error';
        $Resultado='01THE NEW PASSWORD DOES NOT MACHT';
    }
}
$data=$app->viewDataUser($_SESSION['USER']);
?>

<div class="content">
            <div class="container-fluid">
                <div class="row">
                <?php if ( strlen( $msg ) == 6 ) { ?> 
                        <div class="alert alert-success">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> successfull - </b> <?php echo substr($Resultado,2);?>.</span>
                        </div>
                    <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                        <div class="alert alert-danger">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Error - </b> <?php echo substr($Resultado,2);?>.</span>
                        </div>
                    <?php } ?>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Update Password</h4>
                            </div>
                            <div class="content">
                                <form method="POST" action="option" onsubmit="return checkSubmit('btsubmit');">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>current Password</label>
                                                <input type="password" class="form-control" name="clave_anterior" required>
                                                <input type="hidden" name="passanterior" value="<?php echo $data[2];?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>New Password</label>
                                                <input type="password" pattern=".{8,20}" title="8 to 20 characters" required
                                                 max="50" class="form-control" name="pass1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Repeat password</label>
                                                <input type="password" pattern=".{8,20}" title="8 to 20 characters" class="form-control" name="pass2" required>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right">Update</button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

<?php
include 'footer.php';