<?php
$to = "info@paragonafs.ca";  // Recipient's email
$todayis = date("l, F j, Y, g:i a");

$firstname = $_POST['name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$subject_message = $_POST['subject'];
$your_message = $_POST['message'];

$subject = "$firstname $lastName - Contact Us Submission";

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
            <th>Last Name</th>
            <td>$lastName</td>
        </tr>
        <tr>
            <th>First Name</th>
            <td>$firstname</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>$email</td>
        </tr>
        <tr>
            <th>Subject</th>
            <td>$subject_message</td>
        </tr>
        <tr>
            <th>Message</th>
            <td>$your_message</td>
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
// IP Restriction (Simple Mechanism)
$ip_address = $_SERVER['REMOTE_ADDR'];
$time_file = 'ip_restriction.log'; // File to log IPs and timestamps
$time_limit = 60; // Time limit in seconds between submissions

// Check file for previous submissions from this IP
if (file_exists($time_file)) {
    $log_data = json_decode(file_get_contents($time_file), true);
    $current_time = time();

    if (isset($log_data[$ip_address]) && ($current_time - $log_data[$ip_address]) < $time_limit) {
    
    
     echo "<script>alert('You are submitting too quickly. Please wait and try again'); window.history.back();</script>";
        exit;
    
    
    }

    // Update log with current time for this IP
    $log_data[$ip_address] = $current_time;
    file_put_contents($time_file, json_encode($log_data));
} else {
    // Create log file if it doesn't exist
    file_put_contents($time_file, json_encode([$ip_address => time()]));
}

// Set the headers for HTML email
$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers = "From: paragonafs@gmail.com" . "\r\n" .
$headers .= "Reply-To: $firstname $lastName <$email>\r\n"; // Reply-To header

// Send the email using the mail() function
if (mail($to, $subject, $message, $headers)) {
    header("Location: contact_us.php?email_sent=success");
} else {
    echo "Error in sending email.";
}
?>


