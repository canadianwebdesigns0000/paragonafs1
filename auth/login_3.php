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
                                        <h5 class="text-primary">Forgot Password?</h5>
                                        <p class="text-muted">Reset password with Paragon AFS</p>

                                        <div class="mt-2 text-center">
                                            <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop" colors="primary:#0ab39c" class="avatar-xl">
                                            </lord-icon>
                                        </div>

                                        <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                                            Enter your email and instructions will be sent to you!
                                        </div>
                                        <div class="p-2">
                                            <form>
                                                <div class="mb-4">
                                                    <label class="form-label">Email</label>
                                                    <input type="text" class="form-control" id="email" placeholder="Enter email address">
                                                </div>

                                                <div class="text-center mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Send Reset Link</button>
                                                </div>
                                            </form><!-- end form -->
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Wait, I remember my password... <a href="main.php" class="fw-semibold text-primary text-decoration-underline"> Click here </a> </p>
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

</body>

</html>