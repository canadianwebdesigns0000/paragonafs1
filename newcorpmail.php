<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';





$to = "info@paragonafs.ca"; 
$todayis = date("l, F j, Y, g:i a");

$firstName = $_POST['firstName'];
$lastName = $_POST['lastName'];
$sin_number = $_POST['sin_number'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$business_activity = $_POST['business_activity'];

$ship_address = isset($_POST['ship_address']) ? $_POST['ship_address'] : '';
$locality = isset($_POST['locality']) ? $_POST['locality'] : '';
$state = isset($_POST['state']) ? $_POST['state'] : '';
$postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
$country = isset($_POST['country']) ? $_POST['country'] : '';

$corporation_type = $_POST['corporation_type'];
$name_corp = isset($_POST['name_corp']) ? $_POST['name_corp'] : '';

$subject = "Business Registration Request $firstName $lastName";

$id_proof = isset($_POST['id_proof']) ? $_POST['id_proof'] : '';
$id_proof_text = !empty($id_proof) ? implode("\n", explode("<br>", $id_proof)) : '';
$corpVal = '';
if ($name_corp) {
    $corpVal = '<tr>
        <th>Named Corporation</th>
        <td>' . $name_corp . '</td>
    </tr>';
}

$message = "
<html>
<head>
    <title>HTML email</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
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
    <table>
        <tr>
            <th>Name</th>
            <td>$firstName $lastName</td>
        </tr>
        <tr>
            <th>SIN Number</th>
            <td>$sin_number</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>$email</td>
        </tr>
        <tr>
            <th>Phone</th>
            <td>$phone</td>
        </tr>
        <tr>
            <th>Business Activity</th>
            <td>$business_activity</td>
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
            <th>Driver License</th>
            <td>$id_proof_text</td>
        </tr>
        <tr>
            <th>Corporation Type</th>
            <td>$corporation_type</td>
            
        </tr>
        
        
         <tr>
            <th>Corporation Name</th>
     
              <td>$name_corp</td>
        </tr>
        
     
        
    </table>
</body>
</html>
";





// Check reCAPTCHA response
$recaptcha_secret = '6Lem0r0qAAAAAN4AFFln9e3TLMB7I-9JGad3U6Hh'; // Replace with your reCAPTCHA secret key
$recaptcha_response = $_POST['g-recaptcha-response'];

// Validate reCAPTCHA response
if (!empty($recaptcha_response)) {
    $response = file_get_contents(
        "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $recaptcha_response
    );
    $response_keys = json_decode($response, true);

    if (!$response_keys["success"]) {
        // If reCAPTCHA verification fails, show an alert and redirect back
        echo "<script>alert('reCAPTCHA verification failed. Please try again.'); window.history.back();</script>";
        exit;
    }
} else {
    // If reCAPTCHA is not completed, show an alert and redirect back
    echo "<script>alert('Please complete the reCAPTCHA challenge.'); window.history.back();</script>";
    exit;
}





// PHPMailer configuration
$mail = new PHPMailer(true);
        
try {
    //Server settings
    $mail->isSMTP();                                             // Send using SMTP
    $mail->Host       = 'smtp.gmail.com';                        // Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                    // Enable SMTP authentication
    $mail->Username   = 'paragonafs@gmail.com';                    // SMTP username
    $mail->Password   = 'kusysfuqlfwspwgv';                   // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;          // Enable TLS encryption
    $mail->Port       = 587;                                      // TCP port to connect to

    //Recipients
    $mail->setFrom($email, 'Paragon AFS');  
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
    header("Location: https://paragonafs.ca/business-registeration?email_sent=success");
} catch (Exception $e) {
   echo "Error in mail";
}

















function sendToGoogleSheet($formData) {
    $client = new Google\Client();
    $client->setAuthConfig('credentials.json'); // Path to your JSON file
    $client->addScope(Google\Service\Sheets::SPREADSHEETS);

    $service = new Google\Service\Sheets($client);

    // Google Sheet ID and Range
    $spreadsheetId = '1VfBb7oo1iMlSEF4w3opAZSRmigUHZEuzFuxBVaehlms'; // Replace with your Google Sheet ID
    $range = 'Sheet1'; // Append to the next empty row


      // Generate current date in YYYY-MM-DD format
    $currentDate = date('Y-m-d');

    $values = [
        [$formData['yourName'], $formData['email'], $formData['phone'], $formData['sin_number'],$currentDate,]
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



    $result = sendToGoogleSheet([
        'yourName' => $name,
        'email' => $email,
        'phone' => $phone,
        'sin_number' => $sin
    ]);


  


}




// Set the headers for HTML email
/*$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
// Use a fixed email address for Paragon AFS in the From header
$headers .= "From: Paragon AFS <info@paragonafs.ca>\r\n";  // From header
$headers .= "Reply-To: $yourName <$email>\r\n"; // Reply-To header

// Send the email using the mail() function
if (mail($to, $subject, $message, $headers)) {
    header("Location: new-corp.php?email_sent=success");
} else {
    echo "Error in mail";
} */
?>