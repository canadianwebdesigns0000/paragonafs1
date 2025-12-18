<?php
    include 'config.php';

    $key = get('key');

    // Get value from database where key matches
    $statementMatch = $db->prepare('SELECT * FROM `users` where `key`=?');
    $statementMatch->execute(array($key));

if ($statementMatch->rowCount() > 0) {
    // Update database
    $statementMatch = $db->prepare('UPDATE  `users` SET `key`= ?,email_verified =? where `key`=?');
    $statementMatch->execute(array('', 'yes', $key));

    $output['status'] = 'success';
    $output['msg']    = 'Congratulation! You have verified your email id. Click <a href="index.php"> here </a> to go to Login page';

} else {

    $output['status'] = 'danger';
    $output['msg']    = 'Sorry the key has expired.';
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

    <title>Confirm Email</title>
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

<div class="content">

    <!-- BEGIN LOGO -->
    <div class="logo" style="text-align: center;">
        <a href="javascript:;">
        <img src="assets/img/logos/paragon-logo.png" alt="" />
        </a>
    </div>
    <!-- END LOGO -->

    <h3 class="form-title font-green">Success</h3>
    <br>
    <div id="msg" class="<?php echo $output['status'] == 'success' ? 'alert alert-success' : 'alert alert-danger'; ?>"><?php echo $output['msg'];?></div>
    <a href="index.php" class="btn green btn-outline">Click here to login </a>

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

</body>
</html>