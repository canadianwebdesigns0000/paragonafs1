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
        $query        = 'INSERT INTO `tax_information` SET is_file_submit=?, file_submit_date=?, first_name=?, last_name=?, gender=?, apartment_unit_number=?, ship_address=?, locality=?, state=?, postcode=?, country=?, birth_date=?, sin_number=?, phone=?, email=?, another_province=?, move_date=?, move_from=?, move_to=?, first_fillingtax=?, canada_entry=?, birth_country=?, year1=?, year1_income=?, year2=?, year2_income=?, year3=?, year3_income=?, file_paragon=?, years_tax_return=?, marital_status=?, spouse_first_name=?, spouse_last_name=?, spouse_date_birth=?, date_marriage=?, spouse_annual_income=?, residing_canada=?, spouse_annual_income_outside=?, have_child=?, marital_change=?, spouse_sin=?, spouse_phone=?, spouse_email=?, spouse_file_tax=?, spouse_first_tax=?, spouse_canada_entry=?, spouse_birth_country=?, spouse_year1=?, spouse_year1_income=?, spouse_year2=?, spouse_year2_income=?, spouse_year3=?, spouse_year3_income=?, spouse_file_paragon=?, spouse_years_tax_return=?, child_first_name=?, first_time_buyer=?, purchase_first_home=?, direct_deposits=?, id_proof=?, college_receipt=?, t_slips=?, rent_address=?, tax_summary=?, income_delivery=?, summary_expenses=?, delivery_hst=?, hst_number=?, hst_access_code=?, hst_start_date=?, hst_end_date=?, additional_docs=?, message_us=?';
        $parameters = array("No", '', $_SESSION['first_name'], $_SESSION['last_name'], '', '', '', '', '', '', '', '', '', $_SESSION['phone'], $_SESSION['email'], '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '');
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

// Write the message to the server's error log
error_log("Client Logged In: " . encrypt_decrypt("decrypt", $_SESSION['email']));

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Apply For Personal Tax</title>

    <link rel="icon" type="image/x-icon" href="../assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" type="text/css" href="../multi-form.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/foundation-datepicker.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.0/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" type="text/css" />

<script src="https://www.google.com/recaptcha/api.js" async defer></script>


    <script type="module" src="../assets/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>








    <script>
        $(document).ready(function() {

            window.addEventListener("load", () => {
                $("html,body").animate({
                    scrollTop: 0
                }, 100); //100ms for example

                const loader = document.querySelector(".loader");

                loader.classList.add("loader--hidden");

                loader.addEventListener("transitionend", () => {
                    document.body.removeChild(loader);
                });
            });

            

            $('#hide_movedate').click(function() {
                $('#movedate').hide()
            })
            $('#show_movedate').click(function() {
                $('#movedate').show()
            })
            var showMoveDateRadio = document.getElementById('show_movedate');
            if (showMoveDateRadio.checked) {
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
            if (show_filingtaxRadio.checked || hide_filingtaxRadio.checked) {
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
            if (show_first_time_buyer.checked || hide_first_time_buyer.checked) {
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

            if (show_marital_single.checked || show_marital_married.checked || show_marital_common.checked || show_marital_widow.checked || show_marital_divorce.checked || show_marital_seperated.checked) {
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
                    spouse_years_tax_return: 'Which Years Your Spouse want to file tax returns is required',
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
                .navigateTo(0)

        })
    </script>

    <style>
        button {
            color: white;
            letter-spacing: 1px;
            text-transform: uppercase;
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
        appearance: none; /* Hide default dropdown arrow */
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
        content: "▼"; /* Unicode down arrow */
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        color: #666;
        pointer-events: none;
    }

    </style>

</head>

<body>

    <?php include_once 'headers2.php'; ?>

       <?php include_once '../navbar.php'; ?>
       
    <!-- You need this element to prevent the content of the page from jumping up -->
    <!-- <div class="header-fixed-placeholder"></div> -->
    <!-- The content of your page would go here. -->

    <section style="background-image: url(https://paragonafs.ca/assets/images/document.jpg);background-position:center center;background-repeat: no-repeat;background-size: cover;margin-bottom: 0px;height: 422px;z-index: 0;position: relative; ">
        <div style="height: 100%;width: 100%;top: 0;left: 0;position: absolute;background-color: #121212;opacity: 0.70;transition: background 0.3s, border-radius 0.3s, opacity 0.3s;border-style: none;border-color: rgba(33,37,41,0);"></div>
        <div class="text-white d-flex flex-column align-items-center container position-relative services_page">
            <h2>Apply Personal Tax</h2>
        </div>
    </section>

    <div class="container row personal_upload_document">


        <div id="btn_fixed_continue">
            <div class="col-lg-4">
                &nbsp;
            </div>
            <div class="col-lg-8" style="text-align:center;padding-right: 19px;">
                <button type="button" class="hvr-bounce-to-bottom">
                    Continue
                </button>
            </div>
        </div>


        <div class="col-lg-4">
            <div class="other_service">
                <h3>Document Checklist</h3>

                <!-- Accordion -->
                <div class="acc-container">
                    <div class="acc">
                        <div class="acc-head">
                            <p>Registered Slips</p>
                        </div>
                        <div class="acc-content">
                            <ul>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T4- Employment Income</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T3, T5 – Interest, dividends, mutual funds</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T4E – Employment insurance benefits</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T4A – OAS, T4AP – Old Age Security and CPP benefits</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T2202A- Tuition / education receipts</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T4A- Other pensions and annuity income</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;T5007 – Social assistance, Worker’s compensation benefits</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Any other slips</li>
                            </ul>
                        </div>
                    </div>

                    <div class="acc">
                        <div class="acc-head">
                            <p>Receipt</p>
                        </div>
                        <div class="acc-content">
                            <ul>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;RRSP contribution slips</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Child care information (Babysitting)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Professional or union dues</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Tool expenses (Tradespersons)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Medical expenses (by family during the last 24 months)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Employment expenses</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Political contributions</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Charitable donations (last 5 years)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Moving expenses (if moved for work purposes)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Interest paid on student loans (last 5 years)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Professional certification exams</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Property tax for rental property information</li>
                            </ul>
                        </div>
                    </div>

                    <div class="acc">
                        <div class="acc-head">
                            <p>Other Documentation</p>
                        </div>
                        <div class="acc-content">
                            <ul>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Notice of Assessment/Reassessment</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Direct deposit form</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp;Canada Revenue Agency correspondence (any letter from CRA)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp; Sale of principal residence</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp; Rental income and expense receipts</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp; T2200- Declaration of Conditions of Employment</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp; Details of dependents, if any (Kids,Grandparents)</li>
                                <li><img src="../assets/img/paragon_logo_icon.png"> &nbsp; First time home buyer- please bring home documents</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="contact-us row bg-white shadow-none mb-5 mt-4 mx-auto upload_docu_contact" style="width: 100%;">
                <div class="col-12 text-center">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <div class="contact-box">
                                <i aria-hidden="true" class="fas fa-location-arrow" style="font-size: 40px;line-height: 40px;"></i>
                                <h6>Office Location</h6>
                                <p class="par-p mt-2">#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</p>
                            </div>

                        </div>
                        <div class="col-lg-6">
                            <div class="contact-box mt-4">
                                <i aria-hidden="true" class="fas fa-headphones-alt" style="font-size: 40px;line-height: 40px;"></i>
                                <h6>Calling Support</h6>
                                <p class="par-p mt-2"><i class="fas fa-phone-square-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (416) 477 3359<br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (647) 909 8484 <br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (437) 881 9175</p>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="contact-box mt-4">
                                <i aria-hidden="true" class="fas fa-mail-bulk" style="font-size: 40px;line-height: 40px;"></i>
                                <h6>Email Information</h6>
                                <p class="par-p mt-2">info@paragonafs.ca <br><br><br></p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-lg-8">

            <div class="loader" style="text-align: center;">
                <div class="loader-container">
                    <img src="https://paragonafs.ca/assets/img/paragon_logo_icon.png" style="display: block;" alt="">
                    <img src="https://paragonafs.limneo.com/assets/img/x5zwgi0JUz.gif" width="90" alt="">
                </div>
            </div>

            <!-- Modal Success HTML -->
            <div id="emailSentModal" class="modal fade" data-bs-keyboard="false">
                <div class="modal-dialog modal-confirm">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                            <div class="icon-box">
                                <i class="fas fa-thumbs-up"></i>
                            </div>
                        </div>
                        <div class="modal-body text-center">
                            <h4>Documents Upload Confirmation</h4>
                            <p>You have successfully submitted your application. Our Team will reach out to you soon. If you didnt hear from us within 48 hours, Please Contact Us.
                            </p>
                            <p style="color:red;margin:5px 5px 15px;">Note: Emails from Paragon Accounting might go into your SPAM folder, Please do check.</p>
                            <button class="btn btn-success" data-bs-dismiss="modal"><span>Okay</span></button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Confirmation HTML -->
            <div id="confirmationModal" class="modal fade" data-bs-keyboard="false">
                <div class="modal-dialog modal-confirm">
                    <div class="modal-content">
                        <div class="modal-header justify-content-center">
                            <div class="icon-box">
                                <i class="fas fa-exclamation-triangle" style="font-size:50px;"></i>
                            </div>
                        </div>
                        <div class="modal-body text-center">
                            <h4>Are you sure you want to submit?</h4>
                            <p>Double check all information before submitting.</p>
                            
                                                    
 
                            <button class="btn btn-warning" data-bs-dismiss="modal"><span>Back</span></button>
                            <button type="button" id="btnConfirmationModal" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Confirmation HTML -->
            <div id="editModal" class="modal fade" data-bs-keyboard="false">
                <div class="modal-dialog modal-confirm">
                    <div class="modal-content">
                        <div class="modal-body edit_personal_info_modal_body">

                            <button class="btn btn-warning" data-bs-dismiss="modal"><span>Back</span></button>
                        </div>
                    </div>
                </div>
            </div>


            <div id="pageMessages"></div>

            <!-- <button class="button-85" role="button">Button 85</button> -->

            <form action="/gmailapi.php" id="myForm" enctype="multipart/form-data" method="POST">

                <ul class="step-steps mb-3 mt-4">

                    <li class="step" id="docUploadInfo">
                        <img src="../assets/images/paper.png" alt="" width="30" height="30"><span> Doc Upload Instructions</span>
                    </li>

                    <li class="step button-85" id="personInfo">
                        <img src="../assets/images/form.png" alt="" width="30" height="30"><span> Enter Personal Information</span>
                    </li>

                    <li class="step button-85" id="uploadDocs">
                        <img src="../assets/images/attached.png" alt="" width="30" height="30"><span> Attach Tax Documents</span>
                    </li>

                </ul>

                <div class="tab">

                    <h4 class="mt-5 mb-4 mx-auto" style="text-align: center; width:100%;color: #1974D2; text-transform:capitalize;"> <i>Remote Tax Preparation</i> </h4>
                    <h1 class="mt-2 mb-4 mx-auto" style="text-align: center; width:60%;color: #1974D2; font-weight: 700px;"> <i>Virtually</i> </h1>
                    <p class="mb-5 mx-auto upload_doc_p" style="text-align: center; max-width:80%; font-size:15px;color:#5a5a5a;line-height: 25px;">Want to file taxes virtually? We use an extrodinary system for sending files for our clients. Not only is it easy to use, it is also an extremely secure way to send financial documents. </p>
                    <h5 class="par-h5 text-center mb-5" style="color: black; font-size: 18px"> Follow our simplified 4 steps process to files your taxes virtually </h5>

                    <div class="row">

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-2" style="text-align:center;">

                                    <div class="flip-container align-items-center">
                                        <div class="flipper">
                                            <div clas="front">
                                                <img src="../assets/images/paper.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="../assets/images/paper.png" alt="" width="80" height="80">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="col-lg-10">
                                    <h4 class="par-h4" style="color: #0075be;">Step 1: Identify the Documents to Upload</h4>
                                    <p class="par-p">Refer our documents checklist to identify the documents required for tax filing. If your documents are currently in paper format, you may need a scanner to prepare your documents in digital format if there are many pages. We only support this type of attachments .png, .jpeg, .tiff, .pdf, .txt, .doc, .docx, .xls, .xlsx, .csv </p>
                                </div>

                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-2" style="text-align:center;">


                                    <div class="flip-container align-items-center">
                                        <div class="flipper">
                                            <div clas="front">
                                                <img src="../assets/images/form.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="../assets/images/form.png" alt="" width="80" height="80">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-10">
                                    <h4 class="par-h4" style="color: #0075be;">Step 2: Enter Your Personal Information on Online Form</h4>
                                    <p class="par-p">In order to file your taxes correctly, we need your most recent personal information, spousal/partner information (if any) and children information (if any). Use our online form to enter the details which are securely transmitted to us.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-2" style="text-align:center;">

                                    <div class="flip-container align-items-center">
                                        <div class="flipper">
                                            <div clas="front">
                                                <img src="../assets/images/attached.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="../assets/images/attached.png" alt="" width="80" height="80">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-10">
                                    <h4 class="par-h4" style="color: #0075be;">Step 3: Attach documents on Online Form and Upload</h4>
                                    <p class="par-p">Upload your documents using our secure and hassle free new online upload form. Follow the step-by-step documents request form to attach the documents which are required by our tax pro. Our Online File Transfer System is Secure.</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-2" style="text-align:center;">

                                    <div class="flip-container align-items-center">
                                        <div class="flipper">
                                            <div clas="front">
                                                <img src="../assets/images/businessman.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="../assets/images/businessman.png" alt="" width="80" height="80">
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-lg-10">
                                    <h4 class="par-h4" style="color: #0075be;">Step 4: Let Your Paragon Tax Pro Do The Rest</h4>
                                    <p class="par-p">After you successfully transmitted the information to us, our tax pro will start working on your file. Our tax pro will reach out to you in case we need more information. We’ll send your tax return document(s) to you securely for review and signatures.</p>
                                </div>
                            </div>
                        </div>

                        <h6 class="par-h6 text-center" style="color: #7A7A7A;"> <i><strong>Note:</strong> We are trusted tax professional partners. All information shared by our clients are kept confidential. We do not share our client information to anyone.</i> </h6>
                    </div>

                </div>

                <div class="tab">

                    <h4 class="par-h4 mt-5" style="color: black;margin-bottom:10px;font-size:24px;">Let's Get Started</h4>
                    <p class="mb-3" style="font-size: 16px;">Please enter the details requested below in order to process your tax return correctly. All information requested is mandatory.</p>
                    
                    <div class="form_append_error"></div>
                    
                    <h4 class="par-h4 mt-2" style="color: #0075be;margin-bottom: 5px;font-size: 18px;padding-bottom: 10px;border-bottom: 2px solid black;">Tells us about yourself &nbsp; </h4>
                    <h6 style="color: #0075be;margin: 20px 0 0px;">Personal Information</h6>                    
                    <!-- <i id="edit_person_info" style="color:black;font-size: 16px;vertical-align: top;cursor: pointer;" class="fas fa-edit"></i>  -->

                    <div class="personal_basic_information">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Name <span style="color: red;">*</span></label>
                                <input type="text" name="firstName" class="form-control" placeholder="First Name" value="<?= $rowUser['first_name'] ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label for="validationCustom02" class="form-label">&nbsp;</label>
                                <input type="text" name="lastName" class="form-control" id="validationCustom02" placeholder="Last Name" value="<?= $rowUser['last_name'] ?>" required>
                            </div>

                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">Date of Birth <span style="color: red;">*</span></label>
                                    <input type="text" name="birth_date" value="<?= encrypt_decrypt('decrypt', $rowUser['birth_date']) ?>" class="form-control date_input_icon" id="date_birthdate" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SIN Number <span style="color: red;">*</span></label>
                                <input type="text" name="sin_number" value="<?= encrypt_decrypt('decrypt', $rowUser['sin_number']) ?>" class="form-control" maxlength="9" required>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Gender <span style="color: red;">*</span></label>
                                <br>

                                <div class="form-check form-check-inline">
                                    <label for="gender_male" class="form-check-label">Male</label>
                                    <input type="radio" class="form-check-input" id="gender_male" name="gender" value="Male" <?= ($rowUser['gender'] == 'Male') ? 'checked' : '' ?>>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="gender_female" name="gender" value="Female" required <?= ($rowUser['gender'] == 'Female') ? 'checked' : '' ?>>
                                    <label for="gender_female" class="form-check-label">Female</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <hr style="margin: 10px 0 0;">
                            </div>
                           
                            <h6 style="color: #0075be;margin: 20px 0 10px;">Current Address</h6>

                            <div class="col-md-6">
                                <input type="text" id="ship_address" name="ship_address" value="<?= $rowUser['ship_address'] ?>" autocomplete="off" class="form-control" style="margin-bottom: 5px;" placeholder="Street" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" id="apartment_unit_number" name="apartment_unit_number" value="<?= $rowUser['apartment_unit_number'] ?>" autocomplete="off" class="form-control" style="margin-bottom: 5px;" placeholder="Apartment, unit, suite, or floor #">
                            </div>

                            <div class="col-md-6">
                                <input type="text" id="locality" name="locality" value="<?= $rowUser['locality'] ?>" class="form-control" placeholder="City" style="margin-bottom: 5px;" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" id="state" name="state" value="<?= $rowUser['state'] ?>" class="form-control" placeholder="State/Province" style="margin-bottom: 5px;" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" id="postcode" name="postcode" value="<?= $rowUser['postcode'] ?>" class="form-control" placeholder="Postal Code" style="margin-bottom: 5px;" required>
                            </div>

                            <div class="col-md-6">
                                <input type="text" id="country" name="country" value="<?= $rowUser['country'] ?>" class="form-control" placeholder="Country/Region" style="margin-bottom: 5px;" required>
                            </div>

                            <div class="col-md-12">
                                <hr style="margin: 15px 0 0;">
                            </div>

                            <h6 style="color: #0075be;margin: 20px 0 0px;">Contact Information</h6>

                            <div class="col-md-6">
                                <label class="form-label">Phone Number <span style="color: red;">*</span></label>
                                <input type="text" name="phone" value="<?= encrypt_decrypt('decrypt', $rowUser['phone']) ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email Address <span style="color: red;">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= encrypt_decrypt('decrypt', $rowUser['email']) ?>" required readonly>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr style="margin: 25px 0 0;">
                    </div>

                    <h6 style="color: #0075be;margin: 20px 0 0px;">Other Information</h6>

                    <div class="col-md-12">
                        <label class="form-label">Did you move to another province? <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="show_movedate" name="another_province" value="Yes"
                                <?= ($rowUser['another_province'] == 'Yes') ? 'checked' : '' ?>>
                            <label for="show_movedate" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="another_province" id="hide_movedate" value="No"
                                <?= ($rowUser['another_province'] == 'No') ? 'checked' : '' ?>>
                            <label for="hide_movedate" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div id="movedate">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">When did you move? <span style="color: red;">*</span></label>
                                    <input type="text" name="move_date" value="<?= $rowUser['move_date'] ?>" <?= ($rowUser['move_date'] != '') ? 'readonly' : '' ?>  class="form-control date_input_icon" id="date_movedate" required>

                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <label for="validationCustom04" class="form-label">Province moved From? <span style="color: red;">*</span></label>
                                <select class="form-select" id="validationCustom04" name="move_from" required>
                                    <option selected disabled value="">Select State/Province</option>
                                    <option value="Alberta" <?= ($rowUser['move_from'] == 'Alberta') ? 'selected' : '' ?>>
                                        Alberta
                                    </option>
                                    <option value="British Columbia" <?= ($rowUser['move_from'] == 'British Columbia') ? 'selected' : '' ?>>
                                        British Columbia
                                    </option>
                                    <option value="Manitoba" <?= ($rowUser['move_from'] == 'Manitoba') ? 'selected' : '' ?>>
                                        Manitoba
                                    </option>
                                    <option value="New Brunswick" <?= ($rowUser['move_from'] == 'New Brunswick') ? 'selected' : '' ?>>
                                        New Brunswick
                                    </option>
                                    <option value="Newfoundland and Labrador" <?= ($rowUser['move_from'] == 'Newfoundland and Labrador') ? 'selected' : '' ?>>
                                        Newfoundland and Labrador
                                    </option>
                                    <option value="Northwest Territories" <?= ($rowUser['move_from'] == 'Northwest Territories') ? 'selected' : '' ?>>
                                        Northwest Territories
                                    </option>
                                    <option value="Nova Scotia" <?= ($rowUser['move_from'] == 'Nova Scotia') ? 'selected' : '' ?>>
                                        Nova Scotia
                                    </option>
                                    <option value="Nunavut" <?= ($rowUser['move_from'] == 'Nunavut') ? 'selected' : '' ?>>
                                        Nunavut
                                    </option>
                                    <option value="Ontario" <?= ($rowUser['move_from'] == 'Ontario') ? 'selected' : '' ?>>
                                        Ontario
                                    </option>
                                    <option value="Prince Edward Island" <?= ($rowUser['move_from'] == 'Prince Edward Island') ? 'selected' : '' ?>>
                                        Prince Edward Island
                                    </option>
                                    <option value="Quebec" <?= ($rowUser['move_from'] == 'Quebec') ? 'selected' : '' ?>>
                                        Quebec
                                    </option>
                                    <option value="Saskatchewan" <?= ($rowUser['move_from'] == 'Saskatchewan') ? 'selected' : '' ?>>
                                        Saskatchewan
                                    </option>
                                    <option value="Yukon" <?= ($rowUser['move_from'] == 'Yukon') ? 'selected' : '' ?>>
                                        Yukon
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Province moved To? <span style="color: red;">*</span></label>
                                <select class="form-select" name="move_to" required>
                                    <option selected disabled value="">Select State/Province</option>
                                    <option value="Alberta" <?= ($rowUser['move_to'] == 'Alberta') ? 'selected' : '' ?>>
                                        Alberta
                                    </option>
                                    <option value="British Columbia" <?= ($rowUser['move_to'] == 'British Columbia') ? 'selected' : '' ?>>
                                        British Columbia
                                    </option>
                                    <option value="Manitoba" <?= ($rowUser['move_to'] == 'Manitoba') ? 'selected' : '' ?>>
                                        Manitoba
                                    </option>
                                    <option value="New Brunswick" <?= ($rowUser['move_to'] == 'New Brunswick') ? 'selected' : '' ?>>
                                        New Brunswick
                                    </option>
                                    <option value="Newfoundland and Labrador" <?= ($rowUser['move_to'] == 'Newfoundland and Labrador') ? 'selected' : '' ?>>
                                        Newfoundland and Labrador
                                    </option>
                                    <option value="Northwest Territories" <?= ($rowUser['move_to'] == 'Northwest Territories') ? 'selected' : '' ?>>
                                        Northwest Territories
                                    </option>
                                    <option value="Nova Scotia" <?= ($rowUser['move_to'] == 'Nova Scotia') ? 'selected' : '' ?>>
                                        Nova Scotia
                                    </option>
                                    <option value="Nunavut" <?= ($rowUser['move_to'] == 'Nunavut') ? 'selected' : '' ?>>
                                        Nunavut
                                    </option>
                                    <option value="Ontario" <?= ($rowUser['move_to'] == 'Ontario') ? 'selected' : '' ?>>
                                        Ontario
                                    </option>
                                    <option value="Prince Edward Island" <?= ($rowUser['move_to'] == 'Prince Edward Island') ? 'selected' : '' ?>>
                                        Prince Edward Island
                                    </option>
                                    <option value="Quebec" <?= ($rowUser['move_to'] == 'Quebec') ? 'selected' : '' ?>>
                                        Quebec
                                    </option>
                                    <option value="Saskatchewan" <?= ($rowUser['move_to'] == 'Saskatchewan') ? 'selected' : '' ?>>
                                        Saskatchewan
                                    </option>
                                    <option value="Yukon" <?= ($rowUser['move_to'] == 'Yukon') ? 'selected' : '' ?>>
                                        Yukon
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <div class="col-md-12" style="margin-top: 0px">
                        <label class="form-label">Is this the first time you are filing tax? <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_fillingtax" id="show_filingtax" value="Yes"
                                <?= ($rowUser['first_fillingtax'] == 'Yes') ? 'checked' : '' ?>>
                            <label for="show_filingtax" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_fillingtax" id="hide_filingtax" value="No"1
                                <?= ($rowUser['first_fillingtax'] == 'No') ? 'checked' : '' ?>>
                            <label for="hide_filingtax" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div id="filingtax">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">Date of Entry in Canada <span style="color: red;">*</span></label>
                                    <input type="text" name="canada_entry" value="<?= $rowUser['canada_entry'] ?>" class="form-control date_input_icon" id="date_entry" required>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Birth Country <span style="color: red;">*</span></label>
                                <input type="text" name="birth_country" value="<?= $rowUser['birth_country'] ?>" id="birth_country" class="form-control">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <label class="form-label" style="margin-top: 15px;margin-bottom: 15px;">What was your world income in last 3 years before coming to Canada (in CAD)? <span style="color: red;">*</span></label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <input type="text" name="year1" value="<?= $rowUser['year1'] ?>" id="year1" class="form-control" placeholder="Year 1" readonly>
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year1_income" value="<?= $rowUser['year1_income'] ?>" id="year1_income" class="form-control" placeholder="Year 1 Income" style="margin-bottom: 5px;">
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year2" value="<?= $rowUser['year2'] ?>" id="year2" class="form-control" placeholder="Year 2" style="margin-bottom: 5px;" readonly>
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year2_income" value="<?= $rowUser['year2_income'] ?>" id="year2_income" class="form-control" placeholder="Year 2 Income" style="margin-bottom: 5px;">
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year3" value="<?= $rowUser['year3'] ?>" id="year3" class="form-control" placeholder="Year 3" style="margin-bottom: 5px;" readonly>
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year3_income" value="<?= $rowUser['year3_income'] ?>" id="year3_income" class="form-control" placeholder="Year 3 Income" style="margin-bottom: 5px;">
                            </div>
                        </div>

                    </div>

                    <div class="row" id="no_filingtax">
                        <div class="col-md-12">
                            <label class="form-label">Did you file earlier with Paragon Tax Services? <span style="color: red;">*</span></label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="file_paragon" id="file_paragon_yes" value="Yes" required
                                    <?= ($rowUser['file_paragon'] == 'Yes') ? 'checked' : '' ?>>
                                <label for="file_paragon_yes" class="form-check-label">Yes</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="file_paragon" id="file_paragon_no" value="No"
                                    <?= ($rowUser['file_paragon'] == 'No') ? 'checked' : '' ?>>
                                <label for="file_paragon_no" class="form-check-label">No</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Which years do you want to file tax returns? <span style="color: red;">*</span></label>
                            <input type="text" name="years_tax_return" value="<?= $rowUser['years_tax_return'] ?>"  class="form-control">
                            <p><small>(Please enter years separated by commas if you wish to file tax return for multiple year. For e.g., 2020, 2019 etc.)</small></p>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Are you first time home buyer? <span style="color: red;">*</span> 
                            <i class="far fa-question-circle" style="font-size: 15px; vertical-align: top; color:#0075be; cursor: pointer;" data-toggle="popover" title="Choose Yes, If you have purchased your first home in Canada and never applied for first time home buyer tax credit (HBTC)."></i> </label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_time_buyer" id="first_time_buyer_yes" value="Yes" required
                                <?= ($rowUser['first_time_buyer'] == 'Yes') ? 'checked' : '' ?>>
                            <label for="first_time_buyer_yes" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_time_buyer" id="first_time_buyer_no" value="No"
                                <?= ($rowUser['first_time_buyer'] == 'No') ? 'checked' : '' ?>>
                            <label for="first_time_buyer_no" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div class="body_purchase_first_home mb-2" id="body_purchase_first_home" style="none">
                        <div class="col-md-6">
                            <div class="date-container">
                                <label class="form-label">When did you purchase your first home? <span style="color: red;">*</span></label>
                                <input type="text" name="purchase_first_home" value="<?= $rowUser['purchase_first_home'] ?>" class="form-control date_input_icon" id="purchase_first_home" required>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Marital Status <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_single" name="marital_status" value="Single" required
                                <?= ($rowUser['marital_status'] == 'Single') ? 'checked' : '' ?>>
                            <label for="marital_single" class="form-check-label">Single</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_married" name="marital_status" value="Married"
                                <?= ($rowUser['marital_status'] == 'Married') ? 'checked' : '' ?>>
                            <label for="marital_married" class="form-check-label">Married</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_common" name="marital_status" value="Common in Law"
                                <?= ($rowUser['marital_status'] == 'Common in Law') ? 'checked' : '' ?>>
                            <label for="marital_common" class="form-check-label">Common in law</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_seperated" name="marital_status" value="Separated"
                                <?= ($rowUser['marital_status'] == 'Separated') ? 'checked' : '' ?>>
                            <label for="marital_seperated" class="form-check-label">Separated</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_widow" name="marital_status" value="Widow"
                                <?= ($rowUser['marital_status'] == 'Widow') ? 'checked' : '' ?>>
                            <label for="marital_widow" class="form-check-label">Widow</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_divorce" name="marital_status" value="Divorced"
                                <?= ($rowUser['marital_status'] == 'Divorced') ? 'checked' : '' ?>>
                            <label for="marital_divorce" class="form-check-label">Divorced</label>
                        </div>

                    </div>

                    <div class="body_marital_status" id="body_marital_status">
                        <h4 class="par-h4 mt-3" style="color: #0075be;margin-bottom:10px; font-size: 18px;padding-bottom: 10px;border-bottom: 2px solid black;">Tell us about your spouse</h4>
                        
                        <h6 style="color: #0075be;margin: 20px 0 0px;">Spouse Information</h6>                    

                        <div class="row">
                            <div class="col-md-6 marital">
                                <label class="form-label">Spouse Name <span style="color: red;">*</span></label>
                                <input type="text" name="spouse_firstname" value="<?= $rowUser['spouse_first_name'] ?>"   class="form-control" placeholder="First Name">
                            </div>

                            <div class="col-md-6 marital">
                                <label for="spouse_lastname" class="form-label">&nbsp;</label>
                                <input type="text" name="spouse_lastname" value="<?= $rowUser['spouse_last_name'] ?>"  class="form-control" placeholder="Last Name">
                            </div>

                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">Spouse Date of Birth <span style="color: red;">*</span></label>
                                    <input type="text" name="spouse_date_birth" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_date_birth']) ?>" class="form-control date_input_icon" id="spouse_date_birth" required>
                                </div>
                            </div> 

                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label" id="date_marital_status">Date of Marriage <span style="color: red;">*</span></label>
                                    <input type="text" name="date_marriage" value="<?= $rowUser['date_marriage'] ?>" class="form-control date_input_icon" id="date_marriage" required>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Residing in Canada <span style="color: red;">*</span></label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="residing_canada_yes" name="residing_canada" value="Yes" required
                                        <?= ($rowUser['residing_canada'] == 'Yes') ? 'checked' : '' ?>>
                                    <label for="residing_canada_yes" class="form-check-label">Yes</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="residing_canada_no" name="residing_canada" value="No"
                                        <?= ($rowUser['residing_canada'] == 'No') ? 'checked' : '' ?>>
                                    <label for="residing_canada_no" class="form-check-label">No</label>
                                </div>
                            </div>

                            <div id="spouse_residing_canada" style="display: none;margin-top:0;">

                                <div class="col-md-12">
                                    <hr style="margin: 15px 0 0;">
                                </div>
                            
                                <h6 style="color: #0075be;margin: 20px 0 10px;">Spouse Contact Information</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Spouse SIN <span style="color: red;">*</span></label>
                                        <input type="text" name="spouse_sin" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_sin']) ?>" class="form-control" maxlength="9">
                                    </div>

                                    <div class="col-md-6 marital">
                                        <label class="form-label">Spouse Email Address <span style="color: red;">*</span></label>
                                        <input type="text" name="spouse_email" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_email']) ?>" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Spouse Phone Number <span style="color: red;">*</span></label>
                                        <input type="text" name="spouse_phone" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_phone']) ?>" class="form-control">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr style="margin: 25px 0 0;">
                                </div>

                                <h6 style="color: #0075be;margin: 20px 0 5px;">Other Information</h6>

                                <div class="col-md-12">
                                    <label class="form-label">Does your spouse want to file taxes? <span style="color: red;">*</span></label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_file_tax_yes" name="spouse_file_tax" value="Yes" required
                                            <?= ($rowUser['spouse_file_tax'] == 'Yes') ? 'checked' : '' ?>>
                                        <label for="spouse_file_tax_yes" class="form-check-label">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_file_tax_no" name="spouse_file_tax" value="No"
                                            <?= ($rowUser['spouse_file_tax'] == 'No') ? 'checked' : '' ?>>
                                        <label for="spouse_file_tax_no" class="form-check-label">No</label>
                                    </div>
                                </div>

                                <div id="spouse_want_taxes" style="display: none;">
                                    <div class="col-md-12">
                                        <label class="form-label">Is this the first time your spouse filing tax? <span style="color: red;">*</span></label>
                                        <br>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="spouse_first_tax_yes" name="spouse_first_tax" value="Yes" required
                                                <?= ($rowUser['spouse_first_tax'] == 'Yes') ? 'checked' : '' ?>>
                                            <label for="spouse_first_tax_yes" class="form-check-label">Yes</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="spouse_first_tax_no" name="spouse_first_tax" value="No"
                                                <?= ($rowUser['spouse_first_tax'] == 'No') ? 'checked' : '' ?>>
                                            <label for="spouse_first_tax_no" class="form-check-label">No</label>
                                        </div>
                                    </div>


                                    <div id="spouse_filingtax" style="display: none;">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="date-container">
                                                    <label class="form-label">Date of Entry in Canada <span style="color: red;">*</span></label>
                                                    <input type="text" name="spouse_canada_entry" value="<?= $rowUser['spouse_canada_entry'] ?>" class="form-control date_input_icon" id="spouse_date_entry" required>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Birth Country <span style="color: red;">*</span></label>
                                                <input type="text" name="spouse_birth_country" value="<?= $rowUser['spouse_birth_country'] ?>" id="spouse_birth_country" class="form-control">
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <label class="form-label" style="margin: 15px 0px;">What was your spouse world income in last 3 years before coming to Canada (in CAD)? <span style="color: red;">*</span></label>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year1" value="<?= $rowUser['spouse_year1'] ?>" id="spouse_year1" class="form-control" placeholder="Year 1" readonly>
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year1_income" value="<?= $rowUser['spouse_year1_income'] ?>" id="spouse_year1_income" class="form-control" placeholder="Year 1 Income" style="margin-bottom: 5px;">
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year2" value="<?= $rowUser['spouse_year2'] ?>" id="spouse_year2" class="form-control" placeholder="Year 2" style="margin-bottom: 5px;" readonly>
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year2_income" value="<?= $rowUser['spouse_year2_income'] ?>" id="spouse_year2_income" class="form-control" placeholder="Year 2 Income" style="margin-bottom: 5px;">
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year3" value="<?= $rowUser['spouse_year3'] ?>" id="spouse_year3" class="form-control" placeholder="Year 3" style="margin-bottom: 5px;" readonly>
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year3_income" value="<?= $rowUser['spouse_year3_income'] ?>" id="spouse_year3_income" class="form-control" placeholder="Year 3 Income" style="margin-bottom: 5px;">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row" id="no_spouse_filingtax" style="display: none;">
                                        <div class="col-md-12">
                                            <label class="form-label">Did Your Spouse file earlier with Paragon Tax Services? <span style="color: red;">*</span></label>
                                            <br>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="spouse_file_paragon" id="spouse_file_paragon_yes" value="Yes" required
                                                    <?= ($rowUser['spouse_file_paragon'] == 'Yes') ? 'checked' : '' ?>>
                                                <label for="spouse_file_paragon_yes" class="form-check-label">Yes</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="spouse_file_paragon" id="spouse_file_paragon_no" value="No"
                                                    <?= ($rowUser['spouse_file_paragon'] == 'No') ? 'checked' : '' ?>>
                                                <label for="spouse_file_paragon_no" class="form-check-label">No</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12" style="margin-top: 0;">
                                            <label class="form-label">Which Years Your Spouse want to file tax returns? <span style="color: red;">*</span></label>
                                            <input type="text" name="spouse_years_tax_return" value="<?= $rowUser['spouse_years_tax_return'] ?>" class="form-control">
                                            <p style="margin-bottom:0;"><small>(Please enter years separated by commas if you wish to file tax return for multiple year. For e.g., 2020, 2019 etc.)</small></p>
                                        </div>
                                    </div>

                                </div>

                                <div class="body_spouse_want_file_tax mb-2" id="body_spouse_want_file_tax" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6 marital">
                                            <label class="form-label">Spouse Annual Income in CAD <span style="color: red;">*</span></label>
                                            <input type="text" name="spouse_annual_income" value="<?= $rowUser['spouse_annual_income'] ?>" class="form-control">
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div id="spouse_not_residing_canada" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Spousal Annual Income outside Canada (Converted to CAD) <span style="color: red;">*</span></label>
                                        <input type="text" name="spouse_annual_income_outside" value="<?= $rowUser['spouse_annual_income_outside'] ?>" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            

                            <div class="col-md-12">
                                <label class="form-label">Do you have child? <span style="color: red;">*</span></label>
                                <Br>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="have_child_yes" name="have_child" value="Yes" required
                                        <?= ($rowUser['have_child'] == 'Yes') ? 'checked' : '' ?>>
                                    <label for="have_child_yes" class="form-check-label">Yes</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="have_child_no" name="have_child" value="No"
                                        <?= ($rowUser['have_child'] == 'No') ? 'checked' : '' ?>>
                                    <label for="have_child_no" class="form-check-label">No</label>
                                </div>
                            </div>


                            <div class="repeater" id="have_child_body" style="overflow-x:auto; display:none; margin-top: 0px;">

                                <h4 class="par-h4 mt-3 mb-3" style="color: #0075be;margin-bottom:10px; font-size: 18px;padding-bottom: 10px;border-bottom: 2px solid black;">Tells us about your children</h4>

                                <table border="1" style="width: 100%; border: none;">
                                    <thead>
                                        <tr>
                                            <th style="width:20%">Child First Name</th>
                                            <th style="width:20%">Child Last Name</th>
                                            <th style="width:20%">Child Date of Birth</th>
                                            <th style="width:20%">Residing in Canada?</th>
                                                                                        


                                           

                                             <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody data-repeater-list="data">
                                        <?php
                                        $have_child_data = json_decode($rowUser['child_first_name'], true);

                                        if (!empty($have_child_data)) {
                                            foreach ($have_child_data as $child) {
                                        ?>
                                                <tr data-repeater-item>
                                                    
                                                    <td data-label="Child First Name">
                                                        <input type="text" class="form-control child_first_name" name="child_first_name" value="<?= $child['child_first_name'] ?>" required>
                                                    </td>
                                                    
                                                    
                                                    <td data-label="Child Last Name">
                                                        <input type="text" class="form-control child_last_name" name="child_last_name" value="<?= $child['child_last_name'] ?>" required>
                                                    </td>
                                                    
                                                   
                                                    
                                                    
                                                    <td data-label="Child Date of Birth">
                                                        <div class="date-container">
                                                            <input type="text" name="child_date_birth" class="form-control date_input_icon child_date_birth" value="<?= $child['child_date_birth'] ?>" required>
                                                        </div>
                                                        
                                                         </td>
                                                        
   
                                                    <td data-label="Residing in Canada">
                                                    
                                                      
                                                                         
                                                    <div class="row">
                              
                                <div class="form-check form-check-inline"  style="width: 50px; margin-left: 1rem; margin-right:0px;">
                                    <input type="radio" class="form-check-input" id="child_residing_canada" name="child_residing_canada" value="Yes" required checked
                                        style="width:15px">
                                    <label for="residing_canada_yes" class="form-check-label">&nbsp;Yes</label>
                                </div>

                                <div class="form-check form-check-inline"  style="width:50px; margin-left: 1rem; margin-right:0px;">
                                    <input type="radio" class="form-check-input" id="child_residing_canada" name="child_residing_canada" value="No"
                                      style="width:15px">
                                    <label for="residing_canada_no" class="form-check-label">&nbsp;No</label>
                                
                            </div>
                      </td>                                       

                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                      
                                                    
                                                    
                                                  
                             <td>                     
                                   <span class="form-control child_info_delete" data-repeater-delete type="button" style="text-align: center; width: 50px;">
                                                            <i class="fas fa-trash-alt" style="color: red;"></i>
                                                        </span>
                                                    </td>
                                                </tr>
                                                
                                                
                                        <?php
                                            }
                                        } else {
                                        ?>
                                            <tr data-repeater-item>
                                                <td data-label="Child First Name">
                                                    <input type="text" class="form-control child_first_name" name="child_first_name" required>
                                                </td>
                                                
                                                <td data-label="Child Last Name">
                                                    <input type="text" class="form-control child_last_name" name="child_last_name" required>
                                                </td>
                                                <td data-label="Child Date of Birth">
                                                    <div class="date-container">
                                                        <input type="text" name="child_date_birth" class="form-control date_input_icon child_date_birth" required>
                                                    </div>
                                                </td>
                                                
                                                
                                                    <td data-label="Residing in Canada">
                                                
                                                                        
                                                    <div class="row">
                              
                                <div class="form-check form-check-inline"  style="width: 50px; margin-left: 1rem; margin-right:0px;">
                                    <input type="radio" class="form-check-input" id="child_residing_canada" name="child_residing_canada" value="Yes" required
                                        style="width:15px">
                                    <label for="residing_canada_yes" class="form-check-label">&nbsp;Yes</label>
                                </div>

                                <div class="form-check form-check-inline"  style="width:50px; margin-left: 1rem; margin-right:0px;">
                                    <input type="radio" class="form-check-input" id="child_residing_canada" name="child_residing_canada" value="No"
                                      style="width:15px">
                                    <label for="residing_canada_no" class="form-check-label">&nbsp;No</label>
                                
                            </div>
                                                
                                                
                                                
                                                
                                      </td>        
                                                
                                                
                                                
                                                <td>
                                                    <span class="form-control child_info_delete" data-repeater-delete type="button" style="text-align: center;width: 50px;">
                                                        <i class="fas fa-trash-alt" style="color: red;"></i>
                                                    </span>
                                                </td>
                                            </tr>
                                    
                                        <?php
                                        }
                                        ?>
                                    <tbody>
                                </table>

                                <input data-repeater-create type="button" class="child_repeater" value="Add Child" style="background-color: #0075be; color:white; border-radius: 2px; margin-left: 5px; margin-top: 5px; padding: 5px 10px;" />
                            </div>

                        </div>
                    </div>

                    <div class="body_marital_change" id="body_marital_change">
                        <div class="col-md-6 marital">
                            <div class="date-container">
                                <label class="form-label" id="marital_change_label">Date Of Marital Status Change <span style="color: red;">*</span></label>
                                <input type="text" name="marital_change" value="<?= $rowUser['marital_change'] ?>" class="form-control date_input_icon" id="marital_change" required>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="tab">

                    <h4 class="par-h4 mt-5" style="color: black;margin-bottom:10px;font-size:24px;">Attach your documents here</h4>
                    <p class="mb-5" style="font-size: 16px;">Please attach documents which are required for your tax return. If you are not sure about the documents to attach, refer our documents checklist.</p>
                    
                    <div class="CustomTabs">

                        <ul class="tabs" style="padding-left: 0;">

                            <li><a href="#" id="applicant_name_tab"></a></li>
                            <li><a href="#" id="applicant_spouse_tab"></a></li>
         
                        </ul>

                        <div class="tab_content">

                            <div class="tabs_item applicant_name_tab_item">

                                <div id="upload_id_proof">
                                    <label style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">ID Proof <span style="color: red;">*</span> </b><br> In order to verify your identity, Please provide your ID proof. Examples of ID proof are: Driver license, passport etc.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper id_proof_required" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-links-input" name="id_proof" value="<?= $rowUser['id_proof'] ?>">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_id_proof dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_id_proof" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                
                                



                                <div id="upload_sin_number_document" style="display:none;">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">SIN Number Document <span style="color: red;">*</span> </b><br> Please provide your SIN Number Document.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper sin_number_document_required" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded_sin_number_document" name="sin_number_document" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_sin_number_document dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_sin_number_document" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="upload_direct_deposit">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Direct deposit form</b><br> If you want tax refund/benefits from CRA to be deposited directly into your account, please provide direct deposit bank form.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-direct-deposit" name="direct_deposits" value="<?= $rowUser['direct_deposits'] ?>">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_direct_deposit dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_direct_deposit" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="upload_college_receipt">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">T2202(College Receipt)</b><br> If you want to avail college fee credits, please provide all college fee receipts (T2200) issued by your college.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-college-receipt" name="college_receipt" value="<?= $rowUser['college_receipt'] ?>">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_college_receipt dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_college_receipt" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="upload_t_slips">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">T4/T4A/T Slips</b><br>(Please provide passwords if T4s are password secured in the message box)</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-t-slips" name="t_slips" value="<?= $rowUser['t_slips'] ?>">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_t_slips dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_t_slips" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="upload_additional_documents">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Additional Documents to upload</b> <br>
                                        If you have any additional document which is not listed above, please attach in the below section.
                                    </label>

                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-additional-documents" name="additional_docs" value="<?= $rowUser['additional_docs'] ?>">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_additional_documents dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_additional_documents" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                    
                                       
 
 
 





   <div class="col-md-12 mt-4">
                                  
   <label class="form-label" id="rent_view"><b> Do you want to claim your rent benefit? <span style="color: red;">*</span></b></label>  <br>
 
                                                                       
                                                              <div class="form-check form-check-inline">
    <input type="radio" class="form-check-input" id="show_rent" name="rent_benefit" value="Yes"  >
    <label for="show_rent" class="form-check-label">Yes</label>
</div>

<div class="form-check form-check-inline">
    <input type="radio" class="form-check-input" id="hide_rent" name="rent_benefit" value="No" required checked>
    <label for="hide_rent" class="form-check-label">No</label>
</div>



                                  
<script>
    document.addEventListener("DOMContentLoaded", function () {
        function toggleRentDiv() {
            let rentDiv = document.getElementById("rent_id");
            let isChecked = document.getElementById("show_rent").checked;
            rentDiv.style.display = isChecked ? "block" : "none";
        }

        // Attach event listeners to radio buttons
        document.querySelectorAll('input[name="rent_benefit"]').forEach(input => {
            input.addEventListener("change", toggleRentDiv);
        });

        // Initialize the visibility on page load
        toggleRentDiv();
    });
</script>
                                 












                         
   <div id="rent_id" class="repeater mt-3" style="display: block;">                                                            
                                      
  <table border="1" style="border: none;">
    <thead>
        <tr>
            <th style="width:60%">Rent Address <span style="color: red;">*</span></th>
            <th style="width:50%">From Date <span style="color: red;">*</span></th>
            <th style="width:50%">To Date <span style="color: red;">*</span></th>
            <th style="width:40%">Total Rent Paid <span style="color: red;">*</span></th>
            <th style="width:50px">&nbsp;</th>
        </tr>
    </thead>
    <tbody data-repeater-list="group-a">
        <tr data-repeater-item>
            <td data-label="Rent Address">
                <input id="rent_address" class="form-control rent_address_search benefit_rent_address" name="rent_address" required>
                <span class="text-danger error-msg" style="display: none; font-size:0.86em">Rent Address is required.</span>
            </td>

            <td data-label="From Date">
                <div class="row">
                    <div class="col-md-6" style="width:110px">
                        <select class="form-control benefit_rent_address total_month_rent" name="from_month" id="from_month" required>
                            <option value="">Month ▾</option>
                            <option value="Jan">Jan</option>
                            <option value="Feb">Feb</option>
                            <option value="Mar">Mar</option>
                            <option value="Apr">Apr</option>
                            <option value="May">May</option>
                            <option value="Jun">Jun</option>
                            <option value="Jul">Jul</option>
                            <option value="Aug">Aug</option>
                            <option value="Sep">Sep</option>
                            <option value="Oct">Oct</option>
                            <option value="Nov">Nov</option>
                            <option value="Dec">Dec</option>
                        </select>
                        
                        <span class="text-danger error-msg" style="display: none; font-size:0.86em">Required.</span>
                        
                    </div>
                    <div class="col-md-6" style="width:100px; margin-left:-20px">
                        <input class="form-control benefit_rent_address total_month_rent " type="text" name="from_year" id="from_year" placeholder="Year" maxlength="4" pattern="\d{4}" required>
                         <span class="text-danger error-msg" style="display: none; font-size:0.86em">Required.</span>
                    </div>
                </div>
            </td>

            <td data-label="To Date">
                <div class="row">
                    <div class="col-md-6" style="width:110px">
                        <select class="form-control benefit_rent_address total_month_rent" name="to_month" id="to_month" required>
                          
                            <option value="">Month ▾</option>
                            <option value="Jan">Jan</option>
                            <option value="Feb">Feb</option>
                            <option value="Mar">Mar</option>
                            <option value="Apr">Apr</option>
                            <option value="May">May</option>
                            <option value="Jun">Jun</option>
                            <option value="Jul">Jul</option>
                            <option value="Aug">Aug</option>
                            <option value="Sep">Sep</option>
                            <option value="Oct">Oct</option>
                            <option value="Nov">Nov</option>
                            <option value="Dec">Dec</option>
                        </select>
                        <span class="text-danger error-msg" style="display: none;font-size:0.86em">Required.</span>
                    </div>
                    <div class="col-md-6" style="width:100px; margin-left:-20px">
                        <input class="form-control benefit_rent_address total_month_rent" type="text" name="to_year" id="to_year" placeholder="Year" maxlength="4" pattern="\d{4}" required>
                        <span class="text-danger error-msg" style="display: none;font-size:0.86em">Required.</span>
                    </div>
                </div>
                <span id="to_date_error" class="text-danger" style="display:none;font-size:0.86em">To Date must be later than From Date.</span>	
            </td>

            <td data-label="Total Rent Paid">
                <input class="form-control total_rent_paid benefit_rent_address" name="total_rent_paid" required>
                <span class="text-danger error-msg" style="display: none;font-size:0.86em">Total Rent required.</span>
            </td>
            
       <td>
             <span class="form-control rent_info_delete" data-repeater-delete type="button" style="text-align: center;width: 50px;">
                                                            <i class="fas fa-trash-alt" style="color: red;"></i>
                                                        </span>
                                                    </td>
        </tr>
    </tbody>
</table>












               
         <script>                                                 
     
     
     document.addEventListener("DOMContentLoaded", function () {
    function validateDates() {
        const monthMap = {
            "Jan": 1, "Feb": 2, "Mar": 3, "Apr": 4, "May": 5, "Jun": 6,
            "Jul": 7, "Aug": 8, "Sep": 9, "Oct": 10, "Nov": 11, "Dec": 12
        };

        let fromMonth = document.getElementById("from_month").value;
        let fromYear = document.getElementById("from_year").value;
        let toMonth = document.getElementById("to_month").value;
        let toYear = document.getElementById("to_year").value;
        let toError = document.getElementById("to_date_error");

        if (!toError) {
            console.warn("Error element not found in the HTML.");
            return;
        }

        // Hide error if year is not fully entered
        if (fromYear.length < 4 || toYear.length < 4) {
            toError.style.display = "none";
            return;
        }

        let fromMonthNum = monthMap[fromMonth];
        let toMonthNum = monthMap[toMonth];
        let fromYearNum = parseInt(fromYear);
        let toYearNum = parseInt(toYear);

        // Show error if "From Date" is not less than "To Date"
        if (fromYearNum > toYearNum || (fromYearNum === toYearNum && fromMonthNum >= toMonthNum)) {
            toError.style.display = "inline";
        } else {
            toError.style.display = "none"; // Auto-hide error when corrected
        }
    }

    // Validate when the user selects a month
    document.getElementById("from_month").addEventListener("change", validateDates);
    document.getElementById("to_month").addEventListener("change", validateDates);

    // Validate only when the user finishes typing the full 4-digit year
    document.getElementById("from_year").addEventListener("input", function () {
        if (this.value.length === 4) validateDates();
    });
    document.getElementById("to_year").addEventListener("input", function () {
        if (this.value.length === 4) validateDates();
    });
});





      </script>
                              
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                       
                                        <input data-repeater-create type="button" class="rent_repeater" style="border-radius: 2px; background-color:#0075be; color: white; padding: 5px 10px;" value="Add Address" />
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <label class="form-label"><b> Do you have income from Uber/Skip/Lyft/Doordash etc.? <span style="color: red;">*</span></b></label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="show_delivery_tax" name="income_delivery" value="Yes" required
                                            <?= ($rowUser['income_delivery'] == 'Yes') ? 'checked' : '' ?>>
                                        <label for="show_delivery_tax" class="form-check-label">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="hide_delivery_tax" name="income_delivery" value="No"
                                            <?= ($rowUser['income_delivery'] == 'No') ? 'checked' : '' ?>>
                                        <label for="hide_delivery_tax" class="form-check-label">No</label>
                                    </div>
                                </div>
                                
                                <div id="upload_delivery_annual_tax" id="delivery_annual_tax">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px">Annual Tax summary</b> <span style="color: red;">*</span></label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper delivery_annual_tax_required" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-delivery-annual-tax" name="tax_summary" value="<?= $rowUser['tax_summary'] ?>">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_delivery_annual_tax dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_delivery_annual_tax" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="form-group mt-4 mb-3">
                                        <label for="summary_expenses"><b style="font-size: 16px;">Summary of Expenses</b> <span style="color: red;">*</span></label>
                                        <textarea class="form-control mt-2" style="width: 100%; margin-left:auto; margin-right:auto;" rows="6" id="summary_expenses" name="summary_expenses" required></textarea>
                                    </div>

                                    <label class="form-label mt-4"><b>Do you want to file HST for your Uber/Skip/Lyft/Doordash?</b> <span style="color: red;">*</span></label>

                                    <br>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="show_hst" name="delivery_hst" value="Yes" required
                                            <?= ($rowUser['delivery_hst'] == 'Yes') ? 'checked' : '' ?>>
                                        <label for="show_hst" class="form-check-label">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="hide_hst" name="delivery_hst" value="No"
                                            <?= ($rowUser['delivery_hst'] == 'No') ? 'checked' : '' ?>>
                                        <label for="hide_hst" class="form-check-label">No</label>
                                    </div>

                                    <div class="hst" id="hst">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <label class="form-label"><b>HST # <span style="color: red;">*</span></b></label>
                                                <input type="text" name="hst_number" value="<?= encrypt_decrypt('decrypt', $rowUser['hst_number']) ?>" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label"><b>Access code <span style="color: red;">*</span></b></label>
                                                <input type="text" name="hst_access_code" value="<?= encrypt_decrypt('decrypt', $rowUser['hst_access_code']) ?>" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="date-container">
                                                    <label class="form-label">Start Date <span style="color: red;">*</span></label>
                                                    <input type="text" name="hst_start_date" value="<?= encrypt_decrypt('decrypt', $rowUser['hst_start_date']) ?>" class="form-control date_input_icon" id="hst_start_date" required>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="date-container">
                                                    <label class="form-label">End Date <span style="color: red;">*</span></label>
                                                    <input type="text" name="hst_end_date" value="<?= encrypt_decrypt('decrypt', $rowUser['hst_end_date']) ?>" class="form-control date_input_icon" id="hst_end_date" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="tabs_item applicant_spouse_tab_item" style="display:none">

                                <div id="spouse_upload_id_proof">
                                    <label style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">Spouse ID Proof <span style="color: red;">*</span> </b><br> In order to verify your identity, Please provide your ID proof. Examples of ID proof are: Driver license, passport etc.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper spouse_id_proof_required" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_uploaded-links-input" name="spouse_id_proof" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_id_proof dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_id_proof" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="spouse_upload_sin_number_document" style="display:none;">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">Spouse SIN Number Document <span style="color: red;">*</span> </b><br> Please provide your SIN Number Document.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper spouse_sin_number_document_required" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_sin_number_document" name="spouse_sin_number_document" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_sin_number_document dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_sin_number_document" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="spouse_upload_direct_deposit">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Spouse Direct deposit form</b><br> If you want tax refund/benefits from CRA to be deposited directly into your account, please provide direct deposit bank form.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_uploaded-direct-deposit" name="spouse_direct_deposits" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_direct_deposit dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_direct_deposit" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="spouse_upload_college_receipt">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Spouse T2202(College Receipt)</b><br> If you want to avail college fee credits, please provide all college fee receipts (T2200) issued by your college.</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_uploaded-college-receipt" name="spouse_college_receipt" />

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_college_receipt dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_college_receipt" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="spouse_upload_t_slips">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Spouse T4/T4A/T Slips</b><br>(Please provide passwords if T4s are password secured in the message box)</label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_uploaded-t-slips" name="spouse_t_slips" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_t_slips dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_t_slips" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div id="spouse_upload_additional_documents">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Spouse Additional Documents to upload</b> <br>
                                        If you have any additional document which is not listed above, please attach in the below section.
                                    </label>

                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_uploaded-additional-documents" name="spouse_additional_docs" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_additional_documents dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_additional_documents" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                
                                
                                
                                
                               
                                      



                                    






                                  
     
                                
                                    
                                               
 <div class="col-md-12 mt-4">
                                  
   <label class="form-label" id="spouse_rent_view"><b> Do you want to claim your rent benefit? <span style="color: red;">*</span></b></label>  <br>
 
 <div class="form-check form-check-inline">
        <input type="radio" class="form-check-input" id="spouse_show_rent" name="spouse_rent_benefit" value="Yes" required >
        <label for="spouse_show_rent" class="form-check-label">Yes</label>
    </div>

    <div class="form-check form-check-inline">
        <input type="radio" class="form-check-input" id="spouse_hide_rent" name="spouse_rent_benefit" value="No" checked>
        <label for="spouse_hide_rent" class="form-check-label">No</label>
    </div>



<script>
    document.addEventListener("DOMContentLoaded", function () {
        function toggleSpouseRentDiv() {
            let div = document.getElementById("spouse_rent_id");
            let isChecked = document.getElementById("spouse_show_rent").checked;
            div.style.display = isChecked ? "block" : "none";
        }

        // Attach event listeners
        document.querySelectorAll('input[name="spouse_rent_benefit"]').forEach(input => {
            input.addEventListener("change", toggleSpouseRentDiv);
        });

        // Initialize visibility on page load
        toggleSpouseRentDiv();
    });
</script>




 
    <div id="spouse_rent_id" class="repeater mt-3" style="margin-left: auto; margin-right: auto;">
        <table border="1" style="border: none;">
            <thead>
                <tr>
                    <th style="width:60%">Rent Address <span style="color: red;">*</span></th>
                    <th style="width:50%">From Date <span style="color: red;">*</span></th>
                    <th style="width:50%">To Date <span style="color: red;">*</span></th>
                    <th style="width:40%">Total Rent Paid <span style="color: red;">*</span></th>
                    <th style="width:50px">&nbsp;</th>
                </tr>
            </thead>
            <tbody data-repeater-list="spouse_rent_address">
                <tr data-repeater-item>
                    <td data-label="Rent Address">
                        <input id="spouse_rent_address" class="form-control spouse_rent_address_search spouse_benefit_rent_address" name="spouse_rent_address" required>
                        <span class="text-danger error-msg" style="display: none;font-size:0.86em">Rent Address is required.</span>
                    </td>

                    <td data-label="From Date">
                        <div class="row">
                            <div class="col-md-6" style="width:110px">
                                <select class="form-control spouse_total_month_rent spouse_benefit_rent_address" name="spouse_from_month" id="spouse_from_month" required>
                                     <option value="">Month ▾</option>
                                    <option value="Jan">Jan</option>
                                    <option value="Feb">Feb</option>
                                    <option value="Mar">Mar</option>
                                    <option value="Apr">Apr</option>
                                    <option value="May">May</option>
                                    <option value="Jun">Jun</option>
                                    <option value="Jul">Jul</option>
                                    <option value="Aug">Aug</option>
                                    <option value="Sep">Sep</option>
                                    <option value="Oct">Oct</option>
                                    <option value="Nov">Nov</option>
                                    <option value="Dec">Dec</option>
                                </select>
                                <span class="text-danger error-msg" style="display: none;font-size:0.86em">Required.</span>
                                
                            </div>
                            <div class="col-md-6" style="width:100px; margin-left:-20px">
                                <input class="form-control spouse_total_month_rent spouse_benefit_rent_address" type="text" name="spouse_from_year" id="spouse_from_year" placeholder="Year" maxlength="4" pattern="\d{4}" required>
                              <span class="text-danger error-msg" style="display: none;font-size:0.86em">Required.</span>
                          </div>
                        </div>
                    </td>

                    <td data-label="To Date">
                        <div class="row">
                            <div class="col-md-6" style="width:110px">
                                <select class="form-control spouse_total_month_rent spouse_benefit_rent_address" name="spouse_to_month" id="spouse_to_month" required>
                                     <option value="">Month ▾</option>
                                    <option value="Jan">Jan</option>
                                    <option value="Feb">Feb</option>
                                    <option value="Mar">Mar</option>
                                    <option value="Apr">Apr</option>
                                    <option value="May">May</option>
                                    <option value="Jun">Jun</option>
                                    <option value="Jul">Jul</option>
                                    <option value="Aug">Aug</option>
                                    <option value="Sep">Sep</option>
                                    <option value="Oct">Oct</option>
                                    <option value="Nov">Nov</option>
                                    <option value="Dec">Dec</option>
                                </select>
                                      <span class="text-danger error-msg" style="display: none; font-size:0.86em">Required</span>
                            </div>
                            <div class="col-md-6" style="width:100px; margin-left:-20px">
                                <input class="form-control spouse_total_month_rent spouse_benefit_rent_address" type="text" name="spouse_to_year" id="spouse_to_year" placeholder="Year" maxlength="4" pattern="\d{4}" required>
                                  <span class="text-danger error-msg" style="display: none;font-size:0.86em">Required</span>
                            </div>
                        </div>
                        <span id="spouse_to_date_error" class="text-danger" style="display:none;font-size:0.86em">To Date must be later than From Date.</span>
                    </td>

                    <td data-label="Total Rent Paid">
                        <input class="form-control spouse_total_rent_paid spouse_benefit_rent_address" name="spouse_total_rent_paid" required>
                        <span class="text-danger error-msg" style="display: none;font-size:0.86em">Total Rent required.</span>
                    </td>
                    <td>
                        <span class="form-control spouse_rent_info_delete" data-repeater-delete type="button" style="text-align: center;width: 50px;">
                            <i class="fas fa-trash-alt" style="color: red;"></i>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
  

                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    <script>                                                 
document.addEventListener("DOMContentLoaded", function () {
    function validateDates() {
        const monthMap = {
            "Jan": 1, "Feb": 2, "Mar": 3, "Apr": 4, "May": 5, "Jun": 6,
            "Jul": 7, "Aug": 8, "Sep": 9, "Oct": 10, "Nov": 11, "Dec": 12
        };

        let fromMonth = document.getElementById("spouse_from_month").value;
        let fromYear = document.getElementById("spouse_from_year").value;
        let toMonth = document.getElementById("spouse_to_month").value;
        let toYear = document.getElementById("spouse_to_year").value;
        let toError = document.getElementById("spouse_to_date_error");

        if (!toError) {
            console.warn("Error element not found in the HTML.");
            return;
        }

        // Hide error if year is not fully entered
        if (fromYear.length < 4 || toYear.length < 4) {
            toError.style.display = "none";
            return;
        }

        let fromMonthNum = monthMap[fromMonth];
        let toMonthNum = monthMap[toMonth];
        let fromYearNum = parseInt(fromYear);
        let toYearNum = parseInt(toYear);

        // Show error if "From Date" is not less than "To Date"
        if (fromYearNum > toYearNum || (fromYearNum === toYearNum && fromMonthNum >= toMonthNum)) {
            toError.style.display = "inline";
        } else {
            toError.style.display = "none"; // Auto-hide error when corrected
        }
    }

    // Validate when the user selects a month
    document.getElementById("spouse_from_month").addEventListener("change", validateDates);
    document.getElementById("spouse_to_month").addEventListener("change", validateDates);

    // Validate only when the user finishes typing the full 4-digit year
    document.getElementById("spouse_from_year").addEventListener("input", function () {
        if (this.value.length === 4) validateDates();
    });
    document.getElementById("spouse_to_year").addEventListener("input", function () {
        if (this.value.length === 4) validateDates();
    });
});
</script>





















                                        <input data-repeater-create type="button" class="spouse_rent_repeater" style="border-radius: 2px; background-color:#0075be; color: white; padding: 5px 10px;" value="Add Address" />
                                    </div>
                                </div>
                                
                                
                                
                                
                                

                                <div class="col-md-12 mt-4">
                                    <label class="form-label"><b> Do you have income from Uber/Skip/Lyft/Doordash etc.? <span style="color: red;">*</span></b></label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_show_delivery_tax" name="spouse_income_delivery" value="Yes" required
                                            <?= ($rowUser['spouse_income_delivery'] == 'Yes') ? 'checked' : '' ?>>
                                        <label for="spouse_show_delivery_tax" class="form-check-label">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_hide_delivery_tax" name="spouse_income_delivery" value="No"
                                            <?= ($rowUser['spouse_income_delivery'] == 'No') ? 'checked' : '' ?>>
                                        <label for="spouse_hide_delivery_tax" class="form-check-label">No</label>
                                    </div>
                                </div>

                                <div id="spouse_upload_delivery_annual_tax" id="spouse_delivery_annual_tax">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px">Annual Tax summary</b> <span style="color: red;">*</span></label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper spouse_delivery_annual_tax_required" style="margin-bottom: 0px;">
                                            <input type="hidden" id="spouse_uploaded-delivery-annual-tax" name="spouse_tax_summary">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button spouse_clickable_delivery_annual_tax dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="spouse_sortable_delivery_annual_tax" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="form-group mt-4 mb-3">
                                        <label for="spouse_summary_expenses"><b style="font-size: 16px;">Summary of Expenses</b> <span style="color: red;">*</span></label>
                                        <textarea class="form-control mt-2" style="width: 100%; margin-left:auto; margin-right:auto;" rows="6" id="spouse_summary_expenses" name="spouse_summary_expenses" required></textarea>
                                    </div>

                                    <label class="form-label mt-4"><b>Do you want to file HST for your Uber/Skip/Lyft/Doordash?</b> <span style="color: red;">*</span></label>

                                    <br>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_show_hst" name="spouse_delivery_hst" value="Yes" required
                                            <?= ($rowUser['spouse_delivery_hst'] == 'Yes') ? 'checked' : '' ?>>
                                        <label for="spouse_show_hst" class="form-check-label">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_hide_hst" name="spouse_delivery_hst" value="No"
                                            <?= ($rowUser['spouse_delivery_hst'] == 'No') ? 'checked' : '' ?>>
                                        <label for="spouse_hide_hst" class="form-check-label">No</label>
                                    </div>

                                    <div class="spouse_hst" id="spouse_hst">
                                        <div class="row">

                                            <div class="col-md-6">
                                                <label class="form-label"><b>HST # <span style="color: red;">*</span></b></label>
                                                <input type="text" name="spouse_hst_number" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_hst_number']) ?>" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label"><b>Access code <span style="color: red;">*</span></b></label>
                                                <input type="text" name="spouse_hst_access_code" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_hst_access_code']) ?>" class="form-control" required>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="date-container">
                                                    <label class="form-label">Start Date <span style="color: red;">*</span></label>
                                                    <input type="text" name="spouse_hst_start_date" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_hst_start_date']) ?>" class="form-control date_input_icon" id="spouse_hst_start_date" required>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="date-container">
                                                    <label class="form-label">End Date <span style="color: red;">*</span></label>
                                                    <input type="text" name="spouse_hst_end_date" value="<?= encrypt_decrypt('decrypt', $rowUser['spouse_hst_end_date']) ?>" class="form-control date_input_icon" id="spouse_hst_end_date" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                    <script>
                        $(document).ready(function() { 
                            (function ($) { 
                                $('.CustomTabs ul.tabs').addClass('active').find('> li:eq(0)').addClass('current');
                                
                                $('.CustomTabs ul.tabs li a').click(function (g) { 
                                    var tab = $(this).closest('.CustomTabs'), 
                                        index = $(this).closest('li').index();
                                    
                                    tab.find('ul.tabs > li').removeClass('current');
                                    $(this).closest('li').addClass('current');
                                    
                                    tab.find('.tab_content').find('div.tabs_item').not('div.tabs_item:eq(' + index + ')').hide();
                                    tab.find('.tab_content').find('div.tabs_item:eq(' + index + ')').show();
                                    
                                    g.preventDefault();
                                } );
                            })(jQuery);
                        });
                    </script>

                    
                    <div class="form-group mt-2">
                        <label class="form-label" for="message_us">Your Message For Us?</label>
                        <textarea class="form-control" rows="5" id="message_us" name="message_us"></textarea>
                    </div>
                    


   
    
                


                </div>

                <div style="overflow: auto; text-align: center">
                    <div style="float: center; margin-top: 20px" id="multi_step_button">
                        <button type="button" class="previous hvr-bounce-to-bottom">Previous</button>
                        <button type="button" class="next hvr-bounce-to-bottom">Continue</button>
                        <button type="button" class="submit hvr-bounce-to-bottom" id="1st_submit">Submit</button>
                    </div>
                </div>

            </form>
        </div>

        <div class="contact-us row bg-white shadow-none mb-5 mt-4 mx-auto upload_docu_contact_2" style="width: 100%; display:none;">
            <div class="col-12 text-center">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-location-arrow" style="font-size: 40px;line-height: 40px;"></i>
                            <h6>Office Location</h6>
                            <p class="par-p mt-2">#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</p>
                        </div>

                    </div>
                    <div class="col-lg-6">
                        <div class="contact-box mt-4">
                            <i aria-hidden="true" class="fas fa-headphones-alt" style="font-size: 40px;line-height: 40px;"></i>
                            <h6>Calling Support</h6>
                            <p class="par-p mt-2"><i class="fas fa-phone-square-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (416) 477 3359<br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (647) 909 8484 <br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (437) 881 9175</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="contact-box mt-4">
                            <i aria-hidden="true" class="fas fa-mail-bulk" style="font-size: 40px;line-height: 40px;"></i>
                            <h6>Email Information</h6>
                            <p class="par-p mt-2">info@paragonafs.ca <br><br><br></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="../assets/js/jquery.repeater.min.js"></script>
    <script src="../assets/js/foundation-datepicker.js"></script>
    <script src="../assets/js/jquery.inputmask.min.js"></script>
    <script src="../assets/js/jquery.inputmask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <!--script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBA5S-DFToAdSBJP-2wD3eJGt3-ej-xLbk&callback=initAutocomplete&libraries=places&v=weekly" defer></script-->
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDak-JxDYbQ7l9CGSkSHDaUPy7rmLBEUEw&callback=initAutocomplete&libraries=places&v=weekly" defer></script>

    <script src="https://js.upload.io/upload-js/v2"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.js"></script> -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>


     
    <script>
    
    
   
 
 function initMap() {
    const input = document.getElementById('ship_address');
    const autocomplete = new google.maps.places.Autocomplete(input);
};


window.initMap = initMap;


        const submitButton = document.querySelector('.submit');
        
        



        var uploaded_id_proof = <?php echo isset($rowUser['id_proof']) ? json_encode(explode('<br>', $rowUser['id_proof'])) : '[]' ?>;
        var myIdProofDropzone = new Dropzone("#upload_id_proof", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {

                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_id_proof)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_id_proof', myIdProofDropzone, file);
                        });

                        $("#sortable_id_proof").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_id_proof li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_id_proof li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myIdProofDropzone.removeFile(file);
                    uploaded_id_proof.push(response);

                    console.log(uploaded_id_proof);

                    var li = $("#sortable_id_proof li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_id_proof");
                    submitButton.disabled = false;

                    document.querySelector(".id_proof_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabel");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_id_proof" // Define the element that should be used as click trigger to select files.
        });

        var uploaded_sin_number_document = [];
        var mySinNumberDocument = new Dropzone("#upload_sin_number_document", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {

                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_sin_number_document)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_sin_number_document', mySinNumberDocument, file);
                        });

                        $("#sortable_sin_number_document").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });


                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_sin_number_document li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_sin_number_document li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySinNumberDocument.removeFile(file);
                    uploaded_sin_number_document.push(response);

                    console.log(uploaded_sin_number_document);

                    var li = $("#sortable_sin_number_document li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_sin_number_document");
                    submitButton.disabled = false;

                    document.querySelector(".sin_number_document_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabelSin");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_sin_number_document" // Define the element that should be used as click trigger to select files.
        });
        $("#sortable_sin_number_document").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_sin_number_document");
            }
        });

        var uploaded_direct_deposit = <?php echo isset($rowUser['direct_deposits']) ? json_encode(explode('<br>', $rowUser['direct_deposits'])) : '[]' ?>;
        var myDirectDepositDropzone = new Dropzone("#upload_direct_deposit", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_direct_deposit)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_direct_deposit', myDirectDepositDropzone, file);
                        });

                        $("#sortable_direct_deposit").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_direct_deposit li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_direct_deposit li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myDirectDepositDropzone.removeFile(file);
                    uploaded_direct_deposit.push(response);

                    console.log(uploaded_direct_deposit);

                    var li = $("#sortable_direct_deposit li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_direct_deposit");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_direct_deposit" // Define the element that should be used as click trigger to select files.
        });

        var uploaded_college_receipt = <?php echo isset($rowUser['college_receipt']) ? json_encode(explode('<br>', $rowUser['college_receipt'])) : '[]' ?>;
        var myCollegeReceiptDropzone = new Dropzone("#upload_college_receipt", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_college_receipt)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_college_receipt', myCollegeReceiptDropzone, file);
                        });

                        $("#sortable_college_receipt").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_college_receipt li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_college_receipt li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myCollegeReceiptDropzone.removeFile(file);
                    uploaded_college_receipt.push(response);

                    console.log(uploaded_college_receipt);

                    var li = $("#sortable_college_receipt li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_college_receipt");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_college_receipt" // Define the element that should be used as click trigger to select files.
        });

        var uploaded_t_slips = <?php echo isset($rowUser['t_slips']) ? json_encode(explode('<br>', $rowUser['t_slips'])) : '[]' ?>;
        var myTSlipsDropzone = new Dropzone("#upload_t_slips", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_t_slips)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_t_slips', myTSlipsDropzone, file);
                        });

                        $("#sortable_t_slips").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_t_slips li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_t_slips li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myTSlipsDropzone.removeFile(file);
                    uploaded_t_slips.push(response);

                    console.log(uploaded_t_slips);

                    var li = $("#sortable_t_slips li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_t_slips");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_t_slips" // Define the element that should be used as click trigger to select files.
        });

        var uploaded_delivery_annual_tax = <?php echo isset($rowUser['tax_summary']) ? json_encode(explode('<br>', $rowUser['tax_summary'])) : '[]' ?>;
        var myDeliveryAnnualTaxDropzone = new Dropzone("#upload_delivery_annual_tax", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_delivery_annual_tax)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_delivery_annual_tax', myDeliveryAnnualTaxDropzone, file);
                        });

                        $("#sortable_delivery_annual_tax").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_delivery_annual_tax li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_delivery_annual_tax li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myDeliveryAnnualTaxDropzone.removeFile(file);
                    uploaded_delivery_annual_tax.push(response);

                    console.log(uploaded_delivery_annual_tax);

                    var li = $("#sortable_delivery_annual_tax li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_delivery_annual_tax");
                    submitButton.disabled = false;

                    document.querySelector(".delivery_annual_tax_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabelTax");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_delivery_annual_tax" // Define the element that should be used as click trigger to select files.
        });

        var uploaded_additional_documents = <?php echo isset($rowUser['additional_docs']) ? json_encode(explode('<br>', $rowUser['additional_docs'])) : '[]' ?>;
        var myAdditionalDocumentsDropzone = new Dropzone("#upload_additional_documents", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_additional_documents)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_additional_documents', myAdditionalDocumentsDropzone, file);
                        });

                        $("#sortable_additional_documents").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_additional_documents li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_additional_documents li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myAdditionalDocumentsDropzone.removeFile(file);
                    uploaded_additional_documents.push(response);

                    console.log(uploaded_additional_documents);

                    var li = $("#sortable_additional_documents li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_additional_documents");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_additional_documents" // Define the element that should be used as click trigger to select files.
        });


        // Initialize the sortable list - ID PROOF
        $("#sortable_id_proof").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_id_proof");
            }
        });
        // Initialize the sortable list - DIRECT DEPOSIT
        $("#sortable_direct_deposit").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_direct_deposit");
            }
        });
        // Initialize the sortable list - COLLEGE RECEIPT
        $("#sortable_college_receipt").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_college_receipt");
            }
        });
        // Initialize the sortable list - T SLIPS
        $("#sortable_t_slips").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_t_slips");
            }
        });
        // Initialize the sortable list - TAX SUMMARY
        $("#sortable_delivery_annual_tax").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_delivery_annual_tax");
            }
        });
        // Initialize the sortable list - TAX SUMMARY
        $("#sortable_additional_documents").sortable({
            update: function (event, ui) {
                updateInputField("#sortable_additional_documents");
            }
        });




        var spouse_uploaded_id_proof = <?php echo isset($rowUser['spouse_id_proof']) ? json_encode(explode('<br>', $rowUser['spouse_id_proof'])) : '[]' ?>;
        var mySpouseIdProofDropzone = new Dropzone("#spouse_upload_id_proof", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {

                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_id_proof)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_id_proof', mySpouseIdProofDropzone, file);
                        });

                        $("#spouse_sortable_id_proof").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_id_proof li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_id_proof li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseIdProofDropzone.removeFile(file);
                    uploaded_id_proof.push(response);

                    console.log(uploaded_id_proof);

                    var li = $("#spouse_sortable_id_proof li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_id_proof");
                    submitButton.disabled = false;

                    document.querySelector(".spouse_id_proof_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabel");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_id_proof" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - ID PROOF
        $("#spouse_sortable_id_proof").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_id_proof");
            }
        });

        var spouse_uploaded_sin_number_document = [];
        var mySpouseSinNumberDocumentDropzone = new Dropzone("#spouse_upload_sin_number_document", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {

                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_sin_number_document)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_sin_number_document', mySpouseSinNumberDocumentDropzone, file);
                        });

                        $("#spouse_sortable_sin_number_document").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_sin_number_document li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_sin_number_document li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseSinNumberDocumentDropzone.removeFile(file);
                    spouse_uploaded_sin_number_document.push(response);

                    console.log(spouse_uploaded_sin_number_document);

                    var li = $("#spouse_sortable_sin_number_document li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_sin_number_document");
                    submitButton.disabled = false;

                    document.querySelector(".spouse_sin_number_document_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabelSinSpouse");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_sin_number_document" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - ID PROOF
        $("#spouse_sortable_sin_number_document").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_sin_number_document");
            }
        });

        var spouse_uploaded_direct_deposit = <?php echo isset($rowUser['spouse_direct_deposits']) ? json_encode(explode('<br>', $rowUser['spouse_direct_deposits'])) : '[]' ?>;
        var mySpouseDirectDepositDropzone = new Dropzone("#spouse_upload_direct_deposit", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_direct_deposit)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_direct_deposit', mySpouseDirectDepositDropzone, file);
                        });

                        $("#spouse_sortable_direct_deposit").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_direct_deposit li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_direct_deposit li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseDirectDepositDropzone.removeFile(file);
                    spouse_uploaded_direct_deposit.push(response);

                    console.log(spouse_uploaded_direct_deposit);

                    var li = $("#spouse_sortable_direct_deposit li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_direct_deposit");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_direct_deposit" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - DIRECT DEPOSIT
        $("#spouse_sortable_direct_deposit").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_direct_deposit");
            }
        });

        var spouse_uploaded_college_receipt = <?php echo isset($rowUser['spouse_college_receipt']) ? json_encode(explode('<br>', $rowUser['spouse_college_receipt'])) : '[]' ?>;
        var mySpouseCollegeReceiptDropzone = new Dropzone("#spouse_upload_college_receipt", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_college_receipt)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_college_receipt', mySpouseCollegeReceiptDropzone, file);
                        });

                        $("#spouse_sortable_college_receipt").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_college_receipt li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_college_receipt li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseCollegeReceiptDropzone.removeFile(file);
                    spouse_uploaded_college_receipt.push(response);

                    console.log(spouse_uploaded_college_receipt);

                    var li = $("#spouse_sortable_college_receipt li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_college_receipt");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_college_receipt" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - COLLEGE RECEIPT
        $("#spouse_sortable_college_receipt").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_college_receipt");
            }
        });

        var spouse_uploaded_t_slips = <?php echo isset($rowUser['spouse_t_slips']) ? json_encode(explode('<br>', $rowUser['spouse_t_slips'])) : '[]' ?>;
        var mySpouseTSlipsDropzone = new Dropzone("#spouse_upload_t_slips", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_t_slips)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_t_slips', mySpouseTSlipsDropzone, file);
                        });

                        $("#spouse_sortable_t_slips").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_t_slips li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_t_slips li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseTSlipsDropzone.removeFile(file);
                    spouse_uploaded_t_slips.push(response);

                    console.log(spouse_uploaded_t_slips);

                    var li = $("#spouse_sortable_t_slips li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_t_slips");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_t_slips" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - T SLIPS
        $("#spouse_sortable_t_slips").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_t_slips");
            }
        });

        var spouse_uploaded_additional_documents = <?php echo isset($rowUser['spouse_additional_docs']) ? json_encode(explode('<br>', $rowUser['spouse_additional_docs'])) : '[]' ?>;
        var mySpouseAdditionalDocumentsDropzone = new Dropzone("#spouse_upload_additional_documents", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_additional_documents)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_additional_documents', mySpouseAdditionalDocumentsDropzone, file);
                        });

                        $("#spouse_sortable_additional_documents").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_additional_documents li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_additional_documents li:contains('" + fileName + "')");
                    li.remove();

                    document.querySelector(".spouse_delivery_annual_tax_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabelTax");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseAdditionalDocumentsDropzone.removeFile(file);
                    spouse_uploaded_additional_documents.push(response);

                    console.log(spouse_uploaded_additional_documents);

                    var li = $("#spouse_sortable_additional_documents li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_additional_documents");
                    submitButton.disabled = false;

                    document.querySelector(".spouse_delivery_annual_tax_required").style.border = "";
                    var errorLabel = document.querySelector("#errorLabelTax");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_additional_documents" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - ADDITIONAL DOCUMENTS
        $("#spouse_sortable_additional_documents").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_additional_documents");
            }
        });

        var spouse_uploaded_delivery_annual_tax = <?php echo isset($rowUser['spouse_tax_summary']) ? json_encode(explode('<br>', $rowUser['spouse_tax_summary'])) : '[]' ?>;
        var mySpouseDeliveryAnnualTaxDropzone = new Dropzone("#spouse_upload_delivery_annual_tax", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {
                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, spouse_uploaded_delivery_annual_tax)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#spouse_sortable_delivery_annual_tax', mySpouseDeliveryAnnualTaxDropzone, file);
                        });

                        $("#spouse_sortable_delivery_annual_tax").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_delivery_annual_tax li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#spouse_sortable_delivery_annual_tax li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    mySpouseDeliveryAnnualTaxDropzone.removeFile(file);
                    spouse_uploaded_delivery_annual_tax.push(response);

                    console.log(spouse_uploaded_delivery_annual_tax);

                    var li = $("#spouse_sortable_delivery_annual_tax li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#spouse_sortable_delivery_annual_tax");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".spouse_clickable_delivery_annual_tax" // Define the element that should be used as click trigger to select files.
        });
        // Initialize the sortable list - TAX SUMMARY
        $("#spouse_sortable_delivery_annual_tax").sortable({
            update: function (event, ui) {
                updateInputField("#spouse_sortable_delivery_annual_tax");
            }
        });


        
        if ($("#uploaded-links-input").val()) {
            // Initialize the list items and images from the input field
            var initialValues = $("#uploaded-links-input").val().split('<br>');
            initialValues.forEach(function (url) {
                // Extract filename from the URL
                var fileName = url.split('/').pop();
                var fileThumbnail = getFileThumbnail(url);
                
                var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='" + url + "' src='" + fileThumbnail.thumbnail + "'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size></p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress='' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0' style='width: 100%;'></div></div></div></li>");

                li.find(".delete-button").click(function() {
                    removeItem(li, '#sortable_id_proof');
                });

                $("#sortable_id_proof").append(li);
            });
        }

        if ($("#uploaded-direct-deposit").val()) {
            // Initialize the list items and images from the input field
            var initialValues = $("#uploaded-direct-deposit").val().split('<br>');
            initialValues.forEach(function (url) {
                // Extract filename from the URL
                var fileName = url.split('/').pop();
                var fileThumbnail = getFileThumbnail(url);
                
                var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='" + url + "' src='" + fileThumbnail.thumbnail + "'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size></p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress='' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0' style='width: 100%;'></div></div></div></li>");

                li.find(".delete-button").click(function() {
                    removeItem(li, '#sortable_direct_deposit');
                });

                $("#sortable_direct_deposit").append(li);
            });
        }

        if ($("#uploaded-college-receipt").val()) {
            // Initialize the list items and images from the input field
            var initialValues = $("#uploaded-college-receipt").val().split('<br>');
            initialValues.forEach(function (url) {
                // Extract filename from the URL
                var fileName = url.split('/').pop();
                var fileThumbnail = getFileThumbnail(url);
                
                var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='" + url + "' src='" + fileThumbnail.thumbnail + "'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size></p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress='' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0' style='width: 100%;'></div></div></div></li>");

                li.find(".delete-button").click(function() {
                    removeItem(li, '#sortable_college_receipt');
                });

                $("#sortable_college_receipt").append(li);
            });
        }

        if ($("#uploaded-t-slips").val()) {
            // Initialize the list items and images from the input field
            var initialValues = $("#uploaded-t-slips").val().split('<br>');
            initialValues.forEach(function (url) {
                // Extract filename from the URL
                var fileName = url.split('/').pop();
                var fileThumbnail = getFileThumbnail(url);
                
                var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='" + url + "' src='" + fileThumbnail.thumbnail + "'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size></p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress='' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0' style='width: 100%;'></div></div></div></li>");

                li.find(".delete-button").click(function() {
                    removeItem(li, '#sortable_t_slips');
                });

                $("#sortable_t_slips").append(li);
            });
        }

        if ($("#uploaded-delivery-annual-tax").val()) {
            // Initialize the list items and images from the input field
            var initialValues = $("#uploaded-delivery-annual-tax").val().split('<br>');
            initialValues.forEach(function (url) {
                // Extract filename from the URL
                var fileName = url.split('/').pop();
                var fileThumbnail = getFileThumbnail(url);
                
                var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='" + url + "' src='" + fileThumbnail.thumbnail + "'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size></p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress='' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0' style='width: 100%;'></div></div></div></li>");

                li.find(".delete-button").click(function() {
                    removeItem(li, '#sortable_delivery_annual_tax');
                });

                $("#sortable_delivery_annual_tax").append(li);
            });
        }

        if ($("#uploaded-additional-documents").val()) {
            // Initialize the list items and images from the input field
            var initialValues = $("#uploaded-additional-documents").val().split('<br>');
            initialValues.forEach(function (url) {
                // Extract filename from the URL
                var fileName = url.split('/').pop();
                var fileThumbnail = getFileThumbnail(url);
                
                var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='" + url + "' src='" + fileThumbnail.thumbnail + "'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size></p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress='' aria-valuemin='0' aria-valuemax='100' aria-valuenow='0' style='width: 100%;'></div></div></div></li>");

                li.find(".delete-button").click(function() {
                    removeItem(li, '#sortable_additional_documents');
                });

                $("#sortable_additional_documents").append(li);
            });
        }


        function formatFileSize(size) {
            var units = ["B", "KB", "MB", "GB", "TB"];
            var i = 0;
            while (size > 1024) {
                size /= 1024;
                i++;
            }
            return size.toFixed(2) + " " + units[i];
        }

        function getFileThumbnail(url) {
            var ext = getFileExtensionFromUrl(url); // Get extension
            var thumbnailPath = "";

            // Check extension
            if (ext === 'pdf') {
                thumbnailPath = "../assets/images/pdf_icon.png"; // default image path
            } else if (ext === 'docx') {
                thumbnailPath = "../assets/images/word_icon.png"; // default image path
            } else if (ext === 'doc') {
                thumbnailPath = "../assets/images/doc_icon.png"; // default image path
            } else if (ext === 'txt') {
                thumbnailPath = "../assets/images/txt_icon.png"; // default image path
            } else if (ext === 'xls') {
                thumbnailPath = "../assets/images/xls_icon.png"; // default image path
            } else if (ext === 'xlsx') {
                thumbnailPath = "../assets/images/xlsx_icon.png"; // default image path
            } else if (ext === 'csv') {
                thumbnailPath = "../assets/images/csv_icon.png"; // default image path for CSV
            } else {
                // For non-image files, return the original URL as well
                return { thumbnail: url, original: url };
            }

            return { thumbnail: thumbnailPath, original: url };
        }

        function getFileExtensionFromUrl(url) {
            var pathArray = url.split('.');
            if (pathArray.length > 1) {
                var extension = pathArray.pop().toLowerCase();
                return extension;
            }
            return null;
        }

        function fileExists(fileName, inputUrls) {
            // Extract the filename without path and timestamp
            var cleanFileName = fileName.replace(/^.*[\\\/]/, '').replace(/\.\w+$/, '');

            console.log('Clean FileName:', cleanFileName);

            return inputUrls.some(function (uploadedFile) {
                // Extract the filename without path and timestamp from uploaded file
                var fileNameWithTimestamp = uploadedFile.replace(/^.*[\\\/]/, '');
                var fileNameWithoutTimestamp = fileNameWithTimestamp.replace(/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.\w+$/, '').replace(/\.\w+$/, '');

                console.log('Clean Uploaded FileName:', fileNameWithoutTimestamp);

                // Compare filenames (case-insensitive)
                var exists = fileNameWithoutTimestamp.toLowerCase() === cleanFileName.toLowerCase();

                console.log('File Exists:', exists);

                return exists;
            });
        }

        function removeItem(li, selector, dropzone, current_file) {
            dropzone.removeFile(current_file);
            
            var img = li.find("img");
            var fullUrl = img.attr("id");

            // Extract the filename from the URL
            var parts = fullUrl.split('/');
            var filename = parts[parts.length - 1];

            // Replace spaces with underscores
            filename = filename.replace(/ /g, "_");

            console.log(filename);

            // Send an AJAX request to delete the file from your server
            $.ajax({
                url: "../delete.php",
                type: "POST",
                data: {
                    filename: filename
                },
                success: function (response) {
                    console.log(response);
                    if (response !== '') {
                        createAlert(response, "", "", "danger", true, true, "pageMessages");
                    }
                },
                error: function (error) {
                    console.error("Error deleting file:", error);
                }
            });

            // Update the hidden input field
            var inputField = $(selector).siblings('input');
            var currentValues = inputField.val().split('<br>');
            currentValues.splice(currentValues.indexOf(filename), 1);
            inputField.val(currentValues.join('<br>'));

            submitButton.disabled = false;
            // Call default removedfile function to remove the file preview
            li.remove();
            updateInputField(selector);
        }

        function updateInputField(selector) {
            var sortedValues = $(selector + " li").map(function () {
                return $(this).find("img").attr("id");
            }).get().join('<br>');
            
            var siblingInput = $(selector).siblings('input');
            var siblingName = siblingInput.attr('name');
            
            console.log("Sibling Name:", siblingName);
            
            siblingInput.val(sortedValues);

            // $.ajax({
            //     type: "POST",
            //     url: "../update_document_uploads.php",
            //     data: {
            //         inputName: siblingName,
            //         inputValue: sortedValues
            //     },
            //     success: function(response) {
            //         console.log("Data updated successfully:", response);
            //     },
            //     error: function(error) {
            //         console.error("Error updating data:", error);
            //     }
            // });

            console.log(selector);
        }

    </script>

    <script>
        // When the button is clicked, show the modal and move the row data
        $('#edit_person_info').on('click', function () {
            // Show the modal
            $('#editModal').modal('show');

            // Set the default value of modal_first_name to match firstName
            $('#modal_first_name').val($('[name="firstName"]').val());
            $('.modal_lastName').val($('[name="lastName"]').val());

            // Listen for input change
            $('#modal_first_name').on('input', function () {
                $('[name="firstName"]').val($(this).val());
            });
            $('.modal_lastName').on('input', function () {
                $('[name="lastName"]').val($(this).val());
            });

        });

        // When the modal is closed, bring back the original row to its original position
        $('#editModal').on('hide.bs.modal', function () {
            console.log('Modal closed event triggered');

            // var modalBody = document.querySelector(".edit_personal_info_modal_body");

            // // Capture the updated values from the cloned form in the modal
            // var updatedValues = $(modalBody).serialize();

            // console.log(updatedValues);

            // // var contentBody = document.querySelector(".personal_basic_information");
            // // contentBody.innerHTML = ''; // Clear previous content

            // // var updatedRow = $('.edit_personal_info_modal_body').clone();
            // // // Clone the original row and append it to modal body
            // // updatedRow.appendTo('.personal_basic_information');


            // // Clear the modal body for the next use
            // modalBody.innerHTML = '';
        });



        $(document).ready(function() {
            // check if email_sent parameter is present in URL
            var emailSent = new URLSearchParams(window.location.search).get("email_sent");

            // $(window).on('unload', function(){
            //     navigator.sendBeacon('../auth/logout.php');
            // });

            // if email_sent is present, show modal and remove parameter from URL
            if (emailSent === "success") {
                $('#emailSentModal').modal('show');
                var url = window.location.href.split("?")[0];
                history.replaceState(null, null, url);
            }

            $('[data-toggle="popover"]').popover({
                placement: 'top',
                trigger: 'hover'
            });
        });

        function toggleEditing() {
            // Select all input elements with the readonly attribute and remove the readonly attribute and class
            var readonlyInputs = document.querySelectorAll('input[readonly], .readonly-field');
            readonlyInputs.forEach(function (element) {
                element.removeAttribute('readonly');
                element.classList.remove('readonly-field');
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            const maritalStatusRadios = document.querySelectorAll('input[name="marital_status"]');
            const dateMaritalStatusLabel = document.getElementById('date_marital_status');
            const dateMaritalChangeLabel = document.getElementById('marital_change_label');


            for (let i = 0; i < maritalStatusRadios.length; i++) {
                maritalStatusRadios[i].addEventListener('change', function() {
                    if (this.value === 'Married') {
                        dateMaritalStatusLabel.innerHTML = 'Date of Marriage';
                    } else if (this.value === 'Common in Law') {
                        dateMaritalStatusLabel.innerHTML = 'Date of Status Start';
                    } else if (this.value === 'Separated') {
                        dateMaritalChangeLabel.innerHTML = 'Date of Separation';
                    } else if (this.value === 'Widow') {
                        dateMaritalChangeLabel.innerHTML = 'Date Widowed';
                    } else if (this.value === 'Divorced') {
                        dateMaritalChangeLabel.innerHTML = 'Date of Divorce';
                    }
                });
            }

            const popover = new bootstrap.Popover('.popover-dismiss', {
                trigger: 'focus'
            })


        });
    </script>

    <script>
        $(document).ready(function() {

            document.querySelector('#validationCustom04').addEventListener('change', function() {
                let selectedOption = this.value;
                let secondSelect = document.querySelector('select[name="move_to"]');
                secondSelect.querySelectorAll('option').forEach(function(option) {
                    if (option.value === selectedOption) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = 'block';
                    }
                });
            });

            document.querySelector('select[name="move_to"]').addEventListener('change', function() {
                let selectedOption = this.value;
                let firstSelect = document.querySelector('#validationCustom04');
                firstSelect.querySelectorAll('option').forEach(function(option) {
                    if (option.value === selectedOption) {
                        option.style.display = 'none';
                    } else {
                        option.style.display = 'block';
                    }
                });
            });


            $('#date_entry').on('change blur', function() {
                const date = new Date($(this).val());

                const firstYear = subtractYears(date, 0); // Get the value of the first year

                $('#year1').val(firstYear);
                $('#year2').val(firstYear - 1);
                $('#year3').val(firstYear - 2);

                $('#year1_income').attr('placeholder', 'Year ' + firstYear + ' Income');
                $('#year2_income').attr('placeholder', 'Year ' + (firstYear - 1)  + ' Income');
                $('#year3_income').attr('placeholder', 'Year ' + (firstYear - 2)  + ' Income');
            });

            $('#spouse_date_entry').on('change blur', function() {
                const date = new Date($(this).val());

                const firstYear = subtractYears(date, 0); // Get the value of the first year

                $('#spouse_year1').val(firstYear);
                $('#spouse_year2').val(firstYear - 1); // Subtract 1 from the first year
                $('#spouse_year3').val(firstYear - 2); // Subtract 2 from the first year

                $('#spouse_year1_income').attr('placeholder', 'Year ' + firstYear + ' Income');
                $('#spouse_year2_income').attr('placeholder', 'Year ' + (firstYear - 1) + ' Income');
                $('#spouse_year3_income').attr('placeholder', 'Year ' + (firstYear - 2) + ' Income');
            });

            
            function subtractYears(date, years) {
                // Subtract one day
                date.setDate(date.getDate() - 1);
                // Subtract the specified number of years
                date.setFullYear(date.getFullYear() - years);
                // Return the year
                return date.getFullYear();
            }

        });

        function createAlert(title, summary, details, severity, dismissible, autoDismiss, appendToId) {
            var iconMap = {
                info: "fa fa-info-circle",
                success: "fa fa-thumbs-up",
                warning: "fa fa-exclamation-triangle",
                danger: "fa ffa fa-exclamation-circle"
            };

            var iconAdded = false;

            var alertClasses = ["alert", "animated", "flipInX"];
            alertClasses.push("alert-" + severity.toLowerCase());

            if (dismissible) {
                alertClasses.push("alert-dismissible");
            }

            var msgIcon = $("<i />", {
                "class": iconMap[severity] // you need to quote "class" since it's a reserved keyword
            });

            var msg = $("<div />", {
                "class": alertClasses.join(" ") // you need to quote "class" since it's a reserved keyword
            });

            if (title) {
                var msgTitle = $("<strong />", {
                    html: title
                }).appendTo(msg);

                if (!iconAdded) {
                    msgTitle.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (summary) {
                var msgSummary = $("<strong />", {
                    html: summary
                }).appendTo(msg);

                if (!iconAdded) {
                    msgSummary.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (details) {
                var msgDetails = $("<h6 />", {
                    html: details
                }).appendTo(msg);

                if (!iconAdded) {
                    msgDetails.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (dismissible) {
                var msgClose = $("<span />", {
                    "class": "close",
                    "data-dismiss": "alert",
                    html: "<i class='fa fa-times-circle'></i>"
                }).appendTo(msg);

                msgClose.on("click", function() {
                    msg.remove();
                });
            }

            $('#' + appendToId).prepend(msg);
            msg.fadeIn();

            if (autoDismiss) {
                setTimeout(function() {
                    msg.removeClass("flipInX").addClass("flipOutX");
                    setTimeout(function() {
                        msg.fadeOut(function() {
                            msg.remove();
                        });
                    }, 1000);
                }, 5000);
            }
        }


        $('.repeater').repeater({
            // options and callbacks here
            // isFirstItemUndeletable: true,
            show: function() {
                $(this).slideDown();
            },
        });



        $(document).ready(function() {

            function updateChildInfoDeleteVisibility() {
                var childInfoDeleteElements = $(".child_info_delete");
                console.log("Number of child delete elements:", childInfoDeleteElements.length);

                var firstRowInputs = $("#have_child_body tr:first-child").find(".child_first_name, .child_last_name, .child_date_birth");
                console.log("Number of first row child inputs:", firstRowInputs.length);

                var firstRowDeleteElement = $("#have_child_body tr:first-child").find(".child_info_delete_first");
                console.log("Number of first row child delete elements:", firstRowDeleteElement.length);

                if (childInfoDeleteElements.length > 1) {
                    // $("#have_child_body tr:first-child .child_info_delete").hide();
                    // console.log("Hiding child delete elements");
                    // childInfoDeleteElements.not(":first").show();
                    // console.log("Showing child delete elements except the first one");
                } else {
                    childInfoDeleteElements.hide();
                    console.log("Hiding all child delete elements");
                    var isAnyInputNotEmpty = false;

                    firstRowInputs.each(function() {
                        if ($(this).val().trim().length > 0) {
                            isAnyInputNotEmpty = true;
                            return false;
                        }
                    });
					
                    if (isAnyInputNotEmpty) {
                        if (firstRowDeleteElement.length === 0) {
                            var newDeleteElement = $(' <span class="form-control child_info_delete_first" type="button" style="text-align: center;width: 50px;"><i class="fas fa-trash-alt" style="color: red;"></i></span>');
                            $("#have_child_body tr:first-child td:nth-child(5)").append(newDeleteElement);
                            console.log("Appending new child delete element");
                        }
                        firstRowDeleteElement.show();
                        console.log("Showing first row child delete element");
                    } else {
                        firstRowDeleteElement.hide();
                        console.log("Hiding first row child delete element");
                    } 
                } 
            }
            
            // Call the updateChildInfoDeleteVisibility function on page load
            updateChildInfoDeleteVisibility();

            // Add click event listener to the add button
            $('.child_repeater').click(function() {
                // Call the updateChildInfoDeleteVisibility function after the new row is added
                setTimeout(updateChildInfoDeleteVisibility, 100);
            });

            // Add keyup event listener to the input elements
            $(document).on("keyup", ".child_first_name, .child_last_name, .child_date_birth", function() {
                // Call the updateChildInfoDeleteVisibility function
                updateChildInfoDeleteVisibility();
            });

            // Add click event listener to the delete buttons
            $(document).on("click", ".child_info_delete, .child_info_delete_first", function() {
                var parentRow = $(this).closest("tr");

                // Clear the values of the inputs in the current row
                parentRow.find(".child_first_name, .child_last_name, .child_date_birth").val("");

                // Call the updateChildInfoDeleteVisibility function
                updateChildInfoDeleteVisibility();
            });




            function updateRentInfoDeleteVisibility() {
                var rentInfoDeleteElements = $(".rent_info_delete");
                var firstRowInputs = $("#rent_id tr:first-child").find(".rent_address_search, .total_month_rent, .total_rent_paid");
                var firstRowDeleteElement = $("#rent_id tr:first-child").find(".rent_info_delete_first");


                if (rentInfoDeleteElements.length > 1) {
                    $("#rent_id rent_idtr:first-child .rent_info_delete").hide();
                    rentInfoDeleteElements.not(":first").show();
                } else {
                    rentInfoDeleteElements.hide();
                    var isAnyInputNotEmpty = false;

                    firstRowInputs.each(function() {
                        if ($(this).val().trim().length > 0) {
                            isAnyInputNotEmpty = true;
                            return false;
                        }
                    });

                    if (isAnyInputNotEmpty) {
                        if (firstRowDeleteElement.length === 0) {
                            var newDeleteElement = $('<span class="form-control rent_info_delete_first" type="button" style="text-align: center; width: 50px;"><i class="fas fa-trash-alt" style="color: red;"></i></span>');
                            $("#rent_id tr:first-child td:nth-child(5)").append(newDeleteElement);
                            console.log("Appending new delete element");
                        }
                        firstRowDeleteElement.show();
                    } else {
                        firstRowDeleteElement.hide();
                    }
                }
            }

            // Call the updateRentInfoDeleteVisibility function on page load
            updateRentInfoDeleteVisibility();

            // Add click event listener to the add button
            $('.rent_repeater').click(function() {
                // Call the updateRentInfoDeleteVisibility function after the new row is added
                setTimeout(updateRentInfoDeleteVisibility, 100);
            });

            // Add keyup event listener to the input elements
            $(document).on("keyup", ".rent_address_search, .total_month_rent, .total_rent_paid", function() {
                // Call the updateRentInfoDeleteVisibility function
                updateRentInfoDeleteVisibility();
            });

            // Add click event listener to the delete buttons
            $(document).on("click", ".rent_info_delete, .rent_info_delete_first", function() {
                var parentRow = $(this).closest("tr");

                // Clear the values of the inputs in the current row
                parentRow.find(".rent_address_search, .total_month_rent, .total_rent_paid").val("");

                // Call the updateRentInfoDeleteVisibility function
                updateRentInfoDeleteVisibility();
            });


            

            // Function to hide the first delete icon if there's only one, else show it
            function updateSpouseRentInfoDeleteVisibility() {
                var rentInfoDeleteElements = $(".spouse_rent_info_delete");
                var firstRowInputs = $("#spouse_rent_id tr:first-child").find(".spouse_rent_address_search, .spouse_total_month_rent, .spouse_total_rent_paid");
                var firstRowDeleteElement = $("#spouse_rent_id tr:first-child").find(".spouse_rent_info_delete_first");

                if (rentInfoDeleteElements.length > 1) {
                    $("#spouse_rent_id tr:first-child .spouse_rent_info_delete").hide();
                    rentInfoDeleteElements.not(":first").show();
                } else {
                    rentInfoDeleteElements.hide();
                    var isAnyInputNotEmpty = false;

                    firstRowInputs.each(function() {
                        if ($(this).val().trim().length > 0) {
                            isAnyInputNotEmpty = true;
                            return false;
                        }
                    });

                    if (isAnyInputNotEmpty) {
                        if (firstRowDeleteElement.length === 0) {
                            var newDeleteElement = $('<span class="form-control spouse_rent_info_delete_first" type="button" style="text-align: center; width: 50px;"><i class="fas fa-trash-alt" style="color: red;"></i></span>');
                            $("#spouse_rent_id tr:first-child td:nth-child(5)").append(newDeleteElement);
                        }
                        firstRowDeleteElement.show();
                    } else {
                        firstRowDeleteElement.hide();
                    }
                }
            }

            // Call the updateSpouseRentInfoDeleteVisibility function on page load
            updateSpouseRentInfoDeleteVisibility();

            // Add click event listener to the add button
            $('.spouse_rent_repeater').click(function() {
                // Call the updateSpouseRentInfoDeleteVisibility function after the new row is added
                setTimeout(updateSpouseRentInfoDeleteVisibility, 100);
            });

            // Add keyup event listener to the input elements
            $(document).on("keyup", ".spouse_rent_address_search, .spouse_total_month_rent, .spouse_total_rent_paid", function() {
                // Call the updateSpouseRentInfoDeleteVisibility function
                updateSpouseRentInfoDeleteVisibility();
            });

            // Add click event listener to the delete buttons
            $(document).on("click", ".spouse_rent_info_delete, .spouse_rent_info_delete_first", function() {
                var parentRow = $(this).closest("tr");

                // Clear the values of the inputs in the current row
                parentRow.find(".spouse_rent_address_search, .spouse_total_month_rent, .spouse_total_rent_paid").val("");

                // Call the updateSpouseRentInfoDeleteVisibility function
                updateSpouseRentInfoDeleteVisibility();
            });


        });



        $(function() {
            $('body').scrollTop(0);
        });
        
        // $('body').on('focus', ".child_date_birth", function() {
        //     $(this).fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // });
        // $('#purchase_first_home').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#date_birthdate').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#date_movedate').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#date_entry').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#spouse_date_birth').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#date_marriage').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#marital_change').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#hst_start_date').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#hst_end_date').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#spouse_date_entry').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('.child_date_birth').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#spouse_hst_start_date').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        // $('#spouse_hst_end_date').fdatepicker().inputmask("99/99/9999",{ "placeholder": "mm/dd/yyyy" });
        
        
        $('body').on('focus', ".child_date_birth", function() {
            $(this).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd M yy',
                yearRange: '1900:' + new Date().getFullYear(),
                onSelect: function(dateText, inst) {
                    $(this).trigger('change'); // Ensures validation detects change

                    // 🔹 Remove duplicate error messages
                    let errorLabel = $(this).closest('td').find("label.error");
                    if (errorLabel.length > 1) {
                        errorLabel.not(":first").remove(); // Keep only the first label
                    }

                    // 🔹 Hide error if a valid date is selected
                    $(this).valid();
                }
            });
        });

        $('#purchase_first_home').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#date_birthdate').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#date_movedate').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#date_entry').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#spouse_date_birth').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#date_marriage').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#marital_change').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#hst_start_date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#hst_end_date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#spouse_date_entry').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

       
        
        $('body').on('focus', ".child_date_birth", function() {
            if (!$(this).hasClass("hasDatepicker")) { // Prevents re-initialization
                $(this).datepicker({
                    changeMonth: true,
                    changeYear: true,
                    dateFormat: 'dd M yy',
                    yearRange: '1900:' + new Date().getFullYear(),
                    onSelect: function(dateText, inst) {
                        $(this).trigger('change'); // Ensures validation detects change

                         // 🔹 Remove duplicate error messages by keeping only ONE
                        let errorLabels = $(this).closest('.date-container').find("label.error");
                        if (errorLabels.length > 1) {
                            errorLabels.not(":first").remove(); // Keep only the first error message
                        }

                        // 🔹 Hide error if a valid date is selected
                        $(this).valid();
                    }
                });
            }
        });
        // 🔹 Extra safeguard: Clear errors when input manually
        $(document).on('change', '.child_date_birth', function() {
            $(this).valid();
        });
        $('#spouse_hst_start_date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        $('#spouse_hst_end_date').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        // Get file extension
        function checkFileExt(filename) {
            filename = filename.toLowerCase();
            return filename.split('.').pop();
        }

        // Check if there are no files added
        document.querySelector('button.submit').addEventListener("click", function(e) {

            // Function to get the value of the checked radio button by name
            function getCheckedRadioValue(name) {
                var radio = document.querySelector('input[name="' + name + '"]:checked');
                return radio ? radio.value : null;
            }

            // Get the value of the radio button named "income_delivery"
            var incomeDeliveryValue = getCheckedRadioValue("income_delivery");
            console.log("Income delivery value: ", incomeDeliveryValue);

            var uploadAnnualTaxSummaryCheck = document.getElementById("uploaded-delivery-annual-tax");
            var uploadAnnualTaxSummaryContainer = document.getElementById("upload_delivery_annual_tax");

            if (incomeDeliveryValue === "Yes") {
                if (uploadAnnualTaxSummaryCheck.value.trim() === '') {
                    // Check if error label already exists
                    if (!document.querySelector("#errorLabelTax")) {
                        // Display error message
                        var errorLabelTax = document.createElement("label");
                        errorLabelTax.innerHTML = "Annual Tax Summary is required";
                        errorLabelTax.style.color = "red";
                        errorLabelTax.id = "errorLabelTax";
                        document.querySelector(".delivery_annual_tax_required").appendChild(errorLabelTax);
                        document.querySelector(".delivery_annual_tax_required").style.border = "2px solid red";
                    }

                    e.preventDefault();
                } else {
                    // Remove error label if it exists
                    var errorLabel = document.querySelector("#errorLabelTax");
                    if (errorLabel) {
                        errorLabel.remove();
                    }
                }
            }


            // Fetch the hidden input element
            var uploadIdProofCheck = document.getElementById("uploaded-links-input");

            // Check if the value of the hidden input is empty
            if (uploadIdProofCheck.value.trim() === '') {
                // Display error message
                var errorLabel = document.querySelector("#errorLabel");

                if (!errorLabel) {
                    errorLabel = document.createElement("label");
                    errorLabel.innerHTML = "ID Proof is required";
                    errorLabel.style.color = "red";
                    errorLabel.id = "errorLabel";
                    document.querySelector(".id_proof_required").appendChild(errorLabel);
                    document.querySelector(".id_proof_required").style.border = "2px solid red";
                }

                // Prevent form submission
                event.preventDefault();
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabel");
                if (errorLabel) {
                    errorLabel.remove();
                }

            }

        
        
        
      
     
        
        
        
        
        
        
        
            // Fetch the hidden input element
            var uploadSinNumberDocumentCheck = document.getElementById("uploaded_sin_number_document");

            // Check if the value of the hidden input is empty
            if (uploadSinNumberDocumentCheck.value.trim() === '') {
                // Display error message
                var errorLabel = document.querySelector("#errorLabelSin");

                if (!errorLabel) {
                    errorLabel = document.createElement("label");
                    errorLabel.innerHTML = "SIN Number Document is required";
                    errorLabel.style.color = "red";
                    errorLabel.id = "errorLabelSin";
                    document.querySelector(".sin_number_document_required").appendChild(errorLabel);
                    document.querySelector(".sin_number_document_required").style.border = "2px solid red";
                }

                // Prevent form submission
                event.preventDefault();
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabelSin");
                if (errorLabel) {
                    errorLabel.remove();
                }

            }

            if (incomeDeliveryValue === null) {

                // createAlert(
                //     'Please Fill In All Required Fields',
                //     '',
                //     '',
                //     'danger',
                //     true,
                //     true,
                //     'pageMessages'
                // );
                var do_you_have_income = document.getElementById("rent_info");

                $('.CustomTabs ul.tabs li:eq(0) a').click();

                var errors = $(".error").get().reverse(); // Get all elements with class 'error' and reverse their order
				var errorNames = new Set(); // Create a new Set to store the names of the input fields

				errors.forEach(function (element) {
					if (element.type === 'radio') {
                        $(element).focus(); // Focus on each element 
                        do_you_have_income.scrollIntoView();
					} else {
                        $(element).focus(); // Focus on each element 
                    }


					if (element.name) { // Check if the name property is not undefined
						errorNames.add(element.name); // Add the name of each element to the Set
					}
				});

				// Convert the Set back to an Array
				var uniqueErrorNames = Array.from(errorNames);

				uniqueErrorNames.forEach(function (name) {
					console.log(name); // Log the name of each unique element
					var email = $('input[name="email"]').val(); // Get the value of the input field with name 'email'
					$.ajax({
						url: '../log_errors.php',
						type: 'post',
						data: { 'error': name, 'email': email },
						success: function (data) {

						}
					});
				});
                return false;

            }

            // Fetch the hidden input element
            var uploadSpouseIdProofCheck = document.getElementById("spouse_uploaded-links-input");

            // Check if the value of the hidden input is empty
            if (uploadSpouseIdProofCheck.value.trim() === '') {
                // Display error message
                var errorLabel = document.querySelector("#errorLabel");

                if (!errorLabel) {
                    errorLabel = document.createElement("label");
                    errorLabel.innerHTML = "Spouse ID Proof is required";
                    errorLabel.style.color = "red";
                    errorLabel.id = "errorLabel";
                    document.querySelector(".spouse_id_proof_required").appendChild(errorLabel);
                    document.querySelector(".spouse_id_proof_required").style.border = "2px solid red";
                }

                if (uploadIdProofCheck.value.trim() === '') {
                    $('.CustomTabs ul.tabs li:eq(0) a').click();
                } else {
                    // Move to the second tab
                    var spouseTabParent = document.querySelector("#applicant_spouse_tab").parentNode;
                    var spouseTabParentDisplayStyle = window.getComputedStyle(spouseTabParent).display;

                    if (spouseTabParentDisplayStyle !== "none") {
                        $('.CustomTabs ul.tabs li:eq(1) a').click();
                    }
                }
                
                // Prevent form submission
                event.preventDefault();
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabel");
                if (errorLabel) {
                    errorLabel.remove();
                }

            }

            var incomeSpouseDeliveryValue = getCheckedRadioValue("spouse_income_delivery");
            var uploadSpouseAnnualTaxSummaryCheck = document.getElementById("spouse_uploaded-delivery-annual-tax");

            if (incomeSpouseDeliveryValue === "Yes") {
                if (uploadSpouseAnnualTaxSummaryCheck.value.trim() === '') {
                    // Check if error label already exists
                    if (!document.querySelector("#errorLabelTax")) {
                        // Display error message
                        var errorLabelTax = document.createElement("label");
                        errorLabelTax.innerHTML = "Annual Tax Summary is required";
                        errorLabelTax.style.color = "red";
                        errorLabelTax.id = "errorLabelTax";
                        document.querySelector(".spouse_delivery_annual_tax_required").appendChild(errorLabelTax);
                        document.querySelector(".spouse_delivery_annual_tax_required").style.border = "2px solid red";
                    }
                    // Prevent form submission
                    e.preventDefault();
                } else {
                    // Remove error label if it exists
                    var errorLabel = document.querySelector("#errorLabelTax");
                    if (errorLabel) {
                        errorLabel.remove();
                    }
                }
            }

            // Fetch the hidden input element
            var uploadSpouseSinNumberDocumentCheck = document.getElementById("spouse_sin_number_document");

            // Check if the value of the hidden input is empty
            if (uploadSpouseSinNumberDocumentCheck.value.trim() === '') {
                // Display error message
                var errorLabel = document.querySelector("#errorLabelSinSpouse");

                if (!errorLabel) {
                    errorLabel = document.createElement("label");
                    errorLabel.innerHTML = "SIN Number Document is required";
                    errorLabel.style.color = "red";
                    errorLabel.id = "errorLabelSinSpouse";
                    document.querySelector(".spouse_sin_number_document_required").appendChild(errorLabel);
                    document.querySelector(".spouse_sin_number_document_required").style.border = "2px solid red";
                }

                // Prevent form submission
                event.preventDefault();
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabelSinSpouse");
                if (errorLabel) {
                    errorLabel.remove();
                }
                document.querySelector(".spouse_sin_number_document_required").style.border = "";
            }
        });

    </script>

    <script>
        $(document).ready(function() {
            // $(".acc-container .acc:nth-child(1) .acc-head").addClass("active");
            // $(".acc-container .acc:nth-child(1) .acc-content").slideDown();
            $(".acc-head").on("click", function() {
                if ($(this).hasClass("active")) {
                    $(this).siblings(".acc-content").slideUp();
                    $(this).removeClass("active");
                } else {
                    $(".acc-content").slideUp();
                    $(".acc-head").removeClass("active");
                    $(this).siblings(".acc-content").slideToggle();
                    $(this).toggleClass("active");
                }
            });
        });
		$.validator.setDefaults({
        showErrors: function(errorMap, errorList) {
        this.defaultShowErrors(); // Keep default behavior

        // Remove duplicate errors dynamically
        $(".date-container").each(function() {
            let errorLabels = $(this).find("label.error");
            if (errorLabels.length > 1) {
                errorLabels.not(":first").remove();
            }
        });
    }
});
    </script>

    <script type="text/javascript" src="../multi-form.js"></script>
    

        
        
        
 
</body>

</html>