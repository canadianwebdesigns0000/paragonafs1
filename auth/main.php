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

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Sign In | Paragon AFS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- App favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/paragon_logo_icon.png" />

    <!-- Layout config Js -->
    <script src="admin/assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="admin/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="admin/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="admin/assets/css/app.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="admin/assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden">
                            <div class="row g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="/" class="d-block text-center">
                                                    <img src="https://paragonafs.ca/auth/assets/img/logos/paragon-logo.png" alt="" height="50">
                                                </a>
                                            </div>

                                            <div>

                                                <!-- Headings -->
                                                <h3 class="text-warning text-center">Welcome to Paragon Accounting</h3>

                                                <img src="https://paragonafs.ca/auth/assets/img/tax-image.png" alt="Tax Image" class="w-100">
                                                
                                               

                                                <!-- 
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-info"></i>
                                                </div> -->

                                                <!-- <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Lorem Ipsum Dolor</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Lorem Ipsum Dolor"</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Lorem Ipsum Dolor</p>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- end carousel -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">

                                        <!-- Warning Alert -->
                                        <div class="alert alert-warning alert-dismissible alert-additional shadow fade show" role="alert">
                                            <div class="alert-body">
                                                <div class="d-flex">
                                                    <div class="flex-shrink-0 me-3">
                                                        <i class="ri-alert-line fs-16 align-middle"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h5 class="alert-heading">Alert</h5>
                                                        <p class="mb-0">Please enter a <span style="color:#0075be;"><b>valid email for creating an account</b></span>. If you enter invalid email, you will not receive email to activate your account.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="mt-4">
                                            <h5 class="text-dark fw-bold text-center">Enter your Email to Sign In or to Create an Account</h5>
                                            <p class="text-muted text-center">Sign in to continue to Paragon AFS.</p>
                                        </div>

                                        <div class="mt-4">

                                            <form class="login-form needs-validation" method="POST" autocomplete="off" onsubmit="return validateForm(event)" novalidate>

                                                <div class="mb-3">
                                                    <label for="useremail" class="form-label">Email Address <span class="text-danger">*</span></label>
                                                    <div class="input-group has-validation">
                                                        <span style="border:none;" class="border-start border-3 border-success rounded-0 input-group-text" id="inputEmailPrepend"><i class="ri-mail-line"></i></span>
                                                        <input type="email" class="form-control" id="useremail" aria-describedby="inputEmailPrepend" required>
                                                        <div class="invalid-feedback">
                                                            Invalid Email Address. Please Try Again.
                                                        </div>
                                                        <div class="valid-feedback">
                                                            Looks Good!
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-success btn-border w-100" type="submit">Continue</button>
                                                </div>

                                                <div class="mt-4 text-center">
                                                    <div class="signin-other-title">
                                                        <h5 class="fs-13 mb-4 title">Other Services</h5>
                                                    </div>

                                                    <div>

                                                        <!-- <button type="button" class="btn btn-danger btn-sm waves-effect waves-light">Forgot Password</button> -->
                                                        <button type="button" class="btn btn-secondary btn-sm waves-effect waves-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Online Services</button>
                                                        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom">Need Help</button>

                                                    </div>
                                                </div>

                                            </form>

                                            <!-- right offcanvas -->
                                            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                                                <div class="offcanvas-header">
                                                    <h5 id="offcanvasRightLabel">Paragon AFS Services</h5>
                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <div class="list-group list-group-fill-success list-group-flush">
                                                        <a href="https://paragonafs.ca/personal_tax.php" class="list-group-item list-group-item-action">Personal Income Tax</a>
                                                        <a href="https://paragonafs.ca/corporate_tax.php" class="list-group-item list-group-item-action">Corporate Income Tax</a>
                                                        <a href="https://paragonafs.ca/incorporate.php" class="list-group-item list-group-item-action">Incorporate / Register a Business</a>
                                                        <a href="https://paragonafs.ca/bookkeeping.php" class="list-group-item list-group-item-action">Accounting / Bookkeeping</a>
                                                        <a href="https://paragonafs.ca/payroll_salary.php" class="list-group-item list-group-item-action">Payroll &amp; Salary Calculations</a>
                                                        <a href="https://paragonafs.ca/gst_hst.php" class="list-group-item list-group-item-action">GST/HST Returns</a>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- bottom offcanvas -->
                                            <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasBottom" style="height: auto !important;" aria-labelledby="offcanvasBottomLabel">
                                                <div class="offcanvas-header">
                                                    <h5 id="offcanvasBottomLabel">Need Help? Contact Us.</h5>
                                                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                                </div>
                                                <div class="offcanvas-body">
                                                    <div class="row gallery-light">
                                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                                            <div class="gallery-box card light mb-0">
                                                                <div class="gallery-container">
                                                                    <a href="https://paragonafs.ca" title="">
                                                                        <img class="gallery-img img-fluid mx-auto" src="https://paragonafs.ca/auth/assets/img/logos/paragon-logo.png" alt="">
                                                                    </a>
                                                                </div>
                                                             
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                                            <div class="gallery-box card light mb-0">
                                                                <div class="gallery-container">
                                                                   
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="https://paragonafs.ca/assets/icons/clock.png" alt="" class="avatar-xs">
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-2">
                                                                                    Mon-Fri 09:00 - 19:00  
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="https://paragonafs.ca/assets/icons/iphone.png" alt="" class="avatar-xs">
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-2">
                                                                                    647-909-8484
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="https://paragonafs.ca/assets/icons/iphone.png" alt="" class="avatar-xs">
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-2">
                                                                                    437-881-9175
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    
                                                                    </ul>
                                                                
                                                                </div>
                                                               
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-xl-3 col-lg-4 col-sm-6">
                                                            <div class="gallery-box card mb-0">
                                                                <div class="gallery-container">
                                                              
                                                                    <ul class="list-group">
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="https://paragonafs.ca/assets/icons/gmail.png" alt="" class="avatar-xs">
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-2">
                                                                                    info@paragonafs.ca
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="https://paragonafs.ca/assets/icons/landline.png" alt="" class="avatar-xs">
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-2">
                                                                                    416-477-3359
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                        <li class="list-group-item">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="flex-shrink-0">
                                                                                    <img src="https://paragonafs.ca/assets/icons/map.png" alt="" class="avatar-xs">
                                                                                </div>
                                                                                <div class="flex-grow-1 ms-2">
                                                                                    #19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7
                                                                                </div>
                                                                            </div>
                                                                        </li>
                                                                    
                                                                    </ul>
                                                                 

                                                                </div>

                                                              
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-xl-3 col-lg-4 col-sm-6 d-md-none d-xl-block">
                                                            <div class="gallery-box card mb-0">
                                                                <div class="gallery-container">
                                                                    <a href="https://www.google.com/maps?ll=43.66714,-79.733547&z=16&t=m&hl=en&gl=PH&mapclient=embed&q=1+Bartley+Bull+Pkwy+%2319a+Brampton,+ON+L6W+3T7+Canada" title="">
                                                                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2886.08800044863!2d-79.7335473!3d43.667139600000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b3fd159b604b5%3A0x7eb23c5a1f69f3d6!2s1%20Bartley%20Bull%20Pkwy%20%2319a%2C%20Brampton%2C%20ON%20L6W%203T7%2C%20Canada!5e0!3m2!1sen!2sph!4v1674484191141!5m2!1sen!2sph" width="100%" height="170px" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                                                    </a>
                                                                </div>
                                                               
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                                </div>
                                            </div>

                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Don't have an account ? <a href="main_2.php" class="fw-semibold text-primary text-decoration-underline"> Signup</a> </p>
                                        </div>
                                    </div>
                                </div>
                                <!-- end col -->
                            </div>
                            <!-- end row -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> Paragon Accounting and Financial Services Inc. <i class="mdi mdi-heart text-danger"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="admin/assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="admin/assets/libs/simplebar/simplebar.min.js"></script>
    <script src="admin/assets/libs/node-waves/waves.min.js"></script>
    <script src="admin/assets/libs/feather-icons/feather.min.js"></script>
    <script src="admin/assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="admin/assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="admin/assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="admin/assets/js/pages/particles.app.js"></script>
    <!-- validation init -->
    <script src="admin/assets/js/pages/form-validation.init.js"></script>

</body>

</html>