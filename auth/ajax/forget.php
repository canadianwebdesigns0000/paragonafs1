<?php
    session_start();
    include '../config.php';
    $url = $baseUrl;
    $smtp = include '../smtp.php';
    require '../assets/PHPMailer/PHPMailerAutoload.php';

    $email = post('emailForget');

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

    // SELECT MATCH FROM THE DATABASE
    $query      = 'SELECT * FROM `users` where email=? ';
    $parameters = array(encrypt_decrypt('encrypt', $email));
    $statement  = $db->prepare($query);
    $statement->execute($parameters);

if ($statement->rowCount() > 0) {

    $data = $statement->fetch(PDO::FETCH_ASSOC);

    // Forget Key generation Login
    $forgetKey = sha1($encryptionKey . $email);

    $statement = $db->prepare('UPDATE `users` SET forget_key = ?  where email=? ');
    $statement->execute(array($forgetKey, encrypt_decrypt('encrypt', $email)));

    // Email verification------------
    $mail = new PHPMailer(); // create a new object
    $mail->IsHTML(true);
    $mail->WordWrap = 50;

    // If Smtp is set true. Then the email will be sent using smtp
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

    $mail->addAddress(encrypt_decrypt('decrypt', $data['email']));     // Add a recipient

    // Fetch email template
    $template = file_get_contents('../email_template.php');
    $body = '<a href='.$url.'/auth/reset_password.php?key='.$forgetKey.' mc:edit data-button data-text-style="Buttons" style="font-family:\'Barlow\',Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;font-weight:700;font-style:normal;color:#FFFFFF;text-decoration:none;letter-spacing:0px;padding: 15px 35px 15px 35px;display: inline-block;"><span>RESET PASSWORD</span></a>';
    
    $mail->Subject = 'Reset Password';
    $mail->Body    = str_replace('#BODY#', $body, $template);

    if (!$mail->send()) {
        $output = responseError($mail->ErrorInfo);
    }
    else
    {
        $output = responseSuccess('Reset password mail sent to ' . encrypt_decrypt('decrypt', $data['email']) . ". Please check also on your Email Spam!", '', '');
    }
} else {

    $output = responseError('This username is not registered. Please type the correct username');
}
echo json_encode($output);
