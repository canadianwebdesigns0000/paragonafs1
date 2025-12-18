<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8" />
    <title>Admin Sign In | Paragon AFS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="" name="description" />
    <meta content="" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/paragon_logo_icon.png">

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

    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position" style="background-image: url('assets/images/paragon_login_image.png');background-position: center;background-size: cover;" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            <div>
                                <a href="/" class="d-inline-block auth-logo">
                                    <img src="assets/images/paragon_logo.png" alt="" width="200">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="p-2">

                                    <form class="login-form" autocomplete="off" method="POST">
                                        <!-- <h3 class="form-title font-green">Sign In</h3> -->
                                        <div id="error"></div>

                                        <div class="form-group form-md-line-input mb-3">
                                            <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                                            <label class="control-label visible-ie8 visible-ie9">Username</label>
                                            <input class="form-control form-control-solid placeholder-no-fix" type="text"  placeholder="Username" name="username" id="username" autocomplete="new-username" />
                                            <div class="form-control-focus"> </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="password-input">Password</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input" placeholder="Enter password" name="password" id="password-input">
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                            </div>
                                        </div>

                                        <!-- <div class="form-group form-md-line-input mb-3">
                                            <label class="control-label visible-ie8 visible-ie9">Password</label>
                                            <input class="form-control form-control-solid placeholder-no-fix" type="password"  placeholder="Password" name="password" id="password" autocomplete="new-password" />
                                            <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                            <div class="form-control-focus"> </div>
                                        </div> -->
                                        
                                        <div class="form-actions mt-4">
                                            <button class="btn btn-success w-100" type="submit" onclick="login();return false;">Sign In</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
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
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> Paragon Accounting and Financial Services Inc..
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
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- particles js -->
    <script src="assets/libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="assets/js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="assets/js/pages/password-addon.init.js"></script>

    <!-- JS FILES    -->
    <script src="../assets/global/plugins/respond.min.js"></script>
    <script src="../assets/global/plugins/excanvas.min.js"></script>
    <script src="../assets/global/plugins/ie8.fix.min.js"></script>

    <!-- BEGIN CORE PLUGINS -->
    <script src="../assets/global/plugins/jquery.min.js" type="text/javascript"></script>
    <script src="../assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <!-- END CORE PLUGINS -->
    <!-- BEGIN PAGE LEVEL PLUGINS -->

    <script src="../assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->
    <!-- BEGIN THEME GLOBAL SCRIPTS -->
    <script src="../assets/global/app.min.js" type="text/javascript"></script>
    <script src="../assets/plugins/froiden-helper/helper.js" type="text/javascript"></script>
    <script>
        function login() {
            $.easyAjax({
                url: "ajax/login.php",
                type: "POST",
                data: $(".login-form").serialize(),
                container: ".login-form",
                messagePosition: "inline"
            });
        }
    </script>
</body>

</html>