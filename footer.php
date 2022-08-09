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


?>
        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-right">
                    <ul>
                        <li>
                            <a href="politics" title="Information Policies">
                                Information policies
                            </a>
                        </li>
                        <li>
                            <a href="terms_conditions" title="Terms and Conditions">
                                Terms and Conditions
                            </a>
                        </li>
                        <li>
                            <a href="privacy_policies" title="Privacy Policies">
                                Privacy Policies
                            </a>
                        </li>
                        <li>
                            <a href="#" title="Home">
                                <i class="pe-7s-up-arrow" style="font-size:28px;"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <p class="copyright pull-left">
                    Energex Pass Corp.
                </p>
            </div>
        </footer>

    </div>
</div>

</body>
    <!--   Core JS Files   -->
    <script src="assets/js/jquery.3.2.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>

	<!--  Charts Plugin -->
	<script src="assets/js/chartist.min.js"></script>

    <!--  Notifications Plugin    -->
    <script src="assets/js/bootstrap-notify.js"></script>
    
    <!-- Light Bootstrap Table Core javascript and methods -->
	<script src="assets/js/light-bootstrap-dashboard.js?v=1.4.0"></script>

    <!-- Fancybox -->
    <script src="assets/js/jquery.fancybox.min.js"></script>

	<!-- Light Bootstrap -->
	<!-- <script src="assets/js/demo.js"></script> -->
    <script type='text/javascript' src="assets/js/demo.js?filever=<?=filesize('assets/js/demo.js')?>"></script>

    
	<script type="text/javascript">
    	$(document).ready(function(){

        	demo.initChartist();
            /*
        	$.notify({
            	icon: 'pe-7s-gift',
            	message: "<b>Dashboard</b>"

            },{
                type: 'info',
                timer: 4000
            });
            */
        });
        
        $(document).ready(function() {
            $('#tablesearch').DataTable();
        } );
	</script>
    
    <script  src="assets/js/index.js"></script>

    <!-- Need to use datatables.net -->
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

    <script>
    $(document).ready(function(){
    
    //Apply the datatables plugin to your table
    $('#myTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/English.json"
        },
        dom: 'Bfrtip',
        buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ]
    });

    //Datatable para Compañias
    $('#Companys').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/English.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                filename: 'Companys'
            },
            {
                extend: 'excelHtml5',
                filename: 'Companys'
            }
            ,
            {
                extend: 'csvHtml5',
                filename: 'Companys'
            }
            ,
            {
                extend: 'pdfHtml5',
                filename: 'Companys'
            }
        ]
    });

    //Datatable para Usuarios
    $('#Users').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/English.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                filename: 'Users'
            },
            {
                extend: 'excelHtml5',
                filename: 'Users'
            }
            ,
            {
                extend: 'csvHtml5',
                filename: 'Users'
            }
            ,
            {
                extend: 'pdfHtml5',
                filename: 'Users'
            }
        ]
    });

    //Datatable para Fondeos
    $('#Founds').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/English.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                filename: 'Founds'
            },
            {
                extend: 'excelHtml5',
                filename: 'Founds'
            }
            ,
            {
                extend: 'csvHtml5',
                filename: 'Founds'
            }
            ,
            {
                extend: 'pdfHtml5',
                filename: 'Founds'
            }
        ]
    });

    //Datatable para tarjeta habientes
    $('#CH').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/English.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                filename: 'CardHolders'
            },
            {
                extend: 'excelHtml5',
                filename: 'CardHolders'
            }
            ,
            {
                extend: 'csvHtml5',
                filename: 'CardHolders'
            }
            ,
            {
                extend: 'pdfHtml5',
                filename: 'CardHolders'
            }
        ]
    });

    //Datatable para Tarjetas
    $('#CardsMovements').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/English.json"
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copyHtml5',
                filename: 'Movement Cards'
            },
            {
                extend: 'excelHtml5',
                filename: 'Movement Cards'
            }
            ,
            {
                extend: 'csvHtml5',
                filename: 'Movement Cards'
            }
            ,
            {
                extend: 'pdfHtml5',
                filename: 'Movement Cards'
            }
        ]
    });


    });

    $(document).ready(function(){
        $(".dropdown-toggle").dropdown();
    });
</script>
</html>
