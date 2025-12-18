<?php
$to = "info@paragonafs.ca";
$todayis = date("l, F j, Y, g:i a");

$firstname = $_POST['firstName'];
$lastName = $_POST['lastName'];
$gender = $_POST['gender'];
$ship_address = $_POST['ship_address'];
$locality = $_POST['locality'];
$state = $_POST['state'];
$postcode = $_POST['postcode'];
$country = $_POST['country'];
$birth_date = $_POST['birth_date'];
$sin_number = $_POST['sin_number'];
$phone = $_POST['phone'];
$email = $_POST['email'];

$from_email = "paragonafs.ca";

$subject = "$firstname $lastName ($email) - Online File Submission";

$another_province = $_POST['another_province'];
$move_date = $_POST['move_date'];
$move_from = $_POST['move_from'];
$move_to = $_POST['move_to'];

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

$first_fillingtax = $_POST['first_fillingtax'];
$canada_entry = $_POST['canada_entry'];
$birth_country = $_POST['birth_country'];
$year1 = $_POST['year1'];
$year1_income = $_POST['year1_income'];
$year2 = $_POST['year2'];
$year2_income = $_POST['year2_income'];
$year3 = $_POST['year3'];
$year3_income = $_POST['year3_income'];
$file_paragon = $_POST['file_paragon'];
$years_tax_return = $_POST['years_tax_return'];

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

$marital_status = $_POST['marital_status'];
$spouse_firstname = $_POST['spouse_firstname'];
$spouse_lastname = $_POST['spouse_lastname'];
$spouse_date_birth = $_POST['spouse_date_birth'];
$date_marriage = $_POST['date_marriage'];
$spouse_annual_income = $_POST['spouse_annual_income'];
$residing_canada = $_POST['residing_canada'];
$have_child = $_POST['have_child'];
$marital_change = $_POST['marital_change'];
$spouse_sin = $_POST['spouse_sin'];
$spouse_phone = $_POST['spouse_phone'];
$spouse_email = $_POST['spouse_email'];
$spouse_file_tax = $_POST['spouse_file_tax'];
$spouse_first_tax = $_POST['spouse_first_tax'];
$spouse_canada_entry = $_POST['spouse_canada_entry'];
$spouse_birth_country = $_POST['spouse_birth_country'];
$spouse_year1 = $_POST['spouse_year1'];
$spouse_year1_income = $_POST['spouse_year1_income'];
$spouse_year2 = $_POST['spouse_year2'];
$spouse_year2_income = $_POST['spouse_year2_income'];
$spouse_year3 = $_POST['spouse_year3'];
$spouse_year3_income = $_POST['spouse_year3_income'];
$spouse_file_paragon = $_POST['spouse_file_paragon'];
$spouse_years_tax_return = $_POST['spouse_years_tax_return'];


if ($spouse_file_tax == 'Yes') {
    $spouse_file_tax_text = "
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
    $spouse_file_tax_text = "
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
        <th>Spouse Phone Number </th>
        <td>$spouse_phone</td>
    </tr>
    <tr>
        <th>Spouse Email Address </th>
        <td>$spouse_email</td>
    </tr>
    <tr>
        <th>Does your spouse want to file taxes </th>
        <td>$spouse_file_tax</td>
    </tr>
    <tr>
        <th>Is this the first time your spouse filing tax</th>
        <td>$spouse_first_tax</td>
    </tr>
    $spouse_file_tax_text
    ";
} else {
    $residing_canada_text = "
    <tr>
        <th>Residing in Canada</th>
        <td>No</td>
    </tr>
    ";
}

$child_first_name = $_POST['data'];
$child_last_name = $_POST['data'];
$child_date_birth = $_POST['data'];

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
    <tr>
        <th>Spouse Annual Income in CAD</th>
        <td>$spouse_annual_income</td>
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

$first_time_buyer = $_POST['first_time_buyer'];

$direct_deposits = $_POST['direct'];
$id_proof = $_POST['id_proof'];
$college_receipt = $_POST['college'];
$t_slips = $_POST['t_slips'];

$rent_address = $_POST['group-a'];
$total_month_rent = $_POST['group-a'];
$total_rent_paid = $_POST['group-a'];

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
        </tr>
        <tr>
            <th></th>
            <td></td>
        </tr>";
}

$direct_deposit_text = implode("<br>", $direct_deposits);
$id_proof_text = implode("<br>", $id_proof);
$college_text = implode("<br>", $college_receipt);
$t_slip_text = implode("<br>", $t_slips);

$tax_summary = $_POST['tax_summary'];
$tax_summary_text = implode("<br>", $tax_summary);

$income_delivery = $_POST['income_delivery'];
$summary_expenses = $_POST['summary_expenses'];
$delivery_hst = $_POST['delivery_hst'];

$hst_number = $_POST['hst_number'];
$hst_access_code = $_POST['hst_access_code'];
$hst_start_date = $_POST['hst_start_date'];
$hst_end_date = $_POST['hst_end_date'];

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
    $income_delivery_text = "";
}

$additional_docs = $_POST['additional_docs'];
$additional_docs_text = implode("<br>", $additional_docs);

$message_us = $_POST['message_us'];

$urls = array();

array_push($urls, ...$direct_deposits, ...$id_proof, ...$college_receipt, ...$t_slips, ...$tax_summary, ...$additional_docs);

// echo $url;

$message = "
<html>
<head>
    <title>HTML email</title>
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
    <table style='text-align:center;'>
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
            <th>Marital Status</th>
            <td>$marital_status</td>
        </tr>
        $marital_status_output
        <tr>
            <th>Are you first time home buyer?</th>
            <td>$first_time_buyer</td>
        </tr>
        <tr>
            <th>Direct Deposit Form</th>
            <td>$direct_deposit_text</td>
        </tr>
        <tr>
            <th>ID Proof</th>
            <td>$id_proof_text</td>
        </tr>
        <tr>
            <th>T2202(College Receipt)</th>
            <td>$college_text</td>
        </tr>
        <tr>
            <th>T4/T4A/T Slips</th>
            <td>$t_slip_text</td>
        </tr>
        $rent_address_text
        $income_delivery_text
        <tr>
            <th>Additional Documents to Upload</th>
            <td>$additional_docs_text</td>
        </tr>
        <tr>
            <th>Your Message For Us</th>
            <td>$message_us</td>
        </tr>
    </table>
</body>
</html>
";

$mime_boundary = "==Multipart_Boundary_x" . md5(mt_rand()) . "x";

$headers = "From: paragonafs.ca\r\n" . "Reply-To: $email\r\n" .
    "MIME-Version: 1.0\r\n" .
    "Content-Type: multipart/mixed;\r\n" .
    " boundary=\"{$mime_boundary}\"";

$message = "This is a multi-part message in MIME format.\n\n" .
    "--{$mime_boundary}\n" .
    "Content-Type: text/html; charset=\"ISO-8859-1\"\n" .
    "Content-Transfer-Encoding: 7bit\n\n" .
    $message . "\n\n";

foreach ($urls as $url) {
    // Get the file information from the remote server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    // Extract the content type and file name
    $content_type = $info['content_type'];
    $parts = explode('/', $content_type);
    $extension = $parts[count($parts) - 1];
    $parts = explode('/', $url);
    $file_name = $parts[count($parts) - 1];
    $file_name_parts = explode('.', $file_name);
    array_pop($file_name_parts);
    $file_name = implode('.', $file_name_parts) . '.' . $extension;

    // Get the file content from the remote server
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    curl_close($ch);

    // Add the attachment to the message
    $message .= "--{$mime_boundary}\r\n" .
        "Content-Type: {$content_type};\r\n" .
        " name=\"{$file_name}\"\r\n" .
        "Content-Disposition: attachment;\r\n" .
        " filename=\"{$file_name}\"\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n" .
        chunk_split(base64_encode($content)) . "\r\n\r\n";
}


$message .= "--{$mime_boundary}--\n";

if (mail($to, $subject, $message, $headers))
    header("Location: upload-documents.php?email_sent=success");
else
    echo "Error in mail";
