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

    <!-- Sweet Alert css-->
    <link href="assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

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
                                                <!-- <p id="refreshTimer"></p> -->
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
                                            <!-- <div class="col-xxl-1 col-sm-4">
                                                <div class="input-light">
                                                    <select class="form-control" name="user_tax_view_count" id="user_tax_view_count">
                                                        <option value="10">10</option>
                                                        <option value="25">25</option>
                                                        <option value="50">50</option>
                                                    </select>
                                                </div>
                                            </div> -->
                                        
                                            <div class="col-xxl-4 col-sm-4 text-end">
                                                <button type="button" id="user_tax_info_count" data-bs-toggle="dropdown" aria-expanded="false" class="btn btn-soft-info"><i class="ri-more-2-fill"></i></button>
                                                <ul class="dropdown-menu" aria-labelledby="user_tax_info_count" style="min-width: 0;">
                                                    <li><a class="dropdown-item" href="#">10</a></li>
                                                    <li><a class="dropdown-item" href="#">25</a></li>
                                                    <li><a class="dropdown-item" href="#">50</a></li>
                                                    <li><a class="dropdown-item" href="#">100</a></li>
                                                    <li><a class="dropdown-item" href="#">250</a></li>
                                                    <li><a class="dropdown-item" href="#">500</a></li>
                                                    <li><a class="dropdown-item" href="#">1000</a></li>
                                                    <li><a class="dropdown-item" href="#">2500</a></li>
                                                    <li><a class="dropdown-item" href="#">4000</a></li>
                                                </ul>
                                                <button type="button" class="btn btn-soft-success" id="users_table_refresh">
                                                    <i class="ri-refresh-line"></i>
                                                </button>
                                                <button type="button" class="btn btn-soft-primary" id="users_table_clear">
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
    <!-- <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script> -->
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

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <script>
        $(document).ready(function () {
            
            // // Set the initial countdown time to 5 minutes 
            // let countdown = 5 * 60; // 5 minutes in seconds

            // // Function to update the timer
            // function updateTimer() {
            //     const minutes = Math.floor(countdown / 60);
            //     const seconds = countdown % 60;
            //     const formattedTime = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            //     document.getElementById('refreshTimer').innerHTML = `Next refresh in: ${formattedTime}`;
                
            //     // If countdown reaches 0, refresh the page and reset the timer
            //     if (countdown === 0) {
            //         location.reload();
            //         countdown = 5 * 60; // Reset the countdown to 5 minutes
            //     } else {
            //         countdown--; // Decrease the countdown by 1 second
            //     }
            // }

            // // Initial call to update the timer
            // updateTimer();

            // // Set interval to update the timer every second
            // setInterval(updateTimer, 1000);
            
        });
    </script>

    <script>
        $(document).ready(function () {
    
            var currentPage = 1; // Track the current page
            var user_tax_count = 10;
            var searchQuery = '';
            var selectedIfEmailVerified = <?php echo json_encode($form_email_verified); ?>;
            var selectedIfFileSubmitted = <?php echo json_encode($form_file_submit); ?>;

            // Function to load real estate listings with pagination and search term
            function loadUserInformation(page, search, emailVerified, fileSubmitted, userTaxCount) {
                // $('.noresult').show();
                $.ajax({
                    url: 'ajax/view_tax_information2.php',
                    method: 'POST',
                    data: {
                        page: page,
                        search: search,
                        emailVerified: emailVerified,
                        fileSubmitted: fileSubmitted,
                        userTaxCount: userTaxCount,
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
                    loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);
                }
            });

            // Search input change handlers
            $('#search').on('input', function () {
                searchQuery = $(this).val();
                currentPage = 1;
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);
            });

            // Filter change handlers
            $('#if_user_email_verified').change(function () {
                selectedIfEmailVerified = $(this).val();
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);
            });

            // Filter change handlers
            $('#if_user_file_submitted').change(function () {
                selectedIfFileSubmitted = $(this).val();
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);
            });

            loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);

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
                
                loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);
            });

            $('#users_table_refresh').on('click', function() {
                location.reload();
            });

            // Get all dropdown items
            var dropdownItems = document.querySelectorAll('.dropdown-item');

            // Add click event listener to each dropdown item
            dropdownItems.forEach(function(item) {
                item.addEventListener('click', function() {
                    // Get the value of the clicked item
                    user_tax_count = item.textContent;
                    loadUserInformation(currentPage, searchQuery, selectedIfEmailVerified, selectedIfFileSubmitted, user_tax_count);
                    // You can use this value for further processing
                });
            });
        });
    </script>
</body>
</html>