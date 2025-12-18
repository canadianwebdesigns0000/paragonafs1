<?php
    session_start();
    include '../config.php';
    error_reporting(0);
    $msg = "";

    // SESSION CHECK SET OR NOT
    if (!isset($_SESSION['admin'])) {
        header('location:index.php');
    }

    $logFile = '/home/paragonafs/logs/php_log';
    $linesPerPage = 100; // Change this to the number of lines you want per page

    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $currentPage = $_GET['page'];
    } else {
        $currentPage = 1;
    }
?>
<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Paragon AFS | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="Paragon AFS" name="author" />

    <!-- Sweet Alert css-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <?php include '../inc/css/global.php' ?>
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php include '../inc/header.php'; ?>

        <!-- ========== App Menu ========== -->
        <?php include '../inc/sidebar.php'; ?>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0">Logs</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0"> 
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Menu</a></li>
                                        <li class="breadcrumb-item active">Logs</li>
                                    </ol>
                                </div>

                            </div>
                        </div>  
                    </div>
                    <!-- end page title -->

                   <?php 
                   
                   if (file_exists($logFile)) {
                        $lines = file($logFile);
                        $lines = array_reverse($lines); // Reverse the array to display from latest to oldest

                        // Determine the start and end of the slice that represents the current page
                        $start = ($currentPage - 1) * $linesPerPage;
                        $end = min($start + $linesPerPage, count($lines));

                        // Get the lines for the current page and display them
                        $linesForCurrentPage = array_slice($lines, $start, $linesPerPage);
                        foreach ($linesForCurrentPage as $line) {
                            echo nl2br($line); // nl2br() is used to maintain line breaks in the output
                        }
                    } else {
                        echo "The log file does not exist.";
                    }
            
                   ?>

                   <?php
                        $totalLines = count($lines);
                        $totalPages = ceil($totalLines / $linesPerPage);

                        echo "Page: ";
                        for ($i = 1; $i <= $totalPages; $i++) {
                            if ($i == $currentPage) {
                                echo $i . ' ';
                            } else {
                                echo '<a href="?page=' . $i . '">' . $i . '</a> ';
                            }
                        }
                    ?>

                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © Paragon Accounting and Financial Services.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->


    <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" style="bottom: 65px;" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!--preloader-->
    <div id="preloader">
        <div id="status">
            <div class="spinner-border text-primary avatar-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>


    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>
    <!-- list.js min js -->
    <script src="assets/libs/list.js/list.min.js"></script>
    <!--list pagination js-->
    <script src="assets/libs/list.pagination.js/list.pagination.min.js"></script>
    <!-- invoicelist init js -->
    
    <!-- <script src="assets/js/pages/invoiceslist.init.js"></script> -->
    <!-- Sweet Alerts js -->
    <script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>
    <!-- App js -->
    <script src="assets/js/app.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
    
        });


    </script>
</body>
</html>