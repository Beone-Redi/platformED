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
                        <h4 class="title">Terms and conditions policies</h4>
                            <p class="category">General view of the information related to the terms and conditions policies.</p>
                            </div>
                            <div class="content">
                                <p class="category">
                                    
                                    <br>
                                        <p align="justify" style="font-size:14px;">
                                            Energex Pass Corp. S.A. de C.V, a company that is committed to protecting your privacy. In addition to the provisions of the law and its corresponding regulations in the matter,
                                             We follow the best international practices in the management and administration of personal data. In any case, we will handle your personal data with high standards of ethics, responsibility and professionalism.
                                       </p>
                                       <br>
                                        
                                        <p align="justify" style="font-size:14px;">
                                            These terms and conditions are specifically applicable to the information we collect from you through our application called energexpass and Energex Pass Corp. S.A. de C.V.
                                            <br> <br> 
                                                In attention to the Federal Law on Protection of Personal Data Held by Private Parties and its regulations (Mexico) in our capacity as responsible for the processing of your personal data, we inform you of the following:
                                            <br> 
                                            <br>
                                            <h5><b>1. Through this application and platform, we collect only the data indicated below:</b></h5>
                                        </p>
                                        <br>
                                        
                                        <b>Personal and Financial Data:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                        Full name, gender, place and date of birth, email, address and cell number.   
                                            <br>
                                            Financial information concerning the type of bank account, income and expenses.          
                                            
                                        </p><br>
                                        <b>Usage Data:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                        Navigation, use of features, clicks, visits, conversions.          
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">        
                                            You agree that the data you provide to Energex Pass Corp. S.A. C.V. be true, complete and accurate. Any false, incomplete or inaccurate data at the time of collecting them, will be your sole responsibility, in case you need to rectify any of them, 
                                            we offer you to do it directly through your User account or by email: info@energexpass .mx 
                                        </p>
                                        

                                        <p align="justify" style="font-size:14px;">
                                            <br>
                                            <h5><b>2. We collect the information indicated in the previous point with the following purposes:</b></h5>
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            Purposes of identification and verification of data
                                            <br>
                                            Statistical purposes
                                            <br>
                                            o inform you about application updates, as well as send you important information regarding your user account.
                                            <br>
                                            To provide you with a better experience of using our application, knowing your consumption habits and corresponding income and expenses data, and thus offering more accurate financial advice.
                                        </p>
                                        <br> 
                                        <p align="justify" style="font-size:14px;">
                                            <br>
                                            <h5><b>3. In the collection of personal data we follow all the principles established by Mexican law (Article 6):</b></h5>
                                        </p>
                                        <p align="justify" style="font-size:14px;">
                                            Legality, quality, consent, information, purpose, loyalty, proportionality and responsibility
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            4. In no case will your personal and / or financial data be sold, given, provided or rented to third parties, unless expressly authorized,
                                             with the exception of the partners involved in the operation of our systems, or when a court order requires
                                             to comply with certain procedural provisions. Information may only be disseminated in special cases, such as identifying,
                                             locate or take legal action against those who violate the service conditions of the application, cause damage to,
                                             or interfere with, the rights of Energex Pass Corp. S.A. of C.V, its properties, of other Users of the portal or of any person that could be harmed by said actions.
                                        </p>
                                        <br>
                                        
                                        <p align="justify" style="font-size:14px;">
                                            5. The security and confidentiality of the data that users provide when hiring a service or buying a product online will be protected
                                             by a secure server under the Secure Socket Layer (SSL) protocol, so that the data sent will be transmitted encrypted to ensure its safekeeping..         
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            6. The user gives his consent to Energex Pass Corp. S.A. of C.V to send you all kinds of advertising, including promotions.
                                             The user may stop receiving promotional and advertising messages by sending an email to the address info@energexpass.mx asking to be removed from the mailing list for sending advertising.
                                        </p>
                                        <br>

                                        <p align="justify" style="font-size:14px;">
                                            7. Cookies are text files that are automatically downloaded and stored on the hard disk of the computer's computer
                                            User when browsing a specific website, which allows the Internet server to remember some information about the users who access
                                            to this electronic portal.
                                            <br><br>
                                            In Energex Pass Corp. S.A. de C.V we use cookies to:
                                            <br>
                                            Your browser type and operating system.
                                            <br>
                                            The internet pages you visit.
                                            <br>
                                            The links that follow.
                                            <br>
                                            These cookies and other technologies can be disabled. To learn how to do this, see the information in your Internet browser.
                                            <br>
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            8. This privacy notice meets the requirements set by law (articles 15 and 16). Recarga Grupo Energeticos S.A. de C.V takes into account the safety of
                                            your data, so we have technological, physical, administrative and legal mechanisms to protect your information, such as
                                            servers with the highest levels of computer security and SSL certificate of secure connection.
                                            In addition, our databases and all the information that travels through them are under 128-bit encryption.
                                            (the same standard used by banking sites) and properly guarded on servers that have more than 7 levels of security
                                            both physical and computer.  
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            Energex Pass Corp. S.A. de C.V reserves the right to change this privacy notice at any time, through the proper notification required by law.
                                            In case there is any change in this privacy notice, Recargas Grupo Energeticos S.A. de C.V will communicate it as follows: (a) sending an email to the account
                                            that you have registered in the application and / or (b) publishing a visible note in our application. Energex Pass Corp. S.A. de C.V will not be responsible if you do not receive notification of
                                            change in the privacy notice if there is a problem with your email account or data transmission over the internet.
                                            For your safety, check at all times that you want the content of these terms and conditions of this portal.
                                        </p>
                                        <br>

                                        <p align="justify" style="font-size:14px;">
                                            10. You may exercise your ARCO rights (access, rectification, cancellation and / or opposition) from April 1, 2016.
                                            <br>
                                            Address: Av. Fundidora 501, Interior 128PN , Colonia Obrera, Monterrey Nuevo León.
                                            <br>
                                            Correo Electrónico: info@energexpass.mx
                                            <br>
                                            Teléfono: +52 (55) 5350 9654
                                            <br>
                                            The ARCO application must contain and accompany what is stated in the law in its article 29 and 89, 90, 92 and other applicable regulations.
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            11. n the event of a dispute arising from this privacy notice,
                                            the parties will first attempt to resolve it through negotiations in good faith, and may be assisted by a professional mediator.
                                            If after a maximum of 30 days of negotiation the parties do not reach an agreement, the provisions of the Federal Law on Protection of Personal Data will be followed.
                                            In Possession of Individuals, the parties accepting the intervention that the Federal Institute of Access to Information and Protection of Personal Data may have.
                                        </p>
                                        <br>
                                        <p align="justify" style="font-size:14px;">
                                            12. When using the platform Energex Pass Corp. S.A. de C.V, you waive any other jurisdiction and legislation that may apply.
                                             This portal and its services are governed by Mexican laws, and any dispute will be resolved before the competent Mexican authorities.
                                            
                                            <br>
                                            <br>13. These terms and conditions are accepted at the time of registering in our energexpass application and platform Energex Pass Corp. S.A. de C.V.</p>
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