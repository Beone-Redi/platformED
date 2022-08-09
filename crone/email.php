<?php
 error_reporting(E_ALL);
 ini_set("display_errors", 1);
// Load Composer's autoloader
//Import the PHPMailer class into the global namespace
use PHPMailer\PHPMailer\PHPMailer;

class sendmail
{
    public function enviarmail($msj,$titulo)
    {
        require 'vendor/autoload.php'; 
        $mail = new PHPMailer;
        try 
        {
            $mail->SMTPDebug      = 0;
            $mail->Host           = 'gocard.mx';
            $mail->Port           = 587;
            $mail->SMTPAuth       = TRUE;
            $mail->SMTPSecure     = 'tls';
            $mail->isHTML(true);
            $mail->Username       = 'notifica@gocard.mx';
            $mail->Password       = 'Xy6-FPf}-t5T';
            $mail->Subject        = $titulo;
            $mail->SMTPOptions    =
                [
                    'ssl'   =>
                    [
                        'verify_peer'       => FALSE,
                        'verify_peer_name'  => FALSE,
                        'allow_self_signed' => TRUE
                    ]
                ]; 
            $mail->isSMTP();
            $mail->Body   = $msj; // Se carga el mensaje del email.
            $mail->setFrom( 'notifica@gocard.mx', '' );
            $mail->addAddress( 'salvador@hotpay.mx', 'Salvador Villalobos' );
            $mail->addAddress( 'jose@hotpay.mx', 'Jose ' );
            //$mail->addAddress( 'smarquez@pagos.pro','Sebastian Marquez' );
            //$mail->addAddress( 'sergio@gocard.mx', 'Sergio Marquez' );
            //$mail->addAttachment('test.txt');
            //$mail->msgHTML(file_get_contents('message.html'), _DIR_);

            if ( !$mail->send() )
            {            
                return $mail->ErrorInfo;
            }   
            else 
            {
            //echo 'Message sent!';
                return 202;
            }
        } 
        catch (Exception $EX) 
        {
            //echo 'Mensaje de Error: '. $EX->getMessage() ."\n";
            return $EX->getMessage();
        }
    }

  
}
