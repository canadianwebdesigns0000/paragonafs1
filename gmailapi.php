<?php
session_start(); // Start session for database update
header('Content-Type: application/json; charset=utf-8');

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized. Please log in.']);
    exit();
}

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Include config for form submission email
require_once 'auth/config.php';

// Collect local file paths for attachments created during this request
$uploadedAttachmentPaths = [];

/**
 * Mail transport switch:
 * - Local/dev (localhost / 127.0.0.1 / *.test / *.local): try LOCAL mail catcher first, fallback to Gmail SMTP
 * - Production: use Gmail SMTP only (never try localhost)
 *
 * Adjust LOCAL_SMTP_* to match your Laragon mail catcher settings.
 */
const LOCAL_SMTP_HOST = '127.0.0.1';
const LOCAL_SMTP_PORT = 1025;

function isLocalRequestHost(): bool {
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? ''));
    if ($host === '') return false;
    if ($host === 'localhost' || str_starts_with($host, 'localhost:')) return true;
    if ($host === '127.0.0.1' || str_starts_with($host, '127.0.0.1:')) return true;
    if (str_ends_with($host, '.test') || str_ends_with($host, '.local')) return true;
    return false;
}

function configureMailerLocal(\PHPMailer\PHPMailer\PHPMailer $m): void {
    $m->isSMTP();
    $m->Host = LOCAL_SMTP_HOST;
    $m->Port = LOCAL_SMTP_PORT;
    $m->SMTPAuth = false;
    $m->SMTPSecure = false;
    $m->SMTPAutoTLS = false;
}

function configureMailerGmail(\PHPMailer\PHPMailer\PHPMailer $m): void {
    $m->isSMTP();
    $m->Host       = 'smtp.gmail.com';
    $m->SMTPAuth   = true;
    $m->Username   = 'paragonafs@gmail.com';
    $m->Password   = 'kusysfuqlfwspwgv';
    $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $m->Port       = 587;
}

function sendWithAutoTransport(\PHPMailer\PHPMailer\PHPMailer $m, ?string &$usedTransport = null): bool {
    $usedTransport = null;
    $isLocal = isLocalRequestHost();

    // Local/dev: try local catcher first
    if ($isLocal) {
        try {
            configureMailerLocal($m);
            $m->send();
            $usedTransport = 'local';
            return true;
        } catch (\Throwable $e) {
            error_log('Local SMTP send failed, falling back to Gmail SMTP. Error: ' . ($m->ErrorInfo ?: $e->getMessage()));
            try { $m->smtpClose(); } catch (\Throwable $t) {}
            // fall through to Gmail
        }
    }

    // Production (and fallback in local): Gmail SMTP
    try {
        configureMailerGmail($m);
        $m->send();
        $usedTransport = 'gmail';
        return true;
    } catch (\Throwable $e) {
        error_log('Gmail SMTP send failed. Error: ' . ($m->ErrorInfo ?: $e->getMessage()));
        try { $m->smtpClose(); } catch (\Throwable $t) {}
        return false;
    }
}












// Database connection
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
// Handle both string and array formats for formSubmissionEmail
if (isset($formSubmissionEmail)) {
    if (is_array($formSubmissionEmail)) {
        $to = !empty($formSubmissionEmail) ? $formSubmissionEmail[0] : "info@paragonafs.ca";
    } else {
        $to = $formSubmissionEmail;
    }
} else {
    $to = "info@paragonafs.ca";
}
$from_email = "paragonafs.ca";
$todayis = date("l, F j, Y, g:i a");

// Handle file uploads if files are sent (new form sends File objects)
// Upload files to server and generate URLs
function uploadFilesAndGetUrls($filesArray, $fieldName) {
    if (empty($filesArray) || !isset($filesArray['name'])) {
        error_log("uploadFilesAndGetUrls: No files array or name for field: $fieldName");
        return '';
    }
    
    // Use absolute path relative to script location (gmailapi.php is in root, uploads folder is also in root)
    $uploadDir = __DIR__ . '/uploads/';
    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            error_log("uploadFilesAndGetUrls: Failed to create upload directory: $uploadDir");
            return '';
        }
    }
    
    // Ensure directory is writable
    if (!is_writable($uploadDir)) {
        error_log("uploadFilesAndGetUrls: Upload directory is not writable: $uploadDir");
        return '';
    }
    
    $urls = [];
    // Build a URL based on the current host + script directory (supports subfolder installs like /Paragon)
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'paragonafs.ca';
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    $scriptDir = ($scriptDir === '/' ? '' : $scriptDir);
    $baseUrl = $scheme . '://' . $host . $scriptDir . '/uploads/';
    
    // Handle multiple files (array format from FormData)
    $fileCount = is_array($filesArray['name']) ? count($filesArray['name']) : 1;
    
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = is_array($filesArray['name']) ? $filesArray['name'][$i] : $filesArray['name'];
        $tmpName = is_array($filesArray['tmp_name']) ? $filesArray['tmp_name'][$i] : $filesArray['tmp_name'];
        $error = is_array($filesArray['error']) ? $filesArray['error'][$i] : $filesArray['error'];
        $fileSize = is_array($filesArray['size']) ? $filesArray['size'][$i] : $filesArray['size'];
        
        // Log file info for debugging
        error_log("uploadFilesAndGetUrls: Processing file $i for field $fieldName - Name: $fileName, Size: $fileSize, Error: $error");
        
        if ($error !== UPLOAD_ERR_OK) {
            $errorMessages = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            ];
            $errorMsg = $errorMessages[$error] ?? "Unknown error code: $error";
            error_log("uploadFilesAndGetUrls: Upload error for $fileName ($fieldName): $errorMsg");
            continue;
        }
        
        if (empty($tmpName) || !is_uploaded_file($tmpName)) {
            error_log("uploadFilesAndGetUrls: Invalid uploaded file for $fileName ($fieldName)");
            continue;
        }
        
        // Get file extension (accept all file types including images)
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (empty($fileExt)) {
            error_log("uploadFilesAndGetUrls: No file extension for $fileName ($fieldName)");
            // Continue anyway - some files might not have extensions
        }
        
        $uniqueName = uniqid() . '_' . time() . '_' . $i . ($fileExt ? '.' . $fileExt : '');
        $targetPath = $uploadDir . $uniqueName;
        
        if (move_uploaded_file($tmpName, $targetPath)) {
            // Store both the URL (for email body) and the local path (for attaching without HTTP fetch)
            $urls[] = $baseUrl . $uniqueName;
            $GLOBALS['uploadedAttachmentPaths'][] = $targetPath;
            error_log("uploadFilesAndGetUrls: Successfully uploaded $fileName to $targetPath");
        } else {
            error_log("uploadFilesAndGetUrls: Failed to move uploaded file $fileName to $targetPath");
        }
    }
    
    $result = implode('<br>', $urls);
    error_log("uploadFilesAndGetUrls: Field $fieldName - Total files processed: $fileCount, Successfully uploaded: " . count($urls));
    return $result;
}

// Process file uploads from new form (if sent as $_FILES)
$fileFields = [
    'id_proof_files' => 'id_proof',
    't_slips_files' => 't_slips',
    'college_receipt_files' => 'college_receipt',
    'direct_deposits_files' => 'direct_deposits',
    'tax_summary_files' => 'tax_summary',
    'additional_docs_files' => 'additional_docs',
    'spouse_id_proof_files' => 'spouse_id_proof',
    'spouse_t_slips_files' => 'spouse_t_slips',
    'spouse_direct_deposits_files' => 'spouse_direct_deposits',
    'spouse_tax_summary_files' => 'spouse_tax_summary'
];

foreach ($fileFields as $fileField => $postField) {
    if (isset($_FILES[$fileField])) {
        // Check if files were uploaded (handle both single file and array of files)
        $hasFiles = false;
        if (is_array($_FILES[$fileField]['name'])) {
            // Check if at least one file name is not empty
            $hasFiles = !empty(array_filter($_FILES[$fileField]['name'], function($name) { return !empty($name); }));
        } else {
            $hasFiles = !empty($_FILES[$fileField]['name']);
        }
        
        if ($hasFiles) {
            error_log("Processing file upload for field: $fileField -> $postField");
            $uploadedUrls = uploadFilesAndGetUrls($_FILES[$fileField], $postField);
            if ($uploadedUrls) {
                // Merge with existing URLs if any
                $existing = isset($_POST[$postField]) ? $_POST[$postField] : '';
                $_POST[$postField] = $existing ? $existing . '<br>' . $uploadedUrls : $uploadedUrls;
                error_log("File upload successful for $postField - URLs: $uploadedUrls");
            } else {
                error_log("File upload returned empty URLs for $postField (field: $fileField)");
            }
        } else {
            error_log("No files found in upload field: $fileField");
        }
    } else {
        error_log("File field not set in \$_FILES: $fileField");
    }
}

// Define $response as an empty array to avoid undefined variable error
$response = [];

// Retrieve form inputs with fallbacks
function post_any(array $keys, string $default = ''): string {
    foreach ($keys as $k) {
        if (isset($_POST[$k]) && $_POST[$k] !== '') return (string)$_POST[$k];
    }
    return $default;
}

$firstname = post_any(['firstName','first_name']);
$lastName = post_any(['lastName','last_name']);
$gender = post_any(['gender']);
$apartment_unit_number = post_any(['apartment_unit_number','unit']);
$ship_address = post_any(['ship_address','street']);
$locality = post_any(['locality','city']);
$state = post_any(['state','province']);
$postcode = post_any(['postcode','postal']);
$country = post_any(['country']);
$birth_date = post_any(['birth_date','dob']);
$sin_number = post_any(['sin_number','sin']);
$phone = post_any(['phone']);
$email = post_any(['email']);



$another_province = isset($_POST['another_province']) ? $_POST['another_province'] : '';
$move_date = isset($_POST['move_date']) ? $_POST['move_date'] : '';
$move_from = isset($_POST['move_from']) ? $_POST['move_from'] : '';
$move_to = isset($_POST['move_to']) ? $_POST['move_to'] : '';

$subject = " Personal Tax - $firstname $lastName($email) - Online File Submission";

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

$first_fillingtax = post_any(['first_fillingtax']);
$canada_entry = post_any(['canada_entry','entry_date']);
$birth_country = post_any(['birth_country']);
$year1 = post_any(['year1']);
$year1_income = post_any(['year1_income','inc_y1']);
$year2 = post_any(['year2']);
$year2_income = post_any(['year2_income','inc_y2']);
$year3 = post_any(['year3']);
$year3_income = post_any(['year3_income','inc_y3']);
$file_paragon = post_any(['file_paragon','paragon_prior']);
$years_tax_return = post_any(['years_tax_return','return_years']);

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

$marital_status = post_any(['marital_status']);
$spouse_firstname = post_any(['spouse_firstname','spouse_first_name']);
$spouse_lastname = post_any(['spouse_lastname','spouse_last_name']);
$spouse_date_birth = post_any(['spouse_date_birth','spouse_dob']);
$date_marriage = post_any(['date_marriage','status_date']);
$spouse_annual_income = post_any(['spouse_annual_income','spouse_income_cad','spouse_income_cad']);
$residing_canada = post_any(['residing_canada','spouse_in_canada']);
$spouse_annual_income_outside = post_any(['spouse_annual_income_outside','spouse_income_outside_cad']);

$have_child = post_any(['have_child','children']);
$marital_change = post_any(['marital_change','status_date']);
$spouse_sin = post_any(['spouse_sin']);
$spouse_phone = post_any(['spouse_phone']);
$spouse_email = post_any(['spouse_email']);

$spouse_file_tax = post_any(['spouse_file_tax','spouseFile']);
$spouse_first_tax = post_any(['spouse_first_tax','sp_first_time']);

$spouse_canada_entry = post_any(['spouse_canada_entry','sp_entry_date']);
$spouse_birth_country = post_any(['spouse_birth_country','sp_birth_country']);
$spouse_year1 = post_any(['spouse_year1']);
$spouse_year1_income = post_any(['spouse_year1_income','sp_inc_y1']);
$spouse_year2 = post_any(['spouse_year2']);
$spouse_year2_income = post_any(['spouse_year2_income','sp_inc_y2']);
$spouse_year3 = post_any(['spouse_year3']);
$spouse_year3_income = post_any(['spouse_year3_income','sp_inc_y3']);
$spouse_file_paragon = post_any(['spouse_file_paragon','sp_paragon_prior']);
$spouse_years_tax_return = post_any(['spouse_years_tax_return','sp_return_years']);

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

// Children list (legacy expects $_POST['data'] as an array)
$child_first_name_text = '';
$childrenPayload = (isset($_POST['data']) && is_array($_POST['data'])) ? $_POST['data'] : [];

foreach ($childrenPayload as $x) {
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
            <th> Residing in Canada?</th>
            <td>" . $x['child_residing_canada']  . "</td>
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

// Collect T4/T4A/T Slips password arrays
$app_tslips_pw = isset($_POST['app_tslips_pw']) && is_array($_POST['app_tslips_pw']) ? $_POST['app_tslips_pw'] : [];
$app_tslips_pw_protected = isset($_POST['app_tslips_pw_protected']) && is_array($_POST['app_tslips_pw_protected']) ? $_POST['app_tslips_pw_protected'] : [];

// Convert URLs to clickable links for email display (HTML format)
$id_proof_text = urlsToClickableLinks($id_proof);
$direct_deposit_text = urlsToClickableLinks($direct_deposits);
$college_text = urlsToClickableLinks($college_receipt);
$t_slip_text = urlsToClickableLinks($t_slips);
$tax_summary_text = urlsToClickableLinks($tax_summary);
$additional_docs_text = urlsToClickableLinks($additional_docs);
$sin_number_document_text = urlsToClickableLinks($sin_number_document);
$summary_expenses = isset($_POST['summary_expenses']) ? $_POST['summary_expenses'] : '';


$claim_rent = isset($_POST['rent_benefit']) ? $_POST['rent_benefit'] : '';



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

// Collect Spouse T4/T4A/T Slips password arrays
$sp_tslips_pw = isset($_POST['sp_tslips_pw']) && is_array($_POST['sp_tslips_pw']) ? $_POST['sp_tslips_pw'] : [];
$sp_tslips_pw_protected = isset($_POST['sp_tslips_pw_protected']) && is_array($_POST['sp_tslips_pw_protected']) ? $_POST['sp_tslips_pw_protected'] : [];

// Convert spouse URLs to clickable links for email display (HTML format)
$spouse_id_proof_text = urlsToClickableLinks($spouse_id_proof);
$spouse_direct_deposit_text = urlsToClickableLinks($spouse_direct_deposits);
$spouse_college_text = urlsToClickableLinks($spouse_college_receipt);
$spouse_t_slip_text = urlsToClickableLinks($spouse_t_slips);
$spouse_tax_summary_text = urlsToClickableLinks($spouse_tax_summary);
$spouse_additional_docs_text = urlsToClickableLinks($spouse_additional_docs);
$spouse_sin_number_document_text = urlsToClickableLinks($spouse_sin_number_document);
$spouse_summary_expenses = isset($_POST['spouse_summary_expenses']) ? $_POST['spouse_summary_expenses'] : '';
$spouse_claim_rent=isset($_POST['spouse_rent_benefit']) ? $_POST['spouse_rent_benefit'] : '';


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


$rent_address_text = '';
$rent_address = (isset($_POST['group-a']) && is_array($_POST['group-a'])) ? $_POST['group-a'] : [];
foreach ($rent_address as $x) {
 if ( $claim_rent == 'Yes') {
    

    $rent_address_text .=
        "
         <tr>
        <th>Do you want to claim Your Rent benefit?</th>
            <td>$claim_rent</td>
    </tr>
        
        <tr>
            <th>Rent Address</th>
            <td>" . $x['rent_address'] . "</td>
        </tr>
        <tr>
            <th>From Date</th>
            <td>" . $x['from_month'] . " "  . $x['from_year'] .   "</td>
           
        </tr>
        
         <tr>
            <th>To Date</th>
            <td>" . $x['to_month'] . " "  . $x['to_year'] .   "</td>
           
        </tr>
       
        
        
        <tr>
            <th>Total Rent Paid</th>
            <td>" . $x['total_rent_paid']  . "</td>
        </tr>";
  } else {
               $rent_address_text .= "
    <tr>
        <th>Do you want to claim Your Rent benefit?</th>
            <td>$claim_rent</td>
    </tr>";
 
 }
}
  

$spouse_rent_address_text = '';
$spouse_rent_address = (isset($_POST['group-a']) && is_array($_POST['group-a'])) ? $_POST['group-a'] : [];
foreach ($spouse_rent_address as $x) {

   if ( $spouse_claim_rent == 'Yes') {
    
    $spouse_rent_address_text .=
        " 
        
     <tr>
        <th>Do you want to claim Your Rent benefit?</th>
            <td>$spouse_claim_rent</td>
    </tr>
        
        <tr>
            <th>Rent Address</th>
            <td>" . $x['spouse_rent_address'] . "</td>
        </tr>
        <tr>
            <th>From Date</th>
            <td>" . $x['spouse_from_month'] . " "  . $x['spouse_from_year'] .   "</td>
           
        </tr>
        
         <tr>
            <th>To Date</th>
            <td>" . $x['spouse_to_month'] . " "  . $x['spouse_to_year'] .   "</td>
           
        </tr>
       
        
        
        <tr>
            <th>Total Rent Paid</th>
            <td>" . $x['spouse_total_rent_paid']  . "</td>
        </tr>";
   
    } else {
              $spouse_rent_address_text .= "
    <tr>
        <th>Do you want to claim Your Rent benefit?</th>
            <td>$spouse_claim_rent</td>
    </tr>";
 
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

// Helper function to convert URLs to clickable HTML links for email display
function urlsToClickableLinks($input) {
    if (empty($input)) {
        return 'N/A';
    }
    
    // Split by <br> tags
    $urls = explode('<br>', $input);
    $links = [];
    
    foreach ($urls as $url) {
        $url = trim($url);
        if (empty($url)) {
            continue;
        }
        
        // Check if it's a valid URL
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            // Convert to clickable link
            $links[] = '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" style="color: #0066cc; text-decoration: underline;">' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a>';
        } else {
            // Not a valid URL, just display as text
            $links[] = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Return as HTML with line breaks, or N/A if no valid links
    return !empty($links) ? implode('<br>', $links) : 'N/A';
}

$urls = array();

// Helper function to build T4/T4A/T Slips email display with passwords (always shown)
function buildTslipsEmailDisplay($urls, $passwords, $pwProtected) {
    if (empty($urls)) {
        return '';
    }
    
    $html = '';
    foreach ($urls as $index => $url) {
        // Skip empty URLs
        if (empty(trim($url))) {
            continue;
        }
        
        // Convert URL to clickable link if valid
        $urlDisplay = $url;
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $urlDisplay = '<a href="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '" style="color: #0066cc; text-decoration: underline;">' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '</a>';
        } else {
            $urlDisplay = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
        }
        
        $html .= '<tr><th>T4/T4A/T Slips</th><td>' . $urlDisplay . '</td></tr>';
        
        // Always show password row - display password if available, otherwise "No Password"
        $passwordValue = 'No Password';
        if (isset($pwProtected[$index]) && 
            strtolower(trim($pwProtected[$index])) === 'yes' && 
            isset($passwords[$index]) && 
            !empty(trim($passwords[$index]))) {
            $passwordValue = trim($passwords[$index]);
        }
        $html .= '<tr><th>T4/T4A/T Slip password</th><td>' . htmlspecialchars($passwordValue) . '</td></tr>';
    }
    
    return $html;
}

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

// Build T4/T4A/T Slips email displays with passwords
$tSlipsEmailDisplay = buildTslipsEmailDisplay($tSlipsUrls, $app_tslips_pw, $app_tslips_pw_protected);
$spouse_tSlipsEmailDisplay = buildTslipsEmailDisplay($spouse_tSlipsUrls, $sp_tslips_pw, $sp_tslips_pw_protected);

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
        $spouse_tSlipsEmailDisplay
        
         <tr>
            <th></th>
            <td> $spouse_rent_address_text</td>
        </tr>
        
        <tr>
            <th>Additional Documents to Upload</th>
            <td>$spouse_additional_docs_text</td>
        </tr>
    
        $spouse_income_delivery_text
        <tr>
            <th colspan='2'></th>
        </tr>
    ";
}




//Set the subject line
// Prepare move date question rows (always show, even if empty)
$move_date_rows = '';
if ($another_province == 'Yes') {
    $move_date_rows = "
    <tr>
        <th>When Did You Move</th>
        <td>" . ($move_date ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Province moved From?</th>
        <td>" . ($move_from ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Province moved To?</th>
        <td>" . ($move_to ?: 'N/A') . "</td>
    </tr>";
} else {
    $move_date_rows = "
    <tr>
        <th>When Did You Move</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>Province moved From?</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>Province moved To?</th>
        <td>N/A</td>
    </tr>";
}

// Prepare children rows (always show)
$children_rows = '';
if ($have_child == 'Yes' && !empty($childrenPayload)) {
    foreach ($childrenPayload as $x) {
        $children_rows .= "
        <tr>
            <th>Child First Name</th>
            <td>" . (isset($x['child_first_name']) ? htmlspecialchars($x['child_first_name']) : '') . "</td>
        </tr>
        <tr>
            <th>Child Last Name</th>
            <td>" . (isset($x['child_last_name']) ? htmlspecialchars($x['child_last_name']) : '') . "</td>
        </tr>
        <tr>
            <th>Child Date of Birth</th>
            <td>" . (isset($x['child_date_birth']) ? htmlspecialchars($x['child_date_birth']) : '') . "</td>
        </tr>
        <tr>
            <th>Residing in Canada?</th>
            <td>" . (isset($x['child_residing_canada']) ? htmlspecialchars($x['child_residing_canada']) : '') . "</td>
        </tr>
        <tr>
            <th colspan='2'></th>
        </tr>";
    }
} else {
    $children_rows = "
    <tr>
        <th>Child Information</th>
        <td>No children</td>
    </tr>";
}

// Prepare spouse information rows
$spouse_info_rows = '';
if ($marital_status == "Single") {
    $spouse_info_rows = "";
} elseif (($marital_status == "Married") || ($marital_status == "Common in Law") || ($marital_status == "Seperated")) {
    $spouse_info_rows = "
    <tr>
        <th>Spouse Last Name</th>
        <td>" . ($spouse_lastname ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Spouse First Name</th>
        <td>" . ($spouse_firstname ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Spouse Date of Birth</th>
        <td>" . ($spouse_date_birth ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Date of Marriage</th>
        <td>" . ($date_marriage ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Residing in Canada</th>
        <td>" . ($residing_canada ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Spouse SIN</th>
        <td>" . ($spouse_sin ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Spouse Email Address</th>
        <td>" . ($spouse_email ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Spouse Phone Number</th>
        <td>" . ($spouse_phone ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Does your spouse want to file taxes?</th>
        <td>" . ($spouse_file_tax ?: 'N/A') . "</td>
    </tr>";
    if ($spouse_file_tax == 'Yes') {
        $spouse_info_rows .= "
        <tr>
            <th>Is this the first time your spouse filing tax</th>
            <td>" . ($spouse_first_tax ?: 'N/A') . "</td>
        </tr>
        $spouse_first_tax_text";
    } else {
        $spouse_info_rows .= "
        <tr>
            <th>Spouse Annual Income in CAD</th>
            <td>" . ($spouse_annual_income ?: 'N/A') . "</td>
        </tr>";
    }
    if ($residing_canada == "No") {
        $spouse_info_rows .= "
        <tr>
            <th>Spousal Annual Income outside Canada (Converted to CAD)</th>
            <td>" . ($spouse_annual_income_outside ?: 'N/A') . "</td>
        </tr>";
    }
} else {
    $spouse_info_rows = "
    <tr>
        <th>Date Of Marital status change</th>
        <td>" . ($marital_change ?: 'N/A') . "</td>
    </tr>";
}

// Prepare spouse documents rows (always show)
$spouse_docs_rows = '';
if ($spouse_firstname && $spouse_file_tax == 'Yes' && $residing_canada == 'Yes') {
    $spouse_docs_rows = "
    <tr>
        <th>ID Proof</th>
        <td>" . ($spouse_id_proof_text ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>SIN Number Document</th>
        <td>" . ($spouse_sin_number_document_text ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>Direct Deposit Form</th>
        <td>" . ($spouse_direct_deposit_text ?: 'N/A') . "</td>
    </tr>
    <tr>
        <th>T2202(College Receipt)</th>
        <td>" . ($spouse_college_text ?: 'N/A') . "</td>
    </tr>
    $spouse_tSlipsEmailDisplay
    <tr>
        <th>Additional Documents to Upload</th>
        <td>" . ($spouse_additional_docs_text ?: 'N/A') . "</td>
    </tr>";
    if (!empty($spouse_rent_address_text)) {
        $spouse_docs_rows .= $spouse_rent_address_text;
    }
    if (!empty($spouse_income_delivery_text)) {
        $spouse_docs_rows .= $spouse_income_delivery_text;
    }
} else {
    $spouse_docs_rows = "
    <tr>
        <th>ID Proof</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>SIN Number Document</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>Direct Deposit Form</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>T2202(College Receipt)</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>T4/T4A/T Slips</th>
        <td>N/A</td>
    </tr>
    <tr>
        <th>Additional Documents to Upload</th>
        <td>N/A</td>
    </tr>";
}

$message = "<html>
<head>
    <title>HTML Email with Table</title>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            text-align: left;
            margin-bottom: 20px;
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
    <!-- Pre Details Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Pre Details</th>
        </tr>
        <tr>
            <th>Did you move to another province?</th>
            <td>" . ($another_province ?: 'N/A') . "</td>
        </tr>
        $move_date_rows
    </table>

    <!-- Personal Information Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Personal Information</th>
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
    </table>

    <!-- Tax Filing Information Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Tax Filing Information</th>
        </tr>
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
    </table>

    <!-- Marital Status & Spouse Information Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Marital Status & Spouse Information</th>
        </tr>
        <tr>
            <th>Marital Status</th>
            <td>$marital_status</td>
        </tr>
        $spouse_info_rows
    </table>

    <!-- Children Information Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Children Information</th>
        </tr>
        <tr>
            <th>Do you have child</th>
            <td>" . ($have_child ?: 'N/A') . "</td>
        </tr>
        $children_rows
    </table>

    <!-- Applicant Documents Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Applicant Documents</th>
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
        $tSlipsEmailDisplay
        <tr>
            <th>Additional Documents to Upload</th>
            <td>$additional_docs_text</td>
        </tr>
        $rent_address_text
        $income_delivery_text
    </table>

    <!-- Spouse Documents Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Spouse Documents</th>
        </tr>
        $spouse_docs_rows
    </table>

    <!-- Additional Information Table -->
    <table style=\"text-align:left;\">
        <tr>
            <th colspan=\"2\" style=\"background-color: #f0f0f0; font-weight: bold; padding: 12px;\">Additional Information</th>
        </tr>
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
    // Server settings are applied by sendWithAutoTransport()

    //Recipients
    $mail->setFrom('paragonafs@gmail.com', 'Paragon AFS');  
    // Add primary recipient
    $mail->addAddress($to);
    // Add additional recipients if formSubmissionEmail is an array
    if (isset($formSubmissionEmail) && is_array($formSubmissionEmail) && count($formSubmissionEmail) > 1) {
        for ($i = 1; $i < count($formSubmissionEmail); $i++) {
            $mail->addAddress($formSubmissionEmail[$i]);
        }
    }
    
    // Add CC recipients if configured
    if (isset($formSubmissionCC) && !empty($formSubmissionCC)) {
        if (is_array($formSubmissionCC)) {
            foreach ($formSubmissionCC as $ccEmail) {
                if (!empty($ccEmail)) {
                    $mail->addCC($ccEmail);
                }
            }
        } else {
            $mail->addCC($formSubmissionCC);
        }
    }
    
    // Add BCC recipients if configured
    if (isset($formSubmissionBCC) && !empty($formSubmissionBCC)) {
        if (is_array($formSubmissionBCC)) {
            foreach ($formSubmissionBCC as $bccEmail) {
                if (!empty($bccEmail)) {
                    $mail->addBCC($bccEmail);
                }
            }
        } else {
            $mail->addBCC($formSubmissionBCC);
        }
    }
    
    // Let office easily reply back to client and improve engagement
    if (!empty($email)) {
        $mail->addReplyTo($email, trim($firstname . ' ' . $lastName));
    }


    // Attachments: prefer local files uploaded in this request (no HTTP fetch / no 404s in local)
    $attachedFilenames = []; // Track attached filenames to avoid duplicates
    if (!empty($uploadedAttachmentPaths) && is_array($uploadedAttachmentPaths)) {
        foreach ($uploadedAttachmentPaths as $path) {
            if (is_string($path) && file_exists($path)) {
                $filename = basename($path);
                $mail->addAttachment($path, $filename);
                $attachedFilenames[] = $filename;
                error_log("Email attachment added from local file: $path (as $filename)");
            } else {
                error_log("Email attachment skipped - file not found: $path");
            }
        }
    }

    // Fallback: attachments from URLs (legacy / previously stored links)
    // Only attach URLs that weren't already attached from local files
    foreach ($urls as $url) {
        if (empty(trim($url))) {
            continue;
        }
        
        $filename = basename($url);
        
        // Skip if this file was already attached from local path
        if (in_array($filename, $attachedFilenames)) {
            error_log("Email attachment skipped (already attached from local file): $url");
            continue;
        }
        
        // Try to fetch and attach from URL (for legacy/previously stored links)
        $content = @file_get_contents($url);
        if ($content !== false && !empty($content)) {
            $mail->addStringAttachment($content, $filename);
            error_log("Email attachment added from URL: $url (as $filename)");
        } else {
            error_log("Email attachment skipped (could not fetch or empty): $url");
        }
    }

    // Content
    $mail->isHTML(true);                                         // Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = strip_tags($message);                       // Plain text version for non-HTML mail clients

    $usedTransport = null;
    if (sendWithAutoTransport($mail, $usedTransport)) {
        $response['status'] = 'success';
        $response['transport'] = $usedTransport;
        $recipientEmailSent = true;
    } else {
        throw new Exception('Mailer send failed: ' . $mail->ErrorInfo);
    }
} catch (Exception $e) {
    $response['status'] = 'failed';
    $response['error'] = $mail->ErrorInfo;
    $recipientEmailSent = false;
    error_log("Failed to send email to recipient: " . $mail->ErrorInfo);
}

// Send confirmation email to client
if (isset($recipientEmailSent) && $recipientEmailSent && !empty($email)) {
    try {
        $clientMail = new PHPMailer(true);
        
        // Server settings are applied by sendWithAutoTransport()
        
        // Recipients
        $clientMail->setFrom('paragonafs@gmail.com', 'Paragon AFS');
        $clientMail->addAddress($email); // Client's email from form
        // If client replies, send it back to office inbox
        $clientMail->addReplyTo('info@paragonafs.ca', 'Paragon AFS');
        
        // Content
        $clientMail->isHTML(true);
        $clientMail->Subject = 'Thank You for Your Tax Submission - Paragon AFS';

        // Load client confirmation template from inside /jejy for easier editing
        ob_start();
        include __DIR__ . '/jejy/email_confirmation.php';
        $clientMessage = ob_get_clean();

        $clientMail->Body = $clientMessage;
        $clientMail->AltBody = strip_tags($clientMessage);
        
        $clientTransport = null;
        if (sendWithAutoTransport($clientMail, $clientTransport)) {
            error_log("Confirmation email sent successfully to client via $clientTransport: " . $email);
        } else {
            error_log("Confirmation email failed (all transports) to client: " . $email . " Error: " . $clientMail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log("Failed to send confirmation email to client ($email): " . $clientMail->ErrorInfo);
        // Don't fail the entire submission if client email fails
    }
}

// Send confirmation email to spouse (if Married and spouse email exists)
// This is completely isolated - any failures here will NOT affect the main submission
if (isset($recipientEmailSent) && $recipientEmailSent && 
    ($marital_status == "Married" || $marital_status == "Common Law" || $marital_status == "Common in Law") && 
    !empty($spouse_email)) {
    try {
        $spouseMail = new PHPMailer(true);
        
        // Server settings are applied by sendWithAutoTransport()
        
        // Recipients
        $spouseMail->setFrom('paragonafs@gmail.com', 'Paragon AFS');
        $spouseMail->addAddress($spouse_email); // Spouse's email from form
        // If spouse replies, send it back to office inbox
        $spouseMail->addReplyTo('info@paragonafs.ca', 'Paragon AFS');
        
        // Content
        $spouseMail->isHTML(true);
        $spouseMail->Subject = 'Thank You for Your Tax Submission - Paragon AFS';

        // Load spouse confirmation template from inside /jejy for easier editing
        // Ensure all required variables are available for the template
        $spouse_firstname = $spouse_firstname ?? '';
        $spouse_lastname = $spouse_lastname ?? '';
        $firstname = $firstname ?? '';
        $lastName = $lastName ?? '';
        
        $templatePath = __DIR__ . '/jejy/email_confirmation_spouse.php';
        if (!file_exists($templatePath)) {
            error_log("Spouse confirmation template not found: $templatePath - skipping spouse email");
            throw new Exception("Template file not found");
        }
        
        ob_start();
        include $templatePath;
        $spouseMessage = ob_get_clean();
        
        if (empty($spouseMessage)) {
            error_log("Spouse confirmation template produced empty output - skipping spouse email");
            throw new Exception("Template produced empty output");
        }

        $spouseMail->Body = $spouseMessage;
        $spouseMail->AltBody = strip_tags($spouseMessage);
        
        $spouseTransport = null;
        if (sendWithAutoTransport($spouseMail, $spouseTransport)) {
            error_log("Confirmation email sent successfully to spouse via $spouseTransport: " . $spouse_email);
        } else {
            $errorInfo = isset($spouseMail) && isset($spouseMail->ErrorInfo) ? $spouseMail->ErrorInfo : 'Unknown error';
            error_log("Confirmation email failed (all transports) to spouse: " . $spouse_email . " Error: " . $errorInfo);
        }
    } catch (Throwable $e) {
        // Catch any exception/error - use Throwable to catch both Exception and Error
        $errorMsg = isset($spouseMail) && isset($spouseMail->ErrorInfo) ? $spouseMail->ErrorInfo : $e->getMessage();
        error_log("Failed to send confirmation email to spouse ($spouse_email): " . $errorMsg);
        // Don't fail the entire submission if spouse email fails - silently continue
    }
}

// SQL query to update tax_information table with latest Jejy form data
// Uses session email as the key for the current logged-in user
$yes_file_submitted = 'Yes';

// Build a comprehensive UPDATE mapping Jejy/gmailapi fields to tax_information columns
$sql = 'UPDATE tax_information SET
    first_name = ?,
    last_name = ?,
    gender = ?,
    apartment_unit_number = ?,
    ship_address = ?,
    locality = ?,
    state = ?,
    postcode = ?,
    country = ?,
    birth_date = ?,
    sin_number = ?,
    phone = ?,
    another_province = ?,
    move_date = ?,
    move_from = ?,
    move_to = ?,
    first_fillingtax = ?,
    canada_entry = ?,
    birth_country = ?,
    year1 = ?,
    year1_income = ?,
    year2 = ?,
    year2_income = ?,
    year3 = ?,
    year3_income = ?,
    file_paragon = ?,
    years_tax_return = ?,
    marital_status = ?,
    spouse_first_name = ?,
    spouse_last_name = ?,
    spouse_date_birth = ?,
    date_marriage = ?,
    spouse_annual_income = ?,
    residing_canada = ?,
    spouse_annual_income_outside = ?,
    have_child = ?,
    marital_change = ?,
    spouse_sin = ?,
    spouse_phone = ?,
    spouse_email = ?,
    spouse_file_tax = ?,
    spouse_first_tax = ?,
    spouse_canada_entry = ?,
    spouse_birth_country = ?,
    spouse_year1 = ?,
    spouse_year1_income = ?,
    spouse_year2 = ?,
    spouse_year2_income = ?,
    spouse_year3 = ?,
    spouse_year3_income = ?,
    spouse_file_paragon = ?,
    spouse_years_tax_return = ?,
    first_time_buyer = ?,
    purchase_first_home = ?,
    direct_deposits = ?,
    id_proof = ?,
    spouse_id_proof = ?,
    college_receipt = ?,
    spouse_t_slips = ?,
    spouse_t_slips_passwords = ?,
    t_slips = ?,
    t_slips_passwords = ?,
    rent_address = ?,
    tax_summary = ?,
    income_delivery = ?,
    spouse_income_delivery = ?,
    summary_expenses = ?,
    delivery_hst = ?,
    spouse_delivery_hst = ?,
    hst_number = ?,
    spouse_hst_number = ?,
    hst_access_code = ?,
    spouse_hst_access_code = ?,
    hst_start_date = ?,
    spouse_hst_start_date = ?,
    hst_end_date = ?,
    spouse_hst_end_date = ?,
    additional_docs = ?,
    message_us = ?,
    is_file_submit = ?,
    file_submit_date = NOW()
    WHERE email = ?';

// Use uploaded URL strings (not the human-readable *_text variants) for DB columns
$direct_deposits_db        = $direct_deposits ?? '';
$id_proof_db               = $id_proof ?? '';
$spouse_id_proof_db        = $spouse_id_proof ?? '';
$college_receipt_db        = $college_receipt ?? '';
$t_slips_db                = $t_slips ?? '';
// Store passwords as JSON (array of password strings, indexed by file position)
$t_slips_passwords_db      = !empty($app_tslips_pw) ? json_encode($app_tslips_pw) : '';
$spouse_t_slips_db         = $spouse_t_slips ?? '';
$spouse_t_slips_passwords_db = !empty($sp_tslips_pw) ? json_encode($sp_tslips_pw) : '';
$rent_address_db           = $rent_address_text ?? '';
$tax_summary_db            = $tax_summary ?? '';
$income_delivery_db        = $income_delivery ?? '';
$spouse_income_delivery_db = $spouse_income_delivery ?? '';
$summary_expenses_db       = $summary_expenses ?? '';
$delivery_hst_db           = $delivery_hst ?? '';
$spouse_delivery_hst_db    = $spouse_delivery_hst ?? '';
$hst_number_db             = $hst_number ?? '';
$spouse_hst_number_db      = $spouse_hst_number ?? '';
$hst_access_code_db        = $hst_access_code ?? '';
$spouse_hst_access_code_db = $spouse_hst_access_code ?? '';
$hst_start_date_db         = $hst_start_date ?? '';
$spouse_hst_start_date_db  = $spouse_hst_start_date ?? '';
$hst_end_date_db           = $hst_end_date ?? '';
$spouse_hst_end_date_db    = $spouse_hst_end_date ?? '';
$additional_docs_db        = $additional_docs ?? '';
$message_us_db             = $message_us ?? '';

// Keep DB format compatible with the legacy form: encrypt sensitive fields where expected.
function enc_if_needed($v) {
    if (!isset($v) || $v === '') return '';
    $enc = encrypt_decrypt('encrypt', $v);
    return ($enc === false || $enc === null) ? '' : $enc;
}
$birth_date_db   = enc_if_needed($birth_date);
$sin_number_db   = enc_if_needed($sin_number);
$phone_db        = enc_if_needed($phone);
$spouse_dob_db   = enc_if_needed($spouse_date_birth);
$spouse_sin_db   = enc_if_needed($spouse_sin);
$spouse_phone_db = enc_if_needed($spouse_phone);
$spouse_email_db = enc_if_needed($spouse_email);

// Prepare and execute the UPDATE
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param(
        str_repeat('s', 81),
        $firstname,
        $lastName,
        $gender,
        $apartment_unit_number,
        $ship_address,
        $locality,
        $state,
        $postcode,
        $country,
        $birth_date_db,
        $sin_number_db,
        $phone_db,
        $another_province,
        $move_date,
        $move_from,
        $move_to,
        $first_fillingtax,
        $canada_entry,
        $birth_country,
        $year1,
        $year1_income,
        $year2,
        $year2_income,
        $year3,
        $year3_income,
        $file_paragon,
        $years_tax_return,
        $marital_status,
        $spouse_firstname,
        $spouse_lastname,
        $spouse_dob_db,
        $date_marriage,
        $spouse_annual_income,
        $residing_canada,
        $spouse_annual_income_outside,
        $have_child,
        $marital_change,
        $spouse_sin_db,
        $spouse_phone_db,
        $spouse_email_db,
        $spouse_file_tax,
        $spouse_first_tax,
        $spouse_canada_entry,
        $spouse_birth_country,
        $spouse_year1,
        $spouse_year1_income,
        $spouse_year2,
        $spouse_year2_income,
        $spouse_year3,
        $spouse_year3_income,
        $spouse_file_paragon,
        $spouse_years_tax_return,
        $first_time_buyer,
        $purchase_first_home,
        $direct_deposits_db,
        $id_proof_db,
        $spouse_id_proof_db,
        $college_receipt_db,
        $spouse_t_slips_db,
        $spouse_t_slips_passwords_db,
        $t_slips_db,
        $t_slips_passwords_db,
        $rent_address_db,
        $tax_summary_db,
        $income_delivery_db,
        $spouse_income_delivery_db,
        $summary_expenses_db,
        $delivery_hst_db,
        $spouse_delivery_hst_db,
        $hst_number_db,
        $spouse_hst_number_db,
        $hst_access_code_db,
        $spouse_hst_access_code_db,
        $hst_start_date_db,
        $spouse_hst_start_date_db,
        $hst_end_date_db,
        $spouse_hst_end_date_db,
        $additional_docs_db,
        $message_us_db,
        $yes_file_submitted,
        $_SESSION['email']
    );
    if (!$stmt->execute()) {
        error_log('Failed to update tax_information for ' . $_SESSION['email'] . ': ' . $stmt->error);
    }
} else {
    error_log('Failed to prepare tax_information UPDATE: ' . $conn->error);
}

error_log("Client " . encrypt_decrypt('decrypt', $_SESSION['email']) . " successfully submitted documents!");


function sendToGoogleSheet($formData) {
    $client = new Google\Client();
    // Local/dev safeguard: don't crash submission if Google credentials.json is missing
    $credPath = __DIR__ . '/credentials.json';
    if (!file_exists($credPath)) {
        error_log("Google Sheets sync skipped: credentials.json not found at " . $credPath);
        return false;
    }

    $client->setAuthConfig($credPath); // Path to your JSON file
    $client->addScope(Google\Service\Sheets::SPREADSHEETS);

    $service = new Google\Service\Sheets($client);

    // Google Sheet ID and Range
    $spreadsheetId = '1V5sklhnd4DRRe0j_xM5P4VZM7lG9ACKS9oEBbZmcyqE'; // Replace with your Google Sheet ID
    $range = 'Sheet1'; // Append to the next empty row

   $fullName = $formData['firstName'] . ' ' . $formData['lastName'];

      // Generate current date in YYYY-MM-DD format
    $currentDate = date('Y-m-d');

    $values = [
        [$fullName, $formData['email'], $formData['phone'], $formData['sin_number'],$currentDate,'Pending']
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
    $firstName = $_POST['firstName'] ?? '';
    $lastName = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $sin = $_POST['sin_number'] ?? '';

 // Combine firstName and lastName into fullName
    $fullName = $firstName . ' ' . $lastName;

    try {
        $result = sendToGoogleSheet([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phone' => $phone,
            'sin_number' => $sin
        ]);
    } catch (Throwable $e) {
        // Never fail the submission due to Sheets sync
        error_log("Google Sheets sync failed: " . $e->getMessage());
    }


  
}

echo json_encode($response);



?>
