<?php
session_start();
// error_reporting(0);
 include '../auth/config.php';

// SESSION CHECK SET OR NOT
/*if (!isset($_SESSION['email'])) {
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

} */


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
    <title> Apply Corporate Tax </title>


    <link rel="icon" type="image/x-icon" href="../assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" type="text/css" href="../multi-form.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="../assets/css/foundation-datepicker.css">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.0/animate.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" type="text/css" />

  <script src="https://www.google.com/recaptcha/api.js" async defer></script>






  
  
	<style>
        .upload_zone{
            margin-top:1.5rem!important;
          
input.form-control.dropzone-label {
    margin-left: 30px;
}
        }
    </style>
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

            

           /*  $('#hide_movedate').click(function() {
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
            } */

           /* $('#body_purchase_first_home').hide()
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
            } */

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

            /*var show_delivery_tax_yes = document.getElementById('show_delivery_tax');
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
		*/

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

            /*var show_show_hst = document.getElementById('show_hst');
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
			*/
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
                    firstName: 'required',
                    lastName: 'required',
                    ship_address: 'required',
                    locality: 'required',
                	summary_income: 'required',
                	first_filingtax: 'required',
                    business_gst_number: 'required',
                    /*business_activity:'required',
                    income_on_sin:'required',
                    summary_income:'required',
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
                    spouse_years_tax_return: 'required', */
                    /*marital_change: 'required',
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
                    hst_end_date: 'required',*/
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
                    firstName: 'First Name is required',
                    lastName: 'Last Name is required',
                    ship_address: 'Address is required',
                    locality: 'City is required',
                    state: 'State is required',
                    postcode: 'Postal Code is required',
                    country: 'Country/Region is required',
                    email: 'Email Address is required',
                    phone: 'Phone no is required',
                    sin_number: 'SIN Number is required',
                    business_activity: 'Business Activity is required',
                    income_on_sin: 'Total income is required',
              
                    /*spouse_firstname: 'Spouse First Name is required',
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
                    spouse_years_tax_return: 'Which Years Your Spouse want to file tax returns is required', */
                    /*marital_change: 'required',
                    "data[0][child_first_name]": 'Child First Name is required',
                    "data[0][child_last_name]": 'Child Last Name is required',
                    "data[0][child_date_birth]": "Child Date Birth is required",
                    "data[1][child_first_name]": 'Child First Name is required',
                    "data[1][child_last_name]": 'Child Last Name is required',
                    "data[1][child_date_birth]": "Child Date Birth is required",
                    "data[2][child_first_name]": 'Child First Name is required',
                    "data[2][child_last_name]": 'Child Last Name is required',
                    "data[2][child_date_birth]": "Child Date Birth is required",
                    "data[3][child_first_name]": 'Child First Name is required',
                    "data[3][child_last_name]": 'Child Last Name is required',
                    "data[3][child_date_birth]": "Child Date Birth is required",
                    "data[4][child_first_name]": 'Child First Name is required',
                    "data[4][child_last_name]": 'Child Last Name is required',
                    "data[4][child_date_birth]": "Child Date Birth is required",
                    // another_province: 'Did you move to another province is required',
                    // first_fillingtax: 'Is this the first time you are filing tax is required',
                    marital_status: 'Marital Status is required',
                    // first_time_buyer: 'First Time Buyer is required',
                   // summary_expenses: 'Summary of Expense is required',
                    email: {
                        required: 'Email is required',
                        email: 'Please enter a valid e-mail',
                    },
                    phone: {
                        required: 'Phone number is required',
                        digits: 'Only numbers are allowed in this field',
                    },
                    date: {
                       // required: 'Date is required',
                        minlength: 'Date should be a 2 digit number, e.i., 01 or 20',
                        maxlength: 'Date should be a 2 digit number, e.i., 01 or 20',
                        digits: 'Date should be a number',
                    },
                    month: {
                       // required: 'Month is required',
                        minlength: 'Month should be a 2 digit number, e.i., 01 or 12',
                        maxlength: 'Month should be a 2 digit number, e.i., 01 or 12',
                        digits: 'Only numbers are allowed in this field',
                    },
                    year: {
                        //required: 'Year is required',
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
                    hst_end_date: 'End Date is required',*/
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

    </style>

</head>

<body>

    <?php include './headers2.php'; ?>
       <?php include_once '../navbar.php'; ?>\\
   
    <!-- You need this element to prevent the content of the page from jumping up -->
    <!-- <div class="header-fixed-placeholder"></div> -->
    <!-- The content of your page would go here. -->

    <section style="background-image: url(https://paragonafs.ca/assets/images/document.jpg);background-position:center center;background-repeat: no-repeat;background-size: cover;margin-bottom: 0px;height: 422px;z-index: 0;position: relative; ">
        <div style="height: 100%;width: 100%;top: 0;left: 0;position: absolute;background-color: #121212;opacity: 0.70;transition: background 0.3s, border-radius 0.3s, opacity 0.3s;border-style: none;border-color: rgba(33,37,41,0);"></div>
        <div class="text-white d-flex flex-column align-items-center container position-relative services_page">
            <h2 style="text-align:center; margin-top:15px;">Apply Corporate Tax</h2>
        </div>
    </section>

    <div class="container row personal_upload_document">


        <div id="btn_fixed_continue">
            <div class="col-lg-4">
                &nbsp;
            </div>
            <div class="col-lg-8" style="text-align:center;padding-right: 19px;">
                <button type="button" class="hvr-bounce-to-bottom" id="continue">
                    Continue
                </button>
            </div>
        </div>


        <div class="col-lg-4">
           

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
                        <div class="col-lg-8 mx-auto">
                            <div class="contact-box mt-8">
                                <i aria-hidden="true" class="fas fa-headphones-alt" style="font-size: 40px;line-height: 40px;"></i>
                                <h6>Calling Support</h6>
                                <p class="par-p mt-2"><i class="fas fa-phone-square-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (416) 477 3359<br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (647) 909 8484 <br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (437) 881 9175</p>
                            </div>
                        </div>
                        <div class="col-lg-8 mx-auto">
                            <div class="contact-box mt-8 ">
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
			  <form action="/corporation_mailapi.php" id="myForm" enctype="multipart/form-data" method="POST">

                <ul class="step-steps mb-3 mt-4">

                    <li class="step button-85" id="personInfo">
                        <img src="../assets/images/form.png" alt="" width="30" height="30"><span> Enter Personal Information</span>
                    </li>

                    <li class="step button-85" id="uploadDocs">
                        <img src="../assets/images/attached.png" alt="" width="30" height="30"><span> Attach Tax Documents</span>
                    </li>

                </ul>

               
                <div class="tab">

                    <h4 class="par-h4 mt-5" style="color: black;margin-bottom:10px;font-size:24px;">Lets Get Started</h4>
                    <p class="mb-3" style="font-size: 16px;">Please enter the details requested below in order to process tax return correctly. All information requested is mandatory.</p>
                    
                    <div class="form_append_error"></div>
                    
                    <h4 class="par-h4 mt-2" style="color: #0075be;margin-bottom: 5px;font-size: 18px;padding-bottom: 10px;border-bottom: 2px solid black;">Tells us about yourself &nbsp; </h4>
                    <h6 style="color: #0075be;margin: 20px 0 0px; margin-bottom:10px">Personal Information</h6>                    
                   
                    <div class="personal_basic_information">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="validationCustom01" class="form-label">First Name <span style="color: red;">*</span></label>
                                <input type="text" name="firstName" class="form-control" id="validationCustom01" placeholder="First Name" value="<?= $rowUser['first_name'] ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="validationCustom02" class="form-label">Last Name <span style="color: red;">*</span></label>
                                <input type="text" name="lastName" class="form-control" id="validationCustom02" placeholder="Last Name" value="<?= $rowUser['last_name'] ?>" required>
                            </div>
                          
                            <div class="col-md-6">
                                <label class="form-label">Your SIN Number <span style="color: red;">*</span></label>
                                <input type="text" name="sin_number" value="<?= encrypt_decrypt('decrypt', $rowUser['sin_number']) ?>" class="form-control" maxlength="9" required>
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
                            </div>
                            <h6 style="color: #0075be;margin: 20px 0 0px;">Contact Information</h6>

                            <div class="col-md-6">
                                <label class="form-label">Your Phone Number <span style="color: red;">*</span></label>
                                <input type="text" name="phone" value="<?= encrypt_decrypt('decrypt', $rowUser['phone']) ?>" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Your Email Address <span style="color: red;">*</span></label>
                                <input type="email" name="email" class="form-control" value="<?= encrypt_decrypt('decrypt', $rowUser['email']) ?>" required>
                            </div>
                            
                            <div class="col-md-12">
                                <hr style="margin: 10px 0 0;">
                            </div>
                           
                            <h6 style="color: #0075be;margin: 20px 0 10px;">Your Current Address</h6>

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

                    </div>


                    <h6 style="color: #0075be;margin: 20px 0 0px;">Other Information</h6>
                   
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label">Business Activity <span style="color: red;">*</span><br>&nbsp;</label>
                            <input type="text" id="activity "name="business_activity" value="" class="form-control" required=""  style="margin-top:-18px">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label"> Did you work on Payroll as well? If yes, what is your total household Income on SIN <br>
(Including your spouse income) <span style="color: red;">*</span></label>
                            <input type="text" name="income_on_sin" value="" class="form-control" required="">
                        </div>
                            
                        <div class="col-md-12">
                            <label class="form-label">Do you any other income (Uber/Skip, EI, contractual, self employed or other)<span style="color: red;">*</span></label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="summary_income" id="summary_income_yes" value="Yes" >
                                <label for="summary_income_yes" class="form-check-label">Yes</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input"  name="summary_income" id="summary_income_no" value="No" >
                                <label for="summary_income_no" class="form-check-label">No</label>
                            </div>
                        </div>
                        <div class="col-md-12" id="income_summary" style="margin-top: 10px; display: none;">
                            <label for="income_summary_text" class="form-label">Please provide a summary:</label>
                            <textarea id="income_summary_text" class="form-control" rows="3" name="summary_income_detail" placeholder="Enter details here..."></textarea>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 0px;">
                        <label class="form-label">Are you filing Corporation Tax for the First Time <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_filingtax" id="filing_first_yes" value="Yes">
                            <label for="filing_first_yes" class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_filingtax" id="filing_first_no" value="No">
                            <label for="filing_first_no" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div class="col-md-12" id="paragon_question" style="display: none; margin-top: 10px;">
                        <label class="form-label">Did you file with Paragon before?</label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="filed_paragon" id="paragon_yes" value="Yes">
                            <label for="paragon_yes" class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="filed_paragon" id="paragon_no" value="No">
                            <label for="paragon_no" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div class="col-md-12" id="cra_account_field" style="display: none; margin-top: 10px;">
                        <label class="form-label">Do you have a CRA Business Account? <span style="color: red;">*</span></label>
                        
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="cra_account" id="cra_yes" value="Yes">
                            <label for="cra_yes" class="form-check-label">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="cra_account" id="cra_no" value="No">
                            <label for="cra_no" class="form-check-label">No</label>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="tab">

                    <h4 class="par-h4 mt-5" style="color: black;margin-bottom:10px;font-size:24px;">Attach your documents here</h4>
                    <p class="mb-5" style="font-size: 16px;">Please attach documents which are required for your tax return. If you are not sure about the documents to attach, refer our documents checklist.</p>
                    
                     <div class="CustomTabs">

                      

                        <div class="tab_content" style="">

                           

                                <div id="upload_id_proof">
                                    <label style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">Certificate of Incorporation <span style="color: red;">*</span> </b><br></label>
                                    
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
                                

                                <div id="upload_business_gst_number" >
                                    <label class="mt-4" style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">Business or GST/HST number<span style="color: red;">*</span> </b></label>
                                    <input type="text" name="business_gst_number" value="" class="form-control" maxlength="9" required="">
                                </div>
                                <div id="upload_corp_account_bank_statement">
                                    <label class="mt-4" style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Corporation Account Bank Statements </b></label>
                                    
                                    <div class="FileUpload">
                                        <div class="wrapper" style="margin-bottom: 0px;">
                                            <input type="hidden" id="uploaded-corp_account_bank_statement" name="corp_account_bank_statement" value="">

                                            <div class="upload">
                                                <p>Drag files here or <span class="upload__button clickable_corp_account_bank_statement dz-clickable">Browse</span></p>
                                            </div>

                                            <ul id="sortable_corp_account_bank_statement" style="padding-left: 0;">
                                            
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr style="margin: 15px 0 0;">
                                </div>
                                <h6 style="color: #0075be;margin: 20px 0 0px; font-size:16px;font-weight:bold">Business Related Expenses</h6>
                    
                    
                    
                    <div class="row">
                                <div class="col-md-6" id="expenses_phone_bills">
                                    <label class="mt-4" style="font-size:14px; margin-bottom:8px">
                                        <b style="color:#0075be; font-size:14px; font-weight:500;">Phone Bills (Monthly) <span style="color: red;">*</span></b>
                                    </label>
                                    <input type="text" class="form-control" name="phone_bills" placeholder="Enter amount for phone bills" required>
                                </div>

                                <div class="col-md-6" id="expenses_fuel_costs">
                                    <label class="mt-4" style="font-size:14px; margin-bottom:8px">
                               
                                       <b style="color:#0075be; font-size:14px; font-weight:500;margin-bottom:8px">Fuel Costs (Monthly) <span style="color: red;">*</span></b>
                                    </label>
                                    <input type="text" class="form-control" name="fuel_costs" placeholder="Enter amount for fuel costs" required>
                                </div>

                   </div> 
                    
                    
                    
                    
                       <div class="row">
                    
                    
                    
                                <div   class="col-md-6" id="expenses_rent">
                                  <label class="mt-4" style="font-size:14px; margin-bottom:8px">
                                        <b style="color:#0075be; font-size:14px; font-weight:500;margin-bottom:8px">Rent (Monthly) <span style="color: red;">*</span></b>
                                    </label>
                                    <input type="text" class="form-control" name="rent" placeholder="Enter amount for rent" required>
                                </div>

                                <div  class="col-md-6" id="expenses_repairs_and_maintenance">
                                    <label class="mt-4" style="font-size:14px; margin-bottom:8px">
                               <b style="color:#0075be; font-size:14px; font-weight:500;margin-bottom:8px">Any Repairs and Maintenance (Yearly) <span style="color: red;">*</span></b>
                                    </label>
                                    <input type="text" class="form-control" name="repairs_and_maintenance" placeholder="Enter amount for repairs and maintenance" required>
                                </div>
                    
                    
                    
                   </div> 
                    
                    
                    
                    
                    
                    
                      <div class="row">
                    

                                <div  class="col-md-6" id="expenses_vehicle_emi">
                                    <label class="mt-4" style="font-size:14px; margin-bottom:8px">
                                         <b style="color:#0075be; font-size:14px; font-weight:500;margin-bottom:8px">Vehicle EMI (Monthly) <span style="color: red;">*</span></b>
                                    </label>
                                    <input type="text" class="form-control" name="vehicle_emi" placeholder="Enter amount for vehicle EMI" required>
                                </div>

                                <div  class="col-md-6" id="expenses_car_insurance">
                                   <label class="mt-4" style="font-size:14px; margin-bottom:8px">
                                       <b style="color:#0075be; font-size:14px; font-weight:500;margin-bottom:8px">Car Insurance (Monthly) <span style="color: red;">*</span></b>
                                    </label>
                                    <input type="text" class="form-control" name="car_insurance" placeholder="Enter amount for car insurance" required>
                                </div>
                    
                    </div>
                    
                    
                    
                    
                    

                                <div id="expense_zones_container">
                                    <label class="mt-4" style="font-size: 15px;">
                                        <b style="color:#0075be; font-size: 16px;">Any other expense, related to your business only</b>
                                    </label>
                                </div>
                                <button type="button" id="add_expense" class="btn btn-primary mt-4" style="background:#0075BE">Add Expense</button>

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
                        <button type="button" class="submit hvr-bounce-to-bottom">Submit</button>
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

    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="../assets/js/jquery.repeater.min.js"></script>
    <script src="../assets/js/foundation-datepicker.js"></script>
    <script src="../assets/js/jquery.inputmask.min.js"></script>
    <script src="../assets/js/jquery.inputmask.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDak-JxDYbQ7l9CGSkSHDaUPy7rmLBEUEw&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src="https://js.upload.io/upload-js/v2"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.js"></script> -->
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

   
    <script>
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


        var uploaded_corp_account_bank_statement = <?php echo isset($rowUser['direct_deposits']) ? json_encode(explode('<br>', $rowUser['direct_deposits'])) : '[]' ?>;
        var myCorpAccountBankStatementDropzone = new Dropzone("#upload_corp_account_bank_statement", {
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
                    if (!fileExists(fileName, uploaded_corp_account_bank_statement)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_corp_account_bank_statement', myDirectDepositDropzone, file);
                        });

                        $("#sortable_corp_account_bank_statement").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_corp_account_bank_statement li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_corp_account_bank_statement li:contains('" + fileName + "')");
                    li.remove()
                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;
                    //corp_account_bank_statement
                    myCorpAccountBankStatementDropzone.removeFile(file);
                    uploaded_corp_account_bank_statement.push(response);

                    console.log(uploaded_corp_account_bank_statement);

                    var li = $("#sortable_corp_account_bank_statement li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                    updateInputField("#sortable_corp_account_bank_statement");
                    submitButton.disabled = false;

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_corp_account_bank_statement" // Define the element that should be used as click trigger to select files.
        });

        
        
        

        // Counter for unique dropzone IDs
        let expenseCounter = 1;

    // Add new expense field dynamically
    document.getElementById("add_expense").addEventListener("click", function () {
        const container = document.getElementById("expense_zones_container");

        // Create a new expense row
        const newExpense = document.createElement("div");
        newExpense.className = "expense_row mt-2";
        newExpense.id = `expense_row_${expenseCounter}`;

        newExpense.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <input type="text" class="form-control" name="expense_label[]" placeholder="Enter expense label" required>
                </div>
                <div class="col-md-5">
                    <input type="text" class="form-control" name="expense_value[]" placeholder="Enter expense value" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="delete-expense btn btn-danger"><i class="fa fa-trash"></i></button>
                </div>
            </div>
        `;

        // Append the new expense row to the container
        container.appendChild(newExpense);

        expenseCounter++;
    });

    // Delete an expense field
    document.getElementById("expense_zones_container").addEventListener("click", function (e) {
        if (e.target.classList.contains("delete-expense") || e.target.closest(".delete-expense")) {
            e.target.closest(".expense_row").remove();
        }
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


            console.log(selector);
        }

    </script>

    <script>
        
        // When the modal is closed, bring back the original row to its original position
        $('#editModal').on('hide.bs.modal', function () {
            console.log('Modal closed event triggered');

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
                            var newDeleteElement = $('<span class="form-control child_info_delete_first" type="button" style="text-align: center;width: 50px;"><i class="fas fa-trash-alt" style="color: red;"></i></span>');
                            $("#have_child_body tr:first-child td:nth-child(4)").append(newDeleteElement);
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
                            $("#rent_id tr:first-child td:nth-child(4)").append(newDeleteElement);
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
                            $("#spouse_rent_id tr:first-child td:nth-child(4)").append(newDeleteElement);
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
        
        $('body').on('focus', ".child_date_birth", function() {
            $(this).datepicker({
                changeMonth: true,
                changeYear: true,
                dateFormat: 'dd M yy',
                yearRange: '1900:' + new Date().getFullYear()
            }).prop('readonly', true);
        });

        /* $('#purchase_first_home').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true); */

       /* $('#date_birthdate').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);*/

        $('#date_movedate').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

        /* $('#date_entry').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);*/

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

        $('.child_date_birth').datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'dd M yy',
            yearRange: '1900:' + new Date().getFullYear()
        }).prop('readonly', true);

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

            $('input[name="summary_income"]').on('change', function () {
                if ($(this).val() === 'Yes') {
                    // Show the text box
                    $('#income_summary').slideDown();
                } else {
                    // Hide the text box
                    $('#income_summary').slideUp();
                }
            });
            $('input[name="first_filingtax"]').on('change', function () {
                if ($(this).val() === 'Yes') {
                    $('#cra_account_field').slideDown();
                    $('#paragon_question').slideUp(); // Hide the Paragon question
                } else {
                    $('#cra_account_field').slideUp();
                    $('#paragon_question').slideDown(); // Show the Paragon question
                }
            });

            // When "Did you file with Paragon before?" is selected
            $('input[name="filed_paragon"]').on('change', function () {
                if ($(this).val() === 'Yes') {
                    $('#cra_account_field').slideUp(); // Hide the CRA account field
                } else {
                    $('#cra_account_field').slideDown(); // Show the CRA account field
                }
            });

        });
    </script>
    
    
    
  
  

    <script type="text/javascript" src="../multi-form-corp.js"></script>

</body>

</html>