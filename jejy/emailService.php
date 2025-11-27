<?php
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host = 'mail.mnd.uvd.mybluehost.me'; 
    $mail->SMTPAuth = true;
    $mail->Username = 'testemail@mnd.uvd.mybluehost.me';
    $mail->Password = '@Nexus369';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
    $mail->Port = 465;

    // Sender & recipient
    $mail->setFrom('testemail@mnd.uvd.mybluehost.me', 'Lance');
    $mail->addAddress('lanceruzel2@gmail.com');
    $mail->addAddress('lance.canadianwebdesigns@gmail.com');
    $mail->addAddress('dev@canadianwebdesigns.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from cPanel SMTP';
    $mail->Body = '<b>Hello!</b> This email was sent using PHPMailer & cPanel SMTP.';

    $mail->send();
    echo "Email sent successfully!";
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}
