<?php
session_start();
include 'config.php';

// user SESSION CHECK SET OR NOT
if (isset($_SESSION['email'])) {
    if (isset($_SESSION['table']) && $_SESSION['table']== 'admin'){
        header('location:admin/');
    } else {
        header('location:../form/');
    } 
    exit();
}
else if (isset($_COOKIE['email']) && isset($_COOKIE['userPassword'])) {
    $_SESSION['email'] = $_COOKIE['email'];
    $_SESSION['table'] = $_COOKIE['table'];
    header('location:../form/');
    exit();
}

?>

<!DOCTYPE html>
<html class="no-js">
<head>
    <title>Paragon AFS | Login</title>
    <meta charset="utf-8">
    <link rel="icon" type="image/x-icon" href="../../assets/img/paragon_logo_icon.png" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <!-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" /> -->
    <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="assets/css/login.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="assets/css/components-md.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="assets/css/plugins-md.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->

    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="assets/plugins/froiden-helper/helper.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/custom.css" rel="stylesheet" type="text/css" />
    <!---END OF CSS FILES -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sofia+Sans:ital,wght@0,1..1000;1,1..1000&display=swap" rel="stylesheet">
    
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            background: #f1f1f1;
            transition: background 1s;
        }
        .container {
            display: flex;
            width: 100vw;
            overflow: hidden;
        }
        .image {
            /* filter: brightness(60%); */
            /* height: 100vh; */
            width: 100vw;
            background: url('./assets/img/logos/paragon_login_image.png') no-repeat center center/cover;
            transition: transform 2s, width 2s;
            transform-origin: left;
            position: relative;
            text-align: center;
            display: inline-block;
            color: white;
        }
        .image img {

            width: 200px;
            opacity: 1;
        }

        .image .image-logo-container {
            /* background: white; */
            /* opacity: 0.5; */
            /* background-color: rgba(255, 255, 255, 0.65); */
            padding: 5px 10px;
            border-radius: 5px;
        }

        .image h1 {
            font-size: 22px;
        }
        .image h2 {
            font-size: 16px;
            margin-top: 0;
        }
        .login_div {
            width: 0;
            background: #fff;
            overflow: hidden;
            transition: transform 2s, width 2s;
            transform-origin: right;
            z-index: 2;
        }

        .login_div .content {
            padding-top: 40px;
        }

        .center {
            /* border: 5px solid; */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 10px;
        }

        @media only screen and (max-width: 991px) {
            /* Adjust styles for screens smaller than 991px */
            .container {
                flex-direction: column;
            }
            
            .image, .login_div {
                width: 100vw;
            }

            .login_div {
     
            }

            .image {
                transition: transform 2s, height 2s;
                transform-origin: bottom;
                min-height: 200px;
                overflow: hidden;
            }
        }

        .modal-body {
            padding: 0;
        }

        .modal-body .sidenav_services a {
            display: block;
            padding: 16px 20px;
            line-height: 1.5;
            border-bottom: 1px solid #EFEFEF;
            font-size: 16px;
        }

        .modal-body .sidenav_services a:hover {
            background-color: #FAFAFA;
            text-decoration: none;
        }

        .modal-body .sidenav_services p {
            font-size: 18px;
            letter-spacing: 0;
            line-height: 1.5;
            padding: 16px 20px;
            margin: 0;
            border-bottom: 1px solid #EFEFEF;
            background-color: #FAFAFA;
        }

        .login-footer {
            position: absolute;
            bottom: 0;
        }

        .login_div .content {
            position: relative;
            padding-bottom: 80px;
            height: 95%;
        }

        .modal-right {
            position: fixed;
            right: 0;
            top: 0;
            margin: 0;
            width: 320px;
            transform: translateX(100%); /* Set initial position to the right */
            transition: transform 0.3s ease;
            height: 100%;
        }

        .modal-header .close {
            font-size: 30px; /* Adjust the font size as needed */
        }

        .modal-content {
            height: 100%;
        }

        .modal-right.in {
            transform: translateX(0);
        }

        .modal-right .modal-dialog {
            width: 300px; /* Adjust the width as needed */
            margin: 0;
        }
        
    </style>

</head>
<body class="login">

<!-- Modal -->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-right" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button> -->
                <!-- <h4 class="modal-title" id="myModalLabel">Modal title</h4> -->
                <button type="button" class="btn btn-default" data-dismiss="modal" style="float:right;">Close</button>
            </div>
            <div class="modal-body">
                <!-- Your modal content goes here -->
                <div class="sidenav_services">
                    <p>Paragon AFS Services</p>
                    <a href="https://paragonafs.ca/personal_tax.php">Personal Income Tax <i class="arrow right"></i></a>
                    <a href="https://paragonafs.ca/corporate_tax.php">Corporate Income Tax</a>
                    <a href="https://paragonafs.ca/incorporate.php">Incorporate / Register a Business</a>
                    <a href="https://paragonafs.ca/bookkeeping.php">Accounting / Bookkeeping</a>
                    <a href="https://paragonafs.ca/payroll_salary.php">Payroll & Salary Calculations</a>
                    <a href="https://paragonafs.ca/gst_hst.php">GST/HST Returns</a>
                </div>
            </div>
            <div class="modal-footer">
  
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div>
    </div>
</div>

<!-- BEGIN LOGO -->
<!-- <div class="logo">
    <a href="javascript:;">
       <img src="assets/img/logos/paragon-logo.png" alt="" />
    </a>

    <span id="secure_login">Secure Login ð???</span>
</div> -->
<!-- END LOGO -->

<!-- BEGIN LOGIN -->


<div class="container" style="padding:0;">

    <div class="image">
        <div class="center">
            <div class="image-logo-container">
                <img src="https://paragonafs.ca/auth/assets/img/logos/paragon-logo.png" />
            </div>
            <h1>Secure Sign-In</h1>
            <h2>Paragon AFS</h2>     
            <!-- <img src="https://paragonafs.limneo.com/auth/assets/img/logos/loader_login.gif" alt="">  -->
        </div>
    </div>

    <div class="login_div">
        <!-- Your login form goes here -->
        <div class="content">
            
            <div class="alert_notice" style="background-color: #fff8d7;padding: 16px 16px 16px;">
                <h3 class="form-title" style="margin-top: 0;"><img src="https://paragonafs.ca/auth/assets/img/exclamation-mark.png"  style="width:20px;margin-right:10px;margin-bottom:5px;" />Alert</h3>
                <p class="form-email-login" style="text-align:left;">Please enter a <span style="color:#0075be;"><b>valid email for creating an account</b></span>. If you enter invalid email, you will not receive email to activate your account.</p>
            </div>

            <form class="login-form" method="POST" autocomplete="off" onsubmit="return validateForm(event)">
                <h3 class="form-title" style="margin-bottom: 18px;">Enter your email address to sign in or to create an account</h3>
                <div class="form-container">
                    <div class="form-group register">
                        <input type="email" name="email" placeholder=" " id="inputField" autofocus>
                        <label for="inputField" id="emailLabel">Email Address</label>
                        <span class="form-control-focus inputError" id="inputError"></span>
                    </div>
                </div>
                <button type="submit" id="register-btn">Continue</button>
                <p>
                    <a href="https://paragon-accounting-and-financial-services-inc.square.site/">Book Appointment Online</a>
                </p>
                <hr>
                <p>
                    <a href="#" data-toggle="modal" data-target="#myModal">Other Online Service &nbsp; â?°</a>
                </p>
                

            </form>
            
            <!-- BEGIN LOGIN FORM -->
            <form class="loginForm" method="POST" onsubmit="return login(event)">
                <h3 class="form-title" style="text-align:center;" id="loginForm_formTitle">Welcome Back!</h3>
                <p class="form-email-login">test@email.com</p>
                <span class="useOtherEmail">
                    <a href="./ajax/logout.php">Use a Different Email</a>
                </span>
                <div class="form-container">

                    <input type="hidden" name="loginEmail" id="loginEmail"/>

                    <style>
                        
                    </style>

                    <div class="form-group register">
                        <input type="password" name="loginPassword" placeholder=" " autocomplete="current-password" id="loginPassword" autofocus>
                        <label for="loginPassword" id="loginPasswordLabel">Password</label>
                        <span class="form-control-focus inputError" id="inputLoginPasswordError"></span>
                        <i class="fa fa-eye toggle1" aria-hidden="true"></i>
                    </div>

                    <button type="submit" id="login-user-btn">Sign In</button>

                    <p>
                        <span class="useOtherEmail">
                            <a href="javascript:;" id="forget-password">Forgot Password?</a>
                        </span>
                    </p>
                </div>
            </form>
            <!-- END LOGIN FORM -->

            <!-- BEGIN REGISTRATION FORM -->
            <form class="registerForm" method="POST" onsubmit="return register(event)">

                <h3 class="form-title" style="text-align:center;">Create a Password</h3>
                <p class="form-email-register">test@email.com</p>
                <span class="useOtherEmail">
                    <a href="./ajax/logout.php" id="email-register-form">Use a Different Email</a>
                </span>
                
                <div class="form-container">

                    <input type="hidden" name="emailRegister" id="emailRegister"/>

                    <div class="form-group register">
                        <input type="text" name="firstNameRegister" placeholder=" " id="firstNameRegister" autofocus>
                        <label for="firstNameRegister" id="inputfirstNameLabel">First Name</label>
                        <span class="form-control-focus inputError" id="inputfirstNameRegisterError"></span>
                    </div>

                    <div class="form-group register">
                        <input type="text" name="lastNameRegister" placeholder=" " id="lastNameRegister">
                        <label for="lastNameRegister" id="inputlastNameLabel">Last Name</label>
                        <span class="form-control-focus inputError" id="inputlastNameRegisterError"></span>
                    </div>

                    <div class="form-group register">
                        <input type="text" name="phoneRegister" placeholder=" " id="phoneRegister">
                        <label for="phoneRegister" id="inputPhoneLabel">Phone Number</label>
                        <span class="form-control-focus inputError" id="inputphoneRegisterError"></span>
                    </div>

                    <div class="form-group register">
                        <input type="email" name="verifyEmail" placeholder=" " id="verifyEmail">
                        <label for="verifyEmail" id="inputVerifyEmailLabel">Confirm Email</label>
                        <span class="form-control-focus inputError" id="inputverifyEmailError"></span>
                    </div>

                    <div class="form-group register">
                        <input type="password" name="passwordRegister" placeholder=" " autocomplete="new-password" id="passwordRegister">
                        <label for="passwordRegister" id="createPasswordLabel">Create Password</label>
                        <span class="form-control-focus inputError" id="inputPasswordRegisterError"></span>
                        <i class="fa fa-eye toggle2" aria-hidden="true"></i>
                    </div>

                    <div class="form-group register">
                        <input type="password" name="cpasswordRegister" placeholder=" " autocomplete="new-password" id="cpasswordRegister">
                        <label for="cpasswordRegister" id="confirmPasswordLabel">Confirm Password</label>
                        <span class="form-control-focus inputError" id="inputConfirmPasswordRegisterError"></span>
                        <i class="fa fa-eye toggle3" aria-hidden="true"></i>
                    </div>

                    <div class="form-group register">
                        <label class="control-label visible-ie8 visible-ie9">Recaptcha</label>
                        <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
                        <span class="form-control-focus inputError" id="inputRecaptchaError"></span>
                    </div>
                </div>

                <button type="button" id="register-back-btn" style="display:none;" class="btn dark btn-outline">Back</button>

                <button type="submit" id="register-submit-btn">Create Account</button>

                <p style="font-size: 16px;text-align: center;" class="register-terms">By creating an account, you are agreeing to our privacy policy and terms of use. You also agree to receive exclusive offers via email; you can unsubscribe at any time.</p>
                
                <span class="useOtherEmail" style="text-decoration:none;">
                    <span style="color:#211E22;">Have an account? </span><a href="./ajax/logout.php" style="text-decoration:underline;">Sign in</a>
                </span>
            </form>
            <!-- END REGISTRATION FORM -->

            <!-- BEGIN FORGOT PASSWORD FORM -->
            <form class="forget-form">
                <button type="button" id="back-btn" class="btn dark btn-outline">Back</button>
                <h3 class="form-title" style="text-align:center;">How Would You Like to Sign In?</h3>
                <p class="form-email-forgot">test@email.com</p>
                <span class="useOtherEmail">
                    <a href="./ajax/logout.php">Use a Different Email</a>
                </span>

                <div id="emailForgetSuccess" class="alert alert-success" style="display:none;"></div>

                <input type="hidden" name="emailForget" id="emailForget">

                <a href="javascript:;" id="forgot_user_password" onclick="forget();return false;">Reset your Password</a>
            </form>
            <!-- END FORGOT PASSWORD FORM -->

            <div class="login-footer">
                <hr>
                <!-- <span>Paragon AFS Online Upload Document.</span> -->
                <p style="font-size:16px;margin-top:0;text-align:center;">Paragon Accounting & Financial Service Inc. Â© 2024</p>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function() {
        const mediaQuery = window.matchMedia('(min-width: 992px)');

        function runScriptDesktop() {
            document.body.style.background = '#fff';
            document.querySelector('.login_div').style.width = '50vw';
            document.querySelector('.image').style.width = '50vw';
        }

        function runScriptMobile() {
            document.querySelector('.login_div').style.height = '100%';
            // document.querySelector('.image').style.min-height = '300px';
            // document.querySelector('.login').style.width = '100%';
            document.querySelector('.image').style.width = '100%';
        }

        if (mediaQuery.matches) {
            // If screen width is more than 991px, run the desktop script
            setTimeout(runScriptDesktop, 1000);
        } else {
            // If screen width is 991px or less, run the mobile script
            console.log('Screen width is less than or equal to 991px');
            setTimeout(runScriptMobile, 1000);
        }
    });
</script>

<script>
    const toggle1 = document.querySelector(".toggle1"),
    input1 = document.querySelector("#loginPassword");
    const toggle2 = document.querySelector(".toggle2"),
    input2 = document.querySelector("#passwordRegister");
    const toggle3 = document.querySelector(".toggle3"),
    input3 = document.querySelector("#cpasswordRegister");

    toggle1.addEventListener("click", () => {
        if (input1.type === "password") {
            input1.type = "text";
            toggle1.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input1.type = "password";
            toggle1.classList.replace("fa-eye-slash", "fa-eye");
        }
    });

    toggle2.addEventListener("click", () => {
        if (input2.type === "password") {
            input2.type = "text";
            toggle2.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input2.type = "password";
            toggle2.classList.replace("fa-eye-slash", "fa-eye");
        }
    });

    toggle3.addEventListener("click", () => {
        if (input3.type === "password") {
            input3.type = "text";
            toggle3.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input3.type = "password";
            toggle3.classList.replace("fa-eye-slash", "fa-eye");
        }
    });

</script>

<script src="assets/global/plugins/respond.min.js"></script>
<script src="assets/global/plugins/excanvas.min.js"></script>
<script src="assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->

<script src="assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<!-- END PAGE LEVEL PLUGINS -->

<!-- BEGIN THEME GLOBAL SCRIPTS -->
<script src="assets/global/app.min.js" type="text/javascript"></script>
<script src="assets/plugins/froiden-helper/helper.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->

<script>

    jQuery('#forget-password').click(function() {
        jQuery('.login-form').hide();
        jQuery('.loginForm').hide();

        jQuery('.forget-form').show();
        $('.forget').each(function () {
            $(this).removeClass('hide').addClass('show');
            $('.forget-form').trigger("reset");
            $('#alert').addClass('hide');
        });
    });

    jQuery('#back-btn').click(function() {
        jQuery('.loginForm').show();
        jQuery('.forget-form').hide();
    });

    // Login Function
    function login(event) {
        event.preventDefault(); // Prevent default form submission
        
        const password = document.getElementById('loginPassword');
        const passwordLabel = document.getElementById('loginPasswordLabel');
        const inputError = document.getElementById('inputLoginPasswordError');

        //Show input error messagessss
        function showError(input, message) {
            inputError.innerText = message;
            password.style.borderColor = 'red'; // Set input border color to red
            passwordLabel.style.color = 'red'; // Set label color to red
            inputError.style.display = 'block'; // Show inputError span
        }

        function showSuccess(input) {

            $.easyAjax({
                url: "ajax/login.php",
                type: "POST",
                data: $(".loginForm").serialize(),
                container: ".loginForm",
                messagePosition: "inline"
            });
        }

        //check email is valid
        function checkPassword(input) {
            const re = /^(?=.*\d)(?=.*[a-zA-Z]).{6,}$/;
            if (re.test(input.value.trim())) {
                showSuccess(input);
            } else {
                showError(input, 'Password must contain at least one letter and one number, and be at least 6 characters long.');
            }
        }

        // Reset styles and hide inputError span
        function resetStylesAndError() {
            password.style.borderColor = ''; // Reset input border color
            passwordLabel.style.color = ''; // Reset label color
            inputError.style.display = 'none'; // Hide inputError span
            inputError.innerText = ''; // Clear content of inputError span
        }

        // Clear errors on input
        password.addEventListener('input', function () {
            resetStylesAndError();
        });

        checkPassword(password);
    }

    // Forget Password Function
    function forget() {
        
        $.easyAjax({
            url: "ajax/forget.php",
            type: "POST",
            data: $(".forget-form").serialize(),
            container: ".forget-form",
            messagePosition: "inline",
            success: function (response) {
                console.log(response);
                if (response.status == "success") {
                    jQuery('#forgot_user_password').hide();
                    jQuery('#emailForgetSuccess').show();
                    jQuery('#emailForgetSuccess').text(response.message);
                }
            }
        });
    }

    function register(event) {
        event.preventDefault(); // Prevent default form submission

        // Get the reCAPTCHA element
        const recaptcha = document.querySelector('.g-recaptcha'); 
        const recaptchaContainer = document.querySelector('#rc-anchor-container');
        const recaptchaLabel = document.querySelector('#inputRecaptchaError');

        const emailRegister = document.getElementById('emailRegister');
        const formEmailRegister = document.querySelector('.form-email-register');

        const verifyEmail = document.getElementById('verifyEmail');
        const inputVerifyEmailLabel = document.getElementById('inputVerifyEmailLabel');
        const inputverifyEmailError = document.getElementById('inputverifyEmailError');

        const firstNameRegister = document.getElementById('firstNameRegister');
        const inputfirstNameLabel = document.getElementById('inputfirstNameLabel');
        const inputfirstNameRegisterError = document.getElementById('inputfirstNameRegisterError');

        const lastNameRegister = document.getElementById('lastNameRegister');
        const inputlastNameLabel = document.getElementById('inputlastNameLabel');
        const inputlastNameRegisterError = document.getElementById('inputlastNameRegisterError');

        const phoneRegister = document.getElementById('phoneRegister');
        const inputPhoneLabel = document.getElementById('inputPhoneLabel');
        const inputphoneRegisterError = document.getElementById('inputphoneRegisterError');

        const passwordRegister = document.getElementById('passwordRegister');
        const createPasswordLabel = document.getElementById('createPasswordLabel');
        const inputPasswordRegisterError = document.getElementById('inputPasswordRegisterError');

        const cpasswordRegister = document.getElementById('cpasswordRegister');
        const confirmPasswordLabel = document.getElementById('confirmPasswordLabel');
        const inputConfirmPasswordRegisterError = document.getElementById('inputConfirmPasswordRegisterError');

        //Show input error messages
        function showError(input, inputLabel, inputError, message) {
            inputError.innerText = message;
            input.style.borderColor = 'red'; // Set input border color to red
            inputLabel.style.color = 'red'; // Set label color to red
            inputError.style.display = 'block'; // Show inputError span
        }

        // Check if reCAPTCHA is filled
        function checkRecaptcha(recaptcha, recaptchaContinaer, recaptchaLabel) {
            // grecaptcha.getResponse() will return an empty string if the user has not completed the reCAPTCHA
            if (grecaptcha.getResponse() === '') {
                recaptchaLabel.innerText = 'Recaptcha must be completed.';
                recaptcha.style.borderColor = 'red'; // Set input border color to red
                recaptchaLabel.style.color = 'red';
                recaptchaLabel.style.display = 'block';
                // showError(recaptcha, recaptchaLabel, );
            } else {
                recaptchaLabel.style.display = 'none';
                return true;
            }
        }

        // Check if the fields are valid
        function checkField(input, inputLabel, inputError, fieldName) {
            if (input.value.trim() === '') {
                showError(input, inputLabel, inputError, `${fieldName} cannot be empty.`);
            } else {
                return true;
            }
        }

        // Validate phone number
        function checkPhoneNumber(input, inputLabel, inputError) {
            const phoneNumber = input.value.trim();
            if (phoneNumber === '') {
                showError(input, inputLabel, inputError, 'Phone number cannot be empty.');
            } else if (!/^\d+$/.test(phoneNumber)) {
                showError(input, inputLabel, inputError, 'Phone number must contain only digits.');
            } else {
                return true;
            }
        }

        // Validate password
        function checkPassword(input, inputLabel, inputError) {
            const re = /^(?=.*\d)(?=.*[a-zA-Z]).{6,}$/;
            if (input.value.trim() === '') {
                showError(input, inputLabel, inputError, 'Password cannot be empty.');
            } else if (re.test(input.value.trim())) {
                return true;
            } else {
                showError(input, inputLabel, inputError, 'Password must contain at least one letter and one number, and be at least 6 characters long.');
            }
        }

        // Validate confirm password
        function checkConfirmPassword(password, confirmPassword, inputLabel, inputError) {
            if (password.value.trim() === '' || confirmPassword.value.trim() === '') {
                showError(confirmPassword, inputLabel, inputError, 'Confirm Password cannot be empty.');
            } else if (password.value.trim() !== confirmPassword.value.trim()) {
                showError(confirmPassword, inputLabel, inputError, 'Passwords do not match.');
            } else {
                return true;
            }
        }

        // Validate confirm password
        function checkVerifyEmail(emailRegister, verifyEmail, inputLabel, inputError, emailLabel) {

            var email_register = emailRegister.value.trim().toLowerCase();
            var verify_email = verifyEmail.value.trim().toLowerCase();

            if (verifyEmail.value.trim() === '') {
                showError(verifyEmail, inputLabel, inputError, 'Verify Email cannot be empty.');
            } else if (verify_email !== email_register) {
                showError(verifyEmail, inputLabel, inputError, 'Email does not match. Please Check.');
                emailLabel.style.backgroundColor = '#f9a2a2';
            } else {
                emailLabel.style.backgroundColor = '';
                return true;
            }
        }

        // Reset styles and hide inputError span
        function resetStylesAndError(input, inputLabel, inputError) {
            input.style.borderColor = ''; // Reset input border color
            inputLabel.style.color = ''; // Reset label color
            inputError.style.display = 'none'; // Hide inputError span
            inputError.innerText = ''; // Clear content of inputError span
        }

        // Clear errors on input
        firstNameRegister.addEventListener('input', function() {
            resetStylesAndError(firstNameRegister, inputfirstNameLabel, inputfirstNameRegisterError);
        });
        lastNameRegister.addEventListener('input', function() {
            resetStylesAndError(lastNameRegister, inputlastNameLabel, inputlastNameRegisterError);
        });
        phoneRegister.addEventListener('input', function() {
            resetStylesAndError(phoneRegister, inputPhoneLabel, inputphoneRegisterError);
        });
        verifyEmail.addEventListener('input', function() {
            resetStylesAndError(verifyEmail, inputVerifyEmailLabel, inputverifyEmailError);
        });
        passwordRegister.addEventListener('input', function() {
            resetStylesAndError(passwordRegister, createPasswordLabel, inputPasswordRegisterError);
        });
        cpasswordRegister.addEventListener('input', function() {
            resetStylesAndError(cpasswordRegister, confirmPasswordLabel, inputConfirmPasswordRegisterError);
        });

        // Check if all fields are valid
        const isFirstNameValid = checkField(firstNameRegister, inputfirstNameLabel, inputfirstNameRegisterError, 'First Name');
        const isLastNameValid = checkField(lastNameRegister, inputlastNameLabel, inputlastNameRegisterError, 'Last Name');
        const isPhoneValid = checkPhoneNumber(phoneRegister, inputPhoneLabel, inputphoneRegisterError, 'Phone Number');
        const isPasswordValid = checkPassword(passwordRegister, createPasswordLabel, inputPasswordRegisterError);
        const isConfirmPasswordValid = checkConfirmPassword(passwordRegister, cpasswordRegister, confirmPasswordLabel, inputConfirmPasswordRegisterError);
        const isVerifyEmailValid = checkVerifyEmail(emailRegister, verifyEmail, inputVerifyEmailLabel, inputverifyEmailError, formEmailRegister);
        const isRecaptchaValid = checkRecaptcha(recaptcha, recaptchaContainer, recaptchaLabel);

        // If all fields are valid, submit the form
        if (isFirstNameValid && isLastNameValid && isPhoneValid && isPasswordValid && isConfirmPasswordValid && isVerifyEmailValid) {
            // Submit the form
            $.easyAjax({
                url: "ajax/register.php",
                type: "POST",
                data: $(".registerForm").serialize(),
                container: ".registerForm",
                messagePosition: "inline",
                success: function (response) {
                    console.log(response);
                    if (response.status == "success") {
                        // Hide registration form and show success message
                        $('.register').each(function () {
                            $(this).removeClass('show').addClass('hide');
                        });
                        $('.register-terms').removeClass('show').addClass('hide');
                        $('#register-submit-btn').removeClass('show').addClass('hide');
                        $('#email-register-form').removeClass('show').addClass('hide');
                    }
                }
            });
        }
    }

    function validateForm(event) {
        event.preventDefault(); // Prevent default form submission
        
        const email = document.getElementById('inputField');
        const emailLabel = document.getElementById('emailLabel');
        const inputError = document.getElementById('inputError');

        //Show input error messagessss
        function showError(input, message) {
            inputError.innerText = message;
            email.style.borderColor = 'red'; // Set input border color to red
            emailLabel.style.color = 'red'; // Set label color to red
            inputError.style.display = 'block'; // Show inputError span
        }

        function showSuccess(input) {
            // resetStylesAndError();

            $.easyAjax({
                url: "ajax/check_email.php",
                type: "POST",
                data: $(".login-form").serialize(),
                success: function (response) {
                    if (response.status == "success") {
                        console.log(response);
                        $('.loginForm .form-email-login').text(response.email);
                        $('.loginForm #loginEmail').val(response.email);
                        $('.loginForm #loginForm_formTitle').text("Welcome Back " + response.first_name + "!");

                        $('.forget-form .form-email-forgot').text(response.email);
                        $('.forget-form #emailForget').val(response.email);

                        jQuery('.login-form').hide();
                        jQuery('.registerForm').hide();
                        jQuery('.loginForm').show();

                        document.getElementById('firstNameRegister').focus();

                    } else if (response.status == "noemail") {
                        console.log(response);
                        $('.registerForm .form-email-register').text(response.email);
                        $('.registerForm #emailRegister').val(response.email);

                        jQuery('.login-form').hide();
                        jQuery('.loginForm').hide();
                        jQuery('.registerForm').show();
                    }
                }
            });
        }

        //check email is valid
        function checkEmail(input) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            if (re.test(input.value.trim())) {
                showSuccess(input)
            } else {
                showError(input, 'Invalid email address. Please try again.');
            }
        }

        // Reset styles and hide inputError span
        function resetStylesAndError() {
            email.style.borderColor = ''; // Reset input border color
            emailLabel.style.color = ''; // Reset label color
            inputError.style.display = 'none'; // Hide inputError span
            inputError.innerText = ''; // Clear content of inputError span
        }

        // Clear errors on input
        email.addEventListener('input', function () {
            resetStylesAndError();
        });

        checkEmail(email);
    };

    $(document).on('click', '#resendVerification', function() {
        // e.preventDefault();
        var key = $(this).data('key');
        // Now you can use this key to perform further actions, such as making an AJAX request
        // You can also pass this key to your AJAX request as a parameter
        $.ajax({
            url: 'ajax/resend_verification.php',
            type: 'POST',
            data: { key: key },
            success: function(response) {
                // Handle success response
                $('#alert').html('<div class="alert alert-success">Email verification has been resent to your email address. Please verify your email to get Registered</div>');
                console.log(response);
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(error);
            }
        });
    });

</script>
</body>
</html>