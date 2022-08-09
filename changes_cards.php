<?php

// Inicio la variables de sesion.
if ( !isset( $_SESSION ) ) 
{
    session_start();
}

if ( isset( $_SESSION['ACTIVO'] ) <> "2019" or $_SESSION['EMPRESA'] === '0' ) 
{
    header( "Location: login" );
}

include "include/coredata.php";

include 'header.php';

$app        = new app();
$msg        = FALSE;
$bandera    = FALSE;
$Empresas   = $app->viewAllCompanysByPerfil('EMPRESA');
$Mes        = date( 'Y-m-' );
$Dia        = date( 'd' ) - 1;
$minDate    = $Mes . '01';
$maxDate    = date( 'Y-m-d' );
$maxDateFin = $Mes . str_pad( $Dia, 2, '0', STR_PAD_LEFT );

$Consulta   = false;
$fechaI  = $maxDate; 
$fechaF  = $maxDate;
$result=[];

if ( $_SERVER[ 'REQUEST_METHOD' ] == 'POST' )
{
    //var_dump($_POST);
    $cards          = $_POST['inputCards'];
    $Icompany       = $_POST['inputEmpresa'];
    $OCompany       = $_POST['outEmpresa'];
    $nombreNew      = $_POST['inputNombreCompleto'];
    $Observaciones  = '';
    $Motivo         = $_POST['inputReasignacion'];
    $User           = $_SESSION['USER'];
    $num_registros  = 0;
    for($i=0;$i<sizeof($cards);$i++)
    {   
        $Ides=$app->viewDataByCard($cards[$i]);
        $IdCard=$Ides[0];//Id Card
        $IdUser=$Ides[1];//Id User
        $dataCard=[$Icompany,$cards[$i],$Motivo,$OCompany,$User];
        $res1=$app->updateCompanyUser($nombreNew,$Observaciones,$OCompany,$IdUser);
        $res2=$app->updateCompanyCard($OCompany,$IdCard);
        $res3=$app->updateCompanyCardPaynet($OCompany,$IdCard);
        $res4=$app->new_ChargeCard($dataCard);
        if($res1 && $res2 && $res3 && $res4)
        {
            $num_registros++;
        }
    }

    if ( $num_registros > 0 )
    {
        $msg    = 'Hecho!';
        $Resultado1  = '00Se crearon ' . $num_registros . ' usuarios';
    }
    else
    {
        $msg    = 'Error';
        $Resultado1  = '01No se creo ningun usuario.';
    }
    
} 
else 
{
    $result = [];    
}

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">

        <?php if ( strlen( $msg ) == 6 ) { ?> 
                <div class="alert alert-success">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Successful - </b> <?php echo substr( $Resultado1, 2 ); ?></span>
                </div>
            <?php } elseif ( strlen( $msg ) == 5 ) { ?>
                <div class="alert alert-danger">
                    <button type="button" aria-hidden="true" class="close">×</button>
                    <span><b> Error - </b> <?php echo substr( $Resultado1, 2 ); ?></span>
                </div>
            <?php } ?>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <!-- <h4 class="title">Cambiar tarjetas </h4> -->
                        <h4 class="title">Change cards</h4>
                        <!-- <p class="category">Reasignacion de tarjetas a empresas.</p> -->
                        <p class="category">Reassignment of cards to companies..</p>
                        <form action="changes_cards?scr=9" method="POST" onsubmit="return checkSubmitBlock('btnsubmit');">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <!-- <label for="inputEmpresa">Empresa de procedencia</label> -->
                                        <label for="inputEmpresa">Company of origin</label>
                                        <select class="form-control transactions2" name="inputEmpresa" required onChange="getCards(this.value)">
                                        <option value=""></option>
                                        
                                        <?php for ($i = 0; $i < count( $Empresas ); $i += 7) {
                                            if ( isset( $Empresas[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empresas[ $i ]; ?>"><?php echo utf8_encode(strtoupper($Empresas[ $i + 1 ])); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                    <!-- <label for="inputCiudad">Tarjeta</label> -->
                                    <label for="inputCiudad">Card</label>
                                        <select class="form-control cards2" id="cards" required="" name="inputCards[]" multiple></select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <!-- <label for="outEmpresa">Empresa destino</label> -->
                                        <label for="outEmpresa">Destination company</label>
                                        <select class="form-control transactions2" name="outEmpresa" required>
                                        <option value=""></option>
                                        <option value="TODOS">TODOS</option>
                                        <?php for ($i = 0; $i < count( $Empresas ); $i += 7) {
                                            if ( isset( $Empresas[ $i ] ) ) {
                                        ?>
                                            <option value="<?php echo $Empresas[ $i ]; ?>"><?php echo utf8_encode(strtoupper($Empresas[ $i + 1 ])); ?></option>
                                        <?php } } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label for="inputNombreCompleto">Nombre Completo</label> -->
                                        <label for="inputNombreCompleto">Full name</label>
                                        <input type="text" class="form-control" name="inputNombreCompleto" value="" required />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <!-- <label for="inputObservaciones">Motivo de reasginacion</label> -->
                                        <label for="inputObservaciones">Reason for reassignment</label>
                                        <textarea rows="5" class="form-control" name="inputReasignacion" required></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="inputCiudad">&nbsp;</label>
                                        <button type="submit" class="form-control btn btn-primary" id="btnsubmit" name="ConsultaF"><b>Re asign</i></b></button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                </div>
                            
                            </div>

                        </form>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>
<?php

echo "<script type='text/javascript'> $('.transactions2').select2();</script>";
echo "<script type='text/javascript'> $('.cards2').select2();</script>";


include 'footer.php';