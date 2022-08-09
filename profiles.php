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
if(isset($_POST["nombre"]) AND isset($_POST["nivel"]))
{
    $string_opt='';
    if($app->getnameProfile($_POST["nombre"])==='')
    {
        for($i=1;$i<=60;$i++)
        {
            $id='opt'.str_pad($i, 2, '0', STR_PAD_LEFT);
            if(isset($_POST[$id])) // si el opt esta seleccionado
            {
                $string_opt.=$id;
            }
        }
        $data=[$_POST["nombre"],$string_opt,$_POST["nivel"],"ACTIVE"];
        if($app->new_Profile($data))
        {
            $MSG = 
            '<div class="alert alert-info">
                <span><b> Atenci&oacute;n</b>: Perfil creado de forma exitosa.
                </span>
            </div>';
        }
        else
        {
            $MSG = 
            '<div class="alert alert-danger">
                <span><b> Atenci&oacute;n </b>: Error:No se pudo crear el perfil intentelo nuevamente. 
                </span>
            </div>';
        }

    }
    else
    {
        $MSG = 
                '<div class="alert alert-danger">
                    <span><b> Atenci&oacute;n </b>: Error:El nombre de perfil ya existe. 
                    </span>
                </div>';
    }
}
elseif ( isset( $_GET['urlToken'] ) && isset( $_SESSION['urlToken'] ) && isset( $_GET['idu'] ) && isset( $_GET['sta'] ) ) // si se activa o desactiva el perfil
{
    if ( $_SESSION['urlToken'] === $_GET['urlToken'] )
    {
        $app->updateStatusProfile( $_GET['sta'],$_GET['idu']);
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
$perfiles=$app->getAllProfiles();

?>
    <?php 
    if ($MSG) { 
        echo $MSG;
     } ?>
    <div class="content">
    
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    
                    <div class="card">
                        <div class="header">
                            <h4 class="title">Administrador de Perfiles</h4>
                            <p class="category">Aquí es posible administrar los perfiles de la APP.</p>
                        </div>
                        <div class="content table-responsive ">
                            <table class="table table-bordered" id="myTable" class="display" />
                            <thead>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Estatus</th>
                            <th>Opciones</th>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i <= count($perfiles); $i += 5) {
                                    if (isset($perfiles[$i])) {
                                        ?>
                                        <tr>
                                            <td><?php echo $perfiles[$i]; ?></td>
                                            <td><?php echo utf8_encode($perfiles[$i + 1]); ?></td>
                                            <td><?php echo $perfiles[$i + 4]; ?></td>
                                            <td>
                                                <?php 
                                                if( $perfiles[$i + 4] === 'ACTIVE' )
                                                {
                                                ?>
                                                <a href="profiles?scr=12&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $perfiles[$i]; ?>&sta=<?php echo $perfiles[$i + 4]; ?>" rel="tooltip" title="Inactive" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-close-circle" style="font-size:18px;"></i>
                                                </a>
                                        <?php
                                            } 
                                            else
                                            {
                                        ?>
                                                <a href="profiles?scr=12&urlToken=<?php echo $urlToken; ?>&idu=<?php echo $perfiles[$i]; ?>&sta=<?php echo $perfiles[$i + 4]; ?>" rel="tooltip" title="Active" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-refresh" style="font-size:18px;"></i>
                                                </a>
                                        <?php
                                            }
                                        ?>
                                                <a href="edit_profiles?scr=12&idu=<?php echo $perfiles[$i]; ?>" rel="tooltip" title="Update" class="btn btn-default btn-sm">
                                                    <i class="pe-7s-search" style="font-size:18px;"></i>
                                                </a>

                                    </td>
                                            
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
            <div class="row">
                <div class="col-md-12">
                <div class="card">
                        <div class="header">
                            <h4 class="title">Crear un nuevo perfil de usuario.</h4>
                            <p class="category">Aqu&iacute; se puede dar de alta un perfil que utilizaran el aplicativo.</p>
                        </div>
                        <div class="content">
                            <div class="container-fluid">
                                <form action="profiles?src=12" method="POST">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Nombre del Perfil</label>
                                                <input type="text" class="form-control" name="nombre" placeholder="" value="" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Nivel Perfil</label>
                                                <input type="number" class="form-control" name="nivel" placeholder="" value="" required="" min="1" max="999">
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Dashboard</h3>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt01" value="opt01"> monto global (Cuenta maestra)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt04" value="opt04"> Transaciones del mes</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt02" value="opt02"> Dashboard admin</label>
                                            </div>        

                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt05" value="opt05"> Bitacora administrador</label>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt03" value="opt03">Dashboard empresa</label>
                                      
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt06" value="opt06"> Bitacora empresa</label>
                                            </div>
                                        </div>
                                    </div>
                                    <h3>Menus</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt10" value="opt10" >Menu empresas(admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt14" value="opt14" >Menu tarjetas habientes(Nivel empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt18" value="opt18" >Menu perfiles</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt67" value="opt67" >Menu ver tarjetahabientes/user</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt11" value="opt11" >Menu Fondear empresas(admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt15" value="opt15" >Menu Fondear tarjetas (Empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt19" value="opt19" >Cargar fondeos a cuenta maestra</label>
                                            </div> 
                                  
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt12" value="opt12" >Menu Reportes admin</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt16" value="opt16" >Menu Reportes empresa</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt13" value="opt13" >Menu Usuarios admin</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt17" value="opt17" >Menu Usuarios empresa</label>
                                            </div>
                                        </div>
                                    </div>

                                    <h3>Opciones del Perfil</h3>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt21" value="opt21" >Crear empresas(Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt24" value="opt24" >Crear usuarios (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt66" value="opt66" >Reasignar tarjeta (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt27" value="opt27" >Editar  tarjetahabientes (Nivel Empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt30" value="opt30" >Crear Usuarios(Nivel Empresa)</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt22" value="opt22" >Editar empresas (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt25" value="opt25" >Editar usuarios (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt28" value="opt28" >Fondeo Tarjetas Individualmente (Nivel empresa) </label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt31" value="opt31" >Editar Usuarios(Nivel Empresa)</label>
                                            </div>
                                            
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt23" value="opt23" >Fondeo empresas (Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt68" value="opt68" >Crear tarjetahabientes(Nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt26" value="opt26" >Crear tarjetahabientes(Nivel empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt29" value="opt29" >Fondeo Tarjetas por layout (Nivel empresa) </label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt64" value="opt64"> Ver Tarjeta/Usuario (nivel admin)</label>
                                            </div>
                                        </div>
                                    </div>
                                   
                                    <h3>Reportes</h3>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt40" value="opt40"> Reporte fondeo (nivel admin)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt44" value="opt44"> Reporte Fondeo(nivel empresa)</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt48" value="opt48"> Reporte Paynet (nivel admin)</label>
                                            </div>
                                              

                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt41" value="opt41"> Reporte movimientos tarjetas admin</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt45" value="opt45"> Reporte movimientos tarjetas empresa</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt49" value="opt49"> Reporte Paynet(nivel empresa)</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt42" value="opt42"> Fondeo a empresas</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt46" value="opt46"> Fondeos recibidos empresas</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt50" value="opt50"> Fondeos en cuenta maestra</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt43" value="opt43"> Reporte empleados de empresas</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt47" value="opt47"> Reporte empleados por empresa</label>
                                            </div>
                                        </div>
                                    </div>
                                  
                                   
                                    <h3>Otros</h3>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">    
                                                <label><input type="checkbox" name="opt60" value="opt60"> Manuales admin </label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt62" value="opt62"> Manuales empresas</label>
                                            </div>

                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt61" value="opt61"> Crear perfiles</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input type="checkbox" name="opt63" value="opt63"> Editar perfiles</label>
                                            </div>
                                        </div>
                                    </div>
                                  
                                    
                                    <button type = "submit" class = "btn btn-info btn-fill pull-right">Crear perfil</button>
                                   
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