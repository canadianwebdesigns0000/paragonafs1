<?php
    session_start();
    include 'config.php';

    $forgetKey = get('key');
    // Get value from database where key matches
    $statementMatch = $db->prepare('SELECT * FROM `users` where `forget_key`=?');
    $statementMatch->execute(array($forgetKey));

if ($statementMatch->rowCount() > 0) {

    $output['status']  = 'success';
    $output['msgtype'] = 'success';
} else {
    $output['status']  = 'error';
    $output['msgtype'] = 'danger';
    $output['msg']     = "Key is expired.Click <a href='index.php'> here</a> to get Login";
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

    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <!---CSS FILES -->

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="assets/css/login.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/custom.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL STYLES -->
    <link href="assets/css/components-md.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="assets/css/plugins-md.css" rel="stylesheet" type="text/css" />
    <!-- END THEME GLOBAL STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->

    <link href="assets/plugins/froiden-helper/helper.css" rel="stylesheet" type="text/css" />

</head>

<body class="login">

<!-- BEGIN LOGIN -->
<div class="content">
    <!-- BEGIN LOGIN FORM -->

    <!-- BEGIN LOGO -->
    <div class="logo" style="text-align: center;">
        <a href="javascript:;">
            <img src="assets/img/logos/paragon-logo.png" alt="" />
        </a>
    </div>
    <!-- END LOGO -->

    <form class="login-form" autocomplete="off" method="POST">
        <h3 class="form-title font-green">Reset your Password</h3>
        <div class="form-group form-md-line-input">
            <input class="form-control form-control-solid placeholder-no-fix" placeholder="Password" type="password" name="password" id="password" />
            <input type="hidden" name="key" id="key" value="<?php echo $forgetKey; ?>" />
            <label class="control-label visible-ie8 visible-ie9">Password</label>
            <span class="form-control-focus"> </span>
        </div>
        <div class="form-group form-md-line-input">
            <input class="form-control form-control-solid placeholder-no-fix" type="password" placeholder="Confirm password" name="cpassword" id="cpassword" />
            <label class="control-label visible-ie8 visible-ie9">Confirm password</label>
            <span class="form-control-focus"> </span>
        </div>
        <div class="form-actions" style="text-align: right;">
            <button type="submit" class="btn green uppercase" onclick="changePassword();return false;">Submit</button>
        </div>
    </form>

    <!-- JS FILES    -->
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
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="assets/global/app.min.js" type="text/javascript"></script>
    <script src="assets/plugins/froiden-helper/helper.js" type="text/javascript"></script>
    <!-- END THEME GLOBAL SCRIPTS -->

    <script>
        function changePassword() {
            $.easyAjax({
                url: "ajax/reset_password.php",
                type: "POST",
                data: $(".login-form").serialize(),
                container: ".login-form",
                messagePosition: "inline",
                success: function (response) {
                console.log(response);
                if (response.status == "success") {
                    jQuery('.form-md-line-input').hide();
                    jQuery('.form-title').hide();
                    jQuery('.form-actions').hide();
                }
            }
            });
        }

    </script>
</body>
</html>