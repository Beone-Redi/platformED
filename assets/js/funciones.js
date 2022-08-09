function checkSubmit(val) {
    document.getElementById(val).value = "Enviando...";
    document.getElementById(val).disabled = true;
    return true;
}
function confirm_form(val) {
    if (confirm('¿Estas seguro de realizar el fondeo a la tarjeta?')) {
        return TRUE;
    }
    else {
        document.getElementById(val).value = 0;
    }
}

function confirm_val_company(val) {
    var trans = document.getElementById(val).value;
    var comision = document.getElementById('comision_calculada_value').value;
    var IVA = document.getElementById('IVA_value').value;
    var monto_fondeo = document.getElementById('monto_fondear_value').value;
    var msj = '\nMonto transferencia recibida : ' + trans + '\nComision : ' + comision + '\nIVA : ' + IVA + '\nFondeo empresa : ' + monto_fondeo;
    if (confirm('¿Estas seguro de fondear a la empresa?' + msj)) {
        return TRUE;
    }
    else {
        //return FALSE;
        document.getElementById(val).value = 0;
    }
}

function checkSubmitBlock(val) {
    $.fancybox.open({
        // src: 'https://codepen.io/about/',

        type: 'iframe',
        opts: {
            modal: true,
            afterShow: function (instance, current) {
                console.info('done!');
            }
        }
    });
    document.getElementById(val).value = "Enviando...";
    document.getElementById(val).disabled = true;
    return true;
}
function getCiudades(val) {
    // Esperando la carga...
    $('#ciudades').html('<option value=""><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></option>');
    //realizo la call via jquery ajax
    $.ajax({
        url: 'cities.php',
        data: 'id=' + val,
        success: function (resp) {
            $('#ciudades').html(resp);
        }
    });
}


function getCards(val) {
    // Esperando la carga...
    $('#cards').html('<option value=""><span class="glyphicon glyphicon-refresh glyphicon-refresh-animate"></span></option>');
    //realizo la call via jquery ajax
    $.ajax({
        url: 'cards.php',
        data: 'id=' + val,
        success: function (resp) {
            $('#cards').html(resp);
        }
    });
}

function mostrarContrasena(val1, val2) {

    if (document.getElementById(val1).type == 'password' && document.getElementById(val2).type == 'password') {
        document.getElementById(val1).type = 'text';
        document.getElementById(val2).type = 'text';

    } else {
        document.getElementById(val1).type = 'password';
        document.getElementById(val2).type = 'password';
    }
}

function getComisiones(val) {
    document.getElementById('amountcompany').value = 0.00;
    document.getElementById('monto_transferencia').value = 0.00;
    document.getElementById('comision_calculada').value = 0.00;
    document.getElementById('comision_porcentaje').value = 0.00;
    document.getElementById('IVA').value = 0.00;
    document.getElementById('monto_fondear').value = 0.00;
    document.getElementById('comision_calculada_value').value = 0.00;
    document.getElementById('comision_porcentaje_value').value = 0.00;
    document.getElementById('IVA_value').value = 0.00;
    document.getElementById('monto_fondear_value').value = 0.00;
    // Esperando la carga...
    //realizo la call via jquery ajax
    $.ajax({
        url: 'comisiones.php',
        data: 'id=' + val,
        success: function (resp) {
            $('#inputComision').val(+resp);
        }
    });
}

function calcularComision(transferencia, comision) {

    var comision2 = document.getElementById(comision).value;// obtiene el valor             

    var tranfs = document.getElementById(transferencia).value;// obtiene el valor             
    var comision_calculada_new = tranfs * comision2 / 100;
    var IVA_comision = comision_calculada_new * 16 / 100;
    document.getElementById('monto_transferencia').value = tranfs;
    document.getElementById('comision_calculada').value = comision_calculada_new;
    document.getElementById('comision_porcentaje').value = comision2;
    document.getElementById('IVA').value = IVA_comision;
    document.getElementById('monto_fondear').value = tranfs - comision_calculada_new - IVA_comision;

    document.getElementById('comision_calculada_value').value = comision_calculada_new;
    document.getElementById('comision_porcentaje_value').value = comision2;
    document.getElementById('IVA_value').value = IVA_comision;
    document.getElementById('monto_fondear_value').value = tranfs - comision_calculada_new - IVA_comision;
}

function calcularComision2(transferencia, comision) {

    var comision2 = document.getElementById(comision).value;// obtiene el valor             

    var tranfs = document.getElementById(transferencia).value;// obtiene el valor             
    var comision_calculada_new = Math.round(tranfs * comision2 / 100);
    var IVA_comision = Math.round(comision_calculada_new * 16 / 100);

    document.getElementById('monto_transferencia').value = tranfs;
    document.getElementById('comision_calculada').value = comision_calculada_new;
    document.getElementById('comision_porcentaje').value = comision2;
    document.getElementById('IVA').value = IVA_comision;
    document.getElementById('monto_fondear').value = tranfs - comision_calculada_new - IVA_comision;

    document.getElementById('comision_calculada_value').value = comision_calculada_new;
    document.getElementById('comision_porcentaje_value').value = comision2;
    document.getElementById('IVA_value').value = IVA_comision;
    document.getElementById('monto_fondear_value').value = tranfs - comision_calculada_new - IVA_comision;
}

function mostrarsaldo(tarjeta) {
    $.ajax({
        url: 'card_balance.php',
        data: 'id=' + tarjeta,
        success: function (resp) {
            value_moneda = resp.toLocaleString();
            $('#saldotarjeta1').val("" + resp);
            return true;
        }
    });
    //     return true;
}

function errasefactura(val) {
    var mensaje = confirm("¿Deseas eliminar la factura " + val + " ?");
    if (mensaje) {
        $.ajax({
            url: 'errasefactura.php',
            data: 'id=' + val,
            success: function (resp) {
                console.log(resp);
                var formulario = document.getElementById("myform1");
                formulario.submit();
                document.getElementById('borrarfactura').value = "Enviando...";
                document.getElementById('borrarfactura').disabled = true;

                //$('#ciudades').html(resp);
            }
        });

    }
    return true;
}

function demo_fancy(val) {
    document.getElementById('authcodefactura').value = val;
    document.getElementById('codefactura').value = val;
    document.getElementById("file").value = "";
    $.fancybox.open({
        src: '#hidden-content',
        type: 'inline',
        opts: {
            afterShow: function (instance, current) {
                console.info('done!');
            }
        }
    });
}

$(buscar_datos());
function buscar_datos(consulta){
	$.ajax({
		url: 'buscar.php' ,
		type: 'POST' ,
		dataType: 'html',
		data: {consulta: consulta},
	})
	.done(function(respuesta){
		$("#datos").html(respuesta);
	})
	.fail(function(){
		console.log("error");
	});
}

$(document).on('keyup','#inputNombreCompleto', function()
{
	var valor = $(this).val();
    if (valor != "") 
    {
		buscar_datos(valor);
    }
    else
    {
		buscar_datos();
	}
});

$(document).on('keyup','#inputCard', function()
{
	var valor = $(this).val();
    if (valor != "") 
    {
		buscar_datos(valor);
    }
    else
    {
		buscar_datos();
	}
});