<?php
    session_start();
    include '../config.php';
    $smtp = include '../smtp.php';
    // error_reporting(0);
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require '../assets/PHPMailer/PHPMailerAutoload.php';

    $captcha   = post('g-recaptcha-response');

    $googleUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $secret     = $secretKey;
    $ip         = $_SERVER['REMOTE_ADDR'];
    $url        = $googleUrl . '?secret=' . $secret . '&response=' . $captcha . '&remoteip=' . $ip;
    $res        = getCurlData($url);
    $res        = json_decode($res, true);

    // Set base url
    // $baseUrl = preg_replace('/\/ajax\/register.php/', '', $_SERVER['PHP_SELF']);

// Server side validation

if (empty($captcha)) {
    $output = responseError('Please Enter the Captcha');
    die(json_encode($output));
}

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

$output = [];
$input = [];

$input['firstNameRegister'] = post('firstNameRegister');
$input['lastNameRegister'] = post('lastNameRegister');
$input['phoneRegister'] = post('phoneRegister');
$input['emailRegister'] = post('emailRegister');
$input['passwordRegister'] = post('passwordRegister');
$input['cpasswordRegister'] = post('cpasswordRegister');

$output = responseFormErrors($input);

if ($output['error']) {

    foreach ($output['errors'] as $key => $out) {

        $output['errors'][$key] = preg_replace('/Register/', '', $out[0]);

        if($key == 'cpasswordRegister') {
            $output['errors'][$key] = preg_replace('/c/', 'confirm ', $output['errors'][$key]);
        }
    }

    echo json_encode($output);
    die;
}

// Check password and confirm password match or not
if ($input['cpasswordRegister'] != $input['passwordRegister']) {
    $output['errors']['passwordRegister'] = 'Password and confirm password do not match';
    $output['status'] = 'fail';
}
else if (!filter_var($input['emailRegister'], FILTER_VALIDATE_EMAIL)) {
    $output['errors']['emailRegister'] = 'Enter correct Email ID';
    $output['status'] = 'fail';
    echo json_encode($output);
    die;
}

// Insert the data into the database
else {

    // SELECT MATCH FROM THE DATABASE
    $query     = 'SELECT * FROM `users` where email=?';
    $statement = $db->prepare($query);
    $statement->execute(array(encrypt_decrypt('encrypt', strtolower($input['phoneRegister']))));

    if ($statement->rowCount() > 0) {

        $output['errors']['emailRegister'] = 'Email Already exists. Try another Email.';
        $output['status'] = 'fail';
        echo json_encode($output);
        die;

    } else {

        // Generate key for email verification
        $key = sha1($encryptionKey . $input['emailRegister']);

        // Encrypt password according to encryption type defined in config.php
        if($encryptionType == 'sha1') {
            $input['passwordRegister'] = sha1($input['passwordRegister']);
        }
        elseif ($encryptionType == 'md5') {
            $input['passwordRegister'] = md5($input['passwordRegister']);
        }
        
        $query      = 'INSERT INTO `users` SET first_name=?, last_name=?, phone=?, password=?, email=?, `key`=?, status = ?, email_verified =?';
        $parameters = array($input['firstNameRegister'], $input['lastNameRegister'], encrypt_decrypt('encrypt', strtolower($input['phoneRegister'])), $input['passwordRegister'], encrypt_decrypt('encrypt', strtolower($input['emailRegister'])), $key, 'enable', 'no');

        $statement = $db->prepare($query);
        $statement->execute($parameters);

        // Email verification
        $mail = new PHPMailer(); // create a new object
        $mail->IsHTML(true);
        $mail->WordWrap = 50;  // Set word wrap to 50 characters

        // Check E-mail verification is true or false
        if($emailVerification) {
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
            }
            else {
                $mail->SetFrom('no-reply@paragonafs.ca', 'Paragon AFS');
            }

         $template = file_get_contents('../email_confirm_template.php');
        
        
        
           $confirm_link = htmlspecialchars($baseUrl . "/auth/confirm.php?key=" . urlencode($key), ENT_QUOTES, 'UTF-8');

$body = '<a href="' . $confirm_link . '" style="font-family:\'Barlow\',Arial,Helvetica,sans-serif;font-size:16px;line-height:20px;font-weight:700;color:#FFFFFF;text-decoration:none;letter-spacing:0px;padding: 15px 35px;display: inline-block;"><span>VERIFY EMAIL</span></a>';
            $mail->addAddress($input['emailRegister']);
            $mail->Subject = 'Confirm Your Email Address';
            $mail->Body    = str_replace('#BODY#', $body, $template);

            if (!$mail->send()) {
                $output = responseSuccess($mail->ErrorInfo, '', '');

            } else {
                $output = responseSuccess('success', '', '');

            }

            // $no_file_submitted = 'No';
            // // SQL query to insert values into the tax_information table
            // $sql = 'INSERT INTO tax_information SET is_file_submit=?, file_submit_date=NOW() WHERE email=?';
            // $parameters2 = array($no_file_submitted, encrypt_decrypt('encrypt', strtolower($input['emailRegister'])));
            // $stmt = $db->prepare($sql);
            // $stmt->execute($parameters2);
            

            // End email verification
            $output = responseSuccess('Mail sent to your email. Please verify your email to get Registered', '', '');
        }
        else {

            $output = responseSuccess('User successfully registered', '', '');

        }
    }
}

echo json_encode($output);

?>

