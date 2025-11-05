<?php
session_start();

// email SESSION CHECK SET OR NOT
if (!isset($_SESSION['email'])) {
    header('location: ./auth');
    exit();
}

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

function responseUserSpouse($firstname, $spouse_firstname, $spouse_file_tax, $residing_canada)
{
    $response = [
        'firstname' => $firstname,
        'spouse_firstname' => $spouse_firstname,
        'spouse_file_tax' => $spouse_file_tax,
        'residing_canada' => $residing_canada
    ];

    return $response;
}

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


$firstname = isset($_POST['firstName']) ? $_POST['firstName'] : '';
$lastName = isset($_POST['lastName']) ? $_POST['lastName'] : '';
$gender = isset($_POST['gender']) ? $_POST['gender'] : '';

$apartment_unit_number = isset($_POST['apartment_unit_number']) ? $_POST['apartment_unit_number'] : '';
$ship_address = isset($_POST['ship_address']) ? $_POST['ship_address'] : '';
$locality = isset($_POST['locality']) ? $_POST['locality'] : '';
$state = isset($_POST['state']) ? sanitizeInput($_POST['state']) : '';
$postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
$country = isset($_POST['country']) ? $_POST['country'] : '';

function sanitizeInput($input) {
    // Remove leading and trailing whitespaces
    $input = trim($input);    
    // Remove any HTML tags
    $input = strip_tags($input);
    // Limit the length to fit the database column size
    $input = substr($input, 0, 255);
    return $input;
}

$email = isset($_POST['email']) ? $_POST['email'] : '';
$sin_number = isset($_POST['sin_number']) ? $_POST['sin_number'] : '';
$phone = isset($_POST['phone']) ? $_POST['phone'] : '';
$birth_date = isset($_POST['birth_date']) ? $_POST['birth_date'] : '';

$encrypted_email = encrypt_decrypt('encrypt', $email);
$encrypted_sin_number = encrypt_decrypt('encrypt', $sin_number);
$encrypted_phone = encrypt_decrypt('encrypt', $phone);
$encrypted_birth_date = encrypt_decrypt('encrypt', $birth_date);

$another_province = isset($_POST['another_province']) ? $_POST['another_province'] : '';
$move_date = isset($_POST['move_date']) ? $_POST['move_date'] : '';
$move_from = isset($_POST['move_from']) ? $_POST['move_from'] : '';
$move_to = isset($_POST['move_to']) ? $_POST['move_to'] : '';
if ($another_province == 'No') {
    $move_date = '';
    $move_from = '';
    $move_to = '';
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
    $file_paragon = '';
    $years_tax_return = '';
} else {
    $canada_entry = '';
    $birth_country = '';
    $year1 = '';
    $year1_income = '';
    $year2 = '';
    $year2_income = '';
    $year3 = '';
    $year3_income = '';
}
$marital_status = isset($_POST['marital_status']) ? $_POST['marital_status'] : '';
$spouse_firstname = isset($_POST['spouse_firstname']) ? $_POST['spouse_firstname'] : '';
$spouse_lastname = isset($_POST['spouse_lastname']) ? $_POST['spouse_lastname'] : '';
$date_marriage = isset($_POST['date_marriage']) ? $_POST['date_marriage'] : '';
$spouse_annual_income = isset($_POST['spouse_annual_income']) ? $_POST['spouse_annual_income'] : '';
$residing_canada = isset($_POST['residing_canada']) ? $_POST['residing_canada'] : '';
$spouse_annual_income_outside = isset($_POST['spouse_annual_income_outside']) ? $_POST['spouse_annual_income_outside'] : '';



$have_child = isset($_POST['have_child']) ? $_POST['have_child'] : "";
$marital_change = isset($_POST['marital_change']) ? $_POST['marital_change'] : "";

$spouse_email = isset($_POST['spouse_email']) ? $_POST['spouse_email'] : "";
$spouse_sin = isset($_POST['spouse_sin']) ? $_POST['spouse_sin'] : "";
$spouse_phone = isset($_POST['spouse_phone']) ? $_POST['spouse_phone'] : "";
$spouse_date_birth = isset($_POST['spouse_date_birth']) ? $_POST['spouse_date_birth'] : '';

$encrypted_spouse_email = encrypt_decrypt('encrypt', $spouse_email);
$encrypted_spouse_sin = encrypt_decrypt('encrypt', $spouse_sin);
$encrypted_spouse_phone = encrypt_decrypt('encrypt', $spouse_phone);
$encrypted_spouse_date_birth = encrypt_decrypt('encrypt', $spouse_date_birth);

$spouse_file_tax = isset($_POST['spouse_file_tax']) ? $_POST['spouse_file_tax'] : "";
$spouse_first_tax = isset($_POST['spouse_first_tax']) ? $_POST['spouse_first_tax'] : "";
$spouse_canada_entry = isset($_POST['spouse_canada_entry']) ? $_POST['spouse_canada_entry'] : "";
$spouse_birth_country = isset($_POST['spouse_birth_country']) ? $_POST['spouse_birth_country'] : "";
$spouse_year1 = isset($_POST['spouse_year1']) ? $_POST['spouse_year1'] : "";
$spouse_year1_income = isset($_POST['spouse_year1_income']) ? $_POST['spouse_year1_income'] : "";
$spouse_year2 = isset($_POST['spouse_year2']) ? $_POST['spouse_year2'] : "";
$spouse_year2_income = isset($_POST['spouse_year2_income']) ? $_POST['spouse_year2_income'] : "";
$spouse_year3 = isset($_POST['spouse_year3']) ? $_POST['spouse_year3'] : "";
$spouse_year3_income = isset($_POST['spouse_year3_income']) ? $_POST['spouse_year3_income'] : "";
$spouse_file_paragon = isset($_POST['spouse_file_paragon']) ? $_POST['spouse_file_paragon'] : "";
$spouse_years_tax_return = isset($_POST['spouse_years_tax_return']) ? $_POST['spouse_years_tax_return'] : "";

if ($marital_status  == 'Single') {
    $spouse_firstname = '';
    $spouse_lastname = '';
    $encrypted_spouse_date_birth = '';
    $date_marriage = '';
    $spouse_annual_income = '';
    $residing_canada = '';
    $spouse_annual_income_outside = '';
    $have_child = '';
    $spouse_first_tax = '';
    $marital_change = '';
    $encrypted_spouse_sin = '';
    $encrypted_spouse_phone = '';
    $encrypted_spouse_email= '';
    $spouse_file_tax = '';
    $spouse_canada_entry = '';
    $spouse_birth_country = '';
    $spouse_year1 = '';
    $spouse_year1_income = '';
    $spouse_year2 = '';
    $spouse_year2_income = '';
    $spouse_year3 = '';
    $spouse_year3_income = '';
    $spouse_file_paragon = '';
    $spouse_years_tax_return = '';
} else if ($marital_status  == 'Married' || $marital_status  == 'Common in Law') {
    $marital_change = '';
} else {
    $spouse_firstname = '';
    $spouse_lastname = '';
    $encrypted_spouse_date_birth = '';
    $date_marriage = '';
    $spouse_annual_income = '';
    $residing_canada = '';
    $spouse_annual_income_outside = '';
    $have_child = '';
    $spouse_first_tax = '';
    $encrypted_spouse_sin = '';
    $encrypted_spouse_phone = '';
    $encrypted_spouse_email= '';
    $spouse_file_tax = '';
    $spouse_canada_entry = '';
    $spouse_birth_country = '';
    $spouse_year1 = '';
    $spouse_year1_income = '';
    $spouse_year2 = '';
    $spouse_year2_income = '';
    $spouse_year3 = '';
    $spouse_year3_income = '';
    $spouse_file_paragon = '';
    $spouse_years_tax_return = '';
}

$child_first_name = isset($_POST['data']) ? json_encode($_POST['data']) : '';

if ($have_child == 'No') {
    $child_first_name = '';
}

if ($spouse_file_tax == 'No') {
    $spouse_first_tax = '';
    $spouse_canada_entry = '';
    $spouse_birth_country = '';
    $spouse_year1 = '';
    $spouse_year1_income = '';
    $spouse_year2 = '';
    $spouse_year2_income = '';
    $spouse_year3 = '';
    $spouse_year3_income = '';
    $spouse_file_paragon = '';
    $spouse_years_tax_return = '';
} else {
    $spouse_annual_income = '';
}

if ($spouse_first_tax == 'No') {
    $spouse_canada_entry = '';
    $spouse_birth_country = '';
    $spouse_year1 = '';
    $spouse_year1_income = '';
    $spouse_year2 = '';
    $spouse_year2_income = '';
    $spouse_year3 = '';
    $spouse_year3_income = '';
} else {
    $spouse_file_paragon = '';
    $spouse_years_tax_return = '';
}


$first_time_buyer = isset($_POST['first_time_buyer']) ? $_POST['first_time_buyer'] : "";
$purchase_first_home = isset($_POST['purchase_first_home']) ? $_POST['purchase_first_home'] : "";

if ($first_time_buyer == 'No') {
    $purchase_first_home = '';
}

// SQL query to insert values into the tax_information table
$sql = 'UPDATE tax_information SET first_name=?, last_name=?, gender=?, apartment_unit_number=?, ship_address=?, locality=?, state=?, postcode=?, country=?, birth_date=?, sin_number=?, phone=?, email=?, another_province=?, move_date=?, move_from=?, move_to=?, first_fillingtax=?, canada_entry=?, birth_country=?, year1=?, year1_income=?, year2=?, year2_income=?, year3=?, year3_income=?, file_paragon=?, years_tax_return=?, marital_status=?, spouse_first_name=?, spouse_last_name=?, spouse_date_birth=?, date_marriage=?, spouse_annual_income=?, residing_canada=?, spouse_annual_income_outside=?, have_child=?, marital_change=?, spouse_sin=?, spouse_phone=?, spouse_email=?, spouse_file_tax=?, spouse_first_tax=?, spouse_canada_entry=?, spouse_birth_country=?, spouse_year1=?, spouse_year1_income=?, spouse_year2=?, spouse_year2_income=?, spouse_year3=?, spouse_year3_income=?, spouse_file_paragon=?, spouse_years_tax_return=?, child_first_name=?, first_time_buyer=?, purchase_first_home=? WHERE email=?';
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sssssssssssssssssssssssssssssssssssssssssssssssssssssssss", $firstname, $lastName, $gender, $apartment_unit_number, $ship_address, $locality, $state, $postcode, $country, $encrypted_birth_date, $encrypted_sin_number, $encrypted_phone, $encrypted_email, $another_province, $move_date, $move_from, $move_to, $first_fillingtax, $canada_entry, $birth_country, $year1, $year1_income, $year2, $year2_income, $year3, $year3_income, $file_paragon, $years_tax_return, $marital_status, $spouse_firstname, $spouse_lastname, $encrypted_spouse_date_birth,  $date_marriage, $spouse_annual_income, $residing_canada, $spouse_annual_income_outside, $have_child, $marital_change, $encrypted_spouse_sin, $encrypted_spouse_phone, $encrypted_spouse_email, $spouse_file_tax, $spouse_first_tax, $spouse_canada_entry, $spouse_birth_country, $spouse_year1, $spouse_year1_income, $spouse_year2, $spouse_year2_income, $spouse_year3, $spouse_year3_income, $spouse_file_paragon, $spouse_years_tax_return, $child_first_name, $first_time_buyer, $purchase_first_home, $encrypted_email);

    if ($stmt->execute()) {
        $output = responseUserSpouse($firstname, $spouse_firstname, $spouse_file_tax, $residing_canada);
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Prepare statement error: " . $conn->error;
}

echo json_encode($output);
$conn->close();
?>
