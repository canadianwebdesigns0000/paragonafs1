<?php
session_start();

// email SESSION CHECK SET OR NOT
if (!isset($_SESSION['email'])) {
    header('location: ./auth');
    exit();
}

/**
 * This shows how to send via Google's Gmail servers using XOAUTH2 authentication.
 */

//Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
//Alias the League Google OAuth2 provider class
use League\OAuth2\Client\Provider\Google;

//Load dependencies from composer
//If this causes an error, run 'composer install'
require 'vendor/autoload.php';

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('America/Toronto');

$servername = "localhost";
$username = "paragonafs";
$password = "W6j4jCV9zJj8Tk8";
$dbname = "paragonafs";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";


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


$income_delivery1 = isset($_POST['income_delivery']) ? $_POST['income_delivery'] : "";
$delivery_hst1 = isset($_POST['delivery_hst']) ? $_POST['delivery_hst'] : '';
$hst_number1 = isset($_POST['hst_number']) ? $_POST['hst_number'] : '';
$hst_access_code1 = isset($_POST['hst_access_code']) ? $_POST['hst_access_code'] : '';
$hst_start_date1 = isset($_POST['hst_start_date']) ? $_POST['hst_start_date'] : '';
$hst_end_date1 = isset($_POST['hst_end_date']) ? $_POST['hst_end_date'] : '';

$encrypted_hst_number1 = encrypt_decrypt('encrypt', $hst_number1);
$encrypted_hst_access_code1 = encrypt_decrypt('encrypt', $hst_access_code1);
$encrypted_hst_start_date1 = encrypt_decrypt('encrypt', $hst_start_date1);
$encrypted_hst_end_date1 = encrypt_decrypt('encrypt', $hst_end_date1);

if ($income_delivery1 == 'No') {
    $delivery_hst1 = '';
    $encrypted_hst_number1 = '';
    $encrypted_hst_access_code1 = '';
    $encrypted_hst_start_date1 = '';
    $encrypted_hst_end_date1 = '';
}

if ($delivery_hst1 == 'No') {
    $encrypted_hst_number1 = '';
    $encrypted_hst_access_code1 = '';
    $encrypted_hst_start_date1 = '';
    $encrypted_hst_end_date1 = '';
}



$spouse_income_delivery1 = isset($_POST['spouse_income_delivery']) ? $_POST['spouse_income_delivery'] : "";
$spouse_delivery_hst1 = isset($_POST['spouse_delivery_hst']) ? $_POST['spouse_delivery_hst'] : '';
$spouse_hst_number1 = isset($_POST['spouse_hst_number']) ? $_POST['spouse_hst_number'] : '';
$spouse_hst_access_code1 = isset($_POST['spouse_hst_access_code']) ? $_POST['spouse_hst_access_code'] : '';
$spouse_hst_start_date1 = isset($_POST['spouse_hst_start_date']) ? $_POST['spouse_hst_start_date'] : '';
$spouse_hst_end_date1 = isset($_POST['spouse_hst_end_date']) ? $_POST['spouse_hst_end_date'] : '';

$spouse_encrypted_hst_number1 = encrypt_decrypt('encrypt', $spouse_hst_number1);
$spouse_encrypted_hst_access_code1 = encrypt_decrypt('encrypt', $spouse_hst_access_code1);
$spouse_encrypted_hst_start_date1 = encrypt_decrypt('encrypt', $spouse_hst_start_date1);
$spouse_encrypted_hst_end_date1 = encrypt_decrypt('encrypt', $spouse_hst_end_date1);


if ($spouse_income_delivery1 == 'No') {
    $spouse_delivery_hst1 = '';
    $spouse_encrypted_hst_number1 = '';
    $spouse_encrypted_hst_access_code1 = '';
    $spouse_encrypted_hst_start_date1 = '';
    $spouse_encrypted_hst_end_date1 = '';
}

if ($spouse_delivery_hst1 == 'No') {
    $spouse_encrypted_hst_number1 = '';
    $spouse_encrypted_hst_access_code1 = '';
    $spouse_encrypted_hst_start_date1 = '';
    $spouse_encrypted_hst_end_date1 = '';
}

// SQL query to insert values into the tax_information table
$sql = 'UPDATE tax_information SET income_delivery=?, delivery_hst=?, hst_number=?, hst_access_code=?, hst_start_date=?, hst_end_date=?, spouse_income_delivery=?, spouse_delivery_hst=?, spouse_hst_number=?, spouse_hst_access_code=?, spouse_hst_start_date=?, spouse_hst_end_date=? WHERE email=?';
$stmt= $conn->prepare($sql);
$stmt->bind_param("sssssssssssss", $income_delivery1, $delivery_hst1, $encrypted_hst_number1, $encrypted_hst_access_code1, $encrypted_hst_start_date1, $encrypted_hst_end_date1, $spouse_income_delivery1, $spouse_delivery_hst1, $spouse_encrypted_hst_number1, $spouse_encrypted_hst_access_code1, $spouse_encrypted_hst_start_date1, $spouse_encrypted_hst_end_date1, $_SESSION['email']);

if ($stmt->execute()) {
    echo "Update successful!";
} else {
    echo "Error updating record: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>