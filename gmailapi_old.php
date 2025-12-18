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
// date_default_timezone_set('America/New_York');

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

// $firstname1 = isset($_POST['firstName']) ? $_POST['firstName'] : '';
// $lastName1 = isset($_POST['lastName']) ? $_POST['lastName'] : '';
// $gender1 = isset($_POST['gender']) ? $_POST['gender'] : '';
// $ship_address1 = isset($_POST['ship_address']) ? $_POST['ship_address'] : '';
// $locality1 = isset($_POST['locality']) ? $_POST['locality'] : '';
// $state1 = isset($_POST['state']) ? $_POST['state'] : '';
// $postcode1 = isset($_POST['postcode']) ? $_POST['postcode'] : '';
// $country1 = isset($_POST['country']) ? $_POST['country'] : '';
// $birth_date1 = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';
// $sin_number1 = isset($_POST['sin_number']) ? $_POST['sin_number'] : '';
// $phone1 = isset($_POST['phone']) ? $_POST['phone'] : '';
// $email1 = isset($_POST['email']) ? $_POST['email'] : '';
// $another_province1 = isset($_POST['another_province']) ? $_POST['another_province'] : '';
// $move_date1 = isset($_POST['move_date']) ? $_POST['move_date'] : '';
// $move_from1 = isset($_POST['move_from']) ? $_POST['move_from'] : '';
// $move_to1 = isset($_POST['move_to']) ? $_POST['move_to'] : '';
// if ($another_province1 == 'No') {
//     $move_date1 = '';
//     $move_from1 = '';
//     $move_to1 = '';
// }

// $first_fillingtax1 = isset($_POST['first_fillingtax']) ? $_POST['first_fillingtax'] : '';
// $canada_entry1 = isset($_POST['canada_entry']) ? $_POST['canada_entry'] : '';
// $birth_country1 = isset($_POST['birth_country']) ? $_POST['birth_country'] : '';
// $year11 = isset($_POST['year1']) ? $_POST['year1'] : '';
// $year1_income1 = isset($_POST['year1_income']) ? $_POST['year1_income'] : '';
// $year21 = isset($_POST['year2']) ? $_POST['year2'] : '';
// $year2_income1 = isset($_POST['year2_income']) ? $_POST['year2_income'] : '';
// $year31 = isset($_POST['year3']) ? $_POST['year3'] : '';
// $year3_income1 = isset($_POST['year3_income']) ? $_POST['year3_income'] : '';
// $file_paragon1 = isset($_POST['file_paragon']) ? $_POST['file_paragon'] : '';
// $years_tax_return1 = isset($_POST['years_tax_return']) ? $_POST['years_tax_return'] : '';
// if ($first_fillingtax1 == 'Yes') {
//     $file_paragon1 = '';
//     $years_tax_return1 = '';
// } else {
//     $canada_entry1 = '';
//     $birth_country1 = '';
//     $year11 = '';
//     $year1_income1 = '';
//     $year21 = '';
//     $year2_income1 = '';
//     $year31 = '';
//     $year3_income1 = '';
// }

// $marital_status1 = isset($_POST['marital_status']) ? $_POST['marital_status'] : '';
// $spouse_firstname1 = isset($_POST['spouse_firstname']) ? $_POST['spouse_firstname'] : '';
// $spouse_lastname1 = isset($_POST['spouse_lastname']) ? $_POST['spouse_lastname'] : '';
// $spouse_date_birth1 = isset($_POST['spouse_date_birth']) ? $_POST['spouse_date_birth'] : '';
// $date_marriage1 = isset($_POST['date_marriage']) ? $_POST['date_marriage'] : '';
// $spouse_annual_income1 = isset($_POST['spouse_annual_income']) ? $_POST['spouse_annual_income'] : '';
// $residing_canada1 = isset($_POST['residing_canada']) ? $_POST['residing_canada'] : '';
// $have_child1 = isset($_POST['have_child']) ? $_POST['have_child'] : "";
// $marital_change1 = isset($_POST['marital_change']) ? $_POST['marital_change'] : "";

// $spouse_sin1 = isset($_POST['spouse_sin']) ? $_POST['spouse_sin'] : "";
// $spouse_phone1 = isset($_POST['spouse_phone']) ? $_POST['spouse_phone'] : "";
// $spouse_email1 = isset($_POST['spouse_email']) ? $_POST['spouse_email'] : "";
// $spouse_file_tax1 = isset($_POST['spouse_file_tax']) ? $_POST['spouse_file_tax'] : "";
// $spouse_first_tax1 = isset($_POST['spouse_first_tax']) ? $_POST['spouse_first_tax'] : "";
// $spouse_canada_entry1 = isset($_POST['spouse_canada_entry']) ? $_POST['spouse_canada_entry'] : "";
// $spouse_birth_country1 = isset($_POST['spouse_birth_country']) ? $_POST['spouse_birth_country'] : "";
// $spouse_year11 = isset($_POST['spouse_year1']) ? $_POST['spouse_year1'] : "";
// $spouse_year1_income1 = isset($_POST['spouse_year1_income']) ? $_POST['spouse_year1_income'] : "";
// $spouse_year21 = isset($_POST['spouse_year2']) ? $_POST['spouse_year2'] : "";
// $spouse_year2_income1 = isset($_POST['spouse_year2_income']) ? $_POST['spouse_year2_income'] : "";
// $spouse_year31 = isset($_POST['spouse_year3']) ? $_POST['spouse_year3'] : "";
// $spouse_year3_income1 = isset($_POST['spouse_year3_income']) ? $_POST['spouse_year3_income'] : "";
// $spouse_file_paragon1 = isset($_POST['spouse_file_paragon']) ? $_POST['spouse_file_paragon'] : "";
// $spouse_years_tax_return1 = isset($_POST['spouse_years_tax_return']) ? $_POST['spouse_years_tax_return'] : "";

// if ($marital_status1  == 'Single') {
//     $spouse_firstname1 = '';
//     $spouse_lastname1 = '';
//     $spouse_date_birth1 = '';
//     $date_marriage1 = '';
//     $spouse_annual_income1 = '';
//     $residing_canada1 = '';
//     $have_child1 = '';
//     $marital_change1 = '';
//     $spouse_sin1 = '';
//     $spouse_phone1 = '';
//     $spouse_file_tax1 = '';
//     $spouse_canada_entry1 = '';
//     $spouse_birth_country1 = '';
//     $spouse_year11 = '';
//     $spouse_year1_income1 = '';
//     $spouse_year21 = '';
//     $spouse_year2_income1 = '';
//     $spouse_year31 = '';
//     $spouse_year3_income1 = '';
//     $spouse_file_paragon1 = '';
//     $spouse_years_tax_return1 = '';
// } else if ($marital_status1  == 'Married' || $marital_status1  == 'Common in Law') {
//     $marital_change1 = '';
// } else {
//     $spouse_firstname1 = '';
//     $spouse_lastname1 = '';
//     $spouse_date_birth11 = '';
//     $date_marriage11 = '';
//     $spouse_annual_income11 = '';
//     $residing_canada1 = '';
//     $have_child1 = '';
//     $spouse_sin1 = '';
//     $spouse_phone1 = '';
//     $spouse_file_tax1 = '';
//     $spouse_canada_entry1 = '';
//     $spouse_birth_country1 = '';
//     $spouse_year11 = '';
//     $spouse_year1_income1 = '';
//     $spouse_year21 = '';
//     $spouse_year2_income1 = '';
//     $spouse_year31 = '';
//     $spouse_year3_income1 = '';
//     $spouse_file_paragon1 = '';
//     $spouse_years_tax_return1 = '';
// }

// $child_first_name1 = isset($_POST['data']) ? json_encode($_POST['data']) : '';
// $first_time_buyer1 = isset($_POST['first_time_buyer']) ? $_POST['first_time_buyer'] : "";


// $id_proof1 = isset($_POST['id_proof']) ? $_POST['id_proof'] : '';
// $direct_deposits1 = isset($_POST['direct_deposits']) ? $_POST['direct_deposits'] : '';
// $college_receipt1 = isset($_POST['college_receipt']) ? $_POST['college_receipt'] : '';
// $t_slips1 = isset($_POST['t_slips']) ? $_POST['t_slips'] : '';
// // $rent_address1 = isset($_POST['group-a']) ? json_encode($_POST['group-a']) : "";

// $id_proof_text1 = !empty($id_proof1) ? implode("<br>", explode("<br>", $id_proof1)) : "";
// $direct_deposit_text1 = !empty($direct_deposits1) ? implode("<br>", explode("<br>", $direct_deposits1)) : "";
// $college_text1 = !empty($college_receipt1) ? implode("<br>", explode("<br>", $college_receipt1)) : "";
// $t_slip_text1 = !empty($t_slips1) ? implode("<br>", explode("<br>", $t_slips1)) : "";

// $tax_summary1 = isset($_POST['tax_summary']) ? $_POST['tax_summary'] : '';
// $tax_summary_text1 = !empty($tax_summary1) ? implode("<br>", explode("<br>", $tax_summary1)) : "";
// // $summary_expenses1 = isset($_POST['summary_expenses']) ? $_POST['summary_expenses'] : "";
// $additional_docs1 = isset($_POST['additional_docs']) ? $_POST['additional_docs'] : '';
// $additional_docs_text1 = !empty($additional_docs1) ? implode("<br>", explode("<br>", $additional_docs1)) : "";
// // $message_us1 = isset($_POST['message_us']) ? $_POST['message_us'] : '';

// $income_delivery1 = isset($_POST['income_delivery']) ? $_POST['income_delivery'] : "";
// $delivery_hst1 = isset($_POST['delivery_hst']) ? $_POST['delivery_hst'] : '';
// $hst_number1 = isset($_POST['hst_number']) ? $_POST['hst_number'] : '';
// $hst_access_code1 = isset($_POST['hst_access_code']) ? $_POST['hst_access_code'] : '';
// $hst_start_date1 = isset($_POST['hst_start_date']) ? $_POST['hst_start_date'] : '';
// $hst_end_date1 = isset($_POST['hst_end_date']) ? $_POST['hst_end_date'] : '';

// $encrypted_hst_number1 = encrypt_decrypt('encrypt', $hst_number1);
// $encrypted_hst_access_code1 = encrypt_decrypt('encrypt', $hst_access_code1);
// $encrypted_hst_start_date1 = encrypt_decrypt('encrypt', $hst_start_date1);
// $encrypted_hst_end_date1 = encrypt_decrypt('encrypt', $hst_end_date1);




// $spouse_income_delivery1 = isset($_POST['spouse_income_delivery']) ? $_POST['spouse_income_delivery'] : "";
// $spouse_delivery_hst1 = isset($_POST['spouse_delivery_hst']) ? $_POST['spouse_delivery_hst'] : '';
// $spouse_hst_number1 = isset($_POST['spouse_hst_number']) ? $_POST['spouse_hst_number'] : '';
// $spouse_hst_access_code1 = isset($_POST['spouse_hst_access_code']) ? $_POST['spouse_hst_access_code'] : '';
// $spouse_hst_start_date1 = isset($_POST['spouse_hst_start_date']) ? $_POST['spouse_hst_start_date'] : '';
// $spouse_hst_end_date1 = isset($_POST['spouse_hst_end_date']) ? $_POST['spouse_hst_end_date'] : '';

// $spouse_encrypted_hst_number1 = encrypt_decrypt('encrypt', $spouse_hst_number1);
// $spouse_encrypted_hst_access_code1 = encrypt_decrypt('encrypt', $spouse_hst_access_code1);
// $spouse_encrypted_hst_start_date1 = encrypt_decrypt('encrypt', $spouse_hst_start_date1);
// $spouse_encrypted_hst_end_date1 = encrypt_decrypt('encrypt', $spouse_hst_end_date1);



// $yes_file_submitted = 'Yes';
// // SQL query to insert values into the tax_information table
// $sql = 'UPDATE tax_information SET is_file_submit=? WHERE email=?';
// $stmt= $conn->prepare($sql);
// $stmt->bind_param("ss", $yes_file_submitted, $_SESSION['email']);
// $stmt->execute();


// $sql = "UPDATE tax_information SET (first_name, last_name, gender, ship_address, locality, state, postcode, country, birth_date, sin_number, phone, email, another_province, move_date, move_from, move_to, first_fillingtax, canada_entry, birth_country, year1, year1_income, year2, year2_income, year3, year3_income, file_paragon, years_tax_return, marital_status, spouse_first_name, spouse_last_name, spouse_date_birth, date_marriage, spouse_annual_income, residing_canada, have_child, marital_change, spouse_sin, spouse_phone, spouse_email, spouse_file_tax, spouse_first_tax, spouse_canada_entry, spouse_birth_country, spouse_year1, spouse_year1_income, spouse_year2, spouse_year2_income, spouse_year3, spouse_year3_income, spouse_file_paragon, spouse_years_tax_return, child_first_name, first_time_buyer, direct_deposits, id_proof, college_receipt, t_slips, rent_address, tax_summary, income_delivery, summary_expenses, delivery_hst, hst_number, hst_access_code, hst_start_date, hst_end_date, additional_docs, message_us)
// VALUES ('$firstname1', '$lastName1', '$gender1', '$ship_address1', '$locality1', '$state1', '$postcode1', '$country1', '$birth_date1', '$sin_number1', '$phone1', '$email1', '$another_province1', '$move_date1', '$move_from1', '$move_to1', '$first_fillingtax1', '$canada_entry1', '$birth_country1', '$year11', '$year1_income1', '$year21', '$year2_income1', '$year31', '$year3_income1', '$file_paragon1', '$years_tax_return1', '$marital_status1', '$spouse_firstname1', '$spouse_lastname1', '$spouse_date_birth1', '$date_marriage1', '$spouse_annual_income1', '$residing_canada1', '$have_child1', '$marital_change1', '$spouse_sin1', '$spouse_phone1', '$spouse_email1', '$spouse_file_tax1', '$spouse_first_tax1', '$spouse_canada_entry1', '$spouse_birth_country1', '$spouse_year11', '$spouse_year1_income1', '$spouse_year21', '$spouse_year2_income1', '$spouse_year31', '$spouse_year3_income1', '$spouse_file_paragon1', '$spouse_years_tax_return1', '$child_first_name1', '$first_time_buyer1', '$direct_deposit_text1', '$id_proof_text1', '$college_text1', '$t_slip_text1', '$rent_address1', '$tax_summary_text1', '$income_delivery1', '$summary_expenses1', '$delivery_hst1', '$hst_number1', '$hst_access_code1', '$hst_start_date1', '$hst_end_date1', '$additional_docs_text1', '$message_us1')";

// if (mysqli_query($conn, $sql)) {ss
//     echo "New record created successfully";
// } else {
//     $error_message = "Error: " . mysqli_error($conn);
//     echo "<script>alert('$error_message');</script>";
// }

// $mail = new PHPMailer(); // create a new object
// $mail->IsHTML(true);
// $mail->IsSMTP(); // enable SMTP
// $mail->SMTPAuth   = true; // authentication enabled
// $mail->SMTPSecure = 'TLS'; // secure transfer enabled REQUIRED for Gmail
// $mail->Host       = 'smtp.mailgun.org';
// $mail->Port       = 587; // or 587
// $mail->Username   = 'support@limneo.com';
// $mail->Password   = 'supportlimneo@123';
// $mail->From       = 'info@paragonafs.ca';
// $mail->FromName   = 'Paragon AFS';
// $mail->addAddress('lawrence.canadianwebdesigns@gmail.com');
// $mail->addAddress('Gurmeet.pawar@gmail.com');

//Create a new PHPMailer instance
$mail = new PHPMailer();
//Tell PHPMailer to use SMTP
$mail->isSMTP();
//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$mail->SMTPDebug = SMTP::DEBUG_SERVER;
$mail->Host = 'smtp.gmail.com'; //Set the hostname of the mail server
$mail->Port = 587; //Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Set the encryption mechanism to use - STARTTLS or SMTPS
$mail->SMTPAuth = true; //Whether to use SMTP authentication
$mail->AuthType = 'XOAUTH2'; //Set AuthType to use XOAUTH2
//Fill in authentication details here
//Either the gmail account owner, or the user that gave consent
$email = 'paragonafs@gmail.com';
$clientId = '810095646586-gsff99otmkljelq8rpl5ntd410nfkhbr.apps.googleusercontent.com';
$clientSecret = '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';
//Obtained by configuring and running get_oauth_token.php
//after setting up an app in Google Developer Console.
$refreshToken = '1//068jw-2CKE5qvCgYIARAAGAYSNwF-L9IrmyS8SEIcfaPR0eaP2jf-C1V-I1IqKlUk4yBK6k822ssgu3tzVywEhDufV1_Gv_KTb6E';

//Create a new OAuth2 provider instance
$provider = new Google(
    [
        'clientId' => $clientId,
        'clientSecret' => $clientSecret,
    ]
);

//Pass the OAuth provider instance to PHPMailer
$mail->setOAuth(
    new OAuth(
        [
            'provider' => $provider,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'refreshToken' => $refreshToken,
            'userName' => $email,
        ]
    )
);

//Set who the message is to be sent from
//For gmail, this generally needs to be the same as the user you logged in as
$mail->setFrom($email, 'Paragon AFS');
//Set who the message is to be sent to
// // $mail->addAddress('gurmeet.pawar@gmail.com');
$mail->addAddress('info@paragonafs.ca'); 
// $mail->addAddress('lawrence.canadianwebdesigns@gmail.com');
$mail->addAddress('limneoplaton10@gmail.com');

// if you want to send email to multiple users, then add the email addresses you which you want to send.
//$mail->addAddress('reciver2@gmail.com');
//$mail->addAddress('reciver3@gmail.com');
$mail->isHTML(true);

// // $to = "info@paragonafs.ca";
// // $todayis = date("l, F j, Y, g:i a");

$firstname = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';
$apartment_unit_number = isset($_POST['apartment_unit_number']) ? $_POST['apartment_unit_number'] : '';
$ship_address = isset($_POST['ship_address']) ? $_POST['ship_address'] : '';
$locality = isset($_POST['locality']) ? $_POST['locality'] : '';
$state = isset($_POST['state']) ? $_POST['state'] : '';
$postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
$country = isset($_POST['country']) ? $_POST['country'] : '';
$birth_date = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';
$sin_number = isset($_POST['sin_number']) ? $_POST['sin_number'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

$from_email = "paragonafs.ca";

$subject = "$firstname $lastName ($email) - Online File Submission";

$another_province = $_POST['another_province'];isset($_POST['another_province']) ? $_POST['another_province'] : '';
$move_date = isset($_POST['move_date']) ? $_POST['move_date'] : '';
$move_from = isset($_POST['move_from']) ? $_POST['move_from'] : '';
$move_to = isset($_POST['move_to']) ? $_POST['move_to'] : '';

$mail->addReplyTo("$email", "$firstname $lastName");

if ($another_province == 'Yes') {
    $move_date_question = "
    <tr>
        <th>When Did You Move</th>
        <td>$move_date</td>
    </tr>
    <tr>
        <th>Province moved From?</th>
        <td>$move_from</td>
    </tr>
    <tr>
        <th>Province moved To?</th>
        <td>$move_to</td>
    </tr>";
} else {
    $move_date_question = "";
}

$first_fillingtax = isset($_POST['first_fillingtax']) ? $_POST['first_fillingtax'] : '';
$canada_entry = isset($_POST['canada_entry']) ? $_POST['canada_entry'] : '';
$birth_country = isset($_POST['birth_country']) ? $_POST['birth_country'] : '';
$year1 = isset($_POST['year1']) ? $_POST['year1'] : '';
$year1_income = isset($_POST['year1_income']) ? $_POST['year1_income'] : '';
$year2 = isset($_POST['year2']) ? $_POST['year2'] : '';
$year2_income = isset($_POST['year2_income']) ? $_POST['year2_income'] : '';
$year3 = isset($_POST['year3']) ? $_POST['year3'] : '';
$year3_income = isset($_POST['year3_income']) ? $_POST['year3_income'] : '';
$file_paragon = isset($_POST['file_paragon']) ? $_POST['file_paragon'] : '';
$years_tax_return = isset($_POST['years_tax_return']) ? $_POST['years_tax_return'] : '';

if ($first_fillingtax == 'Yes') {
    $first_fillingtax_question = "
    <tr>
        <th>Date of Entry in Canada</th>
        <td>$canada_entry</td>
    </tr>
    <tr>
        <th>Birth Country</th>
        <td>$birth_country</td>
    </tr>
    <tr>
        <th>Year 1</th>
        <td>$year1</td>
    </tr>
    <tr>
        <th>Year 1 Income</th>
        <td>$year1_income</td>
    </tr>
    <tr>
        <th>Year 2</th>
        <td>$year2</td>
    </tr>
    <tr>
        <th>Year 2 Income</th>
        <td>$year2_income</td>
    </tr>
    <tr>
        <th>Year 3</th>
        <td>$year3</td>
    </tr>
    <tr>
        <th>Year 3 Income</th>
        <td>$year3_income</td>
    </tr>";
} else {
    $first_fillingtax_question = "
    <tr>
        <th>Did you file earlier with Paragon Tax Services?</th>
        <td>$file_paragon</td>
    </tr>
    <tr>
        <th>Which years do you want to file tax returns? *</th>
        <td>$years_tax_return</td>
    </tr>";
}

$marital_status = isset($_POST['marital_status']) ? $_POST['marital_status'] : '';
$spouse_firstname = isset($_POST['spouse_firstname']) ? $_POST['spouse_firstname'] : '';
$spouse_lastname = isset($_POST['spouse_lastname']) ? $_POST['spouse_lastname'] : '';
$spouse_date_birth = isset($_POST['spouse_date_birth']) ? $_POST['spouse_date_birth'] : '';
$date_marriage = isset($_POST['date_marriage']) ? $_POST['date_marriage'] : '';
$spouse_annual_income = isset($_POST['spouse_annual_income']) ? $_POST['spouse_annual_income'] : '';
$residing_canada = isset($_POST['residing_canada']) ? $_POST['residing_canada'] : '';
$spouse_annual_income_outside = isset($_POST['spouse_annual_income_outside']) ? $_POST['spouse_annual_income_outside'] : '';

$have_child = isset($_POST['have_child']) ? $_POST['have_child'] : '';
$marital_change = isset($_POST['marital_change']) ? $_POST['marital_change'] : '';
$spouse_sin = isset($_POST['spouse_sin']) ? $_POST['spouse_sin'] : '';
$spouse_phone = isset($_POST['spouse_phone']) ? $_POST['spouse_phone'] : '';
$spouse_email = isset($_POST['spouse_email']) ? $_POST['spouse_email'] : '';

$spouse_file_tax = isset($_POST['spouse_file_tax']) ? $_POST['spouse_file_tax'] : '';
$spouse_first_tax = isset($_POST['spouse_first_tax']) ? $_POST['spouse_first_tax'] : '';

$spouse_canada_entry = isset($_POST['spouse_canada_entry']) ? $_POST['spouse_canada_entry'] : '';
$spouse_birth_country = isset($_POST['spouse_birth_country']) ? $_POST['spouse_birth_country'] : '';
$spouse_year1 = isset($_POST['spouse_year1']) ? $_POST['spouse_year1'] : '';
$spouse_year1_income = isset($_POST['spouse_year1_income']) ? $_POST['spouse_year1_income'] : '';
$spouse_year2 = isset($_POST['spouse_year2']) ? $_POST['spouse_year2'] : '';
$spouse_year2_income = isset($_POST['spouse_year2_income']) ? $_POST['spouse_year2_income'] : '';
$spouse_year3 = isset($_POST['spouse_year3']) ? $_POST['spouse_year3'] : '';
$spouse_year3_income = isset($_POST['spouse_year3_income']) ? $_POST['spouse_year3_income'] : '';
$spouse_file_paragon = isset($_POST['spouse_file_paragon']) ? $_POST['spouse_file_paragon'] : '';
$spouse_years_tax_return = isset($_POST['spouse_years_tax_return']) ? $_POST['spouse_years_tax_return'] : '';

if ($spouse_first_tax == 'Yes') {
    $spouse_first_tax_text = "
    <tr>
        <th>Spouse Date of Entry in Canada</th>
        <td>$spouse_canada_entry</td>
    </tr>
    <tr>
        <th>Spouse Birth Country</th>
        <td>$spouse_birth_country</td>
    </tr>
    <tr>
        <th>Spouse Year 1</th>
        <td>$spouse_year1</td>
    </tr>
    <tr>
        <th>Spouse Year 1 Income</th>
        <td>$spouse_year1_income</td>
    </tr>
    <tr>
        <th>Spouse Year 2</th>
        <td>$spouse_year2</td>
    </tr>
    <tr>
        <th>Spouse Year 2 Income</th>
        <td>$spouse_year2_income</td>
    </tr>
    <tr>
        <th>Spouse Year 3</th>
        <td>$spouse_year3</td>
    </tr>
    <tr>
        <th>Spouse Year 3 Income</th>
        <td>$spouse_year3_income</td>
    </tr>
    ";
} else {
    $spouse_first_tax_text = "
    <tr>
        <th>Did your Spouse file earlier with Paragon Tax Services?</th>
        <td>$spouse_file_paragon</td>  
    </tr>
    <tr>
        <th>Which years your Spouse want to file tax returns? *</th>
        <td>$spouse_years_tax_return</td>
    </tr>
    ";
}

if ($spouse_file_tax == 'Yes') {
    $spouse_file_tax_text = "
    <tr>
        <th>Does your spouse want to file taxes? </th>
        <td>$spouse_file_tax</td>
    </tr>
    <tr>
        <th>Is this the first time your spouse filing tax</th>
        <td>$spouse_first_tax</td>
    </tr>
    $spouse_first_tax_text
    ";
} else {
    $spouse_file_tax_text = "
    <tr>
        <th>Does your spouse want to file taxes? </th>
        <td>$spouse_file_tax</td>
    </tr>
    <tr>
        <th>Spouse Annual Income in CAD</th>
        <td>$spouse_annual_income</td>
    </tr>
    ";
}

if ($residing_canada == "Yes") {
    $residing_canada_text = "
    <tr>
        <th>Residing in Canada</th>
        <td>Yes</td>
    </tr>
    <tr>
        <th>Spouse SIN</th>
        <td>$spouse_sin</td>
    </tr>
    <tr>
        <th>Spouse Email Address </th>
        <td>$spouse_email</td>
    </tr>
    <tr>
        <th>Spouse Phone Number </th>
        <td>$spouse_phone</td>
    </tr>
    $spouse_file_tax_text
    ";
} else {
    $residing_canada_text = "
    <tr>
        <th>Residing in Canada</th>
        <td>No</td>
    </tr>
    <tr>
        <th>Spousal Annual Income outside Canada (Converted to CAD)</th>
        <td>$spouse_annual_income_outside </td>
    </tr>
    ";
}

$child_last_name = $_POST['data'];
$child_date_birth = $_POST['data'];

$child_first_name_text = '';
$child_first_name = isset($_POST['data']) && is_array($_POST['data']) ? $_POST['data'] : '';

foreach ($child_first_name as $x) {
    $child_first_name_text .=
        "<tr>
            <th>Child First Name</th>
            <td>" . $x['child_first_name'] . "</td>
        </tr>
        <tr>
            <th>Child Last Name</th>
            <td>" . $x['child_last_name'] . "</td>
        </tr>
        <tr>
            <th>Child Date of Birth</th>
            <td>" . $x['child_date_birth']  . "</td>
        </tr>
        <tr>
            <th></th>
            <td></td>
        </tr>";
}

if ($have_child == 'Yes') {
    $have_child_text = "
        $child_first_name_text
    ";
} else {
    $have_child_text = "";
}


if ($marital_status == "Single") {
    $marital_status_output = "";
} elseif (($marital_status == "Married") || ($marital_status == "Common in Law") || ($marital_status == "Seperated")) {
    $marital_status_output = "
    <tr>
        <th>Spouse Last Name</th>
        <td>$spouse_lastname</td>
    </tr>
    <tr>
        <th>Spouse First Name</th>
        <td>$spouse_firstname</td>
    </tr>
    <tr>
        <th>Spouse Date of Birth</th>
        <td>$spouse_date_birth</td>
    </tr>
    <tr>
        <th>Date of Marriage</th>
        <td>$date_marriage</td>
    </tr>
    $residing_canada_text
    <tr>
        <th>Do you have child</th>
        <td>$have_child</td>
    </tr>
    $have_child_text
    ";
} else {
    $marital_status_output = "
    <tr>
        <th>Date Of Marital status change</th>
        <td>$marital_change</td>
    </tr>";
}

$first_time_buyer = isset($_POST['first_time_buyer']) ? $_POST['first_time_buyer'] : '';
$purchase_first_home = isset($_POST['purchase_first_home']) ? $_POST['purchase_first_home'] : "";


// $direct_deposit_text = implode("\n", $direct_deposits);
// $id_proof_text = implode("\n", $id_proof);
// $college_text = implode("\n", $college_receipt);
// $t_slip_text = implode("\n", $t_slips);

$id_proof = isset($_POST['id_proof']) ? $_POST['id_proof'] : '';
$direct_deposits = isset($_POST['direct_deposits']) ? $_POST['direct_deposits'] : '';
$college_receipt = isset($_POST['college_receipt']) ? $_POST['college_receipt'] : '';
$t_slips = isset($_POST['t_slips']) ? $_POST['t_slips'] : '';
$tax_summary = isset($_POST['tax_summary']) ? $_POST['tax_summary'] : '';
$additional_docs = isset($_POST['additional_docs']) ? $_POST['additional_docs'] : '';
$sin_number_document = isset($_POST['sin_number_document']) ? $_POST['sin_number_document'] : '';

$id_proof_text = !empty($id_proof) ? implode("\n", explode("<br>", $id_proof)) : '';
$direct_deposit_text = !empty($direct_deposits) ? implode("\n", explode("<br>", $direct_deposits)) : '';
$college_text = !empty($college_receipt) ? implode("\n", explode("<br>", $college_receipt)) : '';
$t_slip_text = !empty($t_slips) ? implode("\n", explode("<br>", $t_slips)) : '';
$tax_summary_text = !empty($tax_summary) ? implode("\n", explode("<br>", $tax_summary)) : '';
$additional_docs_text = !empty($additional_docs) ? implode("\n", explode("<br>", $additional_docs)) : '';
$sin_number_document_text = !empty($sin_number_document) ? implode("\n", explode("<br>", $sin_number_document)) : '';
$summary_expenses = isset($_POST['summary_expenses']) ? $_POST['summary_expenses'] : '';


$income_delivery = isset($_POST['income_delivery']) ? $_POST['income_delivery'] : '';
$delivery_hst = isset($_POST['delivery_hst']) ? $_POST['delivery_hst'] : '';
$hst_number = isset($_POST['hst_number']) ? $_POST['hst_number'] : '';
$hst_access_code = isset($_POST['hst_access_code']) ? $_POST['hst_access_code'] : '';
$hst_start_date = isset($_POST['hst_start_date']) ? $_POST['hst_start_date'] : '';
$hst_end_date = isset($_POST['hst_end_date']) ? $_POST['hst_end_date'] : '';


if ($delivery_hst == 'Yes') {
    $delivery_hst_text = "
    <tr>
        <th>HST #</th>
        <td>$hst_number</td>
    </tr>
    <tr>
        <th>Access Code</th>
        <td>$hst_access_code</td>
    </tr>
    <tr>
        <th>Start Date</th>
        <td>$hst_start_date</td>
    </tr>
    <tr>
        <th>End Date</th>
        <td>$hst_end_date</td>
    </tr>";
} else {
    $delivery_hst_text = "";
}

if ($income_delivery == 'Yes') {
    $income_delivery_text = "
    <tr>
        <th>Do you have income from Uber/Skip/Lyft/Doordash etc.?</th>
        <td>$income_delivery</td>
    </tr>
    <tr>
        <th>Annual Tax Summary</th>
        <td>$tax_summary_text</td>
    </tr>
    <tr>
        <th>Summary of Expenses</th>
        <td>$summary_expenses</td>
    </tr>
    <tr>
        <th>Do you want to file HST for your Uber/Skip/Lyft/Doordash?</th>
        <td>$delivery_hst</td>
    </tr>
    $delivery_hst_text";
} else {
    $income_delivery_text = "
    <tr>
        <th>Do you have income from Uber/Skip/Lyft/Doordash etc.?</th>
        <td>$income_delivery</td>
    </tr>";
}


$spouse_id_proof = isset($_POST['spouse_id_proof']) ? $_POST['spouse_id_proof'] : '';
$spouse_direct_deposits = isset($_POST['spouse_direct_deposits']) ? $_POST['spouse_direct_deposits'] : '';
$spouse_college_receipt = isset($_POST['spouse_college_receipt']) ? $_POST['spouse_college_receipt'] : '';
$spouse_t_slips = isset($_POST['spouse_t_slips']) ? $_POST['spouse_t_slips'] : '';
$spouse_tax_summary = isset($_POST['spouse_tax_summary']) ? $_POST['spouse_tax_summary'] : '';
$spouse_additional_docs = isset($_POST['spouse_additional_docs']) ? $_POST['spouse_additional_docs'] : '';
$spouse_sin_number_document = isset($_POST['spouse_sin_number_document']) ? $_POST['spouse_sin_number_document'] : '';

$spouse_id_proof_text = !empty($spouse_id_proof) ? implode("\n", explode("<br>", $spouse_id_proof)) : '';
$spouse_direct_deposit_text = !empty($spouse_direct_deposits) ? implode("\n", explode("<br>", $spouse_direct_deposits)) : '';
$spouse_college_text = !empty($spouse_college_receipt) ? implode("\n", explode("<br>", $spouse_college_receipt)) : '';
$spouse_t_slip_text = !empty($spouse_t_slips) ? implode("\n", explode("<br>", $spouse_t_slips)) : '';
$spouse_tax_summary_text = !empty($spouse_tax_summary) ? implode("\n", explode("<br>", $spouse_tax_summary)) : '';
$spouse_additional_docs_text = !empty($spouse_additional_docs) ? implode("\n", explode("<br>", $spouse_additional_docs)) : '';
$spouse_sin_number_document_text = !empty($spouse_sin_number_document) ? implode("\n", explode("<br>", $spouse_sin_number_document)) : '';
$spouse_summary_expenses = isset($_POST['spouse_summary_expenses']) ? $_POST['spouse_summary_expenses'] : '';


$spouse_income_delivery = isset($_POST['spouse_income_delivery']) ? $_POST['spouse_income_delivery'] : "";
$spouse_delivery_hst = isset($_POST['spouse_delivery_hst']) ? $_POST['spouse_delivery_hst'] : '';
$spouse_hst_number = isset($_POST['spouse_hst_number']) ? $_POST['spouse_hst_number'] : '';
$spouse_hst_access_code = isset($_POST['spouse_hst_access_code']) ? $_POST['spouse_hst_access_code'] : '';
$spouse_hst_start_date = isset($_POST['spouse_hst_start_date']) ? $_POST['spouse_hst_start_date'] : '';
$spouse_hst_end_date = isset($_POST['spouse_hst_end_date']) ? $_POST['spouse_hst_end_date'] : '';


if ($spouse_delivery_hst == 'Yes') {
    $spouse_delivery_hst_text = "
    <tr>
        <th>HST #</th>
        <td>$spouse_hst_number</td>
    </tr>
    <tr>
        <th>Access Code</th>
        <td>$spouse_hst_access_code</td>
    </tr>
    <tr>
        <th>Start Date</th>
        <td>$spouse_hst_start_date</td>
    </tr>
    <tr>
        <th>End Date</th>
        <td>$spouse_hst_end_date</td> 
    </tr>";
} else {
    $spouse_delivery_hst_text = "";
}

if ($spouse_income_delivery == 'Yes') {
    $spouse_income_delivery_text = "
    <tr>
        <th>Do you have income from Uber/Skip/Lyft/Doordash etc.?</th>
        <td>$spouse_income_delivery</td>
    </tr>
    <tr>
        <th>Annual Tax Summary</th>
        <td>$spouse_tax_summary_text</td>
    </tr>
    <tr>
        <th>Summary of Expenses</th>
        <td>$spouse_summary_expenses</td>
    </tr>
    <tr>
        <th>Do you want to file HST for your Uber/Skip/Lyft/Doordash?</th>
        <td>$spouse_delivery_hst</td>
    </tr>
    $spouse_delivery_hst_text";
} else {
    $spouse_income_delivery_text = "
    <tr>
        <th>Do you have income from Uber/Skip/Lyft/Doordash etc.?</th>
        <td>$spouse_income_delivery</td>
    </tr>";
}


$total_month_rent = $_POST['group-a'];
$total_rent_paid = $_POST['group-a'];
$rent_address_text = '';
$rent_address = isset($_POST['group-a']) && is_array($_POST['group-a']) ? $_POST['group-a'] : '';
foreach ($rent_address as $x) {
    $rent_address_text .=
        "<tr>
            <th>Rent Address</th>
            <td>" . $x['rent_address'] . "</td>
        </tr>
        <tr>
            <th>How Many Total Month of Rent</th>
            <td>" . $x['total_month_rent'] . "</td>
        </tr>
        <tr>
            <th>Total Rent Paid</th>
            <td>" . $x['total_rent_paid']  . "</td>
        </tr>";
}

$spouse_total_month_rent = $_POST['spouse_rent_address'];
$spouse_total_rent_paid = $_POST['spouse_rent_address'];

$spouse_rent_address_text = '';
$spouse_rent_address = isset($_POST['spouse_rent_address']) && is_array($_POST['spouse_rent_address']) ? $_POST['spouse_rent_address'] : '';
foreach ($spouse_rent_address as $x) {
    $spouse_rent_address_text .=
        "<tr>
            <th>Rent Address</th>
            <td>" . $x['spouse_rent_address'] . "</td>
        </tr>
        <tr>
            <th>How Many Total Month of Rent</th>
            <td>" . $x['spouse_total_month_rent'] . "</td>
        </tr>
        <tr>
            <th>Total Rent Paid</th>
            <td>" . $x['spouse_total_rent_paid']  . "</td>
        </tr>";
}

$message_us = isset($_POST['message_us']) ? $_POST['message_us'] : '';

function explodeUrls($input) {
    if (is_array($input)) {
        // If $input is already an array, return it as is
        return $input;
    } else {
        // Otherwise, explode the string into an array and filter out empty values
        return array_filter(explode('<br>', $input));
    }
}

$urls = array();

// Assuming $id_proof, $direct_deposits, etc., are strings containing URLs separated by <br>
$idProofUrls = explodeUrls($id_proof);
$directDepositsUrls = explodeUrls($direct_deposits);
$collegeReceiptUrls = explodeUrls($college_receipt);
$tSlipsUrls = explodeUrls($t_slips);
$taxSummaryUrls = explodeUrls($tax_summary);
$additionalDocsUrls = explodeUrls($additional_docs);
$sinNumberDocumentUrls = explodeUrls($sin_number_document);

$spouse_idProofUrls = explodeUrls($spouse_id_proof);
$spouse_directDepositsUrls = explodeUrls($spouse_direct_deposits);
$spouse_collegeReceiptUrls = explodeUrls($spouse_college_receipt);
$spouse_tSlipsUrls = explodeUrls($spouse_t_slips);
$spouse_taxSummaryUrls = explodeUrls($spouse_tax_summary);
$spouse_additionalDocsUrls = explodeUrls($spouse_additional_docs);
$spouse_sinNumberDocumentUrls = explodeUrls($spouse_sin_number_document);

// Merge only non-empty arrays
$nonEmptyArrays = array_filter([
    $idProofUrls,
    $directDepositsUrls,
    $collegeReceiptUrls,
    $tSlipsUrls,
    $taxSummaryUrls,
    $additionalDocsUrls,
    $spouse_idProofUrls,
    $spouse_directDepositsUrls,
    $spouse_collegeReceiptUrls,
    $spouse_tSlipsUrls,
    $spouse_taxSummaryUrls,
    $spouse_additionalDocsUrls,
    $sinNumberDocumentUrls,
    $spouse_sinNumberDocumentUrls
], function($array) {
    return !empty($array);
});

// Merge non-empty arrays
foreach ($nonEmptyArrays as $array) {
    $urls = array_merge($urls, $array);
}

if ($spouse_firstname === '' || $spouse_file_tax === 'No' || $residing_canada === 'No') {
    $spouse_document_upload_text = '';
} else {
    $spouse_document_upload_text = "
        <tr>
            <th colspan='2'>$spouse_firstname Documents</th>
        </tr>
        <tr>
            <th>ID Proof</th>
            <td>$spouse_id_proof_text</td>
        </tr>
        <tr>
            <th>SIN Number Document</th>
            <td>$spouse_sin_number_document_text</td>
        </tr>
        <tr>
            <th>Direct Deposit Form</th>
            <td>$spouse_direct_deposit_text</td>
        </tr>
        <tr>
            <th>T2202(College Receipt)</th>
            <td>$spouse_college_text</td>
        </tr>
        <tr>
            <th>T4/T4A/T Slips</th>
            <td>$spouse_t_slip_text</td>
        </tr>
        <tr>
            <th>Additional Documents to Upload</th>
            <td>$spouse_additional_docs_text</td>
        </tr>
        $spouse_rent_address_text
        $spouse_income_delivery_text
        <tr>
            <th colspan='2'></th>
        </tr>
    ";
}

//Set the subject line
$mail->Subject = $subject;
$mail->Body    = "<html>
<head>
    <title>HTML email with table</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            text-align: left;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
</head>
<body>
    <table style=\"text-align:left;\">
        <tr>
            <th>Questions</th>
            <th>Answer</th>
        </tr>
        <tr>
            <th>Last Name</th>
            <td>$lastName</td>
        </tr>
        <tr>
            <th>First Name</th>
            <td>$firstname</td>
        </tr>
        <tr>
            <th>Gender</th>
            <td>$gender</td>
        </tr>
        <tr>
            <th>Apartment/Unit #</th>
            <td>$apartment_unit_number</td>
        </tr>
        <tr>
            <th>Street</th>
            <td>$ship_address</td>
        </tr>
        <tr>
            <th>City</th>
            <td>$locality</td>
        </tr>
        <tr>
            <th>State/Province</th>
            <td>$state</td>
        </tr>
        <tr>
            <th>Postal Code</th>
            <td>$postcode</td>
        </tr>
        <tr>
            <th>Country/Region</th>
            <td>$country</td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td>$birth_date</td>
        </tr>
        <tr>
            <th>SIN Number</th>
            <td>$sin_number</td>
        </tr>
        <tr>
            <th>Phone Number</th>
            <td>$phone</td>
        </tr>
        <tr>
            <th>Email Address</th>
            <td>$email</td>
        </tr>
        <tr>
            <th>Did you move to another province?</th>
            <td>$another_province</td>
        </tr>
        $move_date_question
        <tr>
            <th>Is this the first time you are filing tax?</th>
            <td>$first_fillingtax</td>
        </tr>
        $first_fillingtax_question
        <tr>
            <th>Are you first time home buyer?</th>
            <td>$first_time_buyer</td>
        </tr>
        <tr>
            <th>When did you purchase your first home?</th>
            <td>$purchase_first_home</td>
        </tr>
        <tr>
            <th>Marital Status</th>
            <td>$marital_status</td>
        </tr>
        $marital_status_output
        <tr>
            <th colspan='2'>$firstname Documents</th>
        </tr>
        <tr>
            <th>ID Proof</th>
            <td>$id_proof_text</td>
        </tr>
        <tr>
            <th>SIN Number Documents</th>
            <td>$sin_number_document_text</td>
        </tr>
        <tr>
            <th>Direct Deposit Form</th>
            <td>$direct_deposit_text</td>
        </tr>
        <tr>
            <th>T2202(College Receipt)</th>
            <td>$college_text</td>
        </tr>
        <tr>
            <th>T4/T4A/T Slips</th>
            <td>$t_slip_text</td>
        </tr>
        <tr>
            <th>Additional Documents to Upload</th>
            <td>$additional_docs_text</td>
        </tr>
        $rent_address_text
        $income_delivery_text
        <tr>
            <th colspan='2'></th>
        </tr>
        $spouse_document_upload_text
        <tr>
            <th>Your Message For Us</th>
            <td>$message_us</td>
        </tr>
    </table>
</body>
</html>";

//Replace the plain text body with one created manually
// $mail->AltBody = 'This is a plain-text message body';

foreach ($urls as $url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Add this line to bypass SSL certificate verification
    $content = curl_exec($ch);
    $error = curl_error($ch); // Add this line to capture any errors
    curl_close($ch);

    if ($content === false) {
        // Log the error
        error_log("Error downloading file from URL: " . $url . " - Error: " . $error);
    } else {
        $filename = basename($url);
        $mail->addStringAttachment($content, $filename);
    }
}

// //For Attachments
// $mail->addAttachment('/var/tmp/file.tar.gz');  // Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); // You can specify the file name

//send the message, check for errors
if (!$mail->send()) {
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $response = array(
        'status' => 'success',
        'message' => 'Email Sent Successfully'
    );

    $yes_file_submitted = 'Yes';
    // SQL query to insert values into the tax_information table
    $sql = 'UPDATE tax_information SET is_file_submit=?, file_submit_date=NOW() WHERE email=?';
    $stmt= $conn->prepare($sql);
    $stmt->bind_param("ss", $yes_file_submitted, $_SESSION['email']);
    $stmt->execute();

    error_log("Client " . encrypt_decrypt('decrypt', $_SESSION['email']) . " Successfully submitted documents!");

    echo json_encode($response);
}
