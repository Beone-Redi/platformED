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

include 'header.php';

?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <h4 class="title">Políticas de terminos y condiciones</h4>
                            <p class="category">Vista general de la información relacionada con las políticas de terminos y condiciones.</p>
                            </div>
                            <div class="content">
                                <p class="category">
                                    
                                    <br>
                                        <p align="justify" style="font-size:14px;">
                                            Recargas Grupo Energeticos S.A. de C.V, una empresa que está comprometida a proteger su privacidad. Además de lo dispuesto en la ley y su reglamento correspondiente en la materia, 
                                            seguimos las mejores prácticas internacionales en el manejo y administración de datos personales. En todo caso, manejaremos sus datos personales con altos estándares de ética, responsabilidad y profesionalismo.
                                       </p>
                                       <br>
                                        
                                        <p align="justify" style="font-size:14px;">
                                            Estas terminos y condiciones son aplicables específicamente a la información que recopilamos de usted a través de nuestra aplicación denominada energexpass y plataforma de Recargas Grupo Energeticos S.A. de C.V.
                                            <br><br>En atención a la Ley Federal de Protección de Datos Personales en Posesión de Particulares y su reglamento (México) en nuestra calidad de responsables del tratamiento de sus datos personales, le informamos lo siguiente: 
                                            <br> 
                                            <br>
                                            <h5><b>1. A través de esta aplicación y plataforma, recopilamos únicamente los datos que a continuación se señalan:</b></h5>
                                        </p>
                                        <br>
                                        
                                        <b>Datos Personales y Financieros:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                            Nombre completo, género, lugar y fecha de nacimiento, correo electrónico, domicilio y número celular.   
                                            <br>
                                            Información financiera concerniente al tipo de cuenta bancaria, ingresos y egresos.          </p>
                                        </p><br>
                                        <b>Datos de Uso:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                            Navegación, uso de características, clicks, visitas, conversiones.          
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">        
                                        Usted se compromete a que los datos que proporciona a Recargas Grupo Energeticos S.A. de C.V sean verídicos, completos y exactos. Cualquier dato falso, incompleto o inexacto al momento de recabarlos, será de su exclusiva responsabilidad, en caso de que requiera rectificar alguno de ellos, le ofrecemos que lo haga directamente a través de su cuenta de Usuario o bien al correo electrónico: energexcard@energexcard.com</p>
                                        

                                        <p align="justify" style="font-size:14px;">
                                            <br>
                                            <h5><b>2. Recopilamos la información señalada en el punto anterior con los siguientes propósitos:</b></h5>
                                        </p>
                                        <br>
                                         <p align="justify" style="font-size:14px;">
                                            Fines de identificación y verificación de datos
                                            <br>
                                            Fines estadísticos
                                            <br>
                                            Para informarle acerca de las actualizaciones de la aplicación, así como enviarle información importante relativa a su cuenta de usuario.
                                            <br>
                                            Para proporcionarle una mejor experiencia de uso de nuestra aplicación, al conocer sus hábitos de consumo y sus correspondientes datos de ingresos y egresos, y así ofrecerle asesoría financiera más precisa.
                                        </p>
                                        <br> 
                                        <p align="justify" style="font-size:14px;">
                                            <br>
                                            <h5><b>3. En la recolección de datos personales seguimos todos los principios que marca la ley mexicana (artículo 6):</b></h5>
                                        </p>
                                        <p align="justify" style="font-size:14px;">
                                            Licitud, calidad, consentimiento, información, finalidad, lealtad, proporcionalidad y responsabilidad
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            4. En ningún caso se venderán, regalarán, facilitarán ni alquilarán sus datos personales y/o financieros a terceros, salvo su expresa autorización,
                                            a excepción de los partners involucrados en la operación de nuestros sistemas, o cuando por medio de una orden judicial se requiera 
                                            para cumplir con ciertas disposiciones procesales. Sólo se podrá difundir la información en casos especiales, como identificar, 
                                            localizar o realizar acciones legales contra aquellas personas que infrinjan las condiciones de servicio de la aplicación, causen daños a,
                                            o interfieran en, los derechos de Recargas Grupo Energeticos S.A. de C.V, sus propiedades, de otros Usuarios del portal o de cualquier persona que pudiese resultar perjudicada por dichas acciones.
                                        </p>
                                        <br>
                                        
                                        <p align="justify" style="font-size:14px;">
                                            5. La seguridad y la confidencialidad de los datos que los usuarios proporcionen al contratar un servicio o comprar un producto en línea estarán protegidos 
                                            por un servidor seguro bajo el protocolo Secure Socket Layer (SSL), de tal forma que los datos enviados se transmitirán encriptados para asegurar su resguardo.         
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            6. El usuario otorga su consentimiento a Recargas Grupo Energeticos S.A. de C.V para que le envíe todo tipo de publicidad, incluyendo promociones. 
                                            El usuario podrá dejar de recibir los mensajes de promoción y publicidad enviando un correo a la dirección energexdcard@energex.com pidiendo que se le elimine de la lista de correos para envío de publicidad.
                                        </p>
                                        <br>

                                        <p align="justify" style="font-size:14px;">
                                            7. Las cookies son archivos de texto que son descargados automáticamente y almacenados en el disco duro del equipo de cómputo del 
                                            Usuario al navegar en una página de internet específica, que permiten recordar al servidor de Internet algunos datos sobre los usuarios que acceden 
                                            a este portal electrónico.
                                            <br><br>
                                            En Recargas Grupo Energeticos S.A. de C.V usamos las cookies para:
                                            <br>
                                            Su tipo de navegador y sistema operativo.
                                            <br>    
                                            Las páginas de internet que visita.
                                            <br>
                                            Los vínculos que sigue.
                                            <br>
                                            Estas cookies y otras tecnologías pueden ser deshabilitadas. Para conocer cómo hacerlo, consulte la información de su explorador de Internet.
                                            <br>
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            8. Este aviso de privacidad cumple con los requisitos que marca la ley (artículos 15 y 16). Recargas Grupo Energeticos S.A. de C.V toma muy en cuenta la seguridad de 
                                            sus datos, por lo que contamos con mecanismos tecnológicos, físicos, administrativos y legales para proteger su información, tales como 
                                            servidores con los más altos niveles de seguridad informática y el certificado SSL de conexión segura. 
                                            Además nuestras bases de datos y toda la información que viaja a través de ellas se encuentra bajo encriptación de 128 bits 
                                            (el mismo estándar que utilizan los sitios bancarios) y debidamente resguardada en servidores que cuentan con más de 7 niveles de seguridad 
                                            tanto física como informática.  
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            9. Recargas Grupo Energeticos S.A. de C.V se reserva el derecho de cambiar este aviso de privacidad en cualquier momento, mediando la debida notificación que exige la ley. 
                                            En caso de que exista algún cambio en este aviso de privacidad, Recargas Grupo Energeticos S.A. de C.V lo comunicará de la siguiente manera: (a) enviándole un correo electrónico a la cuenta 
                                            que ha registrado en la aplicación y/o (b) publicando una nota visible en nuestra aplicación. Recargas Grupo Energeticos S.A. de C.V no será responsable si usted no recibe la notificación de 
                                            cambio en el aviso de privacidad si existiere algún problema con su cuenta de correo electrónico o de transmisión de datos por internet. 
                                            Por su seguridad, revise en todo momento que así lo desee el contenido de estos terminos y condiciones de este portal 
                                            </p>
                                        <br>

                                        <p align="justify" style="font-size:14px;">
                                            10. Usted podrá ejercer sus derechos ARCO (acceso, rectificación, cancelación y/u oposición) a partir del 1 de abril de 2016.
                                            <br>
                                            Domicilio: Cintermex Local 82 Av. Fundidora, Parque Fundidora, Monterrey Nuevo León.
                                            <br>
                                            Correo Electrónico: energexcard@energexcard.com
                                            <br>
                                            Teléfono: +52 (55) 5350 9654
                                            <br>
                                            La solicitud ARCO deberá contener y acompañar lo que señala la ley en su artículo 29 y el 89, 90, 92 y demás aplicables de su Reglamento.
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            11. En caso de que se presente una controversia que se derive del presente aviso de privacidad, 
                                            las partes intentarán primero resolverla a través de negociaciones de buena fe, pudiendo ser asistidos por un mediador profesional. 
                                            Si después de un máximo de 30 días de negociación las partes no llegaren a un acuerdo, se estará a lo dispuesto por la Ley Federal de Protección de Datos Personales. 
                                            en Posesión de Particulares, aceptando las partes la intervención que pudiere tener el Instituto Federal de Acceso a la Información y Protección de Datos Personales.
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            12. Al hacer uso de la plataforma Recargas Grupo Energeticos S.A. de C.V, usted renuncia a cualquier otro fuero y legislación que le pudiere corresponder. 
                                            Este portal y sus servicios están regidos por las leyes mexicanas, y cualquier controversia será resuelta frente a las autoridades mexicanas competentes.
                                            
                                            <br>
                                            <br>13. Estos terminos y condiciones quedan aceptados en el momento de registrarse en nuestra aplicación energexpass y plataforma de Recargas Grupo Energeticos S.A. de C.V.</p>
                                        <br>

                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
<?php
include 'footer.php';