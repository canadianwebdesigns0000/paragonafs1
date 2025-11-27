<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

function sendEmail($to = [], $subject = '', $body = '') {
    $mail = new PHPMailer(true);

    echo "Starting email send process...\n";

    try {
        // Enable verbose debug output
        // $mail->SMTPDebug = 2; // 0 = off, 1 = client messages, 2 = client and server messages
        // $mail->Debugoutput = 'html'; // Output as HTML for readability

        // SMTP settings
        // Host
        $mail->isSMTP();
        $mail->Host = 'mail.mnd.uvd.mybluehost.me';
        $mail->SMTPAuth = true;
        $mail->Username = 'testemail@mnd.uvd.mybluehost.me';
        $mail->Password = '@Nexus369';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        // Sender & recipient
        $mail->setFrom('testemail@mnd.uvd.mybluehost.me', 'Lance');
        
        foreach ($to as $recipient) {
            $mail->addAddress($recipient);
        }

        // Content - use provided HTML body
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "Test";
        $mail->Body = $body;
        $mail->AltBody = strip_tags($body); // fallback plain text
 
        $mail->send();

        return true;
    } catch (Exception $e) {
        return "Email could not be sent. Error: {$mail->ErrorInfo}";
    }
}
