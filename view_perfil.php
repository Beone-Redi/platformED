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
$msg2='';

if ( isset($_POST['inputNombreCompleto']) )
{
    $inputNombreCompleto    = $app->validoInput( $_POST['inputNombreCompleto'] );
    $inputEmail             = '';
    $inputTel               = $app->validoInput( $_POST['inputTelefono'] );
    $inputDireccion         = $app->validoInput( $_POST['inputDireccion'] );
    $inputCodigoPostal      = $app->validoInput( $_POST['inputCodigoPostal'] );
    $inputCiudad            = $app->validoInput( $_POST['inputCiudad'] );
    $inputPerfil            = $app->validoInput( $_POST['inputPerfil'] );
    $inputObservaciones     = $app->validoInput( $_POST['inputObservaciones'] );
    $inputEstado            = $app->validoInput( $_POST['inputEstado'] );
    $update                 = date( 'Y-m-d H:i:s' );

    $aCompany =
    [
        utf8_decode($inputNombreCompleto),   // 0
        $inputEmail,            // 1
        $inputTel,              // 2
        utf8_decode($inputDireccion),        // 3
        $inputCodigoPostal,     // 4
        utf8_decode($inputCiudad),           // 5
        $inputPerfil,           // 6
        utf8_decode($inputObservaciones),    // 7
        utf8_decode($inputEstado),           // 8
        $update,                // 9
        $_GET['idu']            // 10
    ];

    if ( $app->updateUser( $aCompany ) )
    {
        $msg = 'Hecho!';
    }
    else 
    {
        $msg = 'Error';
    }
}

elseif(isset($_POST['inputCard']))
{
    $ComisionAdmin=$app->GetComisionCompanyByIde($_SESSION['EMPRESA']);
        $comisionCompany=0.00;
        $ivaComisionCompany=0.00;
    $dUsuario = $app->viewUser( $_GET['idu'] );
    $IdUserAPI=$app->IdAPIByUser($_GET['idu']);// Obtiene el idapi y password
    $IdCard=$_POST['inputCard'];
    $Month=$_POST['inputMes'];
    $Anio=$_POST['inputAno'];
    $Company=$_SESSION['EMPRESA'];
    $Usuario=$dUsuario[1];
    $City=utf8_decode($dUsuario[5]);
    $nombreEmpleado=utf8_decode($dUsuario[3]);
    $dataComision=[$ComisionAdmin[0],$ComisionAdmin[1],0.00,0.00];//Comisienadmin,/IvAcomisionAdmin,
    
    if($IdUserAPI[0]==='')// si no se tiene el idapi
    {
        $Res=$app->LoginUserAPI($Usuario,$IdUserAPI[1]); // obtiene el ide de user y lo inserta en la tabla
        if(substr($Res,0,2)<>'01')
        {
            $IdUserAPI=$app->IdAPIByUser($_GET['idu']);// Obtiene el idapi y password
            $Data=[$IdUserAPI[0],$IdCard,$Month,$Anio,$Company,$Usuario,$City,$nombreEmpleado];//IdUSerAPI,Idcard,mes,anio,compañia,usuario,ciudad
            $Resultado=$app->NewCard( $Data,$dataComision );
        }
        else
        {
            $Resultado='01NO SE PUEDE REGISTRAR LA TARJETA AL USUARIO,INTENTELO MAS TARDE';
        }
    }
    else
    {
        $Data=[$IdUserAPI[0],$IdCard,$Month,$Anio,$Company,$Usuario,$City];//IdUSerAPI,Idcard,mes,anio,compañia,usuario,ciudad
        $Resultado=$app->NewCard( $Data );
    }
    if(substr( $Resultado, 0, 2 )== '00') // si se creo correctamente el usuario
    {
        $msg2 = 'Hecho!';
    }
    else
    {
        $msg2 = 'Error';
    }
    
}

$meses=['01',1,'02',2,'03',3,'04',4,'05',5,'06',6,'07',7,'08',8,'09',9,'10',10,'11',11,'12',12];
$anio_actual=date('y');
$anios=[$anio_actual,($anio_actual+1),($anio_actual+2),($anio_actual+3),($anio_actual+4),($anio_actual+5)];

$states=$app->GetAllState();
$dUsuario = $app->viewUser( $_GET['idu'] );
$dEmpresas = $app->viewCompany($_SESSION['EMPRESA']);
$CardsByUser=$app->viewCardsByUsers($_SESSION['EMPRESA'],$dUsuario[1]);


include 'header.php';

?>

<div class="content">
            <div class="container-fluid">
                <div class="row">

                    <?php if ( strlen( $msg ) == 6 ) { ?> 
                        <div class="alert alert-success">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Successful - </b> It was successfully updated.</span>
                        </div>
                    <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                        <div class="alert alert-danger">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Error - </b> Could not update check the data and try later.</span>
                        </div>
                    <?php } ?>
                    <?php if ( strlen( $msg2 ) == 6 ) { ?> 
                        <div class="alert alert-success">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Successful - </b> The card was registered successfully.</span>
                        </div>
                    <?php } elseif ( strlen( $msg2 ) == 5 ) { ?>
                        <div class="alert alert-danger">
                            <button type="button" aria-hidden="true" class="close">×</button>
                            <span><b> Error - </b> <?php echo substr($Resultado,2);?></span>
                        </div>
                    <?php } ?>

                    <div class="col-md-8">  
                        <div class="card">
                            <div class="header">
                                <h4 class="title">Cardholder data</h4>
                            </div>
                            <div class="content">
                                <form method="POST" action="view_perfil?scr=9&idu=<?php echo $_GET['idu']; ?>" id="form1"  onsubmit="return checkSubmitBlock('btsubmit');">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label>Company</label>
                                                <input type="text" class="form-control" disabled value="<?php echo strtoupper(utf8_encode($dEmpresas[0])); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Full name</label>
                                                <input type="text" class="form-control" name="inputNombreCompleto" value="<?php echo utf8_encode($dUsuario[2]); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1">E-mail</label>
                                                <input type="email" disabled class="form-control" name="inputEmail" value="<?php echo $dUsuario[1]; ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="inputTel">Telephone</label>
                                                <input type="number" min="2400000000" max="9999999999" class="form-control" name="inputTelefono" value="<?php echo $dUsuario[9]; ?>" maxlength="10" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Address</label>
                                                <input type="text" class="form-control" name="inputDireccion" value="<?php echo utf8_encode($dUsuario[3]); ?>" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>State</label>
                                                <select class="form-control" name="inputEstado" required onChange="getCiudades(this.value)">
                                                    <option value="<?php echo utf8_encode($dUsuario[10]);?>"><?php echo utf8_encode($dUsuario[10]);?></option>
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
                                                <label>City</label>
                                                <select class="form-control" name="inputCiudad" id="ciudades" required="">
                                                    <option value="<?php echo utf8_encode($dUsuario[4]); ?>"><?php echo utf8_encode($dUsuario[4]); ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Postal Code</label>
                                                <input type="number" min="10000" max="99998" class="form-control" name="inputCodigoPostal" value="<?php echo $dUsuario[5]; ?>" maxlength="5" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="inputPerfil">Profile</label>
                                                <select class="form-control" name="inputPerfil">
                                                    <option value="EMPLEADO">Empleado</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Observaciones</label>
                                                <textarea rows="5" class="form-control" name="inputObservaciones" placeholder="" ><?php echo utf8_encode($dUsuario[6]); ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" id="btsubmit" class="btn btn-info btn-fill pull-right" form="form1"> Update profile</button>
                                    &nbsp;
                                    <a href="clients?scr=9" class="btn btn-default btn-sm" >
                                        <i class="pe-7s-back" style="font-size:16px;"></i> Return
                                    </a>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                        <?php if(sizeof($CardsByUser)==0){?>
                        <div class="card">
                            <div class="header">
                                <h4 class="title">New card registration</h4>
                            </div>
                            <div class="content">
                                <form method="POST" action="view_perfil?scr=9&idu=<?php echo $_GET['idu']; ?>" id="form2" onsubmit="return checkSubmitBlock('btsubmit2');">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label for="inputCard">Card</label>
                                            <input type="number" minlength="16" maxlength="16" min="4111111111111111" max="5999999999999999" class="form-control" name="inputCard" value="" required oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="inputMes">Month</label>
                                            <select class="form-control" name="inputMes" required />
                                                <?php for($i=0;$i<sizeof($meses);$i+=2)
                                                {?>
                                                <option value="<?php echo $meses[$i];?>"><?php echo $meses[$i+1];?></option>
                                                <?php
                                                }?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="inputAno">Year</label>
                                            <select class="form-control" name="inputAno" required />
                                                <?php for($i=0;$i<sizeof($anios);$i++)
                                                {?>
                                                <option value="<?php echo $anios[$i];?>"><?php echo $anios[$i];?></option>
                                                <?php
                                            }?>
                                            </select>
                                        </div>
                               
                                    </div>
                                    <br><br>
                                    <button type="submit" id="btsubmit2" class="btn btn-info btn-fill pull-lefth" form="form2">Save</button>
                                    <div class="clearfix"></div>
                                </form>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-user">
                            <div class="image">
                                <img src="./assets/img/<?php echo $dEmpresas[9]; ?>" alt="..."/>
                            </div>
                            <div class="content">
                                <div class="author">
                                    <a href="#">
                                        <img class="avatar border-gray" src="<?php echo $dUsuario[7]; ?>" alt="..."/>
                                        <h4 class="title"><?php echo utf8_encode(strtoupper($dUsuario[2])); ?><br />
                                            <small><?php echo strtoupper($dUsuario[8]); ?></small>
                                            <br>
                                            
                                        </h4>
                                        <h5>
                                        <?php if(sizeof($CardsByUser)>0)
                                        {
                                        ?>
                                        <br>
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; CARD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BALANCE
                                        <br>
                                        <br>
                                        <?php 
                                            for($j=0;$j<sizeof($CardsByUser);$j+=2)
                                            {
                                                $saldo=$app->check_amountCard($CardsByUser[$j+1]);
                                                echo '****-****-'.substr($CardsByUser[$j+1],0,4).'-'.substr($CardsByUser[$j+1],-4).'&nbsp;&nbsp;&nbsp;&nbsp;'.number_format($saldo, 2, '.', ',').'<br>';
                                            }
                                            ?>
                                        <?php 
                                        }
                                        ?>
                                        </h5>
                                        
                                    </a>
                                   
                                </div>
                                <p class="description text-center"></p>
                            </div>

                            <br><br>
                           
                        </div>
                    </div>

                </div>
            </div>
        </div>

<?php
include 'footer.php';