<?php
    session_start();

    include '../config.php';

// if Logged in redirect to dashboard
if (isset($_SESSION['name'])) {
    if(isset($_SESSION['table']) && $_SESSION['table']== 'admin'){
        header('location:dashboard.php');
    }else{
        header('location:../user/dashboard.php');
    } 
    exit();
}

?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>

    <title>Admin Panel | Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!---CSS FILES -->

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="../assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="../assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="../assets/css/login.css" rel="stylesheet" type="text/css" />
    <link href="../assets/css/custom.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="../assets/css/components-md.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="../assets/css/plugins-md.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->

    <link href="../assets/plugins/froiden-helper/helper.css" rel="stylesheet" type="text/css" />

</head>

<body class="login">

<!-- BEGIN LOGO -->
<div class="logo">
    <a href="javascript:;">
<!--        <img src="../assets/img/logos/logo-big.png" alt="" /> -->
        Admin Panel
    </a>
</div>
<!-- END LOGO -->
<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->

<form class="login-form" autocomplete="off" method="POST">
    <h3 class="form-title font-green">Sign In</h3>
    <div id="error"></div>
    <div class="form-group form-md-line-input">
        <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
        <label class="control-label visible-ie8 visible-ie9">Username</label>
        <input class="form-control form-control-solid placeholder-no-fix" type="text"  placeholder="Username" name="username" id="username" autocomplete="new-username" />
        <div class="form-control-focus"> </div>
    </div>
    <div class="form-group form-md-line-input">
        <label class="control-label visible-ie8 visible-ie9">Password</label>
        <input class="form-control form-control-solid placeholder-no-fix" type="password"  placeholder="Password" name="password" id="password" autocomplete="new-password" />
        <div class="form-control-focus"> </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn green uppercase btn-xs btn-outline" onclick="login();return false;">Login</button>
    </div>
</form>

<!-- JS FILES    -->
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script>
<script src="../assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
    <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->

    <script src="../assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="../assets/global/app.min.js" type="text/javascript"></script>
    <script src="../assets/plugins/froiden-helper/helper.js" type="text/javascript"></script>
<!-- END THEME GLOBAL SCRIPTS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<!-- END PAGE LEVEL SCRIPTS -->
<!-- BEGIN THEME LAYOUT SCRIPTS -->
<!-- END THEME LAYOUT SCRIPTS -->
<!-- END OF JS FILES    -->

<script>
    function login() {
        $.easyAjax({
            url: "ajax/login.php",
            type: "POST",
            data: $(".login-form").serialize(),
            container: ".login-form",
            messagePosition: "inline"
        });
    }

</script>
</body>
</html>