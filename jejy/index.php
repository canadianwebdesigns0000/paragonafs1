<?php
session_start();
// error_reporting(0);
include '../auth/config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['email'])) {
    header('location:../auth');
    exit();
} else {
    // Query To Get User Data
    $userData = $db->prepare('SELECT * FROM tax_information WHERE email=?');
    $userData->execute(array($_SESSION['email']));
    $rowUser = $userData->fetch(PDO::FETCH_ASSOC);
      
    if (!$rowUser) {
        // SQL query to insert values into the tax_information table
        $query        = 'INSERT INTO `tax_information` SET is_file_submit=?, file_submit_date=?, first_name=?, last_name=?, gender=?, apartment_unit_number=?, ship_address=?, locality=?, state=?, postcode=?, country=?, birth_date=?, sin_number=?, phone=?, email=?, another_province=?, move_date=?, move_from=?, move_to=?, first_fillingtax=?, canada_entry=?, birth_country=?, year1=?, year1_income=?, year2=?, year2_income=?, year3=?, year3_income=?, file_paragon=?, years_tax_return=?, marital_status=?, spouse_first_name=?, spouse_last_name=?, spouse_date_birth=?, date_marriage=?, spouse_annual_income=?, residing_canada=?, spouse_annual_income_outside=?, have_child=?, marital_change=?, spouse_sin=?, spouse_phone=?, spouse_email=?, spouse_file_tax=?, spouse_first_tax=?, spouse_canada_entry=?, spouse_birth_country=?, spouse_year1=?, spouse_year1_income=?, spouse_year2=?, spouse_year2_income=?, spouse_year3=?, spouse_year3_income=?, spouse_file_paragon=?, spouse_years_tax_return=?, child_first_name=?, first_time_buyer=?, purchase_first_home=?, direct_deposits=?, id_proof=?, spouse_id_proof=?, college_receipt=?, spouse_t_slips=?, t_slips=?, rent_address=?, tax_summary=?, income_delivery=?, summary_expenses=?, delivery_hst=?, hst_number=?, hst_access_code=?, hst_start_date=?, hst_end_date=?, additional_docs=?, message_us=?';
        $parameters = array("No", '', isset($_SESSION['first_name']) ? $_SESSION['first_name'] : '', isset($_SESSION['last_name']) ? $_SESSION['last_name'] : '', '', '', '', '', '', '', '', '', '', isset($_SESSION['phone']) ? $_SESSION['phone'] : '', $_SESSION['email'], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
        $statement = $db->prepare($query);
        $statement->execute($parameters);

        header('location:./');
        exit();
    } else {
        // Check if session email matches $rowUser['email']
        if ($_SESSION['email'] != $rowUser['email']) {
            // Redirect or handle the case where emails do not match
            header('location:../auth');
            exit();
        }
    }

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

// Safe decrypt (handles already-plain values gracefully)
function safe_decrypt_value($v) {
    if ($v === null || $v === '') return '';
    $d = encrypt_decrypt('decrypt', $v);
    return ($d !== false && $d !== null && $d !== '') ? $d : $v;
}
function set_if_empty(&$arr, $key, $val) {
    if (!isset($arr[$key]) || $arr[$key] === '') $arr[$key] = $val;
}

// Normalize DB row to the field keys used throughout jejy/index.php
if (isset($rowUser) && is_array($rowUser)) {
    // Decrypted/plain aliases used by the Jejy form
    set_if_empty($rowUser, 'dob', safe_decrypt_value($rowUser['birth_date'] ?? ''));
    set_if_empty($rowUser, 'sin', safe_decrypt_value($rowUser['sin_number'] ?? ''));
    set_if_empty($rowUser, 'phone_plain', safe_decrypt_value($rowUser['phone'] ?? ''));
    set_if_empty($rowUser, 'email_plain', safe_decrypt_value($rowUser['email'] ?? ''));

    // Address aliases used by inputs
    set_if_empty($rowUser, 'street', $rowUser['ship_address'] ?? '');
    set_if_empty($rowUser, 'unit', $rowUser['apartment_unit_number'] ?? '');
    set_if_empty($rowUser, 'city', $rowUser['locality'] ?? '');
    set_if_empty($rowUser, 'province', $rowUser['state'] ?? '');
    set_if_empty($rowUser, 'postal', $rowUser['postcode'] ?? '');

    // Tax panel (Jejy uses yes/no; DB often stores Yes/No)
    set_if_empty($rowUser, 'first_time', strtolower((string)($rowUser['first_fillingtax'] ?? '')));
    set_if_empty($rowUser, 'paragon_prior', strtolower((string)($rowUser['file_paragon'] ?? '')));
    set_if_empty($rowUser, 'return_years', (string)($rowUser['years_tax_return'] ?? ''));
    set_if_empty($rowUser, 'entry_date', (string)($rowUser['canada_entry'] ?? ''));
    set_if_empty($rowUser, 'moved_province', strtolower((string)($rowUser['another_province'] ?? '')));
    set_if_empty($rowUser, 'moved_date', (string)($rowUser['move_date'] ?? ''));
    set_if_empty($rowUser, 'prov_from', (string)($rowUser['move_from'] ?? ''));
    set_if_empty($rowUser, 'prov_to', (string)($rowUser['move_to'] ?? ''));
    set_if_empty($rowUser, 'first_time_buyer', strtolower((string)($rowUser['first_time_buyer'] ?? '')));
    set_if_empty($rowUser, 'first_home_purchase', (string)($rowUser['purchase_first_home'] ?? ''));

    // Some UI uses moved_date_display; default to ISO/date string if display not stored
    set_if_empty($rowUser, 'moved_date_display', (string)($rowUser['moved_date'] ?? ''));

    // Marital dates (static fields)
    set_if_empty($rowUser, 'date_marriage', (string)($rowUser['date_marriage'] ?? ''));
    set_if_empty($rowUser, 'marital_change', (string)($rowUser['marital_change'] ?? ''));

    // Children (static field)
    set_if_empty($rowUser, 'have_child', strtolower((string)($rowUser['have_child'] ?? '')));
    set_if_empty($rowUser, 'children_data', $rowUser['child_first_name'] ?? '');

    // World income periods (user) - static for first-time filers
    set_if_empty($rowUser, 'year1', (string)($rowUser['year1'] ?? ''));
    set_if_empty($rowUser, 'year2', (string)($rowUser['year2'] ?? ''));
    set_if_empty($rowUser, 'year3', (string)($rowUser['year3'] ?? ''));
    set_if_empty($rowUser, 'year1_income', (string)($rowUser['year1_income'] ?? ''));
    set_if_empty($rowUser, 'year2_income', (string)($rowUser['year2_income'] ?? ''));
    set_if_empty($rowUser, 'year3_income', (string)($rowUser['year3_income'] ?? ''));

    // Gig income (user) - static fields
    set_if_empty($rowUser, 'gig', strtolower((string)($rowUser['income_delivery'] ?? '')));
    set_if_empty($rowUser, 'gig_expenses', (string)($rowUser['summary_expenses'] ?? ''));
    set_if_empty($rowUser, 'gigHst', strtolower((string)($rowUser['delivery_hst'] ?? '')));
    set_if_empty($rowUser, 'hst_number', (string)($rowUser['hst_number'] ?? ''));
    set_if_empty($rowUser, 'hst_access', (string)($rowUser['hst_access_code'] ?? ''));
    set_if_empty($rowUser, 'hst_start', (string)($rowUser['hst_start_date'] ?? ''));
    set_if_empty($rowUser, 'hst_end', (string)($rowUser['hst_end_date'] ?? ''));
}

// Spouse personal UI uses $rowSpouse (derive from tax_information spouse_* columns)
if (!isset($rowSpouse) || !is_array($rowSpouse)) $rowSpouse = [];
if (isset($rowUser) && is_array($rowUser)) {
    set_if_empty($rowSpouse, 'first_name', (string)($rowUser['spouse_first_name'] ?? ''));
    set_if_empty($rowSpouse, 'last_name', (string)($rowUser['spouse_last_name'] ?? ''));
    set_if_empty($rowSpouse, 'dob', safe_decrypt_value($rowUser['spouse_date_birth'] ?? ''));
    set_if_empty($rowSpouse, 'sin', safe_decrypt_value($rowUser['spouse_sin'] ?? ''));
    set_if_empty($rowSpouse, 'phone', safe_decrypt_value($rowUser['spouse_phone'] ?? ''));
    set_if_empty($rowSpouse, 'email', safe_decrypt_value($rowUser['spouse_email'] ?? ''));
    set_if_empty($rowSpouse, 'income_cad', (string)($rowUser['spouse_annual_income'] ?? ''));
    set_if_empty($rowSpouse, 'income_outside_cad', (string)($rowUser['spouse_annual_income_outside'] ?? ''));
}

// Spouse tax UI uses $rowSpouseTax (derive from tax_information spouse_* tax columns)
if (!isset($rowSpouseTax) || !is_array($rowSpouseTax)) $rowSpouseTax = [];
if (isset($rowUser) && is_array($rowUser)) {
    set_if_empty($rowSpouseTax, 'first_time', strtolower((string)($rowUser['spouse_first_tax'] ?? '')));
    set_if_empty($rowSpouseTax, 'paragon_prior', strtolower((string)($rowUser['spouse_file_paragon'] ?? '')));
    set_if_empty($rowSpouseTax, 'return_years', (string)($rowUser['spouse_years_tax_return'] ?? ''));
    set_if_empty($rowSpouseTax, 'entry_date', (string)($rowUser['spouse_canada_entry'] ?? ''));
    set_if_empty($rowSpouseTax, 'birth_country', (string)($rowUser['spouse_birth_country'] ?? ''));

    // Spouse world income periods - static for first-time filers
    set_if_empty($rowSpouseTax, 'year1', (string)($rowUser['spouse_year1'] ?? ''));
    set_if_empty($rowSpouseTax, 'year2', (string)($rowUser['spouse_year2'] ?? ''));
    set_if_empty($rowSpouseTax, 'year3', (string)($rowUser['spouse_year3'] ?? ''));
    set_if_empty($rowSpouseTax, 'inc_y1', (string)($rowUser['spouse_year1_income'] ?? ''));
    set_if_empty($rowSpouseTax, 'inc_y2', (string)($rowUser['spouse_year2_income'] ?? ''));
    set_if_empty($rowSpouseTax, 'inc_y3', (string)($rowUser['spouse_year3_income'] ?? ''));

    // Spouse gig income - static fields
    set_if_empty($rowSpouseTax, 'spGig', strtolower((string)($rowUser['spouse_income_delivery'] ?? '')));
    set_if_empty($rowSpouseTax, 'spGigHst', strtolower((string)($rowUser['spouse_delivery_hst'] ?? '')));
    set_if_empty($rowSpouseTax, 'sp_hst_number', (string)($rowUser['spouse_hst_number'] ?? ''));
    set_if_empty($rowSpouseTax, 'sp_hst_access', (string)($rowUser['spouse_hst_access_code'] ?? ''));
    set_if_empty($rowSpouseTax, 'sp_hst_start', (string)($rowUser['spouse_hst_start_date'] ?? ''));
    set_if_empty($rowSpouseTax, 'sp_hst_end', (string)($rowUser['spouse_hst_end_date'] ?? ''));
}

// Prepare children data for JavaScript pre-population
$childrenListJSON = '[]';
if (isset($rowUser) && is_array($rowUser) && isset($rowUser['children_data']) && $rowUser['children_data'] !== '') {
    $childrenRaw = $rowUser['children_data'];
    // If already JSON, decode and re-encode for JS consumption
    if (is_string($childrenRaw)) {
        $childrenArray = json_decode($childrenRaw, true);
        if (is_array($childrenArray) && count($childrenArray) > 0) {
            // Transform to the format expected by the CHILDREN data store
            $transformedChildren = [];
            foreach ($childrenArray as $child) {
                $transformedChildren[] = [
                    'id' => 'db_' . uniqid(),
                    'first_name' => $child['child_first_name'] ?? '',
                    'last_name' => $child['child_last_name'] ?? '',
                    'dob' => $child['child_date_birth'] ?? '',
                    'dob_display' => '', // Will be computed by JS
                    'in_canada' => isset($child['child_residing_canada']) && $child['child_residing_canada'] === 'No' ? 'No' : 'Yes'
                ];
            }
            $childrenListJSON = json_encode($transformedChildren, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        }
    }
}

// Write the message to the server's error log
error_log("Client Logged In: " . encrypt_decrypt("decrypt", $_SESSION['email']));

?>


<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="referrer" content="no-referrer-when-downgrade" />
    <title> Apply For Personal Tax</title>

    <link rel="icon" type="image/x-icon" href="../assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" type="text/css" href="../multi-form.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/foundation-datepicker.css">
     <link rel="stylesheet" href="../assets/css/styles-jejy.css">
 
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.0/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" type="text/css" />

<script src="https://www.google.com/recaptcha/api.js" async defer></script>


    <script type="module" src="../assets/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
    <script src="../multi-form.js"></script>

    <script>
        $(document).ready(function() {

            window.addEventListener("load", () => {
                $("html,body").animate({
                    scrollTop: 0
                }, 100); //100ms for example

                const loader = document.querySelector(".loader");

                if (loader) {  // Safe fix: null check before accessing classList
                    loader.classList.add("loader--hidden");

                    loader.addEventListener("transitionend", () => {
                        document.body.removeChild(loader);
                    });
                }
            });

            

            $('#hide_movedate').click(function() {
                $('#movedate').hide()
            })
            $('#show_movedate').click(function() {
                $('#movedate').show()
            })
            var showMoveDateRadio = document.getElementById('show_movedate');
            if (showMoveDateRadio && showMoveDateRadio.checked) {  // Safe fix: null check
                $('#movedate').show()
            } else {
                $('#movedate').hide()
            }


            $('#hide_filingtax').click(function() {
                $('#filingtax').hide()
                $('#no_filingtax').show()
            })
            $('#show_filingtax').click(function() {
                $('#filingtax').show()
                $('#no_filingtax').hide()
            })
            var show_filingtaxRadio = document.getElementById('show_filingtax');
            var hide_filingtaxRadio = document.getElementById('hide_filingtax');
            if (show_filingtaxRadio && hide_filingtaxRadio && (show_filingtaxRadio.checked || hide_filingtaxRadio.checked)) {  // Safe fix: null checks
                if (show_filingtaxRadio.checked) {
                    $('#filingtax').show()
                    $('#no_filingtax').hide()
                } else {
                    $('#filingtax').hide()
                    $('#no_filingtax').show()
                }
            }

            $('#body_purchase_first_home').hide()
            $('#first_time_buyer_yes').click(function() {
                $('#body_purchase_first_home').show()
            })
            $('#first_time_buyer_no').click(function() {
                $('#body_purchase_first_home').hide()
            })
            var show_first_time_buyer = document.getElementById('first_time_buyer_yes');
            var hide_first_time_buyer = document.getElementById('first_time_buyer_no');
            if (show_first_time_buyer && hide_first_time_buyer && (show_first_time_buyer.checked || hide_first_time_buyer.checked)) {  // Safe fix: null checks
                if (show_first_time_buyer.checked) {
                    $('#body_purchase_first_home').show()
                } else {
                    $('#body_purchase_first_home').hide()
                }
            }

            var show_marital_single = document.getElementById('marital_single');
            var show_marital_married = document.getElementById('marital_married');
            var show_marital_common = document.getElementById('marital_common');
            var show_marital_widow = document.getElementById('marital_widow');
            var show_marital_divorce = document.getElementById('marital_divorce');
            var show_marital_seperated = document.getElementById('marital_seperated');

            // Safe fix: null checks before accessing .checked property
            if (show_marital_single && show_marital_married && show_marital_common && show_marital_widow && show_marital_divorce && show_marital_seperated &&
                (show_marital_single.checked || show_marital_married.checked || show_marital_common.checked || show_marital_widow.checked || show_marital_divorce.checked || show_marital_seperated.checked)) {
                if (show_marital_single.checked) {
                    $('#body_marital_status').hide();
                    $('#body_marital_change').hide();
                } else if (show_marital_married.checked || show_marital_common.checked) {
                    $('#body_marital_status').show();
                    $('#body_marital_change').hide();
                } else {
                    $('#body_marital_status').hide();
                    $('#body_marital_change').show();
                }
            }
            
            $('#marital_single').click(function() {
                $('#body_marital_status').hide();
                $('#body_marital_change').hide();
            })
            $('#marital_married, #marital_common').click(function() {
                $('#body_marital_status').show();
                $('#body_marital_change').hide();
            })
            $('#marital_widow, #marital_divorce, #marital_seperated').click(function() {
                $('#body_marital_status').hide();
                $('#body_marital_change').show();
            })


            $('#residing_canada_yes').click(function() {
                $('#spouse_residing_canada').show();
                $('#spouse_not_residing_canada').hide();
            })

            $('#residing_canada_no').click(function() {
                $('#spouse_residing_canada').hide();
                $('#spouse_not_residing_canada').show();
            })
            var show_residing_canada_yes = document.getElementById('residing_canada_yes');
            var show_residing_canada_no = document.getElementById('residing_canada_no');
            if (show_residing_canada_yes.checked || show_residing_canada_no.checked) {
                if (show_residing_canada_yes.checked) {
                    $('#spouse_residing_canada').show()
                    $('#spouse_not_residing_canada').hide()
                } else {
                    $('#spouse_residing_canada').hide()
                    $('#spouse_not_residing_canada').show()
                }
            }


            $('#spouse_file_tax_yes').click(function() {
                $('#spouse_want_taxes').show();
                $('#body_spouse_want_file_tax').hide();
            })

            $('#spouse_file_tax_no').click(function() {
                $('#spouse_want_taxes').hide();
                $('#body_spouse_want_file_tax').show();
            })
            var show_spouse_file_tax_yes = document.getElementById('spouse_file_tax_yes');
            var show_spouse_file_tax_no = document.getElementById('spouse_file_tax_no');
            if (show_spouse_file_tax_yes.checked || show_spouse_file_tax_no.checked) {
                if (show_spouse_file_tax_yes.checked) {
                    $('#spouse_want_taxes').show();
                    $('#body_spouse_want_file_tax').hide();
                } else {
                    $('#spouse_want_taxes').hide();
                    $('#body_spouse_want_file_tax').show();
                }
            }


            $('#spouse_first_tax_yes').click(function() {
                $('#spouse_filingtax').show()
                $('#no_spouse_filingtax').hide()
            })

            $('#spouse_first_tax_no').click(function() {
                $('#spouse_filingtax').hide()
                $('#no_spouse_filingtax').show()
            })
            var show_spouse_first_tax_yes = document.getElementById('spouse_first_tax_yes');
            var show_spouse_first_tax_no = document.getElementById('spouse_first_tax_no');
            if (show_spouse_first_tax_yes.checked || show_spouse_first_tax_no.checked) {
                if (show_spouse_first_tax_yes.checked) {
                    $('#spouse_filingtax').show()
                    $('#no_spouse_filingtax').hide()
                } else {
                    $('#spouse_filingtax').hide()
                    $('#no_spouse_filingtax').show()
                }
            }

            $('#have_child_yes').click(function() {
                $('#have_child_body').show()
            })
            $('#have_child_no').click(function() {
                $('#have_child_body').hide()
            })
            var show_have_child_yes = document.getElementById('have_child_yes');
            if (show_have_child_yes.checked) {
                $('#have_child_body').show()
            } else {
                $('#have_child_body').hide()
            }

            var show_delivery_tax_yes = document.getElementById('show_delivery_tax');
            if (show_delivery_tax_yes.checked) {
                $('#upload_delivery_annual_tax').show()
            } else {
                $('#upload_delivery_annual_tax').hide()
            }
            $('#show_delivery_tax').click(function() {
                $('#upload_delivery_annual_tax').show()
            })
            $('#hide_delivery_tax').click(function() {
                $('#upload_delivery_annual_tax').hide()
            })


            var spouse_show_delivery_tax_yes = document.getElementById('spouse_show_delivery_tax');
            if (spouse_show_delivery_tax_yes.checked) {
                $('#spouse_upload_delivery_annual_tax').show()
            } else {
                $('#spouse_upload_delivery_annual_tax').hide()
            }
            $('#spouse_show_delivery_tax').click(function() {
                $('#spouse_upload_delivery_annual_tax').show()
            })
            $('#spouse_hide_delivery_tax').click(function() {
                $('#spouse_upload_delivery_annual_tax').hide()
            })

            var show_show_hst = document.getElementById('show_hst');
            if (show_show_hst.checked) {
                $('#hst').show()
            } else {
                $('#hst').hide()
            }
            $('#show_hst').click(function() {
                $('#hst').show()
            })
            $('#hide_hst').click(function() {
                $('#hst').hide()
            })

            var spouse_show_show_hst = document.getElementById('spouse_show_hst');
            if (spouse_show_show_hst.checked) {
                $('#spouse_hst').show()
            } else {
                $('#spouse_hst').hide()
            }
            $('#spouse_show_hst').click(function() {
                $('#spouse_hst').show()
            })
            $('#spouse_hide_hst').click(function() {
                $('#spouse_hst').hide()
            })
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function() {
            $.validator.addMethod(
                'date',
                function(value, element, param) {
                    return value != 0 && value <= 31 && value == parseInt(value, 10)
                },
                'Please enter a valid date!'
            )
            $.validator.addMethod(
                'month',
                function(value, element, param) {
                    return value != 0 && value <= 12 && value == parseInt(value, 10)
                },
                'Please enter a valid month!'
            )
            $.validator.addMethod(
                'year',
                function(value, element, param) {
                    return value != 0 && value >= 1900 && value == parseInt(value, 10)
                },
                'Please enter a valid year not less than 1900!'
            )
            $.validator.addMethod(
                'username',
                function(value, element, param) {
                    var nameRegex = /^[a-zA-Z0-9]+$/
                    return value.match(nameRegex)
                },
                'Only a-z, A-Z, 0-9 characters are allowed'
            )

            var val = {
                // Specify validation rules
                rules: {
                    lastName: 'required',
                    gender: 'required',
                    ship_address: 'required',
                    locality: 'required',
                    another_province: 'required',
                    move_date: "required",
                    move_from: 'required',
                    move_to: 'required',
                    first_fillingtax: 'required',
                    canada_entry: "required",
                    birth_country: 'required',
                    year1: 'required',
                    year1_income: 'required',
                    year2: 'required',
                    year2_income: 'required',
                    year3: 'required',
                 
                rent_benefit:'required',
                    year3_income: 'required',
                    years_tax_return: 'required',
                    spouse_firstname: 'required',
                    spouse_lastname: 'required',
                    spouse_date_birth: 'required',
                    date_marriage: 'required',
                    spouse_annual_income: 'required',
                    spouse_sin: 'required',
                    spouse_phone: 'required',
                    spouse_email: 'required',
                    spouse_canada_entry: 'required',
                    spouse_birth_country: 'required',
                    spouse_year1: 'required',
                    spouse_year1_income: 'required',
                    spouse_year2: 'required',
                    spouse_year2_income: 'required',
                    spouse_year3: 'required',
                    spouse_year3_income: 'required',
                    spouse_years_tax_return: 'required',
                    marital_change: 'required',
                    marital_status: 'required',
                    spouse_in_canada: 'required',
                    children: 'required',
                    email: {
                        required: true,
                        email: true,
                    },
                    phone: {
                        required: true,
                        digits: true,
                    },
                    date: {
                        date: true,
                        required: true,
                        minlength: 2,
                        maxlength: 2,
                        digits: true,
                    },
                    month: {
                        month: true,
                        required: true,
                        minlength: 2,
                        maxlength: 2,
                        digits: true,
                    },
                    year: {
                        year: true,
                        required: true,
                        minlength: 4,
                        maxlength: 4,
                        digits: true,
                    },
                    username: {
                        username: true,
                        required: true,
                        minlength: 4,
                        maxlength: 16,
                    },
                    password: {
                        required: true,
                        minlength: 8,
                        maxlength: 16,
                    },
                    sin_number: {
                        minlength: 9,
                        maxlength: 9,
                        digits: true,
                    },
                    child_first_name: {
                        required: true,
                    },
                    hst_number: 'required',
                    hst_access_code: 'required',
                    hst_start_date: 'required',
                    hst_end_date: 'required',
                },
                errorPlacement: function(error, element) {
                    if (element.is(":radio")) {
                        //alert('oj');
                        error.insertAfter(element.parent().parent());
                    } else { // This is the default behavior of the script for all fields
                        error.insertAfter(element);
                    }
                },
                // Specify validation error messages
                messages: {
                    lastName: 'Last name is required',
                    gender: 'Gender is required',
                    ship_address: 'Address is required',
                    locality: 'City is required',
                    state: 'State is required',
                    postcode: 'Postal Code is required',
                    country: 'Country/Region is required',
                    birth_date: 'Birthdate is required',
                    // sin_number: 'SIN Number is required',
                
                
                    
                    rent_benefit: 'This field is required',

                  
                    move_date: 'Where did you move is required',
                    move_from: 'Provinced moved From is required',
                    move_to: 'Province moved To is required',
                    canada_entry: 'Date of Entry in Canada is required',
                    birth_country: 'Birth Country is required',
                    year1: 'Year 1 is required',
                    year1_income: 'Year 1 Income is required',
                    year2: 'Year 2 is required',
                    year2_income: 'Year 2 Income is required',
                    year3: 'Year 3 is required',
                    year3_income: 'Year 3 Income is required',
                    years_tax_return: 'Which years do you want to file tax return is required',
                    spouse_firstname: 'Spouse First Name is required',
                    spouse_lastname: 'Spouse Last Name is required',
                    spouse_date_birth: 'Spouse Date of Birth is required',
                    date_marriage: 'Date of Marriage is required',
                    spouse_annual_income: 'Spouse Annual Income in CAD is required',
                    spouse_sin: 'Spouse SIN is required',
                    spouse_phone: 'Spouse Phone Number is required',
                    spouse_email: 'Spouse Email Address is required',
                    spouse_canada_entry: 'Date of Entry in Canada is required',
                    spouse_birth_country: 'Birth Country is required',
                    spouse_year1: 'Year 1 is required',
                    spouse_year1_income: 'Year 1 income is required',
                    spouse_year2: 'Year 2 is required',
                    spouse_year2_income: 'Year 2 income is required',
                    spouse_year3: 'Year 3 is required',
                    spouse_year3_income: 'Year 3 income is required',
                    spouse_years_tax_return: 'This field is required',
                    marital_change: 'required',
                  
                    "data[0][child_first_name]": 'Required',
                    "data[0][child_last_name]": 'Required',
                    "data[0][child_date_birth]": 'Required',
                    "data[0][child_residing_canada]": 'Required',
                    "data[1][child_first_name]": 'Required',
                    "data[1][child_last_name]": 'Required',
                    "data[1][child_date_birth]": 'Required',
                    "data[1][child_residing_canada]": 'Required',
                    
                    "data[2][child_first_name]": 'Required',
                    "data[2][child_last_name]": 'Required',
                    "data[2][child_date_birth]": 'Required',
                    "data[2][child_residing_canada]": 'Required',
                
                    "data[3][child_first_name]": 'Required',
                    "data[3][child_last_name]": 'Required',
                    "data[3][child_date_birth]": 'Required',
                    "data[3][child_residing_canada]": 'Required',
                
                    "data[4][child_first_name]": 'Required',
                    "data[4][child_last_name]": 'Required',
                    "data[4][child_date_birth]": 'Required',
                    "data[4][child_residing_canada]": 'Required',
                
                    // another_province: 'Did you move to another province is required',
                    // first_fillingtax: 'Is this the first time you are filing tax is required',
                    marital_status: 'Marital Status is required',
                    spouse_in_canada: 'Residing in Canada is required',
                    children: 'This field is required',
                    // first_time_buyer: 'First Time Buyer is required',
                    summary_expenses: 'Summary of Expense is required',
                    email: {
                        required: 'Email is required',
                        email: 'Please enter a valid e-mail',
                    },
                    phone: {
                        required: 'Phone number is requied',
                        digits: 'Only numbers are allowed in this field',
                    },
                    date: {
                        required: 'Date is required',
                        minlength: 'Date should be a 2 digit number, e.i., 01 or 20',
                        maxlength: 'Date should be a 2 digit number, e.i., 01 or 20',
                        digits: 'Date should be a number',
                    },
                    month: {
                        required: 'Month is required',
                        minlength: 'Month should be a 2 digit number, e.i., 01 or 12',
                        maxlength: 'Month should be a 2 digit number, e.i., 01 or 12',
                        digits: 'Only numbers are allowed in this field',
                    },
                    year: {
                        required: 'Year is required',
                        minlength: 'Year should be a 4 digit number, e.i., 2018 or 1990',
                        maxlength: 'Year should be a 4 digit number, e.i., 2018 or 1990',
                        digits: 'Only numbers are allowed in this field',
                    },
                    username: {
                        required: 'Username is required',
                        minlength: 'Username should be minimum 4 characters',
                        maxlength: 'Username should be maximum 16 characters',
                    },
                    password: {
                        required: 'Password is required',
                        minlength: 'Password should be minimum 8 characters',
                        maxlength: 'Password should be maximum 16 characters',
                    },
                    child_first_name: {
                        required: 'Child First Name is required',
                    },
                    hst_number: 'HST # is required',
                    hst_access_code: 'Access Code is required',
                    hst_start_date: 'Start Date is required',
                    hst_end_date: 'End Date is required',
                },
            }

            // Safe fix: Check if multiStepForm plugin is loaded
            if (typeof $.fn.multiStepForm !== 'undefined') {
            $('#myForm')
                .multiStepForm({
                    // defaultStep:0,
                    beforeSubmit: function(form, submit) {
                        console.log('called before submiting the form')
                        console.log(form)
                        console.log(submit)
                    },
                    validations: val,
                    // focusInvalid: true, // Automatically focus on the first invalid element
                })
                .navigateTo(0);
            } else {
                console.warn('multiStepForm plugin not loaded. Please include multi-form.js');
            }
        })
    </script>

    <style>
        button {
            color: white;
            letter-spacing: 1px;
            text-transform: normal;
            letter-spacing: 1.3px;
            background-color: #0075be;
            border-radius: 2px;
            border: none;
            padding: 12px 30px;
            font-size: 14px;
            cursor: pointer;
        }

        .ui-widget-header {
            border: none !important;
            background: none !important;
            color: #333333;
            font-weight: bold;
        }

        .ui-state-default, .ui-widget-content .ui-state-default {
            border: none !important;
            background: none !important;
            text-align: center;
            font-weight: normal;
            color: #454545;
        }
        .ui-state-active, .ui-widget-content .ui-state-active {
            border: 1px solid #003eff !important;
            background: #007fff !important;
            font-weight: normal;
            color: #ffffff;
        }

        .ui-state-highlight, .ui-widget-content .ui-state-highlight {
            border: 1px solid #dad55e !important;
            background: #fffa90 !important;
            color: #777620;
        }

    .custom-dropdown {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .custom-dropdown select {
        appearance: none; 
        -webkit-appearance: none;
        -moz-appearance: none;
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background: white;
        font-size: 16px;
        cursor: pointer;
    }

    .custom-dropdown::after {
        content: "▼"; 
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #666;
        pointer-events: none;
    }

    </style>
    
    
    <style>
/* container */
.tax-wrap {
  max-width: 1100px;
  margin: 0 auto;
  padding-top: 20px;
}


.intro-layout {
  display: flex;
  gap: 24px;
  align-items: flex-start;
}

@media (max-width: 1024px) {
  .intro-layout {
    flex-direction: column;
  }
}


.old-form-sidebar {
  flex: 0 0 280px;
  position: sticky;
  top: 100px;
}

.old-form-sidebar-content {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 20px;
}

.old-form-sidebar-title {
  font-size: 16px;
  font-weight: 600;
  color: #334155;
  margin: 0 0 12px 0;
  line-height: 1.4;
}

.old-form-sidebar-text {
  font-size: 14px;
  color: #64748b;
  margin: 0 0 16px 0;
  line-height: 1.5;
}

.old-form-btn {
  display: inline-block;
  background: #fff;
  color: #334155;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  text-align: center;
  transition: all 0.2s ease;
  cursor: pointer;
  width: 100%;
  box-sizing: border-box;
}

.old-form-btn:hover {
  background: #f9fafb;
  border-color: #9ca3af;
  color: #1f2937;
}

.old-form-btn:active {
  background: #f3f4f6;
}

@media (max-width: 1024px) {
  .old-form-sidebar {
    flex: 0 0 auto;
    width: 100%;
    position: static;
    margin-top: 24px;
  }
}

/* card */
.tax-card{
  background: var(--card);
  border: 1px solid #ffffff;
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  padding: 0 100px 48px;

  /* center layout defaults */
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

/* eyebrow */
.tax-eyebrow{
  display: inline-block;
  font-size: 14px;
  font-weight: 600;
  color: #0369a1;
  background: #e0f2fe;
  border: 1px solid #bae6fd;
  padding: 6px 10px;
  border-radius: 999px;
  margin-bottom: 16px;
}

/* title LEFT */
.tax-title{
  font-size: clamp(32px, 4vw, 24px);
  line-height: 1.1;
  margin: 0 0 24px;
  font-weight: 800;
  letter-spacing: -0.02em;
  text-align: left !important;
  align-self: stretch;          
}

/* subtext LEFT */
.tax-sub{
  font-size: 16px;
  color: var(--muted);
  margin: 0 0 24px;
  max-width:1000px;
  text-align: left !important;
  align-self: stretch;
}

/* list block (kept left) */
.tax-list{
  list-style: none;
  margin: 28px 0 8px;
  padding: 0;
  display: grid;
  gap: 22px;
  max-width: 760px;
  text-align: left;
}

.tax-item{
  display: grid;
  grid-template-columns: 44px 1fr;
  gap: 16px;
  align-items: start;
}

/* make the icon column wider so layout stays stable */
.tax-item{
  grid-template-columns: 36px 1fr;   /* was 24px */
  align-items: start;
}

/* bigger icon box */
.tax-check{
  width: 36px;
  height: 36px;
  display: grid;
  place-items: center;
  background: transparent;
  border: 0;
  padding: 0;
}

/* scale the svg precisely */
.tax-check svg{
  width: 24px;   /* increase to taste: 22–28 looks good */
  height: 24px;
  display: block;   /* removes inline gaps */
}

.tax-item h3{ margin: 0 0 4px; font-size: 18px; }
.tax-muted{ color: var(--muted); }

.tax-muted a{ color: #0284c7; text-decoration: none; font-weight: bold; }
.tax-muted a:hover{ text-decoration: underline; }
/* legal note (centered as a block) */
.tax-legal{
  margin: 24px auto 16px;
  color: var(--muted);
  font-size: 14.5px;
  max-width: 900px;
  text-align: center;
}

.tax-legal a{ color: #0284c7; text-decoration: none; font-weight: bold; }
.tax-legal a:hover{ text-decoration: underline; }

/* CTA: button + note stacked and centered */
.tax-cta{
  margin-top: 34px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
}

/* form wrapper reset (prevents white box) */
.tax-cta form{
  background: transparent !important;
  padding: 0 !important;
  margin: 0 !important;
  border: 0 !important;
  border-radius: 0 !important;
  box-shadow: none !important;
  display: inline-block;
}

/* outline button (no white fill) */
.tax-btn, #button{
  background: transparent;
  border: 2px solid #0284c7;
  color: #0284c7;
  box-shadow: none;
  border-radius: 24px;
  font-weight: bold;
}

.tax-btn:hover, #button:hover{
  filter: none;
  background: #0284c7;
  color: #fff;
}

.continue-btn, #button{
  background: #0284c7;
  border: 2px solid #0284c7;
  color: #fff;
  box-shadow: none;
  border-radius: 24px !important;
  font-weight: bold;

}
.continue-btn:hover, #button:hover{
  background: #0575ae;
  color: #fff;
}


.continue:active, #button:active{
  transform: translateY(0);
}

.tax-btn:active, #button:active{
  transform: translateY(0);
}
/* tiny note */
.tax-tiny{ margin-top: 10px; font-size: 13px; color: #64748b; }

/* Submit processing helper text (Review submit) */
.submit-status{
  margin-top: 8px;
  font-size: 13px;
  color: #64748b;
  text-align: center;
  max-width: 520px;
}

/* Processing dots animation (inline) */
.proc-dots{
  display:inline-flex;
  gap:3px;
  margin-left:8px;
  vertical-align:middle;
}
.proc-dots span{
  width:6px;
  height:6px;
  border-radius:999px;
  background: rgba(255,255,255,.92);
  display:inline-block;
  animation: procDot 1.05s infinite ease-in-out;
}
.proc-dots span:nth-child(2){ animation-delay: .15s; opacity:.85; }
.proc-dots span:nth-child(3){ animation-delay: .30s; opacity:.7; }
@keyframes procDot{
  0%, 80%, 100% { transform: translateY(0); opacity: .45; }
  40% { transform: translateY(-4px); opacity: 1; }
}

/* responsive */
@media (max-width: 720px){
  .tax-title{   font-size: clamp(32px, 4vw, 30px);
}
  .tax-card{ padding: 36px 22px; }
  .tax-item{ grid-template-columns: 36px 1fr; }
  .tax-check{ width: 36px; height: 36px; }
.tax-sub{
  padding-left: 20px;

.tax-wrap {
  margin: 0;
  padding: 0 0 72px !important;
}

}
}

</style>

<!-- SECOND PAGE CSS -->

<style>

/* same grid feel, but for radios */
.qs-choicegrid{
  display:grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap:12px 16px;
}
.qs-choicegrid label{
  display:flex; align-items:center; gap:12px; cursor:pointer;
}

/* make radios bigger + blue */
.qs-choicegrid input[type="radio"]{
  width:22px; height:22px; min-width:22px;
  accent-color:#0284c7;     /* blue dot + ring */
  cursor:pointer;
}
.qs-choicegrid input[type="radio"]:focus-visible{
  outline:none;
  box-shadow:0 0 0 3px rgba(11,102,195,.25);
  border-radius:50%;
}

          
/* ===== Questionnaire layout ===== */
.qs-wrap { max-width: 980px; margin: 0 auto; }
.qs-lead { font-size: 16px; color: #475569; margin: 6px 0 24px; max-width: 70ch; }
.qs-title {
  font-size: 36px;
  font-weight: 800; letter-spacing: -0.02em; margin: 8px 0 35px; text-align: left;
}
.qs-label { display: block; margin: 20px 0 10px! important; font-size: clamp(18px, 2vw, 24px); font-weight: 800; }
.qs-note, .qs-help { color: #64748b; font-size: 14px; }


.qs-label {
  display:block;
  margin:0 0 10px;
  font-size:clamp(18px, 2vw, 24px);
  font-weight:800;
}

.qs-note,
.qs-help {
  color:#64748b;
  font-size:14px;
}

/* Base help: neutral white background */
.qs-help {
  position:relative;
  background:#fff;
  border:none; /* Removed border as requested */
  border-radius:6px;
  padding:10px 12px 10px 24px;  /* Left padding for icon (16px) + gap (8px) */
  margin:12px 0;
  color:#334155;
  line-height:1.4;
  text-align:left;
}

/* the little info icon on the left (kept yellow) */
.qs-help::before {
  content:"!";
  position:absolute;
  left:0;  /* Left-aligned without padding */
  top:50%;
  transform:translateY(-50%);
  width:16px;
  height:16px;
  border-radius:50%;
  background:#f4b400;  /* darker yellow/orange */
  color:#fff;
  font-weight:700;
  font-size:12px;
  display:flex;
  align-items:center;
  justify-content:center;
  flex-shrink:0;
}

/* Address autocomplete dropdown */
.addr-suggest{
  position:absolute;
  top:100%; /* Position below the input field */
  left:0;
  right:0;
  z-index:10000;
  width:100%;
  max-height:320px; /* Increased from 220px to show ~7-8 items before scrollbar */
  overflow-y:auto;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:8px;
  box-shadow:0 10px 25px rgba(15,23,42,0.12);
  margin-top:4px; /* Small gap between input and dropdown */
  padding:4px 0;
  opacity:0;
  transform:translateY(4px);
  transition:opacity .18s ease, transform .18s ease;
}

/* Mobile responsiveness for autocomplete dropdowns */
@media (max-width: 640px) {
  .addr-suggest {
    max-height: 260px; /* Proportionally increased from 180px to match desktop scaling */
    border-radius: 6px;
    margin-top: 2px;
  }
  .addr-suggest button {
    padding: 12px 16px; /* Larger touch targets on mobile */
    font-size: 15px;
  }
}
.addr-suggest.show{
  opacity:1;
  transform:translateY(0);
}
.addr-suggest button{
  display:block;
  width:100%;
  text-align:left;
  background:transparent;
  border:0;
  padding:10px 12px;
  cursor:pointer;
  color:#0f172a;
  font-size:16px; /* Match input field font size */
}
.addr-suggest button:hover,
.addr-suggest button:focus-visible{
  background:#f1f5f9;
  outline:none;
}
.addr-suggest .addr-sub{
  display:block;
  font-size:12px;
  color:#6b7280;
}

/* Cap long option lists */
.xsel-list,
.ms-options{
  max-height:260px;
  overflow-y:auto;
}
/* Alert variant (keep yellow panel) */
.qs-help.qs-help-alert {
  background:#fff6dc;
  border:none; /* Removed border as requested */
  color:#3f4a5a;
}

/* checkbox grid */
.qs-checkgrid {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px 16px;
}
.qs-checkgrid label { display: flex; align-items: center; gap: 12px; cursor: pointer; font-size: 16px; }
.qs-checkgrid input[type="checkbox"] {
  width: 22px; height: 22px; min-width: 22px; accent-color: #0284c7; cursor: pointer;
}
.qs-checkgrid input[type="checkbox"]:hover { filter: brightness(1.05); }
.qs-checkgrid input[type="checkbox"]:focus-visible {
  outline: none; box-shadow: 0 0 0 3px rgba(11,102,195,.25); border-radius: 6px;
}

/* ===== Yes / No buttons ===== */
.yn-group { display: flex; gap: 18px; margin-bottom: 25px;}
.yn-group input[type="radio"] { display: none; }

.yn-btn {
  padding: 10px 26px; border-radius: 8px; font-weight: 700; line-height: 1; cursor: pointer;
  user-select: none;
  background: #fff; color: #0284c7; border: 2px solid #0284c7;
  transition: background-color .15s ease, color .15s ease, border-color .15s ease,
              box-shadow .15s ease, transform .05s ease;
}
.yn-btn:hover { background: #eaf4ff; box-shadow: 0 0 0 3px rgba(11,102,195,.12) inset; }
.yn-btn:active { transform: translateY(1px); }
.yn-btn:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(11,102,195,.35); }

/* selected => solid blue */
.yn-group input[type="radio"]:checked + .yn-btn {
  background: #0284c7; color: #fff; border-color: #0284c7;
}
.yn-group input[type="radio"]:checked + .yn-btn:hover {
  filter: brightness(1.05);
}

 .tax-btn-secondary{
  background:#fff;
  border:2px solid #0284c7;  
  color:#0284c7;               
  border-radius:24px;
  font-weight:700;
  padding:12px 50px;
  box-shadow:none;
  cursor:pointer;
}
.tax-btn-secondary:hover, #button:hover{
  filter: none;
  background: #0284c7;
  color: #fff;
}.tax-btn-secondary:active{ transform:translateY(1px); }
                         
/* Keep blocks full-width within centered card */
.qs-title, .qs-block { align-self: stretch; text-align: left; margin-top: 25px}

                         /* place buttons on one line (desktop default) */
.tax-cta-row{
  display:flex;
  flex-direction:row;
  align-items:center;
  gap:16px;                 /* space between Back and Continue */
  justify-content:center;
}

/* Mobile: make Back / Continue share the row 50 / 50 */
@media (max-width: 640px){
  .tax-cta-row{
    justify-content:space-between;
    flex-wrap:wrap;
  }
  .tax-cta-row .tax-btn-secondary,
  .tax-cta-row .continue-btn{
    flex:1 1 0;
    max-width:48%;
    box-sizing:border-box;
  }
  /* Submit status appears below buttons on mobile */
  .tax-cta-row .submit-status{
    flex-basis:100%;
    width:100%;
    margin-top:12px;
    order:3;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) { .yn-btn { transition: none; } }

/* Small screens */
@media (max-width: 720px) { .qs-lead { margin-bottom: 18px; } }
                    
                    
                    
</style>



<!-- THIRD PAGE CSS -->

<style>

.pi-layout{
  display:flex;
  gap:32px;
  align-items:flex-start;
}

/* left panel */
.pi-side{
  flex:0 0 260px;                 /* fixed-ish width */
  max-width:300px;
  position:sticky; top:16px;      /* stays in view while scrolling */
}
.pi-side-title{
  font-size:18px; font-weight:800; margin:0 0 12px; text-align:start;
}
.pi-steps { display:flex; flex-direction:column; gap:8px; }
/* Sidebar list */
.pi-step{
  position:relative;
  display:block;
  padding:10px 12px 10px 40px !important;   /* space for bullet */
  border-radius:8px;
  color:#334155;
  text-decoration:none;
  transition:background .15s ease, color .15s ease;
}

.pi-step.is-current{
  font-weight:700;
  color:#0b66c3;
}

/* right content */
.pi-main{
  flex:1 1 auto;
  min-width:0;                    /* prevents overflow */
}

/* responsive: stack on mobile */
@media (max-width: 960px){
  .pi-layout{ flex-direction:column; }
  .pi-side{ position:static; width:100%; max-width:none; }
  .pi-steps{ flex-wrap:wrap; gap:8px; }
  .pi-step{ display:inline-block; }
}

                    
                    
                    /* ===== Personal Info two-column layout (fixed) ===== */
.pi-layout {
  display: grid;
  grid-template-columns: 260px 1fr;
  gap: 40px;
  align-items: start;
}

/* Sidebar */
.pi-side {
  position: sticky;
  top: 100px; /* adjust if navbar height changes */
  align-self: start;
}
.pi-side-title {
  font-size: 18px;
  font-weight: 800;
  margin-bottom: 16px;
}
.pi-steps {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.pi-step {
  display: block;
  padding: 10px 12px 10px 40px;
  border-radius: 8px;
  color: #334155;
  text-decoration: none;
  font-size: 15px;
  transition: all 0.15s ease;
  text-align: left;
}


                    
.pi-step.is-current {
  font-weight: 700;
  color: #0b66c3;
}

/* Main content (right side) */
.pi-main {
  min-width: 0;
}
.pi-main h1.qs-title {
  margin-top: 0;
}

/* Responsive (mobile view: stack vertically) */
@media (max-width: 960px) {
  .pi-layout {
    grid-template-columns: 1fr;
  }
  .pi-side {
    position: static;
    margin-bottom: 20px;
  }
}
                    

.pi-step:hover{ background:#f8fafc; cursor: pointer;}

/* Reset any old circular bullet styles */
.pi-step::before,
.pi-step::after{ content: none; }

/* Shared icon anchor */
.pi-step::before{
  content:"";
  position:absolute;
  left:14px;
  top:50%;
  transform:translateY(-50%);
  line-height:1;
}

/* ✅ DONE: plain check, no circle */
.pi-step.is-done::before{
  content:"✓";
  color: #0284c7;          
  font-size:12px;
  font-weight:800;
  background-color: #c4edda;
  padding: 4px;
  border-radius: 600px;
}

/* 🔵 CURRENT: keep a simple dot (or swap to › if you prefer) */
.pi-step.is-current{ color:#0284c7; font-weight:700; background:#eef6ff; }
.pi-step.is-current::before{
  content:"•";             /* use "›" if you like an arrow */
  color: #0284c7;
  font-size:22px;
  transform:translateY(-55%);  /* tiny optical lift */
}

/* Future/locked (no icon) */
.pi-step.is-locked::before{ content:""; }

/* Disabled/non-clickable step */
.pi-step.is-disabled,
.pi-step[aria-disabled="true"] {
  opacity: 0.6;
  cursor: not-allowed !important;
  pointer-events: none;
}

.pi-step.is-disabled:hover,
.pi-step[aria-disabled="true"]:hover {
  background: transparent;
}
                    
/* Float when input has a value via JS */
.fi-input.has-value + .fi-float-label{
  top:0;
  transform: translateY(-4px);
  font-size:13px;
  color: #0284c7;
}

a.pi-step.is-done,
a.pi-step.is-done:link,
a.pi-step.is-done:visited {
  color:  #0284c7;
}
/* ----- Desktop keeps grid; mobile shows dropdown bar ----- */
                    
                    
@media (max-width: 960px){
  .pi-layout { display:block; }          /* stack content */
  .pi-side { display:none; }             /* hide desktop sidebar */
}


                
/* 
 * PERSONAL INFORMATION
 * form layout like BMO: underlined fields, two-col grid */
.fi-grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 26px 40px;
}
.fi-span2{ grid-column: 1/2; } /* Span first column only */
.fi-full-width{ grid-column: 1 / -1; } /* Span full width (all columns) */

.fi-group{ display:flex; flex-direction:column; }
.fi-label{
  color:#0b66c3; font-weight:700; margin-bottom:10px;
}
.fi-input{
  border:0; border-bottom:2px solid #475569;
  padding:8px 2px; font-size:16px; background:transparent; outline:none;
}
.fi-input:focus{
  border-bottom-color:#0b66c3;
  box-shadow: inset 0 -2px 0 #0b66c3;
}
.fi-hint{ color:#64748b; font-size:13px; margin-top:6px; }

.small{ font-size: clamp(18px, 2vw, 24px) !important; margin:25px 0 15px !important; }

/* mobile: stack inputs */
@media (max-width: 960px){
  .fi-grid{ grid-template-columns: 1fr; gap: 20px; }
}

/* Floating label pattern for underlined inputs */
.fi-group.fi-float { position: relative; overflow: visible; } /* Allow autocomplete dropdown to show */

.fi-input{
  border:0; border-bottom:2px solid #475569;
  background:transparent; outline:none;
  width:100%;
  padding:22px 2px 0;                  /* top padding leaves room for label */
  font-size:16px;
}
.fi-input:focus{
  border-bottom-color:#0b66c3;
  box-shadow: inset 0 -2px 0 #0b66c3;
}

/* the label that acts like the placeholder */
.fi-float-label{
  position:absolute; left:2px; top:20px;
  line-height:1;
  color:#0b66c3;                          /* same blue you’re using */
  pointer-events:none;
  transition: transform .15s ease, font-size .15s ease, top .15s ease, color .15s ease;
}

/* when focused OR when not empty → float it */
.fi-input:focus + .fi-float-label,
.fi-input:not(:placeholder-shown) + .fi-float-label{
  top:0;
  transform: translateY(-4px);
  font-size:13px;
  color:#0b66c3;
}

/* error/success hooks if you already use them */
/* Keep labels and borders blue even when there's an error - only show error text below */
.fi-group.error .fi-input{ 
  border-bottom-color:#0b66c3; /* Keep blue border */
  box-shadow: inset 0 -2px 0 #0b66c3; /* Keep blue shadow */
}
.fi-group.error .fi-float-label{ 
  color:#334155; /* Keep original label color, not red */
}

/* mobile grid */
@media (max-width:960px){
  .fi-grid{ grid-template-columns:1fr; gap:20px; }
}

/* ===== Shared Modal Form Field Styling - Matches Main Form ===== */
/* This ensures all modals use consistent form field styling */
.qs-modal .fi-float .fi-input,
.qs-modal .fi-group.fi-float .fi-input {
  border: 0;
  border-bottom: 2px solid #475569;
  padding: 22px 2px 0;
  background: transparent;
  border-radius: 0;
  font-size: 16px;
  width: 100%;
  outline: none;
  transition: all .18s ease;
}

.qs-modal .fi-float .fi-input:focus,
.qs-modal .fi-group.fi-float .fi-input:focus {
  border-bottom-color: #0b66c3;
  box-shadow: inset 0 -2px 0 #0b66c3;
  background: transparent;
}

.qs-modal .fi-float-label {
  position: absolute;
  left: 2px;
  top: 20px;
  line-height: 1;
  color: #0b66c3;
  font-weight: 600;
  font-size: 16px;
  pointer-events: none;
  transition: transform .15s ease, font-size .15s ease, top .15s ease, color .15s ease;
}

.qs-modal .fi-input:focus + .fi-float-label,
.qs-modal .fi-input:not(:placeholder-shown) + .fi-float-label {
  top: 0;
  transform: translateY(-4px);
  font-size: 13px;
  color: #0b66c3;
}

         
/* ===== Backdrop + dialog ===== */
.dob-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(15,23,42,.45);
  display: grid;
  place-items: center;
  z-index: 9999;
}
.dob-dialog {
  width: min(400px, 92vw);
  background: #fff;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  box-shadow: 0 10px 30px rgba(2,8,23,.25);
  overflow: hidden;
}

/* ===== Header (title perfectly centered) ===== */
.dob-head {
  display: grid;
  grid-template-columns: 40px 1fr 40px;   /* back | title | close */
  align-items: center;
  padding: 12px 14px;
  border-bottom: 1px solid #e6eef6;
}
.dob-head h3 {
  grid-column: 2;
  margin: 0;
  font-weight: 800;
  font-size: 16px;
  text-align: center;                 /* centers "Select a year" */
}
.dob-icon {
  width: 32px;
  height: 32px;
  background: #fff;
  display: grid;
  place-items: center;
  cursor: pointer;
  border: 0;
}
.dob-icon svg { width: 18px; height: 18px; stroke: #0b66c3; fill: none; }
.dob-head #dob-back  { grid-column: 1; justify-self: start; }
.dob-head #dob-close { grid-column: 3; justify-self: end; }

/* ===== Subhead (arrows tight to centered year) ===== */
.dob-subhead {
  display: grid;
  grid-template-columns: 1fr auto 1fr;   /* prev | year | next */
  align-items: center;
  column-gap: 10px;                      /* spacing between arrow and year */
  padding: 10px 14px;
  color: #64748b;
  text-align: center;
}
#dob-subhead-text {
  grid-column: 2;
  font-weight: 600;
}

/* Arrow buttons (grid items, no absolute positioning) */
.dob-link {
  background: transparent;
  width: 24px;
  height: 36px;
  display: grid;
  place-items: center;
  cursor: pointer;
  border: 0;
}
/* Arrows: left = start, right = end, with slight upward nudge */
#dob-prev{
  grid-column: 1;
  justify-self: end !important;            /* align to start (left) */
  align-self: end !important;
  transform: translateY(-2px) !important;    /* raise a little to align with year */
  padding-right: 30px !important;
}

#dob-next{
  grid-column: 3;
  justify-self: start !important;              /* align to end (right) */
  align-self: start  !important;
  transform: translateY(-2px);    /* same nudge */
}
         
.dob-link svg { width: 18px; height: 18px; }
.dob-link svg path { stroke: #0b66c3; }
/* .dob-link:hover { background: #f1f5f9; border-radius: 6px; }  */

/* ===== Years / Months / Days grids ===== */
.dob-grid {
  display: grid;
  gap: 10px;
  padding: 12px 14px;
  max-height: 260px;
  overflow: auto;
  scroll-behavior: smooth;
}
.dob-grid.years  { grid-template-columns: repeat(4, 1fr); }
.dob-grid.months { grid-template-columns: repeat(4, 1fr); }
.dob-grid.days   { grid-template-columns: repeat(7, 1fr); }

.dob-cell {
  padding: 8px 10px;
  border: 1px solid transparent;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
}
.dob-cell:hover { background: #f1f5f9; }
.dob-cell.active { border-color: #0b66c3; color: #0b66c3; background: #eaf4ff; }
.dob-cell.mute { color: #94a3b8; }

.dob-week {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 10px;
  padding: 10px 14px;
  color: #64748b;
  font-size: 12px;
}


@media (min-width:960px){
#dob-prev{

  margin-right: 20px !important;
}

}

/* ===== Remove old bottom nav if present ===== */
.dob-foot { display: none !important; }

/* ===== Input underline highlight ===== */
.fi-input.dob-highlight {
  border-bottom-color: #0b66c3;
  box-shadow: inset 0 -2px 0 #0b66c3;
}


                       
/* ensure each fi-group aligns from the top inside a two-column fi-grid */
.fi-grid {
  align-items: start; /* don't stretch or center vertically */
}

/* make the calendar icon stay centered within its input only */
.fi-group.fi-float {
  position: relative;
}

.fi-group.fi-float .dob-input {
  padding-right: 36px;
  vertical-align: middle;
}

.dob-calendar-btn {
  position: absolute;
  right: 6px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  padding: 0;
  margin: 0;
  color: #0b66c3;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 0;
}

.dob-calendar-btn svg {
  width: 28px;
  height: 28px;
  pointer-events: none;
}

.dob-calendar-btn:hover{ color:#0754a2; }
.dob-calendar-btn:focus-visible{ outline:2px solid rgba(11,100,194,.35); outline-offset:2px; }

@media (max-width:280px){
  .dob-calendar-btn{ width:24px; height:24px; right:8px; }
  .dob-input.calendarized{ padding-right:40px !important; }
}

.is-hidden { display: none !important; }
          
</style>

<style>
  .fi-suf{ position:relative; }
  .fi-suf .fi-input.with-suffix{ padding-right:28px; }
  .fi-suffix{
    position:absolute; right:10px; top:50%; transform:translateY(-50%);
    pointer-events:none; opacity:.8; font-weight:500;
  }
</style>

<style>
  .wi-hint{
    margin-top: 4px;
    font-size: 12px;
    line-height: 1.2;
    opacity: .75;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  .fi-group:not(.wi-show-hint) .wi-hint { display: none; }
  #period_y1, #period_y2, #period_y3 { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>




<style>


/* Tabs */
.upload-tabs{ display:flex; gap:8px; margin:10px 0 16px; }
.upload-tab{
  padding:10px 16px; border:1px solid #cfe0f5; border-radius:10px; background:#f7fbff;
  font-weight:700; cursor:pointer;
}
.upload-tab.active{ background:#0284c7; color:#fff; border-color: #0284c7; box-shadow:0 2px 8px rgba(11,100,194,.25); }
.upload-pane[hidden]{ display:none !important; }

/* Make the two tabs fill the row evenly and improve contrast */
.upload-tabs{
  display:grid;
  grid-template-columns: 1fr 1fr; /* equal width */
  gap:10px;
  width:100%;
}

.upload-tab{
  width:100%;
  text-align:center;
  padding:12px 18px;
  border:1px solid #cfe0f5;
  border-radius:12px;
  background:#f3f6fb;
  color:#0284c7;              /* clear, high-contrast text for inactive */
  font-weight:800;
  letter-spacing:.03em;
  text-transform:uppercase;
}

.upload-tab.active{
  background:#0284c7;
  color:#fff;                 /* white text only on active */
  border-color:#0284c7;
  box-shadow:0 2px 8px rgba(11,100,194,.25);
}

/* Optional: better focus/hover */
.upload-tab:focus-visible{ outline:3px solid #93c5fd; outline-offset:2px; }
.upload-tab:hover:not(.active){ background:#e9f1fb; }

</style>




<style>
/* =========================
   Mobile Steps — Bar & Drawer
   (<= 959px)
   ========================= */

/* Hide desktop sidebar on mobile */
@media (max-width: 959px){
  .pi-steps { display:none !important; }
}

/* ---- Mobile sticky bar right under header ---- */
@media (max-width: 959px){
  #pi-mobilebar{
    position: fixed;
    left: 0; right: 0;
    top: 0px;              /* match your header height */
    z-index: 2000;             /* above content, below system UI */
    background:#fff;
    border-bottom:1px solid #e5e7eb;
    padding:10px 12px;

    display:grid;
    grid-template-columns:36px 1fr 36px;  /* back | title | chevron */
    align-items:center;
    gap:8px;
  }

  /* Title: "4 of 5 – Review Information" */
  #pi-mobilebar .pi-mb-text{
    font-weight:600; font-size:15px; color:#1a1f36;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    text-align:center;
  }
  #pi-mb-stepcount{ font-weight:700; }
  .pi-mb-dash{ color:#94a3b8; }

  /* Icon buttons */
  #pi-mobilebar .pi-mb-back,
  #pi-mobilebar .pi-mb-toggle{
    appearance:none; border:0; background:#fff;
    width:36px; height:36px; border-radius:10px;
    box-shadow:0 0 0 1px #e5e7eb inset;
    display:grid; place-items:center; cursor:pointer;
  }
  #pi-mobilebar .pi-mb-back:hover,
  #pi-mobilebar .pi-mb-toggle:hover{ background:#f1f5f9; }

  /* Draw the arrows with borders */
  #pi-mobilebar .pi-mb-back::before,
  #pi-mobilebar .pi-mb-toggle::before{
    content:""; width:12px; height:12px; display:block;
    border-right:2px solid #0b66c3; border-bottom:2px solid #0b66c3;
  }
  #pi-mobilebar .pi-mb-back::before{ transform: rotate(135deg); }  /* left arrow */
  #pi-mobilebar .pi-mb-toggle::before{ transform: rotate(45deg); } /* chevron down */
  #pi-mobilebar .pi-mb-toggle[aria-expanded="true"]::before{
    transform: rotate(225deg);                                       /* chevron up */
  }

  /* Progress bar */
  #pi-mobilebar .pi-mb-progress{
    grid-column:1 / -1;
    height:3px; background:#e9edf3; border-radius:2px; overflow:hidden;
    margin-top:6px;
  }
  #pi-mb-progressbar{ height:100%; width:0%; background:#0ea5a4; }
}

/* ---- Drawer overlay & card ---- */
@media (max-width: 959px){
  /* Drawer hidden state */
  #pi-mb-drawer[hidden]{ display:none !important; }

  /* Full-screen backdrop */
  #pi-mb-drawer{
    position: fixed; inset: 0;
    z-index: 1999;                         /* just under the bar */
    background: rgba(17,24,39,.55);
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
  }

  /* White card */
  #pi-mb-drawer .pi-mb-card{
    box-sizing: border-box;
    max-width: 680px; margin: 10px auto;
    background:#fff; border-radius:14px;
    box-shadow:0 18px 48px rgba(2,8,23,.22);

    /* ⚠️ Important for dropdowns/popovers inside the drawer:
       let them overflow outside the card if needed. */
    overflow: visible;
    position: relative; /* new stacking context for high z children */
    z-index: 1;
  }

  /* Header row */
  #pi-mb-drawer .pi-mb-drawer-head{
    display:grid; grid-template-columns:1fr 36px; align-items:center;
    padding:14px 16px;
    background:#f8fafc;
    border-bottom:1px solid #eef1f6;
  }
  #pi-mb-drawer #pi-mb-drawer-title{
    font-weight:700; font-size:16px; color:#0f172a; letter-spacing:.01em;
    text-align:center;
  }

  /* Close btn */
  #pi-mb-close{
    width:36px; height:36px; border:0; border-radius:10px; background:#e0f2fe;
    cursor:pointer; display:grid; place-items:center;
  }
  #pi-mb-close::before{
    content:""; width:12px; height:12px; display:block;
    border-right:2px solid #0b66c3; border-bottom:2px solid #0b66c3;
    transform: rotate(45deg);
  }

  /* List wrapper */
  #pi-mb-nav{
    max-height: calc(100vh - 140px);
    overflow-y:auto;
    padding:6px 8px 10px;
  }

  /* Each step row */
  #pi-mb-nav .pi-mb-link{
    width:100%; display:flex; align-items:center; justify-content:space-between; gap:12px;
    background:none; border:0; cursor:pointer;
    padding:12px 12px; margin:2px 0;
    font-size:15px; line-height:1.25; color:#1f2937;
    border-radius:10px;
    transition: background .15s ease, color .15s ease;
    text-align:left;
  }
  #pi-mb-nav .pi-mb-link:hover,
  #pi-mb-nav .pi-mb-link:focus-visible{
    background:#f3f4f6; outline:none;
  }

  /* Current step */
  #pi-mb-nav .pi-mb-link.is-current{
    font-weight:700; color:#0f172a; background:#eff6ff; position:relative;
  }
  #pi-mb-nav .pi-mb-link.is-current::before{
    content:""; position:absolute; left:0; top:0; bottom:0;
    width:3px; background:#0b66c3; border-radius:3px 0 0 3px;
  }

  /* Done / future */
  #pi-mb-nav .pi-mb-link.is-done{ color:#0b66c3; }
  #pi-mb-nav .pi-mb-link.is-future{ color:#94a3b8; }

  /* Right chevron */
  #pi-mb-nav .pi-mb-link::after{
    content:""; flex:0 0 12px; height:12px;
    border-right:2px solid currentColor; border-bottom:2px solid currentColor;
    transform: rotate(-45deg); opacity:.6;
  }
  #pi-mb-nav .pi-mb-link.is-current::after{ opacity:.4; }
}

/* ---- Desktop: hide mobile UI ---- */
@media (min-width: 960px){
  #pi-mobilebar, #pi-mb-drawer{ display:none !important; }
}

/* =========================
   Dropdown/Popover visibility fixes
   ========================= */

/* If you have custom dropdowns (Select2/Choices/TomSelect/Flatpickr),
   make sure their containers can float above the drawer card. */
.select2-container,
.choices__list--dropdown,
.ts-dropdown,
.flatpickr-calendar,
.autocomplete-list,
.popover,
.menu,
[role="listbox"]{
  position: absolute;
  z-index: 3000;              /* above #pi-mobilebar (2000) and drawer (1999) */
}

/* In case a parent imposes clipping, neutralize common culprits */
#pi-mb-drawer,
#pi-mb-drawer *{
  transform: none !important;       /* avoid new stacking contexts */
  will-change: auto !important;
}

/* If a dropdown is inside the drawer card, ensure its nearest positioned
   ancestor doesn't clip it. (We already set .pi-mb-card { overflow:visible }) */
#pi-mb-drawer .pi-mb-card,
#pi-mb-drawer .pi-mb-card *{
  overflow: visible !important;
}

/* For native <select> that uses a styled wrapper with a pseudo menu */
.select-wrapper{ position: relative; z-index: 1; }
.select-wrapper .select-menu{ position:absolute; left:0; right:0; top:100%; z-index: 3000; }

/* If you’re using flatpickr/date pickers in the drawer: */
.flatpickr-calendar{ z-index: 3001 !important; }

/* Safety: prevent backdrop from blocking clicks to menus that escape the card */
#pi-mb-drawer .pi-mb-card [aria-expanded="true"]{
  position: relative; z-index: 2500;
}


/* =============== MOBILE NAV RESTYLE (≤959px) =============== */
@media (max-width:959px){

  /* Drawer card: simple, airy */
  #pi-mb-drawer .pi-mb-card{
    max-width: 520px;
    margin: 10px auto;
    border-radius: 14px;
    box-shadow: 0 16px 44px rgba(2,8,23,.18);
    background: #fff;
    overflow: visible;
  }

  /* Header */
  #pi-mb-drawer .pi-mb-drawer-head{
    background:#f8fafc;
    border-bottom:1px solid #eef1f6;
    padding:12px 14px;
    display:grid; grid-template-columns:1fr 36px; align-items:center;
  }
  #pi-mb-drawer #pi-mb-drawer-title{
    font-size:16px; font-weight:700; color:#0f172a; text-align:center;
    letter-spacing:.01em;
  }

  /* List container */
  #pi-mb-nav{
    padding: 8px 10px 12px;
    max-height: calc(100vh - 140px);
    overflow-y: auto;
  }

  /* Items → plain list rows (no chips) */
  #pi-mb-nav .pi-mb-link{
    /* layout */
    width:100%; display:flex; align-items:center; gap:10px;
    padding: 10px 12px;
    border: 0; background: transparent; text-align:left; cursor:pointer;

    /* typography */
    text-transform: none;           /* ❌ no uppercase */
    letter-spacing: 0;              /* ❌ no wide tracking */
    font-size: 15px; line-height: 1.35;
    color:#374151; font-weight: 500;

    /* visuals */
    border-radius: 8px;
    transition: background .15s ease, color .15s ease;

    /* prevent the text-caret/selection look */
    -webkit-user-select: none; user-select: none;
  }

  /* Bullet on the left (like the example) */
  #pi-mb-nav .pi-mb-link::before{
    content: "";
    flex: 0 0 8px; height: 8px; border-radius: 50%;
    background: #c7ced9;
    margin-right: 2px;
  }

  /* Hover/focus */
  #pi-mb-nav .pi-mb-link:hover,
  #pi-mb-nav .pi-mb-link:focus-visible{
    background: #f3f4f6;
    outline: none;
  }

  /* Current step → bold + darker + active bullet */
  #pi-mb-nav .pi-mb-link.is-current{
    font-weight: 700; color:#0f172a; background:#f1f5ff;
  }
  #pi-mb-nav .pi-mb-link.is-current::before{
    background: #0b66c3;
  }

  /* Done / future */
  #pi-mb-nav .pi-mb-link.is-done{
    color:#0b66c3;
  }
  #pi-mb-nav .pi-mb-link.is-future{
    color:#9aa3b2;
  }

  /* Right chevron (subtle) */
  #pi-mb-nav .pi-mb-link::after{
    content:"";
    margin-left:auto;
    width: 10px; height: 10px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: rotate(-45deg);
    opacity: .5;
  }
  #pi-mb-nav .pi-mb-link.is-current::after{ opacity:.35; }

  /* Remove any legacy chip/rail styles that might leak in */
  #pi-mb-nav .pi-mb-link,
  #pi-mb-nav .pi-mb-link.is-current{
    box-shadow:none !important;
    border-top:0 !important;
  }
}

/* Desktop hides mobile UI */
@media (min-width:960px){
  #pi-mobilebar, #pi-mb-drawer{ display:none !important; }


}


/* MOBILE (≤ 959px): place bar under header, drawer below the bar */
@media (max-width:959px){
  /* Header height var (fallback 90px). */
  #pi-mobilebar{ top: var(--mh-h, 90px); }

  /* Drawer starts BELOW the bar (header + bar heights). */
  #pi-mb-drawer{
    position: fixed;
    left: 0; right: 0; bottom: 0;
    top: var(--mb-top, 110px);        /* <- computed in JS below */
    z-index: 1999;
    background: rgba(17,24,39,.55);
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
  }

  /* Optional: tighten the card top margin since we already offset the drawer */
  #pi-mb-drawer .pi-mb-card{ margin-top: 8px; }
}

/* ===== MOBILE DRAWER CLEANUP (≤959px) ===== */
@media (max-width:959px){

  /* 1) Remove the "Navigation" header/section completely */
  #pi-mb-drawer .pi-mb-drawer-head{
    display:none !important;
  }
  /* Pull list upward a bit since the header is gone */
  #pi-mb-nav{
    padding: 8px 10px 12px !important;
    max-height: calc(100vh - 100px); /* more room since header is gone */
  }

  /* 2) Base item style (no bullets, no “text caret” look) */
  #pi-mb-nav .pi-mb-link{
    position:relative;
    display:flex; align-items:center; gap:10px;
    width:100%; padding:10px 12px; margin:2px 0;
    background:transparent; border:0; text-align:left; cursor:pointer;
    font-size:15px; line-height:1.35; color:#374151; font-weight:500;
    border-radius:8px; transition:background .15s ease, color .15s ease;
    -webkit-user-select:none; user-select:none;
  }
  #pi-mb-nav .pi-mb-link:hover,
  #pi-mb-nav .pi-mb-link:focus-visible{ background:#f3f4f6; outline:none; }


  /* ✔️ Completed (is-done): leading check icon + brand color */
  #pi-mb-nav .pi-mb-link.is-done{
    color:#0b66c3;
    padding-left:36px; /* space for check */
  }
  #pi-mb-nav .pi-mb-link.is-done::after{
    content:""; position:absolute; left:12px; top:50%; transform:translateY(-50%);
    width:16px; height:16px; border-radius:50%;
    /* circle outline */
    box-shadow: 0 0 0 2px currentColor inset;
  }
  /* the check tick */
  #pi-mb-nav .pi-mb-link.is-done::before{
    content:"-"; position:absolute; left:12px; top:50%; transform:translateY(-50%) rotate(45deg);
    width:6px; height:12px; border-right:3px solid currentColor; border-bottom:3px solid currentColor;
  }

  /* ▶ Current (is-current): bold + soft pill highlight, NO bullet/check */
  #pi-mb-nav .pi-mb-link.is-current{
    font-weight:700; color:#0f172a; background:#eff6ff;
  }

  /* Future (is-future): plain, no icon */
  #pi-mb-nav .pi-mb-link.is-future{ color:#9aa3b2; }

  /* subtle chevron on the far right (all rows) */
  #pi-mb-nav .pi-mb-link > i,
  #pi-mb-nav .pi-mb-link .chev { display:none; } /* kill legacy icons if any */
  #pi-mb-nav .pi-mb-link::marker { content: none; }
  #pi-mb-nav .pi-mb-link::selection { background: transparent; }

  #pi-mb-nav .pi-mb-link::after{
    /* keep a small chevron; tone it down */
    content:"";
    margin-left:auto; width:10px; height:10px;
    border-right:2px solid currentColor; border-bottom:2px solid currentColor;
    transform: rotate(-45deg);
    opacity:.4;
  }
  #pi-mb-nav .pi-mb-link.is-current::after{ opacity:.3; }
}

/* Desktop: unchanged (hide mobile UI) */
@media (min-width:960px){
  #pi-mobilebar, #pi-mb-drawer{ display:none !important; }
}

/* =========================
   MOBILE NAV — BAR & DRAWER (≤959px)
   ========================= */

/* Hide desktop sidebar on mobile */
@media (max-width:959px){
  .pi-steps{ display:none !important; }
}

/* --- Mobile sticky bar under header --- */
@media (max-width:959px){
  #pi-mobilebar{
    position: fixed;
    left: 0; right: 0;
    top: var(--mh-h, 90px);       /* header height (fallback 90px) */
    z-index: 2000;
    background:#fff;
    border-bottom:1px solid #e5e7eb;
    padding:10px 12px;

    display:grid;
    /* Make columns flexible so buttons stay visible on very small widths */
    grid-template-columns:auto minmax(0,1fr) auto; /* back | title | chevron */
    align-items:center;
    gap:8px;
    box-sizing:border-box;
  }
  #pi-mobilebar .pi-mb-text{
    font-weight:600; font-size:15px; color:#1a1f36;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis; text-align:center;
    min-width:0; /* allow text to shrink and ellipsize instead of pushing buttons off-screen */
  }
  #pi-mb-stepcount{ font-weight:700; }
  .pi-mb-dash{ color:#94a3b8; }

  #pi-mobilebar .pi-mb-back,
  #pi-mobilebar .pi-mb-toggle{
    appearance:none; border:0; background:#fff; width:36px; height:36px;
    border-radius:10px; box-shadow:0 0 0 1px #e5e7eb inset;
    display:flex; align-items:center; justify-content:center;
    position:relative;
    cursor:pointer;
  }
  #pi-mobilebar .pi-mb-back::before,
  #pi-mobilebar .pi-mb-toggle::before{
    content:""; width:12px; height:12px; display:block;
    border-right:2px solid #0b66c3; border-bottom:2px solid #0b66c3;
    position:absolute;
    top:50%; left:50%;
    transform: translate(-50%, -50%);
  }
  #pi-mobilebar .pi-mb-back::before{
    transform: translate(-50%, -50%) rotate(135deg);  /* left arrow */
  }
  #pi-mobilebar .pi-mb-toggle::before{
    transform: translate(-50%, -50%) rotate(45deg);   /* chevron down */
  }
  #pi-mobilebar .pi-mb-toggle[aria-expanded="true"]::before{
    transform: translate(-50%, -50%) rotate(225deg);  /* chevron up */
  }
  #pi-mobilebar .pi-mb-progress{
    grid-column:1/-1; height:3px; background:#e9edf3; border-radius:2px; overflow:hidden; margin-top:6px;
  }
  #pi-mb-progressbar{ height:100%; width:0%; background:#0ea5a4; }
}

/* Extra-narrow screens: allow title to wrap and reduce padding */
@media (max-width: 360px){
  #pi-mobilebar{
    padding:8px 8px;
  }
  #pi-mobilebar .pi-mb-text{
    white-space:normal;
  }
}

/* --- Drawer overlay anchored BELOW the bar --- */
@media (max-width:959px){
  /* Backdrop covers screen, begins right below the bar */
  #pi-mb-drawer{
    position: fixed;
    left: 0; right: 0; bottom: 0;
    top: var(--mb-top, 146px);     /* header+bar height (fallback ~146px) */
    z-index: 1999;
    background: rgba(17,24,39,.55);
    -webkit-backdrop-filter: blur(2px);
    backdrop-filter: blur(2px);
  }
  #pi-mb-drawer[hidden]{ display:none !important; }

  /* Drawer panel: 82vw (~80–85%), full height, NO radius */
  #pi-mb-drawer .pi-mb-card{
    position: fixed;               /* pin it */
    top: var(--mb-top, 146px);
    left: 0;
    width: min(82vw, 520px);
    height: calc(100vh - var(--mb-top, 146px));
    margin: 0;
    background:#fff;
    border-radius: 0;              /* 🚫 no radius */
    box-shadow: none;              /* cleaner, like the reference */
    overflow-y: auto;              /* full-height scroll inside */
    overflow-x: hidden;
  }

  /* Remove the "Navigation" section header */
  #pi-mb-drawer .pi-mb-drawer-head{ display:none !important; }

  /* List area spacing */
  #pi-mb-nav{
    padding: 10px 8px 16px;
    max-height: none;              /* full height handled by .pi-mb-card */
    overflow: visible;
  }

  /* Base item */
  #pi-mb-nav .pi-mb-link{
    position:relative;
    display:flex; align-items:center;
    gap:10px; width:100%;
    padding: 10px 12px 10px 36px;  /* left room for check/tick */
    margin: 2px 0;
    background: transparent;
    border: 0;
    text-align: left;
    cursor: pointer;

    font-size: 15px; line-height: 1.35;
    color:#374151; font-weight: 500;

    border-radius: 0;              /* 🚫 no radius on rows */
    transition: background .15s ease, color .15s ease;

    -webkit-user-select: none; user-select: none;
  }
  #pi-mb-nav .pi-mb-link:hover,
  #pi-mb-nav .pi-mb-link:focus-visible{
    background:#f3f4f6; outline:none;
  }

  /* Remove any legacy dots/bullets/rails */
  #pi-mb-nav .pi-mb-link::before,
  #pi-mb-nav .pi-mb-link::after{ box-shadow:none; }
  #pi-mb-nav .pi-mb-link::marker{ content:none; }

  /* ✔ Completed (is-done) — leading CHECK (visible brand color) */
  #pi-mb-nav .pi-mb-link.is-done{
    color:#0b66c3;
  }
  #pi-mb-nav .pi-mb-link.is-done::before{
    content:"";
    position:absolute; left:12px; top:50%; transform: translateY(-50%) rotate(-45deg);
    width: 10px; height: 6px;
    border-left: 3px solid currentColor;
    border-bottom:3px solid currentColor;
  }

  /* ► Current (is-current) — bold text, light bg (no icon) */
  #pi-mb-nav .pi-mb-link.is-current{
    font-weight: 700; color:#0f172a; background:#eef6ff;
  }
  /* Ensure no leftover icons when current */
  #pi-mb-nav .pi-mb-link.is-current::before{ content:none; }

  /* Future (is-future) — plain grey text, no icon */
  #pi-mb-nav .pi-mb-link.is-future{ color:#9aa3b2; }

  /* Small right chevron (all rows) */
  #pi-mb-nav .pi-mb-link > i,
  #pi-mb-nav .pi-mb-link .chev{ display:none; } /* kill legacy icons */
  #pi-mb-nav .pi-mb-link::after{
	display: none;
    content:"";
    margin-left:auto;
    width:10px; height:10px;
    border-right:2px solid currentColor; border-bottom:2px solid currentColor;
    transform: rotate(-45deg);
    opacity:.35;
  }
  #pi-mb-nav .pi-mb-link.is-current::after{ opacity:.3; }
}

/* Desktop: hide mobile UI */
@media (min-width:960px){
  #pi-mobilebar, #pi-mb-drawer{ display:none !important; }
}

/* === Minimal fixes (put this at the very end) === */

/* 1) Drawer should begin BELOW the mobile bar */
:root{ --barH: 70px; }           /* adjust if your bar is taller/shorter */

@media (max-width:959px){
  /* Override earlier 'inset:0' */
  #pi-mb-drawer{
    position: fixed !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    top: 90 !important;  /* <-- drawer now starts under the bar */
  }

  /* If your white card is positioned inside, align it too */
  #pi-mb-drawer .pi-mb-card{
    position: fixed !important;
    top: var(--barH) !important;
    left: 0 !important;
    height: calc(100vh - var(--barH)) !important;
    margin: 0 !important;
  }

/* === MOBILE NAV: match desktop step style (put LAST) === */
@media (max-width:959px){

  /* Base row */
  #pi-mb-nav .pi-mb-link{
    display:flex; align-items:center; gap:8px;
    width:100%;
    padding:10px 12px;
    background:transparent; border:0; text-align:left; cursor:pointer;
    font-size:15px; line-height:1.35; font-weight:500;
    color:#374151;
    border-radius:8px;
    -webkit-user-select:none; user-select:none;
  }
  #pi-mb-nav .pi-mb-link:hover,
  #pi-mb-nav .pi-mb-link:focus-visible{ background:#f3f4f6; outline:none; }

  /* Remove any prior bullets/chevrons */
  #pi-mb-nav .pi-mb-link::before,
  #pi-mb-nav .pi-mb-link::after{ content:none !important; }

  /* ✅ DONE — blue check on soft mint pill */
  #pi-mb-nav .pi-mb-link.is-done{
    color:#0284c7;
  }
  #pi-mb-nav .pi-mb-link.is-done::before{
    content:"✓";
    display:inline-grid; place-items:center;
    font-size:12px; font-weight:800; line-height:1;
    color:#0284c7;
    background:#c4edda;
    padding:4px; border-radius:600px;   /* tiny rounded pill */
    margin-right:2px;
  }

  /* 🔵 CURRENT — simple dot + soft blue row */
  #pi-mb-nav .pi-mb-link.is-current{
    font-weight:700;
    color:#0284c7;                      /* match desktop current text color */
    background:#eef6ff;                 /* same soft blue */
  }
  #pi-mb-nav .pi-mb-link.is-current::before{
    content:"•";
    font-size:22px; line-height:1;
    color:#0284c7;
    transform: translateY(-2px);        /* tiny optical lift like desktop */
    margin-right:4px;
  }

  /* Future — plain, no icon */
  #pi-mb-nav .pi-mb-link.is-future{
    color:#94a3b8;
  }
  #pi-mb-nav .pi-mb-link.is-future::before{ content:""; }
}

@media (max-width:959px){
  /* restore space for the icon */
  #pi-mb-nav .pi-mb-link{ padding-left:12px; }

  /* ✅ DONE — blue check on soft mint pill */
  #pi-mb-nav .pi-mb-link.is-done{
    color:#0284c7;
    padding-left:36px !important;              /* room for the check */
  }
  #pi-mb-nav .pi-mb-link.is-done::before{
    content:"✓" !important;                    /* override the nuke */
    position:absolute; left:12px; top:50%;
    transform:translateY(-50%);
    display:inline-grid; place-items:center;
    font-size:12px; font-weight:800; line-height:1;
    color:#0284c7;
    background:#c4edda;
    padding:4px; border-radius:600px;
  }
}

/* MOBILE drawer: perfect icon alignment + desktop-like bullet for current */
@media (max-width:959px){

  /* Make room for left icons and normalize line-height */
  #pi-mb-nav .pi-mb-link{
    position: relative;
    padding-left: 44px;            /* left gutter for icons */
    line-height: 1.2;              /* avoids baseline drift */
  }

  /* Kill any legacy bullets/chevrons that might collide */
  #pi-mb-nav .pi-mb-link::marker,
  #pi-mb-nav .pi-mb-link > i,
  #pi-mb-nav .pi-mb-link .chev{ display:none !important; }
  #pi-mb-nav .pi-mb-link::after{ content:none !important; }

  /* ✅ DONE — mint circle with blue check (perfectly centered) */
  #pi-mb-nav .pi-mb-link.is-done{
    color:#0284c7;
  }
  #pi-mb-nav .pi-mb-link.is-done::before{
    content:"";
    position:absolute; left:14px; top:50%; transform:translateY(-50%);
    width:20px; height:20px; border-radius:999px; background:#c4edda;
  }
  #pi-mb-nav .pi-mb-link.is-done::after{
    content:"";
    position:absolute; left:20px; top:50%;
    transform: translateY(-50%) rotate(45deg);
    width:6px; height:12px;
    border-right:3px solid #0284c7; border-bottom:3px solid #0284c7;
  }

  /* 🔵 CURRENT — solid blue bullet (matches desktop) */
  #pi-mb-nav .pi-mb-link.is-current{
    background:#eef6ff; color:#0284c7; font-weight:700;
  }
  #pi-mb-nav .pi-mb-link.is-current::before{
    content:"";
    position:absolute; left:20px; top:50%; transform:translateY(-50%);
    width:8px; height:8px; border-radius:999px; background:#0284c7;
  }

  /* Future — plain grey, no icon */
  #pi-mb-nav .pi-mb-link.is-future{ color:#94a3b8; }
}


/* MOBILE drawer — remove any base bullets/chevrons, then re-add per state */
@media (max-width:959px){

  /* 0) Kill all legacy pseudo icons */
  #pi-mb-nav .pi-mb-link::before,
  #pi-mb-nav .pi-mb-link::after{
    content:none !important;
    background:none !important;
    border:0 !important;
    box-shadow:none !important;
  }

  /* 1) Normalize row + left gutter for icons */
  #pi-mb-nav .pi-mb-link{
    position:relative;
    padding-left:44px;    /* room for state icon */
    line-height:1.2;
  }

  /* 2) ✅ DONE — mint circle + blue check */
  #pi-mb-nav .pi-mb-link.is-done{ color:#0284c7; }
  #pi-mb-nav .pi-mb-link.is-done::before{
    content:"";
    position:absolute; left:14px; top:50%; transform:translateY(-50%);
    width:20px; height:20px; border-radius:999px; background:#c4edda;
  }
  #pi-mb-nav .pi-mb-link.is-done::after{
    content:"";
    position:absolute; left:20px; top:50%; transform:translateY(-50%) rotate(45deg);
    width:6px; height:12px;
    border-right:3px solid #0284c7; border-bottom:3px solid #0284c7;
  }

  /* 3) 🔵 CURRENT — blue bullet only (no extra shapes) */
  #pi-mb-nav .pi-mb-link.is-current{
    background:#eef6ff; color:#0284c7; font-weight:700;
  }
  #pi-mb-nav .pi-mb-link.is-current::before{
    content:"";
    position:absolute; left:20px; top:50%; transform:translateY(-50%);
    width:8px; height:8px; border-radius:999px; background:#0284c7;
  }

  /* 4) Future — plain grey, no icon */
  #pi-mb-nav .pi-mb-link.is-future{ color:#94a3b8; }
}

/* === FINAL PATCH — perfect icons, single pseudo === */
@media (max-width:959px){

  /* Reserve left gutter for icons */
  #pi-mb-drawer #pi-mb-nav .pi-mb-link{
    position:relative !important;
    padding-left:44px !important;
    line-height:1.25 !important;
  }

  /* Kill any generic bullets/chevrons */
  #pi-mb-drawer #pi-mb-nav .pi-mb-link::before,
  #pi-mb-drawer #pi-mb-nav .pi-mb-link::after{
    content:none !important;
  }

  /* ✅ DONE — mint circle WITH blue check (single ::before) */
  #pi-mb-drawer #pi-mb-nav .pi-mb-link.is-done{
    color:#0284c7 !important;
  }
  #pi-mb-drawer #pi-mb-nav .pi-mb-link.is-done::before{
    content:"✓" !important;                   /* draw check as text */
    position:absolute !important;
    left:14px !important; top:50% !important;
    transform:translateY(-50%) !important;
    width:20px !important; height:20px !important;
    display:grid !important; place-items:center !important;
    border-radius:999px !important;
    background:#c4edda !important;            /* mint circle */
    color:#0284c7 !important;                 /* blue check */
    font-size:12px !important; font-weight:800 !important; line-height:1 !important;
  }

  /* 🔵 CURRENT — blue bullet + soft row */
  #pi-mb-drawer #pi-mb-nav .pi-mb-link.is-current{
    background:#eef6ff !important;
    color:#0284c7 !important;
    font-weight:700 !important;
  }
  #pi-mb-drawer #pi-mb-nav .pi-mb-link.is-current::before{
    content:"" !important;
    position:absolute !important;
    left:20px !important; top:50% !important;
    transform:translateY(-50%) !important;
    width:8px !important; height:8px !important;
    border-radius:999px !important;
    background:#0284c7 !important;            /* blue bullet */
  }

  /* Future — grey, no icon */
  #pi-mb-drawer #pi-mb-nav .pi-mb-link.is-future{
    color:#94a3b8 !important;
  }
}

</style>


<style id="pi-mobilebar-offset-patch">
  :root{ --pi-header-offset: 0px; }  /* will be set by JS below */

  @media (max-width: 959px){
    /* Make sure these override earlier rules */
    #pi-mobilebar{ top: var(--pi-header-offset) !important; }
    .pi-mb-drawer{ top: var(--pi-header-offset) !important; }

    /* Push page content down so the mobile bar doesn't overlap it */
    .pi-stage{
      margin-top: calc(var(--pi-header-offset) + 1px); /* ~bar height */
    }
  }



</style>



<style>



/* ===== Global modal styling (child, property, rent confirm, etc.) ===== */
.qs-modal__backdrop{
  position:fixed; inset:0; background:rgba(0,0,0,.35); z-index:9998;
}
.qs-modal__dialog{
  position:fixed; left:50%; top:50%; transform:translate(-50%,-50%);
  width:min(760px,92vw);
  max-height:88vh;
  background:#fff; border:1px solid #e5e7eb; border-radius:16px;
  box-shadow:0 12px 40px rgba(0,0,0,.18);
  display:grid; grid-template-rows:auto 1fr auto;
  z-index:9999;
  overflow:hidden;
}
.qs-modal__head,
.qs-modal__header{
  padding:20px 24px;
  border-bottom:1px solid #e5e7eb;
}
.qs-modal__body{
  padding:20px 24px;
  overflow:auto;
  background:#fff;
}
.qs-modal__foot,
.qs-modal__actions{
  padding:16px 24px;
  border-top:1px solid #e5e7eb;
  display:flex; gap:12px; justify-content:flex-end;
  background:#f8fafc;
}
.qs-modal__foot button,
.qs-modal__actions button{ min-width:120px; }

/* Mobile: ensure modals (e.g., \"Add Rental\") sit above main header */
@media (max-width: 959px){
  .qs-modal__backdrop{ z-index:99911000; }
  .qs-modal__dialog{ z-index:99911001; }
}

/* tiny adorners shouldn’t capture clicks */
#prop-modal .fi-suffix{ pointer-events:none; }
#prop-modal .tax-btn, #prop-modal .tax-btn-secondary{ pointer-events:auto; }

/* ---------- Align “Residing in Canada?” with DOB on desktop ---------- */
@media (min-width: 720px){
  #prop-step1 .fi-grid{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 24px;
  }
  /* 1 owner | 2 address | 3 start | 4 end | 5 partner | 6 owner% | 7 own use% | 8 gross */
  #prop-step1 .fi-grid > .fi-group:nth-of-type(3){ grid-column: 1; } /* Start date (left) */
  #prop-step1 .fi-grid > .fi-group:nth-of-type(4){ grid-column: 2; } /* End date (right) */

  /* Put the Residing-in-Canada block into the right column and bottom-align it with DOB row */
  #prop-step1 .fi-grid > .fi-group.residing-block{
    grid-column: 2;
    align-self: end;            /* lines up with the DOB input baseline */
  }
}

/* Optional hook: if you wrap the Residing section like this:
   <div class="fi-group residing-block"> ... yes/no ... </div>
   the rule above will kick in. If you can't change HTML, you can keep your current layout;
   the mobile footer styling below is independent of this alignment hook. */


/* ---------- Close “X” button ---------- */
.qs-modal__head{ position: relative; }
.qs-modal__close,
.qs-modal__x{
  position:absolute; top:10px; right:12px;
  display:inline-flex; align-items:center; justify-content:center;
  width:36px; height:36px;
  border-radius:999px; border:1px solid #dbe3ef;
  background:#fff; cursor:pointer;
  transition: box-shadow .15s ease, transform .06s ease;
}
.qs-modal__close:hover,
.qs-modal__x:hover{ box-shadow:0 4px 14px rgba(2,8,20,.08); }
.qs-modal__close:active,
.qs-modal__x:active{ transform: scale(.98); }

/* ---------- Mobile footer buttons: pill style ---------- */
@media (max-width: 640px){
  .qs-modal__foot,
  .qs-modal__actions{
    justify-content: center !important;
    gap: 14px !important;
    padding: 12px 16px !important;
    box-shadow: 0 -6px 20px rgba(2,8,20,.06);
  }

  .qs-modal__foot .tax-btn,
  .qs-modal__foot .tax-btn-secondary,
  .qs-modal__actions .tax-btn,
  .qs-modal__actions .tax-btn-secondary{
    border-radius: 999px !important;
    padding: 12px 22px !important;
    min-width: 130px;
    font-weight: 700;
  }

  .qs-modal__foot .tax-btn-secondary,
  .qs-modal__actions .tax-btn-secondary{
    background: #fff !important;
    color: #0284c7 !important;
    border: 2px solid #0284c7 !important;
    box-shadow: none !important;
  }
}

/* Keep your calendar icon touchable on mobile (you already have similar rules; this ensures consistency) */
@media (max-width: 640px){
  #prop-modal .dob-calendar-btn{ width:28px; height:28px; }
  #prop-modal .fi-float .fi-input{ min-height:48px; }
}


#prop-modal .qs-modal__head{ position:relative; }
#prop-modal .qs-modal__close{
  position:absolute; right:14px; top:10px;
  appearance:none; background:transparent !important; border:none !important;
  padding:0; margin:0; box-shadow:none !important;
  color:#0b66c3; font-size:28px; line-height:1; cursor:pointer; z-index:5;
}
#prop-modal .qs-modal__close:hover{ color:#0754a2; transform:translateY(-1px); }

@media (max-width:640px){
  #prop-modal .qs-modal__close{ font-size:26px; right:10px; top:8px; }
}

@media (max-width:640px){
  #prop-modal .qs-modal__foot{
    display:flex !important;
    gap:12px !important;
  }
  #prop-foot-1 > .tax-btn,
  #prop-foot-1 > .tax-btn-secondary,
  #prop-foot-2 > .tax-btn,
  #prop-foot-2 > .tax-btn-secondary{
    flex:1 1 0 !important;
    min-width:0 !important;
  }
}

/* leave room for the icon */
#prop-modal .dob-input.calendarized{ padding-right:48px !important; }

/* base size */
#prop-modal .dob-calendar-btn{
  position:absolute; right:12px; top:50%; transform:translateY(-50%);
  width:28px; height:28px; background:transparent !important; border:none !important;
  display:inline-flex; align-items:center; justify-content:center; color:#0b66c3;
}
#prop-modal .dob-calendar-btn svg{ width:100%; height:100%; display:block; }

/* larger on phones */
@media (max-width:640px){
  #prop-modal .dob-calendar-btn{ width:32px; height:32px; right:10px; }
}

</style>

<style>
/* ========== LAYOUT: 50% desktop, 100% mobile ========== */
.xsel-wrap{ position:relative; width:50%; max-width:640px; }
@media (max-width:720px){ .xsel-wrap{ width:100%; }}

/* Hide native select but keep it in the form */
.xsel-native{ position:absolute; width:1px; height:1px; opacity:0; pointer-events:none; }

/* Button that shows current value */
.xsel-btn{
  width:100%; padding:10px 12px; border:1px solid #cbd5e1; border-radius:10px;
  background:#fff; font-size:16px; text-align:left; display:flex; align-items:center; justify-content:space-between;
}
.xsel-btn:hover{ border-color:#0284c7; box-shadow:0 0 0 3px rgba(11,100,194,.15); cursor:pointer; }
.xsel-btn:focus{ outline:3px solid #94d2ff; outline-offset:2px; }
.xsel-value{ flex:1; min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#0f172a; }

/* Caret */
.xsel-caret{ margin-left:8px; width:0; height:0; border-left:6px solid transparent; border-right:6px solid transparent; border-top:6px solid #475569; }

/* Options panel (stays within parent width) */
.xsel-list{
  position:absolute; left:0; right:0; top:calc(100% + 6px); z-index:40;
  background:#fff; border:1px solid #cbd5e1; border-radius:10px;
  box-shadow:0 12px 24px rgba(2,8,23,.12);
  max-height:280px; overflow:auto; padding:6px 0; display:none;
}
.xsel-open .xsel-list{ display:block; }

.xsel-item{
  padding:10px 12px; line-height:1.25; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}

/* BLUE hover/active/selected */
.xsel-item:hover,
.xsel-item[aria-selected="true"],
.xsel-item.xsel-active{
  background:#0284c7; color:#fff;
  cursor:pointer;
}

/* stop auto-uppercasing */
.xsel-btn,
.xsel-btn .xsel-value,
.xsel-item {
  text-transform: none !important;
  letter-spacing: normal;
  font-variant-caps: normal;
}

/* ---------- Dropdown style refinement ---------- */




/* disabled option (already chosen in the other dropdown) */
select.fi-input option:disabled {
  color: #6b7280 !important;     /* gray-500 text */
  background-color: #f9fafb !important; /* subtle gray bg */
}

/* hovered or highlighted option inside open dropdown (browser-dependent) */
select.fi-input option:hover,
select.fi-input option:checked {
  background-color: #0284c7 !important; /* your blue */
  color: #fff !important;               /* white text */
}


</style>


<style>



/* list */
.tax-list { list-style:none; padding:0; margin:0 0 14px; display:grid; gap:18px; }
.tax-item { display:flex; gap:14px; align-items:flex-start; }
.tax-ico  { flex:0 0 28px; display:inline-flex; align-items:center; justify-content:center; }
.tax-item h3 { margin:0 0 4px; font-size:18px; color:#0f172a; }
.tax-muted { margin:0; color:#64748b; }

/* docs box (BMO-like) */
/* --- docs box (BMO-like) --- */
.doc-box{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:16px 16px 8px;box-shadow:0 1px 2px rgba(0,0,0,.05)}
.doc-title{margin:0 0 4px;font-size:20px;font-weight:800;color:#0f172a}
.doc-sub{margin:0 0 10px;color:#334155}

/* link toggle */
.doc-link{appearance:none;background:none;border:0;padding:0;margin:2px 0 0;display:inline-flex;gap:8px;align-items:center;
  font-weight:600;color:#0284c7;cursor:pointer}
.doc-link:hover,.doc-link:focus{text-decoration:underline}
.doc-chev{transition:transform .18s ease}
.doc-link[aria-expanded="true"] .doc-chev{transform:rotate(180deg)}

/* panel */
.doc-panel{margin-top:14px;border-top:1px solid #e5e7eb;padding-top:16px}
.doc-filelist{list-style:none;margin:0 0 8px;padding:0;display:grid;grid-template-columns:1fr 1fr;gap:10px 28px}
.doc-filelist li{display:flex;gap:10px;align-items:flex-start;color:#0f172a}
.file-ico{width:20px;height:22px;flex:0 0 20px;display:inline-flex}
.badge{display:inline-block;padding:2px 6px;border-radius:999px;font-size:11px;line-height:1;border:1px solid #e2e8f0;background:#f1f5f9;color:#334155;font-weight:700;margin-left:6px}
.badge-required{border-color:#fecaca;background:#fee2e2;color:#b91c1c}

.doc-notes{margin:8px 0 0;padding-left:18px;color:#334155}
.doc-close{display:flex;justify-content:flex-end;margin-top:12px}
.doc-closebtn{appearance:none;background:none;border:0;padding:6px 0;display:inline-flex;gap:8px;align-items:center;
  font-weight:700;color:#0284c7;cursor:pointer;text-transform:lowercase;letter-spacing:.02em}
.doc-closebtn:hover,.doc-closebtn:focus{text-decoration:underline}

@media (max-width:680px){ .doc-filelist{grid-template-columns:1fr} }

/* (optional) basic styles you already have */
.tax-item{display:flex;gap:14px;align-items:flex-start;margin-bottom:12px}
.tax-ico{flex:0 0 28px;display:inline-flex;align-items:center;justify-content:center}

</style>

<style>
/* Hide only the marital-status radio grid after the dropdown */
.xsel-wrap + .qs-choicegrid { display:none !important; }
</style>


<style>

/* Add Child modal — align with clean fi inputs */
#child-modal .qs-modal__dialog{
  position:fixed; left:50%; top:50%;
  transform:translate(-50%,-50%);
  width:min(760px,92vw);
  max-height:88vh;
  overflow:auto;
  background:#fff;
  border:1px solid #e5e7eb;
  border-radius:16px;
  box-shadow:0 12px 40px rgba(0,0,0,.18);
}
#child-modal .qs-modal__header{
  padding:20px 24px;
  border-bottom:1px solid #e5e7eb;
}
#child-modal .child-title.qs-title{
  font-size:24px;
  line-height:1.25;
  font-weight:700;
  margin:0;
}
#child-modal .qs-modal__body{
  padding:20px 24px;
}
#child-modal .qs-modal__actions{
  padding:16px 24px;
  border-top:1px solid #e5e7eb;
  display:flex;
  gap:12px;
  justify-content:flex-end;
  background:#f8fafc;
}
#child-modal .fi-group{ margin-bottom:16px; }
#child-modal .child-residing-label{
  font-size:16px;
  font-weight:700;
  color:#0f172a;
  margin:0 0 8px;
}

/* Optional: bump title a bit on wide screens */
@media (min-width: 768px){
  #child-modal .child-title.qs-title { font-size: 26px; }
}

/* Property modal title - match child modal styling */
#prop-modal .prop-title.qs-title{
  font-size:24px;
  line-height:1.25;
  font-weight:700;
  margin:0;
  text-transform:none; /* Override lowercase from .prop-title */
}

@media (min-width: 768px){
  #prop-modal .prop-title.qs-title { font-size: 26px; }
}

          
.children-table th,
.children-table td { padding:8px 10px; font-size:15px; }

/* Mobile: card-style rows */
@media (max-width: 640px){




  .children-table thead { display:none; }

  .children-table,
  .children-table tbody,
  .children-table tr,
  .children-table td { display:block; width:100%; }

  .children-table tr{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:10px 12px;
    margin:10px 0;
    box-shadow:0 1px 4px rgba(0,0,0,.04);
  }

  .children-table td{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:6px 0;
    font-size:14px;
  }

  /* Labels per cell (no markup changes needed) */
  .children-table td:nth-child(1)::before{content:"Child First Name"; font-weight:600; color:#334155; margin-right:12px; font-size:12px;}
  .children-table td:nth-child(2)::before{content:"Child Last Name";  font-weight:600; color:#334155; margin-right:12px; font-size:12px;}
  .children-table td:nth-child(3)::before{content:"Child Date of Birth"; font-weight:600; color:#334155; margin-right:12px; font-size:12px;}
  .children-table td:nth-child(4)::before{content:"Residing in Canada?"; font-weight:600; color:#334155; margin-right:12px; font-size:12px;}
  .children-table td:nth-child(5)::before{content:"Actions"; font-weight:600; color:#ffffff; margin-right:12px; font-size:12px;}

  /* Actions row buttons smaller */
  .children-table td:last-child{ justify-content:flex-end; gap:8px; }
  .children-table td:last-child .tax-btn,
  .children-table td:last-child .tax-btn-secondary{
    padding:6px 10px;
    font-size:12px;
    border-radius:999px;
  }
                                        

} 
                                          
                                          
.children-table td.actions-cell{
  padding: 8px;
  text-align: center;                 /* center inline content */
}

.children-table td.actions-cell .action-link{
  color: #068ac1;
  font-weight: 700;
  text-decoration: none;
  margin: 0 8px;                      /* space around each link */
}
.children-table td.actions-cell .action-link:hover{
  text-decoration: underline;
}
.children-table td.actions-cell .action-link.delete{
  color: #e11d48;                     /* optional: reddish delete */
}
.children-table td.actions-cell .action-sep{
  color: #94a3b8;                     /* separator dot color */
}

/* Show action icons for ALL breakpoints (not just desktop) */
.children-table .action-link[data-edit]::after{
  content:"";
  width:16px; height:16px; display:inline-block;
  background-size:contain; background-repeat:no-repeat;
  margin-left:6px;
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23068ac1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <path d='M12 20h9'/><path d='M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z'/></svg>");
}

.children-table .action-link.delete::after{
  content:"";
  width:16px; height:16px; display:inline-block;
  background-size:contain; background-repeat:no-repeat;
  margin-left:6px;
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23068ac1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <polyline points='3 6 5 6 21 6'/><path d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/>\
  <path d='M10 11v6'/><path d='M14 11v6'/><path d='M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2'/></svg>");
}

/* Mobile tune-up: keep spacing and size tidy */
@media (max-width:640px){
  .children-table .action-link{ gap:6px; }
  .children-table td.actions-cell{ display:flex; justify-content:flex-end; gap:12px; }
  .children-table .action-link[data-edit]::after,
  .children-table .action-link.delete::after{ width:18px; height:18px; }
}

/* Align Edit/Delete with their icons on mobile */
@media (max-width:640px){
  .children-table td.actions-cell{
    display:flex !important;
    justify-content:flex-end !important;
    align-items:center !important;
    gap:14px !important;
  }
  .children-table .action-link{
    display:inline-flex !important;
    align-items:center !important;
    line-height:1.1 !important;
    gap:6px !important;                /* word ↔ icon spacing */
  }
  .children-table .action-link[data-edit]::after,
  .children-table .action-link.delete::after{
    width:18px; height:18px;           /* same size */
    display:block;                     /* remove baseline wiggle */
  }
}                                          
</style>


<style>
  /* Vertical splitters only on the header of the children table */
  .children-table {
    border-collapse: separate; /* keep cell borders independent */
  }
  .children-table thead th {
    position: relative;
    padding: 8px 12px;
  }
  .children-table thead th:not(:last-child)::after {
    content: "";
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 1.6em;          /* splitter length inside the header cell */
    width: 1px;
    background: #17191d;    /* light gray; adjust if needed */
  }

  /* Optional: slightly shorter splitters on small screens */
  @media (max-width: 640px) {
    .children-table thead th:not(:last-child)::after { height: 1.3em; }
  }
                   
 #child-modal .child-residing-label { 
  font-size: 20px !important;           /* make this smaller than the title */
  line-height: 1.4;
  font-weight: 600;
}

/* ===== Children table — desktop look like Rent (no arrows) ===== */
@media (min-width:641px){
  .children-table{
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    background:#fff;
  }

  /* Header */
  .children-table thead th{
    background:#f9fafb;
    color:#0f172a;
    font-weight:700;
    padding:12px 14px;
    border-bottom:2px solid #121826; /* dark underline */
    position:relative;
  }
  /* slim vertical splitters between header cells */
  .children-table thead th + th::before{
    content:"";
    position:absolute; left:0; top:50%;
    transform:translateY(-50%);
    width:1px; height:1.7em; background:#121826;
    opacity:.9;
  }

  /* Rows */
  .children-table tbody tr{ background:#fff; }
  .children-table tbody tr:nth-child(even){ background:#fcfdff; }
  .children-table tbody td{
    padding:12px 14px;
    border-bottom:1px solid #f3f4f6;
    vertical-align:middle;
  }

  /* Actions on the far right */
  .children-table td.actions-cell{
    text-align:right;             /* ← end align */
    white-space:nowrap;
  }

  /* Links: blue, word first, icon after (like Rent) */
  .children-table .action-link{
    color:#068ac1;
    font-weight:700;
    text-decoration:none;
    display:inline-flex; align-items:center;
    gap:6px;                       /* space before icon */
    margin:0 4px;
  }
  .children-table .action-link:hover{ text-decoration:underline; }
  .children-table .action-sep{ color:#94a3b8; margin:0 4px; }

  /* Pencil icon after "Edit" */
  .children-table .action-link[data-edit]::after{
    content:"";
    width:16px; height:16px; display:inline-block;
    background-size:contain; background-repeat:no-repeat;
    background-image:url("data:image/svg+xml;utf8,\
    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23068ac1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
    <path d='M12 20h9'/><path d='M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z'/></svg>");
  }

  /* Trash icon after "Delete" (same blue to match Rent) */
  .children-table .action-link.delete::after{
    content:"";
    width:16px; height:16px; display:inline-block;
    background-size:contain; background-repeat:no-repeat;
    background-image:url("data:image/svg+xml;utf8,\
    <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23068ac1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
    <polyline points='3 6 5 6 21 6'/><path d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/>\
    <path d='M10 11v6'/><path d='M14 11v6'/><path d='M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2'/></svg>");
  }

  /* Compact the table a bit on wide screens */
  .children-table th, .children-table td{ font-size:15px; }
}

/* ===== Add Child pill: no overlap, icon after text (top & bottom) ===== */
#btn-add-child{
  display:inline-flex !important;
  align-items:center; gap:.5rem;
  white-space:nowrap;
  padding:8px 14px;
 border: none;}

#btn-add-child:hover{
background-color: #fff;
color: #0284c7;
                          }                           
#btn-add-child::after{
  content:""; width:20px; height:20px; display:inline-block;
  background-repeat:no-repeat; background-size:contain;
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='%230284c7'>\
  <circle cx='12' cy='12' r='10'/><path d='M12 8v8M8 12h8' stroke='%23fff' stroke-width='2' stroke-linecap='round'/></svg>");
}

@media (max-width: 640px){
  .children-table tr#children-empty-row td::before,
  .children-table tr.children-empty-row td::before{
    content: none !important;
    display: none !important;
    text-align: center;
  }
}

/* Desktop: center the empty-state cell */
.children-table #children-empty-row td{
  text-align:center;
  color:#64748b;           /* optional softer tone */
}

/* Mobile cards: center the empty-state row */
@media (max-width:640px){
  .children-table #children-empty-row{ display:block; }
  .children-table #children-empty-row td{
    display:flex !important;
    justify-content:center !important;
    align-items:center !important;
    padding:12px !important;
    text-align:center !important;
  }
  /* keep the pseudo-label hidden for the empty row */
  .children-table #children-empty-row td::before{ content:none !important; display:none !important; }
}

/* Hide the separator dot */
.children-table .action-sep{ display:none !important; }

/* Make Delete link same color as Edit */
.children-table .action-link.delete{ color:#068ac1 !important; }

/* Kill any header splitters that were added with ::before/::after */
.children-table thead th::before,
.children-table thead th::after{
  content:none !important;
  display:none !important;
}
</style>

<style>
/* ===== Modal frame & title ===== */
#child-modal .qs-modal__dialog{
  border-radius:16px !important;
  box-shadow:0 20px 50px rgba(2, 8, 20, 0.25) !important;
  background:#fff;
}

#child-modal .child-title.qs-title{
  font-size:26px;
  line-height:1.25;
  font-weight:800;
  margin:0;
  text-align: center;
}

/* ===== Layout & spacing ===== */
#child-modal .child-body{
  background:#fff;
  /* Remove container styling - content should not be in a container */
}
/* Remove box-shadow from all forms */
form {
  box-shadow: none !important;
}

#child-modal #child-form{
  box-shadow: none !important; /* Remove form box-shadow */
}

/* Apply same styling to other modals */
#prop-modal #prop-form,
#rent-confirm form{
  box-shadow: none !important; /* Remove form box-shadow */
}

#prop-modal .fi-float .fi-input,
#rent-confirm .fi-float .fi-input{
  border: 0;
  border-bottom: 2px solid #475569; /* Match main form border color */
  padding: 22px 2px 0; /* Match main form padding (top padding for label) */
  background: transparent !important; /* Remove background to match main form */
  border-radius: 0 !important; /* Remove border-radius to match main form */
  font-size: 16px; /* Match main form font size */
  width: 100%;
  outline: none;
  transition: all .18s ease;
}

/* Ensure prop-modal and other modals have relative positioning for labels */
#prop-modal .fi-group.fi-float,
.qs-modal .fi-group.fi-float {
  position: relative;
  overflow: visible; /* Allow autocomplete dropdown to show */
}

/* Match main form label styling for prop-modal */
#prop-modal .fi-float-label{
  position: absolute;
  left: 2px;
  top: 20px;
  line-height: 1;
  color: #0b66c3; /* Match main form label color (blue) */
  font-weight: 600;
  font-size: 16px; /* Match main form label font size */
  pointer-events: none;
  transition: transform .15s ease, font-size .15s ease, top .15s ease, color .15s ease;
}

/* Float label when focused or has value */
#prop-modal .fi-input:focus + .fi-float-label,
#prop-modal .fi-input:not(:placeholder-shown) + .fi-float-label{
  top: 0;
  transform: translateY(-4px);
  font-size: 13px; /* Match main form floated label size */
  color: #0b66c3;
}

/* Match main form label styling for rent-confirm */
#rent-confirm .fi-float-label{
  position: absolute;
  left: 2px;
  top: 20px;
  line-height: 1;
  color: #0b66c3; /* Match main form label color (blue) */
  font-weight: 600;
  font-size: 16px; /* Match main form label font size */
  pointer-events: none;
  transition: transform .15s ease, font-size .15s ease, top .15s ease, color .15s ease;
}

/* Float label when focused or has value for rent-confirm */
#rent-confirm .fi-input:focus + .fi-float-label,
#rent-confirm .fi-input:not(:placeholder-shown) + .fi-float-label{
  top: 0;
  transform: translateY(-4px);
  font-size: 13px; /* Match main form floated label size */
  color: #0b66c3;
}

/* Match main form focus styling - keep blue border and shadow */
#prop-modal .fi-float .fi-input:focus,
#rent-confirm .fi-float .fi-input:focus,
#child-modal .fi-float .fi-input:focus{
  border-bottom-color: #0b66c3; /* Blue border on focus (match main form) */
  box-shadow: inset 0 -2px 0 #0b66c3; /* Blue shadow on focus (match main form) */
}

#child-modal .fi-grid{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px 24px;
}

/* Make "Residing in Canada?" span both columns (own line) */
#child-modal .fi-group.fi-span2{
  grid-column: 1 / -1; /* Span from first to last column */
}

@media (max-width: 720px){
  #child-modal .fi-grid{ grid-template-columns: 1fr; }
  #child-modal .fi-group.fi-span2{
    grid-column: 1; /* On mobile, single column anyway */
  }
}

/* Better field rhythm */
#child-modal .fi-group{ position:relative; }

/* Float labels - match main form styling */
#child-modal .fi-float .fi-input{
  border: 0;
  border-bottom: 2px solid #475569; /* Match main form */
  padding: 22px 2px 0; /* Match main form */
  background: transparent; /* Match main form */
  border-radius: 0;
  font-size: 16px;
  width: 100%;
  outline: none;
  transition: all .18s ease;
}

#child-modal .fi-float .fi-input:focus{
  border-bottom-color: #0b66c3; /* Match main form */
  box-shadow: inset 0 -2px 0 #0b66c3; /* Match main form */
  background: transparent; /* Match main form */
}

#child-modal .fi-float-label{
  position: absolute;
  left: 2px;
  top: 20px;
  line-height: 1;
  color: #0b66c3; /* Match main form - blue */
  font-weight: 600; /* Match main form */
  font-size: 16px; /* Match main form */
  pointer-events: none;
  transition: transform .15s ease, font-size .15s ease, top .15s ease, color .15s ease;
}

/* Float label when focused or has value */
#child-modal .fi-input:focus + .fi-float-label,
#child-modal .fi-input:not(:placeholder-shown) + .fi-float-label{
  top: 0;
  transform: translateY(-4px);
  font-size: 13px; /* Match main form */
  color: #0b66c3; /* Match main form */
}

/* ===== Calendar icon inside field ===== */
#child-modal .fi-group{ position:relative; }
#child-modal .dob-input.calendarized{ padding-right: 44px !important; }

#child-modal .dob-calendar-btn{
  position:absolute; right:12px; top:50%; transform:translateY(-50%);
  width:28px; height:28px; color:#0284c7;
  background:transparent; border:0; padding:0; margin:0;
  display:inline-flex; align-items:center; justify-content:center; cursor:pointer;
}
#child-modal .dob-calendar-btn:hover{ color:#0754a2; }
#child-modal .dob-calendar-btn svg{ width:100%; height:100%; display:block; }

/* ===== Yes/No pills ===== */
#child-modal .yn-group--pills{
  display:inline-flex; gap:10px; flex-wrap:wrap;
}

#child-modal .yn-group--pills .yn-btn{
  display:inline-flex; align-items:center; justify-content:center;
  min-width:94px; padding:10px 16px;
  border:2px solid #0284c7; color:#0284c7; background:#fff;
  border-radius:999px; font-weight:700; cursor:pointer; transition:.18s ease;
}

#child-modal .yn-group--pills input[type="radio"]{ display:none; }

#child-modal .yn-group--pills input[type="radio"]:checked + .yn-btn{
  background:#0284c7; color:#fff;
  box-shadow:0 6px 18px rgba(11,102,195,.25);
}

/* Label above the pills */
#child-modal .child-residing-label{
  display:block; margin:0 0 8px; font-size:16px; font-weight:700; color:#0f172a;
}

/* ===== Sticky footer actions ===== */
#child-modal .child-actions{
  position:sticky; bottom:0;
  background:#fff;
  border-top:1px solid #eef2f7;
  padding:14px 24px;
  box-shadow: 0 -6px 20px rgba(2,8,20,.06);
}

/* Make actions responsive */
@media (max-width:680px){
  #child-modal .child-actions{ gap:10px; }
  #child-modal .child-actions .tax-btn,
  #child-modal .child-actions .tax-btn-secondary{
    flex:1 1 0; min-width:0;
  }
}

/* Buttons (inherit your existing .tax-btn styles; subtle polish) */
#child-modal .tax-btn{
  font-weight:700; border-radius:999px; padding:12px 20px;
}
#child-modal .tax-btn-secondary{
  font-weight:700; border-radius:999px; padding:12px 20px;
}

/* Small typographic nudges */
#child-modal .qs-title.small{ color:#475569; }

/* Make the form a 2-col grid on desktop and place the Residing block opposite the DOB */
#child-modal .fi-grid{
  display: grid;
  gap: 16px 24px;              /* row/col spacing */
}

@media (min-width: 720px){
  #child-modal .fi-grid{ grid-template-columns: 1fr 1fr; }

  /* 1: First name | 2: Last name */
  #child-modal .fi-grid > .fi-group:nth-of-type(1){ grid-column: 1; }
  #child-modal .fi-grid > .fi-group:nth-of-type(2){ grid-column: 2; }

  /* 3: DOB (left) | 4: Residing (right) */
  #child-modal .fi-grid > .fi-group:nth-of-type(3){ grid-column: 1; }
  #child-modal .fi-grid > .fi-group:nth-of-type(4){
    grid-column: 2;

    /* align the Yes/No section to the bottom of the row so it lines up with the DOB input */
    align-self: end;
  }
}

/* Small tidy-ups so the label + pills have nice spacing, without changing your input look */
#child-modal .child-residing-label { margin: 0 0 8px; font-weight: 600; }
#child-modal .yn-group { display: inline-flex; gap: 10px; flex-wrap: wrap; }

/* Keep inputs’ vertical size consistent so bottom alignment is crisp */
#child-modal .fi-float .fi-input { min-height: 48px; }  /* does not alter your styling */


/* Hide any native picker indicator (some browsers show a tiny square) */
#child-modal .dob-input::-webkit-calendar-picker-indicator { display:none !important; }
#child-modal .dob-input::-ms-clear { display:none !important; }
#child-modal .dob-input::-webkit-clear-button { display:none !important; }

/* Reserve space on the right inside the input for our button */
#child-modal .dob-input.calendarized { padding-right: 52px !important; }

/* Calendar button inside the field */
#child-modal .dob-calendar-btn{
  position:absolute; right:10px; top:50%; transform:translateY(-50%);
  width:36px; height:36px;                   /* nice tap size */
  border:0; background:transparent; color: #0284c7; cursor:pointer;
  display:inline-flex; align-items:center; justify-content:center;
  -webkit-appearance:none; appearance:none;  /* iOS fix */
  -webkit-tap-highlight-color: transparent;
}
#child-modal .dob-calendar-btn svg{ width:22px; height:22px; display:block; }

@media (max-width:640px){
  #child-modal .dob-calendar-btn{ width:40px; height:40px; right:8px; }
  #child-modal .dob-input.calendarized{ padding-right: 60px !important; }
}


/* Actions row layout */
#child-modal .child-actions{
  display:flex;
  justify-content:flex-end;
  gap:16px;
}

/* Save = solid blue pill */
#child-modal #child-save,
#child-modal .tax-btn.save-primary{
  background:#0284c7;           /* blue fill */
  color:#fff;                    /* white text */
  border:2px solid #0284c7;      /* matching border */
  border-radius:999px;           /* pill */
  padding:12px 24px;
  font-weight:800;
  letter-spacing:.02em;
  line-height:1;
  box-shadow:0 8px 20px rgba(2,132,199,.22);
}
#child-modal #child-save:hover{ background:#0284c7; border-color:#0284c7; }
#child-modal #child-save:active{ transform:translateY(1px); }
#child-modal #child-save:focus-visible{
  outline:3px solid rgba(2,132,199,.35);
  outline-offset:2px;
}
#child-modal #child-save:disabled{
  background:#93c5fd; border-color:#93c5fd; box-shadow:none; cursor:not-allowed;
}

/* Cancel = white pill with blue outline */
#child-modal #child-cancel,
#child-modal .tax-btn-secondary{
  background:#fff;
  color:#0284c7;
  border:2px solid #0284c7;
  border-radius:999px;
  padding:12px 24px;
  font-weight:800;
  letter-spacing:.02em;
  line-height:1;
}
#child-modal #child-cancel:hover{ background:#eef6ff; }

/* END OF CHILD STYLE  */
</style>

<style>
  /* We render card rows, so drop the old column headers entirely */
  #rental-table thead { display: none !important; }

  /* Keep the placeholder looking tidy */
  #props-empty-row td { padding: 12px 8px; opacity: .7; }

  /* Cosmetic: ensure there’s no odd spacing from the table */
  #rental-table { border-collapse: collapse; }

/* ===== Property cards (visual) ===== */
.prop-card{background:#fff;border:1px solid #e9eef5;border-radius:14px;padding:12px;margin:16px 0}
.prop-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.prop-title{font-weight:700;font-size:20px;margin:0}
.prop-actions{display:flex;gap:.5rem}
.prop-actions .icon-btn{display:inline-flex;align-items:center;justify-content:center;
  width:34px;height:34px;border:1px solid #d9e4f0;border-radius:9px;background:#f7fbff;color:#0b62b2}

/* ===== Tables inside the card ===== */
.prop-table{width:100%;border-collapse:separate;border-spacing:0}
.prop-table td,.prop-table th{padding:12px 16px;border-bottom:1px solid #eef2f6;vertical-align:top;}
.prop-table tr:last-child td{border-bottom:0}

/* Make long partner names wrap nicely */
.prop-table .wrap{word-break:break-word}

/* 1) Keep Start / End dates on ONE line */
.prop-table .date,
.prop-table [data-date],
/* fallback to the value cells of the first row (Start / End) */
.prop-details tr:nth-child(1) td:nth-child(2),
.prop-details tr:nth-child(1) td:nth-child(4){
  white-space:nowrap;           /* no line wrap */
  overflow-wrap:normal;
}


/* Small niceties */
.prop-section-title{font-weight:700;font-size:16px;padding:10px 14px;background:#f7fbff;
  border:1px solid #eef2f6;border-radius:10px;margin:12px 0 0}
.prop-box{border:1px solid #eef2f6;border-radius:12px;margin-top:0}



</style>

<style>
/* ---------- tablet (≤1024px) ---------- */
@media (max-width: 1024px){
  .prop-card{ padding:12px }
  .prop-table td{ padding:10px 12px }
}

/* ---------- mobile (≤768px) ---------- */
/* Make each label/value pair a single line:
   we turn each table row into a 2-column grid (Label | Value).
   Rows that have 6 cells (Start/End/Partner) will become 3 lines automatically. */


/* Suffixes (kept for mobile/desktop) */
.prop-details td[data-unit="percent"]::after{ content:" %" }
.prop-details td[data-unit="cad"]::after{ content:" $CAD" }
.prop-expenses td[data-unit="cad"]::after{ content:" $CAD" }

/* ========== HARD FIXES ========== */

/* 0) Make the embedded tables act like their own world */
.prop-card { overflow: hidden; }
.prop-card table { width: 100%; table-layout: fixed; border-collapse: separate; }
.prop-card td { word-break: break-word; }

/* Kill any baseline pseudo-lines inherited from the parent rental table/theme */
#rental-table .prop-card td::before,
#rental-table .prop-card td::after { content: none !important; }

/* 1) ONE-LINE layout on mobile/tablet (label | value) */
@media (max-width: 1024px){
  .prop-details tbody tr,
  .prop-expenses tbody tr{
    display: grid;
    grid-template-columns:1fr 200px;  /* label | value */
    column-gap: 12px;
    row-gap: 6px;
    align-items: start;
  }

  /* labels stronger, values normal */
  .prop-details td:nth-child(odd),
  .prop-expenses td:nth-child(odd){
    color:#475569;
    font-weight: 600;
  }

  /* keep any dates on a single line */
  .prop-details .date,
  .prop-details [data-date]{ white-space: nowrap; }
}

/* 2) APPEND UNITS ONLY WHEN WE SAY SO (by data-unit),
   and NEVER by brittle nth-child fallbacks */
.prop-details td[data-unit="percent"]::after { content: " %" }
.prop-details td[data-unit="cad"]::after     { content: " $CAD" }
.prop-expenses td[data-unit="cad"]::after    { content: " $CAD" }

/* REMOVE the old fallbacks that were adding % to your dates.
   Delete these from your CSS if present:
     .prop-details tbody tr:nth-child(2) td:nth-child(2)::after{content:" %"}
     .prop-details tbody tr:nth-child(2) td:nth-child(4)::after{content:" %"}
     .prop-details tbody tr:nth-child(2) td:nth-child(6)::after{content:" $CAD"}
     .prop-expenses tbody tr > td:nth-child(2)::after{content:" $CAD"}
     .prop-expenses tbody tr > td:nth-child(4)::after{content:" $CAD"}
*/

/* 3) No extra separator under the last "Other" row */
.prop-expenses tbody tr:last-child td { border-bottom: 0 !important; }


@media (max-width: 768px){
  .prop-card{ padding:12px }
  .prop-table{ border-spacing:0; border-collapse:separate }
  .prop-table td{ border-bottom:0; padding:8px 10px }

  /* One-line label/value layout */
  .prop-details tbody tr,
  .prop-expenses tbody tr{
    display:grid;
    grid-template-columns: 140px 1fr !important;  
    column-gap:10px;
    row-gap:6px;
    align-items:end !important;
  }

  /* Emphasize labels, keep values normal */
  .prop-details td:nth-child(odd),
  .prop-expenses td:nth-child(odd){
    color:#475569; font-weight:600;
  }

  /* Keep dates on one line */
  .prop-table .date,
  .prop-details [data-date]{
    white-space:nowrap;
  }
}



/* Desktop default: label cells at 18% */
.prop-details td.prop-label { width: 18%; font-weight: 600;}

@media (max-width: 1024px){
 .prop-details td.prop-label { width: 60%; }
}
/* Mobile ≤ 768px: stack cells, labels take full width */
@media (max-width: 768px){
  .prop-details tbody tr{
    display: grid;
    grid-template-columns: 1fr;   /* each cell becomes a full-width row */
    row-gap: 6px;
  }
  .prop-details td{
    display: block;
    width: 100% !important;
  }
  .prop-details td.prop-label{
    font-weight: 600;
    color: #475569;
  }
}

.prop-expenses td.prop-label{font-weight: 600; color: #475569;}

/* --- Property modal fixes only --- */

/* 0) Make [hidden] always win */
#prop-modal [hidden] { display: none !important; }

/* 1) Pure X in the top-right (no bg/border) */
#prop-modal .prop-x {
  position: absolute; top: 10px; right: 16px;
  background: none !important; border: 0 !important;
  padding: 6px; line-height: 1; cursor: pointer;
  color: #0b66c3; display: inline-flex; align-items: center; justify-content: center;
}
#prop-modal .prop-x::before {
  content: "×"; font-size: 26px; font-weight: 700; line-height: 1;
}
#prop-modal .prop-x:focus-visible { outline: 2px solid rgba(11,102,195,.35); outline-offset: 2px; }

/* 2) Buttons — unify size on desktop */
#prop-modal .qs-modal__foot .tax-btn,
#prop-modal .qs-modal__foot .tax-btn-secondary{
  height: 48px; padding: 0 24px; min-width: 160px;
  border-radius: 999px; display: inline-flex; align-items: center; justify-content: center;
}

/* 3) Mobile footer: two equal columns and full-width pills */
@media (max-width: 640px){
  #prop-modal .qs-modal__foot{
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px;
  }
  #prop-modal .qs-modal__foot .tax-btn,
  #prop-modal .qs-modal__foot .tax-btn-secondary{
    min-width: 0; width: 100%;
  }
}

/* 4) Calendar icon size + padding on phone */
#prop-modal .dob-calendar-btn{ width: 28px; height: 28px; }
@media (max-width: 640px){
  #prop-modal .dob-calendar-btn{ width: 32px; height: 32px; right: 10px; }
  #prop-modal .fi-float .fi-input{ padding-right: 52px; } /* room for the bigger icon */
}

/* Mobile: only show the visible footer, make it 2 equal columns */
@media (max-width:640px){
  #prop-modal .qs-modal__foot{ display:none; } /* baseline */
  #prop-modal .qs-modal__foot:not([hidden]){
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:12px;
  }
  #prop-modal .qs-modal__foot .tax-btn,
  #prop-modal .qs-modal__foot .tax-btn-secondary{
    width:100%;
    min-width:0;
    height:48px;
    border-radius:999px;
    display:inline-flex;
    align-items:center;
    justify-content:center;
  }
}

/* (Optional) Desktop: make Back/Save the same size */
@media (min-width:641px){
  #prop-modal .qs-modal__foot .tax-btn,
  #prop-modal .qs-modal__foot .tax-btn-secondary{
    height:48px;
    min-width:160px;
    padding:0 24px;
    border-radius:999px;
  }
}


#btn-add-property{
  background: transparent !important;
  border: none !important;
  box-shadow: none !important;
  padding: 0 !important;
  margin: 0;
  color: #0b66c3;                 /* same blue */
  font-weight: 700;
  line-height: 1;
  display: inline-flex !important;
  align-items: center;
  gap: 8px;                       /* space between text and + */
  white-space: nowrap;
  cursor: pointer;
}

/* hover/focus like a link */
#btn-add-property:hover{ text-decoration: none; }
#btn-add-property:focus-visible{
  outline: 2px solid rgba(11,102,195,.25);
  outline-offset: 2px;
  border-radius: 6px;
}


#btn-add-property::after{
  content: "";
  width: 20px; height: 20px;               /* tweak to 22px if you want it larger */
  display: inline-block;
  background-repeat: no-repeat;
  background-size: contain;
  background-image: url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='%230284c7'>\
    <circle cx='12' cy='12' r='10'/>\
    <path d='M12 8v8M8 12h8' stroke='%23fff' stroke-width='2' stroke-linecap='round'/>\
  </svg>");
}

/* a little breathing room on small screens */
@media (max-width:640px){
  #add-prop-wrap-top, #add-prop-wrap-bottom{ padding-right: 4px; }
  #btn-add-property::after{ width: 22px; height: 22px; } /* slightly bigger touch target */
}


/* END OF PROPERTY STYLE */
</style>

<style>
/* ============ DROPZONE ============ */
/* Container holds the dashed area + the file list together */
.dropzone{
  position:relative;
  background:#fff;
  border-radius:14px;
  box-shadow:0 4px 12px rgba(0,0,0,.05);
  padding:14px;
}
.dropzone.dragover .dropzone-ui{
  border-color:#0284c7;
  box-shadow:inset 0 0 0 4px rgba(2,132,199,.12);
  background:#f8fdff;
}

/* Dashed area */
.dropzone-ui{
  border:2px dashed #f4b860;
  border-radius:12px;
  min-height:120px;
  display:flex;align-items:center;justify-content:center;
  padding:24px;
  color:#64748b;
  text-align:center;
}
.dropzone .dz-browse{
  margin-left:.35rem;
  padding:.5rem 1rem;
  border:2px solid #0284c7;
  border-radius:999px;
  background:#fff;
  color:#0284c7;
  font-weight:700;
  cursor:pointer;
}
.dropzone .dz-browse:hover{ background:#0284c7; color:#fff; }

/* File list INSIDE the box */
.dz-list{
  margin-top:14px;
  display:flex;flex-direction:column;gap:10px;
  max-height:280px;overflow:auto;
}

/* File card */
.dz-item{
  display:flex;align-items:center;gap:12px;
  padding:10px 12px;
  background:#fff;
  border:1px solid #e2e8f0;
  border-radius:12px;
  color:#0f172a;
}
.dz-item:hover{ border-color:#cbd5e1; box-shadow:0 2px 12px rgba(2,8,23,.06); }

/* Icon & thumbnail */
.dz-icon,.dz-thumb{ width:44px; height:44px; flex:0 0 44px; border-radius:8px; }
.dz-thumb{ object-fit:cover; }

/* Meta */
.dz-meta{ flex:1; min-width:0; }
.dz-name-row{ display:flex; align-items:center; gap:12px; }
.dz-name{ font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dz-sub{ font-size:12px; color:#64748b; margin-top:2px; }
.dz-loading{ display:flex; align-items:center; gap:8px; flex-shrink:0; }
.dz-loading .dz-bar{ width:60px; height:4px; background:#e5f4e8; border-radius:4px; overflow:hidden; margin:0; }
.dz-loading .dz-bar-fill{ width:0%; height:100%; background-image:repeating-linear-gradient(135deg,#22c55e 0 8px,#16a34a 8px 16px); background-size:16px 16px; animation:dzStripes .75s linear infinite; transition: width 0.2s ease; }

/* Progress — always full, animated stripes */
.dz-bar{
  height:10px; background:#e5f4e8; border-radius:8px; overflow:hidden; margin-top:8px;
  position:relative;
}
.dz-bar-fill{
  width:0%; height:100%;
  background-image:repeating-linear-gradient(135deg,#22c55e 0 12px,#16a34a 12px 24px);
  background-size:28px 28px;
  animation:dzStripes .75s linear infinite;
  will-change:background-position;
  transition: width 0.2s ease;
}
@keyframes dzStripes{
  from{ background-position:0 0; }
  to  { background-position:28px 0; }
}

/* Remove button */
.dz-remove{
  background:none; border:0; color:#334155;
  font-size:22px; line-height:1; padding:6px 8px; cursor:pointer;
}
.dz-remove:hover{ color:#ef4444; }

/* ============ TOASTS (centered, responsive, non-blocking) ============ */
.dz-toastbox{
  position:fixed;
  top:clamp(12px, 3vh, 36px);
  left:50%;
  transform:translateX(-50%);
  width:min(92vw, 520px);
  display:grid;
  gap:10px;
  z-index:9999;
  pointer-events:none;              /* never block clicks behind */
}

/* Toast card */
.dz-toast{
  display:flex; align-items:center; gap:12px; width:100%;
  border-radius:14px; padding:12px 14px;
  box-shadow:0 12px 30px rgba(0,0,0,.18);
  border:1px solid transparent;
  animation:dzPop .18s ease-out;
  pointer-events:none;              /* only the X is clickable */
}
.dz-toast-ok{  background:#e7f6ec; border-color:#bbf7d0; color:#065f46; }
.dz-toast-bad{ background:#fee2e2; border-color:#fecaca; color:#7f1d1d; }

.dz-toast-icon{ width:28px; height:28px; display:block; border-radius:999px; }
.dz-toast-msg{ line-height:1.35; flex:1; }

/* Close “×” — perfectly centered */
.dz-toast-x{
  display:inline-flex; align-items:center; justify-content:center;
  width:32px; height:32px; padding:0; margin:0; line-height:1;
  border-radius:8px; background:none; border:0; color:inherit; cursor:pointer;
  pointer-events:auto;
}
.dz-toast-x:hover{ background:rgba(0,0,0,.06); }

@keyframes dzPop{
  from{ transform:translateY(-8px) scale(.98); opacity:0; }
  to  { transform:translateY(0)    scale(1);   opacity:1; }
}

/* Tablet/Laptop tweak */
@media (min-width:768px){
  .dz-toastbox{ width:min(85vw, 560px); }
}

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce){
  .dz-toast{ animation:none; }
  .dz-bar-fill{ animation:none; }
}

/* Keep tabs above nearby UI */
.upload-tabs{ position:relative; z-index:5; }

.continue-button{
text-decoration: uppercase;
}

/* ============ Password toggle UI ============ */
.dz-row { display:flex; align-items:center; gap:12px; }
.dz-grow { flex:1; min-width:0; }

.dz-pw {
  display:grid;
  grid-template-columns: auto auto 160px;
  align-items:center;
  gap:10px;
  margin-left:auto;
}
.dz-pw-label { font-size:12px; color:#475569; white-space:nowrap; }

.pw-group { display:inline-flex; border:1px solid #cbd5e1; border-radius:999px; overflow:hidden; }
.pw-btn {
  padding:6px 12px; font-weight:700; border:0; background:#f1f5f9; color:#334155; cursor:pointer;
}
.pw-btn:hover { background:#e2e8f0; }
.pw-btn.is-active { background:#0284c7; color:#fff; }   /* active look = “YES design” */

.pw-input {
  width:160px; border:1px solid #cbd5e1; border-radius:8px; padding:6px 8px; font:inherit;
}
.pw-input:disabled {
  background:#f8fafc; color:#94a3b8; cursor:not-allowed; opacity:.7;
}
@media (max-width:680px){
  .dz-pw{ grid-template-columns: auto 1fr; }
  .pw-input{ grid-column: 1 / -1; width:100%; }
}


/* Header row above file cards */
.dz-head{
  display:grid;
  grid-template-columns: 1fr 260px 180px; /* File | Is Password? | Password */
  gap:12px;
  align-items:center;
  font-weight:700;
  color:#334155;
  margin:10px 2px 6px;
}
.dz-head > div{ font-size:14px; }

/* Make each item use the same 3-column layout on wide screens */
.dz-item{
  display:grid;
  grid-template-columns: 1fr 260px 180px;
  gap:12px;
  align-items:center;
}

/* Left column content (icon + meta) stays horizontal */
.dz-left{
  display:flex; align-items:center; gap:12px; min-width:0;
}

/* The Yes/No pills area */
.dz-pw-yn .yn-group{ display:inline-flex; }
.dz-pw-yn .yn-btn{ min-width:56px; padding:8px 14px; }

/* Error styling for password protected question */
.dz-pw-yn.error {
  border: 1px solid #ef4444;
  border-radius: 8px;
  padding: 8px;
  background-color: #fef2f2;
}
.dz-pw-yn.error .fi-error-text {
  color: #dc2626;
  font-size: 12px;
  margin-top: 6px;
  display: block;
}

/* Error styling for upload sections (ID Proof, etc.) */
.qs-block.error {
  padding: 0px;
  background-color:rgb(255, 255, 255);
}
.qs-block.error .fi-error-text {
  color: #dc2626;
  font-size: 12px;
  margin-top: 8px;
  display: block;
}

/* The password box */
.dz-pw-input .pw-input{
  width:100%;
  border:1px solid #cbd5e1; border-radius:8px; padding:8px 10px; font:inherit;
}
.dz-pw-input .pw-input:disabled{
  background:#f8fafc; color:#94a3b8; cursor:not-allowed; opacity:.7;
}

/* Responsive: stack into rows on narrow screens */
@media (max-width:860px){
  .dz-head{ display:none; }
  .dz-item{
    grid-template-columns: 1fr; gap:8px;
  }
  .dz-pw-yn{ order:2; }
  .dz-pw-input{ order:3; }
}

/* --- Align file | Yes/No | Password to header columns --- */
.dz-head{
  display:grid;
  grid-template-columns: minmax(0,1fr) 240px 220px; /* match item rows */
  gap:12px;
  align-items:center;
  font-weight:700; color:#334155;
  margin:10px 2px 6px;
}

.dz-item{
  display:grid;
  grid-template-columns: minmax(0,1fr) 240px 220px; /* File | Is PW? | Password */
  gap:12px;
  align-items:center;   /* vertical center across the row */
}

/* left cell (icon+meta card) stays as-is */
.dz-left{ display:flex; align-items:center; gap:12px; min-width:0; }

/* middle + right cells: center content neatly */
.dz-pw-yn, .dz-pw-input{ display:flex; align-items:center; justify-content: center;}
.dz-pw-yn .yn-group{ display:inline-flex; gap:10px; justify-content: center;}

/* ===== MOBILE TWEAKS (≤ 860px) ===== */
@media (max-width: 860px){

  /* 2-row/grid layout per file:
     Row 1: left (icon+name+bar) spans both columns
     Row 2: [Is PW? + Yes/No] | [Password field] */
  .dz-head{ display:none !important; }

  .dz-item{
    display:grid !important;
    grid-template-columns: minmax(140px, 0.55fr) 1fr; /* left col a bit narrower */
    grid-template-areas:
      "left left"
      "yn   pw";
    gap:10px;
    align-items:start;   /* align tops with the icon area */
  }

  .dz-left{
    grid-area:left;
    display:flex; align-items:flex-start; gap:12px; min-width:0;
  }

  /* --- LEFT column: no change to icon/meta, but keep spacing tight --- */
  .dz-left .dz-meta{ margin-top:2px; }

  /* --- MIDDLE: question + pills stacked (pills UNDER the question) --- */
  .dz-pw-yn{
    grid-area:yn;
    display:flex; flex-direction:column; align-items:flex-start;
  }
  /* Show the question text above the pills */
  .dz-pw-yn::before{
    content: attr(data-label);     /* e.g., "Is Password Protected?" */
    display:block;
    font-size:12px; color:#475569; font-weight:600;
    margin:0 0 6px;
  }
  /* Make pills sit directly under the question */
  .dz-pw-yn .yn-group{
    display:inline-flex; gap:8px;
  }
  .dz-pw-yn .yn-btn{
    min-width:56px; padding:10px 14px; border-radius:999px; line-height:1;
  }

  /* --- RIGHT: password field; hide the word "Password" on mobile --- */
  .dz-pw-input{
    grid-area:pw;
    display:flex; flex-direction:column; align-items:stretch;
  }
  /* Remove the label only on mobile */
  .dz-pw-input::before{ content:''; display:none; }

  .dz-pw-input .pw-input{
    width:100%;
    height:44px;                    /* match pill height */
    border:2px solid #94a3b8;
    border-radius:10px;
    padding:8px 12px;
    font:inherit;
  }
}

/* =========================
   DROPZONE – FINAL OVERRIDES
   (Place AFTER your current CSS)
   ========================= */

/* Desktop: force a clean 3-column grid and baseline align */
.dropzone .dz-item{
  display:grid;
  grid-template-columns: minmax(0,1fr) 240px 260px; /* File | Y/N | Password */
  gap:12px;
  align-items:center;                   /* vertical centering for all three cells */
}

/* Center the Y/N group vertically and kill stray margins */
.dropzone .dz-pw-yn,
.dropzone .dz-pw-input{
  display:flex;
  align-items:center;
  justify-content:center;
  margin:0;
}

/* Make pills the same visual height as the password field */
.dropzone .dz-pw-yn .yn-group{ display:inline-flex; gap:10px; }
.dropzone .dz-pw-yn .yn-btn{
  display:inline-flex;                  /* fix “too high” look */
  align-items:center;
  justify-content:center;
  height:44px;                          /* match input height */
  line-height:1;
  padding:0 16px;
}

/* Make the password field the same height as the pills */
.dropzone .dz-pw-input .pw-input{
  width:100%;
  height:44px;
  border:2px solid #94a3b8;
  border-radius:10px;
  padding:8px 12px;
  font:inherit;
}

/* ---------- Mobile (≤ 860px) ---------- */
@media (max-width:860px){

  /* Stack per item:
     Row 1: left (icon+name+bar)
     Row 2: [question + pills] | [password] */
  .dropzone .dz-head{ display:none !important; }

  .dropzone .dz-item{
    grid-template-columns: minmax(140px, 0.55fr) 1fr;
    grid-template-areas:
      "left left"
      "yn   pw";
    align-items:start;
    gap:10px;
  }

  .dropzone .dz-left{ grid-area:left; }
  .dropzone .dz-pw-yn{ grid-area:yn; flex-direction:column; align-items:flex-start; }
  .dropzone .dz-pw-input{ grid-area:pw; align-items:flex-start; }

  /* Show the question in one line, pills below it */
  .dropzone .dz-pw-yn::before{
    content: attr(data-label);          /* comes from JS: "Is Password Protected?" */
    display:block;
    font-size:12px;
    color:#475569;
    font-weight:600;
    margin:0 0 6px;
    white-space:nowrap;                 /* ensure it's on one line */
  }

  /* Hide the word “Password” (keep placeholder in the input) */
  .dropzone .dz-pw-input::before{ content:''; display:none; }

  /* Keep pills tidy on mobile too */
  .dropzone .dz-pw-yn .yn-btn{
    min-width:56px;
    height:42px;                        /* a hair smaller if you prefer */
    padding:0 14px;
  }
}


/* =========================
   DROPZONE – LAYOUT OVERRIDES
   ========================= */

/* Desktop stays 3 columns; keep your existing desktop rules. */

/* Tablet & Mobile: stack as
   Row 1: left (file card)
   Row 2: label + Yes/No (inline, same line)
   Row 3: password (full width)
*/
@media (max-width: 1024px){
  .dropzone .dz-head{ display:none !important; }

  .dropzone .dz-item{
    display:grid !important;
    grid-template-columns: 1fr;                 /* single column */
    grid-template-areas:
      "left"
      "yn"
      "pw";
    gap:10px;
    align-items:start;
  }

  .dropzone .dz-left{ grid-area:left; }

  /* Question + pills on ONE line */
  .dropzone .dz-pw-yn{
    grid-area:yn;
    display:flex;
    align-items:center;                          /* vertical align label & pills */
    gap:10px;
    margin:0;
  }
  /* show the question as inline text before the pills */
  .dropzone .dz-pw-yn::before{
    content: attr(data-label);                   /* e.g., "Is Password Protected?" */
    display:inline-block;
    font-size:12px; font-weight:600; color:#475569;
    margin-right:6px;
    white-space:nowrap;                          /* keep on one line */
  }
  .dropzone .dz-pw-yn .yn-group{
    display:inline-flex; gap:8px;
  }
  .dropzone .dz-pw-yn .yn-btn{
    min-width:56px; height:42px; padding:0 14px; line-height:1;
  }

  /* Password input UNDER the question+pills, full width */
  .dropzone .dz-pw-input{
    grid-area:pw;
    display:block;
  }
  .dropzone .dz-pw-input .pw-input{
    width:100%;
    height:44px;
    border:2px solid #94a3b8; border-radius:10px; padding:8px 12px; font:inherit;
  }
}

/* Desktop polish: Y/N and Password same height & baseline */
.dropzone .dz-item{
  grid-template-columns: minmax(0,1fr) 240px 260px;
  align-items:center;
}
.dropzone .dz-pw-yn .yn-group{ display:inline-flex; gap:10px; }
.dropzone .dz-pw-yn .yn-btn{ height:44px; padding:0 16px; display:inline-flex; align-items:center; }
.dropzone .dz-pw-input .pw-input{ height:44px; }


/* =========================================
   FINAL, MINIMAL OVERRIDES
   ========================================= */

/* --- Desktop (keep 3 cols; align pills with input) --- */
.dropzone .dz-item{
  grid-template-columns: minmax(0,1fr) 240px 260px; /* File | Y/N | Password */
  align-items: center;
}
.dropzone .dz-pw-yn .yn-group{ display:inline-flex; gap:10px; }
.dropzone .dz-pw-yn .yn-btn{
  height:44px; padding:0 16px;
  display:inline-flex; align-items:center; justify-content:center;
}
.dropzone .dz-pw-input .pw-input{ height:44px; }

/* --- Mobile & Tablet (≤860px)
     Row1: file card
     Row2: [ "Is Password Protected?"  Yes  No ]  <-- same line
     Row3: [ Password input ] (full width)
-------------------------------------------------- */
@media (max-width:860px){

  .dropzone .dz-head{ display:none !important; }

  .dropzone .dz-item{
    display:grid !important;
    grid-template-columns: 1fr;
    grid-template-areas:
      "left"
      "yn"
      "pw";
    gap:10px;
    align-items:start;
  }

  .dropzone .dz-left{ grid-area:left; }

  /* Put label + pills on ONE line */
  .dropzone .dz-pw-yn{
    grid-area:yn;
    display:grid !important;
    grid-template-columns: auto max-content;   /* label | pills */
  justify-content: left;
    align-items:center;
    column-gap:10px;
    margin:0;
  }
  .dropzone .dz-pw-yn::before{
    content: attr(data-label);
    font-size:12px; font-weight:600; color:#475569;
    white-space:nowrap;                  /* stay on one line */
  }
  .dropzone .dz-pw-yn .yn-group{
    display:inline-flex; gap:8px; 
  margin-bottom: 0;
  }
  .dropzone .dz-pw-yn .yn-btn{
    min-width:56px; height:42px; padding:0 14px; line-height:1;
  }

  /* Password input BELOW, full width */
  .dropzone .dz-pw-input{
    grid-area:pw;
    display:block;
  }
  .dropzone .dz-pw-input .pw-input{
    width:100%;
    height:40px;
    border:2px solid #94a3b8; border-radius:10px; padding:8px 12px; font:inherit;
  }

  /* Optional: remove the inner scrollbar on short screens */
  .dropzone .dz-list{ max-height:unset; }
}
/* Desktop: center the Yes/No pills vertically in their grid cell */
@media (min-width: 861px){
  .dropzone .dz-item{ align-items: center; }            /* center all cells */
  .dropzone .dz-pw-yn{
    align-self: center;                                  /* center this cell */
    display: flex;
    align-items: center;                                  /* center the pills inside */
    margin: 0;
  }
  .dropzone .dz-pw-yn .yn-group{ display:inline-flex; align-items:center; margin-bottom: 0; }
  .dropzone .dz-pw-input{ align-self: center; }          /* keep password aligned too */
}

/* Mid desktop widths: let cols 2 & 3 shrink gracefully */
@media (min-width: 861px) and (max-width: 1200px){
  .dropzone .dz-head,
  .dropzone .dz-item{
    /* File | Y/N (fixed-ish) | Password (flex) */
    grid-template-columns: minmax(0,1fr) 180px minmax(220px, 1fr) !important;
    gap: 12px !important;
    align-items: center !important;
  }

  /* keep Y/N tidy and centered, no wrapping */
  .dropzone .dz-pw-yn{
    justify-self: center !important;
    display: flex !important;
    align-items: center !important;
  }
  .dropzone .dz-pw-yn .yn-group{
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    white-space: nowrap !important;
  }

  /* password fills remaining space cleanly */
  .dropzone .dz-pw-input{ justify-self: stretch !important; }
  .dropzone .dz-pw-input .pw-input{
    width: 100% !important;
    height: 44px;
  }
}

/* ========== 1) Mid-desktop (covers 1204px) ========== */
/* Let cols 2–3 shrink gracefully between 861–1280px */
@media (min-width: 861px) and (max-width: 1280px){
  .dropzone .dz-head,
  .dropzone .dz-item{
    /* File | Y/N (200) | Password (flex, min 240) */
    grid-template-columns: minmax(0,1fr) 200px minmax(240px,1fr) !important;
    gap: 12px !important;
    align-items: center !important;
  }
  .dropzone .dz-pw-yn{
    justify-self: center !important;
    display: flex !important;
    align-items: center !important;
  }
  .dropzone .dz-pw-yn .yn-group{
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px !important;
    white-space: nowrap !important;
  }
  .dropzone .dz-pw-input{ justify-self: stretch !important; }
  .dropzone .dz-pw-input .pw-input{ width: 100% !important; height: 44px; }
}

/* ========== 2) Ultra-narrow phones (covers 344px) ========== */
/* Make pills smaller; if still tight, stack them under the label */
@media (max-width: 360px){
  /* shrink pills */
  .dropzone .dz-pw-yn .yn-btn{
    min-width: 48px !important;
    height: 38px !important;
    padding: 0 10px !important;
    font-size: 14px !important;
  }
  .dropzone .dz-pw-yn .yn-group{ gap: 6px !important; }

  /* if space is too tight, allow a clean stack */
  .dropzone .dz-pw-yn{
    display: flex !important;
    flex-direction: column !important;   /* label on top, pills below */
    align-items: flex-start !important;
    gap: 6px !important;
  }
  .dropzone .dz-pw-yn::before{ margin: 0 !important; }

  .dropzone .dz-pw-input .pw-input{ height: 40px !important; }
}


</style>

<style>
.rev-panel { border:1px solid #cbd5e1; border-top:0; border-radius:0 0 12px 12px; background:#fff; padding:16px; margin-top:-2px; }
.rev-panel[hidden] { display:none !important; }
.rev-item[hidden]  { display:none !important; }
  /* ===== Review container ===== */
  .rev-wrap { max-width: 1160px; margin: 0 auto; }
  .rev-card  { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:18px; box-shadow:0 8px 22px rgba(2,6,23,.06); }
  .rev-title { font-size:28px; font-weight:800; text-align:center; margin:6px 0 18px; color:#0f172a; }

  /* ===== Accordion ===== */
  .rev-item {
    width:100%; display:flex; align-items:center; justify-content:space-between;
    background:#0284c7; color:#fff; border:0; border-radius:10px;
    padding:14px 16px; font-size:18px; font-weight:700; cursor:pointer;
    margin:12px 0 0; transition:filter .15s ease;
  }
  .rev-item:hover { filter:brightness(1.05); }
  .rev-item .rev-left { display:flex; align-items:center; gap:10px; }
  .rev-item .rev-small { opacity:.9; font-weight:600; font-size:14px; }
  .rev-item .rev-icon { font-size:26px; line-height:1; user-select:none; }
  .rev-item[aria-expanded="true"] .rev-icon::before { content:"–"; }
  .rev-item[aria-expanded="false"] .rev-icon::before { content:"+"; }


  /* Summary list layout */
  .rev-dl { display:grid; grid-template-columns: 240px 1fr; gap:10px 18px; margin:6px 0 6px; }
  .rev-dl dt { color:#475569; font-weight:600; }
  .rev-dl dd { margin:0; color:#0f172a; }

  /* Plain “Go to …” links (no box) */
  .rev-actions { margin-top:8px; }
  .rev-link { color: #0284c7; font-weight:700; text-decoration:none; }
  .rev-link:hover { text-decoration:underline; }

  /* Notes box (outside tabs) */
  .rev-notes { margin-top:16px; }
  .rev-textarea { width:100%; min-height:140px; padding:12px; border:1px solid #cbd5e1; border-radius:10px; font:inherit; }

  /* Make sidebar steps clickable (cursor only; style is yours) */
  .pi-steps.progress-only .pi-step { cursor:pointer; }

  @media (max-width: 680px){
    .rev-dl { grid-template-columns: 1fr; }
    .rev-item { font-size:16px; }
    .rev-title { font-size:24px; }
  }
                 
   /* Scroll wrapper for wide tables */
.rev-table-wrap { overflow-x:auto; -webkit-overflow-scrolling: touch; }

/* Keep your existing .rev-table styles; only small refinements below */
.rev-table th, .rev-table td { white-space: nowrap; }

/* On small screens, allow wrapping for address and partner */
@media (max-width: 760px) {
  .rev-table td:nth-child(2), /* Address */
  .rev-table td:nth-child(5)  /* Partner */
  { white-space: normal; }
}

/* tidy definition list */
.rev-dl { margin: 6px 0 14px; }
.rev-dl dt { font-weight:600; color:#334155; padding:8px 0 2px; }
.rev-dl dd { margin: 8px 0 2px; color:#0f172a; }

/* Base list */
/* Review filenames */
.rev-files{ margin:8px 0 0; padding-left:0; list-style:none; }
.rev-file{ display:flex; align-items:center; gap:12px; padding:6px 0; line-height:1.2; }
.rev-file-name{ font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.rev-ico-svg{ display:block; }

/* Phones */
@media (max-width: 480px){
  .rev-file{ gap:10px; padding:5px 0; }
  .rev-file .rev-ico-svg{ width:24px; height:24px; }
  .rev-file-name{ font-size:13.5px; max-width:100%; }
}

/* Tablets */
@media (min-width: 481px) and (max-width: 1024px){
  .rev-file .rev-ico-svg{ width:26px; height:26px; }
}

/* Desktop */
@media (min-width: 1025px){
  .rev-file .rev-ico-svg{ width:28px; height:28px; }
}

/* Icons */
.rev-ico{ display:inline-flex; align-items:center; justify-content:center; flex:0 0 auto; }
.rev-ico-wrap{ position:relative; display:inline-block; line-height:0; }
.rev-ico-badge{
  position:absolute; left:22px; bottom:-2px; transform:translateX(-50%);
  background:#fff; color:#111827; border-radius:3px; padding:1px 3px;
  font-size:9px; font-weight:700; letter-spacing:.2px;
  border:1px solid rgba(0,0,0,.08);
}
/* Tiny thumbnail for image files */
.rev-ico-thumb-wrap{ width:20px; height:20px; border-radius:4px; overflow:hidden; display:inline-block; }
.rev-ico-thumb{ width:100%; height:100%; object-fit:cover; display:block; }

/* Count text near "file(s)" (optional polish) */
.rev-dl dd > span[id$="-count"]{ font-weight:700; margin-right:4px; }

/* ---------- Responsiveness ---------- */

/* Phones: tighter spacing, smaller icons */
@media (max-width: 480px){
  .rev-file{ gap:8px; padding:4px 0; }
  .rev-ico-badge{ font-size:8px; left:20px; }
  .rev-ico-thumb-wrap{ width:18px; height:18px; }
  .rev-file-name{ font-size:13px; }
  /* Prevent horizontal scroll in narrow side panels */
  .rev-dl, .rev-files{ max-width:100%; overflow:hidden; }
}

/* Tablets: comfortable targets */
@media (min-width: 481px) and (max-width: 1024px){
  .rev-file{ gap:10px; padding:6px 0; }
  .rev-ico-thumb-wrap{ width:20px; height:20px; }
  .rev-file-name{ font-size:14px; }
}

/* Desktop: a little larger */
@media (min-width: 1025px){
  .rev-ico-thumb-wrap{ width:22px; height:22px; }
  .rev-file-name{ font-size:14px; }
}

/* ---------- Desktop default (≥1024px) ---------- */
.dz-head{
  display:grid;
  grid-template-columns: minmax(0,1fr) minmax(200px,0.6fr) minmax(180px,0.6fr);
  gap:12px; align-items:center;
  font-weight:700; color:#334155;
  margin:10px 2px 6px;
}
.dz-item{
  display:grid;
  grid-template-columns: minmax(0,1fr) minmax(200px,0.6fr) minmax(180px,0.6fr);
  gap:12px; align-items:center;
}
.dz-left{ display:flex; align-items:center; gap:12px; min-width:0; }
.dz-pw-yn, .dz-pw-input{ display:flex; align-items:center; }
.dz-pw-yn .yn-group{ display:inline-flex; gap:10px; }

.dz-pw-input .pw-input{
  width:100%; height:42px;
  border:2px solid #94a3b8; border-radius:10px; padding:8px 12px; font:inherit;
}
.pw-input:disabled{ background:#f8fafc; color:#94a3b8; cursor:not-allowed; opacity:.7; }

/* ---------- Tablet (720px–1023px) keep header, compress columns ---------- */
@media (max-width:1023px){
  .dz-head,
  .dz-item{
    grid-template-columns: minmax(0,1fr) 190px 170px;
    gap:10px;
  }
  .dz-head > div{ font-size:13px; }
  .dz-name{ font-size:15px; }
}

/* ---------- Mobile (<720px): hide header, show inline labels per row ---------- */
@media (max-width:719.98px){
  .dz-head{ display:none; }
  .dz-item{
    grid-template-columns: 1fr;
    gap:8px;
  }

  /* Inline section labels */
  .dz-pw-yn::before,
  .dz-pw-input::before{
    content: attr(data-label);
    display:block;
    font-size:12px;
    color:#475569;
    margin-bottom:4px;
    font-weight:600;
  }

  /* make buttons full width-ish without looking cramped */
  .dz-pw-yn .yn-group{ gap:8px; }
  .dz-pw-yn .yn-btn{ flex:1 1 auto; min-width:0; padding:10px 12px; text-align:center; }

  .dz-pw-input .pw-input{ height:44px; }
}

/* ensure pills fill when active (your site styles, repeated for safety) */
.yn-btn.solid  { background:#0284c7; color:#fff; border:2px solid #0284c7; }
.yn-btn.outline{ background:#fff;    color:#0284c7; border:2px solid #0284c7; }


/* Force vertical centering of the Yes/No cell on desktop */
@media (min-width: 861px){
  /* center all three cells in the row */
  .dropzone .dz-item{
    align-items: center !important;
  }

  /* kill any leftover mobile label/padding that can push pills down/up */
  .dropzone .dz-pw-yn::before{
    content: none !important;
  }

  /* center the Y/N container itself */
  .dropzone .dz-pw-yn{
    align-self: center !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin: 0 !important;
    padding: 0 !important;
  }

  /* keep pills vertically centered inside */
  .dropzone .dz-pw-yn .yn-group{
    display: inline-flex !important;
    align-items: center !important;
    gap: 10px;
  }

  /* match input height */
  .dropzone .dz-pw-yn .yn-btn{ height:40px; padding:0 16px; }
  .dropzone .dz-pw-input      { align-self:center !important; }
  .dropzone .dz-pw-input .pw-input{ height:44px; }
}

/* ===========================
   Desktop column centering
   (keep mobile/tablet as-is)
   =========================== */
@media (min-width: 861px){

  /* keep the 3-col grid */
  .dropzone .dz-head{
    display:grid;
    grid-template-columns: minmax(0,1fr) 240px 260px;
    align-items:center;
  }
  .dropzone .dz-item{
    display:grid;
    grid-template-columns: minmax(0,1fr) 240px 260px;
    align-items:center;                 /* vertical middle */
  }

  /* headers: center the titles for cols 2 & 3 */
  .dropzone .dz-head > div:nth-child(2),
  .dropzone .dz-head > div:nth-child(3){
    text-align:center;
  }

  /* row content: center the cells for cols 2 & 3 */
  .dropzone .dz-pw-yn,
  .dropzone .dz-pw-input{
    justify-self:center;                /* horizontal center in their grid cell */
  }

  /* keep col 1 (file name) left-aligned */
  .dropzone .dz-left{ justify-self:start; }

  /* make pills & input same height for perfect alignment */
  .dropzone .dz-pw-yn .yn-group{ display:inline-flex; gap:10px; align-items:center; }
  .dropzone .dz-pw-yn .yn-btn{ height:44px; padding:0 16px; }
  .dropzone .dz-pw-input .pw-input{ height:44px; width:100%; }
}


</style>

<style>
  .pi-main[data-review-jump="1"] .tax-cta .continue-btn,
  .pi-main[data-review-jump="1"] .tax-cta [data-goto="prev"] { display:none !important; }
  .pi-main .tax-cta .review-back { display:none; }
  .pi-main[data-review-jump="1"] .tax-cta .review-back { display:inline-flex !important; }
</style>

<style>
  .rev-table{width:100%;border-collapse:collapse;margin:6px 0 4px;}
  .rev-table th,.rev-table td{padding:8px 10px;border:1px solid #e6eef6;text-align:left;vertical-align:top}
  .rev-table th{font-weight:700}
</style>

<style>
  /* List shell */
  .prop-list{list-style:none;margin:8px 0 0;padding:0;display:flex;flex-direction:column;gap:12px}

  /* Card */
  .prop-item{border:1px solid #e6eef6;border-radius:12px;padding:12px 14px}

  /* Header row */
  .prop-row{display:flex;flex-wrap:wrap;gap:10px 12px;align-items:center}
  .prop-title{font-weight:800;font-size:20px;letter-spacing:.2px;flex:1 1 auto;min-width:220px}

  /* Labeled “chips” */
  .prop-meta{display:flex;flex-wrap:wrap;gap:8px 10px}
  .kv{display:inline-flex;align-items:center;gap:6px;border:1px solid #dbe7f5;border-radius:999px;padding:6px 10px;line-height:1}
  .kv b{font-weight:600;color:#475569}

  /* “Expenses” as a link */
  .prop-toggle{margin-left:auto;appearance:none;background:none;border:0;padding:0;
               color:#0ea5e9;font-weight:800;text-decoration:none;cursor:pointer}
  .prop-toggle:hover{ text-decoration:underline; }

  /* Expand area */
  .prop-exp{margin-top:10px;border-top:1px dashed #e6eef6;padding-top:10px;display:none}
  .prop-exp table{width:100%;border-collapse:collapse}
  .prop-exp td{padding:6px 8px;vertical-align:top}

  /* Mobile: stack neatly */
  @media (max-width:680px){
    .prop-item{padding:12px}
    .prop-title{font-size:18px}
    .prop-row{gap:8px}
    .prop-meta{gap:6px}
    .kv{padding:6px 8px;font-size:.92rem}
    .prop-toggle{margin-left:0} /* sits under the chips on phones */
  }

  /* Very small phones: make the expenses grid breathe */
  @media (max-width:480px){
    .prop-exp td{padding:6px 4px}
  }
            
 <style>
/* Header row: title + Expenses link */
.prop-head{
  display:flex; align-items:center; justify-content:space-between;
  gap:12px; flex-wrap:wrap; margin-bottom:8px;
}
.prop-title{ font-weight:800; font-size:22px; text-transform:lowercase; }

/* “Expenses” as a link (not a button) */
.prop-toggle-link{
  font-weight:700; text-decoration:none; border-bottom:2px solid currentColor;
  line-height:1; padding:4px 0; color:#0ea5e9;
}
.prop-toggle-link:hover{ text-decoration:none; filter:brightness(1.1); }

/* Chip row with labels */
.prop-meta{
  display:flex; flex-wrap:wrap; gap:8px 10px; margin-bottom:10px;
}
.chip{
  display:inline-block; border:1px solid #dbe7f5; border-radius:999px;
  padding:6px 10px; white-space:nowrap; font-size:.95rem;
}
.chip b{ font-weight:800; margin-right:6px; }

/* Expenses area */
.prop-exp{ margin-top:8px; border-top:1px dashed #e6eef6; padding-top:8px; display:none; }
.prop-exp table{ width:100%; border-collapse:collapse; }
.prop-exp td{ padding:6px 8px; }

/* Mobile tweaks */
@media (max-width:680px){
  .prop-title{ font-size:18px; }
  .prop-meta{ gap:6px; }
  .chip{ font-size:.9rem; }
}
</style>


</style>


<style>

/* Already have: horizontal scroll */
.rev-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }

/* Base */


/* Let address wrap; keep others tight */
.rev-table td:nth-child(2){ white-space:normal; max-width:320px; }

/* Phone: hide lower-priority columns to reduce clutter */
@media (max-width:680px){
  /* Keep: Owner(1), Address(2), Start(3), Gross(8), Net(17) */
  .rev-table th:nth-child(4),
  .rev-table td:nth-child(4),  /* End */
  .rev-table th:nth-child(5),
  .rev-table td:nth-child(5),  /* Partner */
  .rev-table th:nth-child(6),
  .rev-table td:nth-child(6),  /* Ownership % */
  .rev-table th:nth-child(7),
  .rev-table td:nth-child(7),  /* Own Use % */
  .rev-table th:nth-child(9),
  .rev-table td:nth-child(9),  /* Mortgage */
  .rev-table th:nth-child(10),
  .rev-table td:nth-child(10), /* Insurance */
  .rev-table th:nth-child(11),
  .rev-table td:nth-child(11), /* Repairs */
  .rev-table th:nth-child(12),
  .rev-table td:nth-child(12), /* Utilities */
  .rev-table th:nth-child(13),
  .rev-table td:nth-child(13), /* Internet */
  .rev-table th:nth-child(14),
  .rev-table td:nth-child(14), /* Property Tax */
  .rev-table th:nth-child(15),
  .rev-table td:nth-child(15), /* Other */
  .rev-table th:nth-child(16),
  .rev-table td:nth-child(16)  /* Total Exp. */
  { display:none; }

  .rev-table th,.rev-table td{ padding:8px 6px; font-size:14px; }
  .rev-table td:nth-child(2){ max-width:220px; }
}


</style>  

<style>
  .rev-table{width:100%;border-collapse:collapse;margin:6px 0 4px;}
  .rev-table th,.rev-table td{padding:8px 10px;border:1px solid #e6eef6;text-align:left;vertical-align:top}
  .rev-table th{font-weight:700}
</style>

<style>

/* ===== Common-Law Modal (responsive) ===== */

/* Hidden by default via your markup */
.cl-modal[hidden] { display: none !important; }

/* Modal root: fixed overlay, centers dialog */
.cl-modal{
  position: fixed;
  inset: 0;
  z-index: 4000;                  /* above page UI */
  display: grid;
  place-items: center;
  padding: 16px;                  /* breathing room on small screens */
}

/* Backdrop */
.cl-modal .cl-backdrop{
  position: absolute;
  inset: 0;
  background: rgba(15, 23, 42, 0.55); /* slate-900 at ~55% */
  backdrop-filter: blur(2px);
}

/* Dialog panel */
.cl-modal .cl-dialog{
  position: relative;
  width: 100%;
  max-width: clamp(320px, 92vw, 560px); /* mobile-friendly, comfy on desktop */
  background: #ffffff;
  color: #0f172a;                        /* slate-900 */
  border-radius: 14px;
  box-shadow:
    0 20px 55px rgba(2, 6, 23, 0.25),
    0 6px 16px rgba(2, 6, 23, 0.18);
  border: 1px solid #e5e7eb;             /* subtle border */
  padding: 18px 18px 14px;
  overflow: hidden;

  /* Appear animation */
  opacity: 0;
  transform: translateY(8px) scale(0.98);
  transition: opacity .18s ease, transform .18s ease;
}

/* When the modal is present (not [hidden]), fade/slide in */
.cl-modal:not([hidden]) .cl-dialog{
  opacity: 1;
  transform: translateY(0) scale(1);
}

/* Reduce motion preference */
@media (prefers-reduced-motion: reduce){
  .cl-modal .cl-dialog{
    transition: none;
    transform: none;
  }
}

/* Title & body */
#clTitle{
  margin: 0 0 8px;
  font-size: clamp(18px, 2.1vw, 22px);
  line-height: 1.25;
  font-weight: 700;
  color: #0b1324;
}

#clBody{
  margin: 0 0 14px;
  font-size: clamp(14px, 1.8vw, 16px);
  line-height: 1.55;
  color: #334155; /* slate-600 */
}

#clBody strong{ color: #0284c7; font-weight: 700; }

/* Actions row */
.cl-actions{
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 6px;
}

/* If your .tax-btn already exists, this just nudges spacing; otherwise basic style */
.cl-actions .tax-btn{
  appearance: none;
  border: 1px solid #0284c7;
  background: #0284c7;
  color: #fff;
  font-weight: 600;
  border-radius: 10px;
  padding: 10px 14px;
  cursor: pointer;
  line-height: 1;
}
.cl-actions .tax-btn:hover{ filter: brightness(0.95); }
.cl-actions .tax-btn:focus-visible{
  outline: 2px solid rgba(11,100,194,.35);
  outline-offset: 2px;
}

/* === Tablet (<= 960px) tweaks === */
@media (max-width: 960px){
  .cl-modal{ padding: 14px; }
  .cl-modal .cl-dialog{
    max-width: min(92vw, 520px);
    border-radius: 12px;
    padding: 16px 16px 12px;
  }
}

/* === Small mobile (<= 420px) tweaks: comfy reading & edges === */
@media (max-width: 420px){
  .cl-modal{ padding: 10px; }
  .cl-modal .cl-dialog{
    max-width: 100%;
    border-radius: 12px;
    padding: 14px 14px 12px;
  }
  .cl-actions{ gap: 8px; }
  .cl-actions .tax-btn{ padding: 10px 12px; }
}

/* Optional dark-mode (if your site supports it) */
@media (prefers-color-scheme: dark){
  .cl-modal .cl-backdrop{ background: rgba(2, 6, 23, 0.6); }
  .cl-modal .cl-dialog{
    background: #0b1324;          /* dark panel */
    color: #e5e7eb;
    border-color: #1f2937;
    box-shadow:
      0 20px 55px rgba(0,0,0,0.5),
      0 6px 16px rgba(0,0,0,0.35);
  }
  #clTitle{ color: #f1f5f9; }
  #clBody{ color: #cbd5e1; }
}

</style>


<style>

select.fi-input option:disabled {
  color: #9ca3af; /* gray */
  background: #f9fafb;
}
</style>


<style>
/* === BMO-style selection error banner === */
.qs-error-banner{
  /* layout */
  display:none;                 /* shown via .show */
  position:relative;
  box-sizing:border-box;
  padding:16px 18px 16px 58px;  /* room for icon */
  margin:16px 0 18px;           /* add top air so it doesn’t hug titles */
  text-align: start;

  /* look */
  --err-bg:#FDEAEA;           /* soft pink */
  --err-border:#F2C4C4;       /* pale red border */
  --err-title:#8A1F1F;        /* deep red title */
  --err-text:#8A1F1F;         /* body text */
  --err-icon:#D93025;         /* red icon circle */

  background:var(--err-bg);
  border-bottom:3px solid var(--err-border);
  border-radius:0;          /* slight rounding like screenshot */
  color:var(--err-text);
}

/* show/hide toggle */
.qs-error-banner.show{ display:block; }

/* red circular “!” icon on the left */
.qs-error-banner::before{
  content:"";
  position:absolute;
  left:18px; top:50%;
  transform:translateY(-50%);
  width:26px; height:26px;
  border-radius:50%;
  background:var(--err-icon);
}
.qs-error-banner::after{
  content:"!";
  position:absolute;
  left:18px; top:50%;
  transform:translateY(-50%);
  width:26px; height:26px;
  display:flex; align-items:center; justify-content:center;
  font-weight:800; font-size:16px; line-height:1;
  color:#fff;
}

/* heading + subtext */
.qs-error-banner h3{
  margin:0 0 6px;
  font-size:16px;
  font-weight:800;
  color:var(--err-title);
}
.qs-error-banner p{
  margin:0;
  font-size:14px;
  line-height:1.35;
  color:var(--err-text);
}

/* small screens: tighten slightly */
@media (max-width: 520px){
  .qs-error-banner{ padding:14px 14px 14px 54px; }
  .qs-error-banner::before,
  .qs-error-banner::after{ left:14px; width:24px; height:24px; }
}


@media (max-width: 959px){
  .form-button#intro-form{
    display:block !important;   /* avoid flex squeezing */
    text-align: initial;        /* stop centering from parent */
  }
  #intro-form { width: 100%; }  /* ensure the form can expand */
  #intro-continue{
    display: block !important;   /* override inline/inline-flex */
    width: 100% !important;      /* fill the row */
    max-width: none !important;  /* kill any max-width caps */
    box-sizing: border-box;      /* include padding/border */
    flex: 1 1 auto !important;   /* if inside a flex container */
    align-self: stretch !important;
    min-width: 0 !important;     /* beats any preset min-width */
  }
}

/* Mobile: make ONLY the intro CONTINUE button full width */
@media (max-width: 959px){
  /* 1) Let the form occupy the whole row even in a flex parent */
  #intro-card #intro-form{
    display: block !important;
    width: 100% !important;
    flex: 0 0 100% !important;   /* if parent is flex */
    align-self: stretch !important;
    margin: 0 !important;
  }

  /* 2) Make the button fill the form */
  #intro-card #intro-form #intro-continue{
    display: block !important;
    width: 100% !important;
    max-width: none !important;
    box-sizing: border-box !important;
    flex: 0 0 100% !important;   /* if the button itself is flex item */
    min-width: 0 !important;
  }
}
@media (max-width: 959px){
  #intro-card .tax-cta{ display:block !important; }   /* stops right-aligned flex from shrinking the form */
}
.is-invalid .yn-btn{ /* outline/button radios */ border-color:#d93025 !important; } 
.fi-error-text{ 
  margin-top:6px;
  font-size:12px;
  line-height:1.3;
  color:#a11a12;
  text-align:left;
  display:block;
  width:100%;
}
.qs-block .fi-error-text, .yn-group .fi-error-text, .xsel-wrap .fi-error-text{
  margin-left:2px;
}
/* Ensure error text appears below fi-group fields */
.fi-group .fi-error-text {
  margin-top: 6px;
  margin-left: 0;
  width: 100%;
  display: block;
}



.children-table.is-invalid-table{
  border-bottom: 2px solid #d93025;
}


</style>

<style>
/* mobile-friendly rent table */
@media (max-width:640px){
  .rent-table thead{ display:none; }
  .rent-table, .rent-table tbody, .rent-table tr, .rent-table td{ display:block; width:100%; }
  .rent-table tr{ background:#fff; border:1px solid #e5e7eb; border-radius:12px; padding:10px 12px; margin:10px 0; box-shadow:0 1px 4px rgba(0,0,0,.04); }
  .rent-table td{ display:flex; justify-content:space-between; align-items:center; padding:6px 0; font-size:14px; }
  .rent-table td:nth-child(1)::before{ content:"Rent Address"; font-weight:600; color:#334155; margin-right:12px; font-size:12px; }
  .rent-table td:nth-child(2)::before{ content:"From";         font-weight:600; color:#334155; margin-right:12px; font-size:12px; }
  .rent-table td:nth-child(3)::before{ content:"To";           font-weight:600; color:#334155; margin-right:12px; font-size:12px; }
  .rent-table td:nth-child(4)::before{ content:"Total Rent";   font-weight:600; color:#334155; margin-right:12px; font-size:12px; }
  .rent-table td:nth-child(5)::before{ content:"Actions";      font-weight:600; color:#ffffff; margin-right:12px; font-size:12px; }
  .rent-table td:last-child{ justify-content:flex-end; gap:8px; }
  .rent-table td:last-child .tax-btn, .rent-table td:last-child .tax-btn-secondary{ padding:6px 10px; font-size:12px; border-radius:999px; }
}

.rent-table.is-invalid-table {
  border-bottom: 2px solid #d93025;
}
</style>

<style>
  .link-btn {
    background: none; border: 0; padding: 0;
    color: #0a6db6; text-decoration: none; cursor: pointer;
    font: inherit; font-weight: 600;
  }
  .link-btn.danger { color: #b60a2c; }
  .link-btn:focus { outline: 2px solid #0a6db6; outline-offset: 2px; }
  
  
  /* --- Rent table: keep Month + Year on a single line --- */
.fi-table.rent-table td:nth-child(2),
.fi-table.rent-table td:nth-child(3) {
  white-space: nowrap;                 /* don't wrap within the cell */
}

/* Some frameworks force selects to 100% width; override just in the rent table */
.fi-table.rent-table td select,
.fi-table.rent-table td .xsel-native {
  display: inline-block !important;
  width: 6.5rem !important;            /* adjust as needed (≈104px) */
  max-width: 6.5rem !important;
  vertical-align: middle;
}

/* space between Month and Year */
.fi-table.rent-table td select + select,
.fi-table.rent-table td .xsel-native + .xsel-native {
  margin-left: 8px;
}

/* If your selects are wrapped in .fi-group, make those inline too */
.fi-table.rent-table td .fi-group {
  display: inline-block;
  width: auto;
  margin: 0;
}
.fi-table.rent-table td .fi-group + .fi-group { margin-left: 8px; }
.fi-table.rent-table td .fi-group .fi-input,
.fi-table.rent-table td .fi-group .xsel-native { width: auto !important; }

/* Make the "Total Rent Paid" input narrower so the row breathes */
.fi-table.rent-table td:nth-child(4) input[type="text"],
.fi-table.rent-table td:nth-child(4) input[type="number"] {
  max-width: 8rem;                     /* ≈128px; tweak to taste */
}

@media (max-width: 640px) {
  .fi-table.rent-table thead th:nth-child(5),
  .fi-table.rent-table thead th:last-child {
    color: #ffffff !important;
  }
} 


.fi-group.fi-suf { position: relative; }
.fi-group.fi-suf .fi-input.with-suffix { padding-right: 2.25rem; }
.fi-group.fi-suf .fi-suffix {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  font-weight: 600;
  opacity: .8;
  pointer-events: none;
}

/* ---------- Rent table look (like your WI table + screenshot #3) ---------- */
.fi-table.rent-table{
  border-collapse: separate;
  border-spacing: 0;
  width: 100%;
}
.fi-table.rent-table thead th{
  background:#f9fafb;
  font-weight:600;
  padding:12px 16px;
  text-align:left;
  border-bottom:2px solid #282c34;  /* bold header rule */
  white-space:nowrap;
}
.fi-table.rent-table tbody td{
  padding:12px 16px;
  border-bottom:1px solid #eef0f3;
  vertical-align:middle;
}
.fi-table.rent-table tbody tr:nth-child(even) td{ background:#fcfdff; }  /* zebra */
.fi-table.rent-table tbody tr:hover td{ background:#f7fbff; }

/* keep From/To selects compact on one line */
.fi-table.rent-table td:nth-child(2),
.fi-table.rent-table td:nth-child(3){ white-space:nowrap; }
.fi-table.rent-table td select,
.fi-table.rent-table td .xsel-native{
  display:inline-block !important;
  width:6.5rem !important;
  max-width:6.5rem !important;
  vertical-align:middle;
}
.fi-table.rent-table td select + select,
.fi-table.rent-table td .xsel-native + .xsel-native{ margin-left:8px; }

/* Total Rent Paid input: $ prefix, compact */
.fi-table.rent-table td:nth-child(4) .fi-group{ position:relative; display:inline-block; }
.fi-table.rent-table td:nth-child(4) .fi-group::before{
  content:"$";
  position:absolute; left:10px; top:50%; transform:translateY(-50%);
  color:#6b7280; font-weight:600; pointer-events:none;
}
.fi-table.rent-table td:nth-child(4) input[type="text"],
.fi-table.rent-table td:nth-child(4) input[type="number"],
.fi-table.rent-table td:nth-child(4) .fi-input{
  height:38px;
  max-width:10rem;                 /* ~160px */
  padding:8px 10px 8px 26px;       /* room for $ */
  border:1px solid #d1d5db; border-radius:8px;
  background:#fff; font-size:15px; color:#111827;
  outline:none; box-sizing:border-box;
}
.fi-table.rent-table td:nth-child(4) .fi-input:focus{
  border-color:#0284c7; box-shadow:0 0 0 2px rgba(11,100,194,.16);
}

/* Actions with icons (Edit pencil, Delete bin) */
.rent-table td:last-child{ text-align:center; white-space:nowrap; }
.link-btn{
  background:none; border:0; padding:0; cursor:pointer;
  font:inherit; font-weight:600; text-decoration:none;
  color:#0284c7; display:inline-flex; align-items:center; gap:3px; margin-right: 10px;
}
.link-btn.danger{ color:#c0262d; }

.link-btn.edit::before,
.link-btn.delete::before{
  content:""; width:16px; height:16px; display:inline-block; background-repeat:no-repeat; background-size:contain;
}
.link-btn.edit::before{
  /* pencil icon */
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%230b64c2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><path d='M12 20h9'/><path d='M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z'/></svg>");
}
.link-btn.delete::before{
  /* trash icon */
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23c0262d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><polyline points='3 6 5 6 21 6'/><path d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/><path d='M10 11v6'/><path d='M14 11v6'/><path d='M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2'/></svg>");
}

/* “Add Address” pill with plus icon */
#rent-add-wrap-top .tax-btn,
#rent-add-wrap-bottom .tax-btn{
  position:relative;
  background:#fff; color:#0284c7; border: none;
  padding:10px 0 10px 10px; line-height:1;
}
#rent-add-wrap-top .tax-btn::after,
#rent-add-wrap-bottom .tax-btn::after{
  content:""; position:absolute; right:10px; top:50%; transform:translateY(-50%);
  width:22px; height:22px; background-repeat:no-repeat; background-size:contain;
  /* blue plus-in-circle */
  background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='22' height='22' viewBox='0 0 24 24' fill='%230284c7'><circle cx='12' cy='12' r='10'/><path d='M12 8v8M8 12h8' stroke='%23fff' stroke-width='2' stroke-linecap='round'/></svg>");
}

/* Empty row style */
#rent-empty-row td{ padding:14px 16px; color:#6b7280; }

/* ---------- Mobile cards for rent table ---------- */
@media (max-width:640px){
  .rent-table thead{ display:none; }
  .rent-table, .rent-table tbody, .rent-table tr, .rent-table td{ display:block; width:100%; }
  .rent-table tr{
    background:#fff; border:1px solid #e5e7eb; border-radius:12px;
    padding:12px 14px; margin:10px 0; box-shadow:0 1px 4px rgba(0,0,0,.04);
  }
  .rent-table td{ padding:6px 0; font-size:14px; }
  .rent-table td:nth-child(1)::before{ content:"Rent Address"; font-weight:600; color:#334155; display:block; margin-bottom:4px; font-size:12px; }
  .rent-table td:nth-child(2)::before{ content:"From"; font-weight:600; color:#334155; display:block; margin-bottom:4px; font-size:12px; }
  .rent-table td:nth-child(3)::before{ content:"To"; font-weight:600; color:#334155; display:block; margin-bottom:4px; font-size:12px; }
  .rent-table td:nth-child(4)::before{ content:"Total Rent Paid"; font-weight:600; color:#334155; display:block; margin-bottom:4px; font-size:12px; }
  .rent-table td:last-child{ display:flex; justify-content:end; gap:10px; padding-top:8px; }
  .link-btn{ font-size:13px; border: none;}
}
       

/* Header look + green sort arrows (all headers except the last 'Actions') */
.fi-table.rent-table thead th{
  position:relative;
  background:#f9fafb;
  font-weight:600;
  padding:12px 16px;
  text-align:left;
  border-bottom:2px solid #282c34;
  white-space:nowrap;
}
.fi-table.rent-table thead th:not(:last-child)::after{
  content:"";
  position:absolute; right:8px; top:50%; transform:translateY(-50%);
  width:14px; height:14px; background-repeat:no-repeat; background-size:contain;
  /* double arrow (up/down) in green */
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2309A34A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <polyline points='7 15 12 20 17 15'/>\
  <polyline points='7 9 12 4 17 9'/>\
  </svg>");
}

/* Zebra & hover (kept) */
.fi-table.rent-table tbody tr:nth-child(even) td{ background:#fcfdff; }
.fi-table.rent-table tbody tr:hover td{ background:#f7fbff; }

/* Keep Month + Year inline & compact selects */
.fi-table.rent-table td:nth-child(2),
.fi-table.rent-table td:nth-child(3){ white-space:nowrap; }
.fi-table.rent-table td select,
.fi-table.rent-table td .xsel-native{
  display:inline-block !important; width:6.5rem !important; max-width:6.5rem !important; vertical-align:middle;
}
.fi-table.rent-table td select + select,
.fi-table.rent-table td .xsel-native + .xsel-native{ margin-left:8px; }

/* Total ($) input even in view mode */
.fi-table.rent-table td:nth-child(4) .fi-group{ position:relative; display:inline-block; }
.fi-table.rent-table td:nth-child(4) .fi-group::before{
  content:"$"; position:absolute; left:10px; top:50%; transform:translateY(-50%); color:#6b7280; font-weight:600; pointer-events:none;
}
.fi-table.rent-table td:nth-child(4) .fi-input{
  height:38px; max-width:10rem; padding:8px 10px 8px 26px;
  border:1px solid #d1d5db; border-radius:8px; background:#fff; font-size:15px; color:#111827;
}
.fi-table.rent-table td:nth-child(4) .fi-input:focus{
  border-color:#0284c7; box-shadow:0 0 0 2px rgba(11,100,194,.16);
}

/* Actions with icons (links match your ref) */
.link-btn{
  background:none; border:0; padding:0; cursor:pointer;
  font:inherit; font-weight:600; text-decoration:none;
  color:#068ac1; display:inline-flex; align-items:center; gap:6px;
}
.link-btn.danger{ color:#c0262d; }

/* remove old pseudo arrows on <th> if any */
.fi-table.rent-table thead th::after{ content:none !important; }

/* clickable sort buttons inside headers */
.rent-sort{
  display:inline-flex; align-items:center; gap:8px;   /* ← space after words */
  background:none; border:0; padding:0; font:inherit; color:inherit;
  cursor:pointer;
}
.rent-sort .sort-ico{
  width:14px; height:14px; background-repeat:no-repeat; background-size:contain;
  /* default: double arrow */
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2309A34A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <polyline points='7 15 12 20 17 15'/><polyline points='7 9 12 4 17 9'/>\
  </svg>");
}
.rent-sort.asc  .sort-ico{
  /* up arrow */
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2309A34A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <polyline points='7 9 12 4 17 9'/><line x1='12' y1='4' x2='12' y2='20'/>\
  </svg>");
}
.rent-sort.desc .sort-ico{
  /* down arrow */
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2309A34A' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <polyline points='7 15 12 20 17 15'/><line x1='12' y1='4' x2='12' y2='20'/>\
  </svg>");
}

/* actions: word first, icon after; both BLUE (same color) */
.link-btn{ color:#068ac1; }
.link-btn.danger{ color:#068ac1; }

.link-btn.rent-edit::after,
.link-btn.edit::after{
  content:""; width:16px; height:16px; margin-left:3px;
  background-size:contain; background-repeat:no-repeat; display:inline-block;
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23068ac1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <path d='M12 20h9'/><path d='M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z'/>\
  </svg>");
}
.link-btn.rent-del::after,
.link-btn.delete::after{
  content:""; width:16px; height:16px; margin-left:3px;
  background-size:contain; background-repeat:no-repeat; display:inline-block;
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%23068ac1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'>\
  <polyline points='3 6 5 6 21 6'/><path d='M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6'/>\
  <path d='M10 11v6'/><path d='M14 11v6'/><path d='M9 6V4a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2'/>\
  </svg>");
}

/* === Mobile cards: show labels only for real data rows === */
@media (max-width:640px){
  /* Your current mobile transform stays; these just refine labels */
  .rent-table tr.rent-empty-row td::before{ content:none !important; display:none !important; }

  /* Scope the mobile labels to data rows only */
  .rent-table tr.data-row td:nth-child(1)::before{ content:"Rent Address"; }
  .rent-table tr.data-row td:nth-child(2)::before{ content:"From"; }
  .rent-table tr.data-row td:nth-child(3)::before{ content:"To"; }
  .rent-table tr.data-row td:nth-child(4)::before{ content:"Total Rent Paid"; }
  .rent-table tr.data-row td:nth-child(5)::before{ content:"Actions"; color:#ffffff; } /* keep hidden look */
}

/* Add Address — text + icon (no overlap) */
#rent-add-btn,
#rent-add-btn-bottom{
  display:inline-flex !important;
  align-items:center;
  gap:.5rem;                   /* space between text and icon */
  white-space:nowrap;
  background:#fff;
  color:#0b64c2;
  border:2px solid #0b64c2;
  border-radius:999px;
  font-weight:700;
  line-height:1;
  padding:8px 14px;            /* compact */
  box-sizing:border-box;
}

/* Put the plus icon AFTER the text (inline, not absolute) */
#rent-add-btn::after,
#rent-add-btn-bottom::after{
  content:"";
  display:inline-block !important;
  width:20px; height:20px;
  background-repeat:no-repeat; background-size:contain;
  margin-left:.25rem;
  position:static !important;  /* override any absolute from older CSS */
  right:auto !important; top:auto !important; transform:none !important;
  background-image:url("data:image/svg+xml;utf8,\
  <svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='%230284c7'>\
  <circle cx='12' cy='12' r='10'/><path d='M12 8v8M8 12h8' stroke='%23fff' stroke-width='2' stroke-linecap='round'/>\
  </svg>");
}

/* Narrow phones: keep it readable without overlap */
@media (max-width:400px){
  #rent-add-btn, #rent-add-btn-bottom{ gap:.4rem; padding:8px 12px; }
}
      
</style>

<style>
/* ===============================
   World Income — fixed 2 cols (left), responsive, clean placeholders
   =============================== */
.wi-grid{
  display: grid;                     /* keep it a grid! */
  grid-template-columns: 300px 300px;/* Period | Income */
  width: 600px;                      /* exact width (no excess) */
  max-width: 100%;                   /* don't overflow small viewports */
  margin: 0 0 1rem 0;                /* left-aligned block */
  border-bottom: 1px solid #e5e7eb;  /* like your screenshot */
  background: #fff;
  overflow: hidden;
}

/* columns */
.wi-col{ display:flex; flex-direction:column; }
.wi-col--period{ border-right:1px solid #e5e7eb; }

/* header */
.wi-title{
  height:56px; display:flex; align-items:center;
  padding:0 18px;
  font-weight:600; font-size:15px; color:#0f172a;
  background:#f9fafb;
  border-bottom:2px solid #282c34;
}

/* rows */
.wi-row{
  height:60px; display:flex; align-items:center;
  padding:0 18px;
  border-bottom:1px solid #f3f4f6;
}
.wi-col .wi-row:last-child{ border-bottom:none; }

/* zebra */
.wi-col--period .wi-row:nth-child(even),
.wi-col--income .wi-row:nth-child(even){ background:#fcfdff; }

/* inputs */
.wi-inline{ position:relative; width:100%; margin:0; }
.wi-inline::before{
  content:"$";
  position:absolute; left:12px; top:50%; transform:translateY(-50%);
  color:#6b7280; font-weight:600; pointer-events:none;
}
.wi-inline .fi-input{
  width:100%;
  height:44px;
  padding:10px 12px 10px 28px; /* space for $ */
  border:1px solid #d1d5db; border-radius:8px;
  background:#fff; font-size:16px; color:#111827;
  outline:none; box-sizing:border-box;
  transition:border-color .15s, box-shadow .15s;
}
.wi-inline .fi-input:focus{
  border-color: #0284c7;
  box-shadow:0 0 0 3px rgba(11,100,194,.15);
}

/* remove float label like "Year 1 Income" */
.wi-grid .fi-float-label{ display:none !important; }

/* placeholders:
   - visible on mobile
   - hidden on tablet/desktop
*/
.wi-grid .fi-input::placeholder{ color:#9aa3af; }           /* mobile default */
/* Hide placeholders everywhere */
.wi-grid .fi-input::placeholder,
.wi-stack .fi-input::placeholder{ color: transparent !important; }

/* Remove the float label text inside these inputs */
.wi-grid .fi-float-label{ display:none !important; }

/* ---- Mobile card layout ---- */
@media (max-width: 680px){
  /* the JS already hides .wi-grid and shows .wi-stack on mobile */
  .wi-stack{
    display:grid;
    gap:12px;
    width:100%;
  }
  .wi-card{
    border:1px solid #e5e7eb;
    border-radius:12px;
    background:#fff;
    padding:12px 14px;
  }
  .wi-line{                       /* "Period: [dates]" line */
    font-size:14px;
    color:#0f172a;
    margin-bottom:8px;
  }
  .wi-line .wi-k{ font-weight:700; margin-right:6px; }   /* "Period:" */
  .wi-line .wi-v{ font-weight:500; }

  .wi-mobile-label{              /* "World Income (CAD)" label */
    display:block;
    font-size:12px;
    text-transform:uppercase;
    letter-spacing:.04em;
    color:#64748b;
    margin:6px 0 8px;
  }

  /* input inside card */
  .wi-card .wi-inline{ position:relative; width:100%; margin:0; }
  .wi-card .wi-inline::before{
    content:"$";
    position:absolute; left:12px; top:50%; transform:translateY(-50%);
    color:#6b7280; font-weight:600; pointer-events:none;
  }
  .wi-card .wi-inline .fi-input{
    width:100%;
    height:44px;
    padding:10px 12px 10px 28px;  /* space for $ */
    border:1px solid #d1d5db; border-radius:8px;
    background:#fff; font-size:16px; color:#111827;
    outline:none; box-sizing:border-box;
  }
  .wi-card .wi-inline .fi-input:focus{
    border-color:#0284c7;
    box-shadow:0 0 0 3px rgba(11,100,194,.15);
  }
}


@media (max-width: 959px) {
  /* your exact button */
  #intro-continue,
  .form-button .continue-btn {
    display: block !important;
    width: 100% !important;
    max-width: none !important;
    box-sizing: border-box !important;
    flex: 0 0 100% !important;
    align-self: stretch !important;
  }
}


/* base look for yes/no buttons */
.yn-group input {
  display: none;  /* hide the real radio */
}

.yn-group .yn-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: #fff;               /* neutral */
  color: #0b7ec3;
  cursor: pointer;
}

/* when radio is checked → make it solid blue */
.yn-group input:checked + .yn-btn {
  background: #0b7ec3;
  color: #fff;
  border-color: #0b7ec3;
}

/* override for button-based yes/no pills (e.g., T4/T4A/T Slips password) */
.yn-group .yn-btn.solid {
  background:#0284c7;
  color:#fff;
  border:2px solid #0284c7;
}
.yn-group .yn-btn.outline {
  background:#fff;
  color:#0284c7;
  border:2px solid #0284c7;
}

/* Review panel: make "Your Message For Us?" textarea a thin outlined box */
#other_message.fi-input{
  border:1px solid #cbd5e1;
  border-radius:8px;
  padding:10px 12px;
  min-height:120px;
  box-shadow:none;
}
#other_message.fi-input:focus{
  border-color:#0b66c3;
  box-shadow:0 0 0 1px rgba(11,102,195,.15);
}


</style>


</head>
    <body>

    <?php include_once 'headers2.php'; ?>

    
<?php
$greetName = isset($rowUser['first_name']) && $rowUser['first_name'] !== ''
  ? htmlspecialchars($rowUser['first_name'])
  : 'Hi there'; // fallback if empty
?>

<div class="tax-wrap">
  <?php
    // --- controls ---
    $isExistingCustomer = $isExistingCustomer ?? true;     // set true/false from your app
    $useShortGreeting   = $useShortGreeting   ?? false;    // set true to show "Hi" instead of "Good morning"
    $name               = htmlspecialchars($greetName ?? 'Hi there');

    $h = (int) date('G');
    $dayGreeting = ($h < 12) ? 'Good morning' : (($h < 18) ? 'Good afternoon' : 'Good evening');
    $greeting    = $useShortGreeting ? 'Hi' : $dayGreeting;
  ?>

  <div class="intro-layout">
    <section class="tax-card" id="intro-card" role="region" aria-labelledby="welcome-title">
      <h1 id="welcome-title" class="tax-title"><?= $greeting; ?>, <?= $name; ?>!</h1>

    <p class="tax-sub">
      <?php if ($isExistingCustomer): ?>
        Glad to have you back! Applying taxes online is easy. Here is what you need:
      <?php else: ?>
        Welcome to Paragon Accounting &amp; Financial Services. Applying taxes online has always been easy.
      <?php endif; ?>
    </p>

    <ul class="tax-list" aria-label="Requirements">
      <!-- 1 person -->
      <li class="tax-item">
        <span class="tax-ico" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="12" cy="8" r="4" stroke="#0b66c3" stroke-width="2"/>
            <path d="M4 20c1.8-3.5 5-5 8-5s6.2 1.5 8 5" stroke="#0b66c3" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>
        <div>
          <h3>Your personal and tax-filing details</h3>
          <p class="tax-muted">We’ll use these to prepare your return accurately.</p>
        </div>
      </li>

      <!-- 2 persons -->
      <li class="tax-item">
        <span class="tax-ico" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <circle cx="9"  cy="8" r="3.5" stroke="#0b66c3" stroke-width="2"/>
            <circle cx="16" cy="9.5" r="3"   stroke="#0b66c3" stroke-width="2"/>
            <path d="M3 20c1.4-3 4-4.3 6.9-4.3S15.4 17 16.8 20" stroke="#0b66c3" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>
        <div>
          <h3>If applicable, your spouse/partner’s details</h3>
          <p class="tax-muted">Personal and tax-filing details for accurate family benefits.</p>
        </div>
      </li>

      <!-- documents + expandable (BMO-style) -->
      <li class="tax-item">
        <span class="tax-ico" aria-hidden="true">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
            <path d="M7 3h7l4 4v14H7V3Z" stroke="#0b66c3" stroke-width="2"/>
            <path d="M14 3v5h5" stroke="#0b66c3" stroke-width="2"/>
          </svg>
        </span>

        <div class="doc-box" role="region" aria-labelledby="docs-head">
          <h3 id="docs-head" class="doc-title">Get your documents ready</h3>
          <p class="doc-sub">
            Keep your documents handy (pdf, txt, jpeg, tiff, png, excel, word). We’ll ask you to upload them later before submitting your request.
          </p>

          <!-- BMO-style link toggle -->
          <button class="doc-link" type="button" aria-expanded="false" aria-controls="docs-panel">
            Documents for Tax Filing
            <svg class="doc-chev" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
              <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <!-- Collapsible panel -->
          <div id="docs-panel" class="doc-panel" hidden>
            <ul class="doc-filelist">
              <li>
                <span class="file-ico" aria-hidden="true">
                  <svg viewBox="0 0 24 24" width="20" height="22" fill="none">
                    <path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/>
                  </svg>
                </span>
                <span>ID proof <span class="badge badge-required">Mandatory</span></span>
              </li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>T4 / T4A</span></li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>T2202</span></li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>RRSP / FHSA / Investment receipts <em>(if any)</em></span></li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>Work from home / Employment expenses <strong>(T2200)</strong></span></li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>Annual Tax Summary <em>(from Other Income page)</em></span></li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>Summary of expenses related to employment expenses</span></li>
              <li><span class="file-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="20" height="22" fill="none"><path d="M7 3h7l4 4v14H7V3Z" stroke="#64758b" stroke-width="2"/><path d="M14 3v5h5" stroke="#64758b" stroke-width="2"/></svg>
              </span><span>Additional documents</span></li>
            </ul>

            <ul class="doc-notes">
              <li>Use the latest statements/receipts where possible.</li>
              <li>Ensure names and account/reference numbers are visible.</li>
              <li>If you’re unsure, upload it — we’ll advise during review.</li>
            </ul>

            <div class="doc-close">
              <button class="doc-closebtn" type="button" aria-controls="docs-panel">
                CLOSE
                <svg class="doc-chevup" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true">
                  <path d="M6 15l6-6 6 6" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </li>
    </ul>

    <div class="tax-legal">
      Your privacy is important to us. To learn more about how we collect, use and safeguard your personal information, your choices,
      and the rights you have, view our <a href="#">Privacy Code</a>.
    </div>

    <div class="tax-cta tax-cta-row">
      <form class="form-button" id="intro-form">
        <input type="hidden" name="name" value="<?= $name; ?>">
        <button class="continue-btn this-btn" type="button" id="intro-continue">CONTINUE</button>
      </form>
    </div>
  </section>

  <!-- Old Form Sidebar -->
  <aside class="old-form-sidebar">
    <div class="old-form-sidebar-content">
      <p class="old-form-sidebar-title">You are using our new form</p>
      <p class="old-form-sidebar-text">You can opt to use our old form here:</p>
      <a href="/form" class="old-form-btn">Use old Form</a>
    </div>
  </aside>
  </div>
</div>



<!-- SECOND PAGE -->
<!-- WELCOME PANEL -->
<div id="welcome-panel" class="tax-next" style="display:none;">
  <section class="tax-card" role="region" aria-labelledby="qs-title">
    <div class="qs-wrap" >


      <h2 id="qs-title" class="qs-title">
        <?php echo $greetName; ?>, before we begin, we need to ask you a few questions.
      </h2>

<!-- Global error banner (hidden by default) -->
<div id="qsError" class="qs-error-banner" role="alert" aria-live="polite" aria-atomic="true">
  <h3>A selection is required.</h3>
  <p>To proceed, please fill in or correct the required field(s).</p>
</div>
      <!-- MARITAL STATUS -->
      <div class="qs-block">
        <label class="qs-label">What is your Marital Status?</label>

        <!-- Dropdown UI (kept) -->
        <div class="xsel-wrap">
          <select id="marital_status_select" name="marital_status"
                  class="xsel-native" aria-label="Marital Status"
                  data-placeholder="Select status">
            <?php $ms = $rowUser['marital_status'] ?? ''; ?>
            <option value="" disabled <?= ($ms === '' ? 'selected' : '') ?>>Select status</option>
            <option value="Single"     <?= ($ms === 'Single' ? 'selected' : '') ?>>Single</option>
            <option value="Married"    <?= ($ms === 'Married' ? 'selected' : '') ?>>Married</option>
            <option value="Common Law" <?= ($ms === 'Common Law' ? 'selected' : '') ?>>Common Law</option>
            <option value="Separated"  <?= ($ms === 'Separated' ? 'selected' : '') ?>>Separated</option>
            <option value="Divorced"   <?= ($ms === 'Divorced' ? 'selected' : '') ?>>Divorced</option>
            <option value="Widowed"    <?= ($ms === 'Widowed' ? 'selected' : '') ?>>Widowed</option>
          </select>
        </div>

        <!-- Keep your radios in DOM (IDs unchanged) -->
        <div class="qs-choicegrid">
          <label><input type="radio" name="marital_status" value="Single"     id="ms_single"    <?= (($ms ?? '') === 'Single' ? 'checked' : '') ?>><span>Single</span></label>
          <label><input type="radio" name="marital_status" value="Married"    id="ms_married"   <?= (($ms ?? '') === 'Married' ? 'checked' : '') ?>><span>Married</span></label>
          <label><input type="radio" name="marital_status" value="Common Law" id="ms_commonlaw" <?= (($ms ?? '') === 'Common Law' ? 'checked' : '') ?>><span>Common Law</span></label>
          <label><input type="radio" name="marital_status" value="Separated"  id="ms_separated" <?= (($ms ?? '') === 'Separated' ? 'checked' : '') ?>><span>Separated</span></label>
          <label><input type="radio" name="marital_status" value="Divorced"   id="ms_divorced"  <?= (($ms ?? '') === 'Divorced' ? 'checked' : '') ?>><span>Divorced</span></label>
          <label><input type="radio" name="marital_status" value="Widowed"    id="ms_widowed"   <?= (($ms ?? '') === 'Widowed' ? 'checked' : '') ?>><span>Widowed</span></label>
        </div>
      </div>

      <!-- Common-Law modal (kept) -->
      <div class="cl-modal" id="commonlawModal" role="dialog" aria-modal="true"
          aria-labelledby="clTitle" aria-describedby="clBody" hidden>
        <div class="cl-backdrop" data-close></div>
        <div class="cl-dialog" role="document">
          <h3 id="clTitle">Date of common-law status</h3>
          <p id="clBody">
            <strong>Example:</strong> If you moved in together on <strong>August 15, 2022</strong>,
            then your common-law status date would be
            <strong>Date of Common-Law Status: August 15, 2023</strong>.
          </p>
          <div class="cl-actions">
            <button type="button" class="tax-btn" id="clOkBtn">Okay</button>
          </div>
        </div>
      </div>

      <!-- Married/Common Law: Date of Marriage + Canada + Spouse File + Children -->
      <div class="qs-block" id="status-date-block" style="display:none;margin-top: 35px;">
        <label class="qs-label" id="status-date-label">Date of Marriage</label>
        <div class="fi-grid">
          <div class="fi-group fi-float">
            <input id="status_date" name="status_date" class="fi-input dob-input" placeholder=" " value="<?= htmlspecialchars($rowUser['date_marriage'] ?? '') ?>">
            <label class="fi-float-label" for="status_date">DD | MMM | YYYY</label>
          </div>
        </div>

  <p class="qs-help qs-help-alert" id="status-commonlaw-help" style="display:none; margin-top:12px;">
    <strong>What is common-law?</strong> You’re usually considered common-law when you have lived together in a conjugal relationship for 12 continuous months (or meet your province’s rule).  
    <br>
    <strong>Example:</strong> If you moved in together on <strong>August 15, 2022</strong>, then your common-law status date would be <strong>August 15, 2023</strong>.
  </p>

      </div>
<h2 class="qs-title small" style="margin-bottom:12px;">Residing in Canada?</h2>
<div class="yn-group" style="margin: 0;">
  <input type="radio" id="spouse_in_canada_yes" name="spouse_in_canada" value="Yes">
  <label for="spouse_in_canada_yes" class="yn-btn">Yes</label>

  <input type="radio" id="spouse_in_canada_no" name="spouse_in_canada" value="No">
  <label for="spouse_in_canada_no" class="yn-btn">No</label>
</div>


      <div class="qs-block" id="spouse-file-block" style="display:none;">
<label class="qs-title small">Does your spouse want to file taxes? <span class="qs-note">*</span></label>
<div class="yn-group" style="margin: 0;">
  <input type="radio" id="spouse_yes" name="spouseFile" value="yes">
  <label for="spouse_yes" class="yn-btn">Yes</label>

  <input type="radio" id="spouse_no" name="spouseFile" value="no">
  <label for="spouse_no" class="yn-btn">No</label>
</div>
      </div>

     

      <!-- Separated/Divorced/Widowed: separate block with UNIQUE IDs -->
      <div class="qs-block" id="status-date-sdw-block"  style="display:none;margin-top: 35px;">
        <label class="qs-label" id="status-date-sdw-label">Date</label>
        <div class="fi-grid">
          <div class="fi-group fi-float">
            <input id="status_date_sdw" name="status_date" class="fi-input dob-input" placeholder=" " value="<?= htmlspecialchars($rowUser['marital_change'] ?? '') ?>">
            <label class="fi-float-label" for="status_date_sdw">DD | MMM | YYYY</label>
          </div>
        </div>
      </div>

 <div class="qs-block" id="children-block" style="display:none;">
<label class="qs-title small">Do you have children?</label>
<div class="yn-group">
  <input type="radio" id="children_yes" name="children" value="yes">
  <label for="children_yes" class="yn-btn">Yes</label>

  <input type="radio" id="children_no" name="children" value="no">
  <label for="children_no" class="yn-btn">No</label>
</div>

      </div>


      <!-- sync field (unchanged) -->
      <input type="hidden" id="spouseFile_value" name="spouseFile_value" value="no">
      
      <div class="tax-cta tax-cta-row">
        <button type="button" class="tax-btn-secondary" id="qs-back">Back</button>
        <button type="button" class="continue-btn" id="qs-continue">Let&#39;s Start</button>
      </div>

    </div>
  </section>
</div>
 
    
<!-- 3RD PAGE -->      
<!-- MASTER FORM PANEL (replace your #personal-info-panel wrapper with this) -->
                        
<div id="form-panel" style="display:none;">
  <section class="tax-card pi-layout" role="region" aria-labelledby="flow-title">

    <!-- Left Sidebar (ALWAYS visible) -->
    <!-- Left Sidebar (DISPLAY ONLY — not clickable) -->
<aside class="pi-side">
  <!-- Progress-only list -->
  <nav class="pi-steps progress-only" aria-label="Sections">
    <!-- Always checked -->
    <a class="pi-step is-done" data-step="pre" aria-disabled="true" tabindex="-1">
      Pre-details
    </a>

    <!-- The rest get marked current/done by the script -->
    <a class="pi-step" data-step="personal" aria-disabled="true" tabindex="-1">Personal information</a>
    <a class="pi-step" data-step="tax" aria-disabled="true" tabindex="-1">Tax Filing Information</a>
    <a class="pi-step" data-step="spouse" aria-disabled="true" tabindex="-1">Spouse Information</a>
    <a class="pi-step" data-step="spouse-tax" aria-disabled="true" tabindex="-1">Spouse Tax Filing Information</a>
    <a class="pi-step" data-step="children" aria-disabled="true" tabindex="-1">Children Information</a>
    <a class="pi-step" data-step="other-income" aria-disabled="true" tabindex="-1">Other Income</a>
    <a class="pi-step" data-step="upload-self" aria-disabled="true" tabindex="-1">Add/Upload Documents (Applicant)</a>
    <a class="pi-step" data-step="upload-spouse" aria-disabled="true" tabindex="-1">Spouse Add/Upload Documents</a>    
    <a class="pi-step" data-step="review" aria-disabled="true" tabindex="-1">Review Information</a>
    <a class="pi-step" data-step="confirm" aria-disabled="true" tabindex="-1">Confirmation of Document Submission</a>
  </nav>
</aside>

  
<!-- MOBILE BAR -->
<div class="pi-mobilebar" id="pi-mobilebar" role="region" aria-label="Step navigation">
  <button type="button" class="pi-mb-back" id="pi-mb-back" aria-label="Back"></button>
  <div class="pi-mb-text">
    <span id="pi-mb-stepcount">1 of 12</span>
    <span class="pi-mb-dash"> - </span>
    <span id="pi-mb-steptitle">Account Selection</span>
  </div>
  <button type="button" class="pi-mb-toggle" id="pi-mb-toggle" aria-expanded="false" aria-controls="pi-mb-drawer"></button>
  <div class="pi-mb-progress"><div id="pi-mb-progressbar"></div></div>
</div>

<!-- MOBILE DRAWER -->
<aside class="pi-mb-drawer" id="pi-mb-drawer" hidden>
  <div class="pi-mb-card" role="dialog" aria-modal="true" aria-label="Navigation">
    <div class="pi-mb-drawer-head">
      <div id="pi-mb-drawer-title">Navigation</div>
      <button type="button" class="pi-mb-close" id="pi-mb-close" aria-label="Close"></button>
    </div>
    <nav class="pi-mb-nav" id="pi-mb-nav"></nav>
  </div>
</aside>
                        
                        
    <!-- STAGE: where pages swap -->
    <div class="pi-stage">

      <!-- PAGE 1: PERSONAL  -->
      <div class="pi-main" data-panel="personal">
        
        <div class="qs-block">
  		<label class="qs-title small" style="margin-top: -46px !important;">What’s your name?</label>
  		<div class="qs-help">Enter your name as it appears on your SIN number document.</div>
		</div>
        

        <div class="fi-grid">
          <div class="fi-group fi-float">
            <input id="first_name" name="first_name" class="fi-input" autocomplete="given-name"
                   value="<?= htmlspecialchars($rowUser['first_name'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="first_name">First name</label>
          </div>

          <div class="fi-group fi-float">
            <input id="middle_name" name="middle_name" class="fi-input" autocomplete="additional-name"
                   value="<?= htmlspecialchars($rowUser['middle_name'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="middle_name">Middle name (Optional)</label>
          </div>

          <div class="fi-group fi-float ">
            <input id="last_name" name="last_name" class="fi-input" autocomplete="family-name"
                   value="<?= htmlspecialchars($rowUser['last_name'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="last_name">Last name</label>
          </div>
        </div>

        <h2 class="qs-title small" style="margin-bottom:24px">What’s your date of birth?</h2>
<div class="fi-grid">
      <div class="fi-group fi-float">
  <input id="dob" name="dob" class="fi-input dob-input" autocomplete="bday"
         value="<?= htmlspecialchars($rowUser['dob'] ?? '') ?>" placeholder=" ">
  <label class="fi-float-label" for="dob">DD | MMM | YYYY</label>
</div>
</div>
		
        <div class="qs-block">
  			<label class="qs-title small">What’s your Social Insurance Number?</label>
  			<div class="qs-help">Enter 9 digits.</div>
		</div>
<div class="fi-grid">
        <div class="fi-group fi-float ">
          <input id="sin" name="sin" class="fi-input" type="tel" inputmode="numeric" pattern="[0-9]{9}" maxlength="9"
                 value="<?= htmlspecialchars($rowUser['sin'] ?? '') ?>" placeholder=" " 
                 onkeypress="return /[0-9]/i.test(event.key)" onpaste="return false">
          <label class="fi-float-label" for="sin">SIN (9 digits)</label>
        </div>
        <div class="fi-hint"></div>
</div>
        <h2 class="qs-title small" style="margin-bottom:24px">What’s your gender?</h2>
        <div class="yn-group" style="margin-bottom:24px">
          <input type="radio" id="gender_male"   name="gender" value="Male" <?= (($rowUser['gender'] ?? '') === 'Male') ? 'checked' : '' ?>>
          <label for="gender_male" class="yn-btn">Male</label>

          <input type="radio" id="gender_female" name="gender" value="Female" <?= (($rowUser['gender'] ?? '') === 'Female') ? 'checked' : '' ?>>
          <label for="gender_female" class="yn-btn">Female</label>
        </div>

        <h2 class="qs-title small" style="margin-bottom:24px">Address</h2>
        <div class="fi-grid">
          <div class="fi-group fi-float">
            <input id="street" name="street" class="fi-input" list="street-suggest"
                   value="<?= htmlspecialchars($rowUser['street'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="street">Street</label>
            <datalist id="street-suggest">
              <option value="123 Main St">
              <option value="456 King St W">
              <option value="789 Queen St E">
            </datalist>
            <div id="street-autocomplete" class="addr-suggest" hidden></div>
            <!-- Address data © OpenStreetMap contributors, ODbL -->
          </div>

          <div class="fi-group fi-float">
            <input id="unit" name="unit" class="fi-input"
                   value="<?= htmlspecialchars($rowUser['unit'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="unit">Apartment, Unit, Suite, or floor #</label>
          </div>

          <div class="fi-group fi-float">
            <input id="city" name="city" class="fi-input"
                   value="<?= htmlspecialchars($rowUser['city'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="city">City</label>
          </div>

          <div class="fi-group fi-float">
            <input id="province" name="province" class="fi-input"
                   value="<?= htmlspecialchars($rowUser['province'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="province">State/Province</label>
          </div>

          <div class="fi-group fi-float">
            <input id="postal" name="postal" class="fi-input"
                   value="<?= htmlspecialchars($rowUser['postal'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="postal">Postal Code</label>
          </div>

          <div class="fi-group fi-float">
  			<input id="country" name="country" class="fi-input" list="country-list"
         value="<?= htmlspecialchars($rowUser['country'] ?? 'Canada') ?>" placeholder=" ">
  				<label class="fi-float-label" for="country">Country</label>
  			<datalist id="country-list"></datalist>
          </div>
        </div>

        <h2 class="qs-title small" style="margin-bottom:24px">Contact info</h2>
        <div class="fi-grid">
          <div class="fi-group fi-float">
            <input id="phone" name="phone" class="fi-input" inputmode="tel"
                   value="<?= htmlspecialchars(($rowUser['phone_plain'] ?? '') === '0000000000' ? '' : ($rowUser['phone_plain'] ?? '')) ?>" placeholder=" ">
            <label class="fi-float-label" for="phone">Phone number</label>
          </div>

          <div class="fi-group fi-float">
            <input id="email" name="email" class="fi-input" type="email" autocomplete="email"
                   value="<?= htmlspecialchars($rowUser['email_plain'] ?? $_SESSION['email'] ?? '') ?>" placeholder=" ">
            <label class="fi-float-label" for="email">Email</label>
          </div>
        </div>

        <!-- Back / Continue -->
        <div class="tax-cta tax-cta-row" style="margin-top:56px;">
         <button type="button" class="tax-btn-secondary" data-goto="welcome">Back</button>
         <button type="button" class="continue-btn" data-goto="next">Continue</button>
        </div>
      </div>

                  
      <!-- PAGE 2: TAX FILING (your same UI moved here) -->
<div class="pi-main" data-panel="tax" hidden>
                        
<div class="qs-block">
  <label class="qs-label">Is this the first time you are filing tax? <span class="qs-note">*</span></label>
  <div class="yn-group">
    <input type="radio" id="first_yes" name="first_time" value="yes" required <?= (($rowUser['first_time'] ?? '') === 'yes' ? 'checked' : '') ?>>
    <label for="first_yes" class="yn-btn">Yes</label>

    <input type="radio" id="first_no" name="first_time" value="no" required <?= (($rowUser['first_time'] ?? '') === 'no' ? 'checked' : '') ?>>
    <label for="first_no" class="yn-btn">No</label>
  </div>
</div>

<!-- BRANCH A: Prior customer / years (shown when FIRST-TIME = NO) -->
<div id="prior-customer-section" class="qs-block is-hidden" aria-hidden="true">
  <label class="qs-label">Did you file earlier with Paragon Tax Services? <span class="qs-note">*</span></label>
  <div class="yn-group" style="margin-bottom:24px">
    <input type="radio" id="paragon_yes" name="paragon_prior" value="yes" required <?= (($rowUser['paragon_prior'] ?? '') === 'yes' ? 'checked' : '') ?>>
    <label for="paragon_yes" class="yn-btn">Yes</label>

    <input type="radio" id="paragon_no" name="paragon_prior" value="no" required <?= (($rowUser['paragon_prior'] ?? '') === 'no' ? 'checked' : '') ?>>
    <label for="paragon_no" class="yn-btn">No</label>
  </div>
<div class="fi-grid">
  <div class="fi-group fi-float fi-full-width" style="margin-top:10px">
    <input id="return_years" name="return_years" class="fi-input" placeholder=" " required value="<?= htmlspecialchars($rowUser['return_years'] ?? '') ?>">
    <label class="fi-float-label" for="return_years">Which years do you want to file tax returns? <span class="qs-note">*</span></label>
    <div class="qs-help" style="margin-top: 0">(Enter years separated by commas, e.g., 2024, 2023, 2022)</div>
  </div>
</div>
</div>



<!-- BRANCH B: First-time details (shown when FIRST-TIME = YES) -->
<div id="firsttime-details" class="is-hidden" aria-hidden="true">
  <!-- Entry & Birth -->
  <div class="fi-grid">
<div class="fi-group fi-float">
  <input id="entry_date_display"
         class="fi-input dob-input"
         placeholder=" "
         data-bind="#entry_date"
         data-dob-mode="ymd"
         required>      <!-- Year-Month-Day -->
  <label class="fi-float-label" for="entry_date_display">Date of Entry</label>

  <!-- Hidden stores YYYY-MM-DD -->
  <input type="hidden" id="entry_date" name="entry_date" value="<?= htmlspecialchars($rowUser['entry_date'] ?? '') ?>">
</div>

<div class="fi-group fi-float">
  <input id="birth_country" name="birth_country" class="fi-input" placeholder=" " required value="<?= htmlspecialchars($rowUser['birth_country'] ?? '') ?>">
  <label class="fi-float-label" for="birth_country">
    Country of Previous Residency <span class="qs-note">*</span>
  </label>
  <div id="birth-country-suggest" class="addr-suggest" hidden></div>

</div>

</div>

                      
                        
  <!-- World Income -->
<!-- World Income (hidden until entry date is set) -->
<div id="wi-wrapper" class="is-hidden" style="margin-bottom: 46px;" aria-hidden="true">
  <div class="qs-block" style="margin-top: 24px;">
    <label class="qs-label">What was your world income in last 3 years before coming to Canada?</label>
  </div>

  <section class="wi-grid" aria-label="World Income Periods and Amounts">
    <!-- LEFT: Periods -->
    <div class="wi-col wi-col--period">
      <div class="wi-title">Period</div>
      <div class="wi-row" id="period_y1">—</div>
      <div class="wi-row" id="period_y2">—</div>
      <div class="wi-row" id="period_y3">—</div>
    </div>

    <!-- RIGHT: Income inputs -->
    <div class="wi-col wi-col--income">
      <div class="wi-title">World Income (CAD)</div>

      <div class="wi-row">
        <div class="fi-group fi-float wi-inline">
          <input id="inc_y1" name="inc_y1" class="fi-input" 
                 inputmode="decimal" autocomplete="off"
                 pattern="^[0-9]+([.,][0-9]{1,2})?$" aria-describedby="period_y1"
                 value="<?= htmlspecialchars($rowUser['year1_income'] ?? '') ?>">
          <label class="fi-float-label" for="inc_y1">Year 1 Income</label>
        </div>
      </div>

      <div class="wi-row">
        <div class="fi-group fi-float wi-inline">
          <input id="inc_y2" name="inc_y2" class="fi-input" 
                 inputmode="decimal" autocomplete="off"
                 pattern="^[0-9]+([.,][0-9]{1,2})?$" aria-describedby="period_y2"
                 value="<?= htmlspecialchars($rowUser['year2_income'] ?? '') ?>">
          <label class="fi-float-label" for="inc_y2">Year 2 Income</label>
        </div>
      </div>

      <div class="wi-row">
        <div class="fi-group fi-float wi-inline">
          <input id="inc_y3" name="inc_y3" class="fi-input" 
                 inputmode="decimal" autocomplete="off"
                 pattern="^[0-9]+([.,][0-9]{1,2})?$" aria-describedby="period_y3"
                 value="<?= htmlspecialchars($rowUser['year3_income'] ?? '') ?>">
          <label class="fi-float-label" for="inc_y3">Year 3 Income</label>
        </div>
      </div>
    </div>
  </section>
</div>
               
</div>

                        
  <!-- Move to another province -->
<div class="qs-block"  style="margin-top:-2px !important;">
  <label class="qs-label">Did you move to another province? <span class="qs-note">*</span></label>
  <div class="yn-group">
    <input type="radio" id="mprov_yes" name="moved_province" value="yes" required>
    <label for="mprov_yes" class="yn-btn">Yes</label>

    <input type="radio" id="mprov_no" name="moved_province" value="no" required>
    <label for="mprov_no" class="yn-btn">No</label>
  </div>
</div>

<!-- When did you move? (toggled ) -->

<div id="moved-section" class="qs-block is-hidden" aria-hidden="true">
  <label class="qs-label">When did you move? <span class="qs-note">*</span></label>

<div class="fi-grid">
<div class="fi-group fi-float">
  <input
    id="moved_date_display"
    name="moved_date_display"
    class="fi-input dob-input"
    placeholder=" "
    value="<?= htmlspecialchars($rowUser['moved_date_display'] ?? '') ?>"
    data-bind="#moved_date_iso"
    data-dob-mode="ymd"       
    required>

  <label class="fi-float-label" for="moved_date_display">Date moved</label>

  <!-- now stores YYYY-MM-DD -->
  <input type="hidden"
         id="moved_date_iso"
         name="moved_date"
         value="<?= htmlspecialchars($rowUser['moved_date'] ?? '') ?>">
</div>
</div>

<div class="fi-grid" style="margin: 10px 0 46px;">
  <div class="fi-group fi-float">
    <select id="prov_from" name="prov_from" class="fi-input" data-value="<?= htmlspecialchars($rowUser['prov_from'] ?? '') ?>" required>
      <option value="">Select State/Province</option>
      <option>Alberta</option><option>British Columbia</option><option>Manitoba</option>
      <option>New Brunswick</option><option>Newfoundland and Labrador</option>
      <option>Nova Scotia</option><option>Ontario</option><option>Prince Edward Island</option>
      <option>Quebec</option><option>Saskatchewan</option><option>Northwest Territories</option>
      <option>Nunavut</option><option>Yukon</option>
    </select>
    <label class="fi-float-label" for="prov_from">Province moved From? <span class="qs-note">*</span></label>
  </div>

  <div class="fi-group fi-float">
    <select id="prov_to" name="prov_to" class="fi-input" data-value="<?= htmlspecialchars($rowUser['prov_to'] ?? '') ?>" required>
      <option value="">Select State/Province</option>
      <option>Alberta</option><option>British Columbia</option><option>Manitoba</option>
      <option>New Brunswick</option><option>Newfoundland and Labrador</option>
      <option>Nova Scotia</option><option>Ontario</option><option>Prince Edward Island</option>
      <option>Quebec</option><option>Saskatchewan</option><option>Northwest Territories</option>
      <option>Nunavut</option><option>Yukon</option>
    </select>
    <label class="fi-float-label" for="prov_to">Province moved To? <span class="qs-note">*</span></label>
  </div>
 </div>   

 <!-- Moving Expenses -->
<div class="qs-block">
 <label class="qs-label">Do you want to claim moving expenses? <span class="qs-note">*</span></label>
 <div class="yn-group">
    <input type="radio" id="movexp_yes" name="moving_expenses_claim" value="yes" required>
    <label for="movexp_yes" class="yn-btn">Yes</label>

    <input type="radio" id="movexp_no" name="moving_expenses_claim" value="no" required>
    <label for="movexp_no" class="yn-btn">No</label>
  </div>
</div> 
<!-- If yes: details -->
  <div id="movexp-details" class="qs-block fi-grid is-hidden" style="margin-bottom:46px;" aria-hidden="true">
    <!-- Previous address (text box) -->
    <div class="fi-group fi-float">
      <input
        id="moving_prev_address"
        name="moving_prev_address"
        class="fi-input"
        placeholder=" "
        value="<?= htmlspecialchars($rowUser['moving_prev_address'] ?? '') ?>"
        <?= (isset($rowUser['moving_expenses_claim']) && $rowUser['moving_expenses_claim'] === 'yes') ? 'required' : '' ?>>
      <label class="fi-float-label" for="moving_prev_address">Previous address</label>
      <div id="moving-prev-address-autocomplete" class="addr-suggest" hidden></div>
      <!-- Address data © OpenStreetMap contributors, ODbL -->
    </div>

    <!-- Distance between previous and current address (text box) -->
    <div class="fi-group fi-float">
      <input
        id="moving_distance"
        name="moving_distance"
        class="fi-input"
        placeholder=" "
        value="<?= htmlspecialchars($rowUser['moving_distance'] ?? '') ?>"
        <?= (isset($rowUser['moving_expenses_claim']) && $rowUser['moving_expenses_claim'] === 'yes') ? 'required' : '' ?>>
      <label class="fi-float-label" for="moving_distance">Distance to current address</label>
      <div class="fi-hint" id="moving-distance-hint" style="display:none; color:#64748b; font-size:13px; margin-top:4px;"></div>
    </div>
  </div>
                    
 </div>                       

                     
   
                      
                      
<!-- CONTROLLER -->
                        

<!-- Controller -->
<div class="qs-block">
  <label class="qs-label">Are you first time home buyer? <span class="qs-note">*</span></label>
  <div class="yn-group">
    <input type="radio" id="fthb_yes" name="first_home_buyer" value="yes" required>
    <label for="fthb_yes" class="yn-btn">Yes</label>

    <input type="radio" id="fthb_no" name="first_home_buyer" value="no" required>
    <label for="fthb_no" class="yn-btn">No</label>
  </div>
</div>

<!-- Details (shown only when first_home_buyer = yes) -->
 <div class="qs-block">      

<div class="fi-grid">                        
<div id="fthb-details" class="is-hidden" aria-hidden="true">
                          <label class="qs-label"> When did you purchase your first home? <span class="qs-note">*</span></label>

  <div class="fi-group fi-float" style="margin-bottom:46px;">
    <input id="first_home_purchase_display" name="first_home_purchase_display"
           class="fi-input dob-input" placeholder=" "
           value="<?= htmlspecialchars($rowUser['first_home_purchase_display'] ?? '') ?>"
           data-bind="#first_home_purchase" required>
    <label class="fi-float-label" for="first_home_purchase_display">
                      Date of Purchase      </label>
  </div>
                      
  <input type="hidden" id="first_home_purchase" name="first_home_purchase"
         value="<?= htmlspecialchars($rowUser['first_home_purchase'] ?? '') ?>">
</div>
  </div>                      
                        
<!-- Sole owner? (replaces "Do you want to claim the full amount?") -->
<div class="qs-block" style="margin-top:6px;">
  <label class="qs-label">Are you the sole owner of the home? <span class="qs-note">*</span></label>
  <div class="yn-group">
    <input type="radio" id="claim_full_yes" name="claim_full" value="yes" required>
    <label for="claim_full_yes" class="yn-btn">Yes</label>

    <input type="radio" id="claim_full_no" name="claim_full" value="no" required>
    <label for="claim_full_no" class="yn-btn">No</label>
  </div>
</div>

<!-- Show ONLY when NOT sole owner -->
<div class="fi-grid" style="margin-bottom: 28px;">
  <div id="owners-wrap"
       class="fi-group fi-float <?= (isset($rowUser['claim_full']) && $rowUser['claim_full']==='no') ? '' : 'is-hidden' ?>"
       aria-hidden="<?= (isset($rowUser['claim_full']) && $rowUser['claim_full']==='no') ? 'false' : 'true' ?>">
    <input type="number"
           id="owner_count"
           name="owner_count"
           class="fi-input"
           placeholder=" "
           min="2" step="1"
           value="<?= htmlspecialchars($rowUser['owner_count'] ?? '') ?>"
           <?= (isset($rowUser['claim_full']) && $rowUser['claim_full']==='no') ? 'required' : '' ?>>
    <label class="fi-float-label" for="owner_count"># of owners including you</label>
  </div>
</div>

                        
<!-- Living on Rent -->
<div class="qs-block">
  <label class="qs-label">Are you living on Rent?</label>
  <div class="yn-group" id="onRentGroup">
    <input type="radio" id="onrent_yes" name="onRent" value="yes">
    <label for="onrent_yes" class="yn-btn">Yes</label>

    <input type="radio" id="onrent_no" name="onRent" value="no">
    <label for="onrent_no" class="yn-btn">No</label>
  </div>
</div>

<!-- NEW: Claim rent benefit (only visible when onRent = Yes) -->
<div class="qs-block" id="claim-rent-block" style="display:none;">
  <label class="qs-label">Do you want to claim rent benefit?</label>
  <div class="yn-group" id="claimRentGroup">
    <input type="radio" id="claimrent_yes" name="claimRent" value="yes">
    <label for="claimrent_yes" class="yn-btn">Yes</label>

    <input type="radio" id="claimrent_no" name="claimRent" value="no">
    <label for="claimrent_no" class="yn-btn">No</label>
  </div>

</div>

<!-- RENT ADDRESSES (inline editor, not a modal) -->
<div id="rent-addresses" style="display:none;">
  <div class="qs-block">
    <label class="qs-label">Rent Addresses</label>
    <div class="qs-help">Add each rental address you paid for, with dates and total paid.</div>
  </div>

  <!-- Add button (top-right when empty) -->
  <div id="rent-add-wrap-top" style="margin:10px 0 16px; display:flex; justify-content:flex-end;">
    <button type="button" id="rent-add-btn" class="tax-btn">Add Address</button>
  </div>

  <div class="qs-block">
    <table class="fi-table rent-table" style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px;">Rent Address</th>
          <th style="text-align:left; padding:8px;">From</th>
          <th style="text-align:left; padding:8px;">To</th>
          <th style="text-align:left; padding:8px;">Total Rent Paid</th>
          <th style="text-align:center; padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody id="rent-tbody">
        <tr id="rent-empty-row"><td colspan="5" style="padding:10px; opacity:.7;">No addresses added yet.</td></tr>
      </tbody>
    </table>
  </div>

  <!-- Add button (bottom-right when there are rows) -->
  <div id="rent-add-wrap-bottom" style="margin:16px 0 0; display:none; justify-content:flex-end;">
    <button type="button" id="rent-add-btn-bottom" class="tax-btn">Add Address</button>
  </div>

  <!-- Hidden inputs for form POST -->
  <div id="rent-hidden-inputs"></div>

  <!-- Address suggestions -->
  <datalist id="rent-addr-suggest">
    <?php if (!empty($addressSuggestions ?? [])): foreach (($addressSuggestions ?? []) as $addr): ?>
      <option value="<?= htmlspecialchars($addr) ?>"></option>
    <?php endforeach; endif; ?>
  </datalist>

  <!-- Seed previously saved rows (optional) -->
  <script type="application/json" id="rent-seed">
    <?= isset($rentListJSON) ? $rentListJSON : '[]' ?>
  </script>
</div>
   
                        
</div>

<!-- Back / Continue -->
<div class="tax-cta tax-cta-row" style="margin-top:58px;">
         <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
         <button type="button" class="continue-btn" data-goto="next">Continue</button>
</div>

</div>
                        
                        
                                          
<!--PAGE 3 - SPOUSE PANEL -->
<div class="pi-main" data-panel="spouse">
  
<!-- Spouse: Name -->
<div class="qs-block">
  <label class="qs-title small" style="margin-top:-46px !important;">What’s your spouse’s name?</label>
  <div class="qs-help">Enter your spouse’s name as it appears on official documents.</div>
</div>

<div class="fi-grid">
  <div class="fi-group fi-float">
    <input id="spouse_first_name" name="spouse_first_name" class="fi-input" autocomplete="given-name"
           value="<?= htmlspecialchars($rowSpouse['first_name'] ?? '') ?>" placeholder=" ">
    <label class="fi-float-label" for="spouse_first_name">First name</label>
  </div>

  <div class="fi-group fi-float">
    <input id="spouse_middle_name" name="spouse_middle_name" class="fi-input" autocomplete="additional-name"
           value="<?= htmlspecialchars($rowSpouse['middle_name'] ?? '') ?>" placeholder=" ">
    <label class="fi-float-label" for="spouse_middle_name">Middle name (Optional)</label>
  </div>

  <div class="fi-group fi-float">
    <input id="spouse_last_name" name="spouse_last_name" class="fi-input" autocomplete="family-name"
           value="<?= htmlspecialchars($rowSpouse['last_name'] ?? '') ?>" placeholder=" ">
    <label class="fi-float-label" for="spouse_last_name">Last name</label>
  </div>
</div>

<!-- Spouse: DOB -->
<h2 class="qs-title small" style="margin-bottom:24px;">What’s your spouse’s date of birth?</h2>
<div class="fi-grid">
  <div class="fi-group fi-float">
    <input id="spouse_dob" name="spouse_dob" class="fi-input dob-input" autocomplete="bday"
           value="<?= htmlspecialchars($rowSpouse['dob'] ?? '') ?>" placeholder=" ">
    <label class="fi-float-label" for="spouse_dob">MM | DD | YYYY</label>
  </div>
</div>


<!-- Only when Residing in Canada = No -->
<div class="fi-grid">                        
<div id="spouse-foreign-income" class="fi-group fi-float fi-span2"
     style="<?= (isset($rowSpouse['in_canada']) && $rowSpouse['in_canada'] === 'No') ? '' : 'display:none;' ?>">
  <input id="spouse_income_outside_cad"
         name="spouse_income_outside_cad"
         class="fi-input"
         inputmode="decimal"
         pattern="^\d+(\.\d{1,2})?$"
         value="<?= htmlspecialchars($rowSpouse['income_outside_cad'] ?? '') ?>"
         placeholder=" ">
  <label class="fi-float-label" for="spouse_income_outside_cad">
    Spousal Annual Income outside Canada (Converted to CAD) <span class="qs-note">*</span>
  </label>
  <div class="fi-hint">Enter numbers only, e.g., 25000.00</div>
</div>
</div>

                       
                        
<!-- Everything else goes inside this wrapper; it is shown only when Residing = Yes -->
<div id="spouse-remaining"
     style="<?= (!isset($rowSpouse['in_canada']) || $rowSpouse['in_canada'] === 'Yes') ? '' : 'display:none;' ?>">

  <!-- SIN now in Spouse Information (moved from Contact) -->
<div class="qs-block">
  			<label class="qs-title small" style="margin-top: 28px;">Spouse SIN</label>
  			<div class="qs-help">Enter 9 digits.</div>
</div>                        
 <div class="fi-grid">
        <div class="fi-group fi-float">
    <input id="spouse_sin" name="spouse_sin" class="fi-input" type="tel" inputmode="numeric" pattern="[0-9]{9}" maxlength="9"
           value="<?= htmlspecialchars($rowSpouse['sin'] ?? '') ?>" placeholder=" " 
           onkeypress="return /[0-9]/i.test(event.key)" onpaste="return false">
    <label class="fi-float-label" for="spouse_sin">SIN (9 digits)</label>
  </div>
        <div class="fi-hint"></div>
</div>
                        

  <!-- Address same? -->
  <div class="fi-group fi-float">
    <label class="qs-title small" style="display:block;margin:0 0 8px;">Is your spouse’s address the same as yours?</label>
    <div class="yn-group" style="margin-bottom:0;">
      <?php $addrSame = $rowSpouse['address_same'] ?? ''; ?>
      <input type="radio" id="spouse_addr_same_yes" name="spouse_address_same" value="Yes"
             <?= ($addrSame === 'Yes') ? 'checked' : '' ?>>
      <label for="spouse_addr_same_yes" class="yn-btn">Yes</label>

      <input type="radio" id="spouse_addr_same_no" name="spouse_address_same" value="No"
             <?= ($addrSame === 'No') ? 'checked' : '' ?>>
      <label for="spouse_addr_same_no" class="yn-btn">No</label>
    </div>
  </div>

  <!-- Address (hidden unless address_same = No) -->
  <?php $showAddr = (isset($rowSpouse['address_same']) && $rowSpouse['address_same'] === 'No'); ?>
  <div id="spouse-address-fields" style="<?= $showAddr ? '' : 'display:none;' ?>">
    <h2 class="qs-title small" style="margin-top:8px;">Spouse Address</h2>
    <div class="fi-grid">
      <div class="fi-group fi-float fi-span2">
        <input id="spouse_street" name="spouse_street" class="fi-input"
               value="<?= htmlspecialchars($rowSpouse['street'] ?? '') ?>" placeholder=" ">
        <label class="fi-float-label" for="spouse_street">Street</label>
        <div id="spouse-street-autocomplete" class="addr-suggest" hidden></div>
        <!-- Address data © OpenStreetMap contributors, ODbL -->
      </div>

      <div class="fi-group fi-float">
        <input id="spouse_unit" name="spouse_unit" class="fi-input"
               value="<?= htmlspecialchars($rowSpouse['unit'] ?? '') ?>" placeholder=" ">
        <label class="fi-float-label" for="spouse_unit">Apartment, Unit, Suite, or floor #</label>
      </div>

      <div class="fi-group fi-float">
        <input id="spouse_city" name="spouse_city" class="fi-input"
               value="<?= htmlspecialchars($rowSpouse['city'] ?? '') ?>" placeholder=" ">
        <label class="fi-float-label" for="spouse_city">City</label>
      </div>

      <div class="fi-group fi-float">
        <input id="spouse_province" name="spouse_province" class="fi-input"
               value="<?= htmlspecialchars($rowSpouse['province'] ?? '') ?>" placeholder=" ">
        <label class="fi-float-label" for="spouse_province">State/Province</label>
      </div>

      <div class="fi-group fi-float">
        <input id="spouse_postal" name="spouse_postal" class="fi-input"
               value="<?= htmlspecialchars($rowSpouse['postal'] ?? '') ?>" placeholder=" ">
        <label class="fi-float-label" for="spouse_postal">Postal Code</label>
      </div>

<div class="fi-group fi-float">
  <input id="spouse_country" name="spouse_country" class="fi-input" list="country-list"
         value="<?= htmlspecialchars($rowSpouse['country'] ?? 'Canada') ?>" placeholder=" ">
  <label class="fi-float-label" for="spouse_country">Country</label>
</div>
    </div>
  </div>

  <!-- Contact Information -->
  <h2 class="qs-title small" style="margin-top:0;">Spouse Contact Information</h2>
  <div class="fi-grid">
    <div class="fi-group fi-float">
      <input id="spouse_phone" name="spouse_phone" class="fi-input" inputmode="tel"
             value="<?= htmlspecialchars($rowSpouse['phone'] ?? '') ?>" placeholder=" ">
      <label class="fi-float-label" for="spouse_phone">Phone number</label>
    </div>

    <div class="fi-group fi-float">
      <input id="spouse_email" name="spouse_email" class="fi-input" type="email" autocomplete="email"
             value="<?= htmlspecialchars($rowSpouse['email'] ?? '') ?>" placeholder=" ">
      <label class="fi-float-label" for="spouse_email">Email</label>
    </div>
  </div>
</div>

 <!-- Spouse Annual Income (appears only if spouseFile == 'no') -->
<h2 id="spouse-income-title" class="qs-title small" style="margin-bottom:24px;">
  Spouse Annual Income
</h2>
<div class="fi-grid">
  <div id="spouse-income" class="fi-group fi-float fi-suf" style="display:none;">
    <input id="spouse_income_cad" name="spouse_income_cad"
           class="fi-input with-suffix"
           inputmode="decimal" pattern="^\d+(\.\d{1,2})?$"
           value="<?= htmlspecialchars($rowSpouse['income_cad'] ?? '') ?>"
           placeholder="">
    <label class="fi-float-label" for="spouse_income_cad">(CAD)</label>
    <span class="fi-suffix" aria-hidden="true">$</span>
  </div>
</div>



  <!-- CTA -->
  <div class="tax-cta tax-cta-row" style="margin-top:58px;">
         <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
         <button type="button" class="continue-btn" data-goto="next">Continue</button>
  </div>
                        
</div>
                        
<!-- SPOUSE TAX PANEL-->
<div class="pi-main" data-panel="spouse-tax">

  <?php
    // neutral: don't force 'yes' or 'no'
    $spFirst = isset($rowSpouseTax['first_time'])
      ? strtolower($rowSpouseTax['first_time'])
      : '';
  ?>

  <!-- CONTROLLER: First time filing (spouse) -->
  <div class="qs-block" style="margin-top: -2px !important;">
    <label class="qs-label">Is this the first time your spouse is filing tax? <span class="qs-note">*</span></label>
    <div class="yn-group" id="sp-first-ctrl">
      <input type="radio" id="sp_first_yes" name="sp_first_time" value="yes" required
             >
      <label for="sp_first_yes" class="yn-btn">Yes</label>

      <input type="radio" id="sp_first_no" name="sp_first_time" value="no" required
             >
      <label for="sp_first_no" class="yn-btn">No</label>
    </div>
  </div>


  <!-- BRANCH A (spouse): Prior customer / years (should show only when FIRST-TIME = NO) -->
  <?php
    $spPrior  = isset($rowSpouseTax['paragon_prior']) ? strtolower($rowSpouseTax['paragon_prior']) : '';
    $spYears  = $rowSpouseTax['return_years'] ?? '';
    // we start hidden; JS should unhide when user clicks "No"
  ?>
  <div id="sp-prior-customer-section" class="qs-block is-hidden" aria-hidden="true">
    <label class="qs-label">Did your spouse file earlier with Paragon Tax Services? <span class="qs-note">*</span></label>
    <div class="yn-group">
      <input type="radio" id="sp_paragon_yes" name="sp_paragon_prior" value="yes" required
             >
      <label for="sp_paragon_yes" class="yn-btn">Yes</label>

      <input type="radio" id="sp_paragon_no" name="sp_paragon_prior" value="no" required
             >
      <label for="sp_paragon_no" class="yn-btn">No</label>
    </div>

    <div class="fi-group fi-float fi-full-width" style="margin-top:10px">
      <input id="sp_return_years" name="sp_return_years" class="fi-input" placeholder=" " required
             value="<?= htmlspecialchars($spYears) ?>">
      <label class="fi-float-label" for="sp_return_years">
        Which Years Your Spouse want to file tax returns? <span class="qs-note">*</span>
      </label>
      <div class="qs-help">(Enter years separated by commas, e.g., 2024, 2023, 2022)</div>
    </div>
  </div>


  <!-- BRANCH B (spouse): First-time details (should show only when FIRST-TIME = YES) -->
  <?php
    $spEntryDisp = $rowSpouseTax['entry_date_display'] ?? '';
    $spEntryISO  = $rowSpouseTax['entry_date'] ?? '';
    $spBirthCtry = $rowSpouseTax['birth_country'] ?? '';
    $spInc1 = $rowSpouseTax['inc_y1'] ?? '';
    $spInc2 = $rowSpouseTax['inc_y2'] ?? '';
    $spInc3 = $rowSpouseTax['inc_y3'] ?? '';
    // start hidden; JS will show when user picks "yes"
  ?>
  <div id="sp-firsttime-details" class="is-hidden" aria-hidden="true">

    <div class="fi-grid">
      <!-- Spouse entry -->
      <div class="fi-group fi-float fi-span2">
        <input
          id="sp_entry_date_display"
          name="sp_entry_date_display"
          class="fi-input dob-input"
          placeholder=" "
          value="<?= htmlspecialchars($spEntryDisp) ?>"
          data-bind="#sp_entry_date"
          data-dob-mode="ymd"
          required>
        <label class="fi-float-label" for="sp_entry_date_display">MM | DD | YYYY</label>

        <!-- Hidden stores YYYY-MM-DD -->
        <input type="hidden"
               id="sp_entry_date"
               name="sp_entry_date"
               value="<?= htmlspecialchars($spEntryISO) ?>">
      </div>

      <div class="fi-group fi-float">
        <input id="sp_birth_country" name="sp_birth_country" class="fi-input" placeholder=" " required
               value="<?= htmlspecialchars($spBirthCtry) ?>">
        <label class="fi-float-label" for="sp_birth_country">
          Country of Previous Residency <span class="qs-note">*</span>
        </label>
      </div>
    </div>

    <!-- World Income (spouse) -->
    <div id="sp-wi-wrapper" class="is-hidden" style="margin-bottom: 46px;" aria-hidden="true">
      <div class="qs-block" style="margin-top: 24px;">
        <label class="qs-label">What was your spouse's world income in the last 3 years before coming to Canada?</label>
      </div>

      <section class="wi-grid" aria-label="World Income Periods and Amounts (Spouse)">
        <!-- LEFT: Periods -->
        <div class="wi-col wi-col--period">
          <div class="wi-title">Period</div>
          <div class="wi-row" id="sp_period_y1">—</div>
          <div class="wi-row" id="sp_period_y2">—</div>
          <div class="wi-row" id="sp_period_y3">—</div>
        </div>

        <!-- RIGHT: Income inputs -->
        <div class="wi-col wi-col--income">
          <div class="wi-title">World Income (CAD)</div>

          <div class="wi-row">
            <div class="fi-group fi-float wi-inline">
              <input id="sp_inc_y1" name="sp_inc_y1" class="fi-input" placeholder=" "
                     inputmode="decimal" autocomplete="off"
                     value="<?= htmlspecialchars($spInc1) ?>"
                     pattern="^[0-9]+([.,][0-9]{1,2})?$"
                     aria-describedby="sp_period_y1">
              <label class="fi-float-label" for="sp_inc_y1">Year 1 Income</label>
            </div>
          </div>

          <div class="wi-row">
            <div class="fi-group fi-float wi-inline">
              <input id="sp_inc_y2" name="sp_inc_y2" class="fi-input" placeholder=" "
                     inputmode="decimal" autocomplete="off"
                     value="<?= htmlspecialchars($spInc2) ?>"
                     pattern="^[0-9]+([.,][0-9]{1,2})?$"
                     aria-describedby="sp_period_y2">
              <label class="fi-float-label" for="sp_inc_y2">Year 2 Income</label>
            </div>
          </div>

          <div class="wi-row">
            <div class="fi-group fi-float wi-inline">
              <input id="sp_inc_y3" name="sp_inc_y3" class="fi-input" placeholder=" "
                     inputmode="decimal" autocomplete="off"
                     value="<?= htmlspecialchars($spInc3) ?>"
                     pattern="^[0-9]+([.,][0-9]{1,2})?$"
                     aria-describedby="sp_period_y3">
              <label class="fi-float-label" for="sp_inc_y3">Year 3 Income</label>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>


  <!-- Did your spouse move to another province? -->
  <?php
    $spMoved = isset($rowSpouseTax['moved_province'])
      ? strtolower($rowSpouseTax['moved_province'])
      : '';
    $spMovedDateDisp = $rowSpouseTax['moved_date_display'] ?? '';
    $spMovedDateISO  = $rowSpouseTax['moved_date'] ?? '';
    $spFromProv      = $rowSpouseTax['prov_from'] ?? '';
    $spToProv        = $rowSpouseTax['prov_to'] ?? '';
  ?>
  <div class="qs-block">
    <label class="qs-label">Did your spouse move to another province? <span class="qs-note">*</span></label>
    <div class="yn-group">
      <input type="radio" id="sp_mprov_yes" name="sp_moved_province" value="yes" required
             <?= ($spMoved === 'yes') ? 'checked' : '' ?>>
      <label for="sp_mprov_yes" class="yn-btn">Yes</label>

      <input type="radio" id="sp_mprov_no" name="sp_moved_province" value="no" required
             <?= ($spMoved === 'no') ? 'checked' : '' ?>>
      <label for="sp_mprov_no" class="yn-btn">No</label>
    </div>
  </div>

  <!-- Spouse moved details (always hidden first; JS will show when yes) -->
  <div id="sp-moved-section" class="qs-block is-hidden" aria-hidden="true">
    <label class="qs-label">When did your spouse move? <span class="qs-note">*</span></label>

    <div class="fi-grid">
      <div class="fi-group fi-float">
        <input id="sp_moved_date_display"
               name="sp_moved_date_display"
               class="fi-input dob-input"
               placeholder=" "
               value="<?= htmlspecialchars($spMovedDateDisp) ?>"
               data-bind="#sp_moved_date_iso"
               data-dob-mode="ymd"
               required>
        <label class="fi-float-label" for="sp_moved_date_display">MM | DD | YYYY</label>

        <input type="hidden" id="sp_moved_date_iso" name="sp_moved_date"
               value="<?= htmlspecialchars($spMovedDateISO) ?>">
      </div>
    </div>

    <div class="fi-grid" style="margin-top: 10px;">
      <div class="fi-group fi-float">
        <select id="sp_prov_from" name="sp_prov_from" class="fi-input" data-value="<?= htmlspecialchars($spFromProv) ?>" required>
          <option value="">Select State/Province</option>
          <option>Alberta</option><option>British Columbia</option><option>Manitoba</option>
          <option>New Brunswick</option><option>Newfoundland and Labrador</option>
          <option>Nova Scotia</option><option>Ontario</option><option>Prince Edward Island</option>
          <option>Quebec</option><option>Saskatchewan</option><option>Northwest Territories</option>
          <option>Nunavut</option><option>Yukon</option>
        </select>
        <label class="fi-float-label" for="sp_prov_from">Province moved From? <span class="qs-note">*</span></label>
      </div>

      <div class="fi-group fi-float">
        <select id="sp_prov_to" name="sp_prov_to" class="fi-input" data-value="<?= htmlspecialchars($spToProv) ?>" required>
          <option value="">Select State/Province</option>
          <option>Alberta</option><option>British Columbia</option><option>Manitoba</option>
          <option>New Brunswick</option><option>Newfoundland and Labrador</option>
          <option>Nova Scotia</option><option>Ontario</option><option>Prince Edward Island</option>
          <option>Quebec</option><option>Saskatchewan</option><option>Northwest Territories</option>
          <option>Nunavut</option><option>Yukon</option>
        </select>
        <label class="fi-float-label" for="sp_prov_to">Province moved To? <span class="qs-note">*</span></label>
      </div>
    </div>
  </div>


  <!-- CTA -->
  <div class="tax-cta tax-cta-row" style="margin-top:58px;">
    <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
    <button type="button" class="continue-btn" data-goto="next">Continue</button>
  </div>

</div>

                        
                        
<!-- CHILDREN PANEL -->
                        
<div class="pi-main" data-panel="children">
 <div class="qs-block">
        <label class="qs-title small" style="margin-top: -46px !important;">Add your children below</label>
        <div class="qs-help">You can edit or remove entries anytime.</div>
</div>
  <!-- Actions -->
<div id="add-child-wrap-top" style="margin:10px 0 16px; display:flex; justify-content:flex-end;">
  <button type="button" id="btn-add-child" class="tax-btn">Add Child</button>
</div>

  <!-- Children Table -->
  <div class="qs-block">
	<table class="fi-table children-table" style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th style="text-align:left; padding:8px;">Child First Name</th>
          <th style="text-align:left; padding:8px;">Child Last Name</th>
          <th style="text-align:left; padding:8px;">Child Date of Birth</th>
          <th style="text-align:left; padding:8px;">Residing in Canada?</th>
          <th style="text-align:center; padding:8px;">Actions</th>
        </tr>
      </thead>
      <tbody id="children-tbody">
        <tr id="children-empty-row"><td colspan="5" style="padding:10px; opacity:.7; text-align: center;">No children added yet.</td></tr>
      </tbody>
    </table>
  </div>

  <!-- Hidden inputs for form POST -->
  <div id="children-hidden-inputs"></div>
<div id="add-child-wrap-bottom" style="margin:16px 0 0; display:none; justify-content:flex-end;"></div>

  <!-- CTA -->
  <div class="tax-cta tax-cta-row" style="margin-top:58px;">
         <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
         <button type="button" class="continue-btn" data-goto="next">Continue</button>
  </div>
</div>

<!-- Seed (optional): put server list here; else leave empty array -->
<script type="application/json" id="children-seed">
<?= isset($childrenListJSON) ? $childrenListJSON : '[]' ?>
</script>

<!-- Child Modal -->
<div id="child-modal" class="qs-modal" style="display:none;">
  <div class="qs-modal__backdrop" style="position:fixed; inset:0; background:rgba(0,0,0,.35);"></div>

  <div class="qs-modal__dialog" role="dialog" aria-modal="true">
    <div class="qs-modal__header">
      <h2 class="qs-title child-title" id="child-modal-title">ADD CHILD</h2>
    </div>

    <div class="qs-modal__body child-body">
      <form id="child-form">
        <input type="hidden" id="child_id">

        <div class="fi-grid">
          <div class="fi-group fi-float">
            <input id="child_first_name" class="fi-input" placeholder=" " required>
            <label class="fi-float-label" for="child_first_name">First name</label>
          </div>

          <div class="fi-group fi-float">
            <input id="child_last_name" class="fi-input" placeholder=" " required>
            <label class="fi-float-label" for="child_last_name">Last name</label>
          </div>

          <div class="fi-group fi-float">
            <input id="child_dob_display" class="fi-input dob-input calendarized" placeholder=" " data-bind="#child_dob" required>
            <label class="fi-float-label" for="child_dob_display">Date of Birth</label>
            <!-- calendar icon button (if you already have it elsewhere, keep one only) -->
            <button type="button" class="dob-calendar-btn" aria-label="Open date picker">
             
            </button>
          </div>
          <input type="hidden" id="child_dob">

          <div class="fi-group fi-span2">
            <label class="qs-title child-residing-label">Residing in Canada?</label>
            <div class="yn-group yn-group--pills">
              <input type="radio" id="child_in_canada_yes" name="child_in_canada" value="Yes">
              <label for="child_in_canada_yes" class="yn-btn">Yes</label>

              <input type="radio" id="child_in_canada_no" name="child_in_canada" value="No">
              <label for="child_in_canada_no" class="yn-btn">No</label>
            </div>
          </div>
        </div>
      </form>
    </div>

    <div class="qs-modal__actions child-actions">
      <button type="button" class="tax-btn-secondary" id="child-cancel">Cancel</button>
      <button type="button" class="tax-btn" id="child-save">Save</button>
    </div>
  </div>
</div>
            
                        
                        
<!-- OTHER INCOME PANEL -->
<div class="pi-main" data-panel="other-income">

  <!-- Gig / Delivery income -->
<div class="qs-block"  style="margin-top: -2px !important">
  <label class="qs-label">Do you have income from Uber/Skip/Lyft/Doordash etc.? <span class="qs-note">*</span></label>
  <div class="yn-group" style="margin-bottom: 0;">
    <input type="radio" id="gig_income_yes" name="gig_income" value="yes" required>
    <label for="gig_income_yes" class="yn-btn outline">Yes</label>

    <input type="radio" id="gig_income_no" name="gig_income" value="no" required>
    <label for="gig_income_no" class="yn-btn outline">No</label>
  </div>
</div>

<!-- Shown only when gig_income = yes -->
<div id="gig-expenses-block" style="display:none;">
  <div class="qs-block">
    <label class="qs-label">Summary of Expenses <span class="qs-note">*</span></label>
    <div class="fi-group fi-float fi-span2" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
      <textarea id="gig_expenses_summary" name="gig_expenses_summary" class="fi-input" rows="5" placeholder=" " style="border: none; padding: 0;" required><?= htmlspecialchars($rowUser['summary_expenses'] ?? '') ?></textarea>
    </div>
    <p class="qs-help qs-help-alert" style="margin-top: 12px;">
      <strong>Tip:</strong> List the business-related costs you incurred (e.g., gas, car maintenance, phone bills, etc.)
    </p>
  </div>

  <div class="qs-block" id="hst-q-block" style="margin-top: 24px;">
    <label class="qs-label">Do you want to file HST for your Uber/Skip/Lyft/Doordash? <span class="qs-note">*</span></label>
    <div class="yn-group">
      <input type="radio" id="hst_yes" name="gig_hst" value="yes" required>
      <label for="hst_yes" class="yn-btn outline">Yes</label>

      <input type="radio" id="hst_no" name="gig_hst" value="no" required>
      <label for="hst_no" class="yn-btn outline">No</label>
    </div>
  </div>

  <!-- HST fields (hst_yes only) -->
  <div id="hst-fields" style="display:none; margin-top: 24px;">
    <div class="fi-grid">
      <div class="fi-group fi-float">
        <input id="hst_number" name="hst_number" class="fi-input" placeholder=" " required value="<?= htmlspecialchars($rowUser['hst_number'] ?? '') ?>">
        <label class="fi-float-label" for="hst_number">HST # <span class="qs-note">*</span></label>
      </div>
      <div class="fi-group fi-float">
        <input id="hst_access" name="hst_access" class="fi-input" placeholder=" " required value="<?= htmlspecialchars($rowUser['hst_access_code'] ?? '') ?>">
        <label class="fi-float-label" for="hst_access">Access code <span class="qs-note">*</span></label>
      </div>

      <div class="fi-group fi-float">
        <input id="hst_start" name="hst_start" class="fi-input dob-input" placeholder=" " required value="<?= htmlspecialchars($rowUser['hst_start_date'] ?? '') ?>">
        <label class="fi-float-label" for="hst_start">Start Date <span class="qs-note">*</span></label>
      </div>
      <div class="fi-group fi-float">
        <input id="hst_end" name="hst_end" class="fi-input dob-input" placeholder=" " required value="<?= htmlspecialchars($rowUser['hst_end_date'] ?? '') ?>">
        <label class="fi-float-label" for="hst_end">End Date <span class="qs-note">*</span></label>
      </div>
    </div>
  </div>
</div>
                        
<!-- Gig / Delivery income — SPOUSE -->

<div class="qs-block" id="sp-gig-question" style="display:none; margin-top:24px;">
  <label class="qs-label">
    Does your spouse have income from Uber/Skip/Lyft/Doordash etc.? <span class="qs-note">*</span>
  </label>
  <div class="yn-group" style="margin-bottom:0;">
    <input type="radio" id="sp_gig_income_yes" name="sp_gig_income" value="yes" required>
    <label for="sp_gig_income_yes" class="yn-btn outline">Yes</label>

    <input type="radio" id="sp_gig_income_no" name="sp_gig_income" value="no" required>
    <label for="sp_gig_income_no" class="yn-btn outline">No</label>
  </div>
</div>

<!-- Shown only when sp_gig_income = yes -->
<div id="sp-gig-expenses-block" style="display:none;">
  <div class="qs-block">
    <label class="qs-label">Spouse – Summary of Expenses <span class="qs-note">*</span></label>
    <div class="fi-group fi-float fi-span2" style="border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
      <textarea id="sp_gig_expenses_summary" name="sp_gig_expenses_summary" class="fi-input" rows="5" placeholder=" " style="border: none; padding: 0;" required><?= htmlspecialchars($rowUser['spouse_summary_expenses'] ?? '') ?></textarea>
    </div>
    <p class="qs-help qs-help-alert" style="margin-top: 12px;">
      <strong>Tip:</strong> List the business-related costs you incurred (e.g., gas, car maintenance, phone bills, etc.)
    </p>
  </div>

  <div class="qs-block" id="sp-hst-q-block" style="margin-top: 24px;">
    <label class="qs-label">File HST for spouse's Uber/Skip/Lyft/Doordash? <span class="qs-note">*</span></label>
    <div class="yn-group">
      <input type="radio" id="sp_hst_yes" name="sp_gig_hst" value="yes" required>
      <label for="sp_hst_yes" class="yn-btn outline">Yes</label>

      <input type="radio" id="sp_hst_no" name="sp_gig_hst" value="no" required>
      <label for="sp_hst_no" class="yn-btn outline">No</label>
    </div>
  </div>

  <!-- HST fields (sp_hst_yes only) -->
  <div id="sp-hst-fields" style="display:none; margin-top: 24px;">
    <div class="fi-grid">
      <div class="fi-group fi-float">
        <input id="sp_hst_number" name="sp_hst_number" class="fi-input" placeholder=" " required value="<?= htmlspecialchars($rowSpouseTax['sp_hst_number'] ?? '') ?>">
        <label class="fi-float-label" for="sp_hst_number">HST # <span class="qs-note">*</span></label>
      </div>
      <div class="fi-group fi-float">
        <input id="sp_hst_access" name="sp_hst_access" class="fi-input" placeholder=" " required value="<?= htmlspecialchars($rowSpouseTax['sp_hst_access'] ?? '') ?>">
        <label class="fi-float-label" for="sp_hst_access">Access code <span class="qs-note">*</span></label>
      </div>
      <div class="fi-group fi-float">
        <input id="sp_hst_start" name="sp_hst_start" class="fi-input dob-input" placeholder=" " required value="<?= htmlspecialchars($rowSpouseTax['sp_hst_start'] ?? '') ?>">
        <label class="fi-float-label" for="sp_hst_start">Start Date <span class="qs-note">*</span></label>
      </div>
      <div class="fi-group fi-float">
        <input id="sp_hst_end" name="sp_hst_end" class="fi-input dob-input" placeholder=" " required value="<?= htmlspecialchars($rowSpouseTax['sp_hst_end'] ?? '') ?>">
        <label class="fi-float-label" for="sp_hst_end">End Date <span class="qs-note">*</span></label>
      </div>
    </div>
  </div>
</div>
                        
                        
<!-- Rental income question -->
<div class="qs-block" style="margin-top:24px;">
  <label class="qs-label">Do you have rental income? <span class="qs-note">*</span></label>
  <div class="yn-group" style="margin-bottom:0;">
    <input type="radio" id="rental_yes" name="rentalIncome" value="yes" required>
    <label for="rental_yes" class="yn-btn outline">Yes</label>

    <input type="radio" id="rental_no" name="rentalIncome" value="no" required>
    <label for="rental_no" class="yn-btn outline">No</label>
  </div>
</div>

<!-- Rental properties (shown only if rentalIncome = yes) -->
<div id="rental-income-section" style="display:none;">
  <div class="qs-block">
    <label class="qs-title small">Add your rental properties</label>
    <div class="qs-help">Add each property below. You can edit or remove entries anytime.</div>
  </div>

  <!-- Add button (top) -->
  <div id="add-prop-wrap-top" style="margin:10px 0 16px; display:flex; justify-content:flex-end;">
    <button type="button" id="btn-add-property" class="tax-btn">Add Property</button>
  </div>

  <!-- Table -->
  <div class="qs-block">
    <table id="rental-table" class="rental-table props-table" style="width:100%; border-collapse:collapse;">
      <thead>
        <tr>
          <th>Property Address</th>
          <th>Start</th>
          <th>End</th>
          <th>Business Partner</th>
          <th>Ownership %</th>
          <th>Own Use %</th>
          <th>Gross Income (CAD)</th>
          <th style="text-align:center;">Actions</th>
        </tr>
      </thead>
      <tbody id="props-tbody">
        <tr id="props-empty-row">
          <td colspan="8" style="padding:10px; opacity:.7; text-align: center;">No properties added yet.</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Hidden inputs for your form POST -->
  <div id="props-hidden-inputs"></div>

  <!-- Add button (bottom mount) -->
  <div id="add-prop-wrap-bottom" style="margin:16px 0 0; display:none; justify-content:flex-end;"></div>
</div>

<!-- ===== Property Modal (2-step) ===== -->
<div id="prop-modal" class="qs-modal" hidden>
  <div class="qs-modal__backdrop"></div>
  <div class="qs-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="prop-modal-title">
    <div class="qs-modal__head">
      <h2 class="qs-title prop-title" id="prop-modal-title">Add Property</h2>
       <button type="button" class="qs-modal__close" id="prop-close" aria-label="Close">×</button>

</button>
    </div>

    <div class="qs-modal__body">

      <form id="prop-form" novalidate>
        <input type="hidden" id="prop_id">

        <!-- ===== STEP 1: DETAILS ===== -->
        <div id="prop-step1">
          <div class="fi-grid">
            <div class="fi-group fi-float">
              <input id="prop_owner_name" class="fi-input" placeholder=" ">
              <label class="fi-float-label" for="prop_owner_name">Who is the owner of the property?</label>
            </div>

            <div class="fi-group fi-float">
              <input id="prop_address" class="fi-input" placeholder=" " required>
              <label class="fi-float-label" for="prop_address">Property Address</label>
              <div id="prop-address-autocomplete" class="addr-suggest" hidden></div>
              <!-- Address data © OpenStreetMap contributors, ODbL -->
            </div>

            <div class="fi-group fi-float">
              <input id="prop_start_display" class="fi-input dob-input" placeholder=" "
                     data-dob-mode="ymd" data-bind="#prop_start_iso" required>
              <label class="fi-float-label" for="prop_start_display">Start — MM | DD | YYYY</label>
              <input type="hidden" id="prop_start_iso">
            </div>

            <div class="fi-group fi-float">
              <input id="prop_end_display" class="fi-input dob-input" placeholder=" "
                     data-dob-mode="ymd" data-bind="#prop_end_iso">
              <label class="fi-float-label" for="prop_end_display">End — MM | DD | YYYY</label>
              <input type="hidden" id="prop_end_iso">
            </div>

            <div class="fi-group fi-float fi-span2">
              <input id="prop_partner" class="fi-input" placeholder=" ">
              <label class="fi-float-label" for="prop_partner">Business Partner (if any)</label>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input type="number" id="prop_owner_pct" class="fi-input with-suffix" placeholder=" " min="0" max="100" step="1">
              <label class="fi-float-label" for="prop_owner_pct">Ownership Percentage</label>
              <span class="fi-suffix">%</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input type="number" id="prop_ownuse_pct" class="fi-input with-suffix" placeholder=" " min="0" max="100" step="1">
              <label class="fi-float-label" for="prop_ownuse_pct">Own Use Percentage</label>
              <span class="fi-suffix">%</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input id="prop_gross" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_gross">Gross Income</label>
              <span class="fi-suffix">CAD</span>
            </div>
          </div>
        </div>

        <!-- ===== STEP 2: EXPENSES ===== -->
        <div id="prop-step2" style="display:none;">
         
          <div class="fi-grid">
            <div class="fi-group fi-float fi-suf">
              <input id="prop_exp_mortgage" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_mortgage">Mortgage Interest</label>
              <span class="fi-suffix">CAD</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input id="prop_exp_insurance" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_insurance">Insurance</label>
              <span class="fi-suffix">CAD</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input id="prop_exp_repairs" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_repairs">Repairs and Maintenance</label>
              <span class="fi-suffix">CAD</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input id="prop_exp_utilities" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_utilities">Utilities</label>
              <span class="fi-suffix">CAD</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input id="prop_exp_internet" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_internet">Internet</label>
              <span class="fi-suffix">CAD</span>
            </div>

            <div class="fi-group fi-float fi-suf">
              <input id="prop_exp_propertytax" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_propertytax">Property tax</label>
              <span class="fi-suffix">CAD</span>
            </div>

            <div class="fi-group fi-float fi-span2 fi-suf">
              <input id="prop_exp_other" class="fi-input with-suffix" inputmode="decimal" placeholder=" ">
              <label class="fi-float-label" for="prop_exp_other" style="text-align: left;">Other Expenses</label>
              <span class="fi-suffix">CAD</span>
            </div>
          </div>
        </div>
      </form>

    </div>

    <!-- Footers per step -->
<!-- Step 1 footer (now has Cancel + Continue) -->
<div class="qs-modal__foot" id="prop-foot-1">
  <button type="button" class="tax-btn-secondary" id="prop-cancel-1">Cancel</button>
  <button type="button" class="tax-btn" id="prop-next">Continue</button>
</div>


    <div class="qs-modal__foot" id="prop-foot-2" style="display:none;">
      <button type="button" class="tax-btn-secondary" id="prop-back">Back</button>
      <button type="button" class="tax-btn" id="prop-save">Save</button>
    </div>
  </div>
</div>


<!-- RENT DELETE CONFIRM -->
<div id="rent-confirm" class="qs-modal" style="display:none;">
  <div class="qs-modal__backdrop"></div>
  <div class="qs-modal__dialog" role="dialog" aria-modal="true" style="width:min(520px,92vw);">
    <div class="qs-modal__head">
      <h2 class="qs-title small" style="margin-top: 0 !important;">Remove address?</h2>
    </div>
    <div class="qs-modal__body">
      <p class="qs-lead" id="rent-confirm-text">This will delete the selected record.</p>
    </div>
    <div class="qs-modal__foot">
      <button type="button" class="tax-btn-secondary" id="rent-confirm-cancel">Cancel</button>
      <button type="button" class="tax-btn" id="rent-confirm-yes">Delete</button>
    </div>
  </div>
</div>
          
 
                  
  <!-- CTA -->
   <div class="tax-cta tax-cta-row" style="margin-top:58px;">
         <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
         <button type="button" class="continue-btn" data-goto="next">Continue</button>
  </div>
</div>




                        
                        
<!-- UPLOAD PANEL -->
<!-- UPLOAD – APPLICANT (was tab content) -->
<style>
/* Add spacing between upload sections */
.pi-main[data-panel="upload-self"] .qs-block,
.pi-main[data-panel="upload-spouse"] .qs-block {
  margin-bottom: 50px;
}
.pi-main[data-panel="upload-self"] .qs-block:last-child,
.pi-main[data-panel="upload-spouse"] .qs-block:last-child {
  margin-bottom: 0; /* Remove margin from last section */
}
/* Add spacing after gig income sections when visible */
#upload-gig-section,
#sp-upload-gig-section {
  margin-bottom: 50px;
}
</style>
<div class="pi-main" data-panel="upload-self">
  <h2 class="qs-title" style="margin-bottom: 24px;"><?= htmlspecialchars($rowUser['first_name'] ?? 'Your') ?>'s Documents</h2>
  <section aria-labelledby="upload-self-title">
    <h2 class="visually-hidden" id="upload-self-title">Applicant Uploads</h2>

    <!-- Self Employed Uploads (kept hidden by your existing toggles) -->
    <div id="upload-gig-section" style="display:none;">
      <div class="qs-block">
        <label class="qs-label">Add Your Self-employed Income <span class="qs-note">*</span></label>
        <div class="dropzone" id="gig-drop">
          <input id="gig_tax_summary" name="gig_tax_summary[]" type="file" multiple style="display:none">
          <div class="dropzone-ui">
            <span>Drag files here or</span>
            <button type="button" class="tax-btn" id="gig-browse">Browse</button>
          </div>
        </div>
        <div id="gig-files" class="dropzone-list"></div>
      </div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">ID Proof <span class="qs-note">*</span></h3>
      <p class="qs-help">In order to verify your identity, Please provide your ID proof. Examples: Driver license, passport.</p>
      <div class="dropzone" data-input="#app_id_proof" data-list="#app_id_list">
        <input id="app_id_proof" name="app_id_proof[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_id_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">T4 / T4A / T Slips</h3>
      <p class="qs-help">(If password-protected, include passwords in the message box.)</p>
      <div class="dropzone" data-input="#app_tslips" data-list="#app_tslips_list" data-password="true">
        <input id="app_tslips" name="app_tslips[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_tslips_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">T2202 (College Receipt)</h3>
      <p class="qs-help">Provide all T2202 college fee receipts if you want to claim credits.</p>
      <div class="dropzone" data-input="#app_t2202_receipt" data-list="#app_t2202_receipt_list">
        <input id="app_t2202_receipt" name="app_t2202_receipt[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_t2202_receipt_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">RRSP / FHSA / Investment Receipts</h3>
      <div class="dropzone" data-input="#app_invest" data-list="#app_invest_list">
        <input id="app_invest" name="app_invest[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_invest_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">Work from home / Employment expenses (T2200)</h3>
      <div class="dropzone" data-input="#app_t2200_work" data-list="#app_t2200_work_list">
        <input id="app_t2200_work" name="app_t2200_work[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_t2200_work_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">Summary of expenses</h3>
      <p class="qs-help">Expenses related to employment.</p>
      <div class="dropzone" data-input="#app_exp_summary" data-list="#app_exp_summary_list">
        <input id="app_exp_summary" name="app_exp_summary[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_exp_summary_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block">
      <h3 class="qs-label">Additional Documents</h3>
      <p class="qs-help">Anything not listed above.</p>
      <div class="dropzone" data-input="#app_otherdocs" data-list="#app_otherdocs_list">
        <input id="app_otherdocs" name="app_otherdocs[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="app_otherdocs_list" class="dropzone-list"></div>
    </div>

    <!-- CTA -->
    <div class="tax-cta tax-cta-row" style="margin-top:58px;">
      <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
      <button type="button" class="continue-btn" data-goto="next">Continue</button>
    </div>
  </section>
</div>

<!-- UPLOAD – SPOUSE (was tab content; gated by spouse wants to file) -->
<div class="pi-main" data-panel="upload-spouse">
  <h2 class="qs-title" style="margin-bottom: 24px;">
    <?php
      $spouseName = trim($rowSpouse['first_name'] ?? '');
      echo htmlspecialchars($spouseName !== '' ? $spouseName : 'Spouse') . "'s Documents";
    ?>
  </h2>
  <section aria-labelledby="upload-spouse-title">
    <h2 class="visually-hidden" id="upload-spouse-title">Spouse Uploads</h2>

<!-- Spouse – Self Employed Uploads (visible only if sp_gig_income = yes) -->
<div id="sp-upload-gig-section" style="display:none;">
  <div class="qs-block">
    <label class="qs-label">Spouse – Add Self-employed Income <span class="qs-note">*</span></label>
    <div class="dropzone" id="sp-gig-drop">
      <input id="sp_gig_tax_summary" name="sp_gig_tax_summary[]" type="file" multiple style="display:none">
      <div class="dropzone-ui">
        <span>Drag files here or</span>
        <button type="button" class="tax-btn" id="sp-gig-browse">Browse</button>
      </div>
    </div>
    <div id="sp-gig-files" class="dropzone-list"></div>
  </div>
    </div>


    <div class="qs-block" style="margin-top: 24px;">
      <h3 class="qs-label">ID Proof <span class="qs-note">*</span></h3>
      <p class="qs-help">Spouse ID (driver license, passport, etc.).</p>
      <div class="dropzone" data-input="#sp_id_proof" data-list="#sp_id_list">
        <input id="sp_id_proof" name="sp_id_proof[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="sp_id_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block" style="margin-top: 50px;">
      <h3 class="qs-label">RRSP / FHSA / Investment Receipts</h3>
      <div class="dropzone" data-input="#sp_invest" data-list="#sp_invest_list">
        <input id="sp_invest" name="sp_invest[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="sp_invest_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block" style="margin-top: 50px;">
      <h3 class="qs-label">T2202 (College Receipt)</h3>
      <p class="qs-help">Provide spouse's T2202 receipts if applicable.</p>
      <div class="dropzone" data-input="#sp_t2202" data-list="#sp_t2202_list">
        <input id="sp_t2202" name="sp_t2202[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="sp_t2202_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block" style="margin-top: 50px;">
      <h3 class="qs-label">T4 / T4A / T Slips</h3>
      <p class="qs-help">(If password-protected, include passwords in the message box.)</p>
      <div class="dropzone" data-input="#sp_tslips" data-list="#sp_tslips_list" data-password="true">
        <input id="sp_tslips" name="sp_tslips[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="sp_tslips_list" class="dropzone-list"></div>
    </div>

    <div class="qs-block" style="margin-top: 50px;">
      <h3 class="qs-label">Additional Documents</h3>
      <p class="qs-help">Anything not listed above.</p>
      <div class="dropzone" data-input="#sp_otherdocs" data-list="#sp_otherdocs_list">
        <input id="sp_otherdocs" name="sp_otherdocs[]" type="file" multiple style="display:none">
        <div class="dropzone-ui">Drag files here or <button type="button" class="tax-btn dz-browse">Browse</button></div>
      </div>
      <div id="sp_otherdocs_list" class="dropzone-list"></div>
    </div>

    <!-- CTA -->
    <div class="tax-cta tax-cta-row" style="margin-top:58px;">
      <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
      <button type="button" class="continue-btn" data-goto="next">Continue</button>
    </div>
  </section>
</div>

                        

                        
<!-- REVIEW PANEL -->                        
<!-- ===== REVIEW PANEL ===== -->
<div class="pi-main" data-panel="review" hidden>
  <section class="rev-wrap">
    <div class="rev-card" role="region" aria-labelledby="rev-heading">

      <div class="rev-accordion" id="rev-accordion">
        <!-- PRE-DETAILS -->
        <button class="rev-item" type="button" aria-expanded="true" aria-controls="rev-pre" id="rev-pre-btn">
          <span class="rev-left">
            <span>Pre-Details</span>
          </span>
          <span class="rev-icon" aria-hidden="true"></span>
        </button>
        <div id="rev-pre" class="rev-panel" role="region" aria-labelledby="rev-pre-btn">
         <dl class="rev-dl">
  <dt>Marital Status</dt>
  <dd><span data-bind-text="#marital_status_select,[name='marital_status']:checked" data-fallback="—"></span></dd>

  <!-- Shown only for Married / Common Law -->
  <dt class="pre-cond">Date</dt>
  <dd class="pre-cond">
    <span data-bind-text="#status_date,#status_date_sdw" data-format="date" data-fallback="—"></span>
  </dd>

  <dt class="pre-cond">Residing in Canada?</dt>
  <dd class="pre-cond"><span data-bind-radio="spouse_in_canada" data-fallback="—"></span></dd>

  <dt class="pre-cond">Spouse will file with us?</dt>
  <dd class="pre-cond"><span data-bind-radio="spouseFile" data-fallback="—"></span></dd>

  <dt class="pre-cond">Do you have children?</dt>
  <dd class="pre-cond"><span data-bind-radio="children" data-fallback="—"></span></dd>
</dl>
          <div class="rev-actions"><a href="#" class="rev-link" data-open="pre">Go to Pre-details</a></div>
        </div>

        <!-- PERSONAL -->
   <!-- PERSONAL (Review) -->
<button class="rev-item" type="button" aria-expanded="false" aria-controls="rev-personal" id="rev-personal-btn">
  <span class="rev-left"><span>Personal Information</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>
<div id="rev-personal" class="rev-panel" role="region" aria-labelledby="rev-personal-btn" hidden>
  <dl class="rev-dl">
    <dt>Full Name</dt>
    <dd>
      <span data-bind-text="#first_name" data-fallback="—"></span>
      <span data-bind-text="#middle_name"></span>
      <span data-bind-text="#last_name" data-fallback=""></span>
    </dd>

    <dt>Date of Birth</dt>
    <dd><span data-bind-text="#dob" data-format="date" data-fallback="—"></span></dd>

    <dt>SIN</dt>
    <dd><span data-bind-text="#sin" data-mask="sin" data-fallback="—"></span></dd>

    <dt>Gender</dt>
    <dd><span data-bind-radio="gender" data-fallback="—"></span></dd>

    <dt>Street</dt>
    <dd><span data-bind-text="#street" data-fallback="—"></span></dd>

    <dt>Unit / Suite</dt>
    <dd><span data-bind-text="#unit" data-fallback="—"></span></dd>

    <dt>City</dt>
    <dd><span data-bind-text="#city" data-fallback="—"></span></dd>

    <dt>Province</dt>
    <dd><span data-bind-text="#province" data-fallback="—"></span></dd>

    <dt>Postal Code</dt>
    <dd><span data-bind-text="#postal" data-fallback="—"></span></dd>

    <dt>Country</dt>
    <dd><span data-bind-text="#country" data-fallback="—"></span></dd>

    <dt>Phone</dt>
    <dd><span data-bind-text="#phone" data-mask="phone" data-fallback="—"></span></dd>

    <dt>Email</dt>
    <dd><span data-bind-text="#email" data-fallback="—"></span></dd>
  </dl>

  <div class="rev-actions">
    <a href="#" class="rev-link" data-open="personal">Go to Personal</a>
  </div>
</div>


    <!-- TAX FILING (Review) -->
<button class="rev-item" type="button" aria-expanded="false" aria-controls="rev-tax" id="rev-tax-btn">
  <span class="rev-left"><span>Tax Filing Information</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>

<div id="rev-tax" class="rev-panel" role="region" aria-labelledby="rev-tax-btn" hidden>

  <!-- SUMMARY (always shown) -->
  <dl class="rev-dl">
    <dt>First-time filer?</dt>
    <dd><span data-bind-radio="first_time" data-fallback="—"></span></dd>
  </dl>

  <!-- FIRST-TIME DETAILS (shown when first_time = yes) -->
  <div class="rt-first" hidden>
    <dl class="rev-dl">
      <dt>Date of Entry</dt>
      <dd><span data-bind-text="#entry_date_display,#entry_date" data-format="date" data-fallback="—"></span></dd>

      <dt>Country of Previous Residency</dt>
      <dd><span data-bind-text="#birth_country" data-fallback="—"></span></dd>

      <dt>World Income – Year 1</dt>
      <dd><span data-bind-text="#period_y1" data-fallback="—"></span> : <span data-bind-text="#inc_y1" data-fallback="—"></span></dd>

      <dt>World Income – Year 2</dt>
      <dd><span data-bind-text="#period_y2" data-fallback="—"></span> : <span data-bind-text="#inc_y2" data-fallback="—"></span></dd>

      <dt>World Income – Year 3</dt>
      <dd><span data-bind-text="#period_y3" data-fallback="—"></span> : <span data-bind-text="#inc_y3" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- PRIOR (shown when first_time = no) -->
  <div class="rt-prior" hidden>
    <dl class="rev-dl">
      <dt>Filed with Paragon before?</dt>
      <dd><span data-bind-radio="paragon_prior" data-fallback="—"></span></dd>

      <dt>Years to file</dt>
      <dd><span data-bind-text="#return_years" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- SUMMARY (always shown) -->
  <dl class="rev-dl">
    <dt>Moved to another province?</dt>
    <dd><span data-bind-radio="moved_province" data-fallback="—"></span></dd>
  </dl>

  <!-- MOVED DETAILS (when moved_province = yes) -->
  <div class="rt-moved" hidden>
    <dl class="rev-dl">
      <dt>Move Date</dt>
      <dd><span data-bind-text="#moved_date_display,#moved_date_iso" data-format="date" data-fallback="—"></span></dd>

      <dt>From → To</dt>
      <dd>
        <span data-bind-text="#prov_from" data-fallback="—"></span>
        →
        <span data-bind-text="#prov_to" data-fallback="—"></span>
      </dd>
    </dl>
  </div>

  <!-- SUMMARY (always shown) -->
  <dl class="rev-dl">
    <dt>Claim moving expenses?</dt>
    <dd><span data-bind-radio="moving_expenses_claim" data-fallback="—"></span></dd>
  </dl>

  <!-- MOVING EXPENSES DETAILS (when moving_expenses_claim = yes) -->
  <div class="rt-movexp" hidden>
    <dl class="rev-dl">
      <dt>Previous address</dt>
      <dd><span data-bind-text="#moving_prev_address" data-fallback="—"></span></dd>
      <dt>Distance (prev → current)</dt>
      <dd><span data-bind-text="#moving_distance" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- SUMMARY (always shown) -->
  <dl class="rev-dl">
    <dt>First-time home buyer?</dt>
    <dd><span data-bind-radio="first_home_buyer" data-fallback="—"></span></dd>
  </dl>

  <!-- FTHB DETAILS (when first_home_buyer = yes) -->
  <div class="rt-fthb" hidden>
    <dl class="rev-dl">
      <dt>First home purchase</dt>
      <dd><span data-bind-text="#first_home_purchase_display,#first_home_purchase" data-format="date" data-fallback="—"></span></dd>

      <dt>Sole owner?</dt>
      <dd><span data-bind-radio="claim_full" data-fallback="—"></span></dd>
    </dl>

    <!-- co-owners only when NOT sole owner -->
    <div class="rt-coowners" hidden>
      <dl class="rev-dl">
        <dt># of owners (incl. you)</dt>
        <dd><span data-bind-text="#owner_count" data-fallback="—"></span></dd>
      </dl>
    </div>
  </div>

  <!-- SUMMARY (always shown) -->
  <dl class="rev-dl">
    <dt>Living on rent?</dt>
    <dd><span data-bind-radio="onRent" data-fallback="—"></span></dd>
  </dl>

  <!-- RENT DETAILS (when onRent = yes) -->
  <div class="rt-rent" hidden>
    <dl class="rev-dl">
      <dt>Rent addresses added</dt>
      <dd><span id="rent-row-count">0</span></dd>
    </dl>
  </div>

  <div class="rev-actions">
    <a href="#" class="rev-link" data-open="tax">Go to Tax Filing</a>
  </div>
</div>

        <!-- SPOUSE -->
<!-- SPOUSE (Review) -->
<button class="rev-item" type="button" aria-expanded="false" aria-controls="rev-spouse" id="rev-spouse-btn">
  <span class="rev-left"><span>Spouse Information</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>
<div id="rev-spouse" class="rev-panel" role="region" aria-labelledby="rev-spouse-btn" hidden>

  <!-- Summary (always show) -->
  <dl class="rev-dl">
    <dt>Full Name</dt>
    <dd>
      <span data-bind-text="#spouse_first_name" data-fallback="—"></span>
      <span data-bind-text="#spouse_middle_name"></span>
      <span data-bind-text="#spouse_last_name" data-fallback=""></span>
    </dd>

    <dt>Date of Birth</dt>
    <dd><span data-bind-text="#spouse_dob" data-format="date" data-fallback="—"></span></dd>

    <dt>Residing in Canada?</dt>
    <dd><span data-bind-radio="spouse_in_canada" data-fallback="—"></span></dd>

    <dt>Spouse will file with us?</dt>
    <dd><span data-bind-radio="spouseFile" data-fallback="—"></span></dd>
  </dl>

  <!-- When NOT in Canada -->
  <div class="rs-foreign" hidden>
    <dl class="rev-dl">
      <dt>Annual income outside Canada (CAD)</dt>
      <dd><span data-bind-text="#spouse_income_outside_cad" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- When in Canada -->
  <div class="rs-canada" hidden>
    <dl class="rev-dl">
      <dt>SIN</dt>
      <dd><span data-bind-text="#spouse_sin" data-mask="sin" data-fallback="—"></span></dd>

      <dt>Address</dt>
      <dd class="rs-addr-same" hidden><em id="rs-addr-same-text">Same as your address</em></dd>
      <dd class="rs-addr-fields" hidden>
        <span data-bind-text="#spouse_street" data-fallback="—"></span>
        <span data-bind-text="#spouse_unit"></span>,
        <span data-bind-text="#spouse_city" data-fallback="—"></span>,
        <span data-bind-text="#spouse_province" data-fallback="—"></span>,
        <span data-bind-text="#spouse_postal" data-fallback="—"></span>,
        <span data-bind-text="#spouse_country" data-fallback="—"></span>
      </dd>

      <dt>Phone</dt>
      <dd><span data-bind-text="#spouse_phone" data-mask="phone" data-fallback="—"></span></dd>

      <dt>Email</dt>
      <dd><span data-bind-text="#spouse_email" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- Only when spouse will NOT file -->
  <div class="rs-income" hidden>
    <dl class="rev-dl">
      <dt>Spouse annual income (CAD)</dt>
      <dd><span data-bind-text="#spouse_income_cad" data-fallback="—"></span></dd>
    </dl>
  </div>

  <div class="rev-actions"><a href="#" class="rev-link" data-open="spouse">Go to Spouse</a></div>
</div>

<!-- SPOUSE TAX (Review) -->
<button class="rev-item" type="button" aria-expanded="false" aria-controls="rev-spouse-tax" id="rev-spouse-tax-btn">
  <span class="rev-left"><span>Spouse Tax Filing Information</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>
<div id="rev-spouse-tax" class="rev-panel" role="region" aria-labelledby="rev-spouse-tax-btn" hidden>

  <!-- Summary (always show) -->
  <dl class="rev-dl">
    <dt>First-time filer?</dt>
    <dd><span data-bind-radio="sp_first_time" data-fallback="—"></span></dd>
  </dl>

  <!-- First-time details -->
  <div class="rst-first" hidden>
    <dl class="rev-dl">
      <dt>Date of Entry</dt>
      <dd><span data-bind-text="#sp_entry_date_display,#sp_entry_date" data-format="date" data-fallback="—"></span></dd>

      <dt>Country of Previous Residency</dt>
      <dd><span data-bind-text="#sp_birth_country" data-fallback="—"></span></dd>

      <dt>World Income – Year 1</dt>
      <dd><span data-bind-text="#sp_period_y1" data-fallback="—"></span> : <span data-bind-text="#sp_inc_y1" data-fallback="—"></span></dd>

      <dt>World Income – Year 2</dt>
      <dd><span data-bind-text="#sp_period_y2" data-fallback="—"></span> : <span data-bind-text="#sp_inc_y2" data-fallback="—"></span></dd>

      <dt>World Income – Year 3</dt>
      <dd><span data-bind-text="#sp_period_y3" data-fallback="—"></span> : <span data-bind-text="#sp_inc_y3" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- Prior details -->
  <div class="rst-prior" hidden>
    <dl class="rev-dl">
      <dt>Filed with Paragon before?</dt>
      <dd><span data-bind-radio="sp_paragon_prior" data-fallback="—"></span></dd>

      <dt>Years to file</dt>
      <dd><span data-bind-text="#sp_return_years" data-fallback="—"></span></dd>
    </dl>
  </div>

  <!-- Move summary -->
  <dl class="rev-dl">
    <dt>Moved to another province?</dt>
    <dd><span data-bind-radio="sp_moved_province" data-fallback="—"></span></dd>
  </dl>

  <!-- Move details -->
  <div class="rst-moved" hidden>
    <dl class="rev-dl">
      <dt>Move Date</dt>
      <dd>
        <span
          data-bind-text="#sp_moved_date_display,#sp_moved_date_iso,#moved_date_display,#moved_date_iso"
          data-format="date" data-fallback="—"></span>
      </dd>

      <dt>From → To</dt>
      <dd>
        <span data-bind-text="#sp_prov_from" data-fallback="—"></span>
        →
        <span data-bind-text="#sp_prov_to" data-fallback="—"></span>
      </dd>
    </dl>
  </div>

  <div class="rev-actions"><a href="#" class="rev-link" data-open="spouse-tax">Go to Spouse Tax</a></div>
</div>


        <!-- CHILDREN -->
       <!-- CHILDREN (Review) -->
<button class="rev-item" type="button" aria-expanded="false" aria-controls="rev-children" id="rev-children-btn">
  <span class="rev-left"><span>Children Information</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>
<div id="rev-children" class="rev-panel" role="region" aria-labelledby="rev-children-btn" hidden>

  <!-- Summary (always) -->
  <dl class="rev-dl">
    <dt>Do you have children?</dt>
    <dd><span data-bind-radio="children" data-fallback="—"></span></dd>

    <dt>Children added</dt>
    <dd><span id="children-count">0</span></dd>
  </dl>

  <!-- Detail list (shown when “children = yes” and rows exist) -->
  <div class="rc-list" hidden>
    <div class="qs-help" style="margin:0 0 8px;">Saved children</div>
    <table class="fi-table" style="width:100%; border-collapse:collapse;">
      <thead>
        <tr><th>First</th><th>Last</th><th>DOB</th><th>In Canada?</th></tr>
      </thead>
      <tbody id="children-list"></tbody>
    </table>
  </div>

  <div class="rev-actions"><a href="#" class="rev-link" data-open="children">Go to Children</a></div>
</div>


<!-- OTHER INCOME (Review, cleaned) -->
<button class="rev-item" type="button" aria-expanded="false" aria-controls="rev-other-income" id="rev-other-income-btn">
  <span class="rev-left"><span>Other Income</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>

<div id="rev-other-income" class="rev-panel" role="region" aria-labelledby="rev-other-income-btn" hidden>

  <!-- Gig summary -->
  <dl class="rev-dl">
    <dt>Gig / delivery income?</dt>
    <dd><span data-bind-radio="gig_income" data-fallback="—"></span></dd>

    <dt>Expenses summary</dt>
    <dd><span data-bind-text="#gig_expenses_summary" data-fallback="—"></span></dd>

    <dt>File HST for gig?</dt>
    <dd><span data-bind-radio="gig_hst" data-fallback="—"></span></dd>
  </dl>

  <!-- HST details (only when gig_hst = yes) -->
  <div class="ro-hst" hidden>
    <dl class="rev-dl">
      <dt>HST #</dt><dd><span data-bind-text="#hst_number" data-fallback="—"></span></dd>
      <dt>Access code</dt><dd><span data-bind-text="#hst_access" data-fallback="—"></span></dd>
      <dt>HST Period</dt>
      <dd>
        <span data-bind-text="#hst_start" data-format="date" data-fallback="—"></span>
        –
        <span data-bind-text="#hst_end" data-format="date" data-fallback="—"></span>
      </dd>
    </dl>
  </div>

  <!-- Rentals -->
<dl class="rev-dl">
  <dt>Rental properties</dt>
  <dd><span id="rev-props-count">0</span></dd>
</dl>

<ul id="rev-props-compact" class="prop-list" hidden></ul>
                        
  <!-- Compact, clean table -->
  <table class="rev-table" id="rev-props-table" hidden>
    <thead>
      <tr>
        <th>Address</th>
        <th>Start</th>
        <th>End</th>
        <th>Partner</th>
        <th>Ownership %</th>
        <th>Own Use %</th>
        <th>Gross (CAD)</th>
      </tr>
    </thead>
    <tbody id="rev-props-list"></tbody>
  </table>

  <div class="rev-actions"><a href="#" class="rev-link" data-open="other-income">Go to Other Income</a></div>
</div>



<!-- =========================
     REVIEW — UPLOADS (Applicant)
     ========================= -->
<button class="rev-item" type="button" aria-expanded="false"
        aria-controls="rev-upload-self" id="rev-upload-self-btn">
  <span class="rev-left"><span>ADD / UPLOAD DOCUMENT — Applicant</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>

<div id="rev-upload-self" class="rev-panel" role="region"
     aria-labelledby="rev-upload-self-btn" hidden>
  <h4 class="rev-subtitle" style="margin-top:4px;">Applicant</h4>
  <dl class="rev-dl">
    <dt>ID proof</dt>
    <dd>
      <span id="rev-app-id-count">0</span> file(s)
      <ul id="rev-app-id-names" class="rev-files" hidden></ul>
    </dd>

    <dt>T4 / T4A / T Slips</dt>
    <dd>
      <span id="rev-app-tslips-count">0</span> file(s)
      <ul id="rev-app-tslips-names" class="rev-files" hidden></ul>
    </dd>

    <dt>T2202 (College Receipt)</dt>
    <dd>
      <span id="rev-app-t2202-count">0</span> file(s)
      <ul id="rev-app-t2202-names" class="rev-files" hidden></ul>
    </dd>

    <dt>RRSP / FHSA / Investment</dt>
    <dd>
      <span id="rev-app-invest-count">0</span> file(s)
      <ul id="rev-app-invest-names" class="rev-files" hidden></ul>
    </dd>

    <dt>Work from home / T2200</dt>
    <dd>
      <span id="rev-app-t2200-count">0</span> file(s)
      <ul id="rev-app-t2200-names" class="rev-files" hidden></ul>
    </dd>

    <dt>Employment expenses summary</dt>
    <dd>
      <span id="rev-app-exp-count">0</span> file(s)
      <ul id="rev-app-exp-names" class="rev-files" hidden></ul>
    </dd>

    <dt>Additional documents</dt>
    <dd>
      <span id="rev-app-other-count">0</span> file(s)
      <ul id="rev-app-other-names" class="rev-files" hidden></ul>
    </dd>
  </dl>

  <!-- Applicant: self-employed (gig) uploads -->
  <div class="ru-gig" hidden>
    <dl class="rev-dl">
      <dt>Self-employed / gig income uploads</dt>
      <dd>
        <span id="rev-app-gig-count">0</span> file(s)
        <ul id="rev-app-gig-names" class="rev-files" hidden></ul>
      </dd>
    </dl>
  </div>

  <div class="rev-actions">
    <a href="#" class="rev-link" data-open="upload-self">Go to Applicant Uploads</a>
  </div>
</div>


<!-- =========================
     REVIEW — UPLOADS (Spouse)
     ========================= -->
<button class="rev-item" type="button" aria-expanded="false"
        aria-controls="rev-upload-spouse" id="rev-upload-spouse-btn" hidden>
  <span class="rev-left"><span>ADD / UPLOAD DOCUMENT — Spouse</span></span>
  <span class="rev-icon" aria-hidden="true"></span>
</button>

<div id="rev-upload-spouse" class="rev-panel" role="region"
     aria-labelledby="rev-upload-spouse-btn" hidden>
  <h4 class="rev-subtitle" style="margin-top:4px;">Spouse</h4>
  <dl class="rev-dl">
    <dt>ID proof</dt>
    <dd>
      <span id="rev-sp-id-count">0</span> file(s)
      <ul id="rev-sp-id-names" class="rev-files" hidden></ul>
    </dd>

    <dt>RRSP / FHSA / Investment</dt>
    <dd>
      <span id="rev-sp-invest-count">0</span> file(s)
      <ul id="rev-sp-invest-names" class="rev-files" hidden></ul>
    </dd>

    <dt>T2202 (College Receipt)</dt>
    <dd>
      <span id="rev-sp-t2202-count">0</span> file(s)
      <ul id="rev-sp-t2202-names" class="rev-files" hidden></ul>
    </dd>

    <dt>T4 / T4A / T Slips</dt>
    <dd>
      <span id="rev-sp-tslips-count">0</span> file(s)
      <ul id="rev-sp-tslips-names" class="rev-files" hidden></ul>
    </dd>

    <dt>Additional documents</dt>
    <dd>
      <span id="rev-sp-other-count">0</span> file(s)
      <ul id="rev-sp-other-names" class="rev-files" hidden></ul>
    </dd>
  </dl>

  <div class="rev-actions">
    <a href="#" class="rev-link" data-open="upload-spouse">Go to Spouse Uploads</a>
  </div>
</div>


        <!-- NOTE: Confirmation intentionally excluded from accordion -->
      </div>

      <!-- Notes FOR PREPARER (outside tabs) -->
      <div class="qs-block" style="margin-top: 24px;"> 
     <label class="qs-label">Your Message For Us?</label>
      <div class="fi-group fi-float fi-span2"> 
     <textarea id="other_message" name="other_message" class="fi-input" rows="6" placeholder=" "></textarea> 
        </div>
          </div>

      <!-- CTA -->
      <div class="tax-cta tax-cta-row" style="margin-top:52px;">
        <button type="button" class="tax-btn-secondary" data-goto="prev">Back</button>
        <button type="button" class="continue-btn" data-goto="next" id="review-submit-btn">Submit</button>
        <div id="review-submit-status" class="submit-status" aria-live="polite" hidden>
          Please keep this tab open while we process your submission.
        </div>
      </div>
    </div>
  </section>
</div>

              
                        
<div class="pi-main" data-panel="confirm" hidden>
  <section class="rev-wrap">
    <div class="rev-card" role="region" aria-labelledby="confirm-heading">
      <h2 id="confirm-heading" class="qs-title">Confirmation of Document Submission</h2>

      <div class="qs-block" style="margin-top: 10px;">
        <p class="qs-lead" style="margin:0;">
          We have successfully received your tax information submission. Our team will review your documents and get back to you soon.
        </p>
      </div>

      <div class="qs-block" style="margin-top: 14px;">
        <div class="qs-title small" style="margin: 0 0 12px;">What happens next?</div>
        <ul style="margin: 0; padding-left: 18px; color:#334155;">
          <li>Our tax professionals will review your submission</li>
          <li>We will contact you within 48 hours if we need any additional information</li>
          <li>You will receive updates on the status of your tax filing</li>
        </ul>
      </div>

      <div class="qs-block" style="margin-top: 14px;">
        <div class="qs-title small" style="margin: 0 0 12px;">Important Notes:</div>
        <ul style="margin: 0; padding-left: 18px; color:#334155;">
          <li>Please check your SPAM folder if you don't see our emails</li>
          <li>Keep this confirmation for your records</li>
          <li>If you have any questions, please contact us using the information below</li>
        </ul>
      </div>

      <div class="qs-block" style="margin-top: 14px;">
        <div class="qs-title small" style="margin: 0 0 12px;">Contact Information:</div>
        <div style="color:#334155;">
          Email: info@paragonafs.ca<br>
          Phone: +1 (416) 477 3359<br>
          Website: https://paragonafs.ca
        </div>
        <div style="margin-top: 12px; font-weight:700; color:#0f172a;">
          Thank you for choosing Paragon AFS!
        </div>
      </div>

      <div class="qs-block" style="margin-top: 18px;">
        <div class="qs-title small" style="margin: 0 0 12px;">Submission Details</div>
        <div id="confirm-accordion-mount"></div>
      </div>
    </div>
  </section>
</div>

    </div><!-- /pi-stage -->
  </section>
</div>

    
<!-- FOOTER -->      
      
<?php include_once 'footer.php'; ?>
    




<!-- DOB modal -->
<div id="dob-modal" class="dob-backdrop" hidden>
  <div class="dob-dialog" role="dialog" aria-modal="true" aria-labelledby="dob-title">

    <div class="dob-head">
      <button type="button" class="dob-icon" id="dob-back" aria-label="Back" hidden>
        <svg viewBox="0 0 24 24"><path d="M15 6l-6 6 6 6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
      </button>

      <h3 id="dob-title">Select a day</h3>

      <button type="button" class="dob-icon" id="dob-close" aria-label="Close">
        <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6L6 18" stroke-width="2" stroke-linecap="round"/></svg>
      </button>
    </div>

    <!-- arrows moved here -->
    <div class="dob-subhead" id="dob-subhead">
      <button class="dob-link dob-prev" id="dob-prev" aria-label="Previous">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M15 6l-6 6 6 6" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>

      <div id="dob-subhead-text"></div>

      <button class="dob-link" id="dob-next" aria-label="Next">
        <svg viewBox="0 0 24 24" aria-hidden="true">
          <path d="M9 6l6 6-6 6" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </button>
    </div>

    <div id="dob-body"></div>

    <!-- remove or keep hidden -->
    <!-- <div class="dob-foot"> … (deleted) … </div> -->

  </div>
</div>

  </div>
</div>


<script>
(function () {
  const $ = s => document.querySelector(s);
  const dash = v => { const t = (v ?? '').toString().trim(); return t && t !== '-' ? t : '—'; };

  /** Collect from hidden inputs (supports both props[...] and rental_props[...]) */
  function collectFromHidden() {
    const root = document.getElementById('props-hidden-inputs');
    if (!root) return [];

    const inputs = root.querySelectorAll(
      'input[name^="props["],input[name^="rental_props["],select[name^="rental_props["],textarea[name^="rental_props["]'
    );
    if (!inputs.length) return [];

    const map = new Map();

    inputs.forEach(el => {
      let m;

      // Flat style: props[i][field]  OR  rental_props[i][field]
      m = el.name.match(/^(?:props|rental_props)\[(\d+)\]\[([a-z_]+)\]$/i);
      if (m) {
        const i = +m[1], key = m[2];
        if (!map.has(i)) map.set(i, {});
        map.get(i)[key] = el.value;
        return;
      }

      // Nested expenses: rental_props[i][expenses][field]
      m = el.name.match(/^rental_props\[(\d+)\]\[expenses\]\[([a-z_]+)\]$/i);
      if (m) {
        const i = +m[1], key = 'exp_' + m[2];  // e.g. exp_property_tax
        if (!map.has(i)) map.set(i, {});
        map.get(i)[key] = el.value;
      }
    });

    // Normalize to a single shape the renderer expects
    return [...map.keys()].sort((a,b)=>a-b).map(i => {
      const r = map.get(i) || {};
      return {
        title:  dash(r.address || r.prop_address),         // primary title (address)
        owner:  dash(r.owner   || r.prop_owner_name),      // show owner, if present
        start:  dash(r.start_display || r.start_label),
        end:    dash(r.end_display   || r.end_label),
        partner:dash(r.partner || r.business_partner),
        ownPct: dash(r.owner_pct || r.own_pct || r.ownership || r.ownership_pct),
        usePct: dash(r.ownuse_pct || r.own_use || r.own_use_pct),
        gross:  dash(r.gross || r.gross_income),
        exp: {
          mortgage:   dash(r.exp_mortgage    || r.mortgage),
          insurance:  dash(r.exp_insurance   || r.insurance),
          repairs:    dash(r.exp_repairs     || r.repairs),
          utilities:  dash(r.exp_utilities   || r.utilities),
          internet:   dash(r.exp_internet    || r.internet),
          // ✅ check BOTH exp_property_tax (nested) and exp_propertytax (flat)
          propertytx: dash(r.exp_property_tax || r.exp_propertytax || r.property_tax || r.propertytx),
          other:      dash(r.exp_other       || r.other),
        }
      };
    });
  }

  function renderCompact(){
    const list  = $('#rev-props-compact');
    const count = $('#rev-props-count');
    if (!list || !count) return;

    const props = collectFromHidden();
    count.textContent = String(props.length);
    list.hidden = props.length === 0;
    list.innerHTML = '';

    props.forEach((p, idx) => {
      const li = document.createElement('li');
      li.className = 'prop-item';
      li.innerHTML = `
        <div class="prop-head">
          <div class="prop-title">${p.title}</div>
          <a href="#" class="prop-toggle-link" aria-expanded="false" aria-controls="prop-exp-${idx}">Expenses</a>
        </div>

        <div class="prop-meta">
          <span class="chip"><b>Start–End:</b> ${p.start} – ${p.end}</span>
          <span class="chip"><b>Partner:</b> ${p.partner}</span>
          <span class="chip"><b>Ownership %:</b> ${p.ownPct}</span>
          <span class="chip"><b>Own Use %:</b> ${p.usePct}</span>
          <span class="chip"><b>Gross (CAD):</b> ${p.gross}</span>
          <span class="chip"><b>Owner:</b> ${p.owner}</span>
        </div>

        <div class="prop-exp" id="prop-exp-${idx}" hidden>
          <table>
            <tr><td>Mortgage Interest</td><td>${p.exp.mortgage}</td><td>Insurance</td><td>${p.exp.insurance}</td></tr>
            <tr><td>Repairs & Maintenance</td><td>${p.exp.repairs}</td><td>Utilities</td><td>${p.exp.utilities}</td></tr>
            <tr><td>Internet</td><td>${p.exp.internet}</td><td>Property Tax</td><td>${p.exp.propertytx}</td></tr>
            <tr><td>Other</td><td>${p.exp.other}</td><td></td><td></td></tr>
          </table>
        </div>`;
      list.appendChild(li);
    });
  }

  // Event delegation for the toggle link
  document.addEventListener('click', (e) => {
    const a = e.target.closest('.prop-toggle-link');
    if (!a) return;
    e.preventDefault();
    const id = a.getAttribute('aria-controls');
    const panel = document.getElementById(id);
    const open = a.getAttribute('aria-expanded') === 'true';
    a.setAttribute('aria-expanded', String(!open));
    if (panel){ panel.hidden = open; panel.style.display = open ? 'none' : 'block'; }
  });

  // Initial paint + keep in sync
  renderCompact();
  const hiddenRoot = document.getElementById('props-hidden-inputs');
  if (hiddenRoot) {
    const mo = new MutationObserver(renderCompact);
    mo.observe(hiddenRoot, { childList:true, subtree:true, attributes:true, characterData:true });
  }
  document.addEventListener('props:hidden-updated', renderCompact);
})();
</script>


<!-- YES 1 -->      

<script>
(function onReady(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);})(function(){

  /* =========================
     MAIN APP (your original)
  ==========================*/
  const introCard    = document.getElementById('intro-card');
  const welcomePanel = document.getElementById('welcome-panel');
  const formPanel    = document.getElementById('form-panel');
  const sidebar      = document.querySelector('.pi-steps');
  const oldFormSidebar = document.querySelector('.old-form-sidebar');

  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

const ORDER = [
  'personal',
  'tax',
  'spouse',
  'spouse-tax',
  'children',
  'other-income',
  'upload-self',   // NEW – applicant uploads
  'upload-spouse', // NEW – spouse uploads (gated)
  'review',
  'confirm'
];

  let CURRENT = null;

  function getVal(name){
    const el = document.querySelector(`input[name="${name}"]:checked`);
    return el ? el.value : null;
  }

 function flags(){
  const ms = getVal('marital_status');
  const marriedLike      = (ms === 'Married' || ms === 'Common Law');
  const childQuestionVisible = (marriedLike || ms === 'Separated' || ms === 'Divorced' || ms === 'Widowed');
  const spouseFiles      = marriedLike && (getVal('spouseFile') === 'yes');
  const hasChildren      = childQuestionVisible && (getVal('children') === 'yes');
  const needsStatusDate  = (ms === 'Separated' || ms === 'Divorced' || ms === 'Widowed');

  const rentYes          = (getVal('rentalIncome') === 'yes');
  const selfYes          = (getVal('selfEmp') === 'yes');          // (legacy toggle, if you still use it)
  const gigIncomeYes     = (getVal('gig_income') === 'yes');       // ✅ NEW
  const onRentYes        = (getVal('onRent') === 'yes');
  const claimBenefit     = (getVal('rentBenefit') === 'yes');

  const otherIncomeNeeded= (rentYes || selfYes || gigIncomeYes);
  const benefitNeeded    = (onRentYes && claimBenefit);

  return {
    ms, marriedLike, spouseFiles,
    childQuestionVisible, hasChildren, needsStatusDate,
    rentYes, selfYes, gigIncomeYes, onRentYes, claimBenefit,
    otherIncomeNeeded, benefitNeeded,
    showOtherIncomePanel: true
  };
}

function activeSteps(){
  const f = flags();
  return ORDER.filter(step => {
    if (step === 'spouse')         return f.marriedLike;
    if (step === 'spouse-tax')     return f.spouseFiles;      // unchanged
    if (step === 'children')       return f.hasChildren;      // unchanged
    if (step === 'upload-self')    return true;               // always show
    if (step === 'upload-spouse')  return f.spouseFiles;      // only if spouse will file
    if (step === 'other-income')   return true;
    return true;
  });
}

  function nextFrom(key){
    const steps = activeSteps();
    const i = steps.indexOf(key);
    return steps[Math.min(i+1, steps.length-1)] || key;
  }
  function prevFrom(key){
    const steps = activeSteps();
    const i = steps.indexOf(key);
    return steps[Math.max(i-1, 0)] || key;
  }

  // ====== Welcome panel conditional UI
  function refreshWelcomeBlocks(){
    const f = flags();

    const canadaYes   = document.getElementById('spouse_in_canada_yes');
    const inCanadaYes = !!canadaYes?.checked;

    const spouseFileBlock = document.getElementById('spouse-file-block');
    const childrenBlock   = document.getElementById('children-block');

if (childrenBlock)   childrenBlock.style.display   = f.childQuestionVisible ? '' : 'none';
  if (spouseFileBlock) spouseFileBlock.style.display = (f.marriedLike && inCanadaYes) ? '' : 'none';


    const mclDateBlock = document.getElementById('status-date-block');
    const mclDateLabel = document.getElementById('status-date-label');
    const sdwDateBlock = document.getElementById('status-date-sdw-block');
    const sdwDateLabel = document.getElementById('status-date-sdw-label');

    if (mclDateBlock) mclDateBlock.style.display = 'none';
    if (sdwDateBlock) sdwDateBlock.style.display = 'none';

// assuming f.ms is your marital status
if (f.marriedLike) {
  if (mclDateLabel) {
    mclDateLabel.textContent = (f.ms === 'Common Law')
      ? 'Date of Status Start'
      : 'Date of Marriage';
  }
  if (mclDateBlock) mclDateBlock.style.display = '';

  // show / hide the common-law help
  const clHelp = document.getElementById('status-commonlaw-help');
  if (clHelp) {
    clHelp.style.display = (f.ms === 'Common Law') ? '' : 'none';
  }

} else if (f.ms === 'Separated' || f.ms === 'Divorced' || f.ms === 'Widowed') {
  if (sdwDateLabel) {
    sdwDateLabel.textContent =
      f.ms === 'Separated' ? 'Date of Separation' :
      f.ms === 'Divorced'  ? 'Date of Divorce'    :
                              'Date of Passing';
  }
  if (sdwDateBlock) sdwDateBlock.style.display = '';

  // make sure help is hidden if we’re not in married/common-law
  const clHelp = document.getElementById('status-commonlaw-help');
  if (clHelp) clHelp.style.display = 'none';
} else {
  // nothing selected or single – hide block + help
  if (mclDateBlock) mclDateBlock.style.display = 'none';
  const clHelp = document.getElementById('status-commonlaw-help');
  if (clHelp) clHelp.style.display = 'none';
}


    const rentBenefitQ = document.getElementById('rentBenefitBlock');
    if (rentBenefitQ) rentBenefitQ.style.display = f.onRentYes ? '' : 'none';

    const hiddenSpouseVal = document.getElementById('spouseFile_value');
    if (hiddenSpouseVal) hiddenSpouseVal.value = (getVal('spouseFile') || 'no');
  }

  function refreshOtherIncomePanel(){
    const f = flags();
    const rentalPlaceholder   = document.getElementById('rental-income-section');
    if (rentalPlaceholder)    rentalPlaceholder.style.display = f.rentYes ? '' : 'none';
    const rentBenefitWrapper  = document.getElementById('rent-section');
    if (rentBenefitWrapper)   rentBenefitWrapper.style.display = f.benefitNeeded ? '' : 'none';
    const gigSection          = document.getElementById('gig-section');
    if (gigSection)           gigSection.style.display = f.selfYes ? '' : 'none';

  const uploadGig = document.getElementById('upload-gig-section');
  if (uploadGig) uploadGig.style.display = f.gigIncomeYes ? '' : 'none';
  }

// ====== Show panel + progress
const stagePanels = formPanel ? formPanel.querySelectorAll('.pi-main[data-panel]') : [];
function showPanel(id){
  if (!formPanel) return;
  CURRENT = id;

  // show only the requested panel
  stagePanels.forEach(p => { p.hidden = (p.dataset.panel !== id); });

  // re-evaluate visibility when entering these steps
  if (id === 'other-income' || id === 'upload-self' || id === 'upload-spouse') {
    refreshOtherIncomePanel();          // keeps applicant gig upload in sync
  }
  if (id === 'upload-spouse') {
    window.App?.applySpouseGigUpload?.(); // keeps spouse gig upload in sync
  }

  updateProgress(id);
  window.scrollTo({ top: 0, behavior: 'smooth' });
}



  // Track completed panels (validated and passed)
  const completedPanels = new Set();
  
  // Mark a panel as completed
  function markPanelCompleted(panelKey) {
    if (panelKey) {
      completedPanels.add(panelKey);
      updateProgress(CURRENT || panelKey);
    }
  }
  
  // Check if a panel is completed
  function isPanelCompleted(panelKey) {
    return completedPanels.has(panelKey);
  }
  
  // Check if all previous steps (in sequence) are completed
  function areAllPreviousStepsCompleted(stepKey) {
    // 'pre' and 'personal' always accessible
    if (stepKey === 'pre' || stepKey === 'personal') return true;
    
    const stepsActive = activeSteps();
    const targetIndex = ORDER.indexOf(stepKey);
    
    // If not in ORDER or not active, allow (edge cases)
    if (targetIndex === -1 || !stepsActive.includes(stepKey)) return true;
    
    // Check all previous steps in ORDER that are active
    for (let i = 0; i < targetIndex; i++) {
      const prevStep = ORDER[i];
      if (stepsActive.includes(prevStep) && !isPanelCompleted(prevStep)) {
        return false; // Found an incomplete previous step
      }
    }
    
    return true; // All previous steps completed
  }

  function updateProgress(currentKey){
    if (!sidebar) return;
    const stepsActive = activeSteps();
    const ordered     = stepsActive;
    const curIdx      = ordered.indexOf(currentKey);

    $$('.pi-steps [data-step]').forEach(el => {
      const key = el.dataset.step;

      if (key !== 'pre' && !stepsActive.includes(key)) {
        el.style.display = 'none';
        return;
      }
      el.style.display = '';

      el.classList.remove('is-current','is-done','is-locked','is-disabled');
      el.removeAttribute('aria-current');
      el.removeAttribute('aria-disabled');
      el.removeAttribute('tabindex');

      if (key === 'pre') {
        if (formPanel && formPanel.style.display !== 'none') el.classList.add('is-done');
        return;
      }

      const idx = ordered.indexOf(key);
      
      // Check if this step can be accessed
      const canAccess = areAllPreviousStepsCompleted(key);
      const isCurrent = (idx === curIdx);
      const isCompleted = isPanelCompleted(key);
      
      // Determine if step should be clickable
      const isClickable = isCurrent || canAccess || isCompleted;
      
      // Set disabled state
      if (!isClickable) {
        el.setAttribute('aria-disabled', 'true');
        el.setAttribute('tabindex', '-1');
        el.classList.add('is-disabled');
        el.style.pointerEvents = 'none';
        el.style.cursor = 'not-allowed';
        el.style.opacity = '0.6';
      } else {
        el.removeAttribute('aria-disabled');
        el.removeAttribute('tabindex');
        el.classList.remove('is-disabled');
        el.style.pointerEvents = '';
        el.style.cursor = '';
        el.style.opacity = '';
      }
      
      if (idx < curIdx) {
        // Only mark as done if the panel has been validated and completed
        if (isPanelCompleted(key)) {
          el.classList.add('is-done');
        } else {
          // If not completed, keep it unlocked but don't mark as done
          // This allows users to go back but shows they haven't completed it
        }
      } else if (idx === curIdx)  { 
        el.classList.add('is-current'); 
        el.setAttribute('aria-current','step'); 
      } else {
        // Lock future steps unless previous step is completed
        if (idx > 0) {
          const prevKey = ordered[idx - 1];
          if (isPanelCompleted(prevKey) || prevKey === currentKey) {
            // Previous step is completed or is current, allow access
            // Don't lock, but also don't mark as done
          } else {
            el.classList.add('is-locked');
          }
        } else {
          el.classList.add('is-locked');
        }
      }
    });
  }

  // ====== Landing buttons
  document.getElementById('intro-continue')?.addEventListener('click', () => {
    if (introCard) introCard.style.display = 'none';
    if (welcomePanel) welcomePanel.style.display = 'block';
    if (oldFormSidebar) oldFormSidebar.style.display = 'none';
    refreshWelcomeBlocks();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
  document.getElementById('qs-back')?.addEventListener('click', () => {
    if (welcomePanel) welcomePanel.style.display = 'none';
    if (introCard) introCard.style.display = 'block';
    if (oldFormSidebar) oldFormSidebar.style.display = '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  // ====== Sidebar clicks
  sidebar?.addEventListener('click', (e) => {
    const link = e.target.closest('.pi-step');
    if (!link) return;
    e.preventDefault();
    
    // Check if step is disabled (non-clickable)
    if (link.classList.contains('is-disabled') || 
        link.getAttribute('aria-disabled') === 'true') {
      return; // Step is disabled, prevent navigation
    }
    
    if (link.classList.contains('is-locked')) return;

    const target = link.dataset.goto || link.dataset.step;
    if (target === 'welcome' || link.dataset.step === 'pre') {
      if (formPanel)    formPanel.style.display = 'none';
      if (welcomePanel) welcomePanel.style.display = 'block';
      if (oldFormSidebar) oldFormSidebar.style.display = 'none';
      refreshWelcomeBlocks();
      window.scrollTo({top:0,behavior:'smooth'});
      updateProgress('personal');
      // leaving review-jump (if any)
      document.querySelectorAll('.pi-main[data-review-jump="1"]').forEach(p=>p.removeAttribute('data-review-jump'));
      document.querySelectorAll('.review-back').forEach(b=>b.remove());
      return;
    }

    const stepsNow = activeSteps();
    if (!stepsNow.includes(target)) return;

    // Check if all previous steps are completed
    if (!areAllPreviousStepsCompleted(target)) {
      showPanelError('Please complete all previous steps before accessing this section.');
      return false;
    }

    // Validate current panel before navigating to a different panel
    if (CURRENT && CURRENT !== target) {
      const currentIndex = ORDER.indexOf(CURRENT);
      const targetIndex = ORDER.indexOf(target);
      
      // If navigating forward, validate current panel first
      if (targetIndex > currentIndex) {
        if (!validateCurrentPanel()) {
          showPanelError();
          // Prevent navigation
          return false;
        }
        hidePanelError();
        // Mark current panel as completed
        markPanelCompleted(CURRENT);
      }
      
      // Prevent jumping ahead if intermediate steps aren't completed
      if (targetIndex > currentIndex + 1) {
        // Check if all intermediate steps are completed
        for (let i = currentIndex + 1; i < targetIndex; i++) {
          const intermediateStep = ORDER[i];
          // Only check if the step is active (should be shown)
          if (stepsNow.includes(intermediateStep) && !isPanelCompleted(intermediateStep)) {
            // Intermediate step exists and isn't completed, prevent jumping
            const stepName = intermediateStep.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            showPanelError(`Please complete all required fields in the previous step before proceeding.`);
            return false;
          }
        }
      }
    }

    if (welcomePanel && welcomePanel.style.display !== 'none') {
      welcomePanel.style.display = 'none';
      if (formPanel) formPanel.style.display = 'block';
    }
    showPanel(target);
    // leaving review-jump (if any)
    document.querySelectorAll('.pi-main[data-review-jump="1"]').forEach(p=>p.removeAttribute('data-review-jump'));
    document.querySelectorAll('.review-back').forEach(b=>b.remove());
  });

  // ====== Validation rules mapping (field names from validation rules to actual input names)
  const VALIDATION_RULES = {
    // Personal Information
    'lastName': 'last_name',
    'gender': 'gender',
    'ship_address': 'street',
    'locality': 'city',
    'state': 'province',
    'postcode': 'postal',
    'country': 'country',
    'birth_date': 'dob',
    'sin_number': 'sin',
    'phone': 'phone',
    'email': 'email',
    'canada_entry': 'canada_entry',
    'birth_country': 'birth_country',
    // Tax Filing Information
    'another_province': 'another_province',
    'move_date': 'move_date',
    'move_from': 'move_from',
    'move_to': 'move_to',
    'first_fillingtax': 'first_fillingtax',
    'year1': 'year1',
    'year1_income': 'year1_income',
    'year2': 'year2',
    'year2_income': 'year2_income',
    'year3': 'year3',
    'year3_income': 'year3_income',
    'years_tax_return': 'years_tax_return',
    'rent_benefit': 'rent_benefit',
    // Spouse Information
    'spouse_firstname': 'spouse_first_name',
    'spouse_lastname': 'spouse_last_name',
    'spouse_date_birth': 'spouse_date_birth',
    'date_marriage': 'date_marriage',
    'spouse_annual_income': 'spouse_annual_income',
    'spouse_sin': 'spouse_sin',
    'spouse_phone': 'spouse_phone',
    'spouse_email': 'spouse_email',
    'spouse_canada_entry': 'spouse_canada_entry',
    'spouse_birth_country': 'spouse_birth_country',
    'spouse_year1': 'spouse_year1',
    'spouse_year1_income': 'spouse_year1_income',
    'spouse_year2': 'spouse_year2',
    'spouse_year2_income': 'spouse_year2_income',
    'spouse_year3': 'spouse_year3',
    'spouse_year3_income': 'spouse_year3_income',
    'spouse_years_tax_return': 'spouse_years_tax_return',
    'marital_change': 'marital_change',
    'marital_status': 'marital_status',
    'spouse_in_canada': 'spouse_in_canada',
    'children': 'children'
  };
  
  // ====== Fields that are required based on validation rules
  const REQUIRED_FIELDS = [
    'last_name', 'gender', 'street', 'city', 'province', 'postal', 'country',
    'dob', 'sin', 'phone', 'email', 'canada_entry', 'birth_country',
    'another_province', 'move_date', 'move_from', 'move_to', 'first_fillingtax',
    'year1', 'year1_income', 'year2', 'year2_income', 'year3', 'year3_income',
    'years_tax_return', 'rent_benefit',
    'marital_change', 'marital_status',
    // Other Income fields
    'gig_income', 'rentalIncome'
    // Note: spouse fields, spouse_in_canada, children, and sp_gig_income are conditionally required
    // They are added dynamically based on marital status in getConditionallyRequiredFields()
  ];
  
  // ====== Error messages for required fields
  const FIELD_ERROR_MESSAGES = {
    'last_name': 'This field is required',
    'gender': 'This field is required',
    'street': 'This field is required',
    'city': 'This field is required',
    'province': 'This field is required',
    'postal': 'This field is required',
    'country': 'This field is required',
    'dob': 'This field is required',
    'sin': 'This field is required',
    'phone': 'This field is required',
    'email': 'This field is required',
    'canada_entry': 'This field is required',
    'birth_country': 'This field is required',
    'another_province': 'This field is required',
    'move_date': 'This field is required',
    'move_from': 'This field is required',
    'move_to': 'This field is required',
    'first_fillingtax': 'This field is required',
    'year1': 'This field is required',
    'year1_income': 'This field is required',
    'year2': 'This field is required',
    'year2_income': 'This field is required',
    'year3': 'This field is required',
    'year3_income': 'This field is required',
    'years_tax_return': 'This field is required',
    'rent_benefit': 'This field is required',
    'spouse_first_name': 'This field is required',
    'spouse_last_name': 'This field is required',
    'spouse_date_birth': 'This field is required',
    'date_marriage': 'This field is required',
    'spouse_annual_income': 'This field is required',
    'spouse_sin': 'This field is required',
    'spouse_phone': 'This field is required',
    'spouse_email': 'This field is required',
    'spouse_canada_entry': 'This field is required',
    'spouse_birth_country': 'This field is required',
    'sp_birth_country': 'This field is required',
    'sp_first_time': 'This field is required',
    'sp_paragon_prior': 'This field is required',
    'sp_return_years': 'This field is required',
    'sp_entry_date': 'This field is required',
    'sp_entry_date_display': 'This field is required',
    'sp_moved_province': 'This field is required',
    'sp_moved_date': 'This field is required',
    'sp_prov_from': 'This field is required',
    'sp_prov_to': 'This field is required',
    'spouse_year1': 'This field is required',
    'spouse_year1_income': 'This field is required',
    'spouse_year2': 'This field is required',
    'spouse_year2_income': 'This field is required',
    'spouse_year3': 'This field is required',
    'spouse_year3_income': 'This field is required',
    'spouse_years_tax_return': 'This field is required',
    'marital_change': 'This field is required',
    'marital_status': 'This field is required',
    'spouse_in_canada': 'This field is required',
    'children': 'This field is required',
    'gig_income': 'This field is required',
    'gig_expenses_summary': 'This field is required',
    'gig_hst': 'This field is required',
    'hst_number': 'This field is required',
    'hst_access': 'This field is required',
    'hst_start': 'This field is required',
    'hst_end': 'This field is required',
    'sp_gig_income': 'This field is required',
    'sp_gig_expenses_summary': 'This field is required',
    'sp_gig_hst': 'This field is required',
    'sp_hst_number': 'This field is required',
    'sp_hst_access': 'This field is required',
    'sp_hst_start': 'This field is required',
    'sp_hst_end': 'This field is required',
    'rentalIncome': 'This field is required',
    // Tax panel fields
    'first_time': 'This field is required',
    'paragon_prior': 'This field is required',
    'return_years': 'This field is required',
    'entry_date': 'This field is required',
    'birth_country': 'This field is required',
    'moved_province': 'This field is required',
    'moved_date': 'This field is required',
    'prov_from': 'This field is required',
    'prov_to': 'This field is required',
    'moving_expenses_claim': 'This field is required',
    'moving_prev_address': 'This field is required',
    'moving_distance': 'This field is required',
    'spouse_income_outside_cad': 'This field is required',
    'spouse_street': 'This field is required',
    'spouse_city': 'This field is required',
    'spouse_province': 'This field is required',
    'spouse_postal': 'This field is required',
    'date_marriage': 'This field is required',
    'spouseFile': 'This field is required',
    'first_home_buyer': 'This field is required',
    'first_home_purchase': 'This field is required',
    'claim_full': 'This field is required',
    'owner_count': 'This field is required'
  };

  // ====== Show field error message
  function showFieldError(fieldName, fieldElement) {
    // Remove any existing error for this field
    hideFieldError(fieldName);
    
    const errorMessage = FIELD_ERROR_MESSAGES[fieldName] || 'This field is required';
    
    // Find the field container (fi-group, qs-block, or parent)
    let container = fieldElement.closest('.fi-group') || 
                    fieldElement.closest('.qs-block') ||
                    fieldElement.closest('.yn-group') ||
                    fieldElement.parentElement;
    
    if (!container) return;
    
    // Create error text element
    const errorText = document.createElement('div');
    errorText.className = 'fi-error-text';
    errorText.id = `error-${fieldName}`;
    errorText.textContent = errorMessage;
    
    // Insert after the field or at the end of container
    if (fieldElement.type === 'radio') {
      // For radio buttons, find the group container
      const radioGroup = fieldElement.closest('.yn-group') || 
                        fieldElement.closest('.qs-choicegrid') ||
                        fieldElement.closest('.qs-block');
      if (radioGroup) {
        radioGroup.appendChild(errorText);
      } else {
        container.appendChild(errorText);
      }
    } else {
      // For other inputs (text, select, textarea), insert inside the container at the end
      // This ensures it appears below the field, not on the right
      if (container && container.classList.contains('fi-group')) {
        // For fi-group containers, append to the container so it appears below the input
        container.appendChild(errorText);
      } else if (container) {
        // Append to container
        container.appendChild(errorText);
      } else if (fieldElement.parentElement) {
        // Fallback: append to parent
        fieldElement.parentElement.appendChild(errorText);
      }
    }
    
    // Add error class to container for red styling
    container.classList.add('error');
  }
  
  // ====== Hide field error message
  function hideFieldError(fieldName) {
    const errorText = document.getElementById(`error-${fieldName}`);
    if (errorText) {
      errorText.remove();
    }
    
    // Remove error class from all containers with this field
    const fieldElements = document.querySelectorAll(`[name="${fieldName}"]`);
    fieldElements.forEach(el => {
      const container = el.closest('.fi-group') || 
                       el.closest('.qs-block') ||
                       el.closest('.yn-group');
      if (container) {
        container.classList.remove('error');
      }
    });
  }
  
  // Expose hideFieldError to global scope so it can be accessed from other scripts
  window.hideFieldError = hideFieldError;
  
  // ====== Clear all field errors in current panel
  function clearAllFieldErrors() {
    if (!CURRENT) return;
    const currentPanel = document.querySelector(`.pi-main[data-panel="${CURRENT}"]`);
    if (!currentPanel) return;
    
    // Remove all error text elements
    currentPanel.querySelectorAll('.fi-error-text').forEach(el => el.remove());
    
    // Remove error classes
    currentPanel.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
  }

  // ====== Get conditionally required fields based on marital status
  function getConditionallyRequiredFields() {
    const maritalStatus = document.querySelector('input[name="marital_status"]:checked')?.value || '';
    const marriedLike = (maritalStatus === 'Married' || maritalStatus === 'Common Law');
    const childQuestionVisible = (marriedLike || maritalStatus === 'Separated' || maritalStatus === 'Divorced' || maritalStatus === 'Widowed');
    const spouseInCanada = document.querySelector('input[name="spouse_in_canada"]:checked')?.value;
    const spouseFile = document.querySelector('input[name="spouseFile"]:checked')?.value;
    
    // Base required fields (always required)
    const conditionalFields = [];
    
    // Spouse-related fields only required if Married/Common Law
    if (marriedLike) {
      conditionalFields.push('spouse_in_canada');
      // If spouse is in Canada and wants to file, then spouse fields are required
      if (spouseInCanada === 'Yes' && spouseFile === 'yes') {
        conditionalFields.push(
          'spouse_first_name', 'spouse_last_name', 'spouse_date_birth', 'date_marriage',
          'spouse_annual_income', 'spouse_sin', 'spouse_phone', 'spouse_email',
          'spouse_canada_entry',
          'spouse_year1', 'spouse_year1_income', 'spouse_year2', 'spouse_year2_income',
          'spouse_year3', 'spouse_year3_income', 'spouse_years_tax_return',
          // Spouse Tax Filing Information fields
          'sp_first_time', 'sp_entry_date', 'sp_entry_date_display',
          // Spouse Other Income fields
          'sp_gig_income'
        );
        
        // Check if spouse is first-time filer
        const spFirstTime = document.querySelector('input[name="sp_first_time"]:checked')?.value;
        if (spFirstTime === 'yes') {
          // First-time filer: require entry date and birth country
          conditionalFields.push('sp_birth_country');
        } else if (spFirstTime === 'no') {
          // Prior customer: require paragon prior and return years
          conditionalFields.push('sp_paragon_prior', 'sp_return_years');
        }
      }
    }
    
    // Children question only required if visible (not Single)
    if (childQuestionVisible) {
      conditionalFields.push('children');
    }
    
    return conditionalFields;
  }

  // ====== Validate current panel's required fields
  function validateCurrentPanel() {
    if (!CURRENT) return true;
    
    const currentPanel = document.querySelector(`.pi-main[data-panel="${CURRENT}"]`);
    if (!currentPanel) return true;
    
    // Clear previous errors
    clearAllFieldErrors();
    
    // Get conditionally required fields based on marital status
    const conditionalFields = getConditionallyRequiredFields();
    
    const errors = [];
    const checkedFields = new Set();
    
    // Helper function to validate a single field
    function validateField(input, fieldName) {
      // Skip if already checked
      if (checkedFields.has(fieldName)) return;
      
      if (input.type === 'radio') {
        // For radio buttons, check if any in the group is checked
        const checked = currentPanel.querySelector(`input[name="${fieldName}"]:checked`);
        if (!checked) {
          errors.push(fieldName);
          showFieldError(fieldName, input);
        }
        checkedFields.add(fieldName);
      } else if (input.type === 'checkbox') {
        // For checkboxes, check if checked
        if (!input.checked) {
          errors.push(fieldName);
          showFieldError(fieldName, input);
        }
        checkedFields.add(fieldName);
      } else {
        // For text inputs, selects, textareas, hidden inputs bound to date fields
        let value = input.value ? input.value.trim() : '';
        
        // For hidden date inputs, check if the display field has a value
        if (input.type === 'hidden' && input.id && input.id.includes('_date') && !input.id.includes('_display')) {
          const displayField = currentPanel.querySelector(`input[id="${input.id}_display"], input[data-bind="#${input.id}"]`);
          if (displayField) {
            value = displayField.value ? displayField.value.trim() : '';
          }
        }
        
        if (value === '') {
          errors.push(fieldName);
          showFieldError(fieldName, input);
        }
        checkedFields.add(fieldName);
      }
    }
    
    // First, check all fields with required attribute (prioritize HTML5 required)
    const requiredInputs = currentPanel.querySelectorAll('input[required], select[required], textarea[required]');
    requiredInputs.forEach(input => {
      if (input.offsetParent === null) return; // Skip hidden fields
      const fieldName = input.name;
      if (!fieldName) return;
      validateField(input, fieldName);
    });
    
    // Then check fields from REQUIRED_FIELDS array and conditional fields
    const allInputs = currentPanel.querySelectorAll('input, select, textarea');
    allInputs.forEach(input => {
      // Skip hidden fields
      if (input.offsetParent === null) return;
      
      const fieldName = input.name;
      if (!fieldName) return;
      
      // Skip if already checked (from required attribute check above)
      if (checkedFields.has(fieldName)) return;
      
      // Check if this field is required (base required OR conditionally required)
      const isBaseRequired = REQUIRED_FIELDS.includes(fieldName);
      const isConditionallyRequired = conditionalFields.includes(fieldName);
      const isRequired = isBaseRequired || isConditionallyRequired;
      
      if (isRequired) {
        validateField(input, fieldName);
      }
    });
    
    // Special validation for children panel: require at least 1 child
    if (CURRENT === 'children') {
      // Check if at least 1 child is added by checking the table rows
      const childrenTable = currentPanel.querySelector('#children-tbody');
      const emptyRow = currentPanel.querySelector('#children-empty-row');
      
      if (childrenTable) {
        // Get all child rows (excluding the empty row)
        const childRows = childrenTable.querySelectorAll('tr:not(#children-empty-row)');
        const hasChildren = childRows.length > 0;
        
        // Also check if empty row is visible (means no children)
        const isEmpty = emptyRow && emptyRow.offsetParent !== null;
        
        // If no children are added, show error
        if (!hasChildren || isEmpty) {
          errors.push('children_required');
          // Show error banner
          showPanelError('At least one child must be added.');
          return false;
        }
      } else {
        // If table doesn't exist, treat as error
        errors.push('children_required');
        showPanelError('At least one child must be added.');
        return false;
      }
    }
    
    // Special validation for upload-self panel: require ID Proof
    if (CURRENT === 'upload-self') {
      // Check if at least one file is uploaded in ID Proof
      const idProofList = currentPanel.querySelector('#app_id_list');
      const idProofItems = idProofList ? idProofList.querySelectorAll('.dz-item') : [];
      
      if (idProofItems.length === 0) {
        errors.push('id_proof_required');
        // Show error banner
        showPanelError('ID Proof is required. Please upload at least one file.');
        
        // Add field-level error styling
        const idProofBlock = currentPanel.querySelector('#app_id_list')?.closest('.qs-block');
        if (idProofBlock && !idProofBlock.classList.contains('error')) {
          idProofBlock.classList.add('error');
          // Check if error message already exists
          let errorText = idProofBlock.querySelector('.fi-error-text');
          if (!errorText) {
            errorText = document.createElement('div');
            errorText.className = 'fi-error-text';
            errorText.id = 'error-app_id_proof';
            errorText.textContent = 'ID Proof is required. Please upload at least one file.';
            // Insert after the dropzone list
            const dropzoneList = idProofBlock.querySelector('#app_id_list');
            if (dropzoneList) {
              dropzoneList.parentNode.insertBefore(errorText, dropzoneList.nextSibling);
            } else {
              idProofBlock.appendChild(errorText);
            }
          }
        }
        return false;
      } else {
        // Clear error if files are uploaded
        const idProofBlock = currentPanel.querySelector('#app_id_list')?.closest('.qs-block');
        if (idProofBlock) {
          idProofBlock.classList.remove('error');
          const errorText = idProofBlock.querySelector('.fi-error-text');
          if (errorText) errorText.remove();
        }
      }
      
      // Check T4/T4A/T Slips password protected questions
      const tslipsList = currentPanel.querySelector('#app_tslips_list');
      if (tslipsList) {
        const tslipsItems = tslipsList.querySelectorAll('.dz-item');
        tslipsItems.forEach((item, index) => {
          const pwHidden = item.querySelector('input[name="app_tslips_pw_protected[]"]');
          if (pwHidden && (!pwHidden.value || pwHidden.value === '')) {
            errors.push(`app_tslips_pw_protected_${index}`);
            // Show error on the specific item
            const pwGroup = item.querySelector('.dz-pw-yn');
            if (pwGroup && !pwGroup.classList.contains('error')) {
              pwGroup.classList.add('error');
              const errorText = document.createElement('div');
              errorText.className = 'fi-error-text';
              errorText.id = `error-app_tslips_pw_protected_${index}`;
              errorText.textContent = 'Please select Yes or No for "Is Password Protected?"';
              pwGroup.appendChild(errorText);
            }
          }
        });
        if (tslipsItems.length > 0 && errors.some(e => e.startsWith('app_tslips_pw_protected'))) {
          showPanelError('Please answer "Is Password Protected?" for all T4/T4A/T Slips files.');
        }
      }
    }
    
    // Special validation for upload-spouse panel: require ID Proof and Self-employed Income (if applicable)
    if (CURRENT === 'upload-spouse') {
      // Always require ID Proof
      const idProofList = currentPanel.querySelector('#sp_id_list');
      const idProofItems = idProofList ? idProofList.querySelectorAll('.dz-item') : [];
      
      if (idProofItems.length === 0) {
        errors.push('sp_id_proof_required');
        // Show error banner
        showPanelError('ID Proof is required. Please upload at least one file.');
        
        // Add field-level error styling
        const idProofBlock = currentPanel.querySelector('#sp_id_list')?.closest('.qs-block');
        if (idProofBlock && !idProofBlock.classList.contains('error')) {
          idProofBlock.classList.add('error');
          // Check if error message already exists
          let errorText = idProofBlock.querySelector('.fi-error-text');
          if (!errorText) {
            errorText = document.createElement('div');
            errorText.className = 'fi-error-text';
            errorText.id = 'error-sp_id_proof';
            errorText.textContent = 'ID Proof is required. Please upload at least one file.';
            // Insert after the dropzone list
            const dropzoneList = idProofBlock.querySelector('#sp_id_list');
            if (dropzoneList) {
              dropzoneList.parentNode.insertBefore(errorText, dropzoneList.nextSibling);
            } else {
              idProofBlock.appendChild(errorText);
            }
          }
        }
        return false;
      } else {
        // Clear error if files are uploaded
        const idProofBlock = currentPanel.querySelector('#sp_id_list')?.closest('.qs-block');
        if (idProofBlock) {
          idProofBlock.classList.remove('error');
          const errorText = idProofBlock.querySelector('.fi-error-text');
          if (errorText) errorText.remove();
        }
      }
      
      // Check if spouse has gig income (sp_gig_income = yes)
      const spGigIncome = document.querySelector('input[name="sp_gig_income"]:checked');
      const hasGigIncome = spGigIncome && spGigIncome.value === 'yes';
      
      // If spouse has gig income, require self-employed income upload
      if (hasGigIncome) {
        const spGigSection = currentPanel.querySelector('#sp-upload-gig-section');
        const isGigSectionVisible = spGigSection && spGigSection.offsetParent !== null;
        
        if (isGigSectionVisible) {
          const gigFilesList = currentPanel.querySelector('#sp-gig-files');
          const gigFileItems = gigFilesList ? gigFilesList.querySelectorAll('.dz-item') : [];
          
          if (gigFileItems.length === 0) {
            errors.push('sp_gig_income_required');
            // Show error banner
            showPanelError('Spouse – Add Self-employed Income is required. Please upload at least one file.');
            return false;
          }
        }
      }
      
      // Check T4/T4A/T Slips password protected questions
      const tslipsList = currentPanel.querySelector('#sp_tslips_list');
      if (tslipsList) {
        const tslipsItems = tslipsList.querySelectorAll('.dz-item');
        tslipsItems.forEach((item, index) => {
          const pwHidden = item.querySelector('input[name="sp_tslips_pw_protected[]"]');
          if (pwHidden && (!pwHidden.value || pwHidden.value === '')) {
            errors.push(`sp_tslips_pw_protected_${index}`);
            // Show error on the specific item
            const pwGroup = item.querySelector('.dz-pw-yn');
            if (pwGroup && !pwGroup.classList.contains('error')) {
              pwGroup.classList.add('error');
              const errorText = document.createElement('div');
              errorText.className = 'fi-error-text';
              errorText.id = `error-sp_tslips_pw_protected_${index}`;
              errorText.textContent = 'Please select Yes or No for "Is Password Protected?"';
              pwGroup.appendChild(errorText);
            }
          }
        });
        if (tslipsItems.length > 0 && errors.some(e => e.startsWith('sp_tslips_pw_protected'))) {
          showPanelError('Please answer "Is Password Protected?" for all T4/T4A/T Slips files.');
        }
      }
    }
    
    // Special validation for personal panel: check conditionally visible fields
    if (CURRENT === 'personal') {
      // Ensure marital_status is answered
      const maritalStatusChecked = currentPanel.querySelector('input[name="marital_status"]:checked');
      if (!maritalStatusChecked) {
        const maritalStatusRadio = currentPanel.querySelector('input[name="marital_status"]');
        if (maritalStatusRadio && !checkedFields.has('marital_status')) {
          errors.push('marital_status');
          showFieldError('marital_status', maritalStatusRadio);
          checkedFields.add('marital_status');
        }
      }
      
      const maritalStatus = maritalStatusChecked?.value;
      const isMarriedOrCommonLaw = (maritalStatus === 'Married' || maritalStatus === 'Common Law');
      const isSeparatedDivorcedWidowed = (maritalStatus === 'Separated' || maritalStatus === 'Divorced' || maritalStatus === 'Widowed');
      
      if (isMarriedOrCommonLaw) {
        // If Married/Common Law, require date_marriage
        const statusDateBlock = currentPanel.querySelector('#status-date-block');
        if (statusDateBlock && statusDateBlock.offsetParent !== null) {
          const statusDate = currentPanel.querySelector('#status_date');
          if (statusDate && statusDate.offsetParent !== null) {
            const value = statusDate.value ? statusDate.value.trim() : '';
            if (value === '' && !checkedFields.has('date_marriage')) {
              errors.push('date_marriage');
              showFieldError('date_marriage', statusDate);
              checkedFields.add('date_marriage');
            }
          }
        }
        
        // Require spouse_in_canada when visible
        const canadaGroup = currentPanel.querySelector('.yn-group:has(#spouse_in_canada_yes)');
        if (canadaGroup && canadaGroup.offsetParent !== null) {
          const spouseInCanadaChecked = currentPanel.querySelector('input[name="spouse_in_canada"]:checked');
          if (!spouseInCanadaChecked) {
            const spouseInCanadaRadio = currentPanel.querySelector('input[name="spouse_in_canada"]');
            if (spouseInCanadaRadio && !checkedFields.has('spouse_in_canada')) {
              errors.push('spouse_in_canada');
              showFieldError('spouse_in_canada', spouseInCanadaRadio);
              checkedFields.add('spouse_in_canada');
            }
          }
          
          // If spouse_in_canada = Yes, require spouseFile
          const spouseInCanada = spouseInCanadaChecked?.value;
          if (spouseInCanada === 'Yes') {
            const spouseFileBlock = currentPanel.querySelector('#spouse-file-block');
            if (spouseFileBlock && spouseFileBlock.offsetParent !== null) {
              const spouseFileChecked = currentPanel.querySelector('input[name="spouseFile"]:checked');
              if (!spouseFileChecked) {
                const spouseFileRadio = currentPanel.querySelector('input[name="spouseFile"]');
                if (spouseFileRadio && !checkedFields.has('spouseFile')) {
                  errors.push('spouseFile');
                  showFieldError('spouseFile', spouseFileRadio);
                  checkedFields.add('spouseFile');
                }
              }
            }
          }
        }
      } else if (isSeparatedDivorcedWidowed) {
        // If Separated/Divorced/Widowed, require marital_change date
        const statusDateSdwBlock = currentPanel.querySelector('#status-date-sdw-block');
        if (statusDateSdwBlock && statusDateSdwBlock.offsetParent !== null) {
          const statusDateSdw = currentPanel.querySelector('#status_date_sdw');
          if (statusDateSdw && statusDateSdw.offsetParent !== null) {
            const value = statusDateSdw.value ? statusDateSdw.value.trim() : '';
            if (value === '' && !checkedFields.has('marital_change')) {
              errors.push('marital_change');
              showFieldError('marital_change', statusDateSdw);
              checkedFields.add('marital_change');
            }
          }
        }
      }
      
      // Children question validation (if visible)
      const childrenBlock = currentPanel.querySelector('#children-block');
      if (childrenBlock && childrenBlock.offsetParent !== null) {
        const childrenChecked = currentPanel.querySelector('input[name="children"]:checked');
        if (!childrenChecked) {
          const childrenRadio = currentPanel.querySelector('input[name="children"]');
          if (childrenRadio && !checkedFields.has('children')) {
            errors.push('children');
            showFieldError('children', childrenRadio);
            checkedFields.add('children');
          }
        }
      }
    }
    
    // Special validation for tax panel: check conditionally visible fields
    if (CURRENT === 'tax') {
      // First, ensure the gated question itself is answered
      const firstTimeChecked = currentPanel.querySelector('input[name="first_time"]:checked');
      if (!firstTimeChecked) {
        const firstTimeRadio = currentPanel.querySelector('input[name="first_time"]');
        if (firstTimeRadio && !checkedFields.has('first_time')) {
          errors.push('first_time');
          showFieldError('first_time', firstTimeRadio);
          checkedFields.add('first_time');
        }
      }
      
      // Check first_time question
      const firstTime = firstTimeChecked?.value;
      
      if (firstTime === 'no') {
        // If first_time = no, require paragon_prior and return_years
        const priorSection = currentPanel.querySelector('#prior-customer-section');
        if (priorSection && priorSection.offsetParent !== null) {
          const paragonPrior = currentPanel.querySelector('input[name="paragon_prior"]:checked');
          if (!paragonPrior) {
            const firstRadio = currentPanel.querySelector('input[name="paragon_prior"]');
            if (firstRadio && !checkedFields.has('paragon_prior')) {
              errors.push('paragon_prior');
              showFieldError('paragon_prior', firstRadio);
              checkedFields.add('paragon_prior');
            }
          }
          
          const returnYears = currentPanel.querySelector('#return_years');
          if (returnYears && returnYears.offsetParent !== null) {
            const value = returnYears.value ? returnYears.value.trim() : '';
            if (value === '' && !checkedFields.has('return_years')) {
              errors.push('return_years');
              showFieldError('return_years', returnYears);
              checkedFields.add('return_years');
            }
          }
        }
      } else if (firstTime === 'yes') {
        // If first_time = yes, require entry_date and birth_country
        const firstTimeSection = currentPanel.querySelector('#firsttime-details');
        if (firstTimeSection && firstTimeSection.offsetParent !== null) {
          const entryDateDisplay = currentPanel.querySelector('#entry_date_display');
          if (entryDateDisplay && entryDateDisplay.offsetParent !== null) {
            const value = entryDateDisplay.value ? entryDateDisplay.value.trim() : '';
            if (value === '' && !checkedFields.has('entry_date')) {
              errors.push('entry_date');
              showFieldError('entry_date', entryDateDisplay);
              checkedFields.add('entry_date');
            }
          }
          
          const birthCountry = currentPanel.querySelector('#birth_country');
          if (birthCountry && birthCountry.offsetParent !== null) {
            const value = birthCountry.value ? birthCountry.value.trim() : '';
            if (value === '' && !checkedFields.has('birth_country')) {
              errors.push('birth_country');
              showFieldError('birth_country', birthCountry);
              checkedFields.add('birth_country');
            }
          }
        }
      }
      
      // Ensure moved_province gated question is answered
      const movedProvinceChecked = currentPanel.querySelector('input[name="moved_province"]:checked');
      if (!movedProvinceChecked) {
        const movedProvinceRadio = currentPanel.querySelector('input[name="moved_province"]');
        if (movedProvinceRadio && !checkedFields.has('moved_province')) {
          errors.push('moved_province');
          showFieldError('moved_province', movedProvinceRadio);
          checkedFields.add('moved_province');
        }
      }
      
      // Check moved_province question
      const movedProvince = movedProvinceChecked?.value;
      
      if (movedProvince === 'yes') {
        // If moved_province = yes, require moved_date, prov_from, prov_to, and moving_expenses_claim
        const movedSection = currentPanel.querySelector('#moved-section');
        if (movedSection && movedSection.offsetParent !== null) {
          const movedDateDisplay = currentPanel.querySelector('#moved_date_display');
          if (movedDateDisplay && movedDateDisplay.offsetParent !== null) {
            const value = movedDateDisplay.value ? movedDateDisplay.value.trim() : '';
            if (value === '' && !checkedFields.has('moved_date')) {
              errors.push('moved_date');
              showFieldError('moved_date', movedDateDisplay);
              checkedFields.add('moved_date');
            }
          }
          
          const provFrom = currentPanel.querySelector('#prov_from');
          if (provFrom && provFrom.offsetParent !== null) {
            const value = provFrom.value ? provFrom.value.trim() : '';
            if ((value === '' || value === 'Select State/Province') && !checkedFields.has('prov_from')) {
              errors.push('prov_from');
              showFieldError('prov_from', provFrom);
              checkedFields.add('prov_from');
            }
          }
          
          const provTo = currentPanel.querySelector('#prov_to');
          if (provTo && provTo.offsetParent !== null) {
            const value = provTo.value ? provTo.value.trim() : '';
            if ((value === '' || value === 'Select State/Province') && !checkedFields.has('prov_to')) {
              errors.push('prov_to');
              showFieldError('prov_to', provTo);
              checkedFields.add('prov_to');
            }
          }
        }
        
        // Ensure moving_expenses_claim question is answered when moved_province = yes
        const movingExpensesClaimBlock = currentPanel.querySelector('.qs-block:has(#movexp_yes)');
        if (movingExpensesClaimBlock && movingExpensesClaimBlock.offsetParent !== null) {
          const movingExpensesChecked = currentPanel.querySelector('input[name="moving_expenses_claim"]:checked');
          if (!movingExpensesChecked) {
            const movingExpensesRadio = currentPanel.querySelector('input[name="moving_expenses_claim"]');
            if (movingExpensesRadio && !checkedFields.has('moving_expenses_claim')) {
              errors.push('moving_expenses_claim');
              showFieldError('moving_expenses_claim', movingExpensesRadio);
              checkedFields.add('moving_expenses_claim');
            }
          }
        }
        
        // Check moving_expenses_claim
        const movingExpenses = currentPanel.querySelector('input[name="moving_expenses_claim"]:checked')?.value;
        if (movingExpenses === 'yes') {
          const movexpDetails = currentPanel.querySelector('#movexp-details');
          if (movexpDetails && movexpDetails.offsetParent !== null) {
            const movingPrevAddress = currentPanel.querySelector('#moving_prev_address');
            if (movingPrevAddress && movingPrevAddress.offsetParent !== null) {
              const value = movingPrevAddress.value ? movingPrevAddress.value.trim() : '';
              if (value === '' && !checkedFields.has('moving_prev_address')) {
                errors.push('moving_prev_address');
                showFieldError('moving_prev_address', movingPrevAddress);
                checkedFields.add('moving_prev_address');
              }
            }
            
            const movingDistance = currentPanel.querySelector('#moving_distance');
            if (movingDistance && movingDistance.offsetParent !== null) {
              const value = movingDistance.value ? movingDistance.value.trim() : '';
              if (value === '' && !checkedFields.has('moving_distance')) {
                errors.push('moving_distance');
                showFieldError('moving_distance', movingDistance);
                checkedFields.add('moving_distance');
              }
            }
          }
        }
      }
      
      // Ensure first_home_buyer gated question is answered
      const firstHomeBuyerChecked = currentPanel.querySelector('input[name="first_home_buyer"]:checked');
      if (!firstHomeBuyerChecked) {
        const firstHomeBuyerRadio = currentPanel.querySelector('input[name="first_home_buyer"]');
        if (firstHomeBuyerRadio && !checkedFields.has('first_home_buyer')) {
          errors.push('first_home_buyer');
          showFieldError('first_home_buyer', firstHomeBuyerRadio);
          checkedFields.add('first_home_buyer');
        }
      }
      
      // Check first_home_buyer question
      const firstHomeBuyer = firstHomeBuyerChecked?.value;
      
      if (firstHomeBuyer === 'yes') {
        // If first_home_buyer = yes, require first_home_purchase
        const fthbDetails = currentPanel.querySelector('#fthb-details');
        if (fthbDetails && fthbDetails.offsetParent !== null) {
          const firstHomePurchaseDisplay = currentPanel.querySelector('#first_home_purchase_display');
          if (firstHomePurchaseDisplay && firstHomePurchaseDisplay.offsetParent !== null) {
            const value = firstHomePurchaseDisplay.value ? firstHomePurchaseDisplay.value.trim() : '';
            if (value === '' && !checkedFields.has('first_home_purchase')) {
              errors.push('first_home_purchase');
              showFieldError('first_home_purchase', firstHomePurchaseDisplay);
              checkedFields.add('first_home_purchase');
            }
          }
        }
      }
      
      // Ensure claim_full gated question is answered
      const claimFullChecked = currentPanel.querySelector('input[name="claim_full"]:checked');
      if (!claimFullChecked) {
        const claimFullRadio = currentPanel.querySelector('input[name="claim_full"]');
        if (claimFullRadio && !checkedFields.has('claim_full')) {
          errors.push('claim_full');
          showFieldError('claim_full', claimFullRadio);
          checkedFields.add('claim_full');
        }
      }
      
      // Check claim_full question
      const claimFull = claimFullChecked?.value;
      
      if (claimFull === 'no') {
        // If claim_full = no, require owner_count
        const ownersWrap = currentPanel.querySelector('#owners-wrap');
        if (ownersWrap && ownersWrap.offsetParent !== null) {
          const ownerCount = currentPanel.querySelector('#owner_count');
          if (ownerCount && ownerCount.offsetParent !== null) {
            const value = ownerCount.value ? ownerCount.value.trim() : '';
            if (value === '' && !checkedFields.has('owner_count')) {
              errors.push('owner_count');
              showFieldError('owner_count', ownerCount);
              checkedFields.add('owner_count');
            }
          }
        }
      }
    }
    
    // Special validation for spouse panel: check conditionally visible fields
    if (CURRENT === 'spouse') {
      // If spouse_in_canada = No, require spouse_income_outside_cad
      const spouseInCanadaChecked = currentPanel.querySelector('input[name="spouse_in_canada"]:checked');
      if (spouseInCanadaChecked) {
        const spouseInCanada = spouseInCanadaChecked.value;
        if (spouseInCanada === 'No') {
          const spouseForeignIncome = currentPanel.querySelector('#spouse-foreign-income');
          if (spouseForeignIncome && spouseForeignIncome.offsetParent !== null) {
            const spouseIncomeOutside = currentPanel.querySelector('#spouse_income_outside_cad');
            if (spouseIncomeOutside && spouseIncomeOutside.offsetParent !== null) {
              const value = spouseIncomeOutside.value ? spouseIncomeOutside.value.trim() : '';
              if (value === '' && !checkedFields.has('spouse_income_outside_cad')) {
                errors.push('spouse_income_outside_cad');
                showFieldError('spouse_income_outside_cad', spouseIncomeOutside);
                checkedFields.add('spouse_income_outside_cad');
              }
            }
          }
        }
      }
      
      // If spouse_address_same = No, require spouse address fields
      const spouseAddrSameChecked = currentPanel.querySelector('input[name="spouse_address_same"]:checked');
      if (spouseAddrSameChecked) {
        const spouseAddrSame = spouseAddrSameChecked.value;
        if (spouseAddrSame === 'No') {
          const spouseAddressFields = currentPanel.querySelector('#spouse-address-fields');
          if (spouseAddressFields && spouseAddressFields.offsetParent !== null) {
            const spouseStreet = currentPanel.querySelector('#spouse_street');
            const spouseCity = currentPanel.querySelector('#spouse_city');
            const spouseProvince = currentPanel.querySelector('#spouse_province');
            const spousePostal = currentPanel.querySelector('#spouse_postal');
            
            [spouseStreet, spouseCity, spouseProvince, spousePostal].forEach(field => {
              if (field && field.offsetParent !== null) {
                const value = field.value ? field.value.trim() : '';
                if (value === '' && !checkedFields.has(field.name)) {
                  errors.push(field.name);
                  showFieldError(field.name, field);
                  checkedFields.add(field.name);
                }
              }
            });
          }
        }
      }
    }
    
    // Special validation for spouse-tax panel: check conditionally visible fields
    if (CURRENT === 'spouse-tax') {
      // First, ensure the gated question itself is answered
      const spFirstTimeChecked = currentPanel.querySelector('input[name="sp_first_time"]:checked');
      if (!spFirstTimeChecked) {
        const spFirstTimeRadio = currentPanel.querySelector('input[name="sp_first_time"]');
        if (spFirstTimeRadio && !checkedFields.has('sp_first_time')) {
          errors.push('sp_first_time');
          showFieldError('sp_first_time', spFirstTimeRadio);
          checkedFields.add('sp_first_time');
        }
      }
      
      // Check sp_first_time question
      const spFirstTime = spFirstTimeChecked?.value;
      
      if (spFirstTime === 'no') {
        // If sp_first_time = no, require sp_paragon_prior and sp_return_years
        const spPriorSection = currentPanel.querySelector('#sp-prior-customer-section');
        if (spPriorSection && spPriorSection.offsetParent !== null) {
          const spParagonPrior = currentPanel.querySelector('input[name="sp_paragon_prior"]:checked');
          if (!spParagonPrior) {
            const firstRadio = currentPanel.querySelector('input[name="sp_paragon_prior"]');
            if (firstRadio && !checkedFields.has('sp_paragon_prior')) {
              errors.push('sp_paragon_prior');
              showFieldError('sp_paragon_prior', firstRadio);
              checkedFields.add('sp_paragon_prior');
            }
          }
          
          const spReturnYears = currentPanel.querySelector('#sp_return_years');
          if (spReturnYears && spReturnYears.offsetParent !== null) {
            const value = spReturnYears.value ? spReturnYears.value.trim() : '';
            if (value === '' && !checkedFields.has('sp_return_years')) {
              errors.push('sp_return_years');
              showFieldError('sp_return_years', spReturnYears);
              checkedFields.add('sp_return_years');
            }
          }
        }
      } else if (spFirstTime === 'yes') {
        // If sp_first_time = yes, require sp_entry_date and sp_birth_country
        const spFirstTimeSection = currentPanel.querySelector('#sp-firsttime-details');
        if (spFirstTimeSection && spFirstTimeSection.offsetParent !== null) {
          const spEntryDateDisplay = currentPanel.querySelector('#sp_entry_date_display');
          if (spEntryDateDisplay && spEntryDateDisplay.offsetParent !== null) {
            const value = spEntryDateDisplay.value ? spEntryDateDisplay.value.trim() : '';
            if (value === '' && !checkedFields.has('sp_entry_date')) {
              errors.push('sp_entry_date');
              showFieldError('sp_entry_date', spEntryDateDisplay);
              checkedFields.add('sp_entry_date');
            }
          }
          
          const spBirthCountry = currentPanel.querySelector('#sp_birth_country');
          if (spBirthCountry && spBirthCountry.offsetParent !== null) {
            const value = spBirthCountry.value ? spBirthCountry.value.trim() : '';
            if (value === '' && !checkedFields.has('sp_birth_country')) {
              errors.push('sp_birth_country');
              showFieldError('sp_birth_country', spBirthCountry);
              checkedFields.add('sp_birth_country');
            }
          }
        }
      }
      
      // Ensure sp_moved_province gated question is answered
      const spMovedProvinceChecked = currentPanel.querySelector('input[name="sp_moved_province"]:checked');
      if (!spMovedProvinceChecked) {
        const spMovedProvinceRadio = currentPanel.querySelector('input[name="sp_moved_province"]');
        if (spMovedProvinceRadio && !checkedFields.has('sp_moved_province')) {
          errors.push('sp_moved_province');
          showFieldError('sp_moved_province', spMovedProvinceRadio);
          checkedFields.add('sp_moved_province');
        }
      }
      
      // Check sp_moved_province question
      const spMovedProvince = spMovedProvinceChecked?.value;
      
      if (spMovedProvince === 'yes') {
        // If sp_moved_province = yes, require sp_moved_date, sp_prov_from, sp_prov_to
        const spMovedSection = currentPanel.querySelector('#sp-moved-section');
        if (spMovedSection && spMovedSection.offsetParent !== null) {
          const spMovedDateDisplay = currentPanel.querySelector('#sp_moved_date_display');
          if (spMovedDateDisplay && spMovedDateDisplay.offsetParent !== null) {
            const value = spMovedDateDisplay.value ? spMovedDateDisplay.value.trim() : '';
            if (value === '' && !checkedFields.has('sp_moved_date')) {
              errors.push('sp_moved_date');
              showFieldError('sp_moved_date', spMovedDateDisplay);
              checkedFields.add('sp_moved_date');
            }
          }
          
          const spProvFrom = currentPanel.querySelector('#sp_prov_from');
          if (spProvFrom && spProvFrom.offsetParent !== null) {
            const value = spProvFrom.value ? spProvFrom.value.trim() : '';
            if ((value === '' || value === 'Select State/Province') && !checkedFields.has('sp_prov_from')) {
              errors.push('sp_prov_from');
              showFieldError('sp_prov_from', spProvFrom);
              checkedFields.add('sp_prov_from');
            }
          }
          
          const spProvTo = currentPanel.querySelector('#sp_prov_to');
          if (spProvTo && spProvTo.offsetParent !== null) {
            const value = spProvTo.value ? spProvTo.value.trim() : '';
            if ((value === '' || value === 'Select State/Province') && !checkedFields.has('sp_prov_to')) {
              errors.push('sp_prov_to');
              showFieldError('sp_prov_to', spProvTo);
              checkedFields.add('sp_prov_to');
            }
          }
        }
      }
    }
    
    // Special validation for other-income panel: check conditionally visible fields
    if (CURRENT === 'other-income') {
      // First, ensure the gated questions themselves are answered
      const gigIncomeChecked = currentPanel.querySelector('input[name="gig_income"]:checked');
      if (!gigIncomeChecked) {
        const gigIncomeRadio = currentPanel.querySelector('input[name="gig_income"]');
        if (gigIncomeRadio && !checkedFields.has('gig_income')) {
          errors.push('gig_income');
          showFieldError('gig_income', gigIncomeRadio);
          checkedFields.add('gig_income');
        }
      }
      
      // Check gig_income question
      const gigIncome = gigIncomeChecked?.value;
      
      if (gigIncome === 'yes') {
        // If gig_income = yes, require gig_expenses_summary and gig_hst
        const gigExpensesBlock = currentPanel.querySelector('#gig-expenses-block');
        if (gigExpensesBlock && gigExpensesBlock.offsetParent !== null) {
          const gigExpensesSummary = currentPanel.querySelector('#gig_expenses_summary');
          if (gigExpensesSummary && gigExpensesSummary.offsetParent !== null) {
            const value = gigExpensesSummary.value ? gigExpensesSummary.value.trim() : '';
            if (value === '' && !checkedFields.has('gig_expenses_summary')) {
              errors.push('gig_expenses_summary');
              showFieldError('gig_expenses_summary', gigExpensesSummary);
              checkedFields.add('gig_expenses_summary');
            }
          }
          
          // Ensure gig_hst question is answered when visible
          const hstQBlock = currentPanel.querySelector('#hst-q-block');
          if (hstQBlock && hstQBlock.offsetParent !== null) {
            const gigHstChecked = currentPanel.querySelector('input[name="gig_hst"]:checked');
            if (!gigHstChecked) {
              const gigHstRadio = currentPanel.querySelector('input[name="gig_hst"]');
              if (gigHstRadio && !checkedFields.has('gig_hst')) {
                errors.push('gig_hst');
                showFieldError('gig_hst', gigHstRadio);
                checkedFields.add('gig_hst');
              }
            }
          }
          
          // Check gig_hst question
          const gigHst = currentPanel.querySelector('input[name="gig_hst"]:checked')?.value;
          if (gigHst === 'yes') {
            const hstFields = currentPanel.querySelector('#hst-fields');
            if (hstFields && hstFields.offsetParent !== null) {
              const hstNumber = currentPanel.querySelector('#hst_number');
              const hstAccess = currentPanel.querySelector('#hst_access');
              const hstStart = currentPanel.querySelector('#hst_start');
              const hstEnd = currentPanel.querySelector('#hst_end');
              
              [hstNumber, hstAccess, hstStart, hstEnd].forEach(field => {
                if (field && field.offsetParent !== null) {
                  const value = field.value ? field.value.trim() : '';
                  if (value === '' && !checkedFields.has(field.name)) {
                    errors.push(field.name);
                    showFieldError(field.name, field);
                    checkedFields.add(field.name);
                  }
                }
              });
            }
          }
        }
      }
      
      // Check sp_gig_income question (if visible)
      const spGigQuestion = currentPanel.querySelector('#sp-gig-question');
      
      if (spGigQuestion && spGigQuestion.offsetParent !== null) {
        // Ensure sp_gig_income question is answered when visible
        const spGigIncomeChecked = currentPanel.querySelector('input[name="sp_gig_income"]:checked');
        if (!spGigIncomeChecked) {
          const spGigIncomeRadio = currentPanel.querySelector('input[name="sp_gig_income"]');
          if (spGigIncomeRadio && !checkedFields.has('sp_gig_income')) {
            errors.push('sp_gig_income');
            showFieldError('sp_gig_income', spGigIncomeRadio);
            checkedFields.add('sp_gig_income');
          }
        }
        
        const spGigIncome = spGigIncomeChecked?.value;
        
        if (spGigIncome === 'yes') {
        // If sp_gig_income = yes, require sp_gig_expenses_summary and sp_gig_hst
        const spGigExpensesBlock = currentPanel.querySelector('#sp-gig-expenses-block');
        if (spGigExpensesBlock && spGigExpensesBlock.offsetParent !== null) {
          const spGigExpensesSummary = currentPanel.querySelector('#sp_gig_expenses_summary');
          if (spGigExpensesSummary && spGigExpensesSummary.offsetParent !== null) {
            const value = spGigExpensesSummary.value ? spGigExpensesSummary.value.trim() : '';
            if (value === '' && !checkedFields.has('sp_gig_expenses_summary')) {
              errors.push('sp_gig_expenses_summary');
              showFieldError('sp_gig_expenses_summary', spGigExpensesSummary);
              checkedFields.add('sp_gig_expenses_summary');
            }
          }
          
          // Ensure sp_gig_hst question is answered when visible
          const spHstQBlock = currentPanel.querySelector('#sp-hst-q-block');
          if (spHstQBlock && spHstQBlock.offsetParent !== null) {
            const spGigHstChecked = currentPanel.querySelector('input[name="sp_gig_hst"]:checked');
            if (!spGigHstChecked) {
              const spGigHstRadio = currentPanel.querySelector('input[name="sp_gig_hst"]');
              if (spGigHstRadio && !checkedFields.has('sp_gig_hst')) {
                errors.push('sp_gig_hst');
                showFieldError('sp_gig_hst', spGigHstRadio);
                checkedFields.add('sp_gig_hst');
              }
            }
          }
          
          // Check sp_gig_hst question
          const spGigHst = currentPanel.querySelector('input[name="sp_gig_hst"]:checked')?.value;
          if (spGigHst === 'yes') {
            const spHstFields = currentPanel.querySelector('#sp-hst-fields');
            if (spHstFields && spHstFields.offsetParent !== null) {
              const spHstNumber = currentPanel.querySelector('#sp_hst_number');
              const spHstAccess = currentPanel.querySelector('#sp_hst_access');
              const spHstStart = currentPanel.querySelector('#sp_hst_start');
              const spHstEnd = currentPanel.querySelector('#sp_hst_end');
              
              [spHstNumber, spHstAccess, spHstStart, spHstEnd].forEach(field => {
                if (field && field.offsetParent !== null) {
                  const value = field.value ? field.value.trim() : '';
                  if (value === '' && !checkedFields.has(field.name)) {
                    errors.push(field.name);
                    showFieldError(field.name, field);
                    checkedFields.add(field.name);
                  }
                }
              });
            }
          }
        }
        }
      }
      
      // Ensure rentalIncome question is answered
      const rentalIncomeChecked = currentPanel.querySelector('input[name="rentalIncome"]:checked');
      if (!rentalIncomeChecked) {
        const rentalIncomeRadio = currentPanel.querySelector('input[name="rentalIncome"]');
        if (rentalIncomeRadio && !checkedFields.has('rentalIncome')) {
          errors.push('rentalIncome');
          showFieldError('rentalIncome', rentalIncomeRadio);
          checkedFields.add('rentalIncome');
        }
      } else {
        // If rentalIncome = yes, require at least one property
        const rentalIncome = rentalIncomeChecked.value;
        if (rentalIncome === 'yes') {
          const rentalSection = currentPanel.querySelector('#rental-income-section');
          if (rentalSection && rentalSection.offsetParent !== null) {
            const propsTable = currentPanel.querySelector('#props-tbody');
            const emptyRow = currentPanel.querySelector('#props-empty-row');
            
            if (propsTable) {
              // Get all property rows (excluding the empty row)
              const propRows = propsTable.querySelectorAll('tr:not(#props-empty-row)');
              const hasProperties = propRows.length > 0;
              
              // Also check if empty row is visible (means no properties)
              const isEmpty = emptyRow && emptyRow.offsetParent !== null;
              
              // If no properties are added, show error
              if (!hasProperties || isEmpty) {
                errors.push('rental_properties_required');
                // Show error banner
                showPanelError('At least one rental property must be added when "Do you have rental income?" is Yes.');
                
                // Add field-level error styling
                const rentalBlock = rentalSection.querySelector('.qs-block');
                if (rentalBlock && !rentalBlock.classList.contains('error')) {
                  rentalBlock.classList.add('error');
                  // Check if error message already exists
                  let errorText = rentalBlock.querySelector('.fi-error-text');
                  if (!errorText) {
                    errorText = document.createElement('div');
                    errorText.className = 'fi-error-text';
                    errorText.id = 'error-rental_properties';
                    errorText.textContent = 'At least one rental property must be added.';
                    // Insert after the table
                    const table = rentalBlock.querySelector('#rental-table');
                    if (table) {
                      table.parentNode.insertBefore(errorText, table.nextSibling);
                    } else {
                      rentalBlock.appendChild(errorText);
                    }
                  }
                }
              } else {
                // Clear error if properties are added
                const rentalBlock = rentalSection.querySelector('.qs-block');
                if (rentalBlock) {
                  rentalBlock.classList.remove('error');
                  const errorText = rentalBlock.querySelector('.fi-error-text');
                  if (errorText) errorText.remove();
                }
              }
            }
          }
        }
      }
    }
    
    // Debug logging
    if (errors.length > 0) {
      console.log('Validation errors for panel', CURRENT, ':', errors);
    }
    
    return errors.length === 0;
  }
  
  // ====== Show error banner in current panel
  function showPanelError(message) {
    const currentPanel = document.querySelector(`.pi-main[data-panel="${CURRENT}"]`);
    if (!currentPanel) return;
    
    // Check if error banner already exists in this panel
    let errorBanner = currentPanel.querySelector('.pi-error-banner');
    if (!errorBanner) {
      // Create error banner
      errorBanner = document.createElement('div');
      errorBanner.className = 'qs-error-banner pi-error-banner';
      errorBanner.setAttribute('role', 'alert');
      errorBanner.setAttribute('aria-live', 'polite');
      errorBanner.setAttribute('aria-atomic', 'true');
      errorBanner.innerHTML = `
        <h3>A selection is required.</h3>
        <p>To proceed, please fill in or correct the required field(s).</p>
      `;
      // Insert at the top of the panel content
      const panelContent = currentPanel.querySelector('.pi-content') || currentPanel;
      const firstChild = panelContent.firstElementChild;
      if (firstChild) {
        panelContent.insertBefore(errorBanner, firstChild);
      } else {
        panelContent.appendChild(errorBanner);
      }
    }
    
    errorBanner.classList.add('show');
    
    // Scroll to top of the page to ensure error banner is fully visible
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Also scroll the error banner into view as a backup
    setTimeout(() => {
      errorBanner.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 100);
  }
  
  // ====== Hide error banner in current panel
  function hidePanelError() {
    const currentPanel = document.querySelector(`.pi-main[data-panel="${CURRENT}"]`);
    if (!currentPanel) return;
    
    const errorBanner = currentPanel.querySelector('.pi-error-banner');
    if (errorBanner) {
      errorBanner.classList.remove('show');
    }
  }

  // ====== Form Submission Handler
  let isSubmitting = false;
  
  async function submitForm() {
    if (isSubmitting) {
      console.log('Form submission already in progress');
      return false;
    }

    // Validate current panel before submission
    if (!validateCurrentPanel()) {
      showPanelError();
      return false;
    }

    isSubmitting = true;
    const submitBtn = document.getElementById('review-submit-btn');
    const submitStatus = document.getElementById('review-submit-status');
    const originalText = (submitBtn?.textContent || 'Submit').trim();
    let stillTimer = null;

    function setSubmitState(state){
      if (!submitBtn) return;
      if (state === 'processing') {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `Processing <span class="proc-dots" aria-hidden="true"><span></span><span></span><span></span></span>`;
        submitBtn.setAttribute('aria-busy','true');
        if (submitStatus) {
          submitStatus.hidden = false;
          submitStatus.textContent = 'Please keep this tab open.';
        }
        // after a short delay, reassure the user
        stillTimer = window.setTimeout(() => {
          if (submitStatus && !submitStatus.hidden) {
            submitStatus.textContent = 'Still working... Please keep this tab open.';
          }
        }, 9000);
        return;
      }
      if (stillTimer) { window.clearTimeout(stillTimer); stillTimer = null; }
      submitBtn.removeAttribute('aria-busy');
      if (state === 'submitted') {
        submitBtn.textContent = 'Submitted';
        submitBtn.disabled = true;
        if (submitStatus) submitStatus.hidden = true;
        return;
      }
      // idle / error reset
      submitBtn.disabled = false;
      submitBtn.textContent = originalText;
      if (submitStatus) submitStatus.hidden = true;
    }

    setSubmitState('processing');

    try {
      // Collect all form data
      const formData = new FormData();

      // Shared mapping for both inputs and radios to match gmailapi.php field expectations
      const FIELD_NAME_MAP = {
        // Personal
        first_name: 'firstName',
        last_name: 'lastName',
        street: 'ship_address',
        city: 'locality',
        unit: 'apartment_unit_number',
        province: 'state',
        postal: 'postcode',
        dob: 'birth_date',
        sin: 'sin_number',

        // Tax panel
        first_time: 'first_fillingtax',
        paragon_prior: 'file_paragon',
        return_years: 'years_tax_return',
        entry_date: 'canada_entry',
        moved_province: 'another_province',
        moved_date: 'move_date',
        prov_from: 'move_from',
        prov_to: 'move_to',
        first_home_buyer: 'first_time_buyer',
        first_home_purchase: 'purchase_first_home',

        // Pre-details
        spouseFile: 'spouse_file_tax',
        children: 'have_child',
        spouse_in_canada: 'residing_canada',

        // Spouse personal/tax
        spouse_first_name: 'spouse_firstname',
        spouse_last_name: 'spouse_lastname',
        spouse_dob: 'spouse_date_birth',
        spouse_first_time: 'spouse_first_tax',
        sp_first_time: 'spouse_first_tax',
        sp_paragon_prior: 'spouse_file_paragon',
        sp_return_years: 'spouse_years_tax_return',
        sp_entry_date: 'spouse_canada_entry',
        sp_birth_country: 'spouse_birth_country',
        spouse_income_cad: 'spouse_annual_income',
        spouse_income_outside_cad: 'spouse_annual_income_outside',

        // Other income / HST
        gig_income: 'income_delivery',
        gig_expenses_summary: 'summary_expenses',
        gig_hst: 'delivery_hst',
        inc_y1: 'year1_income',
        inc_y2: 'year2_income',
        inc_y3: 'year3_income',
        hst_access: 'hst_access_code',
        hst_start: 'hst_start_date',
        hst_end: 'hst_end_date',

        sp_gig_income: 'spouse_income_delivery',
        sp_gig_expenses_summary: 'spouse_summary_expenses',
        sp_gig_hst: 'spouse_delivery_hst',
        sp_inc_y1: 'spouse_year1_income',
        sp_inc_y2: 'spouse_year2_income',
        sp_inc_y3: 'spouse_year3_income',
        sp_hst_number: 'spouse_hst_number',
        sp_hst_access: 'spouse_hst_access_code',
        sp_hst_start: 'spouse_hst_start_date',
        sp_hst_end: 'spouse_hst_end_date',

        // Review message
        other_message: 'message_us',
      };

      function mapFieldName(name) {
        return FIELD_NAME_MAP[name] || name;
      }
      function mapFieldValue(v) {
        if (v === 'yes') return 'Yes';
        if (v === 'no') return 'No';
        return v;
      }

      // Helper to get file URLs from a dropzone input
      // Files are stored as File objects in the input's files property
      // We'll collect them and append to FormData, then gmailapi.php can handle upload/URL generation
      function getFilesFromInput(inputId) {
        const input = document.getElementById(inputId);
        if (!input || !input.files || input.files.length === 0) return [];
        return Array.from(input.files);
      }

      // Helper to append files to FormData
      // Files will be uploaded and URLs generated in gmailapi.php
      function appendFilesToFormData(formData, inputId, fieldName) {
        const files = getFilesFromInput(inputId);
        if (files.length === 0) return;
        
        // Append each file to FormData with the expected field name format
        // PHP will receive these in $_FILES[fieldName_files]
        files.forEach((file) => {
          formData.append(`${fieldName}_files[]`, file);
        });
      }

      // Collect all input fields (including empty ones - backend will handle validation)
      const inputs = document.querySelectorAll('input:not([type="radio"]):not([type="checkbox"]):not([type="file"]):not([type="button"]):not([type="submit"]), textarea, select');
      inputs.forEach(input => {
        if (input.name) {
          const fieldName = mapFieldName(input.name);
          const fieldValue = mapFieldValue(input.value || '');
          formData.append(fieldName, fieldValue);
        }
      });

      // Collect radio button values
      const radios = document.querySelectorAll('input[type="radio"]:checked');
      radios.forEach(radio => {
        if (!radio.name) return;
        const fieldName = mapFieldName(radio.name);
        const fieldValue = mapFieldValue(radio.value);
        formData.append(fieldName, fieldValue);
      });

      // Marital date: map shared status_date into the legacy columns used by gmailapi.php
      const ms = document.querySelector('input[name="marital_status"]:checked')?.value
        || document.getElementById('marital_status_select')?.value
        || '';
      const sdw = ['Separated','Divorced','Widowed'].includes(ms);
      const statusDate = (document.getElementById(sdw ? 'status_date_sdw' : 'status_date')?.value || '').trim();
      if (statusDate) {
        formData.append(sdw ? 'marital_change' : 'date_marriage', statusDate);
      }

      // World income periods (the period rows are divs, but email expects year1/year2/year3)
      const p1 = (document.getElementById('period_y1')?.textContent || '').trim();
      const p2 = (document.getElementById('period_y2')?.textContent || '').trim();
      const p3 = (document.getElementById('period_y3')?.textContent || '').trim();
      if (p1 && p1 !== '—') formData.append('year1', p1);
      if (p2 && p2 !== '—') formData.append('year2', p2);
      if (p3 && p3 !== '—') formData.append('year3', p3);

      const spP1 = (document.getElementById('sp_period_y1')?.textContent || '').trim();
      const spP2 = (document.getElementById('sp_period_y2')?.textContent || '').trim();
      const spP3 = (document.getElementById('sp_period_y3')?.textContent || '').trim();
      if (spP1 && spP1 !== '—') formData.append('spouse_year1', spP1);
      if (spP2 && spP2 !== '—') formData.append('spouse_year2', spP2);
      if (spP3 && spP3 !== '—') formData.append('spouse_year3', spP3);

      // Collect files from dropzone inputs
      // Files will be sent as FormData and gmailapi.php will handle upload/URL generation
      // Applicant files
      appendFilesToFormData(formData, 'app_id_proof', 'id_proof');
      appendFilesToFormData(formData, 'app_tslips', 't_slips');
      appendFilesToFormData(formData, 'app_t2202_receipt', 'college_receipt');
      appendFilesToFormData(formData, 'app_invest', 'direct_deposits');
      appendFilesToFormData(formData, 'app_t2200_work', 'additional_docs');
      appendFilesToFormData(formData, 'app_exp_summary', 'additional_docs');
      appendFilesToFormData(formData, 'app_otherdocs', 'additional_docs');
      appendFilesToFormData(formData, 'gig_tax_summary', 'tax_summary');

      // Spouse files
      appendFilesToFormData(formData, 'sp_id_proof', 'spouse_id_proof');
      appendFilesToFormData(formData, 'sp_tslips', 'spouse_t_slips');
      appendFilesToFormData(formData, 'sp_invest', 'spouse_direct_deposits');
      appendFilesToFormData(formData, 'sp_gig_tax_summary', 'spouse_tax_summary');

      // Collect children data (legacy expects `data[...]`)
      if (typeof CHILDREN !== 'undefined' && Array.isArray(CHILDREN) && CHILDREN.length > 0) {
        CHILDREN.forEach((child, index) => {
          formData.append(`data[${index}][child_first_name]`, child.first_name || '');
          formData.append(`data[${index}][child_last_name]`, child.last_name || '');
          formData.append(`data[${index}][child_date_birth]`, child.dob || '');
          formData.append(`data[${index}][child_residing_canada]`, child.in_canada || '');
        });
      }

      // Collect properties data (format as group-a array)
      if (typeof PROPS !== 'undefined' && PROPS.length > 0) {
        PROPS.forEach((prop, index) => {
          formData.append(`group-a[${index}][rent_address]`, prop.address || '');
          formData.append(`group-a[${index}][from_month]`, prop.start_display || '');
          formData.append(`group-a[${index}][from_year]`, prop.start_display || '');
          formData.append(`group-a[${index}][to_month]`, prop.end_display || '');
          formData.append(`group-a[${index}][to_year]`, prop.end_display || '');
          formData.append(`group-a[${index}][total_rent_paid]`, prop.gross || '');
        });
      }

      // Collect rent addresses (if using RENTS array)
      if (typeof RENTS !== 'undefined' && RENTS.length > 0) {
        RENTS.forEach((rent, index) => {
          formData.append(`group-a[${index}][rent_address]`, rent.address || '');
          formData.append(`group-a[${index}][from_month]`, rent.from_month || '');
          formData.append(`group-a[${index}][from_year]`, rent.from_year || '');
          formData.append(`group-a[${index}][to_month]`, rent.to_month || '');
          formData.append(`group-a[${index}][to_year]`, rent.to_year || '');
          formData.append(`group-a[${index}][total_rent_paid]`, rent.total_rent_paid || '');
        });
      }

      // Submit via AJAX
      const response = await fetch('../gmailapi.php', {
        method: 'POST',
        body: formData
      });

      const raw = await response.text();
      let result;
      try {
        // gmailapi.php now returns JSON; tolerate accidental leading text
        const jsonStart = raw.indexOf('{');
        result = JSON.parse(jsonStart >= 0 ? raw.slice(jsonStart) : raw);
      } catch (e) {
        throw new Error('Submission failed: ' + raw);
      }

      if (!response.ok || result?.status !== 'success') {
        throw new Error('Submission failed: ' + (result?.error || raw));
      }

      // Success: move to Confirmation panel and show read-only review accordion
      hidePanelError();
      markPanelCompleted('review');

      const mount = document.getElementById('confirm-accordion-mount');
      const acc = document.getElementById('rev-accordion');
      if (mount && acc) {
        // Remove edit/jump links so it's read-only
        acc.querySelectorAll('.rev-actions').forEach(n => n.remove());
        mount.innerHTML = '';
        mount.appendChild(acc);
      }

      showPanel('confirm');
      window.scrollTo({ top: 0, behavior: 'smooth' });

      setSubmitState('submitted');
    } catch (error) {
      console.error('Form submission error:', error);
      showPanelError('Failed to submit form. Please try again or contact support.');
      setSubmitState('idle');
      isSubmitting = false;
      return false;
    } finally {
      isSubmitting = false;
    }
  }

  // ====== In-form nav buttons
  formPanel?.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-goto]');
    if (!btn) return;
    e.preventDefault();

    const target = btn.getAttribute('data-goto');

    if (target === 'welcome') {
      formPanel.style.display = 'none';
      if (welcomePanel) welcomePanel.style.display = 'block';
      refreshWelcomeBlocks();
      window.scrollTo({top:0,behavior:'smooth'});
      return;
    }

    // Validate before proceeding to next step
    if (target === 'next') {
      // Check if we're on the review panel - if so, submit instead of navigating
      if (CURRENT === 'review') {
        submitForm();
        return false;
      }
      
      console.log('Continue clicked, validating panel:', CURRENT);
      const isValid = validateCurrentPanel();
      console.log('Validation result:', isValid);
      if (!isValid) {
        console.log('Validation failed, showing error');
        showPanelError();
        return false;
      }
      console.log('Validation passed, proceeding to next panel');
      hidePanelError();
      // Mark current panel as completed
      markPanelCompleted(CURRENT);
      return showPanel(nextFrom(CURRENT));
    }
    
    if (target === 'prev') {
      hidePanelError(); // Hide errors when going back
      return showPanel(prevFrom(CURRENT));
    }

    const stepsNow = activeSteps();
    if (!stepsNow.includes(target)) {
      const forward = ORDER.indexOf(target) >= ORDER.indexOf(CURRENT);
      if (forward && !validateCurrentPanel()) {
        showPanelError();
        return false;
      }
      hidePanelError();
      return showPanel(forward ? nextFrom(CURRENT) : prevFrom(CURRENT));
    }
    
    // When jumping to a specific step forward, validate current first
    if (ORDER.indexOf(target) > ORDER.indexOf(CURRENT)) {
      if (!validateCurrentPanel()) {
        showPanelError();
        return false;
      }
      // Mark current panel as completed if validation passed
      markPanelCompleted(CURRENT);
    }
    hidePanelError();
    showPanel(target);
  });

  // ====== Watch answers
  document.addEventListener('change', (e) => {
    const t = e.target;
    if (!(t instanceof HTMLInputElement)) return;

    const watched = [
      'marital_status','spouseFile','children','onRent',
      'rentalIncome','selfEmp','rentBenefit','spouse_in_canada', 'gig_income'
    ];
    if (!watched.includes(t.name)) return;

    // Hide error banner when user makes a selection
    const qsError = document.getElementById('qsError');
    if (qsError && watched.includes(t.name)) {
      const maritalStatus = document.querySelector('input[name="marital_status"]:checked');
      const spouseInCanada = document.querySelector('input[name="spouse_in_canada"]:checked');
      const children = document.querySelector('input[name="children"]:checked');
      
      // Only hide if all required fields are now filled
      if (maritalStatus && spouseInCanada && children) {
        qsError.classList.remove('show');
      }
    }
    
    // Clear field errors when user fills required fields
    // Check if field is required (in REQUIRED_FIELDS, has required attribute, or is conditionally required)
    if (t.name) {
      const isBaseRequired = REQUIRED_FIELDS.includes(t.name);
      const hasRequiredAttr = t.hasAttribute('required');
      const conditionalFields = getConditionallyRequiredFields();
      const isConditionallyRequired = conditionalFields.includes(t.name);
      
      if (isBaseRequired || hasRequiredAttr || isConditionallyRequired) {
        if (t.type === 'radio' || t.type === 'checkbox') {
          const checked = document.querySelector(`input[name="${t.name}"]:checked`);
          if (checked) {
            hideFieldError(t.name);
          }
        } else {
          const value = t.value ? t.value.trim() : '';
          if (value !== '') {
            hideFieldError(t.name);
          }
        }
      }
    }

    refreshWelcomeBlocks();
    refreshOtherIncomePanel();
    if (formPanel && formPanel.style.display !== 'none' && CURRENT) {
      updateProgress(CURRENT);
    }
    // also keep Review accordion current (if open)
    if (document.querySelector('.pi-main[data-panel="review"]')?.hidden === false){
      refreshReviewAccordion();
      updatePreRows();
      updateBindings();
    }
  });
  
  // Watch for input changes to clear field errors
  document.addEventListener('input', (e) => {
    const field = e.target;
    if (field && field.name) {
      // Check if field is required (in REQUIRED_FIELDS, has required attribute, or is conditionally required)
      const isBaseRequired = REQUIRED_FIELDS.includes(field.name);
      const hasRequiredAttr = field.hasAttribute('required');
      const conditionalFields = getConditionallyRequiredFields();
      const isConditionallyRequired = conditionalFields.includes(field.name);
      
      if (isBaseRequired || hasRequiredAttr || isConditionallyRequired) {
        const value = field.value ? field.value.trim() : '';
        if (value !== '') {
          hideFieldError(field.name);
        }
      }
      
      // For date display fields, also clear error on the bound hidden field
      if (field.hasAttribute('data-bind')) {
        const bindTarget = field.getAttribute('data-bind');
        if (bindTarget) {
          const hiddenField = document.querySelector(bindTarget);
          if (hiddenField && hiddenField.name) {
            const value = field.value ? field.value.trim() : '';
            if (value !== '') {
              hideFieldError(hiddenField.name);
            }
          }
        }
      }
    }
    
    // Restrict SIN fields to numbers only
    if (field && (field.name === 'sin' || field.name === 'spouse_sin')) {
      // Remove any non-numeric characters
      field.value = field.value.replace(/[^0-9]/g, '');
    }
  });
  
  // Watch for select changes to clear field errors
  document.addEventListener('change', (e) => {
    const field = e.target;
    if (field && field.tagName === 'SELECT' && field.name) {
      // Check if field is required
      const isBaseRequired = REQUIRED_FIELDS.includes(field.name);
      const hasRequiredAttr = field.hasAttribute('required');
      const conditionalFields = getConditionallyRequiredFields();
      const isConditionallyRequired = conditionalFields.includes(field.name);
      
      if (isBaseRequired || hasRequiredAttr || isConditionallyRequired) {
        const value = field.value ? field.value.trim() : '';
        if (value !== '' && value !== 'Select State/Province' && value !== 'Select Country') {
          hideFieldError(field.name);
        }
      }
    }
  });
  
  // Watch for file uploads in ID Proof section to clear error (Applicant)
  const idProofList = document.getElementById('app_id_list');
  if (idProofList) {
    const observer = new MutationObserver(() => {
      const idProofItems = idProofList.querySelectorAll('.dz-item');
      if (idProofItems.length > 0 && CURRENT === 'upload-self') {
        // Clear error banner if files are uploaded
        hidePanelError();
      }
    });
    observer.observe(idProofList, { childList: true, subtree: true });
  }
  
  // Watch for file uploads in Spouse ID Proof section to clear error
  const spIdProofList = document.getElementById('sp_id_list');
  if (spIdProofList) {
    const observer = new MutationObserver(() => {
      const idProofItems = spIdProofList.querySelectorAll('.dz-item');
      if (idProofItems.length > 0 && CURRENT === 'upload-spouse') {
        // Clear error banner if files are uploaded
        hidePanelError();
        // Clear field-level error
        const idProofBlock = spIdProofList.closest('.qs-block');
        if (idProofBlock) {
          idProofBlock.classList.remove('error');
          const errorText = idProofBlock.querySelector('.fi-error-text');
          if (errorText) errorText.remove();
        }
      }
    });
    observer.observe(spIdProofList, { childList: true, subtree: true });
  }
  
  // Watch for file uploads in Spouse Self-employed Income section to clear error
  const spGigFilesList = document.getElementById('sp-gig-files');
  if (spGigFilesList) {
    const observer = new MutationObserver(() => {
      const gigFileItems = spGigFilesList.querySelectorAll('.dz-item');
      if (gigFileItems.length > 0 && CURRENT === 'upload-spouse') {
        // Clear error banner if files are uploaded
        hidePanelError();
      }
    });
    observer.observe(spGigFilesList, { childList: true, subtree: true });
  }
  
  // Prevent non-numeric input in SIN fields
  document.addEventListener('keypress', (e) => {
    const field = e.target;
    if (field && (field.name === 'sin' || field.name === 'spouse_sin')) {
      // Only allow numbers (0-9)
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    }
  });
  
  // Prevent pasting non-numeric content in SIN fields
  document.addEventListener('paste', (e) => {
    const field = e.target;
    if (field && (field.name === 'sin' || field.name === 'spouse_sin')) {
      e.preventDefault();
      const paste = (e.clipboardData || window.clipboardData).getData('text');
      const numbersOnly = paste.replace(/[^0-9]/g, '');
      if (numbersOnly) {
        field.value = numbersOnly.substring(0, 9); // Limit to 9 digits
      }
    }
  });

  // ====== Welcome → Form
  $('#welcome-panel #qs-continue')?.addEventListener('click', ()=>{
    // Validate required fields before proceeding
    const qsError = document.getElementById('qsError');
    const maritalStatus = document.querySelector('input[name="marital_status"]:checked');
    const spouseInCanada = document.querySelector('input[name="spouse_in_canada"]:checked');
    const children = document.querySelector('input[name="children"]:checked');
    
    // Determine which fields are conditionally required based on marital status
    const maritalStatusValue = maritalStatus?.value || '';
    const marriedLike = (maritalStatusValue === 'Married' || maritalStatusValue === 'Common Law');
    const childQuestionVisible = (marriedLike || maritalStatusValue === 'Separated' || maritalStatusValue === 'Divorced' || maritalStatusValue === 'Widowed');
    
    // Check if all required fields are filled
    const errors = [];
    if (!maritalStatus) {
      errors.push('Marital Status');
    }
    
    // Only require spouse_in_canada if Married/Common Law
    if (marriedLike && !spouseInCanada) {
      errors.push('Residency in Canada information');
    }
    
    // Only require children if the question is visible (not Single)
    if (childQuestionVisible && !children) {
      errors.push('Children information');
    }
    
    if (errors.length > 0) {
      // Show error banner
      if (qsError) {
        qsError.classList.add('show');
        // Scroll to top of the page to ensure error banner is fully visible
        window.scrollTo({ top: 0, behavior: 'smooth' });
        // Also scroll the error banner into view as a backup
        setTimeout(() => {
          qsError.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 100);
      }
      return false; // Prevent proceeding
    }
    
    // Hide error banner if validation passes
    if (qsError) {
      qsError.classList.remove('show');
    }
    
    if (welcomePanel) welcomePanel.style.display = 'none';
    if (formPanel)    formPanel.style.display    = 'block';
    if (oldFormSidebar) oldFormSidebar.style.display = 'none';
    refreshWelcomeBlocks();
    refreshOtherIncomePanel();
    showPanel('personal');
  });

  // ====== Initial render
  refreshWelcomeBlocks();
  refreshOtherIncomePanel();
  // Show old-form-sidebar only when intro-layout is visible (intro-card is visible)
  // Sidebar should be visible when intro-card is visible and welcome/form panels are hidden
  if (oldFormSidebar) {
    const welcomeVisible = welcomePanel && welcomePanel.style.display === 'block';
    const formVisible = formPanel && formPanel.style.display === 'block';
    const introVisible = !welcomeVisible && !formVisible;
    oldFormSidebar.style.display = introVisible ? '' : 'none';
  }
  if (formPanel && formPanel.style.display !== 'none') {
    showPanel('personal');
  } else {
    updateProgress('personal');
  }

  // ====== Export for Review to use
  window.App = {
    flags,
    activeSteps,
    showPanel,
    updateProgress,
    refreshWelcomeBlocks,
    refreshOtherIncomePanel,
    goToWelcome(){
      if (formPanel)    formPanel.style.display = 'none';
      if (welcomePanel) welcomePanel.style.display = 'block';
      if (oldFormSidebar) oldFormSidebar.style.display = 'none';
      this.refreshWelcomeBlocks?.();
      window.scrollTo({top:0, behavior:'smooth'});
    },
    goToFormAndShow(panelKey){
      if (welcomePanel && welcomePanel.style.display !== 'none') {
        welcomePanel.style.display = 'none';
        if (formPanel) formPanel.style.display = 'block';
        if (oldFormSidebar) oldFormSidebar.style.display = 'none';
      }
      this.showPanel?.(panelKey);
    }
  };

  /* =========================
     REVIEW: behavior + binds
  ==========================*/

  // Inject tiny CSS to hide default CTAs in review-jump mode
(function injectReviewCSS(){
  const css = `
    /* Keep PREV visible; only hide CONTINUE in review-jump */
    .pi-main[data-review-jump="1"] .tax-cta .continue-btn { display:none !important; }

    /* Back-to-Review button is only visible in review-jump */
    .pi-main .tax-cta .review-back { display:none; }
    .pi-main[data-review-jump="1"] .tax-cta .review-back { display:inline-flex !important; }
  `;
  const s = document.createElement('style'); s.textContent = css; document.head.appendChild(s);
})();


  // Accordion expand/collapse
  const acc = document.getElementById('rev-accordion');
  if (acc){
    acc.addEventListener('click', (e)=>{
      const btn = e.target.closest('.rev-item'); if (!btn) return;
      const panel = document.getElementById(btn.getAttribute('aria-controls'));
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
      panel.hidden = expanded;
    });
  }

  // Data binding for review fields
  function formatDate(iso){
    if (!iso) return '';
    const d = new Date(iso);
    if (isNaN(d)) return iso;
    const m = String(d.getMonth()+1).padStart(2,'0');
    const day = String(d.getDate()).padStart(2,'0');
    const y = d.getFullYear();
    return `${m}/${day}/${y}`;
  }
  function getRadioValue(name){
    const el = document.querySelector(`input[name="${name}"]:checked`);
    return el ? el.value : '';
  }
  function readFromSelectorList(selList){
    const sels = (selList || '').split(',').map(s=>s.trim()).filter(Boolean);
    for (const sel of sels){
      const el = document.querySelector(sel);
      if (!el) continue;
      if (el.tagName === 'SELECT'){
        const i = el.selectedIndex;
        if (i >= 0) return el.options[i].text || el.value || '';
      }
      if ((el.type === 'checkbox' || el.type === 'radio')){
        if (el.checked) return el.value || '';
      }
      const v = (el.value || el.textContent || '').trim();
      if (v) return v;
    }
    return '';
  }
  function updateBindings(){
    document.querySelectorAll('[data-bind-text]').forEach(span=>{
      let val = readFromSelectorList(span.getAttribute('data-bind-text'));
      if (!val) val = span.getAttribute('data-fallback') || '—';
      if (span.getAttribute('data-format') === 'date' && val && val !== '—') val = formatDate(val);
      span.textContent = val;
    });
    document.querySelectorAll('[data-bind-radio]').forEach(span=>{
      const name = span.getAttribute('data-bind-radio');
      let val = getRadioValue(name);
      if (!val) val = span.getAttribute('data-fallback') || '—';
      span.textContent = val;
    });
  }
  updateBindings();
  document.addEventListener('input',  updateBindings);
  document.addEventListener('change', updateBindings);

  // Pre-rows visibility (Date/Residing/Spouse/Children)
  function updatePreRows(){
    const f = window.App.flags();
    document.querySelectorAll('#rev-pre .pre-cond').forEach(el=>{
      el.style.display = f.marriedLike ? '' : 'none';
    });
  }
  updatePreRows();

  // Accordion tab gating
  function toggleAcc(id, show){
    const btn   = document.getElementById(id + '-btn');
    const panel = document.getElementById(id);
    if (!btn || !panel) return;
    btn.style.display   = show ? '' : 'none';
    panel.style.display = show ? '' : 'none';
    if (!show){ btn.setAttribute('aria-expanded','false'); panel.hidden = true; }
  }
  function refreshReviewAccordion(){
    const f = window.App.flags();
    // default hide
    toggleAcc('rev-spouse', false);
    toggleAcc('rev-spouse-tax', false);
    toggleAcc('rev-children', false);
    // married/common-law => spouse
    if (f.marriedLike) toggleAcc('rev-spouse', true);
    // spouse tax only if spouse will file
    if (f.spouseFiles) toggleAcc('rev-spouse-tax', true);
    // children only if yes
    if (f.hasChildren) toggleAcc('rev-children', true);
  }
  refreshReviewAccordion();

  // Go to link handling (review-jump mode)
function ensureBackButton(panelEl){
  const cta = panelEl.querySelector('.tax-cta') || panelEl;
  let back = cta.querySelector('.review-back');
  if (!back){
    back = document.createElement('button');
    back.type = 'button';
    back.className = 'tax-btn-secondary review-back';
    back.textContent = 'Back to Review';
    cta.appendChild(back);
    back.addEventListener('click', ()=>{

      // leave review-jump mode + clean up buttons
      document.querySelectorAll('.pi-main[data-review-jump="1"]').forEach(p=>p.removeAttribute('data-review-jump'));
      document.querySelectorAll('.review-back').forEach(b=>b.remove());

      // show Review panel
      document.querySelectorAll('.pi-main').forEach(p=> p.hidden = true);
      const rev = document.querySelector('.pi-main[data-panel="review"]');
      if (rev) rev.hidden = false;

      // ✅ also fix the sidebar highlight
      window.App?.updateProgress?.('review');

      window.scrollTo({top:0, behavior:'smooth'});
    });
  } else {
    back.style.display = '';
  }
}

  document.addEventListener('click', (e)=>{
    const a = e.target.closest('.rev-link[data-open]');
    if (!a) return;
    e.preventDefault();
    const step = a.getAttribute('data-open');
    if (step === 'pre'){
      window.App.goToWelcome();
      window.App.updateProgress?.('personal');
      return;
    }
    window.App.goToFormAndShow(step);
    const panelEl = document.querySelector(`.pi-main[data-panel="${step}"]`);
    if (panelEl){
      panelEl.setAttribute('data-review-jump','1');
      ensureBackButton(panelEl);
      window.scrollTo({top:0, behavior:'smooth'});
    }
  });

  // Keep review gating updated on changes
  document.addEventListener('change', (e)=>{
    const t = e.target;
    if (!(t instanceof HTMLInputElement)) return;
    if (['marital_status','spouseFile','children','spouse_in_canada'].includes(t.name)){
      refreshReviewAccordion();
      updatePreRows();
    }
  });

});

</script>


<!-- YES MOBILE 1 -->

<script>
/* ===== PATCH: emit panel-change events + safe review-jump nav ===== */
(function () {
  if (window.__PI_PANEL_PATCH) return; window.__PI_PANEL_PATCH = true;

  const fireChanged = (panel) =>
    document.dispatchEvent(new CustomEvent('pi:panel-changed', { detail: { panel }}));

  // Wrap App.showPanel so every programmatic navigation notifies the mobile bar
  document.addEventListener('DOMContentLoaded', function () {
    if (window.App && typeof window.App.showPanel === 'function') {
      const _origShow = window.App.showPanel.bind(window.App);
      window.App.showPanel = function (id) {
        _origShow(id);
        fireChanged(id);
      };
    }
  });

  // When Review opens a panel (“Go to …”), fire the event too (capture to run once)
  document.addEventListener('click', function (e) {
    const a = e.target.closest('.rev-link[data-open]');
    if (!a) return;
    // Let your existing handler navigate first, then announce after the DOM swaps
    setTimeout(function () {
      const shown = document.querySelector('.pi-main[data-panel]:not([hidden])');
      if (shown) fireChanged(shown.getAttribute('data-panel'));
    }, 0);
  }, true);

  // Also announce when in-form CTAs navigate between panels
  document.addEventListener('click', function (e) {
    const b = e.target.closest('#form-panel [data-goto]');
    if (!b) return;
    setTimeout(function () {
      const shown = document.querySelector('.pi-main[data-panel]:not([hidden])');
      if (shown) fireChanged(shown.getAttribute('data-panel'));
    }, 0);
  }, true);

  // Announce initial panel on load (in case you land directly inside the form)
  document.addEventListener('DOMContentLoaded', function () {
    const shown = document.querySelector('.pi-main[data-panel]:not([hidden])');
    if (shown) fireChanged(shown.getAttribute('data-panel'));
  });
})();
</script>


                        
<script>
document.addEventListener('DOMContentLoaded', function () {
  const rentBlock = document.getElementById('rentBenefitBlock');

  function updateRentBlock() {
    if (!rentBlock) return; // Safe fix: exit if element doesn't exist
    const selected = document.querySelector('input[name="onRent"]:checked');
    const show = selected && selected.value === 'yes';
    rentBlock.style.display = show ? 'block' : 'none';

    // If user switches back to "No", reset the follow-up radios
    if (!show) {
      const rbYes = document.getElementById('rb_yes');
      const rbNo  = document.getElementById('rb_no');
      if (rbYes) rbYes.checked = false;
      if (rbNo)  rbNo.checked  = true;
    }
  }

  // Listen for changes on the onRent radios
  document.querySelectorAll('input[name="onRent"]')
    .forEach(r => r.addEventListener('change', updateRentBlock));

  // Initialize visibility (respects default checked state)
  updateRentBlock();
});
</script>


<!-- Mobile Dropdown Script-->      

<script>
document.addEventListener('DOMContentLoaded', function () {
  const toggle = document.getElementById('pi-mobile-toggle');
  const menu   = document.getElementById('pi-mobile-menu');
  const back   = document.getElementById('pi-mobile-back');

  if (toggle && menu){
    toggle.addEventListener('click', ()=>{
      const open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!open));
      menu.classList.toggle('open', !open);
      menu.setAttribute('aria-hidden', open ? 'true' : 'false');
    });
  }

  // optional: back -> go to previous panel/section
  if (back){
    back.addEventListener('click', ()=>{
      // Example: return to welcome screen if you keep that flow
      const welcome = document.getElementById('welcome-card');
      const personal = document.getElementById('personal-info-panel');
      if (welcome && personal){
        personal.style.display = 'none';
        welcome.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
      } else {
        history.back();
      }
    });
  }

  // close menu if clicking outside
  document.addEventListener('click', (e)=>{
    if (!menu || !toggle) return;
    if (!menu.classList.contains('open')) return;
    const within = (menu && menu.contains(e.target)) || (toggle && toggle.contains(e.target)); // Safe fix: additional null checks
    if (!within){
      toggle.setAttribute('aria-expanded', 'false');
      menu.classList.remove('open');
      menu.setAttribute('aria-hidden', 'true');
    }
  });
});
</script>


<!-- DATE PICKER -->      

<script>
(function(){
  // All date inputs that should open the modal
  const inputs = document.querySelectorAll('.dob-input');
  if (!inputs.length) return;

  // Modal pieces (use YOUR existing markup)
  const modal   = document.getElementById('dob-modal');
  const dialog  = modal?.querySelector('.dob-dialog');
  const body    = document.getElementById('dob-body');
  const titleEl = document.getElementById('dob-title');
  const subEl   = document.getElementById('dob-subhead-text');
  const prevBtn = document.getElementById('dob-prev');
  const nextBtn = document.getElementById('dob-next');
  const backBtn = document.getElementById('dob-back');
  const closeBtn= document.getElementById('dob-close');

  if (!modal || !dialog || !body || !titleEl || !subEl || !prevBtn || !nextBtn || !backBtn || !closeBtn) {
    console.warn('[DOB] Missing expected modal elements.');
    return;
  }

  // Config
  const YEAR_MIN = 1900;
  const YEAR_MAX = new Date().getFullYear() + 1; // allow next year paging
  const clamp = (n,min,max) => Math.min(max, Math.max(min, n));

  // Months (for display + parsing)
  const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const pad2 = n => String(n).padStart(2,'0');

  // Quick map for parsing textual months
  const MON_TO_IDX = new Map(MONTHS.map((m,i)=>[m.toLowerCase(), i]));
  function monthIndexFromToken(tok){
    if (!tok) return NaN;
    const t = tok.trim().toLowerCase();
    if (MON_TO_IDX.has(t)) return MON_TO_IDX.get(t);
    // also accept numeric month just in case (1..12)
    const n = parseInt(tok,10);
    if (!Number.isNaN(n) && n>=1 && n<=12) return n-1;
    return NaN;
  }

  // State (reseeded per input open)
  let activeInput = null;
  let view = 'year';      // 'year' | 'month' | 'day'
  let mode = 'ymd';       // 'ymd' or 'ym' (read from input.dataset.dobMode)
  let y, m, d, yearPage;  // current selection & paging

  // Helpers
  const getBoundEl = (inp) => {
    const sel = inp?.dataset?.bind;
    return sel ? document.querySelector(sel) : null;
  };
  const getMode = (inp) => (inp?.dataset?.dobMode || 'ymd').toLowerCase();

  // Parse ISO from hidden: YYYY-MM-DD or YYYY-MM
  function parseISOAny(s){
    if (!s) return null;
    s = s.trim();
    let m3 = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s);
    if (m3) return { y:+m3[1], m:+m3[2]-1, d:+m3[3] };
    let m2 = /^(\d{4})-(\d{2})$/.exec(s);
    if (m2) return { y:+m2[1], m:+m2[2]-1, d:1 };
    return null;
  }

  // Parse visible display
  // ymd: "DD | MMM | YYYY"  (also accepts "DD MMM YYYY" and numeric month fallback)
  // ym : "MMM | YYYY"       (also accepts "MMM YYYY" and numeric month fallback)
  function parseDisplayAny(txt, _mode){
    const parts = (txt||'').replace(/\|/g,' ').replace(/\s+/g,' ').trim().split(' ');
    if (!parts.length) return null;

    if (_mode === 'ym'){
      if (parts.length < 2) return null;
      const mi = monthIndexFromToken(parts[0]);
      const yr = parseInt(parts[1],10);
      if (Number.isNaN(mi) || mi < 0 || mi > 11 || !yr || yr < 1000) return null;
      return { y: yr, m: mi, d: 1 };
    } else {
      if (parts.length < 3) return null;
      const dd  = parseInt(parts[0],10);          // DAY first
      const mi  = monthIndexFromToken(parts[1]);  // textual month like "Aug"
      const yr  = parseInt(parts[2],10);
      if (Number.isNaN(dd) || dd < 1 || dd > 31) return null;
      if (Number.isNaN(mi) || mi < 0 || mi > 11) return null;
      if (!yr || yr < 1000) return null;          // prefer 4-digit year
      return { y: yr, m: mi, d: dd };
    }
  }

  // Format for visible input (DISPLAY)
  const fmtDisplay = () => (mode === 'ym'
    ? `${MONTHS[m]}  |  ${y}`
    : `${pad2(d)}  |  ${MONTHS[m]}  |  ${y}`
  );

  // Format for hidden input (BOUND ISO)
  const fmtBound = () => (mode === 'ym'
    ? `${y}-${pad2(m+1)}`
    : `${y}-${pad2(m+1)}-${pad2(d)}`
  );

  // Seed selection from an input (use its hidden or display; else today)
  function seedFromInput(inp){
    mode = getMode(inp);

    const bound = getBoundEl(inp);
    const seed  = parseISOAny(bound?.value) || parseDisplayAny(inp.value, mode);

    if (seed){
      y = clamp(seed.y, YEAR_MIN, YEAR_MAX);
      m = seed.m;
      d = seed.d || 1;
    } else {
      const t = new Date(); // default to TODAY when empty
      y = clamp(t.getFullYear(), YEAR_MIN, YEAR_MAX);
      m = t.getMonth();
      d = t.getDate();
    }
    yearPage = y;
  }

  // Open/close
  function openModal(startView, inp){
    activeInput = inp;
    seedFromInput(inp);
    view = startView || 'year';
    modal.hidden = false;
    render();
    document.body.style.overflow = 'hidden';
  }

  function closeModal(){
    modal.hidden = true;
    document.body.style.overflow = '';
    activeInput = null;
  }

  // Commit selection to inputs
  function commit(){
    if (!activeInput) return;
    const bound = getBoundEl(activeInput);

    activeInput.value = fmtDisplay();
    activeInput.dispatchEvent(new Event('input',  {bubbles:true}));
    activeInput.dispatchEvent(new Event('change', {bubbles:true}));

    if (bound){
      bound.value = fmtBound();
      bound.dispatchEvent(new Event('input',  {bubbles:true}));
      bound.dispatchEvent(new Event('change', {bubbles:true}));
    }
  }

  // Rendering
  function render(){
    body.innerHTML = '';

    // Back button: hidden on year view
    backBtn.hidden = (view === 'year');

    // Prev/Next only useful on year grid pages
    const showPager = (view === 'year');
    prevBtn.style.visibility = showPager ? 'visible' : 'hidden';
    nextBtn.style.visibility = showPager ? 'visible' : 'hidden';

    if (view === 'year'){
      titleEl.textContent = 'Select a year';
      subEl.textContent = `${y}`;
      renderYears();
    } else if (view === 'month'){
      titleEl.textContent = 'Select a month';
      subEl.textContent = `${y}`;
      renderMonths();
    } else {
      titleEl.textContent = 'Select a day';
      subEl.textContent = `${MONTHS[m]} ${y}`;
      renderDays();
    }
  }

  function renderYears(){
    const wrap = document.createElement('div');
    wrap.className = 'dob-grid years';
    const pageSize = 12;

    // compute page start around current y
    let start = Math.max(YEAR_MIN, Math.min(yearPage - 4, YEAR_MAX - (pageSize - 1)));

    for (let i = 0; i < pageSize; i++){
      const yy = start + i;
      const cell = document.createElement('div');
      cell.className = 'dob-cell' + (yy === y ? ' active' : '');
      cell.textContent = yy;
      if (yy >= YEAR_MIN && yy <= YEAR_MAX) {
        cell.onclick = () => { y = yy; view = 'month'; render(); };
      } else {
        cell.classList.add('mute');
      }
      wrap.appendChild(cell);
    }
    body.appendChild(wrap);

    // pager
    prevBtn.onclick = () => {
      const newStart = start - pageSize;
      if (newStart >= YEAR_MIN) { yearPage = clamp(yearPage - pageSize, YEAR_MIN, YEAR_MAX); render(); }
    };
    nextBtn.onclick = () => {
      const newStart = start + pageSize;
      if (newStart + pageSize - 1 <= YEAR_MAX) { yearPage = clamp(yearPage + pageSize, YEAR_MIN, YEAR_MAX); render(); }
    };
  }

  function renderMonths(){
    const wrap = document.createElement('div');
    wrap.className = 'dob-grid months';
    MONTHS.forEach((mm, idx) => {
      const cell = document.createElement('div');
      cell.className = 'dob-cell' + (idx === m ? ' active' : '');
      cell.textContent = mm;
      cell.onclick = () => {
        m = idx;
        if (mode === 'ym'){ d = 1; commit(); closeModal(); }
        else { view = 'day'; render(); }
      };
      wrap.appendChild(cell);
    });
    body.appendChild(wrap);
  }

  function daysInMonth(yy,mm){ return new Date(yy, mm+1, 0).getDate(); }
  function firstDow(yy,mm){ return new Date(yy, mm, 1).getDay(); }

  function renderDays(){
    // weekday header
    const wk = document.createElement('div');
    wk.className = 'dob-week';
    ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'].forEach(txt=>{
      const el = document.createElement('div'); el.textContent = txt; wk.appendChild(el);
    });
    body.appendChild(wk);

    const wrap = document.createElement('div');
    wrap.className = 'dob-grid days';
    const dim  = daysInMonth(y,m);
    const lead = firstDow(y,m);

    // pad: prev-month
    const prevY = (m===0)? y-1 : y;
    const prevM = (m===0)? 11  : m-1;
    const prevDim = daysInMonth(prevY, prevM);
    for (let i=lead-1;i>=0;i--){
      const cell = document.createElement('div');
      cell.className = 'dob-cell mute';
      cell.textContent = prevDim - i;
      wrap.appendChild(cell);
    }

    // current month
    for (let i=1;i<=dim;i++){
      const cell = document.createElement('div');
      cell.className = 'dob-cell' + (i===d ? ' active':'' );
      cell.textContent = i;
      cell.onclick = () => { d = i; commit(); closeModal(); };
      wrap.appendChild(cell);
    }

    // pad: next-month
    const total = lead + dim;
    for (let i=0;i<(7 - (total % 7)) % 7;i++){
      const cell = document.createElement('div');
      cell.className = 'dob-cell mute';
      cell.textContent = i+1;
      wrap.appendChild(cell);
    }

    body.appendChild(wrap);
  }

  // Open modal on focus/click/ArrowDown
  inputs.forEach(inp=>{
    inp.addEventListener('focus', ()=>openModal('year', inp));
    inp.addEventListener('click',  ()=>openModal('year', inp));
    inp.addEventListener('keydown', (e)=>{
      if (e.key === 'ArrowDown'){ e.preventDefault(); openModal('year', inp); }
    });
  });

  // Back/close & outside click
  backBtn.addEventListener('click', () => {
    if (view === 'day')      { view = 'month'; render(); }
    else if (view === 'month'){ view = 'year';  render(); }
    else { closeModal(); }
  });
  closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e)=>{ if (e.target === modal) closeModal(); });
  window.addEventListener('keydown', (e)=>{ if (!modal.hidden && e.key === 'Escape') closeModal(); });

})();
</script>


<script>
(function attachCalendarIcons(){
  const ICON = `
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
      <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
      <line x1="16" y1="2" x2="16" y2="6"></line>
      <line x1="8"  y1="2" x2="8"  y2="6"></line>
      <line x1="3"  y1="10" x2="21" y2="10"></line>
      <!-- small date square just for detail -->
      <rect x="7" y="14" width="3" height="3" fill="currentColor" stroke="none"></rect>
    </svg>
  `;

  document.querySelectorAll('.dob-input').forEach(inp => {
    const group = inp.closest('.fi-group') || inp.parentElement;
    if (!group || group.querySelector('.dob-calendar-btn')) return;

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dob-calendar-btn';
    btn.setAttribute('aria-label','Open calendar');
    btn.innerHTML = ICON;

    inp.classList.add('calendarized');
    group.appendChild(btn);

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (typeof openModal === 'function') {
        openModal('year', inp);        // your existing modal picker
      } else {
        inp.focus();                   // fallback: focus field
      }
    });
  });
})();
</script>

<script>
(function(){
  const SVG = "<svg viewBox='0 0 24 24' xmlns='http://www.w3.org/2000/svg' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'><rect x='3' y='4' width='18' height='18' rx='2' ry='2'/><line x1='16' y1='2' x2='16' y2='6'/><line x1='8' y1='2' x2='8' y2='6'/><line x1='3' y1='10' x2='21' y2='10'/></svg>";

  document.querySelectorAll('#child-modal .dob-input').forEach(inp=>{
    if (inp.dataset.calReady) return;
    const wrap = inp.closest('.fi-group');
    if (!wrap) return;

    inp.classList.add('calendarized');

    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dob-calendar-btn';
    btn.innerHTML = SVG;
    btn.addEventListener('click', (e)=>{
      e.preventDefault(); e.stopPropagation();
      // your date modal opens on input click/focus; trigger it
      inp.focus();
      inp.dispatchEvent(new Event('click', {bubbles:true}));
    });

    wrap.appendChild(btn);
    inp.dataset.calReady = '1';
  });
})();
</script>

<!-- YEAR LOGIC IN TAX FILING -->      

<script>
(function onReady(fn){
  if (document.readyState !== 'loading') fn();
  else document.addEventListener('DOMContentLoaded', fn);
})(function(){

  // ----- Elements -----
  const entryISO  = document.getElementById('entry_date');          // hidden YYYY-MM or YYYY-MM-DD
  const entryDisp = document.getElementById('entry_date_display');  // "MM | DD | YYYY" or "MM | YYYY"

  const p1 = document.getElementById('period_y1');
  const p2 = document.getElementById('period_y2');
  const p3 = document.getElementById('period_y3');

  const y1 = document.getElementById('inc_y1');
  const y2 = document.getElementById('inc_y2');
  const y3 = document.getElementById('inc_y3');

  const wiWrap = document.getElementById('wi-wrapper'); // optional wrapper to hide/show
  if (!p1 || !p2 || !p3 || !y1 || !y2 || !y3) return;

  // Make programmatic .value changes emit events (unchanged)
  const valueDesc = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
  function patchValueEmitter(el){
    if (!el || el.__patchedValueEmitter) return;
    const {get, set} = valueDesc;
    Object.defineProperty(el, 'value', {
      get(){ return get.call(this); },
      set(v){
        const old = get.call(this);
        set.call(this, v);
        if (v !== old) {
          this.dispatchEvent(new Event('input',  {bubbles:true}));
          this.dispatchEvent(new Event('change', {bubbles:true}));
        }
      }
    });
    el.__patchedValueEmitter = true;
  }
  patchValueEmitter(entryISO);
  patchValueEmitter(entryDisp);

  // ----- Helpers -----
  const MONTHS_S = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const MON_IDX  = ["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"];
  const pad2 = n => String(n).padStart(2,'0');
  const fmtMDY = (y,m,d) => `${MONTHS_S[m]} ${pad2(d)}, ${y}`;

  function daysInMonth(yy,mm){ return new Date(yy, mm+1, 0).getDate(); }

  // ISO: supports YYYY-MM and YYYY-MM-DD
  function parseISOAny(s){
    if (!s) return null;
    s = s.trim();
    let m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s);
    if (m) return new Date(+m[1], +m[2]-1, +m[3]);
    m = /^(\d{4})-(\d{2})$/.exec(s);
    if (m) return new Date(+m[1], +m[2]-1, 1);
    return null;
  }

  // Display: "MM DD YYYY", "MM YYYY" (also handles "MMM ...")
  function parseDisplayToDate(txt){
    if (!txt) return null;
    const clean = txt.replace(/\|/g,' ').replace(/\s+/g,' ').trim();

    // MM DD YYYY
    let m = /^(\d{1,2})\s+(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m) return new Date(+m[3], Math.max(0, Math.min(11, +m[1]-1)), +m[2]);

    // MMM DD YYYY
    m = /^([A-Za-z]{3,})\s+(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m){
      const idx = MON_IDX.indexOf(m[1].slice(0,3).toLowerCase());
      if (idx >= 0) return new Date(+m[3], idx, +m[2]);
    }

    // MM YYYY
    m = /^(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m) return new Date(+m[2], Math.max(0, Math.min(11, +m[1]-1)), 1);

    // MMM YYYY
    m = /^([A-Za-z]{3,})\s+(\d{4})$/.exec(clean);
    if (m){
      const idx = MON_IDX.indexOf(m[1].slice(0,3).toLowerCase());
      if (idx >= 0) return new Date(+m[2], idx, 1);
    }
    return null;
  }

  function getEntryDate(){
    // hidden first
    let d = parseISOAny(entryISO?.value);
    if (d) return d;

    // then display
    d = parseDisplayToDate(entryDisp?.value || '');
    if (d && entryISO){
      // Persist to hidden: YYYY-MM if no day, else YYYY-MM-DD
      const parts = (entryDisp.value||'').replace(/\|/g,' ').trim().split(/\s+/);
      const hasDay = parts.length >= 3;
      entryISO.value = hasDay
        ? `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`
        : `${d.getFullYear()}-${pad2(d.getMonth()+1)}`;
    }
    return d;
  }

  // hints under inputs (unchanged)
  function ensureHint(inputEl){
    const group = inputEl.closest('.fi-group') || inputEl.parentElement;
    let hint = group.querySelector('.wi-hint');
    if (!hint){
      hint = document.createElement('div');
      hint.className = 'wi-hint';
      group.appendChild(hint);
    }
    return hint;
  }
  const h1 = ensureHint(y1), h2 = ensureHint(y2), h3 = ensureHint(y3);
  [y1,y2,y3].forEach(inp => { if (inp) inp.placeholder = ' '; });
  function toggleHintVisibility(inputEl){
    const group = inputEl.closest('.fi-group') || inputEl.parentElement;
    group.classList.toggle('wi-show-hint', (inputEl.value.trim() === ''));
  }
  function showWrap(on){
    if (!wiWrap) return;
    wiWrap.hidden = !on;
    wiWrap.classList.toggle('is-hidden', !on);
    wiWrap.setAttribute('aria-hidden', String(!on));
  }

  // ----- MAIN: include DAY in period text -----
  function updatePeriods(){
    const ed = getEntryDate();
    if (!ed){
      p1.textContent = p2.textContent = p3.textContent = '—';
      h1.textContent = h2.textContent = h3.textContent = '';
      [y1,y2,y3].forEach(toggleHintVisibility);
      showWrap(false);
      return;
    }
    showWrap(true);

    const Y = ed.getFullYear();
    const M = ed.getMonth();       // 0..11
    const D = ed.getDate() || 1;   // use entry day if present, else 1

    // Year 1: Jan 01 (Y) – Entry Month/Day (Y)
    const y1Start = { y: Y,   m: 0,  d: 1   };
    const y1End   = { y: Y,   m: M,  d: D   };

    // Year 2: Jan 01 (Y-1) – Dec 31 (Y-1)
    const y2Start = { y: Y-1, m: 0,  d: 1   };
    const y2End   = { y: Y-1, m: 11, d: daysInMonth(Y-1, 11) };

    // Year 3: Jan 01 (Y-2) – Dec 31 (Y-2)
    const y3Start = { y: Y-2, m: 0,  d: 1   };
    const y3End   = { y: Y-2, m: 11, d: daysInMonth(Y-2, 11) };

    const t1 = `${fmtMDY(y1Start.y, y1Start.m, y1Start.d)} – ${fmtMDY(y1End.y, y1End.m, y1End.d)}`;
    const t2 = `${fmtMDY(y2Start.y, y2Start.m, y2Start.d)} – ${fmtMDY(y2End.y, y2End.m, y2End.d)}`;
    const t3 = `${fmtMDY(y3Start.y, y3Start.m, y3Start.d)} – ${fmtMDY(y3End.y, y3End.m, y3End.d)}`;

    p1.textContent = t1;  p2.textContent = t2;  p3.textContent = t3;
    h1.textContent = t1;  h2.textContent = t2;  h3.textContent = t3;

    [y1,y2,y3].forEach(toggleHintVisibility);
  }

  // Listeners (unchanged)
  ['input','change'].forEach(ev => {
    entryISO  && entryISO .addEventListener(ev, updatePeriods);
    entryDisp && entryDisp.addEventListener(ev, updatePeriods);
  });
  [y1,y2,y3].forEach(inp => {
    ['input','change','blur'].forEach(ev => inp.addEventListener(ev, ()=>toggleHintVisibility(inp)));
    toggleHintVisibility(inp);
  });

  // Initial + quick retries for late programmatic sets
  updatePeriods();
  let tries = 0, maxTries = 8;
  const t = setInterval(() => {
    tries++; updatePeriods();
    if (getEntryDate() || tries >= maxTries) clearInterval(t);
  }, 150);
});
</script>



<!-- YEAR LOGIC IN TAX-FILING SPOUSE-->                              
<script>
(function(){
  const entryISO  = document.getElementById('sp_entry_date');           // hidden YYYY-MM[-DD]
  const entryDisp = document.getElementById('sp_entry_date_display');   // visible MM | DD | YYYY (dob-input)

  const p1 = document.getElementById('sp_period_y1');
  const p2 = document.getElementById('sp_period_y2');
  const p3 = document.getElementById('sp_period_y3');

  const y1 = document.getElementById('sp_inc_y1');
  const y2 = document.getElementById('sp_inc_y2');
  const y3 = document.getElementById('sp_inc_y3');

  const wrap = document.getElementById('sp-wi-wrapper');
  if (!p1 || !p2 || !p3) return;

  // fire input/change when value is set programmatically
  const valueDesc = Object.getOwnPropertyDescriptor(HTMLInputElement.prototype, 'value');
  function patchValueEmitter(el){
    if (!el || el.__patchedValueEmitter) return;
    const {get, set} = valueDesc;
    Object.defineProperty(el, 'value', {
      get(){ return get.call(this); },
      set(v){
        const old = get.call(this);
        set.call(this, v);
        if (v !== old) {
          this.dispatchEvent(new Event('input',  {bubbles:true}));
          this.dispatchEvent(new Event('change', {bubbles:true}));
        }
      }
    });
    el.__patchedValueEmitter = true;
  }
  patchValueEmitter(entryISO);
  patchValueEmitter(entryDisp);

  const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const MON_IDX = ["jan","feb","mar","apr","may","jun","jul","aug","sep","oct","nov","dec"];
  const pad2 = n => String(n).padStart(2,'0');
  const fmtMDY = (y,m,d) => `${MONTHS[m]} ${pad2(d)}, ${y}`;
  const daysInMonth = (yy,mm) => new Date(yy, mm+1, 0).getDate();

  // ISO: supports YYYY-MM-DD and YYYY-MM
  function parseISOAny(s){
    if (!s) return null;
    s = s.trim();
    let m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s);
    if (m) return new Date(+m[1], +m[2]-1, +m[3]);
    m = /^(\d{4})-(\d{2})$/.exec(s);
    if (m) return new Date(+m[2], +m[2]-1, 1); // NOTE: fixed below
    return null;
  }
  // fix parseISOAny month/year branch (typo corrected):
  function parseISOAny(s){
    if (!s) return null;
    s = s.trim();
    let m = /^(\d{4})-(\d{2})-(\d{2})$/.exec(s);
    if (m) return new Date(+m[1], +m[2]-1, +m[3]);
    m = /^(\d{4})-(\d{2})$/.exec(s);
    if (m) return new Date(+m[1], +m[2]-1, 1);
    return null;
  }

  // Display: "MM DD YYYY", "MM YYYY", "MMM DD YYYY", "MMM YYYY"
  function parseDisplayToDate(txt){
    if (!txt) return null;
    const clean = txt.replace(/\|/g,' ').replace(/\s+/g,' ').trim();

    // MM DD YYYY
    let m = /^(\d{1,2})\s+(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m) return new Date(+m[3], Math.max(0, Math.min(11, +m[1]-1)), +m[2]);

    // MMM DD YYYY
    m = /^([A-Za-z]{3,})\s+(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m){
      const idx = MON_IDX.indexOf(m[1].slice(0,3).toLowerCase());
      if (idx >= 0) return new Date(+m[3], idx, +m[2]);
    }

    // MM YYYY
    m = /^(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m) return new Date(+m[2], Math.max(0, Math.min(11, +m[1]-1)), 1);

    // MMM YYYY
    m = /^([A-Za-z]{3,})\s+(\d{4})$/.exec(clean);
    if (m){
      const idx = MON_IDX.indexOf(m[1].slice(0,3).toLowerCase());
      if (idx >= 0) return new Date(+m[2], idx, 1);
    }
    return null;
  }

  function getEntryDate(){
    // prefer hidden ISO
    let d = parseISOAny(entryISO?.value);
    if (d) return d;

    // fall back to visible field
    d = parseDisplayToDate(entryDisp?.value || '');
    if (d && entryISO){
      const parts = (entryDisp.value||'').replace(/\|/g,' ').trim().split(/\s+/);
      const hasDay = parts.length >= 3;
      entryISO.value = hasDay
        ? `${d.getFullYear()}-${pad2(d.getMonth()+1)}-${pad2(d.getDate())}`
        : `${d.getFullYear()}-${pad2(d.getMonth()+1)}`;
      // bubble events so dependents update
      entryISO.dispatchEvent(new Event('input',  {bubbles:true}));
      entryISO.dispatchEvent(new Event('change', {bubbles:true}));
    }
    return d;
  }

  function showWrap(on){
    if (!wrap) return;
    wrap.hidden = !on;
    wrap.classList.toggle('is-hidden', !on);
    wrap.setAttribute('aria-hidden', String(!on));
  }

  function ensureHint(inputEl){
    if (!inputEl) return null;
    const grp = inputEl.closest('.fi-group') || inputEl.parentElement;
    let h = grp.querySelector('.wi-hint');
    if (!h){ h = document.createElement('div'); h.className = 'wi-hint'; grp.appendChild(h); }
    return h;
  }

  const h1 = ensureHint(y1), h2 = ensureHint(y2), h3 = ensureHint(y3);
  [y1,y2,y3].forEach(inp => { if (inp) inp.placeholder = ' '; });

  function setHint(inp, txt){
    if (!inp) return;
    const grp = inp.closest('.fi-group') || inp.parentElement;
    let h = grp.querySelector('.wi-hint');
    if (!h){ h = document.createElement('div'); h.className = 'wi-hint'; grp.appendChild(h); }
    h.textContent = txt;
    grp.classList.toggle('wi-show-hint', (inp.value.trim() === ''));
  }

  function apply(){
    const ed = getEntryDate();
    if (!ed){
      p1.textContent = p2.textContent = p3.textContent = '—';
      [y1,y2,y3].forEach(inp => inp && setHint(inp, ''));
      showWrap(false);
      return;
    }
    showWrap(true);

    const Y = ed.getFullYear();
    const M = ed.getMonth();             // 0..11
    const D = ed.getDate() || 1;         // use day if present else 1

    // Year 1: Jan 01 (Y) – Entry Month/Day (Y)
    const y1Start = { y:Y,   m:0,  d:1 };
    const y1End   = { y:Y,   m:M,  d:D };

    // Year 2: Jan 01 (Y-1) – Dec 31 (Y-1)
    const y2Start = { y:Y-1, m:0,  d:1 };
    const y2End   = { y:Y-1, m:11, d:daysInMonth(Y-1, 11) };

    // Year 3: Jan 01 (Y-2) – Dec 31 (Y-2)
    const y3Start = { y:Y-2, m:0,  d:1 };
    const y3End   = { y:Y-2, m:11, d:daysInMonth(Y-2, 11) };

    const t1 = `${fmtMDY(y1Start.y, y1Start.m, y1Start.d)} – ${fmtMDY(y1End.y, y1End.m, y1End.d)}`;
    const t2 = `${fmtMDY(y2Start.y, y2Start.m, y2Start.d)} – ${fmtMDY(y2End.y, y2End.m, y2End.d)}`;
    const t3 = `${fmtMDY(y3Start.y, y3Start.m, y3Start.d)} – ${fmtMDY(y3End.y, y3End.m, y3End.d)}`;

    if (p1.textContent !== t1) p1.textContent = t1;
    if (p2.textContent !== t2) p2.textContent = t2;
    if (p3.textContent !== t3) p3.textContent = t3;

    setHint(y1, t1); setHint(y2, t2); setHint(y3, t3);
  }

  ['input','change'].forEach(ev=>{
    entryISO?.addEventListener(ev, apply);
    entryDisp?.addEventListener(ev, apply);
  });

  // Initial + a few retries to beat late scripts
  apply();
  let ticks = 0;
  const timer = setInterval(()=>{
    apply();
    if (++ticks > 12 && p1.textContent.includes('–')) clearInterval(timer);
  }, 250);
})();
</script>



                        
 <script>
document.addEventListener('DOMContentLoaded', function(){
  const ftYes = document.getElementById('first_yes');
  const ftNo  = document.getElementById('first_no');
  const prior = document.getElementById('prior-customer-section'); // BRANCH A
  const first = document.getElementById('firsttime-details');      // BRANCH B

  function flip(section, on){
    section.classList.toggle('is-hidden', !on);
    section.setAttribute('aria-hidden', String(!on));
    section.querySelectorAll('input, select, textarea').forEach(el=>{
      if(!on){
        if(el.required) el.dataset.wasRequired = '1';
        el.required = false;
        el.disabled = true;
        // Clear validation errors when section is hidden
        if (el.name && typeof hideFieldError === 'function') {
          hideFieldError(el.name);
        }
      }else{
        el.disabled = false;
        if(el.dataset.wasRequired === '1') el.required = true;
      }
    });
  }

  function sync(){
    const firstTime = ftYes.checked;   // YES => show first-time details; hide prior-customer
    flip(first,  firstTime);
    flip(prior, !firstTime);
  }

  ftYes.addEventListener('change', sync);
  ftNo.addEventListener('change', sync);

  // initialize (respects saved PHP value)
  sync();
});
</script>

                        
<script>
document.addEventListener('DOMContentLoaded', function(){
  // First-time home buyer toggle
  const fYes = document.getElementById('fthb_yes');
  const fNo  = document.getElementById('fthb_no');
  const fBox = document.getElementById('fthb-details');

  function flipSection(box, on){
    box.classList.toggle('is-hidden', !on);
    box.setAttribute('aria-hidden', String(!on));
    box.querySelectorAll('input, select, textarea').forEach(el=>{
      if(!on){
        if(el.required) el.dataset.wasRequired='1';
        el.required=false;
        el.disabled=true;
        // Clear validation errors when section is hidden
        if (el.name && typeof hideFieldError === 'function') {
          hideFieldError(el.name);
        }
      }else{
        el.disabled=false;
        if(el.dataset.wasRequired==='1') el.required=true;
      }
    });
  }

  function syncFTHB(){
    flipSection(fBox, fYes.checked);
  }

  fYes.addEventListener('change', syncFTHB);
  fNo .addEventListener('change',  syncFTHB);

  // Claim Details: show percentage only when claim_full = yes (per your text)
  const cYes = document.getElementById('claim_full_yes');
  const cNo  = document.getElementById('claim_full_no');
  const cWrap= document.getElementById('claim-percent-wrap');
  const cInp = document.getElementById('claim_percent');

  function flipPercent(on){
    if (!cWrap) return; // Safe fix: exit if element doesn't exist
    cWrap.classList.toggle('is-hidden', !on);
    cWrap.setAttribute('aria-hidden', String(!on));
    if(!on){
      if(cInp.required) cInp.dataset.wasRequired='1';
      cInp.required=false;
      cInp.disabled=true;
      // Clear validation error when field is hidden
      if (cInp.name && typeof hideFieldError === 'function') {
        hideFieldError(cInp.name);
      }
    }else{
      cInp.disabled=false;
      if(cInp.dataset.wasRequired==='1' || !cInp.dataset.wasRequired){
        cInp.required=true; // make percentage required when showing
      }
    }
  }

  function syncClaim(){
    flipPercent(cYes.checked);
  }

  cYes.addEventListener('change', syncClaim);
  cNo .addEventListener('change',  syncClaim);

  // Initialize states on load (respects saved values)
  syncFTHB();
  syncClaim();
});
</script>
                        
<script>
  const pct = document.getElementById('claim_percent');
  if (pct) {
    pct.addEventListener('input', () => {
      const v = parseInt(pct.value || '', 10);
      if (Number.isNaN(v)) return;
      if (v > 100) pct.value = 100;
      if (v < 1)   pct.value = '';
    });
  }
</script>

 <script>
document.addEventListener('DOMContentLoaded', function(){
  // Sole owner radios (reuse existing ids)
  const cYes = document.getElementById('claim_full_yes');
  const cNo  = document.getElementById('claim_full_no');

  // New owners field
  const wrap = document.getElementById('owners-wrap');
  const inp  = document.getElementById('owner_count');

  // If any old percent UI exists, hard-hide/disable it so other scripts can't revive it
  const oldPctWrap = document.getElementById('claim-percent-wrap');
  const oldPctInp  = document.getElementById('claim_percent');
  if (oldPctWrap) { oldPctWrap.classList.add('is-hidden'); oldPctWrap.setAttribute('aria-hidden','true'); }
  if (oldPctInp)  { oldPctInp.disabled = true; oldPctInp.required = false; }

  function flipOwners(show){
    if (!wrap || !inp) return;
    wrap.classList.toggle('is-hidden', !show);
    wrap.setAttribute('aria-hidden', String(!show));
    if (show) {
      inp.disabled = false;
      inp.required = true;
      // Keep value if returning; otherwise leave blank
    } else {
      if (inp.required) inp.dataset.wasRequired = '1';
      inp.required = false;
      inp.disabled = true;
      // Clear validation error when field is hidden
      if (inp.name && typeof hideFieldError === 'function') {
        hideFieldError(inp.name);
      }
    }
  }

  // Clamp owners to >= 2 (optional upper bound to 20)
  inp?.addEventListener('input', () => {
    if (!inp.value.trim()) return;
    let v = parseInt(inp.value, 10);
    if (!Number.isFinite(v)) { inp.value = ''; return; }
    if (v < 2) v = 2;
    if (v > 20) v = 20;
    if (String(v) !== inp.value) inp.value = String(v);
  });

  function sync(){
    if (cNo?.checked) flipOwners(true);
    else              flipOwners(false); // Yes or unset -> hide
  }

  cYes?.addEventListener('change', sync);
  cNo ?.addEventListener('change',  sync);

  // Initial paint
  sync();
});
</script>
                       
<script>
  // Toggle spouse address fields on "address same?" radios
  document.addEventListener('DOMContentLoaded', function () {
    const yes = document.getElementById('spouse_addr_same_yes');
    const no  = document.getElementById('spouse_addr_same_no');
    const box = document.getElementById('spouse-address-fields');

    function update() { box.style.display = no.checked ? '' : 'none'; }
    if (yes && no && box) {
      yes.addEventListener('change', update);
      no.addEventListener('change', update);
      update();
    }
  });
</script>

                        
                        
 <!-- YES 2 -->      

                       
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Radios (hidden by CSS)
  const msRadios = document.querySelectorAll('input[name="marital_status"]');

  // Blocks we toggle
  const spouseBlock    = document.getElementById('spouse-file-block');
  const childrenBlock  = document.getElementById('children-block');

  // Married/Common Law date block + label
  const mclDateBlk   = document.getElementById('status-date-block');
  const mclDateLbl   = document.getElementById('status-date-label');

  // Separated/Divorced/Widowed date block + label
  const sdwDateBlk   = document.getElementById('status-date-sdw-block');
  const sdwDateLbl   = document.getElementById('status-date-sdw-label');

  // Canada question
  const canadaYes    = document.getElementById('spouse_in_canada_yes');
  const canadaNo     = document.getElementById('spouse_in_canada_no');
  const canadaGroup  = canadaYes?.closest('.yn-group') || canadaNo?.closest('.yn-group');
  const canadaH2     = canadaGroup?.previousElementSibling; // the <h2> above radios

  // Hidden field sync for spouseFile (kept)
  const spouseYes = document.getElementById('spouse_yes');
  const spouseNo  = document.getElementById('spouse_no');
  const spouseValHidden = document.getElementById('spouseFile_value');

  // Ensure spouse-file block appears directly under the Canada buttons (once)
  if (canadaGroup && spouseBlock && !spouseBlock.dataset.placed) {
    canadaGroup.insertAdjacentElement('afterend', spouseBlock);
    spouseBlock.dataset.placed = '1';
  }

  // Helpers
  const show = (el, on) => { if (el) el.style.display = on ? '' : 'none'; };
  const msVal = () => document.querySelector('input[name="marital_status"]:checked')?.value || '';
  const isMCL = () => (msVal() === 'Married' || msVal() === 'Common Law');

  function setMclLabel() {
    if (!mclDateLbl) return;
    mclDateLbl.textContent = (msVal() === 'Common Law') ? 'Date of Status Start' : 'Date of Marriage';
  }
  function setSdwLabel() {
    if (!sdwDateLbl) return;
    const v = msVal();
    sdwDateLbl.textContent =
      v === 'Separated' ? 'Date of Separation' :
      v === 'Divorced'  ? 'Date of Divorce'    :
      v === 'Widowed'   ? 'Date of Passing'    : 'Date';
  }

  function publishSpouseFile() {
    if (!spouseValHidden) return;
    spouseValHidden.value = spouseYes?.checked ? 'yes' : 'no';
    document.dispatchEvent(new CustomEvent('spousefile:changed', { detail: spouseValHidden.value }));
  }

  function updateSpouseBlock() {
    // Show spouse-file ONLY when Married/Common Law AND Residing = YES
    show(spouseBlock, isMCL() && !!canadaYes?.checked);
  }

  function updateMarital() {
    const v = msVal();

  if (isMCL()) {
  // DO NOT auto-pick Yes/No for "Residing in Canada?"
  setMclLabel();
  show(mclDateBlk,   true);  // Fixed: use mclDateBlk instead of mclDateBlock
  show(childrenBlock,  true);
  show(canadaGroup,    true);
  if (canadaH2 && /^H\d$/i.test(canadaH2.tagName)) show(canadaH2, true);

  show(sdwDateBlk, false);  // Fixed: use sdwDateBlk instead of sdwDateBlock

  // keep spouse block visibility, but only based on current choice
  updateSpouseBlock();
}
    else if (v === 'Separated' || v === 'Divorced' || v === 'Widowed') {
      setSdwLabel();
      show(sdwDateBlk,   true);

      show(mclDateBlk,   false);
      show(childrenBlock, true);
      show(canadaGroup,  false);
      if (canadaH2 && /^H\d$/i.test(canadaH2.tagName)) show(canadaH2, false);
      show(spouseBlock,  false);
    }
    else {
      show(mclDateBlk,   false);
      show(sdwDateBlk,   false);
      show(childrenBlock,false);
      show(canadaGroup,  false);
      if (canadaH2 && /^H\d$/i.test(canadaH2.tagName)) show(canadaH2, false);
      show(spouseBlock,  false);
    }
  }

  // Listeners
  msRadios.forEach(r => r.addEventListener('change', updateMarital));
  canadaYes?.addEventListener('change', () => { updateSpouseBlock(); publishSpouseFile(); });
  canadaNo ?.addEventListener('change', () => { updateSpouseBlock(); publishSpouseFile(); });
  [spouseYes, spouseNo].forEach(r => r?.addEventListener('change', publishSpouseFile));

  // Initial paint
  updateMarital();
  publishSpouseFile();
});
</script>

         
                        
                        
<script>
document.addEventListener('DOMContentLoaded', function () {
  const incomeRow = document.getElementById('spouse-income');
  const hiddenVal = document.getElementById('spouseFile_value'); // set on page 2

  function apply(v) {
    const val = (typeof v === 'string') ? v : (hiddenVal?.value || 'no');
    if (incomeRow) incomeRow.style.display = (val === 'no') ? '' : 'none';
  }

  // initial state
  apply();

  // listen to changes from page 2
  document.addEventListener('spousefile:changed', (e) => apply(e.detail));
});
</script>

                        
                     
                        
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Helpers
  function setRequiredIn(container, on) {
    if (!container) return;
    container.querySelectorAll('input, select, textarea').forEach(el => {
      if (on) el.setAttribute('required','required'); else el.removeAttribute('required');
      // Clear validation errors when field becomes non-required
      if (!on && el.name && typeof hideFieldError === 'function') {
        hideFieldError(el.name);
      }
    });
  }
  function show(el, on) {
    if (!el) return;
    el.classList.toggle('is-hidden', !on);
    el.setAttribute('aria-hidden', on ? 'false' : 'true');
    // Clear validation errors for all fields in this element when hidden
    if (!on && typeof hideFieldError === 'function') {
      el.querySelectorAll('input, select, textarea').forEach(field => {
        if (field.name) {
          hideFieldError(field.name);
        }
      });
    }
  }

  // ----- Move province toggle (spouse)
  const spYes = document.getElementById('sp_mprov_yes');
  const spNo  = document.getElementById('sp_mprov_no');
  const spSec = document.getElementById('sp-moved-section');

  function syncMove() {
    const moved = spYes && spYes.checked;
    show(spSec, moved);
    setRequiredIn(spSec, moved);
  }
  [spYes, spNo].forEach(el => el && el.addEventListener('change', syncMove));
  syncMove();

  // Preselect selects from data-value
  ['prov_from','prov_to','sp_prov_from','sp_prov_to'].forEach(id => {
    const sel = document.getElementById(id);
    if (sel && sel.dataset.value) sel.value = sel.dataset.value;
  });

  // ----- First-time controller (spouse)
  const spFirstYes = document.getElementById('sp_first_yes');
  const spFirstNo  = document.getElementById('sp_first_no');
  const spPrior    = document.getElementById('sp-prior-customer-section');
  const spFirstDet = document.getElementById('sp-firsttime-details');

  function syncFirstTime() {
    const first = spFirstYes && spFirstYes.checked; // true if first-time YES
    show(spFirstDet, first);
    show(spPrior, !first);
    setRequiredIn(spFirstDet, first);
    setRequiredIn(spPrior, !first);
  }
  [spFirstYes, spFirstNo].forEach(el => el && el.addEventListener('change', syncFirstTime));
  syncFirstTime();

  // ----- Entry date -> update world income period labels (spouse)
  const spEntry = document.getElementById('sp_entry_date_display');
  const y1 = document.getElementById('sp_period_y1');
  const y2 = document.getElementById('sp_period_y2');
  const y3 = document.getElementById('sp_period_y3');

  function updatePeriodsFromEntry() {
    if (!spEntry) return;
    const v = spEntry.value.trim();
    // Try to parse last 4 digits as year
    const m = v.match(/(\d{4})$/);
    if (!m) { [y1,y2,y3].forEach(el => el && (el.textContent = '—')); return; }
    const yr = parseInt(m[1], 10);
    if (Number.isNaN(yr)) return;
    if (y1) y1.textContent = `${yr-1}`;
    if (y2) y2.textContent = `${yr-2}`;
    if (y3) y3.textContent = `${yr-3}`;
  }
  spEntry && spEntry.addEventListener('change', updatePeriodsFromEntry);
  updatePeriodsFromEntry();
});
</script>


  <!-- CHILD SECTION SCRIPT — drop-in replacement -->
<script>
(function onReady(fn){ document.readyState!=='loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); })(function(){

  // ---------------- Data store (seed) ----------------
  let CHILDREN = [];
  try {
    const seedTag = document.getElementById('children-seed');
    if (seedTag) CHILDREN = JSON.parse(seedTag.textContent || '[]') || [];
  } catch(e){ CHILDREN = []; }

  // Pre-select "Do you have children?" radio based on database data
  if (CHILDREN && CHILDREN.length > 0) {
    const childrenYes = document.getElementById('children_yes');
    if (childrenYes) {
      childrenYes.checked = true;
      // Trigger change event to show children section
      childrenYes.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  // ---------------- Element refs ----------------
  const tbody      = document.getElementById('children-tbody');
  const emptyTr    = document.getElementById('children-empty-row');
  const hiddenWrap = document.getElementById('children-hidden-inputs');

  const addBtn = document.getElementById('btn-add-child');
  const wrapTop = document.getElementById('add-child-wrap-top');
  const wrapBot = document.getElementById('add-child-wrap-bottom');

  // Modal
  const modal     = document.getElementById('child-modal');
  const titleEl   = document.getElementById('child-modal-title');
  const form      = document.getElementById('child-form');
  const idEl      = document.getElementById('child_id');
  const fNameEl   = document.getElementById('child_first_name');
  const lNameEl   = document.getElementById('child_last_name');
  const dobDispEl = document.getElementById('child_dob_display'); // visible
  const dobIsoEl  = document.getElementById('child_dob');         // hidden ISO
  const inYesEl   = document.getElementById('child_in_canada_yes');
  const inNoEl    = document.getElementById('child_in_canada_no');
  const btnSave   = document.getElementById('child-save');
  const btnCancel = document.getElementById('child-cancel');

  // Confirm delete
  const confirmModal  = document.getElementById('confirm-modal');
  const confirmText   = document.getElementById('confirm-text');
  const confirmYes    = document.getElementById('confirm-yes');
  const confirmCancel = document.getElementById('confirm-cancel');

  // Add click
  addBtn?.addEventListener('click', () => openChildModal());

  // ---------------- Helpers ----------------
  const open  = el => { if (el) el.style.display = 'block'; };
  const close = el => { if (el) el.style.display = 'none';  };
  const uid   = () => 'c_' + Date.now().toString(36) + Math.random().toString(36).slice(2,8);

  const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const MON    = {jan:0,feb:1,mar:2,apr:3,may:4,jun:5,jul:6,aug:7,sep:8,oct:9,nov:10,dec:11};
  const pad2   = n => String(n).padStart(2,'0');

  // ISO -> "MMM | DD | YYYY"
  function isoToDisplay(iso){
    if (!iso) return '';
    const m3 = /^(\d{4})-(\d{2})-(\d{2})$/.exec(iso.trim());
    if (!m3) return '';
    const y  = +m3[1], mi = +m3[2]-1, d = +m3[3];
    if (mi<0 || mi>11) return '';
    return `${MONTHS[mi]} | ${pad2(d)} | ${y}`;
  }

  // Accept multiple display formats and return {display:'MMM | DD | YYYY', iso:'YYYY-MM-DD'}
  function normalizeDisplayAndISO(v){
    if (!v) return { display:'', iso:'' };
    const clean = v.replace(/\|/g,' ').replace(/\s+/g,' ').trim();

    // 1) MMM DD YYYY
    let m = /^([A-Za-z]{3,})\s+(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m){
      const mi = MON[m[1].slice(0,3).toLowerCase()];
      if (mi!=null){
        const d = Math.max(1, Math.min(31, +m[2]));
        const y = +m[3];
        const iso = `${y}-${pad2(mi+1)}-${pad2(d)}`;
        return { display: `${MONTHS[mi]} | ${pad2(d)} | ${y}`, iso };
      }
    }

    // 2) DD MMM YYYY
    m = /^(\d{1,2})\s+([A-Za-z]{3,})\s+(\d{4})$/.exec(clean);
    if (m){
      const d  = Math.max(1, Math.min(31, +m[1]));
      const mi = MON[m[2].slice(0,3).toLowerCase()];
      const y  = +m[3];
      if (mi!=null){
        const iso = `${y}-${pad2(mi+1)}-${pad2(d)}`;
        return { display: `${MONTHS[mi]} | ${pad2(d)} | ${y}`, iso };
      }
    }

    // 3) MM DD YYYY (numeric month)
    m = /^(\d{1,2})\s+(\d{1,2})\s+(\d{4})$/.exec(clean);
    if (m){
      const mi = Math.max(1, Math.min(12, +m[1])) - 1;
      const d  = Math.max(1, Math.min(31, +m[2]));
      const y  = +m[3];
      const iso = `${y}-${pad2(mi+1)}-${pad2(d)}`;
      return { display: `${MONTHS[mi]} | ${pad2(d)} | ${y}`, iso };
    }

    return { display:'', iso:'' };
  }

  // wire visible DOB <-> hidden ISO, snapping display to MMM | DD | YYYY
  function bindDob(displayEl, isoEl){
    if (!displayEl) return;
    function sync(){
      const {display, iso} = normalizeDisplayAndISO(displayEl.value);
      if (isoEl) isoEl.value = iso || '';
    }
    function snap(){
      const {display, iso} = normalizeDisplayAndISO(displayEl.value);
      if (display) displayEl.value = display;
      if (isoEl) isoEl.value = iso || '';
    }
    ['input','change'].forEach(ev => displayEl.addEventListener(ev, sync));
    displayEl.addEventListener('blur', snap);
    snap();
  }
  bindDob(dobDispEl, dobIsoEl);

  function escapeHtml(s){
    return (s||'').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  }

  // ---------------- Hidden inputs for POST ----------------
  function renderHiddenInputs(){
    if (!hiddenWrap) return;
    hiddenWrap.innerHTML = '';
    CHILDREN.forEach((c, i) => {
      [
        ['first_name', c.first_name || ''],
        ['last_name',  c.last_name  || ''],
        ['dob',        c.dob        || ''],
        ['in_canada',  c.in_canada  || '']
      ].forEach(([k,v]) => {
        const inp = document.createElement('input');
        inp.type  = 'hidden';
        inp.name  = `children[${i}][${k}]`;
        inp.value = v;
        hiddenWrap.appendChild(inp);
      });
    });
  }

  // ---------------- Add button placement ----------------
  function placeAddButton(){
    if (!addBtn || !wrapTop || !wrapBot) return;
    if (CHILDREN.length > 0) {
      wrapTop.style.display = 'none';
      wrapBot.style.display = 'flex';
      wrapBot.appendChild(addBtn);
    } else {
      wrapTop.style.display = 'flex';
      wrapBot.style.display = 'none';
      wrapTop.appendChild(addBtn);
    }
  }

  // ---------------- Table render ----------------
  function renderTable(){
    tbody.innerHTML = '';
    if (!CHILDREN.length){
      tbody.appendChild(emptyTr);
    } else {
      CHILDREN.forEach((c) => {
        const dobDisplay = c.dob_display || isoToDisplay(c.dob) || '';
        const tr = document.createElement('tr');
        tr.innerHTML = `
  <td style="padding:8px;">${escapeHtml(c.first_name || '')}</td>
  <td style="padding:8px;">${escapeHtml(c.last_name  || '')}</td>
  <td style="padding:8px;">${escapeHtml(dobDisplay)}</td>
  <td style="padding:8px;">${c.in_canada === 'No' ? 'No' : 'Yes'}</td>
  <td class="actions-cell">
    <a href="#" class="action-link" data-edit="${c.id}">Edit</a>
    <span class="action-sep" aria-hidden="true">•</span>
    <a href="#" class="action-link delete" data-del="${c.id}">Delete</a>
  </td>`;
        tbody.appendChild(tr);
      });
    }

    // delegate actions
    tbody.querySelectorAll('[data-edit]').forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const id  = btn.getAttribute('data-edit');
        const row = CHILDREN.find(x=>x.id===id);
        if (row) openChildModal(row);
      });
    });
    tbody.querySelectorAll('[data-del]').forEach(btn=>{
      btn.addEventListener('click', (e)=>{
        e.preventDefault();
        const id  = btn.getAttribute('data-del');
        const row = CHILDREN.find(x=>x.id===id);
        openConfirm(id, row);
      });
    });

    renderHiddenInputs();
    placeAddButton();
  }

  // ---------------- Modal controls ----------------
  function resetChildForm(){
    if (!idEl || !fNameEl || !lNameEl || !dobDispEl || !dobIsoEl || !inYesEl || !inNoEl) return; // Safe fix: null checks
    idEl.value = '';
    fNameEl.value = '';
    lNameEl.value = '';
    dobDispEl.value = '';
    dobIsoEl.value  = '';
    inYesEl.checked = false;
    inNoEl.checked  = false;
  }

  function openChildModal(row){
    if (!modal || !titleEl || !idEl || !fNameEl || !lNameEl || !dobDispEl || !inYesEl || !inNoEl) return; // Safe fix: null checks
    if (row){
      titleEl.textContent = 'Edit Child';
      idEl.value     = row.id;
      fNameEl.value  = row.first_name || '';
      lNameEl.value  = row.last_name  || '';
      dobDispEl.value= row.dob_display || isoToDisplay(row.dob) || '';
      // snap visible + ISO
      dobDispEl.dispatchEvent(new Event('blur', {bubbles:true}));
      (row.in_canada === 'No' ? inNoEl : inYesEl).checked = true;
    } else {
      titleEl.textContent = 'Add Child';
      resetChildForm();
    }
    open(modal);
  }

  function closeChildModal(){ close(modal); }

  btnCancel?.addEventListener('click', closeChildModal);
  modal?.addEventListener('click', (e)=>{
    if (e.target === modal || e.target.classList.contains('qs-modal__backdrop')) closeChildModal();
  });

  btnSave?.addEventListener('click', function(){
    if (!form.reportValidity()) return;

    // normalize one last time
    const norm = normalizeDisplayAndISO(dobDispEl.value);

    const data = {
      id:          idEl.value || uid(),
      first_name:  fNameEl.value.trim(),
      last_name:   lNameEl.value.trim(),
      dob_display: norm.display,           // MMM | DD | YYYY
      dob:         norm.iso,               // YYYY-MM-DD
      in_canada:   inNoEl.checked ? 'No' : 'Yes'
    };

    const idx = CHILDREN.findIndex(x=>x.id===data.id);
    if (idx >= 0) CHILDREN[idx] = data;
    else CHILDREN.push(data);

    renderTable();
    closeChildModal();
  });

  // ---------------- Delete confirm ----------------
  function openConfirm(id, row){
    confirmText.textContent = `Delete ${row?.first_name || 'this'} ${row?.last_name || 'child'}?`;
    open(confirmModal);

    function cleanup(){
      confirmYes.removeEventListener('click', onYes);
      confirmCancel.removeEventListener('click', onCancel);
      confirmModal.removeEventListener('click', onBackdrop);
    }
    function onYes(){
      const i = CHILDREN.findIndex(x=>x.id===id);
      if (i >= 0) CHILDREN.splice(i,1);
      renderTable();
      close(confirmModal);
      cleanup();
    }
    function onCancel(){ close(confirmModal); cleanup(); }
    function onBackdrop(e){
      if (e.target===confirmModal || e.target.classList.contains('qs-modal__backdrop')) onCancel();
    }

    confirmYes.addEventListener('click', onYes);
    confirmCancel.addEventListener('click', onCancel);
    confirmModal.addEventListener('click', onBackdrop);
  }

  // ---------------- Initial paint ----------------
  renderTable();

});
</script>




<!-- YES RENT 1 -->

<script>
document.addEventListener('DOMContentLoaded', () => {
  // ---------- helpers ----------
  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const show = (el, on) => { 
    if (el) {
      el.style.display = on ? '' : 'none';
      // Clear validation errors for all fields in this element when hidden
      if (!on && typeof hideFieldError === 'function') {
        el.querySelectorAll('input, select, textarea').forEach(field => {
          if (field.name) {
            hideFieldError(field.name);
          }
        });
      }
    }
  };
  const req  = (el, on) => { 
    if (!el) return;
    on ? el.setAttribute('required','required') : el.removeAttribute('required');
    // Clear validation errors when field becomes non-required
    if (!on && el.name && typeof hideFieldError === 'function') {
      hideFieldError(el.name);
    }
  };
  const esc  = s => (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));
  const uid  = p => `${p}_${Date.now().toString(36)}${Math.random().toString(36).slice(2,8)}`;
  const open  = el => el && (el.style.display='block');
  const close = el => el && (el.style.display='none');

  // ======= RENT BENEFIT (legacy) =======
  // IMPORTANT: If the new inline Rent UI exists, skip the legacy rent code entirely.
  const hasNewRentUI = !!document.getElementById('rent-addresses');

  if (!hasNewRentUI) {
    const rentYes = $('#rent_benefit_yes');
    const rentNo  = $('#rent_benefit_no');
    const rentSec = $('#rent-section');

    const rentSeed = $('#rent-seed');
    let RENTS = [];
    try { RENTS = rentSeed ? JSON.parse(rentSeed.textContent || '[]') || [] : []; } catch { RENTS = []; }

    const rentTbody  = $('#rent-tbody');
    const rentHidden = $('#rent-hidden-inputs');

    // modal bits
    const rentModal  = $('#rent-modal');
    const rentForm   = $('#rent-form');
    const rentTitle  = $('#rent-modal-title');
    const rentId     = $('#rent_id');
    const rentAddr   = $('#rent_address');
    const fromMon    = $('#rent_from_month');
    const fromYear   = $('#rent_from_year');
    const toMon      = $('#rent_to_month');
    const toYear     = $('#rent_to_year');
    const rentAmt    = $('#rent_amount');

    // confirm bits
    const rentConfirm        = $('#rent-confirm');
    const rentConfirmText    = $('#rent-confirm-text');
    const rentConfirmYes     = $('#rent-confirm-yes');
    const rentConfirmCancel  = $('#rent-confirm-cancel');

    // show/hide rent section
    function applyRentVisibility() { show(rentSec, rentYes && rentYes.checked); }
    [rentYes, rentNo].forEach(el => el && el.addEventListener('change', applyRentVisibility));
    applyRentVisibility();

    // table render + hidden inputs
    function renderRentHidden() {
      rentHidden.innerHTML = '';
      RENTS.forEach((r,i) => {
        const map = {
          address:r.address||'', from_month:r.from_month||'', from_year:r.from_year||'',
          to_month:r.to_month||'', to_year:r.to_year||'', amount:r.amount||''
        };
        Object.entries(map).forEach(([k,v]) => {
          const inp = document.createElement('input');
          inp.type='hidden'; inp.name=`rents[${i}][${k}]`; inp.value=v; rentHidden.appendChild(inp);
        });
      });
    }

    function renderRentTable() {
      if (!RENNTSlength()) {
        rentTbody.innerHTML = '<tr><td colspan="5" style="padding:10px;opacity:.7;">No addresses added yet.</td></tr>';
      } else {
        rentTbody.innerHTML = RENTS.map(r => `
          <tr>
            <td style="padding:8px;">${esc(r.address)}</td>
            <td style="padding:8px;">${esc(r.from_label||'')}</td>
            <td style="padding:8px;">${esc(r.to_label||'')}</td>
            <td style="padding:8px;">${esc(r.amount||'')}</td>
            <td style="padding:8px;display:flex;gap:8px;">
              <button type="button" class="link-btn edit" data-action="edit" data-id="${r.id}">Edit</button>
              <button type="button" class="link-btn delete danger"            data-action="del"  data-id="${r.id}">Delete</button>
            </td>
          </tr>`).join('');
      }
      renderRentHidden();
    }
    const RENNTSlength = () => RENTS && RENTS.length;

    // event delegation for edit/delete
    rentTbody?.addEventListener('click', e => {
      const btn = e.target.closest('[data-action]');
      if (!btn) return;
      const id = btn.dataset.id;
      const row = RENTS.find(x => x.id === id);
      if (btn.dataset.action === 'edit') openRentModal(row);
      if (btn.dataset.action === 'del')  openRentConfirm(row);
    });

    // add / save / cancel
    $('#btn-add-rent')?.addEventListener('click', () => openRentModal());
    $('#rent-cancel')?.addEventListener('click', () => close(rentModal));
    rentModal?.addEventListener('click', e => { if (e.target === rentModal || e.target.classList.contains('qs-modal__backdrop')) close(rentModal); });

    $('#rent-save')?.addEventListener('click', () => {
      if (!rentForm.reportValidity()) return;
      const data = {
        id: rentId.value || uid('r'),
        address: rentAddr.value.trim(),
        from_month: fromMon.value, from_year: fromYear.value.trim(),
        to_month: toMon.value,     to_year:   toYear.value.trim(),
        amount: rentAmt.value.trim()
      };
      data.from_label = data.from_month && data.from_year ? `${data.from_month} ${data.from_year}` : '';
      data.to_label   = data.to_month   && data.to_year   ? `${data.to_month} ${data.to_year}`   : '';
      const i = RENTS.findIndex(x => x.id === data.id);
      (i >= 0) ? RENTS.splice(i,1,data) : RENTS.push(data);
      renderRentTable(); close(rentModal);
    });

    function openRentModal(row) {
      if (row) {
        rentTitle.textContent = 'Edit Address';
        rentId.value = row.id;
        rentAddr.value = row.address || '';
        fromMon.value  = row.from_month || '';  fromYear.value = row.from_year || '';
        toMon.value    = row.to_month   || '';  toYear.value   = row.to_year   || '';
        rentAmt.value  = row.amount     || '';
      } else {
        rentTitle.textContent = 'Add Address';
        rentForm.reset(); rentId.value = '';
      }
      open(rentModal);
    }

    function openRentConfirm(row) {
      rentConfirmText.textContent = `Delete ${row?.address || 'this'} record?`;
      open(rentConfirm);
      function cleanup() {
        rentConfirmYes.removeEventListener('click', onYes);
        rentConfirmCancel.removeEventListener('click', onNo);
        rentConfirm.removeEventListener('click', onBackdrop);
      }
      function onYes() {
        const i = RENTS.findIndex(x => x.id === row.id);
        if (i >= 0) RENTS.splice(i,1);
        renderRentTable(); close(rentConfirm); cleanup();
      }
      function onNo() { close(rentConfirm); cleanup(); }
      function onBackdrop(e){ if (e.target === rentConfirm || e.target.classList.contains('qs-modal__backdrop')) onNo(); }

      rentConfirmYes.addEventListener('click', onYes);
      rentConfirmCancel.addEventListener('click', onNo);
      rentConfirm.addEventListener('click', onBackdrop);
    }

    renderRentTable();
  }
  // ======= END legacy RENT block guard =======


  // ======= GIG INCOME =======
  const gigYes = $('#gig_yes');
  const gigNo  = $('#gig_no');
  const gigSec = $('#gig-section');

  const dz      = $('#gig-drop');
  const dzInput = $('#gig_tax_summary');
  const dzBtn   = $('#gig-browse');
  const dzList  = $('#gig-files');

  const expSummary = $('#gig_expenses_summary');

  const hstYes   = $('#hst_yes');
  const hstNo    = $('#hst_no');
  const hstBox   = $('#hst-fields');
  const hstNum   = $('#hst_number');
  const hstAcc   = $('#hst_access');
  const hstStart = $('#hst_start');
  const hstEnd   = $('#hst_end');

  function applyGig() {
    const on = gigYes && gigYes.checked;
    show(gigSec, on);
    req(expSummary, on);
  }
  [gigYes, gigNo].forEach(el => el && el.addEventListener('change', applyGig));
  applyGig();

  function applyHst() {
    const on = hstYes && hstYes.checked;
    show(hstBox, on);
    [hstNum, hstAcc, hstStart, hstEnd].forEach(el => req(el, on));
  }
  [hstYes, hstNo].forEach(el => el && el.addEventListener('change', applyHst));
  applyHst();

  // simple dropzone
  dzBtn?.addEventListener('click', () => dzInput.click());
  dz?.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
  dz?.addEventListener('dragleave', () => dz.classList.remove('dragover'));
  dz?.addEventListener('drop', e => {
    e.preventDefault(); dz.classList.remove('dragover');
    dzInput.files = e.dataTransfer.files; listFiles();
  });
  dzInput?.addEventListener('change', listFiles);

  function listFiles() {
    dzList.innerHTML = '';
    const files = dzInput.files || [];
    if (!files.length) return;
    const ul = document.createElement('ul');
    ul.style.margin = '8px 0 0'; ul.style.padding = '0 0 0 18px';
    for (const f of files) {
      const li = document.createElement('li');
      li.textContent = `${f.name} (${Math.round(f.size/1024)} KB)`; ul.appendChild(li);
    }
    dzList.appendChild(ul);
  }
});
</script>

<!-- YES RENT 3 -->

<script>
(function(){
  const root = document.getElementById('rent-tbody');
  if(!root) return;

  function formatMoney(str, pad2=true){
    str = (str||'').replace(/[^\d.]/g,'');
    const d = str.indexOf('.');
    if(d !== -1) str = str.slice(0,d+1) + str.slice(d+1).replace(/\./g,'');
    let [a,b=''] = str.split('.');
    a = a.replace(/^0+(?=\d)/,'').replace(/\B(?=(\d{3})+(?!\d))/g,',');
    b = b.slice(0,2); if(pad2) b = (b+'00').slice(0,2);
    const out = b ? `${a}.${b}` : (pad2 ? `${a}.00` : a);
    return out === '.00' ? '' : out;
  }

  function wire(el){
    if(!el || el.dataset.wired) return;
    el.dataset.wired = '1';
    if(el.value.trim()) el.value = formatMoney(el.value, true);
    el.addEventListener('input', ()=>{
      const s = el.selectionStart, before = el.value;
      el.value = formatMoney(before, false);
      const diff = el.value.length - before.length;
      el.setSelectionRange(Math.max(0,(s??el.value.length)+diff), Math.max(0,(s??el.value.length)+diff));
    });
    el.addEventListener('blur', ()=>{ el.value = formatMoney(el.value, true); });
  }

  function scan(){
    root.querySelectorAll('tr td:nth-child(4) input').forEach(wire);
  }
  scan();

  // if rows are added dynamically, observe and wire new inputs
  new MutationObserver(scan).observe(root, {childList:true, subtree:true});
})();
</script>

<!-- YES UPLOAD 1 -->

<script>
document.addEventListener('DOMContentLoaded', () => {
  /* -------------------- Toasts -------------------- */
  const toastBox = document.createElement('div');
  toastBox.className = 'dz-toastbox';
  document.body.appendChild(toastBox);
  function toast(msg, kind='ok'){
    const t = document.createElement('div');
    t.className = `dz-toast ${kind==='ok' ? 'dz-toast-ok' : 'dz-toast-bad'}`;
    t.setAttribute('role','status'); t.setAttribute('aria-live','polite');
    t.innerHTML = `
      <svg class="dz-toast-icon" viewBox="0 0 24 24" aria-hidden="true">
        <circle cx="12" cy="12" r="12" fill="${kind==='ok' ? '#16a34a' : '#dc2626'}"/>
        ${kind==='ok'
          ? '<path d="M7 12l3 3 7-7" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>'
          : '<path d="M8 8l8 8M16 8l-8 8" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round"/>'}
      </svg>
      <span class="dz-toast-msg">${msg}</span>
      <button class="dz-toast-x" aria-label="Dismiss">×</button>
    `;
    toastBox.appendChild(t);
    const kill = ()=> t.remove();
    t.querySelector('.dz-toast-x').addEventListener('click', kill);
    setTimeout(kill, 3000);
  }

  /* -------------------- Helpers -------------------- */
  const human = (b)=> (b<1024)?`${b} B`:[ 'KB','MB','GB' ].map((u,i)=>b<1024**(i+2)?`${(b/1024**(i+1)).toFixed(i?2:0)} ${u}`:null).find(Boolean) || `${(b/1024**3).toFixed(2)} GB`;
  const extOf  = (f)=> (f.name.split('.').pop() || '').toLowerCase();
  const isImg  = (f)=> (f.type||'').startsWith('image/');
  const iconSVG = (ext)=>{
    const map = { pdf:'#ef4444', doc:'#2563eb', docx:'#2563eb', xls:'#16a34a', xlsx:'#16a34a',
                  txt:'#64748b', csv:'#16a34a', ppt:'#f97316', pptx:'#f97316',
                  php:'#64748b', jpeg:'#f59e0b', jpg:'#f59e0b', png:'#f59e0b', webp:'#f59e0b', dxf:'#64748b' };
    const color = map[ext] || '#64748b';
    const label = (ext||'file').slice(0,3).toUpperCase();
    return `<svg class="dz-icon" viewBox="0 0 24 24" aria-hidden="true">
      <path fill="${color}" d="M6 2h8l6 6v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z"/>
      <path fill="#fff" d="M14 2v6h6"/>
      <text x="12" y="18" text-anchor="middle" fill="#fff" font-size="7" font-weight="700">${label}</text>
    </svg>`;
  };

  /* -------------------- Dropzone -------------------- */
  class DZ {
    constructor(root){
      this.root = root;

      // file input
      let wired = root.dataset.input ? document.querySelector(root.dataset.input) : null;
      if (!wired || !root.contains(wired)) wired = root.querySelector('input[type="file"]');
      this.input = wired;

      // list container (move external inside)
      const externalList = root.dataset.list ? document.querySelector(root.dataset.list) : null;
      if (externalList && !root.contains(externalList)) root.appendChild(externalList);
      this.list = (externalList && externalList.classList.add('dz-list'), externalList) || root.querySelector('.dz-list') || (() => {
        const d = document.createElement('div'); d.className = 'dz-list'; root.appendChild(d); return d;
      })();

      this.browseBtn = root.querySelector('.dz-browse') || root.querySelector('.dropzone-ui button');
      this.dt = new DataTransfer(); // authoritative FileList we control
      this.header = null;           // header row ref
      this.needsPw = root.dataset.password === 'true';

      this.bind();
      if (this.input?.files?.length) this.addFiles(this.input.files); // restore
    }

    bind(){
      this.browseBtn && this.browseBtn.addEventListener('click', () => this.input?.click());
      this.input && this.input.addEventListener('change', () => this.addFiles(this.input.files));

      ['dragenter','dragover'].forEach(ev => this.root.addEventListener(ev, e => {
        e.preventDefault(); this.root.classList.add('dragover');
      }));
      ['dragleave','drop'].forEach(ev => this.root.addEventListener(ev, e => {
        e.preventDefault(); if (ev!=='drop') this.root.classList.remove('dragover');
      }));
      this.root.addEventListener('drop', e => {
        this.root.classList.remove('dragover');
        this.addFiles(e.dataTransfer.files);
      });
    }

    ensureHeader(){
      if (this.header || !this.needsPw) return;
      const h = document.createElement('div');
      h.className = 'dz-head';
      h.innerHTML = `<div>File Name</div><div>Is Password Protected?</div><div>Password</div>`;
      this.list.prepend(h);
      this.header = h;
    }
    maybeDropHeader(){
      if (!this.needsPw) return;
      const items = this.list.querySelectorAll('.dz-item');
      if (!items.length && this.header){ this.header.remove(); this.header = null; }
    }

    addFiles(fileList){
      const files = Array.from(fileList || []);
      if (!files.length) return;

      files.forEach(file => {
        this.dt.items.add(file);
        this.ensureHeader();
        this.list.appendChild(this.renderItem(file));
      });

      if (this.input) this.input.files = this.dt.files;

      // Simulate upload progress and hide loading indicator when done
      requestAnimationFrame(() => {
        this.list.querySelectorAll('.dz-item').forEach((item, idx) => {
          const loadingDiv = item.querySelector('.dz-loading');
          const barFill = loadingDiv?.querySelector('.dz-bar-fill');
          if (!barFill || !loadingDiv) return;
          
          // Animate progress bar
          let progress = 0;
          const interval = setInterval(() => {
            progress += Math.random() * 15 + 5; // Random progress increments
            if (progress >= 100) {
              progress = 100;
              clearInterval(interval);
              // Hide loading indicator after upload completes
              setTimeout(() => {
                if (loadingDiv) loadingDiv.style.display = 'none';
              }, 300);
            }
            barFill.style.width = progress + '%';
          }, 100);
        });
      });

      toast('Nice Work! Documents have been added','ok');
    }

    removeAt(index){
      const card = this.list.querySelectorAll('.dz-item')[index];
      const img  = card?.querySelector('.dz-thumb');
      if (img && img.src.startsWith('blob:')) URL.revokeObjectURL(img.src);

      this.dt.items.remove(index);
      if (this.input) this.input.files = this.dt.files;
      card?.remove();
      this.maybeDropHeader();
      toast('File deleted successfully.','bad');
    }

    renderItem(file){
      const baseName = (this.input?.name || 'upload[]').replace(/\[\]$/,'');
      const el = document.createElement('div');
      el.className = 'dz-item';

      /* LEFT (icon + meta + progress + remove) */
      const left = document.createElement('div');
      left.className = 'dz-left';
      const ext = extOf(file);
      const preview = isImg(file)
        ? `<img class="dz-thumb" alt="" src="${URL.createObjectURL(file)}">`
        : iconSVG(ext);
      left.innerHTML = `
        ${preview}
        <div class="dz-meta">
          <div class="dz-name-row" style="display:flex; align-items:center; gap:12px;">
            <div style="flex:1; min-width:0;">
              <div class="dz-name" title="${file.name}">${file.name}</div>
              <div class="dz-sub">${human(file.size)}</div>
            </div>
            <div class="dz-loading" style="display:flex; align-items:center; gap:8px;">
              <div class="dz-bar" style="width:60px; height:4px; background:#e5f4e8; border-radius:4px; overflow:hidden; margin:0;">
                <div class="dz-bar-fill" style="width:0%; height:100%;"></div>
              </div>
              <span style="font-size:12px; color:#64748b;">Uploading...</span>
            </div>
          </div>
        </div>
        <button type="button" class="dz-remove" aria-label="Remove">&times;</button>
      `;
      el.appendChild(left);
      left.querySelector('.dz-remove').addEventListener('click', () => {
        const cards = Array.from(this.list.querySelectorAll('.dz-item'));
        this.removeAt(cards.indexOf(el));
      });

      if (this.needsPw) {
        /* MIDDLE (Yes/No pills — NEUTRAL by default) */
        const mid = document.createElement('div');
        mid.className = 'dz-pw-yn';
        mid.setAttribute('data-label','Is Password Protected?');
        mid.innerHTML = `
          <div class="yn-group" role="group" aria-label="Is password protected?">
            <button type="button" class="yn-btn outline pw-yes">Yes</button>
            <button type="button" class="yn-btn outline pw-no">No</button>
          </div>
          <input type="hidden" name="${baseName}_pw_protected[]" value="">
        `;
        el.appendChild(mid);

        /* RIGHT (Password input — disabled until YES) */
        const right = document.createElement('div');
        right.className = 'dz-pw-input';
        right.setAttribute('data-label','Password');
        right.innerHTML = `
          <input class="pw-input" type="text" name="${baseName}_pw[]" placeholder="Password" disabled>
        `;
        el.appendChild(right);

        /* Toggle logic: states 'yes' | 'no' | 'unset' */
        const yesBtn = mid.querySelector('.pw-yes');
        const noBtn  = mid.querySelector('.pw-no');
        const hid    = mid.querySelector('input[type="hidden"]');
        const pwIn   = right.querySelector('.pw-input');

        function paint(state){
          const isYes = state === 'yes';
          const isNo  = state === 'no';

          // Remove all classes first, then add the correct ones
          yesBtn.classList.remove('solid', 'outline');
          noBtn.classList.remove('solid', 'outline');
          
          if (isYes) {
            yesBtn.classList.add('solid');
            noBtn.classList.add('outline');
          } else if (isNo) {
            yesBtn.classList.add('outline');
            noBtn.classList.add('solid');
          } else {
            // unset state - both outline
            yesBtn.classList.add('outline');
            noBtn.classList.add('outline');
          }

          const enablePw = isYes;
          pwIn.disabled = !enablePw;
          if (!enablePw) pwIn.value = '';
          hid.value = (isYes ? 'yes' : (isNo ? 'no' : ''));
          
          // Hide password field when "No" is selected or unset, show ONLY when "Yes" is selected
          if (isYes) {
            right.style.display = '';
            if (enablePw) pwIn.focus();
          } else {
            // Hide for both "No" and "unset" states (hides the entire password field container including label)
            right.style.display = 'none';
          }
        }

        yesBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          paint('yes');
          // Clear any error on this field when user makes a selection
          const pwGroup = mid;
          if (pwGroup) {
            pwGroup.classList.remove('error');
            const errorText = pwGroup.querySelector('.fi-error-text');
            if (errorText) errorText.remove();
          }
        });
        noBtn.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();
          paint('no');
          // Clear any error on this field when user makes a selection
          const pwGroup = mid;
          if (pwGroup) {
            pwGroup.classList.remove('error');
            const errorText = pwGroup.querySelector('.fi-error-text');
            if (errorText) errorText.remove();
          }
        });

        paint('unset'); // neutral on load - user must select Yes or No
      }

      return el;
    }
  }

  // Activate all dropzones on the page
  document.querySelectorAll('.dropzone').forEach(dz => new DZ(dz));
});
</script>


<script>
/* Conditional tabs: show both tabs only if Married/Common-Law */
(function () {
  const tabsBar = document.querySelector('.upload-tabs');
  const tabA  = document.getElementById('tab-applicant');
  const tabS  = document.getElementById('tab-spouse');
  const paneA = document.getElementById('upload-applicant');
  const paneS = document.getElementById('upload-spouse');
  if (!tabsBar || !tabA || !tabS || !paneA || !paneS) return;

  // ---- helpers -------------------------------------------------------
  function getMaritalValue() {
    const r = document.querySelector('input[name="marital_status"]:checked');
    if (r) return (r.value || '').trim();
    const s = document.querySelector('select[name="marital_status"]');
    return (s ? (s.value || '') : '').trim();
  }
  function marriedLike() {
    // supports “Common Law”, “Common-Law”, casing, etc.
    const v = getMaritalValue().toLowerCase().replace(/[-\s]+/g, ' ');
    return v === 'married' || v === 'common law';
  }

  function activate(which) {
    const showSpouse = !tabS.hidden; // spouse tab visible?
    const isA = (which === 'app') || (which !== 'sp' && !showSpouse);

    // panes
    paneA.hidden = !isA;
    paneS.hidden = isA || !showSpouse;

    // aria+classes only if tab bar visible
    if (!tabsBar.hidden) {
      tabA.classList.toggle('active', isA);
      tabS.classList.toggle('active', !isA);
      tabA.setAttribute('aria-selected', isA ? 'true' : 'false');
      tabS.setAttribute('aria-selected', isA ? 'false' : 'true');
    }
  }

  function render() {
    const showTabs = marriedLike();

    // Toggle whole tab bar + spouse tab
    tabsBar.hidden = !showTabs;
    tabS.hidden = !showTabs;

    // If tabs are hidden, force Applicant view
    if (!showTabs) {
      activate('app');      // Applicant only
      paneS.hidden = true;  // ensure spouse pane is hidden
      return;
    }

    // Tabs visible: if neither active, default to Applicant
    if (!tabA.classList.contains('active') && !tabS.classList.contains('active')) {
      tabA.classList.add('active');
      tabS.classList.remove('active');
    }

    // Re-apply current active state
    activate(tabS.classList.contains('active') ? 'sp' : 'app');
  }

  // Click handlers (only meaningful when tabs show)
  tabA.addEventListener('click', () => { if (!tabsBar.hidden) activate('app'); });
  tabS.addEventListener('click', () => { if (!tabsBar.hidden) activate('sp'); });

  // React to marital status changes
  document.addEventListener('change', (e) => {
    if (e.target && e.target.name === 'marital_status') render();
  });

  // Initial render
  render();
})();
</script>



                                   

<!-- YES 3 -->      

<script>
document.addEventListener('DOMContentLoaded', function () {
  // --- Map dropdown to your existing (hidden) radios ---
  const map = {
    "Single":     document.getElementById('ms_single'),
    "Married":    document.getElementById('ms_married'),
    "Common Law": document.getElementById('ms_commonlaw'),
    "Separated":  document.getElementById('ms_separated'),
    "Divorced":   document.getElementById('ms_divorced'),
    "Widowed":    document.getElementById('ms_widowed')
  };
  const select = document.getElementById('marital_status_select');

  // --- Blocks & bits we toggle ---
  const mclDateBlock  = document.getElementById('status-date-block');       // Married/Common Law date
  const mclDateLabel  = document.getElementById('status-date-label');
  const childrenBlock = document.getElementById('children-block');
  const spouseFile    = document.getElementById('spouse-file-block');

  const sdwDateBlock  = document.getElementById('status-date-sdw-block');    // Separated/Divorced/Widowed date
  const sdwDateLabel  = document.getElementById('status-date-sdw-label');

  const canadaYes   = document.getElementById('spouse_in_canada_yes');
  const canadaNo    = document.getElementById('spouse_in_canada_no');
  const canadaGroup = canadaYes?.closest('.yn-group') || canadaNo?.closest('.yn-group');
  const canadaH2    = canadaGroup?.previousElementSibling; // "Residing in Canada?"

  // Ensure spouse-file block renders right after the Canada buttons
  if (canadaGroup && spouseFile && !spouseFile.dataset.placed) {
    canadaGroup.insertAdjacentElement('afterend', spouseFile);
    spouseFile.dataset.placed = '1';
  }

  // Helpers
  const show = (el, on) => { if (el) el.style.display = on ? '' : 'none'; };
  const msVal = () => document.querySelector('input[name="marital_status"]:checked')?.value || '';
  const isMCL = () => (msVal() === 'Married' || msVal() === 'Common Law');

  function setMclLabel() {
    if (!mclDateLabel) return;
    mclDateLabel.textContent = (msVal() === 'Common Law') ? 'Date of Status Start' : 'Date of Marriage';
  }
  function setSdwLabel() {
    if (!sdwDateLabel) return;
    const v = msVal();
    sdwDateLabel.textContent =
      v === 'Separated' ? 'Date of Separation' :
      v === 'Divorced'  ? 'Date of Divorce'    :
      v === 'Widowed'   ? 'Date of Passing'    : 'Date';
  }

  // --- Event handlers split to avoid overwriting user's Canada click ---
  function onMaritalChange() {
    const marriedCL = isMCL();

if (marriedCL) {
  // don’t force any Canada answer
  setMclLabel();
  show(mclDateBlock,  true);
  show(childrenBlock, true);
  show(canadaGroup,   true);
  if (canadaH2 && /^H\d$/i.test(canadaH2.tagName)) show(canadaH2, true);

  // spouse-file shows only if user chose "Yes" for Canada
  show(spouseFile, !!canadaYes?.checked);

  // hide SDW date
  show(sdwDateBlock, false);
    } else {
      // Not MCL → hide MCL-specific stuff
      show(mclDateBlock,  false);
      show(childrenBlock, true);
      show(canadaGroup,   false);
      if (canadaH2 && /^H\d$/i.test(canadaH2.tagName)) show(canadaH2, false);
      show(spouseFile, false);

      // If S/D/W → show that date with correct label
       const v = msVal();
    const isSDW = (v === 'Separated' || v === 'Divorced' || v === 'Widowed');
    show(childrenBlock, isSDW);

    show(canadaGroup,   false);
    if (canadaH2 && /^H\d$/i.test(canadaH2.tagName)) show(canadaH2, false);
    show(spouseFile, false);

    if (isSDW) {
      setSdwLabel();
      show(sdwDateBlock, true);
    } else {
      show(sdwDateBlock, false); // Single or blank
    }
    }
  }

  function onCanadaChange() {
    // Only meaningful in MCL states
    if (!isMCL()) return;
    show(spouseFile, !!canadaYes?.checked);
  }

  function fireAll(el){
    ['click','input','change'].forEach(type =>
      el?.dispatchEvent(new Event(type, { bubbles:true }))
    );
  }

  // --- Wire events ---
  // Dropdown -> radios, then handle marital change immediately
  select?.addEventListener('change', function () {
    const r = map[this.value];
    if (!r) return;
    r.checked = true;
    fireAll(r);        // keep legacy listeners alive
    onMaritalChange(); // ensure UI updates instantly
    if (this.value === 'Common Law') {
      const modal = document.getElementById('commonlawModal');
      const okBtn = document.getElementById('clOkBtn');
      if (modal){ modal.hidden = false; document.body.style.overflow = 'hidden'; okBtn?.focus(); }
    }
  });

  // Hidden radios (in case they’re toggled elsewhere)
  document.querySelectorAll('input[name="marital_status"]').forEach(r =>
    r.addEventListener('change', onMaritalChange)
  );

  // Canada radios
  canadaYes?.addEventListener('change', onCanadaChange);
  canadaNo?.addEventListener('change',  onCanadaChange);

  // --- Initial paint ---
  // Sync dropdown from any pre-checked radio
  const pre = document.querySelector('input[name="marital_status"]:checked');
  if (pre && select) select.value = pre.value;
  onMaritalChange();

  // Close CL modal wiring (if used)
  const modal = document.getElementById('commonlawModal');
  const okBtn = document.getElementById('clOkBtn');
  okBtn?.addEventListener('click', function(){
    if (!modal) return; modal.hidden = true; document.body.style.overflow = '';
  });
  modal?.addEventListener('click', (e)=>{ if (e.target.dataset?.close !== undefined){ modal.hidden = true; document.body.style.overflow=''; }});
  window.addEventListener('keydown', (e)=>{ if (!modal?.hidden && e.key === 'Escape'){ modal.hidden = true; document.body.style.overflow=''; }});
});
</script>



<!-- YES WORLD 1 -->
<!-- YES WORLD 1 (fixed: no placeholders; robust resize) -->
<script>
(function(){
  function setupWiMobile(grid){
    if (!grid) return;

    // Inject minimal mobile CSS once
    if (!document.getElementById('wi-mobile-card-css')){
      const css = `
      /* hide placeholders everywhere */
      .wi-grid .fi-input::placeholder, .wi-stack .fi-input::placeholder { color:transparent !important; }
      /* mobile card layout */
      @media (max-width:680px){
        .wi-stack{ display:grid; gap:12px; width:100%; }
        .wi-card{ border:1px solid #e5e7eb; border-radius:12px; background:#fff; padding:12px 14px; }
        .wi-line{ font-size:14px; color:#0f172a; margin-bottom:8px; }
        .wi-line .wi-k{ font-weight:700; margin-right:6px; }
        .wi-line .wi-v{ font-weight:500; }
        .wi-mobile-label{ display:block; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#64748b; margin:6px 0 8px; }
        .wi-card .wi-inline{ position:relative; width:100%; margin:0; }
        .wi-card .wi-inline::before{ content:"$"; position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#6b7280; font-weight:600; pointer-events:none; }
        .wi-card .wi-inline .fi-input{ width:100%; height:44px; padding:10px 12px 10px 28px; border:1px solid #d1d5db; border-radius:8px; background:#fff; font-size:16px; color:#111827; outline:none; box-sizing:border-box; }
        .wi-card .wi-inline .fi-input:focus{ border-color:#0284c7; box-shadow:0 0 0 3px rgba(11,100,194,.15); }
      }`;
      const s = document.createElement('style'); s.id='wi-mobile-card-css'; s.textContent=css; document.head.appendChild(s);
    }

    const left  = grid.querySelector('.wi-col--period');
    const right = grid.querySelector('.wi-col--income');
    if (!left || !right) return;

    const periodRows = Array.from(left.querySelectorAll('.wi-row'));
    const incomeRows = Array.from(right.querySelectorAll('.wi-row'));

    // Keep references to the actual groups (inputs)
    const groups = incomeRows.map(r => r.querySelector('.fi-group'));

    // Mobile stack container
    const stack = document.createElement('div');
    stack.className = 'wi-stack';
    stack.hidden = true;
    grid.insertAdjacentElement('afterend', stack);

    const text = el => (el?.textContent || '').trim();

    function buildCards(){
      stack.innerHTML = '';
      periodRows.forEach((pRow, i) => {
        const card = document.createElement('div');
        card.className = 'wi-card';

        const line = document.createElement('div');
        line.className = 'wi-line';
        line.innerHTML = `<span class="wi-k">Period:</span><span class="wi-v wi-mobile-period">${text(pRow)}</span>`;

        const lab2 = document.createElement('div');
        lab2.className = 'wi-mobile-label';
        lab2.textContent = 'World Income (CAD)';

        const grp = groups[i];
        if (grp){
          // hide any float label and placeholder
          const fl = grp.querySelector('.fi-float-label'); if (fl) fl.style.display = 'none';
          const inp = grp.querySelector('.fi-input'); if (inp) inp.removeAttribute('placeholder');
          card.appendChild(line);
          card.appendChild(lab2);
          card.appendChild(grp);                // move into card
        }else{
          card.appendChild(line);
          card.appendChild(lab2);
        }
        stack.appendChild(card);
      });
    }

    // Put groups back into the i-th income row (no placeholders)
    function moveBackToGrid(){
      const rowsNow = Array.from(right.querySelectorAll('.wi-row')); // re-query in case DOM changed
      groups.forEach((grp, i) => {
        if (!grp) return;
        const row = rowsNow[i];
        if (row && grp.parentNode !== row){
          row.appendChild(grp);
        }
      });
    }

    function syncPeriodsIntoCards(){
      const spans = stack.querySelectorAll('.wi-mobile-period');
      periodRows.forEach((pRow, i) => { if (spans[i]) spans[i].textContent = text(pRow); });
    }

    // Watch period text changes
    const mo = new MutationObserver(syncPeriodsIntoCards);
    mo.observe(left, { characterData:true, subtree:true, childList:true });

    // Switcher
    const mq = window.matchMedia('(max-width: 680px)');
    const apply = () => {
      if (mq.matches){
        buildCards(); syncPeriodsIntoCards();
        grid.style.display = 'none';
        stack.hidden = false;
      } else {
        moveBackToGrid();
        stack.hidden = true;
        grid.style.display = '';
        stack.innerHTML = ''; // optional: free nodes
      }
    };
    mq.addEventListener ? mq.addEventListener('change', apply) : mq.addListener(apply);
    apply();
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.wi-grid').forEach(setupWiMobile);
  });
})();
</script>

<!-- YES WORLD 2 -->
<script>
(function(){
  // Target all WI inputs (works on desktop + when moved into mobile cards)
  const inputs = document.querySelectorAll('.wi-grid input.fi-input, .wi-stack input.fi-input');

  // Remove placeholders and float labels everywhere
  inputs.forEach(inp => inp.removeAttribute('placeholder'));
  document.querySelectorAll('.wi-grid .fi-float-label, .wi-stack .fi-float-label')
    .forEach(el => el.style.display = 'none');

  function formatMoney(str, pad2=true){
    str = (str || '').toString().replace(/[^\d.]/g,'');
    const firstDot = str.indexOf('.');
    if (firstDot !== -1){
      str = str.slice(0, firstDot + 1) + str.slice(firstDot + 1).replace(/\./g,'');
    }
    let [intPart, decPart=''] = str.split('.');
    intPart = intPart.replace(/^0+(?=\d)/, '');
    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    decPart = decPart.slice(0,2);
    if (pad2){ decPart = (decPart + '00').slice(0,2); }
    const out = decPart ? \`\${intPart}.\${decPart}\` : (pad2 ? \`\${intPart}.00\` : intPart);
    return out === '.00' ? '' : out;
  }

  inputs.forEach(inp=>{
    if (inp.value.trim()!==''){ inp.value = formatMoney(inp.value); }
    inp.addEventListener('input', ()=>{
      const start = inp.selectionStart;
      const before = inp.value;
      inp.value = formatMoney(before, false);
      const diff = inp.value.length - before.length;
      const caret = Math.max(0, (start ?? inp.value.length) + diff);
      inp.setSelectionRange(caret, caret);
    });
    inp.addEventListener('blur', ()=>{ inp.value = formatMoney(inp.value, true); });
    inp.addEventListener('keydown', (ev)=>{ if(ev.key==='Escape'){ inp.value=''; } });
  });

  // OPTIONAL submit validation (keep commented unless needed)
  /*
  document.querySelector('form')?.addEventListener('submit', (e)=>{
    let bad = 0;
    inputs.forEach(i=>{
      i.classList.toggle('is-error', !i.value.trim());
      if(!i.value.trim()) bad++;
    });
    if(bad){ e.preventDefault(); }
  });
  */
})();
</script>



<!-- YES WORLD 3 (optional; safe to keep or remove) -->
<script>
(function removeWiHintsForever(){
  const kill = () => document.querySelectorAll('.wi-grid .wi-hint, .wi-stack .wi-hint').forEach(n => n.remove());
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', kill);
  } else { kill(); }
  const mo = new MutationObserver(kill);
  mo.observe(document.body, { childList:true, subtree:true });
})();
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const show = (el) => { if (el){ el.classList.remove('is-hidden'); el.setAttribute('aria-hidden','false'); } };
  const hide = (el) => { if (el){ el.classList.add('is-hidden'); el.setAttribute('aria-hidden','true'); } };

  // Generic yes/no binder by radio "name"
  // Show/hide a block when a yes/no radio group changes.
  // Also toggles required fields inside that block so hidden inputs don't block navigation.
  function bindYesNo(name, targetId, yesValue = 'yes') {
    const target = document.getElementById(targetId);
    const radios = document.querySelectorAll(`input[name="${name}"]`);
    if (!radios.length || !target) return;

    const toggleRequired = (on) => {
      target.querySelectorAll('input,select,textarea').forEach(el => {
        const wasRequired = el.dataset.wasRequired === '1';
        // Remember original required state the first time we see it
        if (!el.dataset.seenRequired) {
          el.dataset.wasRequired = el.required ? '1' : '0';
          el.dataset.seenRequired = '1';
        }
        if (on) {
          if (wasRequired) el.required = true;
        } else {
          if (el.required) el.required = false;
          // Clear validation errors when field becomes non-required
          if (el.name) {
            if (typeof window.hideFieldError === 'function') {
              window.hideFieldError(el.name);
            } else if (typeof hideFieldError === 'function') {
              hideFieldError(el.name);
            }
          }
        }
      });
    };

    const sync = () => {
      const checked = [...radios].find(r => r.checked);
      const shouldShow = !!(checked && checked.value === yesValue);
      shouldShow ? show(target) : hide(target);
      toggleRequired(shouldShow);
    };

    radios.forEach(r => r.addEventListener('change', sync));
    sync(); // set initial state on load
  }

  // Your two follow-ups
  bindYesNo('moved_province', 'moved-section');        // shows "When did you move?" block
  bindYesNo('moving_expenses_claim', 'movexp-details'); // shows "Moving expenses details"

  /* -------- Branch toggles that preserve values but drop required when hidden -------- */
  function setReq(block, on){
    if (!block) return;
    block.querySelectorAll('input,select,textarea').forEach(el=>{
      if (!el.dataset.reqInit) el.dataset.reqInit = el.required ? '1' : '0';
      const was = el.dataset.reqInit === '1';
      el.required = on && was;
      
      // Clear validation errors when field becomes non-required
      if (!on && el.name) {
        // Try to call hideFieldError if it exists (may be in global scope or window)
        if (typeof window.hideFieldError === 'function') {
          window.hideFieldError(el.name);
        } else if (typeof hideFieldError === 'function') {
          hideFieldError(el.name);
        } else {
          // Fallback: manually remove error elements
          const errorText = document.getElementById(`error-${el.name}`);
          if (errorText) errorText.remove();
          const fieldElements = document.querySelectorAll(`[name="${el.name}"]`);
          fieldElements.forEach(field => {
            const container = field.closest('.fi-group') || 
                             field.closest('.qs-block') ||
                             field.closest('.yn-group');
            if (container) container.classList.remove('error');
          });
        }
      }
    });
  }
  function toggleBlock(block, on){
    if (!block) return;
    block.hidden = !on;
    block.classList.toggle('is-hidden', !on);
    block.setAttribute('aria-hidden', on ? 'false' : 'true');
    
    // Clear validation errors for all fields in this block when hidden
    if (!on) {
      block.querySelectorAll('input,select,textarea').forEach(el => {
        if (el.name) {
          if (typeof window.hideFieldError === 'function') {
            window.hideFieldError(el.name);
          } else if (typeof hideFieldError === 'function') {
            hideFieldError(el.name);
          }
        }
      });
    }
  }

  // Toggle helper for yes/no groups by name -> target id
  function bindToggleYesNo(name, targetId){
    const target = document.getElementById(targetId);
    if (!target) return;
    const radios = document.querySelectorAll(`input[name="${name}"]`);
    if (!radios.length) return;
    const sync = ()=>{
      const val = [...radios].find(r=>r.checked)?.value;
      const on = val === 'yes';
      toggleBlock(target, on);
      setReq(target, on);
    };
    radios.forEach(r=>r.addEventListener('change', sync));
    sync();
  }

  function applyFirstTime(){
    const v = document.querySelector('input[name="first_time"]:checked')?.value || '';
    const yes = v === 'yes', no = v === 'no';
    const prior = document.getElementById('prior-customer-section');
    const first = document.getElementById('firsttime-details');
    toggleBlock(prior, no);  setReq(prior, no);
    toggleBlock(first, yes); setReq(first, yes);
  }
  document.querySelectorAll('input[name="first_time"]').forEach(r=> r.addEventListener('change', applyFirstTime));
  applyFirstTime();

  function applySpouseFirstTime(){
    const v = document.querySelector('input[name="sp_first_time"]:checked')?.value || '';
    const yes = v === 'yes', no = v === 'no';
    const prior = document.getElementById('sp-prior-customer-section');
    const first = document.getElementById('sp-firsttime-details');
    toggleBlock(prior, no);  setReq(prior, no);
    toggleBlock(first, yes); setReq(first, yes);
  }
  document.querySelectorAll('input[name="sp_first_time"]').forEach(r=> r.addEventListener('change', applySpouseFirstTime));
  applySpouseFirstTime();

  // Other yes/no blocks that should drop required when hidden
  bindToggleYesNo('first_time_buyer', 'body_purchase_first_home');
  bindToggleYesNo('moved_province', 'moved-section');
  bindToggleYesNo('moving_expenses_claim', 'movexp-details');
  bindToggleYesNo('sp_gig_income', 'sp-gig-expenses-block');
  bindToggleYesNo('gig_income', 'gig-expenses-block');

  /* -------- Generic autocomplete helper -------- */
  function makeAutocomplete({ input, box, fetcher, minChars=3, debounceMs=700, limit=5, onSelect, instant=false }){
    if (!input || !box) return;
    const debug = window.AUTOCOMPLETE_DEBUG || false;
    let timer = null;
    let currentRequest = null; // Track ongoing requests to cancel stale ones

    const hide = () => {
      box.innerHTML = '';
      box.hidden = true;
      box.classList.remove('show');
    };

    let lastRenderedQuery = ''; // Track last query that was rendered
    
    const render = (items=[], query='') => {
      if (debug) console.log('makeAutocomplete: render called', { itemCount: items.length, query });
      
      // If query changed significantly, clear old results even in instant mode
      if (query && lastRenderedQuery && query.length < lastRenderedQuery.length - 2) {
        // User deleted significant characters, clear results
        if (debug) console.log('makeAutocomplete: Query shortened significantly, clearing results');
        hide();
        lastRenderedQuery = '';
        return;
      }
      
      if (!items.length) {
        if (debug) console.log('makeAutocomplete: No items to render');
        // Clear results if no items (don't keep stale results)
        hide();
        lastRenderedQuery = '';
        return;
      }
      
      lastRenderedQuery = query;
      box.innerHTML = '';
      items.slice(0, limit).forEach(item => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.innerHTML = item.sub
          ? `<strong>${item.label}</strong><span class="addr-sub">${item.sub}</span>`
          : `<strong>${item.label}</strong>`;
        btn.addEventListener('click', () => {
          onSelect?.(item);
          hide();
        });
        box.appendChild(btn);
      });
      box.hidden = false;
      box.style.display = 'block'; // Force display
      requestAnimationFrame(() => {
        box.classList.add('show');
        if (debug) {
          const computed = window.getComputedStyle(box);
          console.log('makeAutocomplete: Box visible', { 
            buttons: box.querySelectorAll('button').length,
            display: computed.display,
            opacity: computed.opacity
          });
        }
      });
    };

    input.addEventListener('input', () => {
      const q = input.value.trim();
      if (debug) console.log('makeAutocomplete: Input event', { q, length: q.length, minChars });
      if (timer) clearTimeout(timer);
      
      // Cancel any ongoing fetch request
      if (currentRequest && currentRequest.abort) {
        currentRequest.abort();
      }
      
      if (!q || q.length < minChars) {
        if (debug) console.log('makeAutocomplete: Query too short, hiding');
        hide(); // Always hide when query is too short, even in instant mode
        lastRenderedQuery = '';
        return;
      }
      
      // For instant mode, fetch immediately with minimal/no debounce
      const delay = instant ? 0 : debounceMs;
      if (debug) console.log('makeAutocomplete: Starting fetch', { q, delay });
      
      timer = setTimeout(async () => {
        try {
          // Create abort controller for this request
          const controller = new AbortController();
          currentRequest = controller;
          
          if (debug) console.log('makeAutocomplete: Calling fetcher', { q, limit });
          const items = await fetcher(q, { signal: controller.signal, limit });
          if (debug) console.log('makeAutocomplete: Fetcher returned', { itemCount: items.length });
          
          // Only render if this is still the current request
          if (currentRequest === controller) {
            if (debug) console.log('makeAutocomplete: Rendering items');
            render(items, q); // Pass the query so we can track changes
            currentRequest = null;
          }
        } catch (e) {
          // Ignore aborted requests
          if (e.name !== 'AbortError') {
            console.warn('Autocomplete fetch error:', e);
            if (!instant) hide();
          } else {
            if (debug) console.log('makeAutocomplete: Request aborted');
          }
        }
      }, delay);
    });

    input.addEventListener('blur', () => {
      // Only hide after a delay to allow clicks on suggestions
      setTimeout(() => {
        // Check if box still has content before hiding
        if (!box.querySelector('button')) {
          hide();
        }
      }, 200);
    });
  }

  /* -------- Country autocomplete (fast & snappy) -------- */
  (function countryAutocomplete(){
    const input = document.getElementById('birth_country');
    const box   = document.getElementById('birth-country-suggest');
    if (!input || !box) return;
    
    // Set max-height for exactly 5 rows (5 buttons * ~44px each = 220px, matches CSS)
    // Each button: 10px top + ~24px content + 10px bottom = ~44px
    box.style.maxHeight = '220px'; // Matches existing CSS, perfect for 5 rows
    
    const countries = [
      'Canada','United States','United Kingdom','Australia','India','Philippines','Pakistan','Bangladesh','Sri Lanka','China','Hong Kong','Taiwan','United Arab Emirates','Saudi Arabia','Qatar','Kuwait','Oman','France','Germany','Spain','Italy','Nigeria','Ghana','Kenya','South Africa','Mexico','Brazil','Japan','Singapore','Malaysia'
    ];
    makeAutocomplete({
      input,
      box,
      minChars:2,  // Only show suggestions after user types at least 2 characters (like "in" for "India")
      debounceMs:150,  // Very fast response - 150ms debounce for snappy feel (reduced from 3000ms)
      limit:5,  // Limit to 5 rows as requested
      fetcher: async (q)=>{  // Fast synchronous filtering wrapped in async for compatibility
        const re = new RegExp(q.replace(/[.*+?^${}()|[\]\\]/g,'\\$&'), 'i');
        return countries.filter(c=>re.test(c)).map(c=>({ label:c, value:c }));
      },
      onSelect:(item)=>{ input.value = item.value || item.label; }
    });
  })();

  /* -------- Global Nominatim Configuration & Shared Fetcher -------- */
  // Set to true to enable verbose console logging for autocomplete debugging
  window.AUTOCOMPLETE_DEBUG = false;
  
  // Global settings - change in one place for all Nominatim fields
  window.NominatimConfig = {
    minChars: 3,           // Minimum characters before searching
    debounceMs: 300,       // 300ms debounce to respect rate limits
    limit: 5,              // Number of suggestions to show (matches dropdown height)
    instant: false,        // Normal behavior: hide suggestions on blur
    minRequestInterval: 1000  // Min 1 second between requests (Nominatim policy)
  };

  // Global rate limiting state (shared across all Nominatim fields)
  window.NominatimRateLimit = {
    lastRequestTime: 0,
    requestQueue: []
  };

  // Normalize Canadian province names to standard abbreviations
  window.normalizeProvince = function(provinceName) {
    if (!provinceName) return '';
    
    const provinceMap = {
      'ontario': 'ON',
      'quebec': 'QC',
      'québec': 'QC',
      'british columbia': 'BC',
      'alberta': 'AB',
      'manitoba': 'MB',
      'saskatchewan': 'SK',
      'nova scotia': 'NS',
      'new brunswick': 'NB',
      'newfoundland and labrador': 'NL',
      'prince edward island': 'PE',
      'northwest territories': 'NT',
      'yukon': 'YT',
      'nunavut': 'NU'
    };
    
    const lower = provinceName.toLowerCase().trim();
    
    // Return mapped abbreviation if found
    if (provinceMap[lower]) {
      return provinceMap[lower];
    }
    
    // If already an abbreviation (2 uppercase letters), return as-is
    if (/^[A-Z]{2}$/.test(provinceName.trim())) {
      return provinceName.trim();
    }
    
    // Otherwise return the original value
    return provinceName;
  };

  // Wait function to respect Nominatim rate limits (max 1 request per second)
  window.waitForNominatimRateLimit = async function() {
    const state = window.NominatimRateLimit;
    const now = Date.now();
    const timeSinceLastRequest = now - state.lastRequestTime;
    const minInterval = window.NominatimConfig.minRequestInterval;
    
    if (timeSinceLastRequest < minInterval) {
      const waitTime = minInterval - timeSinceLastRequest;
      await new Promise(resolve => setTimeout(resolve, waitTime));
    }
    
    state.lastRequestTime = Date.now();
  };

  // Global shared fetcher function (used by all Nominatim fields - unified for mobile & desktop)
  window.createNominatimFetcher = function() {
    const config = window.NominatimConfig;
    const debug = window.AUTOCOMPLETE_DEBUG || false;
    
    return async function(q, options = {}) {
      const limit = options.limit || config.limit;
      
      try {
        // Respect rate limiting - even via proxy
        await window.waitForNominatimRateLimit();
        
        if (debug) console.log('Nominatim fetcher: Making request via proxy', { q, limit });
          
        // Call local PHP proxy to avoid CORS and centralize Nominatim usage
        const url = `/nominatim-proxy.php?q=${encodeURIComponent(q)}&limit=${encodeURIComponent(limit)}`;
        
        const res = await fetch(url, { 
          method: 'GET',
          headers: { 
            'Accept': 'application/json',
            'Accept-Language': 'en-CA,en;q=0.9'
          },
          credentials: 'same-origin',
          signal: options.signal
        });
        
        if (!res.ok) {
          if (debug) console.error('Nominatim request failed:', { status: res.status, statusText: res.statusText });
          
          // Handle 429 (Too Many Requests) or 403 (Forbidden)
          if (res.status === 429 || res.status === 403) {
            if (debug) console.warn('Nominatim: Rate limit or access issue, waiting before retry');
            // Wait extra time before next request
            window.NominatimRateLimit.lastRequestTime = Date.now() + 2000;
          }
          
          return [];
        }
        
        const data = await res.json();
        
        if (!Array.isArray(data)) {
          if (debug) console.error('Nominatim: Invalid response format');
          return [];
        }
        
        // Transform to our format
        const results = data.map(item => {
          const addr = item.address || {};
          
          // Build street
          const streetParts = [];
          if (addr.house_number) streetParts.push(addr.house_number);
          if (addr.road) streetParts.push(addr.road);
          if (addr.unit) streetParts.push(addr.unit);
          
          let street = streetParts.join(' ').trim();
          if (!street && item.display_name) {
            street = item.display_name.split(',')[0].trim();
          }
          
          // Get city
          const city = addr.city || addr.town || addr.village || addr.municipality || '';
          
          // Get province/state
          const state = addr.state || addr.province || '';
          
          // Get postal code
          const postal = addr.postcode || '';
          
          // Build label
          const labelParts = [];
          if (street) labelParts.push(street);
          if (city) labelParts.push(city);
          if (state) labelParts.push(state);
          if (postal) labelParts.push(postal);
          
          const label = labelParts.length > 0 ? labelParts.join(', ') : item.display_name;
          
          return {
            label,
            street,
            city,
            state,
            postal,
            country: 'Canada',
            lat: item.lat || null,
            lon: item.lon || null
          };
        });
        
        if (debug) console.log('Nominatim: Received results', { count: results.length });
        return results;
        
      } catch (e) {
        if (e.name === 'AbortError') throw e;
        if (debug) console.error('Nominatim fetch error:', e);
        return [];
      }
    };
  };

  // Calculate distance between two coordinates using Haversine formula (returns kilometers)
  window.calculateDistance = function(lat1, lon1, lat2, lon2) {
    if (!lat1 || !lon1 || !lat2 || !lon2) return null;
    
    const R = 6371; // Earth's radius in kilometers
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c; // Distance in kilometers
    
    return Math.round(distance * 10) / 10; // Round to 1 decimal place
  };

  // Reusable function to initialize Nominatim autocomplete for any address field
  window.initNominatimField = function(config) {
    const {
      inputId,           // ID of the input field
      boxId,             // ID of the autocomplete dropdown container
      fieldMapping = null, // Object mapping: {street, city, province, postal, country} - null for single field
      onSelect = null,   // Custom onSelect handler (optional)
      storeCoordinates = false // If true, store coordinates in data attribute for distance calculation
    } = config;
    
    const input = document.getElementById(inputId);
    const box = document.getElementById(boxId);
    
    if (!input || !box) {
      return false;
    }
    
    const nominatimConfig = window.NominatimConfig;
    const fetcher = window.createNominatimFetcher();
    
    // Default onSelect handler
    const defaultOnSelect = (item) => {
      if (fieldMapping) {
        // Multi-field mapping (e.g., street, city, province, postal)
        if (fieldMapping.street) {
          const streetField = document.getElementById(fieldMapping.street);
          if (streetField) {
            streetField.value = item.street || '';
            // Store coordinates if available
            if (storeCoordinates && item.lat && item.lon) {
              streetField.dataset.lat = item.lat;
              streetField.dataset.lon = item.lon;
            }
          }
        }
        if (fieldMapping.city) {
          const cityField = document.getElementById(fieldMapping.city);
          if (cityField) cityField.value = item.city || '';
        }
        if (fieldMapping.province) {
          const provinceField = document.getElementById(fieldMapping.province);
          if (provinceField) provinceField.value = window.normalizeProvince(item.state) || '';
        }
        if (fieldMapping.postal) {
          const postalField = document.getElementById(fieldMapping.postal);
          if (postalField) postalField.value = item.postal || '';
        }
        if (fieldMapping.country) {
          const countryField = document.getElementById(fieldMapping.country);
          if (countryField) countryField.value = item.country || 'Canada';
        }
      } else {
        // Single field - just set the complete address
        input.value = item.label || item.street || '';
        // Store coordinates if available
        if (storeCoordinates && item.lat && item.lon) {
          input.dataset.lat = item.lat;
          input.dataset.lon = item.lon;
        }
      }
    };
    
    makeAutocomplete({
      input,
      box,
      minChars: nominatimConfig.minChars,
      debounceMs: nominatimConfig.debounceMs,
      limit: nominatimConfig.limit,
      instant: nominatimConfig.instant,
      fetcher,
      onSelect: onSelect || defaultOnSelect
    });
    
    return true;
  };

  /* -------- Initialize Main Address Field (Personal Information) -------- */
  function initMainAddressAutocomplete() {
    const street   = document.getElementById('street');
    const box      = document.getElementById('street-autocomplete');
    
    if (!street || !box) {
      return false;
    }
    
    window.initNominatimField({
      inputId: 'street',
      boxId: 'street-autocomplete',
      fieldMapping: {
        street: 'street',
        city: 'city',
        province: 'province',
        postal: 'postal',
        country: 'country'
      },
      storeCoordinates: true, // Store coordinates for distance calculation
      onSelect: (item) => {
        // Populate all address fields
        const streetField = document.getElementById('street');
        const cityField = document.getElementById('city');
        const provinceField = document.getElementById('province');
        const postalField = document.getElementById('postal');
        const countryField = document.getElementById('country');
        
        if (streetField) {
          streetField.value = item.street || '';
          // Store coordinates for distance calculation
          if (item.lat && item.lon) {
            streetField.dataset.lat = item.lat;
            streetField.dataset.lon = item.lon;
          }
        }
        if (cityField) cityField.value = item.city || '';
        if (provinceField) provinceField.value = window.normalizeProvince(item.state) || '';
        if (postalField) postalField.value = item.postal || '';
        if (countryField) countryField.value = item.country || 'Canada';
        
        // Trigger distance calculation after a short delay
        if (typeof calculateMovingDistance === 'function') {
          setTimeout(calculateMovingDistance, 200);
        }
      }
    });
    
    return true;
  }

  // Try to initialize immediately
  // Initialize all Nominatim address fields
  function initAllNominatimFields() {
    // Main address (Personal Information)
    initMainAddressAutocomplete();
    
    // Previous Address (Moving Expenses) - single field
    window.initNominatimField({
      inputId: 'moving_prev_address',
      boxId: 'moving-prev-address-autocomplete',
      fieldMapping: null, // Single field - just populate the input
      storeCoordinates: true, // Store coordinates for distance calculation
      onSelect: (item) => {
        const input = document.getElementById('moving_prev_address');
        if (input) {
          input.value = item.label || item.street || '';
          // Store coordinates
          if (item.lat && item.lon) {
            input.dataset.lat = item.lat;
            input.dataset.lon = item.lon;
          }
          // Calculate distance if current address coordinates are available
          if (typeof calculateMovingDistance === 'function') {
            setTimeout(calculateMovingDistance, 100);
          }
        }
      }
    });
    
    // Spouse Street Address - multi-field
    window.initNominatimField({
      inputId: 'spouse_street',
      boxId: 'spouse-street-autocomplete',
      fieldMapping: {
        street: 'spouse_street',
        city: 'spouse_city',
        province: 'spouse_province',
        postal: 'spouse_postal',
        country: 'spouse_country'
      }
    });
    
    // Property Address (in modal) - single field
    window.initNominatimField({
      inputId: 'prop_address',
      boxId: 'prop-address-autocomplete',
      fieldMapping: null // Single field
    });
    
    // Rent Addresses - initialize when rent address input fields are created
    initRentAddressAutocomplete();
    
    // Set up automatic distance calculation for moving expenses
    setupMovingDistanceCalculation();
  }
  
  // Calculate distance between two coordinates using Haversine formula (returns kilometers)
  window.calculateDistance = function(lat1, lon1, lat2, lon2) {
    if (!lat1 || !lon1 || !lat2 || !lon2) return null;
    
    const R = 6371; // Earth's radius in kilometers (metric - Canadian standard)
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
      Math.sin(dLat / 2) * Math.sin(dLat / 2) +
      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
      Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    const distance = R * c; // Distance in kilometers
    
    return Math.round(distance * 10) / 10; // Round to 1 decimal place
  };
  
  // Calculate and display distance between previous and current address
  function calculateMovingDistance() {
    const distanceField = document.getElementById('moving_distance');
    const hintField = document.getElementById('moving-distance-hint');
    if (!distanceField) {
      console.log('calculateMovingDistance: Distance field not found');
      return;
    }
    
    const currentStreet = document.getElementById('street');
    const prevAddress = document.getElementById('moving_prev_address');
    
    if (!currentStreet) {
      console.log('calculateMovingDistance: Current street field not found');
      return;
    }
    if (!prevAddress) {
      console.log('calculateMovingDistance: Previous address field not found');
      return;
    }
    
    const currentLat = parseFloat(currentStreet.dataset.lat);
    const currentLon = parseFloat(currentStreet.dataset.lon);
    const prevLat = parseFloat(prevAddress.dataset.lat);
    const prevLon = parseFloat(prevAddress.dataset.lon);
    
    console.log('calculateMovingDistance: Checking coordinates', {
      currentLat,
      currentLon,
      prevLat,
      prevLon,
      currentStreetHasData: currentStreet.dataset.lat !== undefined,
      prevAddressHasData: prevAddress.dataset.lat !== undefined
    });
    
    // Check if both addresses have coordinates
    if (currentLat && currentLon && prevLat && prevLon) {
      const distance = window.calculateDistance(currentLat, currentLon, prevLat, prevLon);
      if (distance !== null && !isNaN(distance)) {
        // Format number with commas and add "km" unit
        const formattedDistance = distance.toLocaleString('en-CA', { 
          minimumFractionDigits: 1, 
          maximumFractionDigits: 1 
        });
        distanceField.value = `${formattedDistance} km`;
        if (hintField) {
          hintField.textContent = 'Auto-calculated using straight-line distance';
          hintField.style.display = 'block';
        }
        console.log('Moving distance calculated:', distance, 'km');
      } else {
        console.log('calculateMovingDistance: Distance calculation returned null or NaN');
      }
    } else {
      console.log('calculateMovingDistance: Missing coordinates', {
        hasCurrent: !!(currentLat && currentLon),
        hasPrev: !!(prevLat && prevLon)
      });
      // Clear hint if coordinates not available
      if (hintField) {
        hintField.style.display = 'none';
      }
    }
  }
  
  // Set up listeners for automatic distance calculation
  function setupMovingDistanceCalculation() {
    // Listen for changes to current address (when coordinates are stored)
    const currentStreetField = document.getElementById('street');
    if (currentStreetField) {
      const observer = new MutationObserver(() => {
        calculateMovingDistance();
      });
      observer.observe(currentStreetField, { attributes: true, attributeFilter: ['data-lat', 'data-lon'] });
      
      // Also listen for input changes (in case user types manually)
      currentStreetField.addEventListener('blur', () => {
        setTimeout(calculateMovingDistance, 500); // Small delay to allow autocomplete to finish
      });
    }
    
    // Listen for changes to previous address
    const prevAddressField = document.getElementById('moving_prev_address');
    if (prevAddressField) {
      const observer = new MutationObserver(() => {
        calculateMovingDistance();
      });
      observer.observe(prevAddressField, { attributes: true, attributeFilter: ['data-lat', 'data-lon'] });
      
      prevAddressField.addEventListener('blur', () => {
        setTimeout(calculateMovingDistance, 500);
      });
    }
  }
  
  // Initialize autocomplete for rent address fields (called when rows are created)
  function initRentAddressAutocomplete() {
    // Find all rent address input fields in the table
    const rentAddrInputs = document.querySelectorAll('.rent-addr');
    rentAddrInputs.forEach((input, index) => {
      // Skip if already initialized
      if (input.dataset.nominatimInitialized === 'true') return;
      
      // Create unique box ID for this input
      const boxId = `rent-addr-autocomplete-${Date.now()}-${index}`;
      
      // Create autocomplete container if it doesn't exist
      let box = document.getElementById(boxId);
      if (!box) {
        box = document.createElement('div');
        box.id = boxId;
        box.className = 'addr-suggest';
        box.hidden = true;
        // Insert after the input's parent container
        const container = input.closest('td') || input.parentElement;
        if (container) {
          container.style.position = 'relative'; // Ensure positioning context
          container.appendChild(box);
        }
      }
      
      // Ensure input has an ID
      if (!input.id) {
        input.id = `rent-addr-${Date.now()}-${index}`;
      }
      
      // Initialize autocomplete for this field
      window.initNominatimField({
        inputId: input.id,
        boxId: boxId,
        fieldMapping: null // Single field - just populate the address
      });
      
      // Mark as initialized
      input.dataset.nominatimInitialized = 'true';
    });
  }
  
  // Try to initialize immediately
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAllNominatimFields);
  } else {
    initAllNominatimFields();
  }
  
  // Also try when panels are shown (in case elements weren't ready)
  if (window.App && window.App.showPanel) {
    const originalShowPanel = window.App.showPanel;
    window.App.showPanel = function(panelId) {
      originalShowPanel.call(this, panelId);
      // Re-initialize fields when relevant panels are shown
      setTimeout(() => {
        if (panelId === 'personal' || panelId === 'spouse' || panelId === 'other') {
          initAllNominatimFields();
        }
      }, 100);
    };
  }
});
</script>





<script>
(function(){
  document.querySelectorAll('.ms-select-wrap').forEach((wrap)=>{
    const native = wrap.querySelector('select.ms-select');

    // Build button
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'ms-select-btn';
    btn.setAttribute('aria-haspopup','listbox');
    btn.setAttribute('aria-expanded','false');
    btn.innerHTML = `<span class="ms-label">${native.selectedOptions[0]?.text || 'Select'}</span><span class="ms-caret" aria-hidden="true"></span>`;
    wrap.appendChild(btn);

    // Build listbox
    const list = document.createElement('ul');
    list.className = 'ms-options';
    list.setAttribute('role','listbox');
    wrap.appendChild(list);

    // Fill options from native select
    [...native.options].forEach(opt=>{
      if (opt.disabled && !opt.value) return; // skip placeholder
      const li = document.createElement('li');
      li.className = 'ms-option';
      li.setAttribute('role','option');
      li.tabIndex = -1;
      li.dataset.value = opt.value;
      li.textContent = opt.textContent;
      if (opt.selected) li.setAttribute('aria-selected','true');
      list.appendChild(li);
    });

    const open = ()=>{ wrap.classList.add('open'); btn.setAttribute('aria-expanded','true'); list.focus(); };
    const close = ()=>{ wrap.classList.remove('open'); btn.setAttribute('aria-expanded','false'); };

    btn.addEventListener('click', ()=> wrap.classList.contains('open') ? close() : open());
    document.addEventListener('click', (e)=>{ if(!wrap.contains(e.target)) close(); });

    list.addEventListener('click', (e)=>{
      const item = e.target.closest('.ms-option'); if(!item) return;
      // sync to native select
      native.value = item.dataset.value;
      native.dispatchEvent(new Event('change', {bubbles:true}));
      // update UI
      list.querySelectorAll('.ms-option[aria-selected="true"]').forEach(n=>n.removeAttribute('aria-selected'));
      item.setAttribute('aria-selected','true');
      btn.querySelector('.ms-label').textContent = item.textContent;
      close();
    });

    // Keyboard nav
    list.addEventListener('keydown', (e)=>{
      const items = [...list.querySelectorAll('.ms-option')];
      let i = items.indexOf(document.activeElement);
      if(e.key==='ArrowDown'){ e.preventDefault(); (items[i+1]||items[0]).focus(); }
      if(e.key==='ArrowUp'){ e.preventDefault(); (items[i-1]||items[items.length-1]).focus(); }
      if(e.key==='Enter'){ e.preventDefault(); document.activeElement.click(); }
      if(e.key==='Escape'){ e.preventDefault(); close(); btn.focus(); }
    });
  });
})();
</script>

<script>
(function(){
  function buildCustomSelect(wrap){
    const native = wrap.querySelector('.xsel-native');
    const placeholder = native.dataset.placeholder || 'Select';

    // Button
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'xsel-btn';
    btn.setAttribute('aria-haspopup','listbox');
    btn.setAttribute('aria-expanded','false');
    btn.innerHTML = `<span class="xsel-value"></span><span class="xsel-caret" aria-hidden="true"></span>`;
    wrap.appendChild(btn);

    // Listbox
    const list = document.createElement('ul');
    list.className = 'xsel-list';
    list.setAttribute('role','listbox');
    list.tabIndex = -1;
    wrap.appendChild(list);

    // Fill items from native <option>
    [...native.options].forEach(opt=>{
      if (!opt.value) return; // skip placeholder/empty
      const li = document.createElement('li');
      li.className = 'xsel-item';
      li.setAttribute('role','option');
      li.dataset.value = opt.value;
      li.textContent = opt.textContent;
      if (opt.selected) li.setAttribute('aria-selected','true');
      list.appendChild(li);
    });

    function currentLabel(){
      const idx = native.selectedIndex;
      if (idx > -1 && native.options[idx].value) return native.options[idx].textContent.trim();
      return placeholder;
    }

    function open(){ wrap.classList.add('xsel-open'); btn.setAttribute('aria-expanded','true'); }
    function close(){ wrap.classList.remove('xsel-open'); btn.setAttribute('aria-expanded','false'); }

    function setSelection(value, labelText){
      native.value = value;
      // keep any existing listeners happy
      native.dispatchEvent(new Event('change', {bubbles:true}));
      list.querySelectorAll('.xsel-item[aria-selected="true"]').forEach(n=>n.removeAttribute('aria-selected'));
      const selectedItem = [...list.children].find(li=>li.dataset.value===value);
      if (selectedItem) selectedItem.setAttribute('aria-selected','true');
      btn.querySelector('.xsel-value').textContent = labelText || currentLabel();
    }

    // Initialize label
    btn.querySelector('.xsel-value').textContent = currentLabel();

    // Events
    btn.addEventListener('click', ()=> wrap.classList.contains('xsel-open') ? close() : open());
    document.addEventListener('click', (e)=>{ if(!wrap.contains(e.target)) close(); });

    list.addEventListener('click', (e)=>{
      const item = e.target.closest('.xsel-item'); if(!item) return;
      setSelection(item.dataset.value, item.textContent);
      close(); btn.focus();
    });

    // Keyboard support on the list
    list.addEventListener('keydown', (e)=>{
      const items = [...list.querySelectorAll('.xsel-item')];
      const active = document.activeElement.closest('.xsel-item');
      let i = items.indexOf(active);
      if(e.key==='ArrowDown'){ e.preventDefault(); (items[i+1]||items[0]).focus(); }
      if(e.key==='ArrowUp'){ e.preventDefault(); (items[i-1]||items[items.length-1]).focus(); }
      if(e.key==='Enter'){ e.preventDefault(); active && active.click(); }
      if(e.key==='Escape'){ e.preventDefault(); close(); btn.focus(); }
    });

    // Open and focus first item on button keyboard
    btn.addEventListener('keydown', (e)=>{
      if(e.key==='ArrowDown' || e.key==='Enter' || e.key===' '){
        e.preventDefault(); open();
        const first = list.querySelector('.xsel-item'); first && first.focus();
      }
    });

    // Sync if someone changes the native select externally
    native.addEventListener('change', ()=> btn.querySelector('.xsel-value').textContent = currentLabel());
  }

  document.addEventListener('DOMContentLoaded', ()=>{
    document.querySelectorAll('.xsel-wrap').forEach(buildCustomSelect);
  });
})();
</script>


<script>
  (function(){
    const toggle = document.getElementById('docx-toggle');
    const panel  = document.getElementById('docx-panel');
    const close  = document.getElementById('docx-close');

    function setOpen(open){
      toggle.setAttribute('aria-expanded', String(open));
      panel.hidden = !open;
    }
    toggle?.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    close?.addEventListener('click', () => setOpen(false));
  })();
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.doc-box').forEach(box => {
    const link  = box.querySelector('.doc-link');
    const panel = box.querySelector('.doc-panel');
    const close = box.querySelector('.doc-closebtn');

    const setOpen = (open) => {
      link.setAttribute('aria-expanded', String(open));
      panel.hidden = !open;
    };

    link?.addEventListener('click', () => setOpen(link.getAttribute('aria-expanded') !== 'true'));
    close?.addEventListener('click', () => setOpen(false));
  });
});

document.getElementById('prop-close')?.addEventListener('click', () => {
  const m = document.getElementById('prop-modal');
  if (m){ m.hidden = true; m.style.display = 'none'; }
});


</script>



<!-- YES RENT 2 -->
<script>
document.addEventListener('DOMContentLoaded', function(){
  // --- Radios / blocks ---
  const onYes      = document.getElementById('onrent_yes');
  const onNo       = document.getElementById('onrent_no');

  const claimBlock = document.getElementById('claim-rent-block');
  const claimYesEl = document.getElementById('claimrent_yes');
  const claimNoEl  = document.getElementById('claimrent_no');

  const rentUI     = document.getElementById('rent-addresses');

  // --- Table bits (same as your original) ---
  const tbody   = document.getElementById('rent-tbody');
  const addTop  = document.getElementById('rent-add-wrap-top');
  const addBot  = document.getElementById('rent-add-wrap-bottom');
  const addBtnT = document.getElementById('rent-add-btn');
  const addBtnB = document.getElementById('rent-add-btn-bottom');
  const hidden  = document.getElementById('rent-hidden-inputs');
  const dl      = document.getElementById('rent-addr-suggest');
  let seq = 0;

  const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const pad2 = n => String(n).padStart(2,'0');
  const esc = s => (s??'').replace(/[&<>"']/g,c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;', "'":'&#39;' }[c]));
  const money = s => {
    if (s==null || s==='') return '';
    const n = parseFloat(String(s).replace(/,/g,''));
    return isFinite(n)?n.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2}):s;
  };

  // Disable/enable hidden inputs so data doesn't submit when benefit is OFF
  function setHiddenDisabled(disabled){
    hidden.querySelectorAll('input').forEach(i => i.disabled = !!disabled);
  }

  // Unified visibility logic
  function syncVisibility(){
    const onRent   = !!onYes?.checked;
    const claimYes = !!claimYesEl?.checked;

    // Show "claim rent benefit" block only when onRent = yes
    if (claimBlock) claimBlock.style.display = onRent ? '' : 'none';

    // Show rent table only when onRent = yes AND claim = yes
    if (rentUI) rentUI.style.display = (onRent && claimYes) ? '' : 'none';

    // Control submission of hidden inputs
    setHiddenDisabled(!(onRent && claimYes));
  }

  onYes?.addEventListener('change', syncVisibility);
  onNo ?.addEventListener('change', syncVisibility);
  claimYesEl?.addEventListener('change', syncVisibility);
  claimNoEl ?.addEventListener('change', syncVisibility);
  syncVisibility(); // init

  // --------- Table helpers (unchanged logic) ----------
  function monthSelect(val){
    const MONTHS_LOCAL = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]; // Safe fix: ensure MONTHS is in scope
    let h = '<select class="fi-input rent-mm" aria-label="Month">';
    for (let i=1;i<=12;i++){ 
      const v=pad2(i); 
      const selected = (v===val) ? 'selected' : '';
      h += '<option value="' + v + '" ' + selected + '>' + MONTHS_LOCAL[i-1] + '</option>'; 
    }
    return h + '</select>';
  }
  function yearSelect(val){
    const cy=new Date().getFullYear(), start=cy-15, end=cy+1;
    let h = '<select class="fi-input rent-yy" aria-label="Year"><option value="" disabled '+(val?'':'selected')+'>Year</option>';
    for (let y=start;y<=end;y++){ h+=`<option value="${y}" ${String(y)===String(val)?'selected':''}>${y}</option>`; }
    return h + '</select>';
  }

  function refreshEmptyState(){
    const hasRows = tbody.querySelectorAll('tr.data-row').length > 0 || tbody.querySelector('tr.editing') !== null;

    // Remove ANY existing empty rows
    tbody.querySelectorAll('#rent-empty-row, .rent-empty-row').forEach(el=>el.remove());

    if (!hasRows){
      const tr = document.createElement('tr');
      tr.className = 'rent-empty-row';
      tr.innerHTML = '<td colspan="5" style="padding:10px; opacity:.7;">No addresses added yet.</td>';
      tbody.prepend(tr);
      if (addTop) addTop.style.display = 'flex';
      if (addBot) addBot.style.display = 'none';
    } else {
      if (addTop) addTop.style.display = 'none';
      if (addBot) addBot.style.display = 'flex';
    }
  }

  function saveHidden(key, data){
    hidden.querySelector(`.rent-hidden[data-key="${key}"]`)?.remove();
    const block = document.createElement('div');
    block.className = 'rent-hidden';
    block.dataset.key = key;
    block.innerHTML = `
      <input type="hidden" name="rent[${key}][address]" value="${esc(data.address)}">
      <input type="hidden" name="rent[${key}][from_month]" value="${data.fm}">
      <input type="hidden" name="rent[${key}][from_year]"  value="${data.fy}">
      <input type="hidden" name="rent[${key}][to_month]"   value="${data.tm}">
      <input type="hidden" name="rent[${key}][to_year]"    value="${data.ty}">
      <input type="hidden" name="rent[${key}][total]"      value="${data.total}">
    `;
    hidden.appendChild(block);
  }

function renderViewRow(key, data){
  tbody.querySelectorAll('#rent-empty-row, .rent-empty-row').forEach(el=>el.remove());

  const tr = document.createElement('tr');
  tr.className = 'data-row';
  tr.dataset.key = key;

  tr.innerHTML = `
    <td style="padding:8px;">${esc(data.address)}</td>
    <td style="padding:8px;">${MONTHS[+data.fm-1]} ${data.fy}</td>
    <td style="padding:8px;">${MONTHS[+data.tm-1]} ${data.ty}</td>

    <!-- Keep TOTAL as editable input even after saving -->
    <td style="padding:8px;">
      <div class="fi-group">
        <input type="text" class="fi-input rent-total" data-key="${key}" inputmode="decimal" value="${esc(money(data.total))}">
      </div>
    </td>

    <td style="padding:8px; text-align:center;">
      <a href="#" class="link-btn rent-edit">Edit</a>
      <a href="#" class="link-btn rent-del danger">Delete</a>
    </td>
  `;
  tbody.appendChild(tr);
  saveHidden(key, data);        // ensures hidden fields exist / update
  refreshEmptyState();
}

  function renderEditRow(key, data={}){
    const tr = document.createElement('tr');
    tr.className = 'data-row editing';
    tr.dataset.key = key;
    tr.innerHTML = `
      <td style="padding:8px; position:relative;">
        <input type="text" id="rent-addr-${key}" class="fi-input rent-addr"
               placeholder="Rent Address" value="${esc(data.address||'')}">
        <div id="rent-addr-autocomplete-${key}" class="addr-suggest" hidden></div>
      </td>
      <td style="padding:8px;">${monthSelect(data.fm||'01')} ${yearSelect(data.fy||'')}</td>
      <td style="padding:8px;">${monthSelect(data.tm||'01')} ${yearSelect(data.ty||'')}</td>
      <td style="padding:8px;">
        <input type="text" class="fi-input rent-total" placeholder="0.00" inputmode="decimal"
               value="${esc(data.total||'')}">
      </td>
      <td style="padding:8px; text-align:center;">
        <a href="#" class="link-btn rent-save">Save</a>
        <a href="#" class="link-btn rent-cancel">Cancel</a>
      </td>
    `;
    tbody.appendChild(tr);
    refreshEmptyState();

    // Initialize autocomplete for this rent address field
    const rentInput = tr.querySelector('.rent-addr');
    const rentBox = tr.querySelector(`#rent-addr-autocomplete-${key}`);
    if (rentInput && rentBox && typeof window.initNominatimField === 'function') {
      window.initNominatimField({
        inputId: `rent-addr-${key}`,
        boxId: `rent-addr-autocomplete-${key}`,
        fieldMapping: null // Single field
      });
    }

    tr.querySelector('.rent-save').addEventListener('click', (e) => {
      e.preventDefault();
      const addr = tr.querySelector('.rent-addr').value.trim();
      const fm   = tr.querySelectorAll('.rent-mm')[0].value;
      const fy   = tr.querySelectorAll('.rent-yy')[0].value;
      const tm   = tr.querySelectorAll('.rent-mm')[1].value;
      const ty   = tr.querySelectorAll('.rent-yy')[1].value;
      const tot  = tr.querySelector('.rent-total').value.trim();
      if (!addr || !fm || !fy || !tm || !ty || !tot) { alert('Please complete all fields.'); return; }
      tr.remove();
      renderViewRow(key, { address:addr, fm, fy, tm, ty, total:tot });
    });

    tr.querySelector('.rent-cancel').addEventListener('click', (e) => {
      e.preventDefault();
      const saved = hidden.querySelector(`.rent-hidden[data-key="${key}"]`);
      tr.remove();
      if (saved){
        renderViewRow(key, {
          address: saved.querySelector(`[name="rent[${key}][address]"]`).value,
          fm     : saved.querySelector(`[name="rent[${key}][from_month]"]`).value,
          fy     : saved.querySelector(`[name="rent[${key}][from_year]"]`).value,
          tm     : saved.querySelector(`[name="rent[${key}][to_month]"]`).value,
          ty     : saved.querySelector(`[name="rent[${key}][to_year]"]`).value,
          total  : saved.querySelector(`[name="rent[${key}][total]"]`).value
        });
      }
      refreshEmptyState();
    });
  }

  // table actions
  tbody.addEventListener('click', (e) => {
    const a = e.target.closest('.rent-edit, .rent-del');
    if (!a) return;
    e.preventDefault();
    const tr  = e.target.closest('tr.data-row');
    const key = tr.dataset.key;

    if (a.classList.contains('rent-edit')) {
      const saved = hidden.querySelector(`.rent-hidden[data-key="${key}"]`);
      const data = {
        address: saved.querySelector(`[name="rent[${key}][address]"]`).value,
        fm:      saved.querySelector(`[name="rent[${key}][from_month]"]`).value,
        fy:      saved.querySelector(`[name="rent[${key}][from_year]"]`).value,
        tm:      saved.querySelector(`[name="rent[${key}][to_month]"]`).value,
        ty:      saved.querySelector(`[name="rent[${key}][to_year]"]`).value,
        total:   saved.querySelector(`[name="rent[${key}][total]"]`).value
      };
      tr.remove();
      renderEditRow(key, data);
    } else if (a.classList.contains('rent-del')) {
      tr.remove();
      hidden.querySelector(`.rent-hidden[data-key="${key}"]`)?.remove();
      refreshEmptyState();
    }
  });

  function addNew(){ 
    renderEditRow(++seq, {}); 
  }
  addBtnT?.addEventListener('click', addNew);
  addBtnB?.addEventListener('click', addNew);

  // seed (optional)
  try {
    const seed = JSON.parse(document.getElementById('rent-seed')?.textContent || '[]');
    seed.forEach(item => {
      const key = (item.key ? +item.key : ++seq);
      seq = Math.max(seq, key);
      renderViewRow(key, {
        address: item.address || '',
        fm: pad2(item.from_month || item.fm || 1),
        fy: item.from_year  || item.fy || '',
        tm: pad2(item.to_month   || item.tm || 1),
        ty: item.to_year    || item.ty || '',
        total: item.total || ''
      });
    });
  } catch(_) {}

  refreshEmptyState();
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const inCAyes = document.getElementById('spouse_in_canada_yes');
  const inCAno  = document.getElementById('spouse_in_canada_no');
  const fileYes = document.getElementById('spouse_yes');
  const fileNo  = document.getElementById('spouse_no');

  const secRemaining = document.getElementById('spouse-remaining');
  const secIncome    = document.getElementById('spouse-income');
  const secForeign   = document.getElementById('spouse-foreign-income');

  // NEW: the <h2> title for income (fallback to previous sibling of the grid if id not present)
  const secIncomeTitle =
    document.getElementById('spouse-income-title') ||
    secIncome?.closest('.fi-grid')?.previousElementSibling;

  function flip(section, on) {
    if (!section) return;
    section.style.display = on ? '' : 'none';
    section.setAttribute?.('aria-hidden', String(!on));
    section.querySelectorAll?.('input,select,textarea').forEach(el => {
      if (!on) {
        if (el.required) el.dataset.wasRequired = '1';
        el.required = false;
        el.disabled = true;
      } else {
        el.disabled = false;
        if (el.dataset.wasRequired === '1') el.required = true;
      }
    });
  }
  function showIncome(on){
    if (secIncomeTitle) secIncomeTitle.style.display = on ? '' : 'none';
    flip(secIncome, on);
  }

  function applySpouseVisibility() {
    const residing  = !!inCAyes?.checked;
    const wantsFile = !!fileYes?.checked;

    // Always hide the legacy foreign-income block per new rules
    flip(secForeign, false);

    if (!residing) {                 // Non-resident
      flip(secRemaining, false);
      showIncome(true);
      return;
    }
    if (residing && !wantsFile) {    // Resident, not filing
      flip(secRemaining, false);
      showIncome(true);
      return;
    }
    // Resident, filing
    flip(secRemaining, true);
    showIncome(false);
  }

  [inCAyes, inCAno, fileYes, fileNo].forEach(el => el && el.addEventListener('change', applySpouseVisibility));
  applySpouseVisibility(); // initial paint
});
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  // helpers
  const show = (el, on) => { 
    if (el) {
      el.style.display = on ? '' : 'none';
      // Clear validation errors for all fields in this element when hidden
      if (!on && typeof hideFieldError === 'function') {
        el.querySelectorAll('input, select, textarea').forEach(field => {
          if (field.name) {
            hideFieldError(field.name);
          }
        });
      }
    }
  };
  const req  = (el, on) => { 
    if (!el) return;
    on ? el.setAttribute('required','required') : el.removeAttribute('required');
    // Clear validation errors when field becomes non-required
    if (!on && el.name && typeof hideFieldError === 'function') {
      hideFieldError(el.name);
    }
  };

  // radios
  const gigYes = document.getElementById('gig_income_yes');
  const gigNo  = document.getElementById('gig_income_no');

  // blocks/fields
  const expBlock   = document.getElementById('gig-expenses-block');
  const expText    = document.getElementById('gig_expenses_summary');

  const hstQBlock  = document.getElementById('hst-q-block');
  const hstYes     = document.getElementById('hst_yes');
  const hstNo      = document.getElementById('hst_no');
  const hstFields  = document.getElementById('hst-fields');
  const hstNum     = document.getElementById('hst_number');
  const hstAcc     = document.getElementById('hst_access');
  const hstStart   = document.getElementById('hst_start');
  const hstEnd     = document.getElementById('hst_end');

  // upload (on Upload page)
  const uploadGig  = document.getElementById('upload-gig-section');
  const dz         = document.getElementById('gig-drop');
  const dzInput    = document.getElementById('gig_tax_summary');
  const dzBtn      = document.getElementById('gig-browse');
  const dzList     = document.getElementById('gig-files');

  function applyGig() {
    const on = !!gigYes?.checked;
    show(expBlock,  on);
    show(hstQBlock, on);
    show(uploadGig, on);
    req(expText, on);

    // if they turn gig income OFF, also hide HST bits + clear requirements
    if (!on) {
      show(hstFields, false);
      [hstNum,hstAcc,hstStart,hstEnd].forEach(el => req(el,false));
    }
  }

  function applyHst() {
    const on = !!gigYes?.checked && !!hstYes?.checked;
    show(hstFields, on);
    [hstNum,hstAcc,hstStart,hstEnd].forEach(el => req(el,on));
  }

  // wire events
  [gigYes,gigNo].forEach(r => r?.addEventListener('change', () => { applyGig(); applyHst(); }));
  [hstYes,hstNo].forEach(r => r?.addEventListener('change', applyHst));

  // init
  applyGig();
  applyHst();

  // simple dropzone (upload page)
  dzBtn?.addEventListener('click', () => dzInput?.click());
  dz?.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('dragover'); });
  dz?.addEventListener('dragleave', () => dz.classList.remove('dragover'));
  dz?.addEventListener('drop', e => {
    e.preventDefault(); dz.classList.remove('dragover');
    if (!dzInput) return;
    dzInput.files = e.dataTransfer.files;
    listFiles();
  });
  dzInput?.addEventListener('change', listFiles);

  function listFiles(){
    if (!dzList || !dzInput) return;
    dzList.innerHTML = '';
    const files = dzInput.files || [];
    if (!files.length) return;
    const ul = document.createElement('ul');
    ul.style.margin = '8px 0 0';
    ul.style.padding = '0 0 0 18px';
    for (const f of files) {
      const li = document.createElement('li');
      li.textContent = `${f.name} (${Math.round(f.size/1024)} KB)`;
      ul.appendChild(li);
    }
    dzList.appendChild(ul);
  }
});
</script>

<!-- YES 4 -->

<script>
(function(){
  const table   = document.querySelector('.fi-table.rent-table');
  const thead   = table?.querySelector('thead tr');
  const tbody   = document.getElementById('rent-tbody');
  const hidden  = document.getElementById('rent-hidden-inputs');
  if (!table || !thead || !tbody || !hidden) return;

  // ---------- build clickable sort headers ----------
  const labels = ['Rent Address','From','To','Total Rent Paid','Actions'];
  const fields = ['address','from','to','total', null];

  [...thead.children].forEach((th, i) => {
    const field = fields[i];
    th.textContent = '';                                      // clear
    const btn = document.createElement('button');
    btn.type = 'button'; btn.className = 'rent-sort';
    btn.dataset.field = field || '';
    btn.innerHTML = `<span class="txt">${labels[i]}</span>${field?'<span class="sort-ico"></span>':''}`;
    if (!field) btn.disabled = true;                          // Actions column
    th.appendChild(btn);
  });

  let sortField = null, sortDir = 'asc';

  function setSortButtonState(){
    thead.querySelectorAll('.rent-sort').forEach(b=>{
      b.classList.remove('asc','desc');
      if (b.dataset.field === sortField) b.classList.add(sortDir);
    });
  }

  // helpers to read hidden values
  const MONTHS = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const cleanNum = v => (v||'').toString().replace(/,/g,'').trim();
  const ym = (m,y) => (parseInt(y||0,10)*100 + parseInt(m||0,10));

  function rowData(tr){
    const key = tr.dataset.key;
    const block = hidden.querySelector(`.rent-hidden[data-key="${key}"]`);
    if (!block) return {key};
    const get = name => block.querySelector(`[name="rent[${key}][${name}]"]`)?.value || '';
    return {
      key,
      address: get('address').toLowerCase(),
      from: ym(get('from_month'), get('from_year')),
      to:   ym(get('to_month'),   get('to_year')),
      total: parseFloat(cleanNum(get('total'))) || 0
    };
  }

  function doSort(field){
    if (!field || tbody.querySelector('.editing')) return; // skip while editing
    sortDir = (sortField === field && sortDir === 'asc') ? 'desc' : 'asc';
    sortField = field;

    const rows = Array.from(tbody.querySelectorAll('tr.data-row'));
    rows.sort((a,b)=>{
      const A = rowData(a), B = rowData(b);
      let cmp = 0;
      if (field === 'address') cmp = A.address.localeCompare(B.address);
      if (field === 'from')    cmp = A.from - B.from;
      if (field === 'to')      cmp = A.to   - B.to;
      if (field === 'total')   cmp = A.total - B.total;
      return (sortDir === 'asc') ? cmp : -cmp;
    });
    rows.forEach(r => tbody.appendChild(r));
    setSortButtonState();
  }

  thead.addEventListener('click', e=>{
    const btn = e.target.closest('.rent-sort'); if (!btn || btn.disabled) return;
    e.preventDefault();
    doSort(btn.dataset.field);
  });

  setSortButtonState();

  // ---------- "To" must be >= "From" (equal allowed) ----------
  function enforceToMin(tr){
    const mFrom = tr.querySelectorAll('.rent-mm')[0];
    const yFrom = tr.querySelectorAll('.rent-yy')[0];
    const mTo   = tr.querySelectorAll('.rent-mm')[1];
    const yTo   = tr.querySelectorAll('.rent-yy')[1];
    if (!mFrom || !yFrom || !mTo || !yTo) return;

    const fm = parseInt(mFrom.value||0,10);
    const fy = parseInt(yFrom.value||0,10);
    // enable all first
    [...yTo.options].forEach(o => { if (o.value) o.disabled = false; });
    [...mTo.options].forEach(o => { if (o.value) o.disabled = false; });

    // block years < fromYear
    [...yTo.options].forEach(o=>{
      if (!o.value) return;
      if (parseInt(o.value,10) < fy) o.disabled = true;
    });

    // if same year, block months < fromMonth
    if (parseInt(yTo.value||0,10) === fy){
      [...mTo.options].forEach(o=>{
        if (!o.value) return;
        if (parseInt(o.value,10) < fm) o.disabled = true;
      });
      // if currently invalid, snap to from month
      if (parseInt(mTo.value||0,10) < fm){ mTo.value = String(fm).padStart(2,'0'); }
    }

    // if chosen year now < fy (because user switched), snap to fy
    if (parseInt(yTo.value||0,10) < fy){ yTo.value = fy || ''; }
  }

  // watch for edit rows and wire constraints
  const wireEditRow = (tr)=>{
    if (!tr || tr.dataset.toGuard) return;
    tr.dataset.toGuard = '1';
    const selects = tr.querySelectorAll('.rent-mm, .rent-yy');
    selects.forEach(s => s.addEventListener('change', () => enforceToMin(tr)));
    enforceToMin(tr);
  };

  // initial + on dynamic changes
  tbody.querySelectorAll('tr.editing').forEach(wireEditRow);
  new MutationObserver(muts=>{
    muts.forEach(m=>{
      m.addedNodes.forEach(n=>{
        if (n.nodeType === 1 && n.matches('tr.editing')) wireEditRow(n);
      });
    });
  }).observe(tbody, {childList:true});

  // hard validation before Save (captures before original handler)
  tbody.addEventListener('click', function(e){
    const btn = e.target.closest('.rent-save'); if (!btn) return;
    const tr  = btn.closest('tr.editing'); if (!tr) return;
    const fm = parseInt(tr.querySelectorAll('.rent-mm')[0].value||0,10);
    const fy = parseInt(tr.querySelectorAll('.rent-yy')[0].value||0,10);
    const tm = parseInt(tr.querySelectorAll('.rent-mm')[1].value||0,10);
    const ty = parseInt(tr.querySelectorAll('.rent-yy')[1].value||0,10);
    const from = fy*100 + fm, to = ty*100 + tm;
    if (to < from){
      e.preventDefault(); e.stopImmediatePropagation();
      alert('“To” date cannot be earlier than the “From” date.');
    }
  }, true);
})();
</script>


 <!-- Rental Script -->

<script>
(function onReady(fn){document.readyState!=='loading'?fn():document.addEventListener('DOMContentLoaded',fn);})(function(){

  /* =============== Data =============== */
  let PROPS = [];
  try {
    const seed = document.getElementById('rental-seed');
    if (seed) PROPS = JSON.parse(seed.textContent || '[]') || [];
  } catch(e){ PROPS = []; }

  /* =============== Els =============== */
  const tbody      = document.getElementById('props-tbody');
  const emptyTr    = document.getElementById('props-empty-row');
  const hiddenWrap = document.getElementById('props-hidden-inputs');

  const addTop     = document.getElementById('add-prop-wrap-top');
  const addBottom  = document.getElementById('add-prop-wrap-bottom');
  const addBtn     = document.getElementById('btn-add-property');

  // Modal + steps
  const modal      = document.getElementById('prop-modal');
  const titleEl    = document.getElementById('prop-modal-title');
  const form       = document.getElementById('prop-form');
  const idEl       = document.getElementById('prop_id');

  const step1      = document.getElementById('prop-step1');
  const step2      = document.getElementById('prop-step2');
  const foot1      = document.getElementById('prop-foot-1');
  const foot2      = document.getElementById('prop-foot-2');

  // Step1 fields
  const ownerEl    = document.getElementById('prop_owner_name'); // optional
  const addrEl     = document.getElementById('prop_address');
  const sDispEl    = document.getElementById('prop_start_display');
  const sIsoEl     = document.getElementById('prop_start_iso');
  const eDispEl    = document.getElementById('prop_end_display');
  const eIsoEl     = document.getElementById('prop_end_iso');
  const partnerEl  = document.getElementById('prop_partner');
  const ownerPctEl = document.getElementById('prop_owner_pct');
  const ownUseEl   = document.getElementById('prop_ownuse_pct');
  const grossEl    = document.getElementById('prop_gross');

  // Step2 (expenses)
  const expMortgage  = document.getElementById('prop_exp_mortgage');
  const expIns       = document.getElementById('prop_exp_insurance');
  const expRepairs   = document.getElementById('prop_exp_repairs');
  const expUtils     = document.getElementById('prop_exp_utilities');
  const expInternet  = document.getElementById('prop_exp_internet');
  const expTax       = document.getElementById('prop_exp_propertytax');
  const expOther     = document.getElementById('prop_exp_other');

  // Nav buttons
  const btnCancel1 = document.getElementById('prop-cancel-1'); // step1 cancel
  const btnNext    = document.getElementById('prop-next');
  const btnBack    = document.getElementById('prop-back');
  const btnSave    = document.getElementById('prop-save');

  // Confirm delete (reuse your existing confirm if IDs differ)
  const cModal  = document.getElementById('prop-confirm') || document.getElementById('rent-confirm');
  const cText   = document.getElementById('prop-confirm-text') || document.getElementById('rent-confirm-text');
  const cYes    = document.getElementById('prop-confirm-yes')  || document.getElementById('rent-confirm-yes');
  const cCancel = document.getElementById('prop-confirm-cancel')|| document.getElementById('rent-confirm-cancel');

  /* =============== Helpers =============== */
  const open  = el => { if (!el) return; el.hidden = false; el.style.display = 'block'; };
  const close = el => { if (!el) return; el.hidden = true;  el.style.display = 'none';  };

  const uid   = () => 'p_' + Date.now().toString(36) + Math.random().toString(36).slice(2,8);
  const esc   = s => (s||'').replace(/[&<>"']/g, m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]));
  const MON   = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  const pad2  = n => String(n).padStart(2,'0');

  // "YYYY-MM-DD" -> "Mon | DD | YYYY"
  function fmtYMDWords(iso, fallbackDisplay){
    if (iso && /^\d{4}-\d{2}-\d{2}$/.test(iso)) {
      const [y,m,d] = iso.split('-');
      return `${MON[parseInt(m,10)-1]} | ${d} | ${y}`;
    }
    // try "MM | DD | YYYY"
    if (fallbackDisplay) {
      const clean = fallbackDisplay.replace(/\s/g,'');
      const parts = clean.split('|'); // ["03","09","2017"]
      if (parts.length===3 && /^\d{1,2}$/.test(parts[0]) && /^\d{1,2}$/.test(parts[1]) && /^\d{4}$/.test(parts[2])) {
        const m = Math.max(1, Math.min(12, parseInt(parts[0],10)));
        return `${MON[m-1]} | ${pad2(parts[1])} | ${parts[2]}`;
      }
      // pass-through if already "Mon | DD | YYYY"
      const m2 = /^([A-Za-z]{3})\|(\d{1,2})\|(\d{4})$/.exec(clean);
      if (m2) return `${m2[1].slice(0,3)} | ${pad2(m2[2])} | ${m2[3]}`;
    }
    return fallbackDisplay || '';
  }

  // Append "$CAD" for expenses (only when there is a value)
function fmtPct(v){
  const s = (v ?? '').toString().trim();
  if (!s) return '';
  const n = s.replace(/\s*%$/,'').trim();
  return `${n} %`;
}

// $32 CAD  (normalizes "32 $CAD", "$32", "32 CAD", etc.)
function fmtCAD(v){
  const s = (v ?? '').toString().trim();
  if (!s) return '';
  let n = s
    .replace(/\s*\$?\s*CAD\s*$/i, '') // strip any "... $CAD" / "... CAD"
    .replace(/^\s*\$/,'')             // strip leading $
    .trim();
  return `$${n} CAD`;
}


  function renderHiddenInputs(){
    if (!hiddenWrap) return;
    hiddenWrap.innerHTML = '';
    PROPS.forEach((p,i)=>{
      const flat = {
        owner: p.owner || '',
        address: p.address || '',
        start_display: p.start_display || '',
        start: p.start || '',
        end_display: p.end_display || '',
        end: p.end || '',
        partner: p.partner || '',
        owner_pct: p.owner_pct || '',
        ownuse_pct: p.ownuse_pct || '',
        gross: p.gross || ''
      };
      Object.keys(flat).forEach(k=>{
        const inp = document.createElement('input');
        inp.type='hidden'; inp.name=`rental_props[${i}][${k}]`; inp.value=flat[k];
        hiddenWrap.appendChild(inp);
      });
      const ex = p.expenses || {};
      ['mortgage','insurance','repairs','utilities','internet','property_tax','other'].forEach(k=>{
        const inp = document.createElement('input');
        inp.type='hidden';
        inp.name=`rental_props[${i}][expenses][${k}]`;
        inp.value = ex[k] || '';
        hiddenWrap.appendChild(inp);
      });
    });
  }

  function moveAddButton(){
    if (!addBtn) return;
    if (PROPS.length){
      if (addTop)    addTop.style.display = 'none';
      if (addBottom) addBottom.style.display = 'flex';
      if (addBottom && !addBottom.contains(addBtn)) addBottom.appendChild(addBtn);
    } else {
      if (addTop)    addTop.style.display = 'flex';
      if (addBottom) addBottom.style.display = 'none';
      if (addTop && !addTop.contains(addBtn)) addTop.appendChild(addBtn);
    }
  }

  /* =============== Card renderer (replaces table rows) =============== */
  function renderTable(){
    if (!tbody) return;
    tbody.innerHTML = '';
    if (!PROPS.length){
      if (emptyTr) tbody.appendChild(emptyTr);
    } else {
      PROPS.forEach(p=>{
        const startTxt = fmtYMDWords(p.start, p.start_display);
        const endTxt   = fmtYMDWords(p.end,   p.end_display);
        const ex       = p.expenses || {};

        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td colspan="8" style="padding:12px 0;">
              <div class="prop-card" style="border:1px solid #e5e7eb;border-radius:12px;padding:14px 16px;">
              <div class="prop-card__head" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                <div style="font-weight:700;font-size:20px;text-transform:lowercase;">${esc(p.address||'')}</div>
                <div style="display:flex;gap:10px;align-items:center;">
                  <a href="#" data-edit="${p.id}" title="Edit" class="action-link" style="display:inline-flex;align-items:center;gap:6px;color:#2563eb;text-decoration:none;font-weight:700;">
                    <span>Edit</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 113 3L7 19 3 20l1-4 12.5-12.5z"/></svg>
                  </a>
                  <span class="action-sep" aria-hidden="true" style="color:#94a3b8;">•</span>
                  <a href="#" data-del="${p.id}" title="Delete" class="action-link delete" style="display:inline-flex;align-items:center;gap:6px;color:#dc2626;text-decoration:none;font-weight:700;">
                    <span>Delete</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a2 2 0 012-2h2a2 2 0 012 2v2"/></svg>
                  </a>
                </div>
              </div>

              <!-- Property Details -->
<div style="border:1px solid #eef0f4;border-radius:10px;margin-top:10px;">
  <div style="font-weight:700; color: #475569; padding:10px 12px;border-bottom:1px solid #eef0f4;">Property Details</div>
  <table class="prop-table prop-details" style="width:100%;">
<tbody>
  <!-- Owner -->
  <tr>
    <td class="prop-label" style="padding:10px 12px;color:#475569;">Owner</td>
    <td class="wrap" style="padding:10px 12px;" colspan="5">${esc(p.owner || '—')}</td>
  </tr>

  <!-- Start / End / Partner -->
  <tr>
    <td class="prop-label" style="padding:10px 12px;color:#475569;">Start</td>
    <td class="date" style="padding:10px 12px;" data-date>${esc(startTxt)}</td>

    <td class="prop-label" style="padding:10px 12px;color:#475569;">End</td>
    <td class="date" style="padding:10px 12px;" data-date>${esc(endTxt)}</td>

    <td class="prop-label" style="padding:10px 12px;color:#475569;">Business Partner</td>
    <td class="wrap" style="padding:10px 12px;">${esc(p.partner || '—')}</td>
  </tr>

  <!-- Ownership / Own Use / Gross -->
  <tr style="border-top:1px solid #f1f5f9">
		<td class="prop-label" style="padding:10px 12px;color:#475569;">Ownership %</td>
		<td  style="padding:10px 12px;">${esc(fmtPct(p.owner_pct))}</td>

		<td class="prop-label" style="padding:10px 12px;color:#475569;">Own Use %</td>
		<td  style="padding:10px 12px;">${esc(fmtPct(p.ownuse_pct))}</td>

		<td class="prop-label" style="padding:10px 12px;color:#475569;">Gross Income (CAD)</td>
		<td " style="padding:10px 12px;">${esc(fmtCAD(p.gross))}</td>
  </tr>
</tbody>

  </table>
</div>


              <!-- Annual Expenses -->
<div style="border:1px solid #eef0f4;border-radius:10px;margin-top:16px;">
  <div style="font-weight:700; color: #475569; padding:10px 12px;border-bottom:1px solid #eef0f4;">
    Property (Annual) Expenses
  </div>

  <!-- NOTE: added class="prop-table prop-expenses" and use data-unit="cad" -->
  <table class="prop-table prop-expenses" style="border-collapse:separate;border-spacing:0;">
  <tbody>
    <tr>
      	<td class="prop-label" style="padding:10px 12px;">Mortgage Interest</td>
		<td  style="padding:10px 12px;">${esc(fmtCAD(ex.mortgage))}</td>

		<td class="prop-label" style="padding:10px 12px;">Insurance</td>
		<td style="padding:10px 12px;">${esc(fmtCAD(ex.insurance))}</td>

		<td class="prop-label" style="padding:10px 12px;">Repairs & Maintenance</td>
		<td style="padding:10px 12px;">${esc(fmtCAD(ex.repairs))}</td>

    </tr>
                                       
    <tr style="border-top:1px solid #f1f5f9">
                                       
    	<td class="prop-label" style="padding:10px 12px;">Utilities</td>
    	<td style="padding:10px 12px;">${esc(fmtCAD(ex.utilities))}</td>
                                    
    	<td class="prop-label" style="padding:10px 12px;">Internet</td>
		<td style="padding:10px 12px;">${esc(fmtCAD(ex.internet))}</td>

		<td class="prop-label" style="padding:10px 12px;">Property Tax</td>
    	<td style="padding:10px 12px;">${esc(fmtCAD(ex.property_tax))}</td>
    </tr>
                                       
   	<tr style="border-top:1px solid #f1f5f9">
                                       
		<td class="prop-label" style="padding:10px 12px;">Other</td>
		<td style="padding:10px 12px;">${esc(fmtCAD(ex.other))}</td>
                                       
    </tr>
                                       
  </tbody>
</table>

</div>

            </div>
          </td>`;
        tbody.appendChild(tr);
      });

      // actions (event delegation not needed but kept explicit)
      tbody.querySelectorAll('[data-edit]').forEach(a=>{
        a.addEventListener('click', e=>{
          e.preventDefault();
          const id = a.getAttribute('data-edit');
          const row = PROPS.find(x=>x.id===id);
          if (row) openPropModal(row);
        });
      });
      tbody.querySelectorAll('[data-del]').forEach(a=>{
        a.addEventListener('click', e=>{
          e.preventDefault();
          const id = a.getAttribute('data-del');
          const row = PROPS.find(x=>x.id===id);
          openConfirm(id, row);
        });
      });
    }
    renderHiddenInputs();
    moveAddButton();
  }

  /* =============== Modal wiring =============== */
function showStep(n){
  const on1 = (n === 1), on2 = (n === 2);

  // Use the hidden attribute so CSS can’t fight it
  if (step1) step1.hidden = !on1;
  if (foot1) foot1.hidden = !on1;
  if (step2) step2.hidden = !on2;
  if (foot2) foot2.hidden = !on2;

  // clean up any old inline display leftovers
  [step1, step2, foot1, foot2].forEach(el => el && el.style.removeProperty('display'));

  // Title
  if (titleEl) {
    titleEl.textContent = on2 ? 'Annual Expenses' :
      (idEl?.value ? 'Edit Property' : 'Add Property');
  }
}


  function resetForm(){
    idEl.value = '';
    ownerEl && (ownerEl.value = '');
    addrEl.value = '';
    sDispEl.value = ''; sIsoEl.value = '';
    eDispEl.value = ''; eIsoEl.value = '';
    partnerEl.value = '';
    ownerPctEl.value = '';
    ownUseEl.value = '';
    grossEl.value = '';
    [expMortgage,expIns,expRepairs,expUtils,expInternet,expTax,expOther].forEach(el=>{ if (el) el.value=''; });
    showStep(1);
  }

  function openPropModal(row){
    if (row){
      titleEl.textContent = 'Edit Property';
      idEl.value       = row.id;
      ownerEl && (ownerEl.value = row.owner || '');
      addrEl.value     = row.address || '';
      sDispEl.value    = row.start_display || fmtYMDWords(row.start,'') || '';
      sIsoEl.value     = row.start || '';
      eDispEl.value    = row.end_display   || fmtYMDWords(row.end,'')   || '';
      eIsoEl.value     = row.end   || '';
      partnerEl.value  = row.partner || '';
      ownerPctEl.value = row.owner_pct || '';
      ownUseEl.value   = row.ownuse_pct || '';
      grossEl.value    = row.gross || '';

      const ex = row.expenses || {};
      expMortgage && (expMortgage.value = ex.mortgage || '');
      expIns      && (expIns.value      = ex.insurance || '');
      expRepairs  && (expRepairs.value  = ex.repairs || '');
      expUtils    && (expUtils.value    = ex.utilities || '');
      expInternet && (expInternet.value = ex.internet || '');
      expTax      && (expTax.value      = ex.property_tax || '');
      expOther    && (expOther.value    = ex.other || '');
    } else {
      titleEl.textContent = 'Add Property';
      resetForm();
    }
    // nudge your external DOB picker binder, if any
    [sDispEl,eDispEl,sIsoEl,eIsoEl].forEach(el=>{ if (el) el.dispatchEvent(new Event('input',{bubbles:true})); });

    showStep(1);
    open(modal);
  }

  function closePropModal(){ close(modal); }

  // Add button
  addBtn && addBtn.addEventListener('click', ()=>openPropModal());

  // Step1: Cancel + Continue
  btnCancel1 && btnCancel1.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); closePropModal(); });
  btnNext && btnNext.addEventListener('click', (e)=>{
    e.preventDefault(); e.stopPropagation();
    // validate only inputs inside step1
    if (step1){
      const inputs = step1.querySelectorAll('input,select,textarea');
      for (const el of inputs){ if (el.required && !el.reportValidity()) return; }
    } else if (!form.reportValidity()) {
      return;
    }
    showStep(2);
  });

  // Step2: Back + Save
  btnBack && btnBack.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); showStep(1); });

  btnSave && btnSave.addEventListener('click', (e)=>{
    e.preventDefault(); e.stopPropagation();
    // (Optional) validate step2 if any required fields exist
    if (step2){
      const inputs = step2.querySelectorAll('input,select,textarea');
      for (const el of inputs){ if (el.required && !el.reportValidity()) return; }
    }

    const data = {
      id:         idEl.value || uid(),
      owner:      ownerEl ? ownerEl.value.trim() : '',
      address:    addrEl.value.trim(),
      start_display: sDispEl.value.trim(),
      start:      sIsoEl.value.trim(),
      end_display: eDispEl.value.trim(),
      end:        eIsoEl.value.trim(),
      partner:    partnerEl.value.trim(),
      owner_pct:  ownerPctEl.value.trim(),
      ownuse_pct: ownUseEl.value.trim(),
      gross:      grossEl.value.trim(),
      expenses: {
        mortgage:     expMortgage ? expMortgage.value.trim() : '',
        insurance:    expIns      ? expIns.value.trim()      : '',
        repairs:      expRepairs  ? expRepairs.value.trim()  : '',
        utilities:    expUtils    ? expUtils.value.trim()    : '',
        internet:     expInternet ? expInternet.value.trim() : '',
        property_tax: expTax      ? expTax.value.trim()      : '',
        other:        expOther    ? expOther.value.trim()    : ''
      }
    };

    const i = PROPS.findIndex(x=>x.id===data.id);
    if (i>=0) PROPS[i] = data; else PROPS.push(data);

    renderTable();
    closePropModal();
  });

  // Close on backdrop
  modal && modal.addEventListener('click', (e)=>{ if (e.target===modal || e.target.classList.contains('qs-modal__backdrop')) closePropModal(); });

  /* =============== Confirm delete =============== */
  function openConfirm(id, row){
    if (cText) cText.textContent = `Delete property at "${row?.address || 'this address'}"?`;
    open(cModal);

    function cleanup(){
      cYes.removeEventListener('click', onYes);
      cCancel.removeEventListener('click', onNo);
      cModal.removeEventListener('click', onBackdrop);
    }
    function onYes(){
      const idx = PROPS.findIndex(x=>x.id===id);
      if (idx>=0) PROPS.splice(idx,1);
      renderTable(); close(cModal); cleanup();
    }
    function onNo(){ close(cModal); cleanup(); }
    function onBackdrop(e){ if (e.target===cModal || e.target.classList.contains('qs-modal__backdrop')) onNo(); }

    cYes.addEventListener('click', onYes);
    cCancel.addEventListener('click', onNo);
    cModal.addEventListener('click', onBackdrop);
  }

  /* =============== Init =============== */
  [sDispEl,eDispEl,sIsoEl,eIsoEl].forEach(el=>{ if (el) el.dispatchEvent(new Event('input',{bubbles:true})); });
  renderTable();
  showStep(1);

});
</script>

<script>
(function(){
  const modal = document.getElementById('prop-modal');
  const closeBtn = document.getElementById('prop-close');
  const cancel1  = document.getElementById('prop-cancel-1');
  const backdrop = modal?.querySelector('.qs-modal__backdrop');

  function closeProp(){ if (modal) modal.hidden = true; }
  closeBtn?.addEventListener('click', closeProp);
  cancel1 ?.addEventListener('click', closeProp);
  backdrop?.addEventListener('click', closeProp);
  window.addEventListener('keydown', e => { if (!modal?.hidden && e.key === 'Escape') closeProp(); });
})();
</script>

<!-- YES REVIEW 1 -->
<script>
(function(){
  if (window.__REV_NAV_PATCHED) return; window.__REV_NAV_PATCHED = true;

  function flagsSafe(){
    try { return window.App?.flags?.() || {}; } catch(e){ return {}; }
  }
  function resolveStep(step){
    if (step === 'upload'){
      const f = flagsSafe();
      return f.spouseFiles ? 'upload-spouse' : 'upload-self';
    }
    return step;
  }

  function goToPanel(step){
    step = resolveStep(step);

    if (step === 'pre' && window.App?.goToWelcome){
      window.App.goToWelcome();
      window.App.updateProgress?.('personal');
      window.scrollTo({top:0, behavior:'smooth'}); return;
    }
    if (window.App?.goToFormAndShow){
      window.App.goToFormAndShow(step);
      window.scrollTo({top:0, behavior:'smooth'}); return;
    }

    // Fallback (no App router)
    document.querySelectorAll('.pi-main').forEach(p=> p.hidden = true);
    const el = document.querySelector(`.pi-main[data-panel="${step}"]`);
    if (el){ el.hidden = false; el.scrollIntoView({behavior:'smooth', block:'start'}); }
  }

  // “Go to …” links inside accordion
  document.addEventListener('click', (e)=>{
    const a = e.target.closest('.rev-link[data-open]');
    if (!a) return;
    e.preventDefault();
    goToPanel(a.getAttribute('data-open'));
  });

  // Make sidebar steps clickable
  document.querySelectorAll('.pi-steps.progress-only .pi-step').forEach(a=>{
    a.removeAttribute('aria-disabled'); a.tabIndex = 0;
    a.addEventListener('click', (e)=>{
      e.preventDefault();
      const step = a.getAttribute('data-step');
      if (step) goToPanel(step);
    });
    a.addEventListener('keydown', (e)=>{
      if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); a.click(); }
    });
  });
})();
</script>




<!-- YES REVIEW 2 -->

<script>
(function(){
  function getMaritalStatus(){
    const sel = document.querySelector('#marital_status_select');
    let v = '';
    if (sel && sel.value) v = sel.value.trim();
    if (!v){
      const r = document.querySelector('input[name="marital_status"]:checked');
      if (r) v = (r.value || '').trim();
    }
    return v;
  }
  function updatePreDetailsVisibility(){
    const ms = getMaritalStatus();
    const isMarriedOrCL = (ms === 'Married' || ms === 'Common Law');
    document.querySelectorAll('#rev-pre .pre-cond').forEach(el=>{
      el.style.display = isMarriedOrCL ? '' : 'none';
    });
  }
  function safeBind(fn){
    document.addEventListener('change', fn);
    document.addEventListener('input', fn);
    fn();
  }
  safeBind(updatePreDetailsVisibility);
})();
</script>

<!-- YES REVIEW 3 -->

<!-- YES REVIEW 3 — FINAL (fast, split-upload aware, good Back/Prev) -->
<script>
(function(){
  if (window.__REV3_FINAL) return; window.__REV3_FINAL = true;

  /* ===== tiny helpers ===== */
  const $  = (s, r=document)=>r.querySelector(s);
  const $$ = (s, r=document)=>Array.from(r.querySelectorAll(s));
  const on = (t, sel, fn, opt)=>document.addEventListener(t, e=>{
    const el = e.target.closest(sel); if (!el) return; fn(e, el);
  }, opt);

  function getVal(name){
    const el = document.querySelector('input[name="'+name+'"]:checked');
    return el ? el.value : null;
  }
  function yesLike(name){
    const v = (getVal(name)||'').toString().trim().toLowerCase();
    return v==='y'||v==='yes'||v==='true'||v==='1';
  }
  function flagsSafe(){
    try{ if (window.App && typeof window.App.flags==='function') return window.App.flags(); }catch(e){}
    const ms = getVal('marital_status');
    const marriedLike = (ms==='Married'||ms==='Common Law');
    const childQ = marriedLike || ms==='Separated'||ms==='Divorced'||ms==='Widowed';
    const spouseFiles = marriedLike && (getVal('spouseFile')==='yes');
    const hasChildren = childQ && (getVal('children')==='yes');
    return { ms, marriedLike, spouseFiles, hasChildren };
  }

  /* ===== accordion toggle (one handler for all review panels) ===== */
  on('click', '.rev-item[aria-controls]', (e, btn)=>{
    e.preventDefault();
    const id = btn.getAttribute('aria-controls');
    const panel = document.getElementById(id);
    if (!panel) return;
    const open = btn.getAttribute('aria-expanded')==='true';
    btn.setAttribute('aria-expanded', String(!open));
    panel.hidden = open;
  });
  on('keydown', '.rev-item[aria-controls]', (e, btn)=>{
    if (e.key!=='Enter' && e.key!==' ') return;
    e.preventDefault();
    btn.click();
  });

  /* ===== nice file icons (from old Rev7) ===== */
  function iconKeyFromName(name){
    const ext=(String(name).split('.').pop()||'').toLowerCase();
    if (ext==='pdf') return 'pdf';
    if (ext==='doc'||ext==='docx') return 'doc';
    if (ext==='xls'||ext==='xlsx'||ext==='csv') return 'xls';
    if (ext==='ppt'||ext==='pptx') return 'ppt';
    if (['png','jpg','jpeg','gif','bmp','webp','tif','tiff','svg','heic'].includes(ext)) return 'img';
    if (['zip','rar','7z'].includes(ext)) return 'zip';
    if (['txt','rtf','md','log'].includes(ext)) return 'txt';
    if (['php','js','ts','css','scss','html','py','java','c','cpp','cs','rb','go'].includes(ext)) return 'code';
    return 'file';
  }
  function iconSVG(key, size){
    size = size || 28;
    const fill  = {pdf:'#E53935',doc:'#1E88E5',xls:'#2E7D32',ppt:'#FB8C00',img:'#7E57C2',zip:'#6D4C41',txt:'#546E7A',code:'#00897B',file:'#9E9E9E'}[key] || '#9E9E9E';
    const label = {pdf:'PDF',doc:'DOC',xls:'XLS',ppt:'PPT',img:'IMG',zip:'ZIP',txt:'TXT',code:'CODE',file:''}[key] || '';
    return (
      '<svg class="rev-ico-svg" viewBox="0 0 24 24" width="'+size+'" height="'+size+'" aria-hidden="true" focusable="false">'+
        '<path d="M6 2h8l6 6v12a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="'+fill+'"/>'+
        '<path d="M14 2v6h6" fill="#fff" fill-opacity=".9"/>'+
        (label?'<text x="12" y="16" text-anchor="middle" fill="#fff" font-size="7" font-weight="700">'+label+'</text>':'')+
      '</svg>'
    );
  }

  /* ===== uploads: fast readers + diff render ===== */
  function fileNamesFrom(listSel, inputSel){
    const names=[];
    const list = listSel && $(listSel);
    if (list){
      const items=list.querySelectorAll('[data-filename],[data-name],.dz-filename,li,.dz-item,.file-row');
      items.forEach(it=>{
        let n = it.getAttribute?.('data-filename') || it.getAttribute?.('data-name');
        if (!n){
          const t=(it.textContent||'').trim();
          if (t) n=t.replace(/^×\s*/,'');
        }
        if (n) names.push(n);
      });
    }
    if (!names.length && inputSel){
      const inp=$(inputSel);
      if (inp?.files?.length) for (let i=0;i<inp.files.length;i++) names.push(inp.files[i].name);
    }
    return names;
  }
  function setCount(id, n){
    const el = $('#'+id); if (el && el.textContent!==String(n)) el.textContent=String(n);
  }
  function renderNameList(namesId, names){
    const ul=$('#'+namesId); if (!ul) return;
    if (!names || !names.length){ if (!ul.hidden||ul.innerHTML){ ul.hidden=true; ul.innerHTML=''; } return; }
    ul.hidden=false;
    const frag=document.createDocumentFragment();
    for (const n of names){
      const li=document.createElement('li');
      li.className='rev-file'; li.setAttribute('role','listitem');
      li.style.cssText='display:flex;align-items:center;gap:10px;padding:4px 0';
      const iSpan=document.createElement('span'); iSpan.className='rev-ico'; iSpan.innerHTML=iconSVG(iconKeyFromName(n), 28);
      const nSpan=document.createElement('span'); nSpan.className='rev-file-name'; nSpan.title=n;
      nSpan.style.cssText='font-size:13.5px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis';
      nSpan.textContent=n;
      li.appendChild(iSpan); li.appendChild(nSpan); frag.appendChild(li);
    }
    ul.replaceChildren(frag);
  }
  function panelVisible(id){ const p = document.getElementById(id); return !!p && !p.hidden; }

  // remember last rendered lists (to diff)
  const S = {
    app_id:[],app_tslips:[],app_t2202:[],app_invest:[],app_t2200:[],app_exp:[],app_other:[],
    gig:[], sp_id:[],sp_invest:[],sp_t2202:[],sp_tslips:[],sp_other:[]
  };
  function changed(a,b){ if (!a || a.length!==b.length) return true; for (let i=0;i<a.length;i++) if (a[i]!==b[i]) return true; return false; }

  function refreshReviewUploads(){
    const wantUnified = !!document.getElementById('rev-upload');               // single panel
    const wantSelf    = !!document.getElementById('rev-upload-self');          // split: applicant
    const wantSpouse  = !!document.getElementById('rev-upload-spouse');        // split: spouse
    const wantSelfList   = wantUnified ? panelVisible('rev-upload') : panelVisible('rev-upload-self');
    const wantSpouseList = wantUnified ? panelVisible('rev-upload') : panelVisible('rev-upload-spouse');

    // Applicant
    const app_id     = fileNamesFrom('#app_id_list',          '#app_id_proof');
    const app_tslips = fileNamesFrom('#app_tslips_list',      '#app_tslips');
    const app_t2202  = fileNamesFrom('#app_t2202_receipt_list','#app_t2202_receipt');
    const app_invest = fileNamesFrom('#app_invest_list',      '#app_invest');
    const app_t2200  = fileNamesFrom('#app_t2200_work_list',  '#app_t2200_work');
    const app_exp    = fileNamesFrom('#app_exp_summary_list', '#app_exp_summary');
    const app_other  = fileNamesFrom('#app_otherdocs_list',   '#app_otherdocs');

    setCount('rev-app-id-count',     app_id.length);
    setCount('rev-app-tslips-count', app_tslips.length);
    setCount('rev-app-t2202-count',  app_t2202.length);
    setCount('rev-app-invest-count', app_invest.length);
    setCount('rev-app-t2200-count',  app_t2200.length);
    setCount('rev-app-exp-count',    app_exp.length);
    setCount('rev-app-other-count',  app_other.length);

    if (wantSelfList){
      if (changed(S.app_id,app_id)){ S.app_id=app_id; renderNameList('rev-app-id-names',app_id); }
      if (changed(S.app_tslips,app_tslips)){ S.app_tslips=app_tslips; renderNameList('rev-app-tslips-names',app_tslips); }
      if (changed(S.app_t2202,app_t2202)){ S.app_t2202=app_t2202; renderNameList('rev-app-t2202-names',app_t2202); }
      if (changed(S.app_invest,app_invest)){ S.app_invest=app_invest; renderNameList('rev-app-invest-names',app_invest); }
      if (changed(S.app_t2200,app_t2200)){ S.app_t2200=app_t2200; renderNameList('rev-app-t2200-names',app_t2200); }
      if (changed(S.app_exp,app_exp)){ S.app_exp=app_exp; renderNameList('rev-app-exp-names',app_exp); }
      if (changed(S.app_other,app_other)){ S.app_other=app_other; renderNameList('rev-app-other-names',app_other); }
    }

    // Gig block visibility + list
    const gigNames = fileNamesFrom('#gig-files', '#gig_tax_summary');
    const ruGig = $('.ru-gig');
    const showGig = yesLike('gig_income') || (getVal('selfEmp')==='yes') || gigNames.length>0;
    if (ruGig) ruGig.hidden = !showGig;
    setCount('rev-app-gig-count', gigNames.length);
    if (wantSelfList && changed(S.gig,gigNames)){ S.gig=gigNames; renderNameList('rev-app-gig-names', gigNames); }

    // Spouse (only when spouse files)
    const f = flagsSafe();
    const spTitle = $('.ru-spouse-title'), spWrap=$('.ru-spouse');
    const showSp  = !!f.spouseFiles;
    if (spTitle) spTitle.hidden = !showSp;
    if (spWrap)  spWrap.hidden  = !showSp;

    if (showSp){
      const sp_id     = fileNamesFrom('#sp_id_list',     '#sp_id_proof');
      const sp_invest = fileNamesFrom('#sp_invest_list', '#sp_invest');
      const sp_t2202  = fileNamesFrom('#sp_t2202_list',  '#sp_t2202');
      const sp_tslips = fileNamesFrom('#sp_tslips_list', '#sp_tslips');
      const sp_other  = fileNamesFrom('#sp_otherdocs_list','#sp_otherdocs');

      setCount('rev-sp-id-count',     sp_id.length);
      setCount('rev-sp-invest-count', sp_invest.length);
      setCount('rev-sp-t2202-count',  sp_t2202.length);
      setCount('rev-sp-tslips-count', sp_tslips.length);
      setCount('rev-sp-other-count',  sp_other.length);

      if (wantSpouseList){
        if (changed(S.sp_id,sp_id))         { S.sp_id=sp_id;           renderNameList('rev-sp-id-names',     sp_id); }
        if (changed(S.sp_invest,sp_invest)) { S.sp_invest=sp_invest;   renderNameList('rev-sp-invest-names', sp_invest); }
        if (changed(S.sp_t2202,sp_t2202))   { S.sp_t2202=sp_t2202;     renderNameList('rev-sp-t2202-names',  sp_t2202); }
        if (changed(S.sp_tslips,sp_tslips)) { S.sp_tslips=sp_tslips;   renderNameList('rev-sp-tslips-names', sp_tslips); }
        if (changed(S.sp_other,sp_other))   { S.sp_other=sp_other;     renderNameList('rev-sp-other-names',  sp_other); }
      }
    }
  }

  /* ===== gate spouse / spouse-tax / children accordions ===== */
  function gate(baseId, show){
    const btn   = document.getElementById(baseId+'-btn');
    const panel = document.getElementById(baseId);
    if (!btn || !panel) return;
    btn.hidden   = !show;
    panel.hidden = true; // collapsed
    btn.setAttribute('aria-expanded','false');
  }
  function refreshReviewAccordion(){
    const f = flagsSafe();
    gate('rev-spouse',     !!f.marriedLike);
    gate('rev-spouse-tax', !!(f.marriedLike && f.spouseFiles));
    gate('rev-children',   !!f.hasChildren);
  }

  /* ===== pre-rows (date/residing/spouse/children) ===== */
  function updatePreRows(){
    const f = flagsSafe();
    $$('#rev-pre .pre-cond').forEach(el=>{
      const on = !!f.marriedLike;
      if (el.style.display !== (on ? '' : 'none')) el.style.display = on ? '' : 'none';
    });
  }

  /* ===== scheduler ===== */
  let rafPending=false;
  function scheduleSync(){
    if (rafPending) return;
    rafPending=true;
    requestAnimationFrame(()=>{
      rafPending=false;
      refreshReviewAccordion();
      updatePreRows();
      refreshReviewUploads();
      if (typeof window.updateBindings==='function') window.updateBindings();
    });
  }

  /* ===== “Go to …” links (review → form; keep Prev button) ===== */
  on('click', '.rev-link[data-open]', (e, a)=>{
    e.preventDefault();
    let step = a.getAttribute('data-open');

    // route 'upload' smartly (split or unified)
    if (step==='upload'){
      const f = flagsSafe();
      if (document.querySelector('.pi-main[data-panel="upload-self"], .pi-main[data-panel="upload-spouse"]')){
        step = f.spouseFiles ? 'upload-spouse' : 'upload-self';
      }
    }

    if (step==='pre' && window.App?.goToWelcome){
      window.App.goToWelcome(); window.App.updateProgress?.('personal');
      window.scrollTo({top:0,behavior:'smooth'}); return;
    }

    if (window.App?.goToFormAndShow){
      window.App.goToFormAndShow(step);
    } else {
      // safe fallback
      document.querySelectorAll('.pi-main[data-panel]').forEach(p=> p.hidden = (p.dataset.panel!==step));
    }

    // enter review-jump on target panel
    const panelEl = document.querySelector(`.pi-main[data-panel="${step}"]`);
    if (panelEl){
      panelEl.setAttribute('data-review-jump','1');
      window.enterReviewJumpMode();
      window.scrollTo({top:0, behavior:'smooth'});
    }
  });

  /* ===== review-jump: hide only Continue, keep Prev; add “Back to Review” ===== */
  window.enterReviewJumpMode = function(){
    const shown = document.querySelector('.pi-main[data-panel]:not([hidden])');
    if (!shown) return;

    // hide only CONTINUE
    shown.querySelectorAll('.tax-cta .continue-btn').forEach(el=>{
      el.dataset._savedDisplay = el.style.display;
      el.style.display = 'none';
    });
    // ensure PREV visible
    shown.querySelectorAll('.tax-cta [data-goto="prev"]').forEach(el=>{
      if (el.dataset._savedDisplay === undefined) el.dataset._savedDisplay = el.style.display;
      el.style.display = '';
    });

    // add Back to Review (idempotent)
    let back = shown.querySelector('.tax-cta .review-back');
    if (!back){
      const wrap = shown.querySelector('.tax-cta') || shown;
      back = document.createElement('button');
      back.type='button';
      back.className='tax-btn-secondary review-back';
      back.textContent='Back to Review';
      wrap.appendChild(back);
      back.addEventListener('click', ()=>{
        // restore buttons on this panel
        shown.querySelectorAll('.tax-cta .continue-btn, .tax-cta [data-goto="prev"]').forEach(el=>{
          if (el.dataset._savedDisplay !== undefined){
            el.style.display = el.dataset._savedDisplay;
            delete el.dataset._savedDisplay;
          } else { el.style.display=''; }
        });
        shown.querySelectorAll('.review-back').forEach(b=>b.remove());

        // show Review + sync sidebar
        document.querySelectorAll('.pi-main').forEach(p=> p.hidden = true);
        const rev = document.querySelector('.pi-main[data-panel="review"]');
        if (rev) rev.hidden = false;
        window.App?.updateProgress?.('review');
        window.scrollTo({top:0, behavior:'smooth'});
      });
    } else {
      back.style.display='';
    }
  };

  // leaving review-jump via sidebar/mobile: restore CTAs
  $('.pi-steps')?.addEventListener('click', ()=>{
    document.querySelectorAll('.review-back').forEach(b=>b.remove());
    document.querySelectorAll('.tax-cta .continue-btn, .tax-cta [data-goto="prev"]').forEach(el=>{
      if (el.dataset._savedDisplay !== undefined){
        el.style.display = el.dataset._savedDisplay;
        delete el.dataset._savedDisplay;
      } else { el.style.display=''; }
    });
  });
on('click', '.pi-mb-link', ()=>{
    document.querySelectorAll('.review-back').forEach(b=>b.remove());
    document.querySelectorAll('.tax-cta .continue-btn, .tax-cta [data-goto="prev"]').forEach(el=>{
      if (el.dataset._savedDisplay !== undefined){
        el.style.display = el.dataset._savedDisplay;
        delete el.dataset._savedDisplay;
      } else { el.style.display=''; }
    });
  });

  /* ===== listeners ===== */
  document.addEventListener('change', (e)=>{
    const t=e.target;
    if (!(t instanceof HTMLInputElement)) return;
    if (['marital_status','spouseFile','children','spouse_in_canada','selfEmp','gig_income'].includes(t.name)){
      scheduleSync();
    }
  });

  // refresh when upload accordions open/close so lists render only when visible
  ['rev-upload-btn','rev-upload-self-btn','rev-upload-spouse-btn'].forEach(id=>{
    const b=document.getElementById(id); if (b) b.addEventListener('click', scheduleSync);
  });

  // file inputs
  [
    '#app_id_proof','#app_tslips','#app_t2202_receipt','#app_invest','#app_t2200_work','#app_exp_summary','#app_otherdocs',
    '#sp_id_proof','#sp_tslips','#sp_t2202','#sp_invest','#sp_otherdocs',
    '#gig_tax_summary'
  ].forEach(sel=>{ const el=$(sel); if (el) el.addEventListener('change', scheduleSync); });

  // observe only dropzone lists
  [
    '#app_id_list','#app_tslips_list','#app_t2202_receipt_list','#app_invest_list','#app_t2200_work_list','#app_exp_summary_list','#app_otherdocs_list',
    '#sp_id_list','#sp_tslips_list','#sp_t2202_list','#sp_invest_list','#sp_otherdocs_list',
    '#gig-files'
  ].forEach(sel=>{
    const root=$(sel); if (!root || !('MutationObserver' in window)) return;
    const mo=new MutationObserver(()=>scheduleSync()); mo.observe(root,{childList:true,subtree:true});
  });

  /* ===== initial ===== */
  scheduleSync();
})();
</script>



<!-- YES REVIEW 4 — tax subsection visibility -->

<script>
document.addEventListener('DOMContentLoaded', function () {
  if (window.__PI_MOBILEBAR_FINAL) return; window.__PI_MOBILEBAR_FINAL = true;

  var formPanel    = document.getElementById('form-panel');
  var welcomePanel = document.getElementById('welcome-panel');
  if (!formPanel) return;

  // DOM
  var bar         = document.getElementById('pi-mobilebar');
  var drawer      = document.getElementById('pi-mb-drawer');
  var btnBack     = document.getElementById('pi-mb-back');
  var btnToggle   = document.getElementById('pi-mb-toggle');
  var btnClose    = document.getElementById('pi-mb-close');
  var navList     = document.getElementById('pi-mb-nav');
  var stepCountEl = document.getElementById('pi-mb-stepcount');
  var stepTitleEl = document.getElementById('pi-mb-steptitle');
  var progressEl  = document.getElementById('pi-mb-progressbar');
  if (!bar || !drawer || !navList || !stepCountEl || !stepTitleEl || !progressEl) return;

  // --- never let Back have data-goto (prevents the main app’s generic [data-goto] handler from firing)
  btnBack.removeAttribute('data-goto');
  new MutationObserver(m=>m.forEach(x=>{
    if (x.attributeName==='data-goto') btnBack.removeAttribute('data-goto');
  })).observe(btnBack,{attributes:true});

  // ORDER from sidebar (fallback included)
  var ORDER = (function(){
    var arr = [];
    document.querySelectorAll('.pi-steps [data-step]').forEach(function(a){
      var k = a.getAttribute('data-step'); if (k && k !== 'pre') arr.push(k);
    });
    return arr.length ? arr :
      ['personal','tax','spouse','spouse-tax','children','other-income','upload-self','upload-spouse','review','confirm'];
  })();

  // Title map
  var TITLE_MAP = (function(){
    var m = {};
    document.querySelectorAll('.pi-steps [data-step]').forEach(function(a){
      var k = a.getAttribute('data-step'); if (k && k !== 'pre') m[k] = a.textContent.trim();
    });
    m['personal']      = m['personal']      || 'Personal information';
    m['tax']           = m['tax']           || 'Tax Filing Information';
    m['spouse']        = m['spouse']        || 'Spouse Information';
    m['spouse-tax']    = m['spouse-tax']    || 'Spouse Tax Filing Information';
    m['children']      = m['children']      || 'Children Information';
    m['other-income']  = m['other-income']  || 'Other Income';
    m['upload-self']   = m['upload-self']   || 'Add/Upload Documents (Applicant)';
    m['upload-spouse'] = m['upload-spouse'] || 'Spouse Add/Upload Documents';
    m['review']        = m['review']        || 'Review Information';
    m['confirm']       = m['confirm']       || 'Confirmation of Document Submission';
    return m;
  })();
  function titleFor(k){ return TITLE_MAP[k] || k; }

  // Local gating mirror if App.activeSteps() not available
  function getVal(name){ var el = document.querySelector('input[name="'+name+'"]:checked'); return el ? el.value : null; }
  function localFlags(){
    var ms = getVal('marital_status');
    var marriedLike = (ms === 'Married' || ms === 'Common Law');
    var childQ = marriedLike || ms === 'Separated' || ms === 'Divorced' || ms === 'Widowed';
    var spouseFiles = marriedLike && (getVal('spouseFile') === 'yes');
    var hasChildren = childQ && (getVal('children') === 'yes');
    return { marriedLike:marriedLike, spouseFiles:spouseFiles, hasChildren:hasChildren };
  }
  function activeSteps(){
    try {
      if (window.App && typeof window.App.activeSteps === 'function') {
        var s = window.App.activeSteps(); if (Array.isArray(s) && s.length) return s;
      }
    } catch(e){}
    var f = localFlags(), out=[];
    for (var i=0;i<ORDER.length;i++){
      var step = ORDER[i];
      if (step==='spouse'        && !f.marriedLike) continue;
      if (step==='spouse-tax'    && !f.spouseFiles) continue;
      if (step==='children'      && !f.hasChildren) continue;
      if (step==='upload-spouse' && !f.spouseFiles) continue;
      out.push(step);
    }
    return out;
  }

  // Drawer
  function openDrawer(){ drawer.hidden=false; btnToggle.setAttribute('aria-expanded','true'); document.documentElement.style.overflow='hidden'; }
  function closeDrawer(){ drawer.hidden=true;  btnToggle.setAttribute('aria-expanded','false'); document.documentElement.style.overflow=''; }
  drawer.addEventListener('click', function(e){ if (e.target===drawer) closeDrawer(); });
  // make toggle unmissable
  btnToggle.addEventListener('click', function(e){ e.preventDefault(); e.stopPropagation(); drawer.hidden ? openDrawer() : closeDrawer(); }, true);
  btnClose && btnClose.addEventListener('click', closeDrawer);

  // Current panel key
  function currentKey(){
    var el = formPanel.querySelector('.pi-main[data-panel]:not([hidden])');
    return el ? el.getAttribute('data-panel') : (ORDER[0] || 'personal');
  }

  // Drawer items
  function renderDrawer(curr){
    var steps   = activeSteps();
    var currIdx = steps.indexOf(curr);
    var html    = '';

    for (var i=0;i<steps.length;i++){
      var key       = steps[i];
      var isCurrent = (key === curr);
      var isDone    = (typeof isPanelCompleted === 'function') ? isPanelCompleted(key) : (i < currIdx);
      var canAccess = (typeof areAllPreviousStepsCompleted === 'function') ? areAllPreviousStepsCompleted(key) : (i <= currIdx);

      // Match desktop gating: only current, completed, or “all prev completed” steps are clickable
      var isClickable = !!(isCurrent || isDone || canAccess);

      var cls =
        isDone && !isCurrent ? 'is-done' :
        isCurrent           ? 'is-current' :
                              'is-future';

      html += '<button type="button" class="pi-mb-link '+cls+'"'
           +  (isClickable ? ' data-goto="'+key+'"' : ' aria-disabled="true" tabindex="-1"')
           +  '>'+titleFor(key)+'</button>';
    }

    navList.innerHTML = html;
  }

  // Header + Back behavior (no data-goto)
  function updateHeader(curr){
    var steps = activeSteps();
    
    // Ensure 'confirm' is always included as the last step
    if (steps.indexOf('confirm') === -1) {
      steps.push('confirm');
    }
    
    var idx = steps.indexOf(curr);
    
    // If on confirm page, always show as last step (total of total)
    if (curr === 'confirm') {
      idx = steps.length - 1;
    } else {
      idx = Math.max(0, idx);
    }
    
    var total = steps.length;

    if (idx === 0) {
      btnBack.dataset.action = 'welcome';
      btnBack.removeAttribute('data-goto');
      btnBack.setAttribute('aria-label','Back to pre-details');
    } else {
      btnBack.dataset.action = 'prev';
      btnBack.removeAttribute('data-goto');
      btnBack.setAttribute('aria-label','Back');
    }

    stepCountEl.textContent = (idx+1) + ' of ' + total;
    stepTitleEl.textContent = titleFor(curr);
    progressEl.style.width  = (((idx+1)/total)*100) + '%';

    renderDrawer(curr);
  }

  function syncBarVisibility(){
    var isWelcomeVisible = !!(welcomePanel && welcomePanel.style && welcomePanel.style.display !== 'none');
    bar.hidden = isWelcomeVisible;
  }

  function repaintSoon(){
    setTimeout(function(){ syncBarVisibility(); updateHeader(currentKey()); }, 0);
  }

  // Back button: single authoritative handler (capture + lock so nothing else runs)
  let backLock = false;
  btnBack.addEventListener('click', function(e){
    e.preventDefault(); e.stopPropagation(); if (e.stopImmediatePropagation) e.stopImmediatePropagation();
    if (backLock) return; backLock = true; setTimeout(()=>backLock=false, 250);
    closeDrawer();

    var action = btnBack.dataset.action || 'prev';

    if (action === 'welcome') {
      if (window.App && typeof window.App.goToWelcome === 'function') window.App.goToWelcome();
      document.dispatchEvent(new CustomEvent('pi:panel-changed', { detail:{ panel:'personal' }}));
      return;
    }

    var curr  = currentKey();
    var steps = (window.App && typeof window.App.activeSteps === 'function') ? window.App.activeSteps() : activeSteps();
    var i     = Math.max(0, steps.indexOf(curr));
    var prev  = steps[Math.max(i-1, 0)] || curr;

    if (window.App && typeof window.App.goToFormAndShow === 'function') window.App.goToFormAndShow(prev);
    document.dispatchEvent(new CustomEvent('pi:panel-changed', { detail:{ panel: prev }}));
  }, true); // capture

  // Drawer link clicks
  navList.addEventListener('click', function(e){
    var b = e.target.closest('.pi-mb-link[data-goto]'); if (!b) return;
    e.preventDefault(); e.stopPropagation(); closeDrawer();
    var key = b.getAttribute('data-goto');
    if (key === 'pre' || key === 'welcome') {
      window.App && window.App.goToWelcome && window.App.goToWelcome();
      window.App && window.App.updateProgress && window.App.updateProgress('personal');
    } else {
      if (key === 'upload') {
        var f = (window.App && window.App.flags) ? window.App.flags() : localFlags();
        key = f.spouseFiles ? 'upload-spouse' : 'upload-self';
      }
      window.App && window.App.goToFormAndShow && window.App.goToFormAndShow(key);
    }
    document.dispatchEvent(new CustomEvent('pi:panel-changed', { detail:{ panel: key }}));
  }, true);

  // Repaint when answers change the flow
  document.addEventListener('change', function(e){
    var t=e.target;
    if (!t || t.tagName!=='INPUT' || t.type!=='radio') return;
    var watched = { marital_status:1, spouseFile:1, children:1, spouse_in_canada:1 };
    if (!watched[t.name]) return;
    repaintSoon();
  });

  // React to global panel-change notifications (from your PATCH)
  document.addEventListener('pi:panel-changed', repaintSoon);

  // Entering form from welcome
  document.getElementById('qs-continue')?.addEventListener('click', repaintSoon);

  // Initial paint
  syncBarVisibility();
  updateHeader(currentKey());
});
</script>

<!-- YES REVIEW 4B — RENT ADDRESS COUNT (classic) -->

<script>
(function(){
  if (window.__REV_RENT_COUNT) return; window.__REV_RENT_COUNT = true;

  function visibleRentRows(){
    const tbody = document.getElementById('rent-tbody'); if (!tbody) return [];
    return Array.from(tbody.querySelectorAll(':scope > tr')).filter(tr =>
      tr.id!=='rent-empty-row' && !tr.hidden && !tr.matches('.is-template,[aria-hidden="true"]')
    );
  }
  function paint(){
    const el = document.getElementById('rent-row-count'); if (!el) return;
    el.textContent = String(visibleRentRows().length);
  }

  paint();
  document.addEventListener('input', e=>{ if (e.target.closest('#rent-tbody')) paint(); });
  document.addEventListener('click', e=>{
    if (e.target.closest('#rent-tbody [data-action], #btn-add-rent')) setTimeout(paint,0);
  });

  const tb = document.getElementById('rent-tbody');
  if (tb && 'MutationObserver' in window){
    const mo=new MutationObserver(()=>paint());
    mo.observe(tb,{childList:true,subtree:false,attributes:true});
  }
})();
</script>

<!-- YES REVIEW 5 — spouse section visibility -->

<script>
(function(){
  function getRadio(name){
    const el = document.querySelector(`input[name="${name}"]:checked`);
    return (el ? (el.value || '') : '').toString().trim().toLowerCase();
  }
  function isYes(name){
    const v = getRadio(name);
    return v === 'y' || v === 'yes' || v === 'true' || v === '1';
  }
  const $ = (sel) => document.querySelector(sel);
  function show(el, on){ if (el) el.hidden = !on; }

  function refreshSpouseReview(){
    const inCanada   = isYes('spouse_in_canada');
    const spouseFile = isYes('spouseFile');
    const addrSame   = getRadio('spouse_address_same') !== 'no';

    show($('.rs-foreign'), !inCanada);
    show($('.rs-canada'),  inCanada);
    show($('.rs-income'),  !spouseFile);

    show($('.rs-addr-same'),   inCanada && addrSame);
    show($('.rs-addr-fields'), inCanada && !addrSame);

    const sameText = $('#rs-addr-same-text');
    if (sameText) sameText.textContent = 'Same as your address';
  }

  function refreshSpouseTaxReview(){
    const firstTime = isYes('sp_first_time');
    const movedProv = isYes('sp_moved_province');
    show($('.rst-first'),  firstTime);
    show($('.rst-prior'), !firstTime);
    show($('.rst-moved'),  movedProv);
  }

  function syncSpouse(){
    refreshSpouseReview();
    refreshSpouseTaxReview();
    if (typeof window.updateBindings === 'function') window.updateBindings();
  }
  syncSpouse();

  document.addEventListener('change', (e)=>{
    const t = e.target;
    if (!(t instanceof HTMLInputElement)) return;
    if ([
      'spouse_in_canada','spouseFile','spouse_address_same',
      'sp_first_time','sp_paragon_prior','sp_moved_province'
    ].includes(t.name)){
      syncSpouse();
    }
  });
})();
</script>


<!-- YES REVIEW 6 — children table mirror into review -->

<script>
(function ChildrenReviewClassic(){
  if (window.__REV_CHILDREN_CLASSIC) return; window.__REV_CHILDREN_CLASSIC = true;

  const $  = s => document.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const T  = s => (s||'').replace(/\s+/g,' ').trim();

  const rowFilter = tr =>
    tr && tr.nodeName==='TR' &&
    !tr.matches('#children-empty-row,[hidden],.is-empty,.is-template,[aria-hidden="true"]');

  function getRows(){
    const tbody = $('#children-tbody'); if (!tbody) return [];
    return $$('#children-tbody > tr').filter(rowFilter);
  }

  function paint(){
    const out   = $('#children-list');      // review table body
    const cntEl = $('#children-count');     // number beside “Saved children”
    if (!out || !cntEl) return;

    const rows = getRows();
    cntEl.textContent = String(rows.length);

    out.innerHTML = '';
    rows.forEach(src=>{
      const c = src.cells || src.querySelectorAll('td');
      const first = T(c[0]?.textContent);
      const last  = T(c[1]?.textContent);
      const dob   = T(c[2]?.textContent);   // keep original formatted DOB (e.g., "May | 06 | 2019")
      const inca  = T(c[3]?.textContent);
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${first||'—'}</td><td>${last||'—'}</td><td>${dob||'—'}</td><td>${inca||'—'}</td>`;
      out.appendChild(tr);
    });

    // show the list block only if user answered Yes and there are rows
    const yn = document.querySelector('input[name="children"]:checked');
    const hasChildren = (yn && /^(yes|y|true|1)$/i.test(yn.value||''));
    const listWrap = document.querySelector('.rc-list');
    if (listWrap) listWrap.hidden = !(hasChildren && rows.length>0);
  }

  // initial
  paint();

  // changes
  document.addEventListener('change', e=>{
    const t=e.target;
    if (t instanceof HTMLInputElement && (t.name==='children')) paint();
  });
  document.addEventListener('input', e=>{
    if (e.target.closest('#children-tbody')) paint();
  });
  document.addEventListener('click', e=>{
    if (
      e.target.closest('#btn-add-child') ||
      e.target.closest('#children-tbody [data-action]') ||
      e.target.closest('#children-tbody a')
    ){
      setTimeout(paint,0);
    }
  });

  // stay synced with DOM mutations
  const srcTbody = $('#children-tbody');
  if (srcTbody && 'MutationObserver' in window){
    const mo = new MutationObserver(()=>paint());
    mo.observe(srcTbody, {childList:true, subtree:false, attributes:true});
  }
})();
</script>



<!-- YES REVIEW 7 — Accordion CORE + Upload split gating + precise upload routing (no duplicate counters here) -->

<script>
(function(){
  if (window.__REV_UPLOAD_SPLIT_INIT) return; window.__REV_UPLOAD_SPLIT_INIT = true;

  /* ===== Accordion CORE (single source of truth) ===== */
  if (!window.__REV_ACCORDION_CORE){
    window.__REV_ACCORDION_CORE = true;

    function toggle(btn){
      const id = btn.getAttribute('aria-controls');
      const panel = document.getElementById(id);
      if (!panel) return;
      const expanded = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!expanded));
      panel.hidden = expanded;
    }
    // Sync initial aria-expanded with [hidden]
    document.querySelectorAll('.rev-item[aria-controls]').forEach(btn=>{
      const id = btn.getAttribute('aria-controls');
      const panel = document.getElementById(id);
      if (panel) btn.setAttribute('aria-expanded', String(!panel.hidden));
    });
    const handler = (e)=>{
      const isKey = (e.type === 'keydown');
      if (isKey && e.key !== 'Enter' && e.key !== ' ') return;
      const btn = e.target.closest('.rev-item[aria-controls]');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();
      if (e.stopImmediatePropagation) e.stopImmediatePropagation();
      toggle(btn);
    };
    document.addEventListener('click',   handler, true);
    document.addEventListener('keydown', handler, true);
  }

  /* ===== Flags (safe) ===== */
  function getVal(name){
    var el = document.querySelector('input[name="'+name+'"]:checked');
    return el ? el.value : null;
  }
  function flagsSafe(){
    try { if (window.App && typeof window.App.flags==='function') return window.App.flags(); }
    catch(e){}
    var ms = getVal('marital_status');
    var marriedLike = (ms === 'Married' || ms === 'Common Law');
    var spouseFiles = marriedLike && (getVal('spouseFile') === 'yes');
    return { marriedLike, spouseFiles };
  }

  /* ===== Gate spouse upload accordion entirely ===== */
  function gateUploadSpouse(show){
    var btn   = document.getElementById('rev-upload-spouse-btn');
    var panel = document.getElementById('rev-upload-spouse');
    if (!btn || !panel) return;
    btn.hidden   = !show;
    panel.hidden = true;
    btn.setAttribute('aria-expanded','false');
  }

  function refreshSplitGate(){
    var f = flagsSafe();
    gateUploadSpouse(!!f.spouseFiles);
  }

  // React to radios that affect gating
  document.addEventListener('change', function(e){
    var t = e.target;
    if (!(t instanceof HTMLInputElement)) return;
    if (['marital_status','spouseFile'].includes(t.name)){
      refreshSplitGate();
    }
  });

  /* ===== “Go to Uploads” links — precise routing ===== */
  function hasPanel(key){ return !!document.querySelector('.pi-main[data-panel="'+key+'"]'); }
  function showPanelKey(key){
    if (window.App && typeof window.App.goToFormAndShow==='function'){
      window.App.goToFormAndShow(key);
      window.scrollTo({top:0, behavior:'smooth'});
      return true;
    }
    var ok = false;
    document.querySelectorAll('.pi-main[data-panel]').forEach(function(p){
      var on = (p.getAttribute('data-panel') === key);
      p.hidden = !on; if (on) ok = true;
    });
    if (ok) window.scrollTo({top:0, behavior:'smooth'});
    return ok;
  }
  function tryLegacyUpload(target){ // 'app' or 'sp'
    var uploadPanel = document.querySelector('.pi-main[data-panel="upload"]');
    if (!uploadPanel) return false;

    var secId = (target==='app') ? 'upload-applicant' : 'upload-spouse';
    var sec = document.getElementById(secId);
    if (sec){ uploadPanel.hidden = false; sec.scrollIntoView({behavior:'smooth', block:'start'}); return true; }

    var btnId = (target==='app') ? 'tab-applicant' : 'tab-spouse';
    var btn = document.getElementById(btnId);
    if (btn){ uploadPanel.hidden = false; btn.click(); return true; }

    uploadPanel.hidden = false;
    return true;
  }

  // Capture clicks for the specific upload links to avoid double handlers
  document.addEventListener('click', function(e){
    var link = e.target.closest('.rev-link[data-open="upload-self"], .rev-link[data-open="upload-spouse"]');
    if (!link) return;
    e.preventDefault(); e.stopPropagation(); if (e.stopImmediatePropagation) e.stopImmediatePropagation();

    var targetKey = (link.getAttribute('data-open') === 'upload-spouse') ? 'upload-spouse' : 'upload-self';
    var ok = false;

    if (hasPanel(targetKey)) ok = showPanelKey(targetKey);
    if (!ok) ok = tryLegacyUpload(targetKey === 'upload-spouse' ? 'sp' : 'app');

    if (ok && typeof window.enterReviewJumpMode === 'function'){
      setTimeout(window.enterReviewJumpMode, 0);
    }
  }, true);

  // Initial
  refreshSplitGate();
})();
</script>




<script>
(function(){
  function setOffsets(){
    const header = document.querySelector('.mini-header') || document.querySelector('header');
    const bar = document.getElementById('pi-mobilebar');
    const hh = header ? header.offsetHeight : 90;
    const bh = bar ? bar.offsetHeight : 56;
    document.documentElement.style.setProperty('--mh-h', hh + 'px');
    document.documentElement.style.setProperty('--bar-h', bh + 'px');
    document.documentElement.style.setProperty('--mb-top', (hh + bh) + 'px');
  }
  setOffsets();
  window.addEventListener('load', setOffsets);
  window.addEventListener('resize', setOffsets);
})();
</script>

<script>
(function(){
  const dl = document.getElementById('country-list');
  const inputs = document.querySelectorAll('input[list="country-list"]');

  // Fallback list (used if API fails or offline)
  const FALLBACK_COUNTRIES = [
    "Canada","United States","Philippines","United Kingdom","Australia","New Zealand",
    "India","Singapore","Malaysia","Indonesia","Thailand","Vietnam","Japan","South Korea",
    "China","Hong Kong","Taiwan","United Arab Emirates","Saudi Arabia","Qatar",
    "Mexico","Brazil","Argentina","Chile","Colombia","Peru","Spain","France","Germany",
    "Italy","Netherlands","Belgium","Sweden","Norway","Denmark","Finland","Ireland",
    "Switzerland","Austria","Portugal","Greece","Turkey","South Africa","Kenya","Nigeria",
    "Egypt","Morocco"
  ];

  // Fill datalist options
  function populate(list){
    if (!dl) return;
    const frag = document.createDocumentFragment();
    list.forEach(name => {
      const opt = document.createElement('option');
      opt.value = name;
      frag.appendChild(opt);
    });
    dl.innerHTML = "";
    dl.appendChild(frag);
  }

  // Fetch countries from REST Countries API
  async function loadCountries(){
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 3500); // 3.5s timeout
    try{
      const res = await fetch('https://restcountries.com/v3.1/all?fields=name', { signal: controller.signal });
      clearTimeout(timeout);
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const data = await res.json();
      const names = data
        .map(c => c?.name?.common)
        .filter(Boolean)
        .sort((a,b)=> a.localeCompare(b));

      // Pin some common countries to the top
      const pinned = ["Canada","United States","Philippines","United Kingdom","Australia"];
      const pinSet = new Set(pinned);
      const top = names.filter(n => pinSet.has(n));
      const rest = names.filter(n => !pinSet.has(n));
      populate([...top, ...rest]);
    } catch(e){
      // Fallback if API fails
      populate(FALLBACK_COUNTRIES.sort((a,b)=> a.localeCompare(b)));
    }
  }

  // Initialize
  if (dl) loadCountries();

  // Optional: light validation (warns if typed country not in list)
  inputs.forEach(input => {
    input.addEventListener('change', ()=>{
      const val = (input.value || '').trim();
      const options = Array.from(dl?.options || []).map(o => o.value);
      if (val && !options.includes(val)){
        // optional: show a small hint or toast here
        // console.warn('Unknown country:', val);
      }
    });
  });
})();
</script>


<script>
(function(){
  // Normalize strings for safe comparisons
  function norm(s){
    return (s ?? "").toString().trim().toLowerCase();
  }

  // Ensure a <select> reflects a default value if provided in data-value
  function applyDataValue(sel){
    const dv = sel.getAttribute('data-value');
    if (dv && !sel.value) {
      sel.value = dv;
    }
  }

  // Disable matching option in the opposite select
  function syncPair(selA, selB){
    if (!selA || !selB) return;

    // If the form (or PHP) uses data-value, apply it first
    applyDataValue(selA);
    applyDataValue(selB);

    const aVal = norm(selA.value);
    const bVal = norm(selB.value);

    // Re-enable everything first (except placeholder)
    Array.from(selA.options).forEach(opt => { if (norm(opt.value) !== "") opt.disabled = false; });
    Array.from(selB.options).forEach(opt => { if (norm(opt.value) !== "") opt.disabled = false; });

    // Disable A's value in B
    if (aVal){
      Array.from(selB.options).forEach(opt => {
        const ov = norm(opt.value || opt.text);
        if (ov === aVal) opt.disabled = true;
      });
    }

    // Disable B's value in A
    if (bVal){
      Array.from(selA.options).forEach(opt => {
        const ov = norm(opt.value || opt.text);
        if (ov === bVal) opt.disabled = true;
      });
    }

    // If a now-disabled option is currently selected, clear it
    if (selA.selectedOptions[0] && selA.selectedOptions[0].disabled){
      selA.value = "";
    }
    if (selB.selectedOptions[0] && selB.selectedOptions[0].disabled){
      selB.value = "";
    }
  }

  // Wire a pair with change + programmatic updates
  function linkPair(idFrom, idTo){
    const selFrom = document.getElementById(idFrom);
    const selTo   = document.getElementById(idTo);
    if (!selFrom || !selTo) return;

    const sync = () => syncPair(selFrom, selTo);

    // Initial sync
    sync();

    // User changes
    selFrom.addEventListener('change', sync);
    selTo  .addEventListener('change',  sync);

    // In case values are set later by other scripts
    let lastAF = selFrom.value, lastBT = selTo.value;
    setInterval(() => {
      if (selFrom.value !== lastAF || selTo.value !== lastBT){
        lastAF = selFrom.value; lastBT = selTo.value;
        sync();
      }
    }, 250);
  }

  // Applicant
  linkPair('prov_from','prov_to');
  // Spouse
  linkPair('sp_prov_from','sp_prov_to');
})();
</script>

<script>
(function(){
  const ids = ['prov_from','prov_to','sp_prov_from','sp_prov_to'];
  ids.forEach(id=>{
    const sel = document.getElementById(id);
    if (!sel) return;

    // if it was hidden by xsel, make it visible
    sel.classList.remove('xsel-native');

    // if it sits inside an .xsel-wrap from earlier, unwrap it
    const wrap = sel.closest('.xsel-wrap');
    if (wrap) {
      wrap.parentNode.insertBefore(sel, wrap);
      wrap.remove();
    }
  });
})();
</script>

<!-- OVERRIDE: review-jump shows ONLY “Back to Review” -->
<script>
(function(){
  if (window.__REV_ONLY_BACK) return; window.__REV_ONLY_BACK = true;

  /* ---------- CSS: in review-jump, show only Back to Review ---------- */
  (function injectOnlyBackCSS(){
    const css = `
      /* Hide ALL native nav CTAs when opened from Review */
      .pi-main[data-review-jump="1"] .tax-cta [data-goto="next"],
      .pi-main[data-review-jump="1"] .tax-cta [data-goto="prev"],
      .pi-main[data-review-jump="1"] .tax-cta [data-goto="welcome"],
      .pi-main[data-review-jump="1"] .tax-cta .continue-btn { display:none !important; }

      /* Back-to-Review only visible in review-jump */
      .pi-main .tax-cta .review-back { display:none; }
      .pi-main[data-review-jump="1"] .tax-cta .review-back { display:inline-flex !important; }
    `;
    const s = document.createElement('style'); s.textContent = css; document.head.appendChild(s);
  })();

  /* ---------- Helpers to hide/restore native CTAs (defensive) ---------- */
  function hideNativeCTAs(scope){
    (scope||document).querySelectorAll('.tax-cta [data-goto="next"], .tax-cta [data-goto="prev"], .tax-cta [data-goto="welcome"], .tax-cta .continue-btn')
      .forEach(el=>{
        if (el.dataset._savedDisplay === undefined) el.dataset._savedDisplay = el.style.display || '';
        el.style.display = 'none';
      });
  }
  function restoreNativeCTAs(scope){
    (scope||document).querySelectorAll('.tax-cta [data-goto="next"], .tax-cta [data-goto="prev"], .tax-cta [data-goto="welcome"], .tax-cta .continue-btn')
      .forEach(el=>{
        if (el.dataset._savedDisplay !== undefined){
          el.style.display = el.dataset._savedDisplay;
          delete el.dataset._savedDisplay;
        }
      });
  }

  /* ---------- Create the single Back-to-Review button ---------- */
  function ensureBackButton(panelEl){
  const cta = panelEl.querySelector('.tax-cta') || panelEl;
  let back = cta.querySelector('.review-back');
  if (!back){
    back = document.createElement('button');
    back.type = 'button';
    back.className = 'tax-btn-secondary review-back';
    back.textContent = 'Back to Review';
    cta.appendChild(back);
    back.addEventListener('click', ()=>{

      // leave review-jump and clean up buttons
      document.querySelectorAll('.pi-main[data-review-jump="1"]').forEach(p=>p.removeAttribute('data-review-jump'));
      document.querySelectorAll('.review-back').forEach(b=>b.remove());

      // show Review panel
      document.querySelectorAll('.pi-main').forEach(p=> p.hidden = true);
      const rev = document.querySelector('.pi-main[data-panel="review"]');
      if (rev) rev.hidden = false;

      // keep sidebar in sync
      window.App?.updateProgress?.('review');

      // 🔧 tell the mobile header we’re on "review"
      document.dispatchEvent(new CustomEvent('pi:panel-changed', { detail: { panel: 'review' } }));

      window.scrollTo({top:0, behavior:'smooth'});
    });
  } else {
    back.style.display = '';
  }
}

  /* ---------- Public hook used by Review “Go to …” links ---------- */
  window.enterReviewJumpMode = function(panelEl){
    if (!panelEl){
      panelEl = document.querySelector('.pi-main[data-panel]:not([hidden])');
    }
    if (!panelEl) return;
    panelEl.setAttribute('data-review-jump','1');
    ensureBackButton(panelEl);
  };

  /* ---------- Wire Review links ---------- */
  document.addEventListener('click', (e)=>{
    const a = e.target.closest('.rev-link[data-open]');
    if (!a) return;
    e.preventDefault();

    let step = a.getAttribute('data-open');

    // smart route legacy "upload" to split pages if present
    if (step === 'upload'){
      const hasSplit = document.querySelector('.pi-main[data-panel="upload-self"], .pi-main[data-panel="upload-spouse"]');
      if (hasSplit){
        let spouseFiles = false;
        try { spouseFiles = !!window.App?.flags()?.spouseFiles; } catch(e){}
        step = spouseFiles ? 'upload-spouse' : 'upload-self';
      }
    }

    if (step === 'pre' && window.App?.goToWelcome){
      window.App.goToWelcome();
      window.App.updateProgress?.('personal');
      return;
    }

    if (window.App?.goToFormAndShow){
      window.App.goToFormAndShow(step);
    } else {
      document.querySelectorAll('.pi-main[data-panel]').forEach(p=> p.hidden = (p.dataset.panel !== step));
    }

    const panelEl = document.querySelector(`.pi-main[data-panel="${step}"]`);
    if (panelEl){
      window.enterReviewJumpMode(panelEl);
      window.scrollTo({top:0, behavior:'smooth'});
    }
  }, true);

  /* ---------- Clean up review-jump if user navigates via sidebar/mobile ---------- */
  function leaveReviewJumpEverywhere(){
    document.querySelectorAll('.review-back').forEach(b=>b.remove());
    document.querySelectorAll('.pi-main[data-review-jump="1"]').forEach(p=>p.removeAttribute('data-review-jump'));
    restoreNativeCTAs(document);
  }
  document.querySelector('.pi-steps')?.addEventListener('click', leaveReviewJumpEverywhere);
  document.addEventListener('click', (e)=>{
    if (e.target.closest('.pi-mb-link, #pi-mb-back')) leaveReviewJumpEverywhere();
  });

})();
</script>

<script id="pi-mobilebar-offset-js">
(function(){
  function headerEl(){
    // CHANGE selectors if your header has a different class
    return document.querySelector('.mini-header') || document.querySelector('header');
  }
  function setHeaderOffset(){
    var h = headerEl() ? Math.round(headerEl().getBoundingClientRect().height) : 0;
    document.documentElement.style.setProperty('--pi-header-offset', h + 'px');
  }

  setHeaderOffset();
  window.addEventListener('resize', setHeaderOffset);

  // Recalculate & close the drawer when crossing the breakpoint
  var mq = window.matchMedia('(max-width: 959px)');
  function onMQChange(){
    setHeaderOffset();
    var drawer = document.getElementById('pi-mb-drawer');
    var toggle = document.getElementById('pi-mb-toggle');
    if (drawer) drawer.hidden = true;
    if (toggle) toggle.setAttribute('aria-expanded','false');
  }
  if (mq.addEventListener) mq.addEventListener('change', onMQChange);
  else mq.addListener(onMQChange); // older Safari
})();
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
  // Helpers
  const show = (el, on) => { 
    if (el) {
      el.style.display = on ? '' : 'none';
      // Clear validation errors for all fields in this element when hidden
      if (!on && typeof hideFieldError === 'function') {
        el.querySelectorAll('input, select, textarea').forEach(field => {
          if (field.name) {
            hideFieldError(field.name);
          }
        });
      }
    }
  };
  const req  = (el, on) => { 
    if (!el) return;
    on ? el.setAttribute('required','required') : el.removeAttribute('required');
    // Clear validation errors when field becomes non-required
    if (!on && el.name && typeof hideFieldError === 'function') {
      hideFieldError(el.name);
    }
  };

  // Spouse radios
  const spGigYes = document.getElementById('sp_gig_income_yes');
  const spGigNo  = document.getElementById('sp_gig_income_no');

  const spExpBlock = document.getElementById('sp-gig-expenses-block');
  const spExpText  = document.getElementById('sp_gig_expenses_summary');

  const spHstQ     = document.getElementById('sp-hst-q-block');
  const spHstYes   = document.getElementById('sp_hst_yes');
  const spHstNo    = document.getElementById('sp_hst_no');
  const spHstWrap  = document.getElementById('sp-hst-fields');
  const spHstNum   = document.getElementById('sp_hst_number');
  const spHstAcc   = document.getElementById('sp_hst_access');
  const spHstStart = document.getElementById('sp_hst_start');
  const spHstEnd   = document.getElementById('sp_hst_end');

  // Spouse upload (in Upload–Spouse panel)
  const spUpload   = document.getElementById('sp-upload-gig-section');
  const spDz       = document.getElementById('sp-gig-drop');
  const spDzInput  = document.getElementById('sp_gig_tax_summary');
  const spDzBtn    = document.getElementById('sp-gig-browse');
  const spDzList   = document.getElementById('sp-gig-files');

  function applySpouseGig(){
    const on = !!spGigYes?.checked;
    show(spExpBlock, on);
    show(spHstQ, on);
    req(spExpText, on);

    if (!on){
      show(spHstWrap, false);
      [spHstNum, spHstAcc, spHstStart, spHstEnd].forEach(el => req(el,false));
    }

    // Keep spouse upload section in sync too (useful if user jumps to Upload tab)
    show(spUpload, on);
  }

  function applySpouseHst(){
    const on = !!spGigYes?.checked && !!spHstYes?.checked;
    show(spHstWrap, on);
    [spHstNum, spHstAcc, spHstStart, spHstEnd].forEach(el => req(el,on));
  }

  // Wire events
  [spGigYes, spGigNo].forEach(r => r?.addEventListener('change', () => { applySpouseGig(); applySpouseHst(); }));
  [spHstYes, spHstNo].forEach(r => r?.addEventListener('change', applySpouseHst));

  // Init
  applySpouseGig();
  applySpouseHst();

  // Simple dropzone for spouse
  spDzBtn?.addEventListener('click', () => spDzInput?.click());
  spDz?.addEventListener('dragover', e => { e.preventDefault(); spDz.classList.add('dragover'); });
  spDz?.addEventListener('dragleave', () => spDz.classList.remove('dragover'));
  spDz?.addEventListener('drop', e => {
    e.preventDefault(); spDz.classList.remove('dragover');
    if (!spDzInput) return;
    spDzInput.files = e.dataTransfer.files;
    listSpFiles();
  });
  spDzInput?.addEventListener('change', listSpFiles);

  function listSpFiles(){
    if (!spDzList || !spDzInput) return;
    spDzList.innerHTML = '';
    const files = spDzInput.files || [];
    if (!files.length) return;
    const ul = document.createElement('ul');
    ul.style.margin = '8px 0 0';
    ul.style.padding = '0 0 0 18px';
    for (const f of files) {
      const li = document.createElement('li');
      li.textContent = `${f.name} (${Math.round(f.size/1024)} KB)`;
      ul.appendChild(li);
    }
    spDzList.appendChild(ul);
  }

  // Expose a tiny hook so showPanel can repaint when landing on Upload–Spouse
  window.App = window.App || {};
  window.App.applySpouseGigUpload = applySpouseGig;
});
</script>

<script>
(function(){
  // Safe helpers
  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));
  const show = (el, on) => { if (el) el.style.display = on ? '' : 'none'; };
  const req  = (el, on) => { if (!el) return; on ? el.setAttribute('required','required') : el.removeAttribute('required'); };

  // --- Elements (spouse intent to file)
  const spFileYes = $('#spouse_yes');
  const spFileNo  = $('#spouse_no');

  // --- The wrapper that contains the spouse gig question block
  // Wrap your spouse gig question HTML inside <div id="sp-gig-question"> ... </div>
  const spGigQ    = $('#sp-gig-question');

  // --- Spouse gig Q + fields
  const spGigYes   = $('#sp_gig_income_yes');
  const spGigNo    = $('#sp_gig_income_no');

  const spExpBlock = $('#sp-gig-expenses-block');
  const spExpText  = $('#sp_gig_expenses_summary');

  const spHstQ     = $('#sp-hst-q-block');
  const spHstYes   = $('#sp_hst_yes');
  const spHstNo    = $('#sp_hst_no');

  const spHstWrap  = $('#sp-hst-fields');
  const spHstNum   = $('#sp_hst_number');
  const spHstAcc   = $('#sp_hst_access');
  const spHstStart = $('#sp_hst_start');
  const spHstEnd   = $('#sp_hst_end');

  // --- Spouse upload section (inside data-panel="upload-spouse")
  const spUpload   = $('#sp-upload-gig-section');

  function applySpouseGig(){
    const hasGig = !!spGigYes?.checked;
    show(spExpBlock, hasGig);
    show(spHstQ, hasGig);
    req(spExpText, hasGig);

    if (!hasGig){
      show(spHstWrap, false);
      [spHstNum, spHstAcc, spHstStart, spHstEnd].forEach(el => req(el, false));
    }
    // keep Upload–Spouse extra upload section in sync
    show(spUpload, hasGig);
  }

  function applySpouseHst(){
    const on = !!spGigYes?.checked && !!spHstYes?.checked;
    show(spHstWrap, on);
    [spHstNum, spHstAcc, spHstStart, spHstEnd].forEach(el => req(el, on));
  }

  function gateSpouseGigQuestion(){
    const wantsToFile = !!spFileYes?.checked;
    show(spGigQ, wantsToFile);

    if (!wantsToFile){
      // Collapse dependent UI + clear requirements when the whole question is hidden
      show(spExpBlock, false);
      show(spHstQ, false);
      show(spHstWrap, false);
      show(spUpload, false);
      req(spExpText, false);
      [spHstNum, spHstAcc, spHstStart, spHstEnd].forEach(el => req(el,false));
      // Clear any preselected answer when question is hidden
      if (spGigYes) spGigYes.checked = false;
      if (spGigNo) spGigNo.checked = false;
    } else {
      // Repaint inner state when it becomes visible
      applySpouseGig();
      applySpouseHst();
    }
  }

  // --- Wire events (if elements exist)
  [spFileYes, spFileNo].forEach(el => el && el.addEventListener('change', gateSpouseGigQuestion));
  [spGigYes, spGigNo].forEach(el => el && el.addEventListener('change', () => { applySpouseGig(); applySpouseHst(); }));
  [spHstYes, spHstNo].forEach(el => el && el.addEventListener('change', applySpouseHst));

  // --- Initial paint
  gateSpouseGigQuestion();

  // --- Keep Upload–Spouse in sync even when you navigate
  // If your framework hides/shows panels, this observer re-applies when that panel becomes visible.
  const uploadSpousePanel = document.querySelector('.pi-main[data-panel="upload-spouse"]');
  if (uploadSpousePanel){
    const mo = new MutationObserver(() => {
      // If the panel is now visible (not hidden), sync the upload section
      const isHidden = uploadSpousePanel.hidden || uploadSpousePanel.style.display === 'none';
      if (!isHidden) applySpouseGig();
    });
    mo.observe(uploadSpousePanel, { attributes: true, attributeFilter: ['hidden','style'] });
  }

  // Optional: expose a tiny manual hook if you want to call it from your navigation code
  window.AppSpouseGig = {
    refresh: () => { gateSpouseGigQuestion(); applySpouseGig(); applySpouseHst(); }
  };
})();
</script>


<!-- ERROR SCRIPT -->


</body>