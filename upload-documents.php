<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Upload Documents Pages</title>
    <link rel="icon" type="image/x-icon" href="assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" type="text/css" href="multi-form.css?v2" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/foundation-datepicker.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.0/animate.min.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.css" type="text/css" />

    <script type="module" src="assets/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="multi-form.js?v2"></script>
    <script src="https://apis.google.com/js/api.js"></script>

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

            $('#hide_filingtax').click(function() {
                $('#filingtax').hide()
                $('#no_filingtax').show()
            })

            $('#show_filingtax').click(function() {
                $('#filingtax').show()
                $('#no_filingtax').hide()
            })

            $('#marital_single').click(function() {
                $('#body_marital_status').hide()
                $('#body_marital_change').hide()
            })

            $('#marital_married, #marital_common').click(function() {
                $('#body_marital_status').show()
                $('#body_marital_change').hide()
            })

            $('#marital_widow, #marital_divorce, #marital_seperated').click(
                function() {
                    $('#body_marital_status').hide()
                    $('#body_marital_change').show()
                }
            )

            $('#residing_canada_yes').click(function() {
                $('#spouse_residing_canada').show()
            })

            $('#residing_canada_no').click(function() {
                $('#spouse_residing_canada').hide()
            })

            $('#spouse_file_tax_yes').click(function() {
                $('#spouse_want_taxes').show()
            })

            $('#spouse_file_tax_no').click(function() {
                $('#spouse_want_taxes').hide()
            })

            $('#spouse_first_tax_yes').click(function() {
                $('#spouse_filingtax').show()
                $('#no_spouse_filingtax').hide()
            })

            $('#spouse_first_tax_no').click(function() {
                $('#spouse_filingtax').hide()
                $('#no_spouse_filingtax').show()
            })

            $('#have_child_yes').click(function() {
                $('#have_child_body').show()
            })

            $('#have_child_no').click(function() {
                $('#have_child_body').hide()
            })

            $('#show_delivery_tax').click(function() {
                $('#delivery_annual_tax').show()
            })

            $('#hide_delivery_tax').click(function() {
                $('#delivery_annual_tax').hide()
            })

            $('#show_hst').click(function() {
                $('#hst').show()
            })

            $('#hide_hst').click(function() {
                $('#hst').hide()
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



            // var user = $('input[name^="data"]');

            // user.filter('input[name$="[child_first_name]"]').each(function() {
            //     $(this).rules("add", {
            //         required: true,
            //         messages: {
            //             required: "Name is Mandatory"
            //         }
            //     });
            // });

            // user.filter('input[name$="[child_last_name]"]').each(function() {
            //     $(this).rules("add", {
            //         email: true,
            //         required: true,
            //         messages: {
            //             email: 'Email must be valid email address',
            //             required: 'Email is Mandatory',
            //         }
            //     });
            // });


            var val = {
                // Specify validation rules
                rules: {
                    firstName: 'required',
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
                    firstName: 'First name is required',
                    lastName: 'Last name is required',
                    gender: 'Gender is required',
                    ship_address: 'Address is required',
                    locality: 'City is required',
                    state: 'State is required',
                    postcode: 'Postal Code is required',
                    country: 'Country/Region is required',
                    birth_date: 'Birthdate is required',
                    // sin_number: 'SIN Number is required',
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
    </style>

</head>

<body>

    <?php include_once 'headers2.php'; ?>


    <header class="header-fixed">

        <div class="header-limiter">


            <nav class="navbar navbar-expand-lg" style="z-index: 0;">
                <div class="container-xl" style="white-space: nowrap; justify-content:center">

                    <h1><a class="navbar-brand" href="/index.php"><span><img src="assets/img/paragon_logo.png" alt="logo" style="width: 190px; height: 60px;"></span></a></h1>
                    <!-- <a class="navbar-brand" href="/index.php"><span><img src="assets/img/paragon_logo.png" alt="logo" style="width: 190px;"></span></a> -->

                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07XL" aria-controls="navbarsExample07XL" aria-expanded="false" aria-label="Toggle navigation" style="background-color: white;">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="navbar-collapse collapse align-items-center" id="navbarsExample07XL" style="color:black">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/about.php">About</a>
                            </li>
                            <li class="nav-item">
                                <div class="dropdown nav-link">
                                    <a href="/services.php" class="services_hover" style="text-decoration: none; padding: 0;">Services &nbsp;</a>
                                    <button class="dropbtn"><i class="fa fa-chevron-down" aria-hidden="true" style="color: black;"></i></button>
                                    <div class="dropdown-content">
                                        <ul style="list-style: none; padding: 0; align-items: right;">
                                            <li><a href="/personal_tax.php">Personal Income Tax</a></li>
                                            <li><a href="/corporate_tax.php">Corporate Income Tax</a></li>
                                            <li><a href="/incorporate.php">Incorporate / Register a Business</a></li>
                                            <li><a href="/bookkeeping.php">Accounting/Bookkeeping</a></li>
                                            <li><a href="/payroll_salary.php">Payroll & Salary Calculations</a></li>
                                            <li><a href="/gst_hst.php">GST/HST Returns</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/documents-page.php">Documents</a>
                            </li>
                            <!-- <li class="nav-item">
                <a class="nav-link" href="/upload_document.php">Upload Documents</a>
            </li> -->
                            <li class="nav-item">
                                <a class="nav-link" href="/contact_us.php">Contact Us</a>
                            </li>
                        </ul>
                        <a class="btn text-uppercase hvr-bounce-to-bottom" id="navbar_upload1" href="/form" style="letter-spacing: 1px;border:none; color: white;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a>
                        <a class="btn text-uppercase hvr-bounce-to-bottom2" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none; padding: 15px 30px;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a>
                        <a class="btn text-uppercase" id="navbar_button2" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="color: black; letter-spacing: 1px;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book</a>
                    </div>
                </div>
            </nav>

        </div>

    </header>

    <nav class="navbar navbar-expand-lg">
        <div class="container-xl">

            <!-- <a class="navbar-brand" href="/index.php"><span><img src="assets/img/paragon_logo.png" alt="logo" style="width: 190px;"></span></a> -->


            <div class="navbar-toggler" style="background-color: white;border-radius:0;margin:0;width:100%;padding: 0;border: none;">


                <div class="col-6" style="padding: 0;">

                    <a class="btn text-uppercase hvr-bounce-to-bottom" id="navbar_upload" href="/form" style="letter-spacing: 1px;border:none;text-align: center;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a>

                </div>

                <div class="col-6" style="padding: 0;">

                    <a class="btn text-uppercase hvr-bounce-to-bottom2" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none;text-align: center;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a>
                </div>

                <!-- <div class="col-2 collapsed text-center" style="padding-top: 10px;margin-top: 0px;" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample08XL" aria-controls="navbarsExample08XL" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </div> -->


                <div class="menu-backdrop">
                    <div class="side-bar">


                        <header>

                            <div class="close-btn">
                                <i class="fas fa-times"></i>
                            </div>

                            <img src="assets/img/paragon_logo.png" width="250" height="80" alt="">

                        </header>

                        <div class="menu">
                            <div class="item"><a href="/"><i class="fas fa-home"></i>Home</a></div>
                            <div class="item"><a href="/about.php"><i class="fas fa-info-circle"></i>About</a></div>
                            <div class="item">
                                <a class="sub-btn"><i class="fas fa-hands-helping"></i>Services<i class="fas fa-angle-right dropdown"></i></a>
                                <div class="sub-menu">
                                    <a class="sub-item" href="/services.php">All Services</a>
                                    <a class="sub-item" href="/personal_tax.php">Personal Income Tax</a>
                                    <a class="sub-item" href="/corporate_tax.php">Corporate Income Tax</a>
                                    <a class="sub-item" href="/incorporate.php">Incorporate / Register a Business</a>
                                    <a class="sub-item" href="/bookkeeping.php">Accounting/Bookkeeping</a>
                                    <a class="sub-item" href="/payroll_salary.php">Payroll & Salary Calculations</a>
                                    <a class="sub-item" href="/gst_hst.php">GST/HST Returns</a>
                                </div>
                            </div>
                            <div class="item"><a href="/form"><i class="fas fa-scroll"></i>Documents</a></div>
                            <div class="item"><a href="/contact_us.php"><i class="fas fa-id-card"></i>Contact Us</a></div>
                            <!-- <div class="item">Contact Us</a></div> -->

                            <div class="item">

                                <a href="/upload-documents.php" style="background-color: #0075be; color:white; margin: 0px 10px 10px;"><i class="fas fa-file-upload"></i>Upload Document</a>

                            </div>

                            <div class="item">

                                <a href="https://paragon-accounting-and-financial-services-inc.square.site/" style="background-color: #fcbc45; color:black;margin: 0px 10px 20px;"><i class="fas fa-calendar-alt"></i>Book Appointment</a>

                            </div>

                            <div class="item" style="border-top: none;"><a href="#" style="font-size: 20px;font-weight: 700;color: #ffffff;">Contact Info</a>

                                <ul style="list-style: none;padding: 0px;line-height: 1.5;">
                                    <li>#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</li>
                                    <li>416-477-3359</li>
                                    <li>info@paragonafs.ca</li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="navbar-collapse collapse align-items-center" id="navbarsExample08XL" style="background-color: #063c83;">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link " href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <div class="dropdown nav-link">
                            <a href="/services.php" class="services_hover" style="text-decoration: none;">Services &nbsp;</a>
                            <button class="dropbtn"><i class="fa fa-chevron-down" aria-hidden="true" style="color: white;"></i></button>
                            <div class="dropdown-content" style="padding-top: 10px; background-color: #063c83;">
                                <a href="/personal_tax.php">Personal Income Tax</a>
                                <a href="/corporate_tax.php">Corporate Income Tax</a>
                                <a href="/incorporate.php">Incorporate / Register a Business</a>
                                <a href="/bookkeeping.php">Accounting/Bookkeeping</a>
                                <a href="/payroll_salary.php">Payroll & Salary Calculations</a>
                                <a href="/gst_hst.php">GST/HST Returns</a>

                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/documents-page.php">Documents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact_us.php">Contact Us</a>
                    </li>
                </ul>
                <a class="btn text-uppercase hvr-bounce-to-bottom navupload" id="navbar_upload" href="/form" style="letter-spacing: 1px;border:none;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a>
                <a class="btn text-uppercase hvr-bounce-to-bottom2 navupload" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a>
                <a class="btn text-uppercase" id="navbar_button2" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="color: black; letter-spacing: 1px;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book</a>
            </div>
        </div>


        <div class="menu-btn">
            <i class="fas fa-bars"></i>
        </div>




    </nav>

    <!-- You need this element to prevent the content of the page from jumping up -->
    <!-- <div class="header-fixed-placeholder"></div> -->
    <!-- The content of your page would go here. -->

    <section style="background-color: black;background-position: center center;background-repeat: no-repeat;background-size: cover;margin-bottom: 0px;height: 422px;z-index: 0;position: relative; ">
        <div style="height: 100%;width: 100%;top: 0;left: 0;position: absolute;background-color: #121212;opacity: 0.70;transition: background 0.3s, border-radius 0.3s, opacity 0.3s;border-style: none;border-color: rgba(33,37,41,0);"></div>
        <div class="text-white d-flex flex-column align-items-center container position-relative services_page">
            <h2>Upload Documents</h2>
        </div>
    </section>

    <div class="container row personal_upload_document">

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
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T4- Employment Income</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T3, T5 – Interest, dividends, mutual funds</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T4E – Employment insurance benefits</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T4A – OAS, T4AP – Old Age Security and CPP benefits</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T2202A- Tuition / education receipts</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T4A- Other pensions and annuity income</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;T5007 – Social assistance, Worker’s compensation benefits</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Any other slips</li>
                            </ul>
                        </div>
                    </div>

                    <div class="acc">
                        <div class="acc-head">
                            <p>Receipt</p>
                        </div>
                        <div class="acc-content">
                            <ul>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;RRSP contribution slips</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Child care information (Babysitting)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Professional or union dues</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Tool expenses (Tradespersons)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Medical expenses (by family during the last 24 months)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Employment expenses</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Political contributions</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Charitable donations (last 5 years)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Moving expenses (if moved for work purposes)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Interest paid on student loans (last 5 years)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Professional certification exams</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Property tax for rental property information</li>
                            </ul>
                        </div>
                    </div>

                    <div class="acc">
                        <div class="acc-head">
                            <p>Other Documentation</p>
                        </div>
                        <div class="acc-content">
                            <ul>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Notice of Assessment/Reassessment</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Direct deposit form</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp;Canada Revenue Agency correspondence (any letter from CRA)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp; Sale of principal residence</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp; Rental income and expense receipts</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp; T2200- Declaration of Conditions of Employment</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp; Details of dependents, if any (Kids,Grandparents)</li>
                                <li><img src="assets/img/paragon_logo_icon.png"> &nbsp; First time home buyer- please bring home documents</li>
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

            <!-- Modal HTML -->
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
                            <button class="btn btn-success" data-bs-dismiss="modal"><span>Okay</span></button>
                        </div>
                    </div>
                </div>
            </div>


            <div id="pageMessages">

            </div>


            <form action="/gmailapi.php" id="myForm" enctype="multipart/form-data" method="POST">

                <div class="loader"></div>

                <ul class="step-steps mb-3 mt-4">

                    <li class="step" id="personInfo">
                        <img src="assets/images/paper.png" alt="" width="30" height="30"><span> Doc Upload Instructions</span>
                    </li>

                    <li class="step" id="personInfo">
                        <img src="assets/images/form.png" alt="" width="30" height="30"><span> Enter Personal Information</span>
                    </li>

                    <li class="step" id="uploadDocs">
                        <img src="assets/images/attached.png" alt="" width="30" height="30"><span> Attach Tax Documents</span>
                    </li>

                </ul>

                <!-- One "tab" for each step in the form: -->






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
                                                <img src="assets/images/paper.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="assets/images/paper.png" alt="" width="80" height="80">
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
                                                <img src="assets/images/form.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="assets/images/form.png" alt="" width="80" height="80">
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
                                                <img src="assets/images/attached.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="assets/images/attached.png" alt="" width="80" height="80">
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
                                                <img src="assets/images/businessman.png" alt="" width="80" height="80">
                                            </div>
                                            <div class="back">
                                                <img src="assets/images/businessman.png" alt="" width="80" height="80">
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
                    <p class="mb-5" style="font-size: 16px;">Please enter the details requested below in order to process your tax return correctly. All information requested is mandatory.</p>

                    <h4 class="par-h4" style="color: #0075be;margin-bottom:10px; font-size: 18px;">Tells us about yourself</h4>


                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Name <span style="color: red;">*</span></label>
                            <input type="text" name="firstName" class="form-control" placeholder="First Name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="validationCustom02" class="form-label">&nbsp;</label>
                            <input type="text" name="lastName" class="form-control" id="validationCustom02" placeholder="Last Name" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Gender <span style="color: red;">*</span></label>
                            <br>

                            <div class="form-check form-check-inline">
                                <label for="gender_male" class="form-check-label">Male</label>
                                <input type="radio" class="form-check-input" id="gender_male" name="gender" value="Male">
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" id="gender_female" name="gender" value="Female" required>
                                <label for="gender_female" class="form-check-label">Female</label>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Complete Address <span style="color: red;">*</span></label>
                            <input type="text" id="ship_address" name="ship_address" autocomplete="off" class="form-control" style="margin-bottom: 5px;" placeholder="Apartment, unit, suite, or floor #" required>
                        </div>

                        <div class="col-md-6">
                            <input type="text" id="locality" name="locality" class="form-control" placeholder="City" style="margin-bottom: 5px;" required>
                        </div>

                        <div class="col-md-6">
                            <input type="text" id="state" name="state" class="form-control" placeholder="State/Province" style="margin-bottom: 5px;" required>
                        </div>

                        <div class="col-md-6">
                            <input type="text" id="postcode" name="postcode" class="form-control" placeholder="Postal Code" style="margin-bottom: 5px;" required>
                        </div>

                        <div class="col-md-6">
                            <input type="text" id="country" name="country" class="form-control" placeholder="Country/Region" style="margin-bottom: 5px;" required>
                        </div>

                        <div class="col-md-6">

                            <div class="date-container">

                                <label class="form-label">Date of Birth <span style="color: red;">*</span></label>
                                <input type="text" name="birth_date" class="form-control date_input_icon" id="date_birthdate" required>

                            </div>

                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SIN Number <span style="color: red;">*</span></label>
                            <input type="text" name="sin_number" class="form-control" maxlength="9" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Phone Number <span style="color: red;">*</span></label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Email Address <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Did you move to another province? <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="show_movedate" name="another_province" value="Yes">
                            <label for="show_movedate" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="another_province" id="hide_movedate" value="No">
                            <label for="hide_movedate" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div id="movedate">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">When did you move? <span style="color: red;">*</span></label>
                                    <input type="text" name="move_date" class="form-control date_input_icon" id="date_movedate" required>

                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-md-6">
                                <label for="validationCustom04" class="form-label">Province moved From? <span style="color: red;">*</span></label>
                                <select class="form-select" id="validationCustom04" name="move_from" required>
                                    <option selected disabled value="">Select State/Province</option>
                                    <option value="Alberta">
                                        Alberta </option>
                                    <option value="British Columbia">
                                        British Columbia </option>
                                    <option value="Manitoba">
                                        Manitoba </option>
                                    <option value="New Brunswick">
                                        New Brunswick </option>
                                    <option value="Newfoundland and Labrador">
                                        Newfoundland and Labrador </option>
                                    <option value="Northwest Territories">
                                        Northwest Territories </option>
                                    <option value="Nova Scotia">
                                        Nova Scotia </option>
                                    <option value="Nunavut">
                                        Nunavut </option>
                                    <option value="Ontario">
                                        Ontario </option>
                                    <option value="Prince Edward Island">
                                        Prince Edward Island </option>
                                    <option value="Quebec">
                                        Quebec </option>
                                    <option value="Saskatchewan">
                                        Saskatchewan </option>
                                    <option value="Yukon">
                                        Yukon </option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Province moved To? <span style="color: red;">*</span></label>
                                <select class="form-select" name="move_to" required>
                                    <option selected disabled value="">Select State/Province</option>
                                    <option value="Alberta">
                                        Alberta </option>
                                    <option value="British Columbia">
                                        British Columbia </option>
                                    <option value="Manitoba">
                                        Manitoba </option>
                                    <option value="New Brunswick">
                                        New Brunswick </option>
                                    <option value="Newfoundland and Labrador">
                                        Newfoundland and Labrador </option>
                                    <option value="Northwest Territories">
                                        Northwest Territories </option>
                                    <option value="Nova Scotia">
                                        Nova Scotia </option>
                                    <option value="Nunavut">
                                        Nunavut </option>
                                    <option value="Ontario">
                                        Ontario </option>
                                    <option value="Prince Edward Island">
                                        Prince Edward Island </option>
                                    <option value="Quebec">
                                        Quebec </option>
                                    <option value="Saskatchewan">
                                        Saskatchewan </option>
                                    <option value="Yukon">
                                        Yukon </option>
                                </select>
                            </div>


                        </div>
                    </div>



                    <div class="col-md-12" style="margin-top: 0px">
                        <label class="form-label">Is this the first time you are filing tax? <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_fillingtax" id="show_filingtax" value="Yes">
                            <label for="show_filingtax" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_fillingtax" id="hide_filingtax" value="No">
                            <label for="hide_filingtax" class="form-check-label">No</label>
                        </div>
                    </div>


                    <div id="filingtax">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">Date of Entry in Canada <span style="color: red;">*</span></label>
                                    <input type="text" name="canada_entry" class="form-control date_input_icon" id="date_entry" required>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Birth Country <span style="color: red;">*</span></label>
                                <input type="text" name="birth_country" id="birth_country" class="form-control">
                            </div>
                        </div>


                        <div class="col-md-12">
                            <label class="form-label" style="margin-top: 15px;margin-bottom: 15px;">What was your world income in last 3 years before coming to Canada (in CAD)? <span style="color: red;">*</span></label>
                        </div>

                        <div class="row">
                            <div class="col-md-6">

                                <input type="text" name="year1" id="year1" class="form-control" placeholder="Year 1" readonly>
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year1_income" id="year1_income" class="form-control" placeholder="Year 1 Income" style="margin-bottom: 5px;">
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year2" id="year2" class="form-control" placeholder="Year 2" readonly style="margin-bottom: 5px;">
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year2_income" id="year2_income" class="form-control" placeholder="Year 2 Income" style="margin-bottom: 5px;">
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year3" id="year3" class="form-control" placeholder="Year 3" readonly style="margin-bottom: 5px;">
                            </div>

                            <div class="col-md-6">

                                <input type="text" name="year3_income" id="year3_income" class="form-control" placeholder="Year 3 Income" style="margin-bottom: 5px;">
                            </div>
                        </div>

                    </div>

                    <div class="row" id="no_filingtax">
                        <div class="col-md-12">
                            <label class="form-label">Did you file earlier with Paragon Tax Services?</label>
                            <br>
                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="file_paragon" id="file_paragon_yes" value="Yes" required>
                                <label for="file_paragon_yes" class="form-check-label">Yes</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input type="radio" class="form-check-input" name="file_paragon" id="file_paragon_no" value="No">
                                <label for="file_paragon_no" class="form-check-label">No</label>
                            </div>
                        </div>

                        <div class="col-md-12" style="margin-top: 16px;">
                            <label class="form-label">Which years do you want to file tax returns? <span style="color: red;">*</span></label>
                            <input type="text" name="years_tax_return" class="form-control">
                            <p><small>(Please enter years separated by commas if you wish to file tax return for multiple year. For e.g., 2020, 2019 etc.)</small></p>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Marital Status <span style="color: red;">*</span></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_single" name="marital_status" value="Single" required>
                            <label for="marital_single" class="form-check-label">Single</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_married" name="marital_status" value="Married">
                            <label for="marital_married" class="form-check-label">Married</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_common" name="marital_status" value="Common in Law">
                            <label for="marital_common" class="form-check-label">Common in law</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_seperated" name="marital_status" value="Separated">
                            <label for="marital_seperated" class="form-check-label">Separated</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_widow" name="marital_status" value="Widow">
                            <label for="marital_widow" class="form-check-label">Widow</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="marital_divorce" name="marital_status" value="Divorced">
                            <label for="marital_divorce" class="form-check-label">Divorced</label>
                        </div>

                    </div>

                    <div class="body_marital_status" id="body_marital_status">

                        <h4 class="par-h4 mt-5" style="color: #0075be;margin-bottom:10px; font-size: 18px;">Tell us about your spouse</h4>

                        <div class="row">
                            <div class="col-md-6 marital">
                                <label class="form-label">Spouse Name <span style="color: red;">*</span></label>
                                <input type="text" name="spouse_firstname" class="form-control" placeholder="First Name">
                            </div>

                            <div class="col-md-6 marital">
                                <label for="spouse_lastname" class="form-label">&nbsp;</label>
                                <input type="text" name="spouse_lastname" class="form-control" placeholder="Last Name">
                            </div>

                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label">Spouse Date of Birth <span style="color: red;">*</span></label>
                                    <input type="text" name="spouse_date_birth" class="form-control date_input_icon" id="spouse_date_birth" required>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="date-container">
                                    <label class="form-label" id="date_marital_status">Date of Marriage <span style="color: red;">*</span></label>
                                    <input type="text" name="date_marriage" class="form-control date_input_icon" id="date_marriage" required>

                                </div>
                            </div>

                            <div class="col-md-6 marital">
                                <label class="form-label">Spouse Annual Income in CAD <span style="color: red;">*</span></label>
                                <input type="text" name="spouse_annual_income" class="form-control">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Residing in Canada <span style="color: red;">*</span></label>
                                <br>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="residing_canada_yes" name="residing_canada" value="Yes" required>
                                    <label for="residing_canada_yes" class="form-check-label">Yes</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="residing_canada_no" name="residing_canada" value="No">
                                    <label for="residing_canada_no" class="form-check-label">No</label>
                                </div>
                            </div>

                            <div id="spouse_residing_canada" style="display: none;">

                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Spouse SIN <span style="color: red;">*</span></label>
                                        <input type="text" name="spouse_sin" class="form-control" maxlength="9">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Spouse Phone Number <span style="color: red;">*</span></label>
                                        <input type="text" name="spouse_phone" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12 marital">
                                    <label class="form-label">Spouse Email Address <span style="color: red;">*</span></label>
                                    <input type="text" name="spouse_email" class="form-control">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Does your spouse want to file taxes? <span style="color: red;">*</span></label>
                                    <br>
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_file_tax_yes" name="spouse_file_tax" value="Yes" required>
                                        <label for="spouse_file_tax_yes" class="form-check-label">Yes</label>
                                    </div>

                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" id="spouse_file_tax_no" name="spouse_file_tax" value="No">
                                        <label for="spouse_file_tax_no" class="form-check-label">No</label>
                                    </div>
                                </div>

                                <div id="spouse_want_taxes" style="display: none;">
                                    <div class="col-md-12">
                                        <label class="form-label">Is this the first time your spouse filing tax? <span style="color: red;">*</span></label>
                                        <br>
                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="spouse_first_tax_yes" name="spouse_first_tax" value="Yes" required>
                                            <label for="spouse_first_tax_yes" class="form-check-label">Yes</label>
                                        </div>

                                        <div class="form-check form-check-inline">
                                            <input type="radio" class="form-check-input" id="spouse_first_tax_no" name="spouse_first_tax" value="No">
                                            <label for="spouse_first_tax_no" class="form-check-label">No</label>
                                        </div>
                                    </div>


                                    <div id="spouse_filingtax" style="display: none;">

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="date-container">
                                                    <label class="form-label">Date of Entry in Canada <span style="color: red;">*</span></label>
                                                    <input type="text" name="spouse_canada_entry" class="form-control date_input_icon" id="spouse_date_entry" required>

                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">Birth Country <span style="color: red;">*</span></label>
                                                <input type="text" name="spouse_birth_country" id="spouse_birth_country" class="form-control">
                                            </div>
                                        </div>


                                        <div class="col-md-12">
                                            <label class="form-label" style="margin: 15px 0px;">What was your spouse world income in last 3 years before coming to Canada (in CAD)? <span style="color: red;">*</span></label>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year1" id="spouse_year1" class="form-control" placeholder="Year 1" readonly>
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year1_income" id="spouse_year1_income" class="form-control" placeholder="Year 1 Income" style="margin-bottom: 5px;">
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year2" id="spouse_year2" class="form-control" placeholder="Year 2" readonly style="margin-bottom: 5px;">
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year2_income" id="spouse_year2_income" class="form-control" placeholder="Year 2 Income" style="margin-bottom: 5px;">
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year3" id="spouse_year3" class="form-control" placeholder="Year 3" readonly style="margin-bottom: 5px;">
                                            </div>

                                            <div class="col-md-6">

                                                <input type="text" name="spouse_year3_income" id="spouse_year3_income" class="form-control" placeholder="Year 3 Income" style="margin-bottom: 5px;">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row" id="no_spouse_filingtax" style="display: none;">
                                        <div class="col-md-12">
                                            <label class="form-label">Did Your Spouse file earlier with Paragon Tax Services? <span style="color: red;">*</span></label>
                                            <br>
                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="spouse_file_paragon" id="spouse_file_paragon_yes" value="Yes" required>
                                                <label for="spouse_file_paragon_yes" class="form-check-label">Yes</label>
                                            </div>

                                            <div class="form-check form-check-inline">
                                                <input type="radio" class="form-check-input" name="spouse_file_paragon" id="spouse_file_paragon_no" value="No">
                                                <label for="spouse_file_paragon_no" class="form-check-label">No</label>
                                            </div>
                                        </div>

                                        <div class="col-md-12" style="margin-top: 16px;">
                                            <label class="form-label">Which Years Your Spouse want to file tax returns? <span style="color: red;">*</span></label>
                                            <input type="text" name="spouse_years_tax_return" class="form-control">
                                            <p><small>(Please enter years separated by commas if you wish to file tax return for multiple year. For e.g., 2020, 2019 etc.)</small></p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Do you have child</label>
                                <Br>
                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="have_child_yes" name="have_child" value="Yes" required>
                                    <label for="have_child_yes" class="form-check-label">Yes</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input type="radio" class="form-check-input" id="have_child_no" name="have_child" value="No">
                                    <label for="have_child_no" class="form-check-label">No</label>
                                </div>
                            </div>


                            <div class="repeater" id="have_child_body" style="overflow-x:auto; display:none; margin-top: 0px;">

                                <h4 class="par-h4 mt-5 mb-3" style="color: #0075be;margin-bottom:10px; font-size: 18px;">Tells us about your children</h4>

                                <table border="1" style="width: 100%; border: none;">
                                    <thead>
                                        <tr>
                                            <th style="width:30%">Child First Name</th>
                                            <th style="width:30%">Child Last Name</th>
                                            <th style="width:30%">Child Date of Birth</th>
                                            <th>&nbsp;</th>
                                        </tr>
                                    </thead>
                                    <tbody data-repeater-list="data">
                                        <tr data-repeater-item>
                                            <td data-label="Child First Name">
                                                <input type="text" class="form-control child_first_name" name="child_first_name" required>
                                            </td>
                                            </td>
                                            <td data-label="Child Last Name">
                                                <input type="text" class="form-control child_last_name" name="child_last_name" required>
                                            </td>
                                            <td data-label="Child Date of Birth">
                                                <div class="date-container">
                                                    <input type="text" name="child_date_birth" class="form-control date_input_icon child_date_birth" required>

                                                </div>
                                            </td>
                                            <td>
                                                <span class="form-control child_info_delete" data-repeater-delete type="button" style="text-align: center;width: 50px;">
                                                    <i class="fas fa-trash-alt" style="color: red;"></i>
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <input data-repeater-create type="button" class="child_repeater" value="Add Child" style="background-color: #0075be; color:white; border-radius: 2px; margin-left: 5px; margin-top: 5px; padding: 5px 10px;" />
                            </div>

                        </div>
                    </div>

                    <div class="body_marital_change" id="body_marital_change">
                        <div class="col-md-6 marital">
                            <div class="date-container">
                                <label class="form-label" id="marital_change_label">Date Of Marital status change <span style="color: red;">*</span></label>
                                <input type="text" name="marital_change" class="form-control date_input_icon" id="marital_change" required>

                            </div>
                        </div>
                    </div>


                    <div class="col-md-12">
                        <label class="form-label">Are you first time home buyer?
                            <i class="far fa-question-circle" style="font-size: 15px; vertical-align: top; color:#0075be; cursor: pointer;" data-toggle="popover" title="Choose Yes, If you have purchased your first home in Canada and never applied for first time home buyer tax credit (HBTC)."></i> </label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_time_buyer" id="first_time_buyer_yes" value="Yes" required>
                            <label for="first_time_buyer_yes" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" name="first_time_buyer" id="first_time_buyer_no" value="No">
                            <label for="first_time_buyer_no" class="form-check-label">No</label>
                        </div>
                    </div>
                </div>


                <div class="tab">

                    <h4 class="par-h4 mt-5" style="color: black;margin-bottom:10px;font-size:24px;">Attach your documents here</h4>
                    <p class="mb-5" style="font-size: 16px;">Please attach documents which are required for your tax return. If you are not sure about the documents to attach, refer our documents checklist.</p>


                    <div class="container_direct_deposit">
                        <label style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Direct deposit form</b><br> If you want tax refund/benefits from CRA to be deposited directly into your account, please provide direct deposit bank form.</label>
                        <!-- <input type="file" onchange="onFileSelected(event)" style="display: none;" /> -->
                        <div class="FileUpload">
                            <div class="wrapper" style="margin-bottom: 0px;">

                                <div class="upload myDropzone">
                                    <p>Drag files here or <span class="upload__button fileinput-button dz-clickable">Browse</span></p>
                                </div>


                                <div id="previews_direct_deposit">
                                    <div id="template" class="dz-image-preview">

                                        <div class="uploaded uploaded--one">
                                            <img data-dz-thumbnail>
                                            <div class="file">
                                                <div class="file__name">
                                                    <p data-dz-name class="uploaded_filename"></p>
                                                    <p data-dz-size></p>
                                                    <i data-dz-remove class="fas fa-times"></i>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" data-dz-uploadprogress aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="container_id_proof">
                        <label class="mt-4" style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">ID Proof <span style="color: red;">*</span> </b><br> In order to verify your identity, Please provide your ID proof. Examples of ID proof are: Driver license, passport etc.</label>

                        <div class="FileUpload">
                            <div class="wrapper id_proof_required">

                                <div class="upload collegeReceiptDropzone">
                                    <p>Drag files here or <span class="upload__button fileinput-button4 dz-clickable">Browse</span></p>
                                </div>

                                <div id="previews_id_proof">
                                    <div id="template4" class="dz-image-preview">

                                        <div class="uploaded uploaded--one">
                                            <img data-dz-thumbnail>
                                            <div class="file">
                                                <div class="file__name">
                                                    <p data-dz-name class="uploaded_filename"></p>
                                                    <p data-dz-size></p>
                                                    <i data-dz-remove class="fas fa-times"></i>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" data-dz-uploadprogress aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="container_college_receipt">

                        <label style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">T2202(College Receipt)</b><br> If you want to avail college fee credits, please provide all college fee receipts (T2200) issued by your college.</label>

                        <div class="FileUpload">
                            <div class="wrapper">

                                <div class="upload collegeReceiptDropzone">
                                    <p>Drag files here or <span class="upload__button fileinput-button2 dz-clickable">Browse</span></p>
                                </div>

                                <div id="previews_college_receipt">
                                    <div id="template2" class="dz-image-preview">

                                        <!-- This is used as the file preview template -->
                                        <div class="uploaded uploaded--one">
                                            <img data-dz-thumbnail>
                                            <div class="file">
                                                <div class="file__name">
                                                    <p data-dz-name class="uploaded_filename"></p>
                                                    <p data-dz-size></p>
                                                    <i data-dz-remove class="fas fa-times"></i>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" data-dz-uploadprogress aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="container_t4">
                        <label style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">T4/T4A/T Slips</b><br>(Please provide passwords if T4s are password secured in the message box)</label>


                        <div class="FileUpload">
                            <div class="wrapper">

                                <div class="upload collegeReceiptDropzone">
                                    <p>Drag files here or <span class="upload__button fileinput-button3 dz-clickable">Browse</span></p>
                                </div>

                                <div id="previews_t4">
                                    <div id="template3" class="dz-image-preview">

                                        <!-- This is used as the file preview template -->
                                        <div class="uploaded uploaded--one">
                                            <img data-dz-thumbnail>
                                            <div class="file">
                                                <div class="file__name">
                                                    <p data-dz-name class="uploaded_filename"></p>
                                                    <p data-dz-size></p>
                                                    <i data-dz-remove class="fas fa-times"></i>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" data-dz-uploadprogress aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div id="rent_info">

                        <label class="mt-4 mb-4" style="font-size: 15px;"><b style="color: #0075be; font-size: 16px;">Rent Information</b><br>
                            If you want to claim your benefit, please provide us below mentioned rent details.
                        </label>

                        <div id="rent_id" class="repeater" style="width: 98%;  margin-left: auto; margin-right: auto;">
                            <table border="1" style="border: none;">
                                <thead>
                                    <tr>
                                        <th style="width:50%">Rent Address</th>
                                        <th>Number of Months</th>
                                        <th>Total Rent Paid</th>
                                        <th style="width:50px">&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-repeater-list="group-a">
                                    <tr data-repeater-item>
                                        <td data-label="Rent Address"><input class="form-control rent_address_search" name="rent_address"></td>
                                        <td data-label="Total Months of Rent"><input class="form-control total_month_rent" name="total_month_rent"></td>
                                        <td data-label="Total Rent Paid"><input class="form-control total_rent_paid" name="total_rent_paid"></td>
                                        <td>
                                            <span class="form-control rent_info_delete" data-repeater-delete type="button" style="text-align: center;width: 50px;">
                                                <i class="fas fa-trash-alt" style="color: red;"></i>
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            <input data-repeater-create type="button" class="child_repeater" style="border-radius: 2px; background-color:#0075be; color: white; padding: 5px 10px;" value="Add Address" />
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <label class="form-label"><b> Do you have income from Uber/Skip/Lyft/Doordash etc.? <span style="color: red;">*</span></b></label>
                        <br>
                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="show_delivery_tax" name="income_delivery" value="Yes" required>
                            <label for="show_delivery_tax" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="hide_delivery_tax" name="income_delivery" value="No">
                            <label for="hide_delivery_tax" class="form-check-label">No</label>
                        </div>
                    </div>

                    <div class="delivery_annual_tax mt-4" id="delivery_annual_tax">
                        <label style="font-size: 15px;"><b style="color:#0075be; font-size: 16px">Annual Tax summary</b> <span style="color: red;">*</span></label>

                        <div class="FileUpload">
                            <div class="wrapper annual_tax_required">

                                <div class="upload delivery_annual_tax_box">
                                    <p>Drag files here or <span class="upload__button fileinput-button6 dz-clickable">Browse</span></p>
                                </div>

                                <div id="previews_annual_tax">
                                    <div id="template6" class="dz-image-preview">

                                        <!-- This is used as the file preview template -->
                                        <div class="uploaded uploaded--one">
                                            <img data-dz-thumbnail>
                                            <div class="file">
                                                <div class="file__name">
                                                    <p data-dz-name class="uploaded_filename"></p>
                                                    <p data-dz-size></p>
                                                    <i data-dz-remove class="fas fa-times"></i>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" data-dz-uploadprogress aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4 mb-3">
                            <label for="summary_expenses"><b style="font-size: 16px;">Summary of Expenses</b> <span style="color: red;">*</span></label>
                            <textarea class="form-control mt-2" style="width: 100%; margin-left:auto; margin-right:auto;" rows="6" id="summary_expenses" name="summary_expenses" required></textarea>
                        </div>

                        <label class="form-label"><b>Do you want to file HST for your Uber/Skip/Lyft/Doordash?</b> <span style="color: red;">*</span></label>

                        <br>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="show_hst" name="delivery_hst" value="Yes" required>
                            <label for="show_hst" class="form-check-label">Yes</label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" class="form-check-input" id="hide_hst" name="delivery_hst" value="No">
                            <label for="hide_hst" class="form-check-label">No</label>
                        </div>

                        <div class="hst mt-4" id="hst">
                            <div class="row">

                                <div class="col-md-6">
                                    <label class="form-label"><b>HST # <span style="color: red;">*</span></b></label>
                                    <input type="text" name="hst_number" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label"><b>Access code <span style="color: red;">*</span></b></label>
                                    <input type="text" name="hst_access_code" class="form-control" required>
                                </div>

                                <div class="col-md-6">
                                    <div class="date-container">
                                        <label class="form-label">Start Date <span style="color: red;">*</span></label>
                                        <input type="text" name="hst_start_date" class="form-control date_input_icon" id="hst_start_date" required>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="date-container">
                                        <label class="form-label">End Date <span style="color: red;">*</span></label>
                                        <input type="text" name="hst_end_date" class="form-control date_input_icon" id="hst_end_date" required>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <br>

                    <div class="container_additional_documents">
                        <label style="font-size: 15px;"><b style="color:#0075be; font-size: 16px;">Additional Documents to upload</b> <br>
                            If you have any additional document which is not listed above, please attach in the below section.
                        </label>

                        <div class="FileUpload">
                            <div class="wrapper" style="margin-bottom: 0px;">

                                <div class="upload collegeReceiptDropzone">
                                    <p>Drag files here or <span class="upload__button fileinput-button5 dz-clickable">Browse</span></p>
                                </div>

                                <div id="previews_additional_documents">
                                    <div id="template5" class="dz-image-preview">

                                        <!-- This is used as the file preview template -->
                                        <div class="uploaded uploaded--one">
                                            <img data-dz-thumbnail>
                                            <div class="file">
                                                <div class="file__name">
                                                    <p data-dz-name class="uploaded_filename"></p>
                                                    <p data-dz-size></p>
                                                    <i data-dz-remove class="fas fa-times"></i>
                                                </div>
                                                <div class="progress">
                                                    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" data-dz-uploadprogress aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"></div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <br>

                    <div class="form-group">
                        <label class="form-label" for="message_us">Your Message For Us?</label>
                        <textarea class="form-control" rows="5" id="message_us" name="message_us"></textarea>
                    </div>
                </div>

                <div style="overflow: auto; text-align: center">
                    <div style="float: center; margin-top: 20px" id="multi_step_button">
                        <button type="button" class="previous hvr-bounce-to-bottom">Previous</button>
                        <button type="button" class="next">Continue</button>
                        <!-- <button type="button" class="next hvr-bounce-to-bottom">Continue</button> -->
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

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>
    <script src="assets/js/jquery.repeater.min.js"></script>
    <script src="assets/js/foundation-datepicker.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>


    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCQOcE0JB-zoQEhoj_GOuayRPObjuKyh_k&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src="https://js.upload.io/upload-js/v2"></script>


    <script>
        $(document).ready(function() {
            var emailSent = new URLSearchParams(window.location.search).get("email_sent");
            var emailSentShown = localStorage.getItem("emailSentShown");

            if (emailSent === "success" && !emailSentShown) {
                $('#emailSentModal').modal('show');
                localStorage.setItem("emailSentShown", true);
            }



        });
    </script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="popover"]').popover({
                placement: 'top',
                trigger: 'hover'
            });
        });
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
                        dateMaritalChangeLabel.innerHTML = 'Date Divorced';
                    }
                });
            }

            const popover = new bootstrap.Popover('.popover-dismiss', {
                trigger: 'focus'
            })


            // Function to hide the first delete icon if there's only one, else show it
            function updateRentInfoDeleteVisibility() {
                var rentInfoDeleteElements = $(".rent_info_delete");
                var firstRowInputs = $("tr:first-child").find(".rent_address_search, .total_month_rent, .total_rent_paid");
                var firstRowDeleteElement = $("tr:first-child").find(".rent_info_delete_first");

                if (rentInfoDeleteElements.length > 1) {
                    $("tr:first-child .rent_info_delete").hide();
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
                            $("tr:first-child td:nth-child(4)").append(newDeleteElement);
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
            $('.child_repeater').click(function() {
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
            function updateChildInfoDeleteVisibility() {
                var childInfoDeleteElements = $(".child_info_delete");
                var firstRowInputs = $("tr:first-child").find(".child_first_name, .child_last_name, .child_date_birth");
                var firstRowDeleteElement = $("tr:first-child").find(".child_info_delete_first");

                if (childInfoDeleteElements.length > 1) {
                    $("tr:first-child .child_info_delete").hide();
                    childInfoDeleteElements.not(":first").show();
                } else {
                    childInfoDeleteElements.hide();
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
                            $("tr:first-child td:nth-child(4)").append(newDeleteElement);
                        }
                        firstRowDeleteElement.show();
                    } else {
                        firstRowDeleteElement.hide();
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



        });
    </script>

    <script>

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


            $('#date_entry').on('change', function() {

                const date = new Date($(this).val());
                const date2 = new Date($(this).val());

                $('#year1').val(subtractYears(date, 0));
                $('#year2').val(subtractYears(date, 1));
                $('#year3').val(subtractYears(date, 1));

                $('#year1_income').attr('placeholder', 'Year ' + subtractYears(date2, 0) + ' Income');
                $('#year2_income').attr('placeholder', 'Year ' + subtractYears(date2, 1) + ' Income');
                $('#year3_income').attr('placeholder', 'Year ' + subtractYears(date2, 1) + ' Income');
            });

            $('#spouse_date_entry').on('change', function() {

                const date = new Date($(this).val());
                const date2 = new Date($(this).val());

                $('#spouse_year1').val(subtractYears(date, 0));
                $('#spouse_year2').val(subtractYears(date, 1));
                $('#spouse_year3').val(subtractYears(date, 1));

                $('#spouse_year1_income').attr('placeholder', 'Year ' + subtractYears(date2, 0) + ' Income');
                $('#spouse_year2_income').attr('placeholder', 'Year ' + subtractYears(date2, 1) + ' Income');
                $('#spouse_year3_income').attr('placeholder', 'Year ' + subtractYears(date2, 1) + ' Income');
            });

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

        function subtractYears(date, years) {
            date.setFullYear(date.getFullYear() - years);

            return date.getFullYear();
        }

        $('.repeater').repeater({
            // options and callbacks here
            // isFirstItemUndeletable: true,
            show: function() {
                $(this).slideDown();
            },
        });


        $(document).ready(function() {

            // Function to hide the first delete icon if there's only one, else show it
            function updateRentInfoDeleteVisibility() {
                var rentInfoDeleteElements = $(".rent_info_delete");
                var firstRowInputs = $("tr:first-child").find(".rent_address_search, .total_month_rent, .total_rent_paid");
                var firstRowDeleteElement = $("tr:first-child").find(".rent_info_delete_first");

                if (rentInfoDeleteElements.length > 1) {
                    $("tr:first-child .rent_info_delete").hide();
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
                            $("tr:first-child td:nth-child(4)").append(newDeleteElement);
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
            $('.child_repeater').click(function() {
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
            function updateChildInfoDeleteVisibility() {
                var childInfoDeleteElements = $(".child_info_delete");
                var firstRowInputs = $("tr:first-child").find(".child_first_name, .child_last_name, .child_date_birth");
                var firstRowDeleteElement = $("tr:first-child").find(".child_info_delete_first");

                if (childInfoDeleteElements.length > 1) {
                    $("tr:first-child .child_info_delete").hide();
                    childInfoDeleteElements.not(":first").show();
                } else {
                    childInfoDeleteElements.hide();
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
                            $("tr:first-child td:nth-child(4)").append(newDeleteElement);
                        }
                        firstRowDeleteElement.show();
                    } else {
                        firstRowDeleteElement.hide();
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



        });

        $(function() {
            $('body').scrollTop(0);
        });


        $('body').on('focus', ".child_date_birth", function() {
            $(this).fdatepicker({
                format: 'yyyy-mm-dd'
            });
        });

        $('#date_birthdate').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#date_movedate').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#date_entry').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#spouse_date_birth').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#date_marriage').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#marital_change').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#hst_start_date').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#hst_end_date').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('#spouse_date_entry').fdatepicker({
            format: 'yyyy-mm-dd'
        });
        $('.child_date_birth').fdatepicker({
            format: 'yyyy-mm-dd'
        });

        $('#date_birthdate').fdatepicker();
        $('#date_movedate').fdatepicker();
        $('#date_entry').fdatepicker();
        $('#spouse_date_birth').fdatepicker();
        $('#date_marriage').fdatepicker();
        $('#marital_change').fdatepicker();
        $('#hst_start_date').fdatepicker();
        $('#hst_end_date').fdatepicker();
        $('#spouse_date_entry').fdatepicker();
        $('.child_date_birth').fdatepicker();

        // import { Upload } from "upload-js"
        const upload = Upload({
            // apiKey: 'public_kW15b5vDn4vNXqyhGbUY34DJebXE'
            apiKey: 'public_FW25b5oN4QnUV7szRund7XvErXf3'
            // apiKey: 'public_12a1xyYDPCg7cv4S269y5qFKaBQR'
            // apiKey: "public_W142hcc2h5eQGTMFoVuuuXTRkS6g" // Your real API key.
            // apiKey: "public_12a1xwCAKs8t4PPg6XDjVrGt47k6" // Your real API key.
        });

        let fileUrls = [];

        const onFileSelected = async event => {
            const files = event.target.files;
            const fname = document.querySelector('input[name="firstName"]').value;
            const lname = document.querySelector('input[name="lastName"]').value;
            const birthdate = document.querySelector('input[name="birth_date"]').value;

            try {
                createAlert('Adding Documents', '', '', 'info', false, true, 'pageMessages');

                // Iterate over each file
                for (const file of files) {
                    const fileName = `${file.name}`; // add the unique code and file extension to the file name

                    const {
                        fileUrl
                    } = await upload.uploadFile(file, {
                        onBegin: ({
                            cancel
                        }) => {
                            // Call 'cancel()' to stop the upload.
                        },
                        onProgress: ({
                            bytesSent,
                            bytesTotal
                        }) => {
                            // Use this to display progress.
                        },
                        metadata: {
                            // Up to 2KB of arbitrary JSON.
                            productId: 60891
                        },
                        tags: [
                            // Up to 25 tags per file.
                            "product_image"
                        ],
                        path: {
                            // See path variables: https://upload.io/dashboard/docs/path-variables
                            folderPath: `/uploads/${fname}-${lname}-${birthdate}`,
                            fileName: "{ORIGINAL_FILE_NAME}-{UTC_DATE}-{UTC_HOUR}:{UTC_MINUTE}{ORIGINAL_FILE_EXT}" // use the modified file name
                        }
                    });

                    // Add fileUrl to fileUrls array only if it's not already there
                    if (!fileUrls.includes(fileUrl)) {
                        fileUrls.push(fileUrl);
                    }

                    console.log(fileUrls);
                }

                // Clear out existing input controls before appending new ones
                $("#myForm input[name='direct[]']").remove();

                // Append input control for each file in fileUrls array
                fileUrls.forEach(fileUrl => {
                    $("<input type='hidden' value=" + fileUrl + " />")
                        .attr("name", "direct[]")
                        .prependTo("#myForm");
                });

                createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');

            } catch (e) {
                // alert(`Upload failed: ${e.message}`);
            }
        };



        let collegeReceiptUrls = [];

        const onCollegeReceipt = async event => {
            const files = event.target.files;
            const fname = document.querySelector('input[name="firstName"]').value;
            const lname = document.querySelector('input[name="lastName"]').value;
            const birthdate = document.querySelector('input[name="birth_date"]').value;

            try {

                createAlert('Adding Documents', '', '', 'info', false, true, 'pageMessages');

                // Iterate over each file
                for (const file of files) {
                    const fileName = `${file.name}`; // add the unique code and file extension to the file name

                    const {
                        fileUrl
                    } = await upload.uploadFile(file, {
                        onBegin: ({
                            cancel
                        }) => {
                            // Call 'cancel()' to stop the upload.
                        },
                        onProgress: ({
                            bytesSent,
                            bytesTotal
                        }) => {
                            // Use this to display progress.
                        },
                        metadata: {
                            // Up to 2KB of arbitrary JSON.
                            productId: 60891
                        },
                        tags: [
                            // Up to 25 tags per file.
                            "product_image"
                        ],
                        path: {
                            // See path variables: https://upload.io/dashboard/docs/path-variables
                            folderPath: `/uploads/${fname}-${lname}-${birthdate}`,
                            fileName: "{ORIGINAL_FILE_NAME}-{UTC_DATE}-{UTC_HOUR}:{UTC_MINUTE}{ORIGINAL_FILE_EXT}"
                        }
                    });

                    // Add fileUrl to fileUrls array only if it's not already there
                    if (!collegeReceiptUrls.includes(fileUrl)) {
                        collegeReceiptUrls.push(fileUrl);
                    }

                    console.log(collegeReceiptUrls);
                }

                // Clear out existing input controls before appending new ones
                $("#myForm input[name='college[]']").remove();

                // Append input control for each file in collegeReceiptUrls array
                collegeReceiptUrls.forEach(fileUrl => {
                    $("<input type='hidden' value=" + fileUrl + " />")
                        .attr("name", "college[]")
                        .prependTo("#myForm");
                });

                createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');

            } catch (e) {}

        };

        let onIdProofUrls = [];

        const onIdProof = async event => {
            const files = event.target.files;
            const fname = document.querySelector('input[name="firstName"]').value;
            const lname = document.querySelector('input[name="lastName"]').value;
            const birthdate = document.querySelector('input[name="birth_date"]').value;

            try {

                createAlert('Adding Documents', '', '', 'info', false, true, 'pageMessages');

                // Iterate over each file
                for (const file of files) {
                    const fileName = `${file.name}`; // add the unique code and file extension to the file name

                    const {
                        fileUrl
                    } = await upload.uploadFile(file, {
                        onBegin: ({
                            cancel
                        }) => {
                            // Call 'cancel()' to stop the upload.
                        },
                        onProgress: ({
                            bytesSent,
                            bytesTotal
                        }) => {
                            // Use this to display progress.
                        },
                        metadata: {
                            // Up to 2KB of arbitrary JSON.
                            productId: 60891
                        },
                        tags: [
                            // Up to 25 tags per file.
                            "product_image"
                        ],
                        path: {
                            // See path variables: https://upload.io/dashboard/docs/path-variables
                            folderPath: `/uploads/${fname}-${lname}-${birthdate}`,
                            fileName: "{ORIGINAL_FILE_NAME}-{UTC_DATE}-{UTC_HOUR}:{UTC_MINUTE}{ORIGINAL_FILE_EXT}"
                        }
                    });

                    // Add fileUrl to fileUrls array only if it's not already there
                    if (!onIdProofUrls.includes(fileUrl)) {
                        onIdProofUrls.push(fileUrl);
                    }

                    console.log(onIdProofUrls);
                }

                // Clear out existing input controls before appending new ones
                $("#myForm input[name='id_proof[]']").remove();

                // Append input control for each file in onIdProofUrls array
                onIdProofUrls.forEach(fileUrl => {
                    $("<input type='hidden' value=" + fileUrl + " />")
                        .attr("name", "id_proof[]")
                        .prependTo("#myForm");
                });

                createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');

            } catch (e) {
                alert(`Upload failed: ${e.message}`);
            }
        }


        let onT4Urls = [];

        const onT4 = async event => {
            const files = event.target.files;
            const fname = document.querySelector('input[name="firstName"]').value;
            const lname = document.querySelector('input[name="lastName"]').value;
            const birthdate = document.querySelector('input[name="birth_date"]').value;

            try {


                createAlert('Adding Documents', '', '', 'info', false, true, 'pageMessages');

                // Iterate over each file
                for (const file of files) {
                    const fileName = `${file.name}`; // add the unique code and file extension to the file name

                    const {
                        fileUrl
                    } = await upload.uploadFile(file, {
                        onBegin: ({
                            cancel
                        }) => {
                            // Call 'cancel()' to stop the upload.
                        },
                        onProgress: ({
                            bytesSent,
                            bytesTotal
                        }) => {
                            // Use this to display progress.
                        },
                        metadata: {
                            // Up to 2KB of arbitrary JSON.
                            productId: 60891
                        },
                        tags: [
                            // Up to 25 tags per file.
                            "product_image"
                        ],
                        path: {
                            // See path variables: https://upload.io/dashboard/docs/path-variables
                            folderPath: `/uploads/${fname}-${lname}-${birthdate}`,
                            fileName: "{ORIGINAL_FILE_NAME}-{UTC_DATE}-{UTC_HOUR}:{UTC_MINUTE}{ORIGINAL_FILE_EXT}"
                        }
                    });

                    // Add fileUrl to fileUrls array only if it's not already there
                    if (!onT4Urls.includes(fileUrl)) {
                        onT4Urls.push(fileUrl);
                    }

                    console.log(onT4Urls);
                }

                // Clear out existing input controls before appending new ones
                $("#myForm input[name='t_slips[]']").remove();

                // Append input control for each file in onT4Urls array
                onT4Urls.forEach(fileUrl => {
                    $("<input type='hidden' value=" + fileUrl + " />")
                        .attr("name", "t_slips[]")
                        .prependTo("#myForm");
                });

                createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');

            } catch (e) {
                alert(`Upload failed: ${e.message}`);
            }
        }



        let onAnnualTaxSummaryUrls = [];

        const onAnnualTaxSummary = async event => {
            const files = event.target.files;
            const fname = document.querySelector('input[name="firstName"]').value;
            const lname = document.querySelector('input[name="lastName"]').value;
            const birthdate = document.querySelector('input[name="birth_date"]').value;

            try {

                createAlert('Adding Documents', '', '', 'info', false, true, 'pageMessages');

                // Iterate over each file
                for (const file of files) {
                    const fileName = `${file.name}`; // add the unique code and file extension to the file name

                    const {
                        fileUrl
                    } = await upload.uploadFile(file, {
                        onBegin: ({
                            cancel
                        }) => {
                            // Call 'cancel()' to stop the upload.
                        },
                        onProgress: ({
                            bytesSent,
                            bytesTotal
                        }) => {
                            // Use this to display progress.
                        },
                        metadata: {
                            // Up to 2KB of arbitrary JSON.
                            productId: 60891
                        },
                        tags: [
                            // Up to 25 tags per file.
                            "product_image"
                        ],
                        path: {
                            // See path variables: https://upload.io/dashboard/docs/path-variables
                            folderPath: `/uploads/${fname}-${lname}-${birthdate}`,
                            fileName: "{ORIGINAL_FILE_NAME}-{UTC_DATE}-{UTC_HOUR}:{UTC_MINUTE}{ORIGINAL_FILE_EXT}"
                        }
                    });

                    // Add fileUrl to fileUrls array only if it's not already there
                    if (!onAnnualTaxSummaryUrls.includes(fileUrl)) {
                        onAnnualTaxSummaryUrls.push(fileUrl);
                    }

                    console.log(onAnnualTaxSummaryUrls);
                }

                // Clear out existing input controls before appending new ones
                $("#myForm input[name='tax_summary[]']").remove();

                // Append input control for each file in onAnnualTaxSummaryUrls array
                onAnnualTaxSummaryUrls.forEach(fileUrl => {
                    $("<input type='hidden' value=" + fileUrl + " />")
                        .attr("name", "tax_summary[]")
                        .prependTo("#myForm");
                });

                createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');


            } catch (e) {
                alert(`Upload failed: ${e.message}`);
            }
        }

        let onAdditionalDocumentUrls = [];

        const onAdditionalDocument = async event => {
            const files = event.target.files;
            const fname = document.querySelector('input[name="firstName"]').value;
            const lname = document.querySelector('input[name="lastName"]').value;
            const birthdate = document.querySelector('input[name="birth_date"]').value;

            try {

                createAlert('Adding Documents', '', '', 'info', false, true, 'pageMessages');

                // Iterate over each file
                for (const file of files) {
                    const fileName = `${file.name}`; // add the unique code and file extension to the file name

                    const {
                        fileUrl
                    } = await upload.uploadFile(file, {
                        onBegin: ({
                            cancel
                        }) => {
                            // Call 'cancel()' to stop the upload.
                        },
                        onProgress: ({
                            bytesSent,
                            bytesTotal
                        }) => {
                            // Use this to display progress.
                        },
                        metadata: {
                            // Up to 2KB of arbitrary JSON.
                            productId: 60891
                        },
                        tags: [
                            // Up to 25 tags per file.
                            "product_image"
                        ],
                        path: {
                            // See path variables: https://upload.io/dashboard/docs/path-variables
                            folderPath: `/uploads/${fname}-${lname}-${birthdate}`,
                            fileName: "{ORIGINAL_FILE_NAME}-{UTC_DATE}-{UTC_HOUR}:{UTC_MINUTE}{ORIGINAL_FILE_EXT}"
                        }
                    });

                    // Add fileUrl to fileUrls array only if it's not already there
                    if (!onAdditionalDocumentUrls.includes(fileUrl)) {
                        onAdditionalDocumentUrls.push(fileUrl);
                    }

                    console.log(onAdditionalDocumentUrls);
                }

                // Clear out existing input controls before appending new ones
                $("#myForm input[name='additional_docs[]']").remove();

                // Append input control for each file in onAdditionalDocumentUrls array
                onAdditionalDocumentUrls.forEach(fileUrl => {
                    $("<input type='hidden' value=" + fileUrl + " />")
                        .attr("name", "additional_docs[]")
                        .prependTo("#myForm");
                });

                createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');

            } catch (e) {
                alert(`Upload failed: ${e.message}`);
            }
        }

        // Get the template HTML and remove it from the doumenthe template HTML and remove it from the doument
        var previewNode = document.querySelector("#template");
        var previewNode2 = document.querySelector("#template2");
        var previewNode3 = document.querySelector("#template3");
        var previewNode4 = document.querySelector("#template4");
        var previewNode5 = document.querySelector("#template5");
        var previewNode6 = document.querySelector("#template6");

        previewNode.id = "";
        previewNode2.id = "";
        previewNode3.id = "";
        previewNode4.id = "";
        previewNode5.id = "";
        previewNode6.id = "";

        var previewTemplate = previewNode.parentNode.innerHTML;
        var previewTemplate2 = previewNode2.parentNode.innerHTML;
        var previewTemplate3 = previewNode3.parentNode.innerHTML;
        var previewTemplate4 = previewNode4.parentNode.innerHTML;
        var previewTemplate5 = previewNode5.parentNode.innerHTML;
        var previewTemplate6 = previewNode6.parentNode.innerHTML;

        previewNode.parentNode.removeChild(previewNode);
        previewNode2.parentNode.removeChild(previewNode2);
        previewNode3.parentNode.removeChild(previewNode3);
        previewNode4.parentNode.removeChild(previewNode4);
        previewNode5.parentNode.removeChild(previewNode5);
        previewNode6.parentNode.removeChild(previewNode6);

        var formData = new FormData();


        gapi.load('client', initClient);

        function initClient() {
            gapi.client.init({
                'clientId': '83686099476-0l8uoff8dehfmcl7os6083on7hurdm8c.apps.googleusercontent.com',
                'scope': 'https://www.googleapis.com/auth/drive.file',
                'discoveryDocs': ['https://www.googleapis.com/discovery/v1/apis/drive/v3/rest']
            }).then(function() {
                // Handle successful initialization here
            }, function(error) {
                // Handle error here
            });
        }


        var files = []; // Array to keep track of files

        var myDropzone = new Dropzone(".container_direct_deposit", { // Make the whole body a dropzone
            url: "sendmail", // Set the url'
            autoProcessQueue: true,
            autoDiscover: false,
            uploadMultiple: true,
            parallelUploads: 100,
            maxFiles: 10,
            init: function() {
                this.on("maxfilesexceeded", function(file) {
                    createAlert("Max Upload Exceed!", "", "", "danger", true, true, "pageMessages");
                });

                this.on("addedfile", function(file) {
                    var ext = checkFileExt(file.name); // Get extension
                    var newimage = "";

                    // Check extension
                    if (ext == 'pdf') {
                        newimage = "assets/images/pdf_icon.png"; // default image path
                    } else if (ext == 'docx') {
                        newimage = "assets/images/word_icon.png"; // default image path
                    } else if (ext == 'doc') {
                        newimage = "assets/images/doc_icon.png"; // default image path
                    } else if (ext == 'txt') {
                        newimage = "assets/images/txt_icon.png"; // default image path
                    } else if (ext == 'xls') {
                        newimage = "assets/images/xls_icon.png"; // default image path
                    } else if (ext == 'xlsx') {
                        newimage = "assets/images/xlsx_icon.png"; // default image path
                    } else if (ext == 'csv') {
                        newimage = "assets/images/csv_icon.png"; // default image path
                    }

                    // Check for duplicates
                    if (files.some(function(f) {
                            return f.name == file.name && f.size == file.size
                        })) {
                        this.removeFile(file);
                        createAlert(
                            "File already exists!",
                            "",
                            "",
                            "danger",
                            true,
                            true,
                            "pageMessages"
                        );
                    } else {
                        // onFileSelected(event)
                        uploadToDrive(files);
                        files.push(file);

                        this.createThumbnailFromUrl(file, newimage);
                        console.log(file.type + file.name);
                    }
                });

                this.on("removedfile", function(file) {
                    // Remove the input control for the file from the upload.js form
                    $("#myForm input[value='" + file.upload.uuid + "']").remove();

                    // Remove the file from the files array
                    files = files.filter(function(f) {
                        return f.upload.uuid !== file.upload.uuid;
                    });

                    // Remove the file from the fileUrls array
                    fileUrls = fileUrls.filter(function(url) {
                        return url.indexOf(file.upload.uuid) === -1;
                    });
                });

                this.on("successmultiple", function(files, response) {
                    // console.log(response);
                });


            },
            maxFilesize: 10, // MB
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate,
            autoQueue: true, // Make sure the files aren't queued until manually added
            acceptedFiles: "image/png,.jpeg,.tiff,,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            previewsContainer: "#previews_direct_deposit", // Define the container to display the previews
            clickable: ".fileinput-button" // Define the element that should be used as click trigger to select files.
        });

        function uploadToDrive(file, callback) {
            var metadata = {
                'name': file.name,
                'mimeType': file.type
            };

            var access_token = 'ya29.a0AVvZVsrtyXaDh4wp9CY215S9qc2pEmSwvCL2TyEZFXHvP09ovYLEvMlJ34ESS7ImZFMLFG_z86ipxs6TrPsdFwEcMx2kEqpipI1U046yzq_HtytGWZ22qfRcs3yRSx22A3GKUB3jLFChPQLhFW0QOHnQLR--aCgYKAW8SARMSFQGbdwaIV2qt7sFRns4ri9-58IsoKQ0163';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'https://www.googleapis.com/upload/drive/v3/files?uploadType=resumable');
            xhr.setRequestHeader('Authorization', 'Bearer ' + access_token);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-Upload-Content-Type', file.type);
            xhr.setRequestHeader('X-Upload-Content-Length', file.size);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var location = xhr.getResponseHeader('Location');
                    var uploadXhr = new XMLHttpRequest();
                    uploadXhr.open('PUT', location, true);
                    uploadXhr.setRequestHeader('Content-Type', file.type);
                    uploadXhr.setRequestHeader('X-Upload-Content-Type', file.type);
                    uploadXhr.setRequestHeader('X-Upload-Content-Length', file.size);
                    uploadXhr.setRequestHeader('Authorization', 'Bearer ' + access_token);
                    uploadXhr.upload.onprogress = function(e) {
                        var progress = Math.round((e.loaded / e.total) * 100);
                        console.log(progress);
                    };
                    uploadXhr.onreadystatechange = function() {
                        if (uploadXhr.readyState === 4 && uploadXhr.status === 200) {
                            console.log(uploadXhr.response);
                            var response = JSON.parse(uploadXhr.response);
                            var fileId = response.id;
                            callback(fileId);
                        }
                    };
                    uploadXhr.send(file);
                }
            };
            xhr.send(JSON.stringify(metadata));
        }

        var idProofDropzone = new Dropzone(".container_id_proof", { // Make the whole body a dropzone
            url: "sendmail", // Set the url
            autoProcessQueue: true,
            autoDiscover: false,
            uploadMultiple: true,
            parallelUploads: 100,
            init: function() {
                this.on("addedfile", function(file) {
                    var errorLabel = document.querySelector("#errorLabel");
                    var ext = checkFileExt(file.name); // Get extension
                    var newimage = "";

                    // Check extension
                    if (ext == 'pdf') {
                        newimage = "assets/images/pdf_icon.png"; // default image path
                    } else if (ext == 'docx') {
                        newimage = "assets/images/word_icon.png"; // default image path
                    } else if (ext == 'doc') {
                        newimage = "assets/images/doc_icon.png"; // default image path
                    } else if (ext == 'txt') {
                        newimage = "assets/images/txt_icon.png"; // default image path
                    } else if (ext == 'xls') {
                        newimage = "assets/images/xls_icon.png"; // default image path
                    } else if (ext == 'xlsx') {
                        newimage = "assets/images/xlsx_icon.png"; // default image path
                    } else if (ext == 'csv') {
                        newimage = "assets/images/csv_icon.png"; // default image path
                    }

                    // Check for duplicates
                    if (files.some(function(f) {
                            return f.name == file.name && f.size == file.size
                        })) {
                        this.removeFile(file);
                        createAlert(
                            "File already exists!",
                            "",
                            "",
                            "danger",
                            true,
                            true,
                            "pageMessages"
                        );
                    } else {
                        onIdProof(event)

                        if (errorLabel) {
                            errorLabel.remove();
                        }

                        document.querySelector(".id_proof_required").style.border = "";

                        this.createThumbnailFromUrl(file, newimage);
                        files.push(file);
                        console.log(file.type + file.name);
                    }
                });

                this.on("successmultiple", function(files, response) {
                    // console.log(response);
                });

            },
            maxFilesize: 10, // MB
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate4,
            autoQueue: true, // Make sure the files aren't queued until manually added
            acceptedFiles: "image/png,.jpeg,.tiff,,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            previewsContainer: "#previews_id_proof", // Define the container to display the previews
            clickable: ".fileinput-button4" // Define the element that should be used as click trigger to select files.
        });

        var collegeReceiptDropzone = new Dropzone(".container_college_receipt", { // Make the whole body a dropzone
            url: "sendmail", // Set the url
            autoProcessQueue: true,
            autoDiscover: false,
            uploadMultiple: true,
            parallelUploads: 100,
            init: function() {

                this.on("addedfile", function(file) {
                    var ext = checkFileExt(file.name); // Get extension
                    var newimage = "";

                    // Check extension
                    if (ext == 'pdf') {
                        newimage = "assets/images/pdf_icon.png"; // default image path
                    } else if (ext == 'docx') {
                        newimage = "assets/images/word_icon.png"; // default image path
                    } else if (ext == 'doc') {
                        newimage = "assets/images/doc_icon.png"; // default image path
                    } else if (ext == 'txt') {
                        newimage = "assets/images/txt_icon.png"; // default image path
                    } else if (ext == 'xls') {
                        newimage = "assets/images/xls_icon.png"; // default image path
                    } else if (ext == 'xlsx') {
                        newimage = "assets/images/xlsx_icon.png"; // default image path
                    } else if (ext == 'csv') {
                        newimage = "assets/images/csv_icon.png"; // default image path
                    }

                    // Check for duplicates
                    if (files.some(function(f) {
                            return f.name == file.name && f.size == file.size
                        })) {
                        this.removeFile(file);
                        createAlert(
                            "File already exists!",
                            "",
                            "",
                            "danger",
                            true,
                            true,
                            "pageMessages"
                        );
                    } else {
                        onCollegeReceipt(event)
                        files.push(file);
                        this.createThumbnailFromUrl(file, newimage);
                        console.log(file.type + file.name);
                    }
                });

                this.on("successmultiple", function(files, response) {
                    console.log(response);
                });


            },
            maxFilesize: 10, // MB
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate2,
            autoQueue: true, // Make sure the files aren't queued until manually added
            acceptedFiles: "image/png,.jpeg,.tiff,,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            previewsContainer: "#previews_college_receipt", // Define the container to display the previews
            clickable: ".fileinput-button2" // Define the element that should be used as click trigger to select files.
        });

        var t4Dropzone = new Dropzone(".container_t4", { // Make the whole body a dropzone
            url: "sendmail", // Set the url
            autoProcessQueue: true,
            autoDiscover: false,
            uploadMultiple: true,
            parallelUploads: 100,
            init: function() {

                this.on("addedfile", function(file) {
                    var ext = checkFileExt(file.name); // Get extension
                    var newimage = "";

                    // Check extension
                    if (ext == 'pdf') {
                        newimage = "assets/images/pdf_icon.png"; // default image path
                    } else if (ext == 'docx') {
                        newimage = "assets/images/word_icon.png"; // default image path
                    } else if (ext == 'doc') {
                        newimage = "assets/images/doc_icon.png"; // default image path
                    } else if (ext == 'txt') {
                        newimage = "assets/images/txt_icon.png"; // default image path
                    } else if (ext == 'xls') {
                        newimage = "assets/images/xls_icon.png"; // default image path
                    } else if (ext == 'xlsx') {
                        newimage = "assets/images/xlsx_icon.png"; // default image path
                    } else if (ext == 'csv') {
                        newimage = "assets/images/csv_icon.png"; // default image path
                    }

                    // Check for duplicates
                    if (files.some(function(f) {
                            return f.name == file.name && f.size == file.size
                        })) {
                        this.removeFile(file);
                        createAlert(
                            "File already exists!",
                            "",
                            "",
                            "danger",
                            true,
                            true,
                            "pageMessages"
                        );
                    } else {
                        onT4(event)
                        files.push(file);
                        this.createThumbnailFromUrl(file, newimage);
                        console.log(file.type + file.name);
                    }
                });

                this.on("successmultiple", function(files, response) {
                    console.log(response);
                });

            },
            maxFilesize: 10, // MB
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate3,
            autoQueue: true, // Make sure the files aren't queued until manually added
            acceptedFiles: "image/png,.jpeg,.tiff,,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            previewsContainer: "#previews_t4", // Define the container to display the previews
            clickable: ".fileinput-button3" // Define the element that should be used as click trigger to select files.
        });


        var additionalDocumentDropzone = new Dropzone(".container_additional_documents", { // Make the whole body a dropzone
            url: "sendmail", // Set the url
            autoProcessQueue: true,
            autoDiscover: false,
            uploadMultiple: true,
            parallelUploads: 100,
            init: function() {

                this.on("addedfile", function(file) {
                    var ext = checkFileExt(file.name); // Get extension
                    var newimage = "";

                    // Check extension
                    if (ext == 'pdf') {
                        newimage = "assets/images/pdf_icon.png"; // default image path
                    } else if (ext == 'docx') {
                        newimage = "assets/images/word_icon.png"; // default image path
                    } else if (ext == 'doc') {
                        newimage = "assets/images/doc_icon.png"; // default image path
                    } else if (ext == 'txt') {
                        newimage = "assets/images/txt_icon.png"; // default image path
                    } else if (ext == 'xls') {
                        newimage = "assets/images/xls_icon.png"; // default image path
                    } else if (ext == 'xlsx') {
                        newimage = "assets/images/xlsx_icon.png"; // default image path
                    } else if (ext == 'csv') {
                        newimage = "assets/images/csv_icon.png"; // default image path
                    }

                    // Check for duplicates
                    if (files.some(function(f) {
                            return f.name == file.name && f.size == file.size
                        })) {
                        this.removeFile(file);
                        createAlert(
                            "File already exists!",
                            "",
                            "",
                            "danger",
                            true,
                            true,
                            "pageMessages"
                        );
                    } else {
                        onAdditionalDocument(event)
                        files.push(file);
                        this.createThumbnailFromUrl(file, newimage);
                        console.log(file.type + file.name);
                    }
                });

                this.on("successmultiple", function(files, response) {
                    console.log(response);
                });

            },
            maxFilesize: 10, // MB
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate5,
            autoQueue: true, // Make sure the files aren't queued until manually added
            acceptedFiles: "image/png,.jpeg,.tiff,,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            previewsContainer: "#previews_additional_documents", // Define the container to display the previews
            clickable: ".fileinput-button5" // Define the element that should be used as click trigger to select files.
        });


        var annualTaxSummary = new Dropzone(".delivery_annual_tax", { // Make the whole body a dropzone
            url: "sendmail", // Set the url
            autoProcessQueue: true,
            autoDiscover: false,
            uploadMultiple: true,
            parallelUploads: 100,
            init: function() {

                this.on("addedfile", function(file) {
                    var ext = checkFileExt(file.name); // Get extension
                    var newimage = "";

                    // Check extension
                    if (ext == 'pdf') {
                        newimage = "assets/images/pdf_icon.png"; // default image path
                    } else if (ext == 'docx') {
                        newimage = "assets/images/word_icon.png"; // default image path
                    } else if (ext == 'doc') {
                        newimage = "assets/images/doc_icon.png"; // default image path
                    } else if (ext == 'txt') {
                        newimage = "assets/images/txt_icon.png"; // default image path
                    } else if (ext == 'xls') {
                        newimage = "assets/images/xls_icon.png"; // default image path
                    } else if (ext == 'xlsx') {
                        newimage = "assets/images/xlsx_icon.png"; // default image path
                    } else if (ext == 'csv') {
                        newimage = "assets/images/csv_icon.png"; // default image path
                    }

                    // Check for duplicates
                    if (files.some(function(f) {
                            return f.name == file.name && f.size == file.size
                        })) {
                        this.removeFile(file);
                        createAlert(
                            "File already exists!",
                            "",
                            "",
                            "danger",
                            true,
                            true,
                            "pageMessages"
                        );
                    } else {
                        onAnnualTaxSummary(event)
                        files.push(file);
                        this.createThumbnailFromUrl(file, newimage);
                        console.log(file.type + file.name);
                    }
                });

                this.on("successmultiple", function(files, response) {
                    console.log(response);
                });

            },
            maxFilesize: 10, // MB
            thumbnailWidth: 80,
            thumbnailHeight: 80,
            parallelUploads: 20,
            previewTemplate: previewTemplate5,
            autoQueue: true, // Make sure the files aren't queued until manually added
            acceptedFiles: "image/png,.jpeg,.tiff,,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            previewsContainer: "#previews_annual_tax", // Define the container to display the previews
            clickable: ".fileinput-button6" // Define the element that should be used as click trigger to select files.
        });

        // Get file extension
        function checkFileExt(filename) {
            filename = filename.toLowerCase();
            return filename.split('.').pop();
        }

        // Check if there are no files added
        document.querySelector('button.submit').addEventListener("click", function(e) {
            if (annualTaxSummary.files.length === 0) {

                // Display error message
                var errorLabelTax = document.createElement("label");

                errorLabelTax.innerHTML = "Annual Tax Summary is required";
                errorLabelTax.style.color = "red";
                errorLabelTax.id = "errorLabelTax";

                document.querySelector(".annual_tax_required").appendChild(errorLabelTax);

                document.querySelector(".annual_tax_required").style.border = "2px solid red";
                // Prevent form submission
                e.preventDefault();
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabelTax");
                if (errorLabel) {
                    errorLabel.remove();
                }
            }

            if (idProofDropzone.files.length === 0) {
                // Display error message
                event.preventDefault();
                var errorLabel = document.querySelector("#errorLabel");

                if (!errorLabel) {
                    errorLabel = document.createElement("label");
                    errorLabel.innerHTML = "ID Proof is required";
                    errorLabel.style.color = "red";
                    errorLabel.id = "errorLabel";
                    document.querySelector(".id_proof_required").appendChild(errorLabel);
                    document.querySelector(".id_proof_required").style.border = "2px solid red";
                }
                // // Prevent form submission
                e.preventDefault();
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabel");
                if (errorLabel) {
                    errorLabel.remove();
                }
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
    </script>
</body>

</html>