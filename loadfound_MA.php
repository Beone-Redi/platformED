<?php 
if (!isset($_SESSION)) {
    session_start();
}

if (isset($_SESSION['ACTIVO']) <> "2019") {
    header("Location: login");
}

include "include/coredata.php";
$app = new app();
include_once 'email.php';
$email= new sendmail();
include_once 'msjs_mails.php';
$msjs_mails=new msjs_mails;        

$msg = '';

if ( $_SERVER[ "REQUEST_METHOD" ] == "POST" )
{
    $file_url='';
    $bandera=0; // el perfil por default es empleado
    if ( isset( $_FILES[ 'inputImagenPerfil' ][ 'name' ]) && !empty( $_FILES[ 'inputImagenPerfil' ][ 'name' ]) ) // si se carga el archivo
    {
        $fileTmpPath1   = $_FILES['inputImagenPerfil']['tmp_name'];
        $fileName1      = $_FILES['inputImagenPerfil']['name'];
        $extension_file1 = strtolower(substr($fileName1, -4));
        $info = new SplFileInfo($_FILES[ 'inputImagenPerfil' ][ 'name' ]);
        $extension=$info->getExtension();
        $uFile      = 'downloads/CCM_'.date('YmdHis').'.'.$extension;
        
        if($extension_file1=='.png' OR $extension_file1=='.jpg' OR $extension_file1=='jpeg' OR $extension_file1=='.pdf')
        {
            if ( move_uploaded_file( $_FILES[ 'inputImagenPerfil' ][ 'tmp_name' ], $uFile ) ) // si se guardo en la ruta la imagen de perfil
            {
                $file_url = $uFile;
            }
            else
            {
                $Resultado='01NO SE GUARDO EL ARCHIVO';
                $bandera=1;       
            }
        }
        else 
        {
            $Resultado='01EL FORMATO DEL ARCHIVO NO ES VALIDA';
            $bandera=1;
        }       
    }

    if ($bandera==0)
    {
        $inputConcepto                 =$app->validoInput($_POST['inputAccion']);
        $inputAmount                   =$app->validoInput($_POST['inputFondeo']);
        $upload=$datefound             =date('Y-m-d H:i:s');
        $user                          =$_SESSION['USER'];
        $idadmin=1;
        
        $datacompany=$app->viewCompany($idadmin); // obtiene los datos de la empresa
        $correos=explode(',',$datacompany[17]); //  busca los correos que deben estar separados por comas

        $saldo_actual = $datacompany[11]; //( $empresaMonto = $app->viewCompany( $Company ) )? $empresaMonto[11] : 0;
        if($inputConcepto=='REVERSAR A ADMINISTRADOR')
        {
            if($inputAmount>$saldo_actual)
            {
                $Resultado='01NO SE CUENTAN CON FONDOS EN EL ADMINISTRADOR PARA EL REVERSO';
                $bandera=1;
            }
            $inputAmount=-1*$inputAmount;
        }

        $newBalance=$saldo_actual+$inputAmount;

        if($bandera==0) // si se puede actualizar
        {
            $data =
            [
                $inputConcepto,
                $inputAmount,
                $upload,
                $datefound,
                $user,
                $file_url,
                0.0,
                0.00,//saldo master
                $datacompany[11],// Saldo disponible del master
                ''// tipo de cuenta (mastercard o carnet) 
            ];
            if($app->new_FoundMA($data))
            {
                $app->UpdateFundsAdmin($idadmin,$newBalance);
                
                $Resultado='00Comprobante cargado correctamente';
                $Data=[$datacompany[0],$saldo_actual,$inputAmount,$newBalance,''];                    
                $msj=$msjs_mails->Found_MasterAcount($Data); // construye el mensaje a  enviar                    
                $titulo_correo='FONDEO A CUENTA MAESTRA'; 
               $respuesta_mail=$email->enviarmailMA($msj,$titulo_correo,$correos);
                $datalogmail=[1,$msj,$respuesta_mail];
                $app->insertlogemail($datalogmail);   
                if($respuesta_mail!=202)
                {
                    $respuesta_mail=$email->enviarmailMA($msj,$titulo_correo,$correos);
                    $datalogmail=[1,$msj,$respuesta_mail];
                    $app->insertlogemail($datalogmail);
                }
                                                            
            }
            else
            {
                $Resultado='01Ocurrio un error intentelo mas tarde';
            }
        }

    }
    if(substr($Resultado,0,2)=='00')
    {
        $msg='Hecho!';
    }else
    {
        $msg='Error';
    }

}

$dUsuario = $app->viewEmailUser( $_SESSION['USER'] );
$Data_Masters_Acounts=$app->getProductsPlatform(); // Obtiene los datos de las cuentas de la plataforma(Nombre,AgregamentId,ProductId)
        
include 'header.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!--Mensajes de las operaciones Exitoso o Error-->
            <?php if (strlen($msg) == 6) { ?>
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Exitoso - </b> <?php echo substr($Resultado, 2); ?>.</span>
                </div>
            <?php } elseif (strlen($msg) == 5) { ?>
                <div class="alert alert-danger">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> <?php echo substr($Resultado, 2); ?>.</span>
                </div>
            <?php } ?>

            <div class="col-md-10">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Cargar información de la cuenta maestra</h4>
                    </div>
                    <div class="content">
                    <form action="loadfound_MA?scr=18" method="POST" enctype="multipart/form-data" onsubmit="return checkSubmitBlock('change_password');">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="inputEmail">Monto</label>
                                    <input type="number" step="0.01" class="form-control" name="inputFondeo" value="0.0" required>
                                </div>
                            </div>
                        
                               <div class="col-md-4">
                                <div class="form-group">
                                    <label for="inputEstado">Accion</label>           
                                    <select class="form-control" name="inputAccion" required>
                                        <option value="FONDEAR A ADMINISTRADOR">FONDEAR A ADMINISTRADOR</option>
                                        <option value="REVERSAR A ADMINISTRADOR">REVERSAR A ADMINISTRADOR</option>
                                    </select> 
                                </div>
                            </div>
                   
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="inputPass3">Archivo </label>
                                    <input type="file" class="form-control" name="inputImagenPerfil">
                                </div>
                            </div>
                            
                        </div>
                
                        <input type="submit" id="change_password" value="Guardar" name="act"  class="btn btn-info btn-fill pull-right" />
                            
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
?>