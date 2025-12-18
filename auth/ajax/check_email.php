<?php
    session_start();
    include '../config.php';

    $email = post('email');

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
    $query      = 'SELECT * FROM `users` where email=?';
    $parameters = array(encrypt_decrypt('encrypt', strtolower($email)));
    $statement  = $db->prepare($query);
    $statement->execute($parameters);

if ($statement->rowCount() > 0) {
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    
    $output = responseCheckEmailSuccess($data['first_name'], $email);
} else {
    $output = responseCheckEmail('No User Details', $email);
}
    echo json_encode($output);
?>