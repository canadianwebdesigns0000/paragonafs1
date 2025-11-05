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
                                                <a href="/" class="d-block">
                                                    <img src="https://paragonafs.ca/auth/assets/img/logos/paragon-logo.png" alt="" height="50">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-info"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
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
                                                </div>
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


                                        <div>
                                            <h5 class="text-dark">Enter your Email Address to Sign In or to Create an Account</h5>
                                            <!-- <p class="text-muted">Sign in to continue to Paragon.</p> -->
                                        </div>

                                        <div class="mt-4">

                                        <form class="login-form needs-validation" method="POST" autocomplete="off" onsubmit="return validateForm(event)" novalidate>

                                            <div class="mb-3">
                                                <label for="useremail" class="form-label">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="useremail" required>
                                                <div class="invalid-feedback">
                                                    Invalid Email Address. Please Try Again.
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4">
                                                <button class="btn btn-info w-100" type="submit">Continue</button>
                                            </div>

                                            <div class="mt-4 text-center">
                                                <div class="signin-other-title">
                                                    <h5 class="fs-13 mb-4 title">Sign In with</h5>
                                                </div>

                                                <div>
                                                    <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                                                    <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                                                    <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                                                    <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
                                                </div>
                                            </div>

                                        </form>

                                            
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