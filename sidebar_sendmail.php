<?php
$to = "info@paragonafs.ca";
$todayis = date("l, F j, Y, g:i a");

$firstname = $_POST['name'];
$lastName = $_POST['last_name'];
$email = $_POST['email'];
$subject_message = $_POST['subject'];
$your_message = $_POST['message'];

$subject = "$firstname $lastName ($email) - Services Page Submission";

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

$mime_boundary = "==Multipart_Boundary_x" . md5(mt_rand()) . "x";

$headers = "From: $email\r\n" .
    "MIME-Version: 1.0\r\n" .
    "Content-Type: multipart/mixed;\r\n" .
    " boundary=\"{$mime_boundary}\"";

$message = "This is a multi-part message in MIME format.\n\n" .
    "--{$mime_boundary}\n" .
    "Content-Type: text/html; charset=\"UTF-8\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" .
    $message . "\n\n";

$message .= "--{$mime_boundary}--\n";

if (mail($to, $subject, $message, $headers))
    header("Location: /");
else
    echo "Error in mail";
