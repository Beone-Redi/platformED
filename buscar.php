<?php

require_once( 'include/coredata.php' );
//include 'header.php';

$app = new app();

$html = "";

if (isset($_POST['consulta'])) 
{
	$data = $app->GetInfUser($_POST["consulta"]);
	//var_dump($data);
	if (sizeof($data)>0) 
	{
		$html.="<table class='table table-hover table-striped'>
			<thead>
				<tr id='titulo'>
					<th>ID</th>
					<th>Name</th>
					<th>Company</th>
					<th>Card</th>
					<th>Card Type</th>
					<th>User</th>
					<th>Password</th>
					<th>View Password</th>
				</tr>
			</thead>
		<tbody>";

		foreach ($data as $row) 
		{ 
		$html.=	"<tr>
					<td>".$row["idepersona"]."</td>
					<td>".utf8_encode($row["fullname"])."</td>
					<td>".utf8_encode($row["empresa"])."</td>
					<td>".'****-****-' . substr($row["tarjeta"], 0, 4) . '-' . substr($row["tarjeta"],-4)."</td>
					<td>".$row["Product"]."</td>
					<td>".$row["email"]."</td>
					<td><input type='password' class='form-control' maxlength='25'style='width:180px' name='inputPass1' id='inputPass1' disabled value=".substr(strtoupper(utf8_encode($row["pass"])),0,30)."></td>
					<td><input style='margin-left:60px;' type='checkbox' id='mostrar_contrasena' title='Mostrar contraseña'></td>
				</tr>";
		}

		$html.="</tbody></table>";
	}
	else
	{
		$html.="No se encuntran regitros de la información ingresada...";
	}
	echo $html;
}
?>
<script>
$(document).ready(function () 
{
  $('#mostrar_contrasena').click(function () 
  {
	if ($('#mostrar_contrasena').is(':checked')) 
	{
		$('#inputPass1').attr('type', 'text');
	} else 
	{
		$('#inputPass1').attr('type', 'password');
	}
  });
});
</script>