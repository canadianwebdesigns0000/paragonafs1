<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="icon" type="image/x-icon" href="assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/Navbar-Centered-Links-icons.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/progresscircle.css">
    <link rel="stylesheet" type="text/css" href="./multi-form.css?v2" />
    <link rel="stylesheet" href="assets/css/dropzone.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/foundation-datepicker.css">

    <script type="module" src="assets/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="./multi-form.js?v2"></script>
</head>

<body>

    <?php include_once 'headers2.php'; ?>



    <header class="header-fixed">

        <div class="header-limiter">


            <nav class="navbar navbar-expand-lg" style="z-index: 0;">
                <div class="container-xl" style="white-space: nowrap; justify-content:center">

                    <h1><a class="navbar-brand" href="/index.php"><span><img src="assets/img/paragon_logo.png" alt="logo" style="width: 190px; height: 60px;"></span></a></h1>
                    <!-- <a class="navbar-brand" href="/index.php"><span><img src="assets/img/paragon_logo.png" alt="logo" style="width: 190px;"></span></a> -->

                    <button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample07XL" aria-controls="navbarsExample07XL" aria-expanded="false" aria-label="Toggle navigation" style="background-color: white;">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <div class="navbar-collapse collapse align-items-center" id="navbarsExample07XL" style="color:black">
                        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                            <li class="nav-item">
                                <a class="nav-link" href="/">Home</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/about.php">About</a>
                            </li>
                            <li class="nav-item">
                                <div class="dropdown nav-link">
                                    <a href="/services.php" class="services_hover" style="text-decoration: none; padding: 0;">Services &nbsp;</a>
                                    <button class="dropbtn"><i class="fa fa-chevron-down" aria-hidden="true" style="color: black;"></i></button>
                                    <div class="dropdown-content">
                                        <ul style="list-style: none; padding: 0; align-items: right;">
                                            <li><a href="/personal_tax.php">Personal Income Tax</a></li>
                                            <li><a href="/corporate_tax.php">Corporate Income Tax</a></li>
                                            <li><a href="/incorporate.php">Incorporate / Register a Business</a></li>
                                            <li><a href="/bookkeeping.php">Accounting/Bookkeeping</a></li>
                                            <li><a href="/payroll_salary.php">Payroll & Salary Calculations</a></li>
                                            <li><a href="/gst_hst.php">GST/HST Returns</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/documents-page.php">Documents</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/contact_us.php">Contact Us</a>
                            </li>
                        </ul>
                        <a class="btn text-uppercase hvr-bounce-to-bottom" id="navbar_upload1" href="/form" style="letter-spacing: 1px;border:none; color: white;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a>
                        <a class="btn text-uppercase hvr-bounce-to-bottom2" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none; padding: 15px 30px;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a>
                        <a class="btn text-uppercase" id="navbar_button2" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="color: black; letter-spacing: 1px;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book</a>
                    </div>
                </div>
            </nav>

        </div>

    </header>

    <nav class="navbar navbar-expand-lg">
        <div class="container-xl">

            <div class="navbar-toggler" style="background-color: white;border-radius:0;margin:0;width:100%;padding: 0;border: none;">


                <div class="col-6" style="padding: 0;">

                    <a class="btn text-uppercase hvr-bounce-to-bottom" id="navbar_upload" href="/form" style="letter-spacing: 1px;border:none;text-align: center;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a>

                </div>

                <div class="col-6" style="padding: 0;">

                    <a class="btn text-uppercase hvr-bounce-to-bottom2" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none;text-align: center;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a>
                </div>

                <!-- <div class="col-2 collapsed text-center" style="padding-top: 10px;margin-top: 0px;" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample08XL" aria-controls="navbarsExample08XL" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </div> -->
                <div class="side-bar">

                    <header>

                        <div class="close-btn">
                            <i class="fas fa-times"></i>
                        </div>

                        <img src="assets/img/paragon_logo.png" width="250" height="80" alt="">

                    </header>

                    <div class="menu">
                        <div class="item"><a href="/"><i class="fas fa-home"></i>Home</a></div>
                        <div class="item"><a href="/about.php"><i class="fas fa-info-circle"></i>About</a></div>
                        <div class="item">
                            <a class="sub-btn"><i class="fas fa-hands-helping"></i> Services<i class="fas fa-angle-right dropdown"></i></a>
                            <div class="sub-menu">
                                <a class="sub-item" href="/services.php">All Services</a>
                                <a class="sub-item" href="/personal_tax.php">Personal Income Tax</a>
                                <a class="sub-item" href="/corporate_tax.php">Corporate Income Tax</a>
                                <a class="sub-item" href="/incorporate.php">Incorporate / Register a Business</a>
                                <a class="sub-item" href="/bookkeeping.php">Accounting/Bookkeeping</a>
                                <a class="sub-item" href="/payroll_salary.php">Payroll & Salary Calculations</a>
                                <a class="sub-item" href="/gst_hst.php">GST/HST Returns</a>
                            </div>
                        </div>
                        <div class="item"><a href="/documents-page.php"><i class="fas fa-scroll"></i>Documents</a></div>
                        <div class="item"><a href="/contact_us.php"><i class="fas fa-id-card"></i>Contact Us</a></div>
                        <!-- <div class="item">Contact Us</a></div> -->


                        <div class="item">
                            <a href="/form" style="background-color: #0075be; color:white; margin: 0px 10px 10px;"><i class="fas fa-file-upload"></i>Upload Document</a>
                            <!-- <a class="btn text-uppercase hvr-bounce-to-bottom2 navupload" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a> -->
                        </div>
                        <div class="item">
                            <a href="https://paragon-accounting-and-financial-services-inc.square.site/" style="background-color: #fcbc45; color:black;margin: 0px 10px 20px;"><i class="fas fa-calendar-alt"></i>Book Appointment</a>
                            <!-- <a class="btn text-uppercase hvr-bounce-to-bottom navupload" id="navbar_upload" href="/form" style="letter-spacing: 1px;border:none;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a> -->
                        </div>
                        <div class="item" style="border-top: none;"><a href="#" style="font-size: 20px;font-weight: 700;color: #ffffff;">Contact Info</a>

                            <ul style="list-style: none;padding: 0px;line-height: 1.5;">
                                <li>#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</li>
                                <li>416-477-3359</li>
                                <li>info@paragonafs.ca</li>
                            </ul>

                        </div>
                    </div>
                </div>

            </div>

            <div class="navbar-collapse collapse align-items-center" id="navbarsExample08XL" style="background-color: #063c83;">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <div class="dropdown nav-link">
                            <a href="/services.php" class="services_hover" style="text-decoration: none; color:#fcbc45;">Services &nbsp;</a>
                            <button class="dropbtn"><i class="fa fa-chevron-down" aria-hidden="true" style="color: white;"></i></button>
                            <div class="dropdown-content" style="padding-top: 10px; background-color: #063c83;">
                                <a href="/personal_tax.php">Personal Income Tax</a>
                                <a href="/corporate_tax.php">Corporate Income Tax</a>
                                <a href="/incorporate.php">Incorporate / Register a Business</a>
                                <a href="/bookkeeping.php">Accounting/Bookkeeping</a>
                                <a href="/payroll_salary.php">Payroll & Salary Calculations</a>
                                <a href="/gst_hst.php">GST/HST Returns</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/documents-page.php">Documents</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact_us.php">Contact Us</a>
                    </li>
                </ul>
                <a class="btn text-uppercase hvr-bounce-to-bottom navupload" id="navbar_upload" href="/form" style="letter-spacing: 1px;border:none;"><i class="fas fa-file-upload" style="font-size: 14px;"></i>&nbsp; Upload Document</a>
                <a class="btn text-uppercase hvr-bounce-to-bottom2 navupload" id="navbar_button" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="letter-spacing: 1px; border:none;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book Appointment</a>
                <a class="btn text-uppercase" id="navbar_button2" href="https://paragon-accounting-and-financial-services-inc.square.site/" style="color: black; letter-spacing: 1px;"><i class="fas fa-calendar-alt" style="font-size: 14px;"></i>&nbsp; Book</a>
            </div>

        </div>


        <div class="menu-btn">
            <i class="fas fa-bars"></i>
        </div>




    </nav>

    <!-- You need this element to prevent the content of the page from jumping up -->
    <!-- <div class="header-fixed-placeholder"></div> -->

    <!-- The content of your page would go here. -->

    <section style="background-image: url(https://web-static.wrike.com/blog/content/uploads/2019/12/Professional-Service-Trends-to-Look-Out-for-in-2021-1.jpg?av=662d449becd798efd6ebf85343d00832);background-position: center center;background-repeat: no-repeat;background-size: cover;margin-bottom: 0px;height: 422px;z-index: 0;position: relative; ">
        <div style="height: 100%;width: 100%;top: 0;left: 0;position: absolute;background-color: #121212;opacity: 0.70;transition: background 0.3s, border-radius 0.3s, opacity 0.3s;border-style: none;border-color: rgba(33,37,41,0);"></div>
        <div class="text-white d-flex flex-column align-items-center container position-relative services_page">
            <h2>Services</h2>
        </div>
    </section>

    <!-- Start of Services -->

    <section class="container services" style="padding: 100px 0px;">

        <div class="service-box row bg-white shadow-none" style="width: 100%;padding: 30px;">
            <div class="col-lg-4">
                <h4 class="par-h4" data-aos="slide-right" data-aos-duration="2000">Our Services</h4>
                <h2 class="par-h2">what service we offer</h2>
            </div>
            <div class="col-lg-7 d-flex align-items-end">
                <p class="par-p" style="margin: 0; font-size: 16px;">We offer a wide range of services and ensure it caters all your needs. Our mission is to save your precious time and confusion by offering financial services which are hassle-free, reliable, accurate and personalized.</p>
            </div>
        </div>


        <!-- <div class="service-box row bg-white shadow-none mb-5" style="width: 100%;">
            <div class="col-4">
                <h4 class="par-h4" data-aos="slide-right" data-aos-duration="2000" style="font-weight: 600">Our Services</h4>
                <h2 class="par-h2">what service we offer</h2>
            </div>
            <div class="col-lg-7 d-flex align-items-end">
                <p class="par-p" style="margin: 0; font-size: 16px; margin-left: 40px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
            </div>
            <div class="col-2 text-end">
                <a class="btn btn-primary par-btn text-uppercase" href="/services.php" style="color: black; font-size:14px; border-style: none; margin-top: 30px; padding: 18px 32px; width: 176px;letter-spacing: 0px;">All Services</a>
            </div>
        </div> -->

        <div class="row service-offer mx-auto" style="margin-right: 0px;">

            <div class="service-box services_page_box">
                <div class="col-lg-6" style="margin: 0px;">
                    <img class="services_image" src="https://www.rkbaccounting.ca/wp-content/uploads/2020/12/Personal-Income-tax.jpg" alt="Personal Taxes">
                    <h3>Personal Income Tax</h3>
                    <p class="mobile_description" style="display: none;">Our firm has extensive expertise in personal taxation to ensure accurate and timely filing while maximizing all eligible deductions.</p>
                </div>
                <div class="col-lg-6 services_page_box_text services_page_box_text">

                    <p>Our firm has extensive expertise in personal taxation to ensure accurate and timely filing while maximizing all eligible deductions.</p>
                    <a href="/personal_tax.php">read more <i aria-hidden="true" class="fas fa-arrow-right"></i></a>
                </div>
                <div class="bottom-right">
                    <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/personal.png" alt="Personal Taxes">
                </div>
            </div>

            <div class="service-box services_page_box">
                <div class="col-lg-6">
                    <img class="services_image" src="https://i.ibb.co/PW9NFLp/amendment-to-the-corporate-income-tax-act.png">
                    <h3>Corporate Income Tax</h3>
                    <p class="mobile_description" style="display: none;">Our firm has one goal in mind and that is save you money by mitigating the taxes you pay on your corporate earnings.</p>
                </div>
                <div class="col-lg-6 services_page_box_text">

                    <p>Our firm has one goal in mind and that is save you money by mitigating the taxes you pay on your corporate earnings.</p>
                    <a href="/corporate_tax.php">read more <i aria-hidden="true" class="fas fa-arrow-right"></i></a>
                </div>
                <div class="bottom-right">
                    <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/business.png" alt="Corporate Income Tax">
                </div>
            </div>

            <div class="service-box services_page_box">
                <div class="col-lg-6">
                    <img class="services_image" src="https://apollocover.com/wp-content/uploads/2021/12/how-to-register-a-business-in-canada.jpg" alt="Corporate Income Tax">
                    <h3>Incorporate / Register a Business</h3>
                    <p class="mobile_description" style="display: none;">Let our firm review your small business and give you the right answer which will help with tax planning, income spitting.</p>
                </div>
                <div class="col-lg-6 services_page_box_text">

                    <p>Let our firm review your small business and give you the right answer which will help with tax planning, income spitting.</p>
                    <a href="/incorporate.php">read more <i aria-hidden="true" class="fas fa-arrow-right"></i></a>
                </div>
                <div class="bottom-right">
                    <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/business.png" alt="Corporate Income Tax">
                </div>
            </div>

            <div class="service-box services_page_box">
                <div class="col-lg-6">
                    <img class="services_image" src="https://www.jvstoronto.org/wp-content/uploads/2016/01/calculator-accounting.jpg" alt="Personal Taxes">
                    <h3>Accounting / Bookkeeping</h3>
                    <p class="mobile_description" style="display: none;">Let us take over the Bookkeeping of your company so you can focus on your customers and building your business.</p>
                </div>
                <div class="col-lg-6 services_page_box_text">

                    <p>Let us take over the Bookkeeping of your company so you can focus on your customers and building your business.</p>
                    <a href="/bookkeeping.php">read more <i aria-hidden="true" class="fas fa-arrow-right"></i></a>
                </div>
                <div class="bottom-right">
                    <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/bookkeeping.png" alt="Accounting/Bookkeeping">
                </div>
            </div>

            <div class="service-box services_page_box">
                <div class="col-lg-6">
                    <img class="services_image" src="https://www.farorecruitment.com.vn/images/detailhtml/payroll%20processing%20companies.png" alt="Personal Taxes">
                    <h3>Payroll & Salary Calculations</h3>
                    <p class="mobile_description" style="display: none;">Let us take over the Bookkeeping of your company so you can focus on your customers and building your business.</p>
                </div>
                <div class="col-lg-6 services_page_box_text">

                    <p>Let us take over the Bookkeeping of your company so you can focus on your customers and building your business.</p>
                    <a href="/payroll_salary.php">read more <i aria-hidden="true" class="fas fa-arrow-right"></i></a>
                </div>
                <div class="bottom-right">
                    <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/payroll.png" alt="Payroll & Salary Calculations">
                </div>
            </div>


            <div class="service-box services_page_box">
                <div class="col-lg-6">
                    <img class="services_image" src="https://www.freshbooks.com/wp-content/uploads/2022/02/What-Is-a-GSTHST-Return.jpg" alt="Personal Taxes">
                    <h3>GST/HST Returns</h3>
                    <p class="mobile_description" style="display: none;">Let us take over the Bookkeeping of your company so you can focus on your customers and building your business.</p>
                </div>
                <div class="col-lg-6 services_page_box_text">

                    <p>Let us take over the Bookkeeping of your company so you can focus on your customers and building your business.</p>
                    <a href="/gst_hst.php">read more <i aria-hidden="true" class="fas fa-arrow-right"></i></a>
                </div>
                <div class="bottom-right">
                    <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/outsourcing.png" alt="GST/HST Returns">
                </div>
            </div>
        </div>

    </section>

    <!-- End of Services -->


    <!-- Start of Consultation -->

    <div class="consultation consult_page">
        <div class="consultations consults_page">
            <h2>Contact Us If You Have Question About Our Service</h2>
            <p>Paragon accounting has the necessary skill to set up and run an accurate financial accounting system.</p>
            <button class="btn btn-primary par-btn text-uppercase" type="button" style="border-style: none; height: 64px; padding: 20px 40px; opacity: 1;">Contact us</button>
        </div>
    </div>

    <!-- End of Consultation -->


    <!-- Start of How We Work -->

    <!-- <div class="how_work">
        <div class="row how_works" style="max-width: 1140px;">
            <div class="col-lg-6">
                <div class="tag">
                    <h4>If You Need Help, Get A &nbsp; &nbsp; <br>Consultation</h4>
                    <p><a href="">Get Started <i aria-hidden="true" class="fas fa-arrow-right"></i></a></p>
                    <i aria-hidden="true" class="fas fa-sort-down fa-3x"></i>
                </div>
                <img src="https://templatekit.jegtheme.com/acctual/wp-content/uploads/sites/58/2021/03/group-of-architects-and-business-people-working-together-e1615889142913.jpg" width="495" style="border-radius: 5px;">
            </div>
            <div class="col-lg-6 info_how">
                <h4 class="par-h4">How We Work</h4>
                <h2 class="par-h2" style="margin-bottom: 20;">Find The Right Accounting For Your Business</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua Ut enim ad minim veniam quis nostrud</p>
                <ul>
                    <li><i class="fas fa-check"></i> We Are Leading in Accounting Services</li>
                    <li><i class="fas fa-check"></i> Solutions For Small & Large Business</li>
                    <li><i class="fas fa-check"></i> Learn From Customer Feedback</li>
                </ul>
            </div>
        </div>
    </div> -->

    <?php include_once 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7jJzMZURzl5OamoNrClsIy447MjmAENk&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
</body>

</html>