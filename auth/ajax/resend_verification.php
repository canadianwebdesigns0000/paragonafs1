<?php
session_start();
include '../config.php';
$smtp = include '../smtp.php';
// error_reporting(0);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../assets/PHPMailer/PHPMailerAutoload.php';

// Function to Encrypt Decrypt String
function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    // Update your secret key before use
    $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/';
    // Update your secret iv before use
    $secret_iv = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

// Check if the key is provided
if (isset($_POST['key'])) {

    $key = $_POST['key'];

    // Retrieve user information from the database using the key
    $query = 'SELECT * FROM `users` WHERE `key` = ?';
    $statement = $db->prepare($query);
    $statement->execute(array($key));

    if ($statement->rowCount() > 0) {
        // User found, resend the verification email
        $userData = $statement->fetch(PDO::FETCH_ASSOC);

        // Construct the email template
        $template = file_get_contents('../email_confirm_template.php');
        $body = '<a href='.$baseUrl.'/auth/confirm.php?key='.$key.' mc:edit data-button data-text-style="Buttons" style="font-family:\'Barlow\',Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;font-weight:700;font-style:normal;color:#FFFFFF;text-decoration:none;letter-spacing:0px;padding: 15px 35px 15px 35px;display: inline-block;"><span>VERIFY EMAIL</span></a>';

        // Send the email
        $mail = new PHPMailer(); // create a new object
        $mail->IsHTML(true);
        $mail->WordWrap = 50;  // Set word wrap to 50 characters

        // Configure SMTP settings if needed
        if ($GLOBALS['SMTP'] == false) {
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPAuth   = true; // authentication enabled
            $mail->SMTPSecure = $smtp['encryption']; // secure transfer enabled REQUIRED for Gmail
            $mail->Host       = $smtp['host'];
            $mail->Port       = $smtp['port']; // or 587
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->From       = $smtp['from']['address'];
            $mail->FromName   = $smtp['from']['name'];
        } else {
            $mail->SetFrom('no-reply@paragonafs.ca', 'Paragon AFS');
        }

        $mail->addAddress(encrypt_decrypt("decrypt", $userData['email']));
        $mail->Subject = 'Confirm Your Email Address';
        $mail->Body = str_replace('#BODY#', $body, $template);

        if (!$mail->send()) {
            $output = responseError($mail->ErrorInfo);
        } else {
            $output = responseSuccess('Mail sent to your email. Please verify your email to get Registered', '', '');
        }
    } else {
        // User not found with the provided key
        $output = responseError('User not found');
    }
} else {
    // Key not provided
    $output = responseError('Key not provided');
}

// Return the response
echo json_encode($output);
?>