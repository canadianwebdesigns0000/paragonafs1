<?php
session_start();

// error_reporting(0);
$msg = "";

if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

$form_email_verified = isset($_POST['form_email_verified']) ? $_POST['form_email_verified'] : '';
$form_file_submit = isset($_POST['form_file_submit']) ? $_POST['form_file_submit'] : '';
?>


<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Paragon AFS | Admin Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="Paragon AFS" name="author" />
    <!-- App favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/paragon_logo_icon.png" />

    <!-- Sweet Alert css-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger shadow-none" id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>
                    </div>

                    <div class="d-flex align-items-center">

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle shadow-none" data-toggle="fullscreen">
                                <i class='bx bx-fullscreen fs-22'></i>
                            </button>
                        </div>

                        <div class="ms-1 header-item d-none d-sm-flex">
                            <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode shadow-none">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>

                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn shadow-none" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-8.jpg" alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">Paragon AFS</span>
                                        <span class="d-none d-xl-block ms-1 fs-12 user-name-sub-text">Admin</span>
                                    </span>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <h6 class="dropdown-header">Welcome Admin!</h6>
                                <a class="dropdown-item" href="auth-logout-basic.html"><i class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span class="align-middle" data-key="t-logout">Logout</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="index.html" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="assets/images/paragon_logo_icon.png" alt="" height="40">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/paragon_logo.png" alt="" height="50">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="index.html" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="assets/images/paragon_logo_icon.png" alt="" height="30">
                    </span>
                    <span class="logo-lg">
                        <img src="assets/images/paragon_logo.png" alt="" height="50">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>

            <div id="scrollbar">
                <div class="container-fluid">

                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">

                        <li class="menu-title"><span data-key="t-menu">Menu</span></li>

                        <li class="nav-item">
                            <a class="nav-link menu-link" href="/auth/admin/dashboard.php">
                                <i class="mdi mdi-speedometer"></i> <span data-key="t-dashboard">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link menu-link active" href="/auth/admin/clients.php">
                                <i class="mdi mdi-account-circle-outline"></i> <span data-key="t-clients">Clients</span>
                            </a>
                        </li>

                    </ul>
                </div>
                <!-- Sidebar -->
            </div>


            <div class="sidebar-background"></div>
        </div>
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
                                <h4 class="mb-sm-0">Clients List</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Clients</a></li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card" id="invoiceList">
                                <div class="card-header border-0">
                                    <div class="d-flex align-items-center">
                                        <h5 class="card-title mb-0 flex-grow-1">Clients Information</h5>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <p id="refreshTimer"></p>
                                            	<!-- <button class="btn btn-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                                            	<a href="apps-invoices-create.html" class="btn btn-success"><i class="ri-add-line align-bottom me-1"></i> Create Invoice</a> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                                    <form>
                                        <div class="row g-4">
                                            
                                            <!-- <div class="col-xxl-1 col-sm-1">
                                                <select name="user_table_length" aria-controls="example" class="form-select">
                                                    <option value="10" selected>10</option>
                                                    <option value="25">25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select> 
                                            </div> -->

                                            <div class="col-xxl-4 col-sm-11">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search bg-light border-light" id="search" placeholder="Search">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
                                            
                                            <!--end col-->
                                            <div class="col-xxl-2 col-sm-4">    
                                                <div class="input-light">
                                                    <select class="form-control" name="if_user_email_verified" id="if_user_email_verified">
                                                        <option value="">Email Verified</option>
                                                        <option value="yes">Yes</option>
                                                        <option value="no" <?php echo ($form_email_verified === 'no') ? 'selected' : ''; ?>>No</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!--end col-->
                                            <div class="col-xxl-2 col-sm-4">
                                                <div class="input-light">
                                                    <select class="form-control" name="if_user_file_submitted" id="if_user_file_submitted">
                                                        <option value="">File Submitted</option>
                                                        <option value="Yes" <?php echo ($form_file_submit === 'Yes') ? 'selected' : ''; ?>>Yes</option>
                                                        <option value="No" <?php echo ($form_file_submit === 'No') ? 'selected' : ''; ?>>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <!--end col-->

                                            <!-- <div class="col-xxl-2 col-sm-4">
                                                <div class="input-light">
                                                    <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="idStatus">
                                                        <option value="">Status</option>
                                                        <option value="all" selected>All</option>
                                                        <option value="Unpaid">Unpaid</option>
                                                        <option value="Paid">Paid</option>
                                                        <option value="Cancel">Cancel</option>
                                                        <option value="Refund">Refund</option>
                                                    </select>
                                                </div>
                                            </div> -->
                                            <!--end col-->
                                            <div class="col-xxl-2 col-sm-4"></div>
                                        
                                            <div class="col-xxl-2 col-sm-4 text-end">
                                                <button type="button" class="btn btn-success" id="users_table_refresh">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary" id="users_table_clear">
                                                    Clear
                                                </button>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                                <div class="card-body">
                                    <div id="taxInformationContainer">

                                        <!-- User Tax Information -->

                                    </div>

                                    <!-- Modal -->
                                    <div class="modal fade flip" id="deleteOrder" tabindex="-1" aria-labelledby="deleteOrderLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-body p-5 text-center">
                                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#405189,secondary:#f06548" style="width:90px;height:90px">
                                                    </lord-icon>
                                                    <div class="mt-4 text-center">
                                                        <h4>You are about to delete a order ?</h4>
                                                        <p class="text-muted fs-15 mb-4">Deleting your order will remove all of your information from our database.</p>
                                                        <div class="hstack gap-2 justify-content-center remove">
                                                            <button class="btn btn-link link-success fw-medium text-decoration-none" id="deleteRecord-close" data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</button>
                                                            <button class="btn btn-danger" id="delete-record">Yes, Delete It</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end modal -->
                                </div>
                            </div>

                        </div>
                        <!--end col-->
                    </div>
                    <!--end row-->

                </div><!-- container-fluid -->
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
            // Set the initial countdown time to 5 minutes
            let countdown = 5 * 60; // 5 minutes in seconds

            // Function to update the timer
            function updateTimer() {
                const minutes = Math.floor(countdown / 60);
                const seconds = countdown % 60;
                const formattedTime = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
                document.getElementById('refreshTimer').innerHTML = `Next refresh in: ${formattedTime}`;
                
                // If countdown reaches 0, refresh the page and reset the timer
                if (countdown === 0) {
                    location.reload();
                    countdown = 5 * 60; // Reset the countdown to 5 minutes
                } else {
                    countdown--; // Decrease the countdown by 1 second
                }
            }

            // Initial call to update the timer
            updateTimer();

            // Set interval to update the timer every second
            setInterval(updateTimer, 1000);         
        });
    </script>

    <script>
        $(document).ready(function () {
    
            var currentPage = 1; // Track the current page
            var searchQuery = '';
            var selectedIfEmailVerified = <?php echo json_encode($form_email_verified); ?>;
            var selectedIfFileSubmitted = <?php echo json_encode($form_file_submit); ?>;

            // Function to load real estate listings with pagination and search term
            function loadUserInformation(page, search, emailVerified, fileSubmitted) {
                // $('.noresult').show();
                $.ajax({
                    url: 'ajax/view_tax_information.php',
                    method: 'POST',
                    data: {
                        page: page,
                        search: search,
                        emailVerified: emailVerified,
                        fileSubmitted: fileSubmitted,
                    },
                    success: function (response) {
                        // console.log(response);
                        if (response.trim() === '') {
                            $('#taxInformationContainer').html('');
                            $('.noresult').show();
                        } else {
                            $('#taxInformationContainer').html(response);
                            $('.noresult').hide();
                        }
                    }
                });
            }

            // Pagination click handler
            $(document).on('click', '.pagination .page-link', function (e) {
                e.preventDefault();
                var targetPage = parseInt($(this).data('page'));
                if (!isNaN(targetPage)) {
                    currentPage = targetPage;
                    loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted);
                }
            });

            // Search input change handlers
            $('#search').on('input', function () {
                searchQuery = $(this).val();
                currentPage = 1;
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted);
            });

            // Filter change handlers
            $('#if_user_email_verified').change(function () {
                selectedIfEmailVerified = $(this).val();
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted);
            });

            // Filter change handlers
            $('#if_user_file_submitted').change(function () {
                selectedIfFileSubmitted = $(this).val();
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted);
            });

            loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted);

            const emailVerifiedChoices = new Choices($('#if_user_email_verified')[0], { searchEnabled: false });
            const fileSubmittedChoices = new Choices($('#if_user_file_submitted')[0], { searchEnabled: false });

            $('#users_table_clear').on('click', function() {
                currentPage = 1;
                searchQuery = '';
                selectedIfEmailVerified = '';
                selectedIfFileSubmitted = '';

                $('#search').val('');
                emailVerifiedChoices.setChoiceByValue('');
                fileSubmittedChoices.setChoiceByValue('');
                
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted);
            });
            $('#users_table_refresh').on('click', function() {
                location.reload();
            });
        });
    </script>
</body>
</html>