<?php

// Inicio la variables de sesion.
if (!isset($_SESSION)) 
{
    session_start();
}
$MSG='';
if(isset($_POST["id"]))
{
    if(isset($_POST["check1"]) && isset($_POST["check2"]) && isset($_POST["check3"]))
    {
        //var_dump($_SESSION);
        include "include/coredata.php";
        $app                    = new app();
        $id=$_SESSION['EMAIL'];
        $clave=$_SESSION['PASS'];
        $aUser=  $aUser = [ $id, $clave ];
        $valido = $app->login( $aUser );
        $app->new_User_Term_Conditions( $valido[0] );
        
        /*$_SESSION['EMPRESA']    = $valido[1];
        $_SESSION['ACTIVO']     = "2019";
        $_SESSION['TIEMPOIN']   = date("Y-m-d H:i:s");
        $_SESSION['USER']       = $valido[0];
        $_SESSION['PERFIL']     = $valido[2];
        $_SESSION['PERMISOS'] 	= $app->getoptionsUser( $valido[2] );
        */
        $_SESSION['ACTIVO']     = "2019";
        $_SESSION['TIEMPOIN']   = date("Y-m-d H:i:s");
        $_SESSION['USER']       = $valido[0];
        $_SESSION['EMPRESA']    = $valido[1];
        $_SESSION['NCOMPANY']   = $valido[2];
        $_SESSION['PERFIL']     = $valido[3];
        $_SESSION['FULLNAME']   = $valido[4];
        $_SESSION['PERMISOS'] 	= $valido[5];
                
        // Envia al cliente al Dashboard.
        header( 'Location:dashboard' ); 
    }
}

?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
<!--	<link rel="icon" type="image/png" href="assets/img/favicon.ico"> -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title>Energex Pass Corp.</title>

	<meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <!-- Bootstrap core CSS     -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />

    <!-- Animation library for notifications   -->
    <link href="assets/css/animate.min.css" rel="stylesheet"/>

    <!--  Light Bootstrap Table core CSS    -->
    <link href="assets/css/light-bootstrap-dashboard.css?v=1.4.0" rel="stylesheet"/>

    <!--  CSS -->
    <link href="assets/css/demo.css" rel="stylesheet" /> 

    <!--     Fonts and icons     -->
    <link href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,700,300' rel='stylesheet' type='text/css'>
    <link href="assets/css/pe-icon-7-stroke.css" rel="stylesheet" />

    <!-- Grafica -->
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <!-- <script src="assets/js/funciones.js"></script> -->
 
    <!-- Fancybox -->
    <link  href="assets/css/jquery.fancybox.min.css" rel="stylesheet">
    
    <!-- Need to use datatables.net -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css"/>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
      
    <!-- Mini-extra style to be apply to tables with the dataTable plugin  -->
    <!-- Funciones en javascript -->
    <script type='text/javascript' src="assets/js/funciones.js?filever=<?=filesize('assets/js/funciones.js')?>"></script>
    
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    
    <!-- Selected 2 -->
    <!-- JS Files -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
     
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
      <!-- selected 2-->
   <style type="text/css">
#register_form fieldset:not(:first-of-type) {
display: none;
}
#scroll1 {
	height: 300px;
	overflow-y: scroll;
}

</style>
</head>

<body>
    <br>
    <div class="content">
        <div class="container">
            <center><h3>Terms and conditions, Privacy notice, information policies</h3></center>
            <div class="progress">
            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="alert alert-success hide"></div>
            <form id="register_form" novalidate action="conditions" method="post" onsubmit="return checkSubmit('submit');">
                <fieldset>  
                    <h3>Terms and conditions of use of the platform</h3>
                        <div id="scroll1">
                            <div class="content">
                                <p class="category">

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
                        <br>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="check1" id="check1" required>
                            <label class="form-check-label" for="exampleCheck1">I have read and accept the terms and conditions</label>
                        </div>
                        <br>
                        <a  href="logout" onclick="return checkSubmitBlock('crear_company');" class="btn btn-danger">
                            Cancel
                        </a>                 
                        <input type="button" class="next-form btn btn-info" value="Next" />                         
                </fieldset>
                <fieldset>
                    <h3> Privacy policies</h3>
                        <div id="scroll1">
                            <div class="content">
                                <p class="category">

                                    <p align="justify" style="font-size:14px;">
                                        Energex Pass Corp. S.A. de C.V, established in Av. Fundidora 501, Interior 128PN , Colonia Obrera, Monterrey Nuevo León, is responsible for the use and protection of your personal data, and in this regard we inform you of the following:
                                    </p>
                                    <br>
                                    <b>¿For what purposes will we use your personal data?</b><br>
                                    <p align="justify" style="font-size:14px;">
                                    The personal data we collect from you, we will use for the following purposes
                                        that are necessary for the service we provide:          
                                        <br>
                                        <ul>
                                            <li>Verify and confirm your identity in any legal or business relationship.</li>
                                            <li>Manage, operate and correct operation of the services and products of means of payment of which you are a beneficiary.</li>
                                            <li>Comply with the guidelines and provisions of the authorities that regulate us.</li>
                                            <li>Statistical purposes to improve our current and future products and services.</li>
                                            <li>Attention to doubts, comments and complaints you make.</li>
                                            <li>For marketing, advertising, promotional, telemarketing or commercial prospecting purposes.</li>
                                        </ul>
                                            
                                    </p>
                                    <br>
                                    <b>What personal data do we collect and use about you?</b>
                                    <p align="justify" style="font-size:14px;">
                                    To carry out the purposes described in this privacy notice, we will use your identification data,
                                            contact information, and labor data. This data can be collected in different ways: when you provide it directly to us;
                                            when you visit our website or use our online services, including data authentication; and when we get information through others
                                            sources that are allowed by the Federal Law on Personal Data Held by Private Parties (hereinafter the Law).  
                                    </p>
                                    <br>
                                    <b>What sensitive personal data will we use?</b><br>
                                    <p align="justify" style="font-size:14px;">
                                        Likewise, we inform you that our company does not treat sensitive personal data.          
                                    </p>
                                    <br>
                                    <b>With whom do we share your information and for what purposes?</b>
                                    <p align="justify" style="font-size:14px;">        
                                        
                                    Your personal data may be transferred and processed inside and outside the country, by persons other than this company, to (a) comply with the legal provisions in force;
                                        (b) in compliance with the injunction or court order; and (c) whenever necessary for the operation and operation of the Organization.
                                        In that sense, your information may be shared with our subsidiaries or subsidiaries. In case of transfer of personal data, this will always be carried out through legal figures and instruments that provide 
                                        the level of protection and adequate security measures for such data.
                                        <br> <br>
                                        In case it is not obtained from the Client and / or the cardholders (workers) beneficiaries or final consumers
                                        Your express opposition by sending an email to the info@energexpass.mx email so that your personal data is treated and transferred in the manner and terms described above, it will be understood that you have given your consent for it.
                                            
                                    </p>
                                    <br>
                                    <b>How can you exercise your ARCO Rights, revoke your consent, or limit the use or disclosure of your personal data?</b>
                                    <p align="justify" style="font-size:14px;">
                                        <br>You have the right to know what personal data we have about you, what we use them for and the conditions of use we give them (Access).
                                        It is also your right to request the correction of your personal information if it is outdated, inaccurate or incomplete (Rectification);
                                        that we remove it from our records or databases when it considers that it is not being used in accordance with the principles, duties and obligations provided in the regulations
                                        (Cancellation); as well as oppose the use of your personal data for specific purposes (Opposition). These rights are known as ARCO rights.
                                        <br><br>

                                        You can revoke the consent that, in your case, you have granted us for the treatment of your personal data. However, it is important that you keep in mind
                                        that not in all cases we will be able to attend your request or terminate the use immediately, since it is possible that due to some legal obligation we will need to continue treating your personal data.
                                        Likewise, you should consider that for certain purposes, the revocation of your consent will mean that we cannot
                                        continue to provide the service you requested, or the conclusion of your relationship with us.
                                        
                                        <br><br>
                                        You may limit the use and disclosure of your personal information by registering with the Public Registry to Avoid Advertising, which is in charge of the Federal Consumer Attorney’s Office,
                                        in order that your personal data is not used to receive advertising or promotions of companies of goods or services. For more information about this record,
                                        You can consult the PROFECO Internet portal, or contact it directly.
                                        <br><br>
                                        To exercise any of the ARCO rights, revoke your consent or limit the use and disclosure of your personal information, you must send an email to energexcard@energexcard.com with the title "ARCO Rights"
                                        and must contain at least the following: (i) Name of the Holder of the Personal Data; (ii) Address, telephone and email to receive communications (iii) Documents proving your identity. In case of being a Legal Representative,
                                        the instrument from which their powers of representation derive; (iv) Clear and precise description of the personal data with respect to which the rights are sought; (v) Clear and precise description of the right you seek to exercise;
                                        (vi) Any other element or document that facilitates the location of personal data, as well as any other element that, in accordance with the legislation and the last Privacy Notice that are in force at the time of the presentation of your request.      
                                    </p>
                                    <br> 
                                    <b>The use of tracking technologies in our Internet portal</b>
                                    <p align="justify" style="font-size:14px;">
                                        <br>
                                        We inform you that on our website we use cookies to provide you with a better service and user experience when browsing our page. Cookies are identifiers that our server can send to your computer to identify the equipment that was used during the session. 
                                        Most Internet browsers are designed to accept these Cookies automatically. Additionally, you can deactivate the storage of Cookies or adjust your Internet browser to be informed before Cookies are stored on your computer. Cookies can be debugged by you manually as soon as you decide.
                                        <br><br>
                                        Your personal data may be transferred to third parties to (a) comply with current legal provisions; (b) in compliance with the injunction or court order; and (c) whenever necessary for the operation and operation of the Organization. In case of transfer of personal data, 
                                        this will always be carried out through legal figures and instruments that provide the level of protection and adequate security measures for such data.  </p>
                                    
                                    <br>
                                    <b>How can you find out about the changes to this privacy notice?</b>
                                    <p align="justify" style="font-size:14px;">
                                        <br>
                                        This privacy notice may undergo modifications, changes or updates derived from new legal requirements; of our own needs for the products or services we offer; of our privacy practices; 
                                        Changes in our business model, or other causes.<br><br>
                                        These modifications will be announced and made available to the public through our website www.energexpass.com section "Privacy Notice".
                                    
                                    </p>
                                    <br>
                                    <b> Where can you submit your complaints and complaints about the improper processing of your personal data?</b>
                                    <p align="justify" style="font-size:14px;">
                                    If you consider that your right to protection of personal data has been injured by any conduct of our employees or our actions or responses, you assume that in the processing of your personal data there is a violation of the provisions provided in the Federal Law on the Protection of Personal Data. 
                                    Personal Data Held by Individuals, may file the corresponding complaint or complaint with the IFAI, for more information visit www.ifai.org.mx </p>
                                    </p>
                                        
                                </p>
                            </div>
                        </div>
                        <br>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="check2" id="check2" required>
                            <label class="form-check-label" for="exampleCheck2">I have read and accept the privacy policies</label>
                        </div>
                        <br>
                        <a  href="logout" onclick="return checkSubmitBlock('crear_company');" class="btn btn-danger">
                            Cancel
                        </a>                 
                          <input type="button" name="previous" class="previous-form btn btn-default" value="Previous" />
                            <input type="button" name="next" class="next-form btn btn-info" value="Next" />
                </fieldset>
                <fieldset>
                <h3> Information Policies</h3>
                        <div id="scroll1">
                            <div class="content">
                                <p class="category">
                                    <br>
                                        <b>Policy Summary:</b>
                                        <p align="justify" style="font-size:14px;">
                                            Information must always be protected, whatever its way of being shared, communicated or stored.<br>
                                            Introduction: Information can exist in various forms: printed or written on paper, stored electronically, 
                                            transmitted by mail or by electronic means, shown in projections or orally in conversations.<br>
                                            Information security is the protection of information against a wide range of threats in order to guarantee business continuity, 
                                            minimize business risks and maximize the return on investments and business opportunities.
                                       </p>
                                       <br>
                                        
                                        <b>Scope:</b>
                                        <p align="justify" style="font-size:14px;">
                                            This policy supports the general policy of the Organization's Information Security Management System.
                                            <br>
                                            This policy is considered by all members of the organization.
                                        </p>
                                        <br> 
                                        
                                        <b>Information security objectives:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                        
                                            Understand and address operational and strategic information security risks so that they remain at acceptable levels for the organization. <br>
                                            The protection of the confidentiality of information related to customers and development plans. <br>
                                            The preservation of the integrity of accounting records.<br>
                                            Public access Web services and internal networks meet the required availability specifications.<br>
                                            Understand and cover the needs of all interested parties. 
                                        </p>
                                        <br>
                                        <b>Principles of information security:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                            
                                            This organization faces risk taking and tolerates those that, based on available information, are understandable,
                                            controlled and treated when necessary. The details of the methodology adopted for risk assessment and its treatment are described in the ISMS policy. <br>
                                            All staff will be informed and responsible for information security, as relevant to the performance of their work.<br>
                                            Financing will be available for the operational management of controls related to information security and management 
                                            processes for its implementation and maintenance. <br>
                                            Those fraud possibilities related to the abusive use of information systems within the global management of information systems will be taken into account.<br>
                                            Regular reports will be made available with information on the security situation.<br>
                                            Information security risks will be monitored and relevant measures will be taken when there are changes that imply an unacceptable level of risk.<br>
                                            The criteria for risk classification and acceptance are referenced in the ISMS policy.<br>
                                            Situations that may expose the organization to violation of laws and legal norms will not be tolerated.
                                        </p>
                                        <br>
                                        <b>Responsibilities:</b><br>
                                        <p align="justify" style="font-size:14px;">        
                                            The management team is responsible for ensuring that information security is properly managed throughout the organization.<br>
                                            Each manager is responsible for ensuring that the people who work under his control protect the information in accordance with the standards established by the organization.<br>
                                            The security officer advises the management team, provides specialized support to the organization's staff and ensures that reports on the information security situation are available.<br>
                                            Each staff member has the responsibility to maintain the security of information within the activities related to their work.
                                        </p>
                                        <br>
                                        <b>Key indicators:</b><br>
                                        <p align="justify" style="font-size:14px;">
                                            Information security incidents will not result in serious and unexpected costs, or a serious disruption of commercial services and activities.<br>
                                            Fraud losses will be detected and will remain within acceptable levels.<br>
                                            Customer acceptance of products or services will not be adversely affected by aspects related to information security.

                                        </p>
                                        <br> 
                                        <b>Related Policies:</b><br> 
                                        <p align="justify" style="font-size:14px;">
                                            Below are those policies that provide principles and guidance on specific aspects of information security:<br><br>
                                            Policy of the Information Security Management System (ISMS).<br>
                                            Physical access control policy.<br>
                                            Workplace cleaning policy.<br>
                                            Unauthorized software policy. <br>
                                            File download policy (external / internal network). <br>
                                            Backup policy. <br>
                                            Information exchange policy with other organizations. <br>
                                            Policy for the use of courier services. <br>
                                            Record retention policy. <br>
                                            Policy on the use of network services. <br>
                                            Policy for the use of information technology and mobile communications. <br>
                                            Teleworking Policy. <br>
                                            Policy on the use of cryptographic controls. <br>
                                            Compliance policy with legal provisions. <br>
                                            Software license usage policy. <br>
                                            Data protection and privacy policy. <br>
                                            At a lower level, the information security policy must be supported by other rules or procedures on specific issues that further enforce the application of information security controls and are normally structured to address the needs of certain groups within an organization or to cover certain topics. <br> <br>
                                            
                                            <b> Examples of these policy issues include: </b> <br>
                                            Access control. <br>
                                            Classification of information. <br>
                                            Physical and environmental security. <br> <br>
                                            <b> And more directly aimed at users: </b> <br>
                                            Acceptable use of assets. <br>
                                            Clean and clear screen desk. <br>
                                            The transfer of information. <br>
                                            Mobile devices and teleworking. <br>
                                            Restrictions on software installation and use. <br>
                                            Backup. <br>
                                            The transfer of information. <br>
                                            Protection against malware. <br>
                                            The management of technical vulnerabilities. <br>
                                            Cryptographic controls. <br>
                                            Security communications. <br>
                                            The privacy and protection of personally identifiable information. <br> <br>

                                            These policies / standards / procedures should be communicated to employees and interested external parties. The need for internal information security standards varies depending on the organizations. <br>
                                            When some of the information security rules or policies are distributed outside the organization, care should be taken not to disclose confidential information. Some organizations use other terms for these policy documents, 
                                            such as: rules, guidelines or rules. All of these policies should support the identification of risks through the provision of controls in relation to a reference point that can be used to identify deficiencies in the design and implementation of the systems,
                                            and the treatment of risks through possible Identification of appropriate treatments for localized vulnerabilities and threats.
                                            This identification and treatment of risks are part of the processes defined in the Principles section within the security policy or, as referenced in the example, are usually part of the ISMS policy itself, as noted below.
                                                
                                            <br><br>ISMS POLICY<br>
                                            
                                            In view of the importance for the proper development of business processes, information systems must be adequately protected.
                                            Reliable protection allows the organization to better perceive its interests and efficiently carry out its information security obligations. Inadequate protection affects the overall performance of a company and can 
                                            negatively affect the image, reputation and trust of customers, but also of investors who place their trust, for the strategic growth of our activities internationally. The objective of information security is to ensure 
                                            business continuity in the organization and minimize the risk of damage by preventing security incidents, as well as reducing its potential impact when unavoidable. To achieve this objective, the organization has developed
                                            a risk management methodology that allows regular analysis of the degree of exposure of our important assets against threats that may exploit certain vulnerabilities and introduce adverse impacts to the activities of our staff or to important processes. 
                                            of our organization. The success in the use of this methodology is based on the experience and contribution of all employees in the field of security, and through the communication of any relevant consideration to their direct managers in the 
                                            semiannual meetings established by the management, in order to of locating possible changes in the levels of protection and evaluating the most effective options in cost / benefit of risk management at all times, and as appropriate.
                                            The principles presented in the security policy that accompanies this policy were developed by the security information management group in order to ensure that future decisions are based on preserving the confidentiality, integrity and availability of the relevant 
                                            information of the organization. The organization has the collaboration of all employees in the implementation of the proposed security policies and directives. The daily use of computers by staff determines compliance with the requirements of these principles and an inspection process to confirm 
                                            that they are respected and met by the entire organization. In addition to this policy, and the organization's security policy, specific policies are available for the different activities.
                                            All current security policies will remain available on the organization's intranet and will be updated regularly. The access is direct from all workstations connected to the organization's network and by a mouse click from the main Web page in the Information Security section. 
                                            The objective of the policy is to protect the information assets of the organization against all internal and external threats and vulnerabilities, whether they occur deliberately or accidentally.
                                            
                                            THE EXECUTIVE DIRECTORATE OF THE COMPANY IS RESPONSIBLE FOR APPROVING AN INFORMATION SECURITY POLICY TO ENSURE THAT:
                                            The information will be protected against any unauthorized access.
                                            The confidentiality of information, especially that related to the personal data of employees and customers.
                                            The integrity of the information will be maintained in relation to the classification of the information (especially "internal use").
                                            The availability of information meets the relevant times for the development of critical business processes.
                                            The requirements of the laws and regulations in force are met, especially with the Law on Data Protection and Electronic Signature.
                                            Business continuity plans will be maintained, tested and updated at least annually.
                                            Safety training is met and updated sufficiently for all employees.
                                            All events related to information security, real as assumptions, will be communicated to the security officer and investigated.<br>

                                            In addition, support procedures are available that include the specific way in which the general guidelines indicated in the policies and by the designated managers must be undertaken.
                                            Compliance with this policy, as well as the information security policy and any procedure or documentation included within the ISMS documentation repository, is mandatory and concerns all personnel of the organization.
                                            The visits and external personnel that access our facilities are not exempt from the fulfillment of the obligations indicated in the ISMS documentation, and the internal personnel will observe their compliance.
                                            In any case, in case of doubt, clarification or for more information on the use of this policy and the application of its content, please consult by phone or e-mail the person responsible for the ISMS formally designated in the corporate organization chart.
                                            Signed Mr./Mrs. xxxxxxx, Executive Director.
                                        </p>
                                    </br>          
                                </p>
                            </div>
                        </div>
                        <br>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="check3" id="check3" required/>
                            <label class="form-check-label" for="exampleCheck3">I have read and accept the information policies</label>
                        </div>
                        <br>
                        <a  href="logout" onclick="return checkSubmitBlock('crear_company');" class="btn btn-danger">
                            Cancel
                        </a>
                        <input type="hidden" name="id" value="1">                 
                            <input type="button" name="previous" class="previous-form btn btn-default" value="Previous" />
                            <input type="submit" name="submit" class="submit btn btn-success" value="Finish" />
                </fieldset> 
            </form>
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

	<script type="text/javascript">
    $(document).ready(function(){
        var form_count = 1, previous_form, next_form, total_forms;
        total_forms = $("fieldset").length;
        $(".next-form").click(function(){
            previous_form = $(this).parent();
            next_form = $(this).parent().next();
            next_form.show();
            previous_form.hide();
            setProgressBarValue(++form_count);
        });
        $(".previous-form").click(function(){
            previous_form = $(this).parent();
            next_form = $(this).parent().prev();
            next_form.show();
            previous_form.hide();
            setProgressBarValue(--form_count);
        });
        setProgressBarValue(form_count);
        function setProgressBarValue(value){
            var percent = parseFloat(100 / total_forms) * value;
            percent = percent.toFixed();
            $(".progress-bar")
            .css("width",percent+"%")
            .html(percent+"%");
            }
        // Handle form submit and validation
        $( "#register_form" ).submit(function(event) {
            var error_message = '';
            var isChecked1 = document.getElementById('check1').checked;
            var isChecked2 = document.getElementById('check2').checked;
            var isChecked3 = document.getElementById('check3').checked;
            
            if(!isChecked1){
                error_message+="Please accept the terms and conditions. \t";
            }
            if(!isChecked2){
                error_message+="<br>Please accept the privacy policies";
            }
            if(!isChecked3){
                error_message+="<br>Please accept the information policies";
            }
            // Display error if any else submit form
            if(error_message) {
                $('.alert-success').removeClass('hide').html(error_message);
            return false;
            } 
            else {
                return true;
            }
        });
    });

	</script>
    
   
</html>

</body>