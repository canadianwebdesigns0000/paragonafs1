<html>
<head>
<title>Demo Encrypt Decrypt String PHP - AllPHPTricks.com</title>
<style>
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
}
</style>
</head>
<body>

<div style="width:700px; margin:50 auto;">
<h1>Demo Encrypt Decrypt String PHP</h1>

<?php
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

// Plain String
$string = "AllPHPTricks";
echo "<p><strong>Plain String:</strong> ". $string . "</p>";

//Encrypted String
$encrypted_string = encrypt_decrypt('encrypt', $string);
echo "<p><strong>Encrypted String:</strong> ". $encrypted_string . "</p>";

//Decrypted String
$decrypted_string = encrypt_decrypt('decrypt', $encrypted_string);
echo "<p><strong>Decrypted String:</strong> ". $decrypted_string . "</p>";
?>

</div>    
</body>
</html>