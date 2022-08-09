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
                        <h4 class="title">Information Policies</h4>
                            <p class="category">View of information related to policies and use of information.</p>
                                <p class="category">
                                    </br>
                                    <?php if ( $_SESSION['EMPRESA'] === '1' )
                                    {?>

                                    <a href="create_company?scr=2" class="btn btn-default btn-sm" >
                                        <i class="pe-7s-plus" style="font-size:16px;"></i> Company
                                    </a>
                                    <?php } ?>
                                </p>
                            </div>
                            <div class="content">
                                <p class="category">
                                    <h1>INFORMATION SECURITY POLICY</h1>
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
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
<?php
include 'footer.php';