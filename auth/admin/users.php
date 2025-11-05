<?php
    session_start();
    include '../config.php';
    error_reporting(0);
    $msg = "";

    // SESSION CHECK SET OR NOT
    if (!isset($_SESSION['admin'])) {
        header('location:index.php');
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
                                <h4 class="mb-sm-0">Users List</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item active"><a href="javascript: void(0);">Users</a></li>
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
                                        <h5 class="card-title mb-0 flex-grow-1">Users Information</h5>
                                        <div class="flex-shrink-0">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <!-- <p id="refreshTimer"></p> -->
                                            	<!-- <button class="btn btn-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button> -->
                                                <?php if ($_SESSION['is_superadmin'] === 'yes') : ?>
                                                <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i>Add User Admin</button>
                                                <?php endif; ?>
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

                                            <div class="col-xxl-4 col-sm-4">
                                                <div class="search-box">
                                                    <input type="text" class="form-control search bg-light border-light" id="search" placeholder="Search">
                                                    <i class="ri-search-line search-icon"></i>
                                                </div>
                                            </div>
     
                                            <div class="col-xxl-8 col-sm-4 text-end">

                                                <button type="button" class="btn btn-soft-success" id="users_table_refresh">
                                                    <i class="ri-refresh-line"></i>
                                                </button>

                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                                <div class="card-body">

                                    <div id="userAdminInformationContainer">

                                        <!-- User User Admin Information -->

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

    <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header bg-light p-3">
                    <h5 class="modal-title" id="exampleModalLabel">Add Admin User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="close-modal"></button>
                </div>

                <form class="registerForm" method="POST" onsubmit="return register(event)" style="padding: 16px;">
                    <div class="row g-3">
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="usernamefloatingInput" name="admin_username" placeholder="Enter your username" required>
                                <label for="usernamefloatingInput">Username</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <select class="form-select" id="floatingSelect" name="admin_is_superadmin" aria-label="Floating label select example" required>
                                <option value="yes">Yes</option>
                                <option value="no" selected>No</option>
                                </select>
                                <label for="floatingSelect">Super Admin?</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="firstnamefloatingInput" name="admin_first_name" placeholder="Enter your firstname" required>
                                <label for="firstnamefloatingInput">First Name</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="lastnamefloatingInput" name="admin_last_name" placeholder="Enter your Lastname" required>
                                <label for="lastnamefloatingInput">Last Name</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="phonefloatingInput" name="admin_phone" placeholder="Enter your phone" required>
                                <label for="phonefloatingInput">Phone</label>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="emailfloatingInput" name="admin_email" placeholder="Enter your email" required>
                                <label for="emailfloatingInput">Email Address</label>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="passwordfloatingInput" name="admin_password" placeholder="Enter your password" required>
                                <label for="passwordfloatingInput">Password</label>
                            </div>
                        </div>
                        <!-- <div class="col-lg-6">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="passwordfloatingInput1" name="admin_cpassword" placeholder="Confirm password" required>
                                <label for="passwordfloatingInput1">Confirm Password</label>
                            </div>
                        </div> -->

                        <div class="col-lg-12">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>

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
    
            var searchQuery = '';

            // Function to load real estate listings with pagination and search term
            function loadUserInformation(search) {
                // $('.noresult').show();
                $.ajax({
                    url: 'ajax/view_user_admin.php',
                    method: 'POST',
                    data: {
                        search: search,
                    },
                    success: function (response) {
                        // console.log(response);
                        if (response.trim() === '') {
                            $('#userAdminInformationContainer').html('');
                            // $('.noresult').show();
                        } else {
                            $('#userAdminInformationContainer').html(response);
                            // $('.noresult').hide();
                        }
                    }
                });
            }

            // Search input change handlers
            $('#search').on('input', function () {
                searchQuery = $(this).val();
                loadUserInformation(searchQuery);
            });

            loadUserInformation(searchQuery);

            $('#users_table_refresh').on('click', function() {
                location.reload();
            });
        });


        function register(event) {
            event.preventDefault(); // Prevent default form submission

            // Submit the form
            $.ajax({
                url: "ajax/add_admin.php",
                type: "POST",
                data: $(".registerForm").serialize(),
                container: ".registerForm",
                messagePosition: "inline",
                success: function (response) {
                    location.reload();
                }
            });
        }
    </script>
</body>
</html>