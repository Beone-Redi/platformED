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
                        <h4 class="title">Políticas de privacidad</h4>
                            <p class="category">
                            Vista general de la información relacionada con las políticas de privacidad.</p>
                            </div>
                            <div class="content">
                                <p class="category">
                                    
                                    <br>
                                        <p align="justify" style="font-size:14px;">
                                            Recargas Grupo Energeticos S.A. de C.V, con domicilio en Cintermex Local 82 Av. Fundidora, Parque Fundidora,Monterrey Nuevo León, es el responsable del uso y protección de sus datos personales, y al respecto le informamos lo siguiente:
                                        </p>
                                        <br>
                                        <b>¿Para qué fines utilizaremos sus datos personales?</b><br>
                                        <p align="justify" style="font-size:14px;">
                                            Los datos personales que recabamos de usted, los utilizaremos para las siguientes finalidades 
                                            que son necesarias para el servicio que le proporcionamos:          
                                            <br>
                                            <ul>
                                                <li>Verificar y confirmar su identidad en cualquier relación jurídico o de negocios.</li>
                                                <li>Administrar, operar y correcto funcionamiento de los servicios y productos de medios de pago de los que usted es beneficiario.</li>
                                                <li>Cumplir con los lineamientos y disposiciones de las autoridades que nos regulan.</li>
                                                <li>Fines estadísticos para mejorar nuestros productos y servicios actuales y futuros.</li>
                                                <li>Atención a dudas, comentarios y quejas que realice</li>
                                                <li>Para fines mercadotécnicos, publicitarios, promocionales, telemarketing o de prospección comercial.</li>
                                            </ul>
                                                
                                        </p>
                                        <br>
                                        <b>¿Qué datos personales recabamos y utilizamos sobre usted?</b>
                                        <p align="justify" style="font-size:14px;">
                                            Para llevar a cabo las finalidades descritas en el presente aviso de privacidad, utilizaremos sus datos de identificación,
                                            datos de contacto, y datos laborales. Estos datos pueden ser recabados de distintas formas: cuando usted nos los proporciona directamente; 
                                            cuando visita nuestro sitio de Internet o utiliza nuestros servicios en línea, incluyendo la autenticación de datos; y cuando obtenemos información a través de otras 
                                            fuentes que están permitidas por la Ley Federal de Datos Personales en Posesión de los Particulares (en adelante la Ley).  
                                        </p>
                                        <br>
                                        <b>¿Qué datos personales sensibles utilizaremos?</b><br>
                                        <p align="justify" style="font-size:14px;">
                                            Así mismo, le informamos que nuestra empresa no trata datos personales sensibles.          
                                        </p>
                                        <br>
                                        <b>¿Con quién compartimos su información y para qué fines?</b>
                                        <p align="justify" style="font-size:14px;">        
                                         
                                            Sus datos personales pueden ser transferidos y tratados dentro y fuera del país, por personas distintas a esta empresa, para (a) cumplir con las disposiciones legales vigentes; 
                                            (b) en acatamiento a mandamiento u orden judicial; y (c) siempre que sea necesario para la operación y funcionamiento de la Organización. 
                                            En ese sentido, su información puede ser compartida con nuestras empresas filiales o subsidiarias. En caso de transferencia de los datos personales, esta siempre se llevará a cabo a través de figuras e instrumentos legales que brinden el nivel de protección y medidas de seguridad adecuados para dichos datos.
                                            <br><br> 
                                            En caso de que no se obtenga del Cliente y/o de los tarjetahabientes (trabajadores) beneficiarios o consumidores finales 
                                            su oposición expresa enviando un correo a la dirección electrónica energexcard@energexcard.com para que sus datos personales sean tratados y transferidos en la forma y términos antes descritos, se entenderá que han otorgado su consentimiento para ello.
                                        
                                                
                                        </p>
                                        <br>
                                        <b>¿Cómo puede ejercer sus Derechos ARCO, revocar su consentimiento, o limitar el uso o divulgación de sus datos personales?</b>
                                        <p align="justify" style="font-size:14px;">
                                            <br>Usted tiene derecho a conocer qué datos personales tenemos de usted, para qué los utilizamos y las condiciones del uso que les damos (Acceso).
                                            Asimismo, es su derecho solicitar la corrección de su información personal en caso de que esté desactualizada, sea inexacta o incompleta (Rectificación);
                                            que la eliminemos de nuestros registros o bases de datos cuando considere que la misma no está siendo utilizada conforme a los principios, deberes y obligaciones previstas en la normativa 
                                            (Cancelación); así como oponerse al uso de sus datos personales para fines específicos (Oposición). Estos derechos se conocen como derechos ARCO.
                                            <br><br>
                                            Usted puede revocar el consentimiento que, en su caso, nos haya otorgado para el tratamiento de sus datos personales. Sin embargo, es importante que tenga en cuenta 
                                            que no en todos los casos podremos atender su solicitud o concluir el uso de forma inmediata, ya que es posible que por alguna obligación legal requiramos seguir tratando sus datos personales. 
                                            Asimismo, usted deberá considerar que para ciertos fines, la revocación de su consentimiento implicará que no le podamos 
                                            seguir prestando el servicio que nos solicitó, o la conclusión de su relación con nosotros.
                                            <br><br>
                                            Usted pueda limitar el uso y divulgación de su información personal mediante su inscripción en el Registro Público para Evitar Publicidad, que está a cargo de la Procuraduría Federal del Consumidor, 
                                            con la finalidad de que sus datos personales no sean utilizados para recibir publicidad o promociones de empresas de bienes o servicios. Para mayor información sobre este registro, 
                                            usted puede consultar el portal de Internet de la PROFECO, o bien ponerse en contacto directo con ésta.
                                            <br><br>
                                            Para el ejercicio de cualquiera de los derechos ARCO, revocar su consentimiento o limitar el uso y divulgación de su información personal, usted deberá mandar un correo a energexcard@energexcard.com con el título “Derechos ARCO” 
                                            y deberá contener cuando menos lo siguiente: (i) Nombre del Titular de los Datos Personales; (ii) Domicilio, teléfono y correo electrónico para recibir comunicaciones (iii) Documentos que acrediten su identidad. En caso de ser Representante Legal, 
                                            el instrumento del que se desprendan sus facultades de representación; (iv) Descripción clara y precisa de los datos personales respecto de los que se busca ejercer los derechos; (v) Descripción clara y preciso del derecho que busca ejercer; 
                                            (vi) Cualquier otro elemento o documento que facilite la localización de los datos personales, así como cualquier otro elemento que, de conformidad con la legislación y al último Aviso de Privacidad que se encuentren vigentes al momento de la presentación de su solicitud.
                                            
                                        </p>
                                        <br> 
                                        <b>El uso de tecnologías de rastreo en nuestro portal de Internet</b>
                                        <p align="justify" style="font-size:14px;">
                                            <br>
                                            Le informamos que en nuestra página de Internet utilizamos cookies para brindarle un mejor servicio y 
                                            experiencia de usuario al navegar en nuestra página. Las Cookies son identificadores que nuestro servidor le puede enviar a su 
                                            computadora para identificar el equipo que ha sido utilizado durante la sesión. La mayoría de exploradores de Internet están diseñados para aceptar 
                                            estas Cookies automáticamente. Adicionalmente, usted podrá desactivar el almacenamiento de Cookies o ajustar su explorador de Internet para que le sea 
                                            informado antes que las Cookies se almacenen en su computadora. Las Cookies pueden ser depuradas por usted de forma manual en cuanto usted lo decida.
                                        
                                            <br><br>
                                            Sus datos personales se podrán transferir a terceros para (a) cumplir con las disposiciones legales vigentes; (b) en acatamiento a mandamiento u orden judicial; y (c) 
                                            siempre que sea necesario para la operación y funcionamiento de la Organización. En caso de transferencia de los datos personales, esta siempre se llevará a cabo a través de 
                                            figuras e instrumentos legales que brinden el nivel de protección y medidas de seguridad adecuados para dichos datos.
                                        </p>
                                        
                                        <br>
                                        <b>¿Cómo puede conocer los cambios a este aviso de privacidad?</b>
                                        <p align="justify" style="font-size:14px;">
                                            <br>
                                            El presente aviso de privacidad puede sufrir modificaciones, cambios o actualizaciones derivadas de nuevos requerimientos legales; de nuestras propias necesidades por los 
                                            productos o servicios que ofrecemos; de nuestras prácticas de privacidad; de cambios en nuestro modelo de negocio, o por otras causas.
                                            <br><br>
                                            Estas modificaciones se anunciarán y estarán disponibles al público a través de nuestra página de internet www.energexcard.com sección “Aviso de Privacidad”
                                        
                                        </p>
                                        <br>
                                        <b>¿Ante quién puede presentar sus quejas y denuncias por el tratamiento indebido de sus datos personales?</b>
                                        <p align="justify" style="font-size:14px;">
                                        Si usted considera que su derecho de protección de datos personales ha sido lesionado por alguna conducta de nuestros empleados o de nuestras actuaciones o respuestas, presume que en el 
                                        tratamientos de sus datos personales existe alguna violación a las disposiciones previstas en la Ley Federal de Protección de Datos 
                                        Personales en Posesión de los Particulares, podrá interponer la queja o denuncia correspondiente ante el IFAI, para mayor información visite www.ifai.org.mx
                                        </p>
                                        
                                       
                                      

                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
<?php
include 'footer.php';