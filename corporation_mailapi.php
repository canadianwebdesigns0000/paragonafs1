<?php

// Check reCAPTCHA response


// Check if the email session is set or not
/*if (!isset($_SESSION['email'])) {
    header('location: ./auth');
    exit();
}*/

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Database connection
/*$servername = "localhost";
$username = "paragonafs";
$password = "W6j4jCV9zJj8Tk8";
$dbname = "paragonafs";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}  */

// Encryption/Decryption function




function encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/';
    $secret_iv = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } elseif ($action == 'decrypt') {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}

// Email recipient and details
//$to = "moeed.hussain3@gmail.com";  // Recipient's email
$to = "info@paragonafs.ca";  // Recipient's email
$from_email = "paragonafs.ca";
$todayis = date("l, F j, Y, g:i a");



// Define $response as an empty array to avoid undefined variable error
$response = [];

// Retrieve form inputs with fallbacks
$firstname = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastname = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$apartment_unit_number = isset($_POST['apartment_unit_number']) ? $_POST['apartment_unit_number'] : '';
$ship_address = isset($_POST['ship_address']) ? $_POST['ship_address'] : '';
$locality = isset($_POST['locality']) ? $_POST['locality'] : '';
$state = isset($_POST['state']) ? $_POST['state'] : '';
$postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
$country = isset($_POST['country']) ? $_POST['country'] : '';

$business_activity = isset($_POST['business_activity']) ? $_POST['business_activity'] : '';

$income_sin = isset($_POST['income_on_sin']) ? $_POST['income_on_sin'] : '';


$summary_income = isset($_POST['summary_income']) ? $_POST['summary_income'] : '';


$summary_income_detail = isset($_POST['summary_income_detail']) ? $_POST['summary_income_detail'] : '';






$sin_number = isset($_POST['sin_number']) ? $_POST['sin_number'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$email = isset($_POST['email']) ? $_POST['email'] : '';

$subject = " Corporate Tax - $firstname $lastname ($email) - Online File Submission";

$marital_status = isset($_POST['marital_status']) ? $_POST['marital_status'] : '';


// $direct_deposit_text = implode("\n", $direct_deposits);
// $id_proof_text = implode("\n", $id_proof);
// $college_text = implode("\n", $college_receipt);
// $t_slip_text = implode("\n", $t_slips);

$id_proof = isset($_POST['id_proof']) ? $_POST['id_proof'] : '';
$business_gst_number = isset($_POST['business_gst_number']) ? $_POST['business_gst_number'] : '';
$corp_account_bank_statement = isset($_POST['corp_account_bank_statement']) ? $_POST['corp_account_bank_statement'] : '';
$phone_bills = isset($_POST['phone_bills']) ? $_POST['phone_bills'] : '';
$fuel_costs = isset($_POST['fuel_costs']) ? $_POST['fuel_costs'] : '';
$rent = isset($_POST['rent']) ? $_POST['rent'] : '';
$repairs_and_maintenance = isset($_POST['repairs_and_maintenance']) ? $_POST['repairs_and_maintenance'] : '';
$vehicle_emi = isset($_POST['vehicle_emi']) ? $_POST['vehicle_emi'] : '';
$car_insurance = isset($_POST['car_insurance']) ? $_POST['car_insurance'] : '';
$food_and_groceries = isset($_POST['food_and_groceries']) ? $_POST['food_and_groceries'] : '';
$first_filingtax = isset($_POST['first_filingtax']) ? $_POST['first_filingtax'] : '';
$filed_paragon = isset($_POST['filed_paragon']) ? $_POST['filed_paragon'] : '';
$cra_account = isset($_POST['cra_account']) ? $_POST['cra_account'] : '';

//$additional_docs = isset($_POST['additional_docs']) ? $_POST['additional_docs'] : '';
$additional_docs = isset($_POST['additional_docs']) ? $_POST['additional_docs'] : []; // This will be an array of values from all dropzones
$expense_names = isset($_POST['expense_name']) ? $_POST['expense_name'] : []; // Labels for each dropzone



$id_proof_text = !empty($id_proof) ? implode("\n", explode("<br>", $id_proof)) : '';
$corp_account_bank_statement_text = !empty($corp_account_bank_statement) ? implode("\n", explode("<br>", $corp_account_bank_statement)) : '';
//$additional_docs_text = !empty($additional_docs) ? implode("\n", explode("<br>", $additional_docs)) : '';
// Prepare the email table rows
$expenseRows = '';

// Check if the expense inputs are not empty
if (!empty($_POST['expense_label']) && !empty($_POST['expense_value'])) {
    $expense_labels = $_POST['expense_label'];
    $expense_values = $_POST['expense_value'];

    // Ensure both arrays have the same length
    $expenseCount = min(count($expense_labels), count($expense_values));

    for ($i = 0; $i < $expenseCount; $i++) {
        // Get the label and value for each expense
        $label = isset($expense_labels[$i]) ? $expense_labels[$i] : 'Unknown Label';
        $value = isset($expense_values[$i]) ? $expense_values[$i] : 'Unknown Value';

        // Escape variables for safe HTML output
        $label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        // Append the expense row
        $expenseRows .= "
        <tr>
            <th>{$label}</th>
            <td>{$value}</td>
        </tr>
        ";
    }
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
$corpAccountBankStatementUrls = explodeUrls($corp_account_bank_statement);


// Merge only non-empty arrays
$nonEmptyArrays = array_filter([
    $idProofUrls,
    $corpAccountBankStatementUrls
], function($array) {
    return !empty($array);
});

// Merge non-empty arrays
foreach ($nonEmptyArrays as $array) {
    $urls = array_merge($urls, $array);
}


//Set the subject line
$message = "<html>
<head>
    <title>HTML Email with Table</title>
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
            <th>Name</th>
            <td>$firstname $lastname</td>
        </tr>
        
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
            
        
        <tr>
            <th>Business Activity</th>
            <td>$business_activity</td>
        </tr>
        
        
        
          <tr>
            <th>Total household Income</th>
            <td>$income_sin</td>
        </tr>
        
        
            <tr>
            <th>  Do you any other income? </th>
            <td>$summary_income</td>
        </tr>
      
                      <tr>
            <th>  Summary of other income </th>
            <td> $summary_income_detail</td>
        </tr>
      
                
                
             
        
        <tr>
            <th>Are you filing Corporation Tax for the First Time</th>
            <td> $first_filingtax </td>
        </tr>
        
        
        <tr>
            <th>Did you file with Paragon before?</th>
            <td>$filed_paragon</td>
        </tr>
        <tr>
            <th>Do you have CRA Account</th>
            <td>$cra_account</td>
        </tr>
        
        <tr>
            <th>Marital Status</th>
            <td>$marital_status</td>
        </tr>
        
        <tr>
            <th colspan='2'>$firstname Documents</th>
        </tr>
        <tr>
            <th>Certificate of Incorporation</th>
            <td>$id_proof_text</td>
        </tr>
        <tr>
            <th>Business or GST/HST number</th>
            <td>$business_gst_number</td>
        </tr>
        <tr>
            <th>Corporation Account Bank Statements</th>
            <td>$corp_account_bank_statement_text</td>
        </tr>
        <tr>
            <th>Phone Bills</th>
            <td>$phone_bills</td>
        </tr>
        <tr>
            <th>Fuel Costs</th>
            <td>$fuel_costs</td>
        </tr>
        <tr>
            <th>Rent</th>
            <td>$rent</td>
        </tr>
        <tr>
            <th>Any Repairs and Maintenance</th>
            <td>$repairs_and_maintenance</td>
        </tr>
        <tr>
            <th>Vehicle EMI</th>
            <td>$vehicle_emi</td>
        </tr>   
        <tr>
            <th>Car Insurance</th>
            <td>$car_insurance</td>
        </tr>  
        $expenseRows
        <tr>
            <th>Your Message For Us</th>
            <td>$message_us</td>
        </tr>
        
    </table>
</body>
</html>";

// PHPMailer configuration
$mail = new PHPMailer(true);
        
try {
    //Server settings
    $mail->isSMTP();                                             // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
    $mail->Username   = 'paragonafs@gmail.com';                    // SMTP username
    $mail->Password   ='kusysfuqlfwspwgv';              // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption
    $mail->Port       = 587;                                      // TCP port to connect to

    //Recipients
    $mail->setFrom('paragonafs@gmail.com', 'Paragon AFS');  
    $mail->addAddress($to);  // Add a recipient


    // Attachments from URLs
    
    foreach ($urls as $url) {
        $filename = basename($url);
        $content = file_get_contents($url);
        if ($content !== false) {
            $mail->addStringAttachment($content, $filename);     // Attach file content
        }
    }

    // Content
    $mail->isHTML(true);                                         // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = strip_tags($message);                       // Plain text version for non-HTML mail clients

    $mail->send();
    echo 'Email sent successfully.';
    $response['status'] = 'success';
} catch (Exception $e) {
    echo "Failed to send email. Mailer Error: {$mail->ErrorInfo}";
    $response['status'] = 'failed';
    $response['error'] = $mail->ErrorInfo;
    echo $message;
}



function sendToGoogleSheet($formData) {
    $client = new Google\Client();
    $client->setAuthConfig('credentials.json'); // Path to your JSON file
    $client->addScope(Google\Service\Sheets::SPREADSHEETS);

    $service = new Google\Service\Sheets($client);

    // Google Sheet ID and Range
    $spreadsheetId = '164CinL38oVTE38p0FhujwwrYeHPF9HJogBWOdEXXFjM'; // Replace with your Google Sheet ID
    $range = 'Sheet1'; // Append to the next empty row


      // Generate current date in YYYY-MM-DD format
    $currentDate = date('Y-m-d');

    $values = [
        [ $formData['yourName'], $formData['email'], $formData['phone'], $formData['sin_number'],$formData['business_gst_number'],$currentDate,]
    ];

    $body = new Google\Service\Sheets\ValueRange([
        'values' => $values
    ]);

    $params = [
        'valueInputOption' => 'RAW'
    ];

    try {
        $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
        return $result->getUpdates()->getUpdatedCells();
    } catch (Exception $e) {
        error_log("Error appending data to Google Sheets: " . $e->getMessage());
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['yourName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $sin = $_POST['sin_number'] ?? '';
    $gst = $_POST['business_gst_number'] ?? '';



    $result = sendToGoogleSheet([
        'yourName' => $name,
        'email' => $email,
        'phone' => $phone,
        'sin_number' => $sin,
        'business_gst_number' => $gst
    ]);


  
}
// SQL query to update tax information table
/*
$yes_file_submitted = 'Yes';
$sql = 'UPDATE tax_information SET is_file_submit=?, file_submit_date=NOW() WHERE email=?';
$stmt= $conn->prepare($sql);
$stmt->bind_param("ss", $yes_file_submitted, $_SESSION['email']);
$stmt->execute();

error_log("Client " . encrypt_decrypt('decrypt', $_SESSION['email']) . " successfully submitted documents!");
*/
echo json_encode($response);



?>
