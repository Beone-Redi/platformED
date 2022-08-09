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
$MSG=FALSE;
include "include/coredata.php";
$app = new app();
$perfiles=[];
include 'header.php';
$urlToken       = $app->urlToken();
if(isset($_POST["id_perfil"]))
{
    $string_opt='';
        for($i=1;$i<=70;$i++)
        {
            $id='opt'.str_pad($i, 2, '0', STR_PAD_LEFT);
            if(isset($_POST[$id])) // si el opt esta seleccionado
            {
                $string_opt.=$id;
            }
        }
        if($app->UpdateprofilebyId($string_opt,$_POST["id_perfil"]))
        {
            $MSG = 
            '<div class="alert alert-info">
                <span><b> Atenci&oacute;n</b>: Perfil editado de forma exitosa.
                </span>
            </div>';
        }
        else
        {
            $MSG = 
            '<div class="alert alert-danger">
                <span><b> Atenci&oacute;n </b>: Error:No se pudo editar el perfil intentelo nuevamente. 
                </span>
            </div>';
        }
}
if ( isset( $_GET['idu'] ) ) // si se activa o desactiva el perfil
{
   $data_perfil=$app->getProfilebyId($_GET['idu']);
   
}
else
{
    $data_perfil=[];
}

$opt01 = ($app->optSearch($data_perfil[2],'opt01') == 1) ? "checked" : "";
$opt02 = ($app->optSearch($data_perfil[2],'opt02') == 1) ? "checked" : "";
$opt03 = ($app->optSearch($data_perfil[2],'opt03') == 1) ? "checked" : "";
$opt04 = ($app->optSearch($data_perfil[2],'opt04') == 1) ? "checked" : "";
$opt05 = ($app->optSearch($data_perfil[2],'opt05') == 1) ? "checked" : "";
$opt06 = ($app->optSearch($data_perfil[2],'opt06') == 1) ? "checked" : "";
$opt07 = ($app->optSearch($data_perfil[2],'opt07') == 1) ? "checked" : "";
$opt08 = ($app->optSearch($data_perfil[2],'opt08') == 1) ? "checked" : "";
$opt09 = ($app->optSearch($data_perfil[2],'opt09') == 1) ? "checked" : "";
$opt10 = ($app->optSearch($data_perfil[2],'opt10') == 1) ? "checked" : "";
$opt11 = ($app->optSearch($data_perfil[2],'opt11') == 1) ? "checked" : "";
$opt12 = ($app->optSearch($data_perfil[2],'opt12') == 1) ? "checked" : "";
$opt13 = ($app->optSearch($data_perfil[2],'opt13') == 1) ? "checked" : "";
$opt14 = ($app->optSearch($data_perfil[2],'opt14') == 1) ? "checked" : "";
$opt15 = ($app->optSearch($data_perfil[2],'opt15') == 1) ? "checked" : "";
$opt16 = ($app->optSearch($data_perfil[2],'opt16') == 1) ? "checked" : "";
$opt17 = ($app->optSearch($data_perfil[2],'opt17') == 1) ? "checked" : "";
$opt18 = ($app->optSearch($data_perfil[2],'opt18') == 1) ? "checked" : "";
$opt19 = ($app->optSearch($data_perfil[2],'opt19') == 1) ? "checked" : "";
$opt20 = ($app->optSearch($data_perfil[2],'opt20') == 1) ? "checked" : "";
$opt21 = ($app->optSearch($data_perfil[2],'opt21') == 1) ? "checked" : "";
$opt22 = ($app->optSearch($data_perfil[2],'opt22') == 1) ? "checked" : "";
$opt23 = ($app->optSearch($data_perfil[2],'opt23') == 1) ? "checked" : "";
$opt24 = ($app->optSearch($data_perfil[2],'opt24') == 1) ? "checked" : "";
$opt25 = ($app->optSearch($data_perfil[2],'opt25') == 1) ? "checked" : "";
$opt26 = ($app->optSearch($data_perfil[2],'opt26') == 1) ? "checked" : "";
$opt27 = ($app->optSearch($data_perfil[2],'opt27') == 1) ? "checked" : "";
$opt28 = ($app->optSearch($data_perfil[2],'opt28') == 1) ? "checked" : "";
$opt29 = ($app->optSearch($data_perfil[2],'opt29') == 1) ? "checked" : "";
$opt30 = ($app->optSearch($data_perfil[2],'opt30') == 1) ? "checked" : "";
$opt31 = ($app->optSearch($data_perfil[2],'opt31') == 1) ? "checked" : "";
$opt32 = ($app->optSearch($data_perfil[2],'opt32') == 1) ? "checked" : "";
$opt33 = ($app->optSearch($data_perfil[2],'opt33') == 1) ? "checked" : "";
$opt34 = ($app->optSearch($data_perfil[2],'opt34') == 1) ? "checked" : "";
$opt35 = ($app->optSearch($data_perfil[2],'opt35') == 1) ? "checked" : "";
$opt36 = ($app->optSearch($data_perfil[2],'opt36') == 1) ? "checked" : "";
$opt37 = ($app->optSearch($data_perfil[2],'opt37') == 1) ? "checked" : "";
$opt38 = ($app->optSearch($data_perfil[2],'opt38') == 1) ? "checked" : "";
$opt39 = ($app->optSearch($data_perfil[2],'opt39') == 1) ? "checked" : "";
$opt40 = ($app->optSearch($data_perfil[2],'opt40') == 1) ? "checked" : "";
$opt41 = ($app->optSearch($data_perfil[2],'opt41') == 1) ? "checked" : "";
$opt42 = ($app->optSearch($data_perfil[2],'opt42') == 1) ? "checked" : "";
$opt43 = ($app->optSearch($data_perfil[2],'opt43') == 1) ? "checked" : "";
$opt44 = ($app->optSearch($data_perfil[2],'opt44') == 1) ? "checked" : "";
$opt45 = ($app->optSearch($data_perfil[2],'opt45') == 1) ? "checked" : "";
$opt46 = ($app->optSearch($data_perfil[2],'opt46') == 1) ? "checked" : "";
$opt47 = ($app->optSearch($data_perfil[2],'opt47') == 1) ? "checked" : "";
$opt48 = ($app->optSearch($data_perfil[2],'opt48') == 1) ? "checked" : "";
$opt49 = ($app->optSearch($data_perfil[2],'opt49') == 1) ? "checked" : "";
$opt50 = ($app->optSearch($data_perfil[2],'opt50') == 1) ? "checked" : "";
$opt51 = ($app->optSearch($data_perfil[2],'opt51') == 1) ? "checked" : "";
$opt52 = ($app->optSearch($data_perfil[2],'opt52') == 1) ? "checked" : "";
$opt53 = ($app->optSearch($data_perfil[2],'opt53') == 1) ? "checked" : "";
$opt54 = ($app->optSearch($data_perfil[2],'opt54') == 1) ? "checked" : "";
$opt55 = ($app->optSearch($data_perfil[2],'opt55') == 1) ? "checked" : "";
$opt56 = ($app->optSearch($data_perfil[2],'opt56') == 1) ? "checked" : "";
$opt57 = ($app->optSearch($data_perfil[2],'opt57') == 1) ? "checked" : "";
$opt58 = ($app->optSearch($data_perfil[2],'opt58') == 1) ? "checked" : "";
$opt59 = ($app->optSearch($data_perfil[2],'opt59') == 1) ? "checked" : "";
$opt60 = ($app->optSearch($data_perfil[2],'opt60') == 1) ? "checked" : "";
$opt61 = ($app->optSearch($data_perfil[2],'opt61') == 1) ? "checked" : "";
$opt62 = ($app->optSearch($data_perfil[2],'opt62') == 1) ? "checked" : "";
$opt63 = ($app->optSearch($data_perfil[2],'opt63') == 1) ? "checked" : "";
$opt64 = ($app->optSearch($data_perfil[2],'opt64') == 1) ? "checked" : "";
$opt65 = ($app->optSearch($data_perfil[2],'opt65') == 1) ? "checked" : "";
$opt66 = ($app->optSearch($data_perfil[2],'opt66') == 1) ? "checked" : "";
$opt67 = ($app->optSearch($data_perfil[2],'opt67') == 1) ? "checked" : "";
$opt68 = ($app->optSearch($data_perfil[2],'opt68') == 1) ? "checked" : "";
$opt69 = ($app->optSearch($data_perfil[2],'opt69') == 1) ? "checked" : "";
$opt70 = ($app->optSearch($data_perfil[2],'opt70') == 1) ? "checked" : "";


?>
    <?php  // colocar el opt al inicio del codigo 
    if ($MSG) { 
        echo $MSG;
     } ?>
     
    <div class="content">
    
        <div class="container-fluid">
          
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                        <div class="header">
                            <h4 class="title">Editar las opciones del perfil de usuario.</h4>
                            <p class="category">Aqu&iacute; se puede editar un perfil del aplicativo.</p>
                        </div>
                        <div class="content">
                            <div class="container-fluid">
                                <form action="edit_profiles?scr=12&idu=<?php echo $data_perfil[0]; ?>" method="POST">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Nombre del Perfil</label>
                                                <input type="text" class="form-control" name="nombre" disabled value="<?php echo $data_perfil[1];?>" required="">
                                                <input type="hidden" value="<?php echo $data_perfil[0];?>" name="id_perfil">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nivel Perfil</label>
                                                <input type="number" class="form-control" name="nivel" disabled value="<?php echo $data_perfil[3];?>" max="999">
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Dashboard</h3>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt01" value="opt01"  <?php echo $opt01; ?>> monto global (Cuenta maestra)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt04" value="opt04"  <?php echo $opt04; ?>> Transaciones del mes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt02" value="opt02"  <?php echo $opt02; ?>> Dashboard admin</label>
                                            </div>        

                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt05" value="opt05"  <?php echo $opt05; ?>> Bitacora administrador</label>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt03" value="opt03"  <?php echo $opt03; ?>>Dashboard empresa</label>
                                      
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt06" value="opt06"  <?php echo $opt06; ?>> Bitacora empresa</label>
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Menus</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt10" value="opt10"  <?php echo $opt10; ?>>Menu empresas(admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt14" value="opt14"  <?php echo $opt14; ?>>Menu tarjetas habientes(Nivel empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt18" value="opt18"  <?php echo $opt18; ?>>Menu Perfiles</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt67" value="opt67"  <?php echo $opt67; ?>>Menu Ver tarjetahabientes/user</label>
                                            </div>                                             
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt11" value="opt11"  <?php echo $opt11; ?>>Menu Fondear empresas(admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt15" value="opt15"  <?php echo $opt15; ?>>Menu Fondear tarjetas (Empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt19" value="opt19" <?php echo $opt19; ?>>Cargar fondeos a cuenta maestra</label>
                                            </div>
                                  
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt12" value="opt12"  <?php echo $opt12; ?>>Menu Reportes admin</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt16" value="opt16"  <?php echo $opt16; ?>>Menu Reportes empresa</label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt13" value="opt13"  <?php echo $opt13; ?>>Menu Usuarios admin</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt17" value="opt17"  <?php echo $opt17; ?>>Menu Usuarios empresa</label>
                                            </div>
                                        </div>
                                    </div>

                                    <h3>Opciones del Perfil</h3>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt21" value="opt21"  <?php echo $opt21; ?>>Crear empresas(Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt24" value="opt24"  <?php echo $opt24; ?>>Crear usuarios (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt66" value="opt66"  <?php echo $opt66; ?>>Reasignar tarjeta (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt27" value="opt27"  <?php echo $opt27; ?>>Editar  tarjetahabientes (Nivel Empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt30" value="opt30"  <?php echo $opt30; ?>>Crear Usuarios(Nivel Empresa)</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt22" value="opt22"  <?php echo $opt22; ?>>Editar empresas (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt25" value="opt25"  <?php echo $opt25; ?>>Editar usuarios (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt28" value="opt28"  <?php echo $opt28; ?>>Fondeo Tarjetas Individualmente (Nivel empresa) </label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt31" value="opt31"  <?php echo $opt31; ?>>Editar Usuarios(Nivel Empresa)</label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt23" value="opt23"  <?php echo $opt23; ?>>Fondeo empresas (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt68" value="opt68"  <?php echo $opt68; ?>>Crear tarjetahabientes(Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt26" value="opt26"  <?php echo $opt26; ?>>Crear tarjetahabientes(Nivel empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt29" value="opt29"  <?php echo $opt29; ?>>Fondeo Tarjetas por layout (Nivel empresa) </label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt64" value="opt64"  <?php echo $opt64; ?>> Ver Tarjeta/Usuario (nivel admin)</label>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <h3>Reportes</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt40" value="opt40"  <?php echo $opt40; ?>> Reporte fondeo (nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt44" value="opt44"  <?php echo $opt44; ?>> Reporte Fondeo(nivel empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt48" value="opt48"  <?php echo $opt49; ?>> Reporte Paynet(nivel admin)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt41" value="opt41"  <?php echo $opt41; ?>> Reporte movimientos tarjetas admin</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt45" value="opt45"  <?php echo $opt45; ?>> Reporte movimientos tarjetas empresa</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt49" value="opt49"  <?php echo $opt49; ?>> Reporte Paynet(nivel empresa)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt42" value="opt42"  <?php echo $opt42; ?>> Fondeo a empresas</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt46" value="opt46"  <?php echo $opt46; ?>> Fondeo recibidos empresas</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt50" value="opt50"  <?php echo $opt50; ?>> Fondeos en cuenta maestra</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt43" value="opt43"  <?php echo $opt43; ?>> Reporte empleado de empresas</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt47" value="opt47"  <?php echo $opt47; ?>> Reporte empleado por empresa</label>
                                            </div>

                                          
                                        
                                        </div>
                                    </div>
                                  
                                   
                                    <h3>Otros</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt60" value="opt60" <?php echo $opt60; ?>> Manuales admin </label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt62" value="opt62" <?php echo $opt62; ?>> Manuales empresas</label>
                                            </div>

                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt61" value="opt61" <?php echo $opt61; ?>> Crear perfiles</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt63" value="opt63" <?php echo $opt63; ?>> Editar perfiles</label>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    
                                    <button type = "submit" class = "btn btn-info btn-fill pull-right">Editar perfil</button>
                                    <a href="profiles?scr=12" class="btn btn-succesful btn-fill">Regresar</a>
                                    <div class = "clearfix"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    <?php
  include 'footer.php';