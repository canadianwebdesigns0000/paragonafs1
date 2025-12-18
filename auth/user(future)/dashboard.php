<?php
    session_start();
    include '../config.php';

    // SESSION CHECK SET OR NOT
if (!isset($_SESSION['email'])) {
    header('location:../index.php');
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
    <title>Dashboard - User</title>
    <?php include '../include/css/mandatory.php' ?>
    <!-- BEGIN PAGE LEVEL PLUGINS --><!-- END PAGE LEVEL PLUGINS -->
    <?php include '../include/css/global.php' ?>
</head>

<body class="page-container-bg-solid page-header-fixed page-sidebar-closed-hide-logo page-md">
<?php include '../include/header.php'; ?>
<div class="page-container">
    <?php include '../include/sidebar.php'; ?>
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- BEGIN PAGE BASE CONTENT -->
            <div class="page-head margin-bottom-5">
                <!-- BEGIN PAGE TITLE -->
                <div class="page-title">
                    <h1>Dashboard
                        <small>activities &amp; statistics</small>
                    </h1>
                </div>
                <!-- END PAGE TITLE -->
            </div>
            <div class="portlet light bordered">
                <div class="portlet-body">
                    <div class="note note-success">
                            <p> Welcome  <strong><?php echo $_SESSION['name']; ?></strong> to the Dashboard</p>
                     </div>
                </div>

            </div>
        </div>
    </div>

</div>
<!--End page-container-->
<?php include '../include/footer.php' ?>
<?php include '../include/footerjs.php' ?>

<!-- Start of plugins-->

<!-- End of plugins-->

</body>
</html>