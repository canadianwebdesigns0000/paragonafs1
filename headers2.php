<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// include './auth/config.php';

// // SESSION CHECK SET OR NOT
// if (!isset($_SESSION['email'])) {
//     header('location:../auth');
// }

?>
<style>
#navbar_upload{
	display:none !important;
}
</style>
<div class="header_top">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 text-center">
                <span>
                    <img src="https://paragonafs.ca/assets/icons/clock.png" width="22" style="vertical-align: middle;" />
                    &nbsp; <a href="">Mon-Fri 09:00 - 19:00</a> &nbsp;
                </span>
            </div>

            <div class="col-lg-6 text-center header_top_contact">
                <span>
                    <img src="https://paragonafs.ca/assets/icons/iphone.png" width="22" style="vertical-align: middle;" /> &nbsp; <a href="tel:647-909-8484">647-909-8484</a> &nbsp;
                    <img src="https://paragonafs.ca/assets/icons/iphone.png" width="22" style="vertical-align: middle;" /> &nbsp; <a href="tel:437-881-9175">437-881-9175</a> &nbsp;
                </span>
            </div>

            <div class="col-lg-3 text-center header_top_socials">
                <span>
                    <?php
                        if (!isset($_SESSION['email'])) {
                            // User is not logged in
                            echo '
                            
                            <button class="button-82-pushable" onclick="window.location.href=\'./auth/\'" role="button">
                                    <span class="button-82-shadow"></span>
                                    <span class="button-82-edge"></span>
                                    <span class="button-82-front text">
                                        Sign In
                                    </span>
                                </button>';
                        } else {
                            // User is logged in
                            echo '<button class="button-82-pushable" onclick="window.location.href=\'./auth/logout.php\'" role="button">
                                    <span class="button-82-shadow"></span>
                                    <span class="button-82-edge"></span>
                                    <span class="button-82-front text">
                                        Sign Out
                                    </span>
                                </button>';
                        }
                    ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="header_middle">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 header_top_time">
                <a href="/"> <img src="https://paragonafs.ca/assets/img/paragon_logo.png" alt="logo" style="width: 190px; height:65px"></a>
            </div>
            <div class="col-lg-8 text-center">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="header_middle_image">
                            <img src="https://paragonafs.ca/assets/icons/gmail.png" width="30" />
                        </div>
                        <a href="mailto:%20info@paragonafs.ca" style="font-size: 15px;">info@paragonafs.ca</a>


                    </div>

                    <div class="col-lg-4">
                        <div class="header_middle_image">
                            <img src="https://paragonafs.ca/assets/icons/landline.png" width="30" />
                        </div>

                        <!-- <i class="fas fa-phone-square-alt" style="color: #FCBC45;"></i> -->
                        <ul style="text-align: center;">
                            <li style="margin-right: 20px;">Office Number</li>
                            <li> <a href="tel:416-477-3359" style="font-size: 15px;">416-477-3359</a></li>
                        </ul>

                        <div class="header_middle_image mobile_num">
                            <img src="https://paragonafs.ca/assets/icons/iphone.png" width="35" />
                        </div>

                        <ul class="mobile_num" style="text-align: center;">
                            <li style="margin-right: 20px;">647-909-8484</li>
                            <li style="margin-right: 20px;"> 437-881-9175</li>
                        </ul>
                    </div>

                    <div class="col-lg-4 mobile_num_second" style="display: none;">
                        <div class="header_middle_image mobile_num_second">
                            <img src="https://paragonafs.ca/assets/icons/iphone.png" width="35" />
                        </div>
                        <ul style="text-align: center;">
                            <li style="margin-right: 20px;">647-909-8484</li>
                            <li style="margin-right: 20px;"> 437-881-9175</li>
                        </ul>
                    </div>
                    <div class="col-lg-4">

                        <!-- <i class="fas fa-location-arrow fa-sm" style="color: #FCBC45;"></i> -->
                        <div class="header_middle_image">
                            <img src="https://paragonafs.ca/assets/icons/map.png" width="30" />
                        </div>
                        <ul class="header_middle_location_link">
                            <li style="width:202px;"> <a href="https://www.google.com/maps?ll=43.66714,-79.733547&z=16&t=m&hl=en&gl=PH&mapclient=embed&q=1+Bartley+Bull+Pkwy+%2319a+Brampton,+ON+L6W+3T7+Canada" style="font-size: 15px;margin-right:0px;">#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- 


    <div class="sticky-icon">
        <a href="https://www.google.com/maps/place/1+Bartley+Bull+Pkwy+%2319a,+Brampton,+ON+L6W+3T7,+Canada/data=!4m2!3m1!1s0x882b3fd159b604b5:0x7eb23c5a1f69f3d6?sa=X&ved=2ahUKEwjI6tXW8938AhXXZ2wGHSGzBYkQ8gF6BAgYEAI" class="locbtn"><i class="fas fa-location-arrow"></i></i> #19A - 1, BARTLEY BULL PKWY, BRAMPTON, ONTARIO L6W 3T7</a>
        <a href="tel:416-477-3359" class="callbtn"><i class="fas fa-phone-alt"></i> </i> 416-477-3359 &nbsp; | &nbsp; 647-909-8484 &nbsp; | &nbsp; 437-881-9175</a>
        <a href="mailto:info@paragonafs.ca" class="email"><i class="fas fa-envelope"></i> </i> info@paragonafs.ca </a>
        <a href="https://square.site/book/LCBZG7RXX7V6T/paragon-accounting-and-financial-services-inc-brampton-on" class="calendar"><i class="fas fa-calendar-alt"></i> Book Appointment </a>
    </div>

    <button type="button" class="btn btn-primary btn-floating btn-lg" id="btn-back-to-top">
        <i class="fas fa-arrow-up"></i>
    </button> -->

<button type="button" class="btn btn-primary btn-floating btn-lg" id="btn-back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>