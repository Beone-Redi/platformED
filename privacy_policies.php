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
                        <h4 class="title">Privacy policies</h4>
                            <p class="category">
                            Overview of information related to privacy policies. </p>
                            </div>
                            <div class="content">
                                <p class="category">
                                    
                                    <br>
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
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
<?php
include 'footer.php';