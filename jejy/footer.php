<!-- ========== FOOTER ========== -->
<style>
  .pa-footer{
    background:#f1f2f5;
    margin-top:48px;
    border-top:1px solid rgba(15, 23, 42, 0.04);
    box-shadow:0 -3px 12px rgba(15, 23, 42, 0.03);
    padding:22px 18px 16px;
  }

  /* desktop: 2 columns so it’s not tall */
  .pa-footer-top{
    max-width:1190px;
    margin:0 auto;
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:20px;
    align-items:flex-start;
  }

  /* left column */
  .pa-footer-brand{
    max-width:320px;
  }
  .pa-footer-logo{
    height:58px;
    width:auto;
    display:block;
    margin-bottom:6px;
  }
  .pa-footer-tag{
    margin:0;
    color:#475569;
    font-size:0.88rem;
    line-height:1.35;
  }

  /* right column: contacts */
  .pa-footer-contact ul{
    list-style:none;
    margin:0;
    padding:0;
    display:flex;
    flex-direction:column;
    gap:7px;
  }
  .pa-footer-contact li{
    display:flex;
    gap:10px;
    align-items:flex-start;
  }
  .pa-icon{
    width:20px;
    height:20px;
    flex:0 0 20px;
    margin-top:1px;
  }
  .pa-footer-contact a{
    color:#0f172a;
    text-decoration:none;
    line-height:1.25;
  }
  .pa-footer-contact a:hover{
    color:#0b66c3;
  }

  /* bottom bar */
  .pa-footer-bottom{
    max-width:1190px;
    margin:18px auto 0;
    padding-top:12px;
    border-top:1px solid rgba(15, 23, 42, 0.08);
    display:flex;
    justify-content:space-between;
    gap:12px;
    align-items:center;
    font-size:0.8rem;
    color:#64748b;
  }
  .pa-made{
    display:flex;
    gap:8px;
    align-items:center;
  }
  .pa-cwd-pill{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    background:#0f172a;
    border-radius:999px;
    padding:3px 10px;
  }
  .pa-cwd-pill img{
    height:28px;
    width:auto;
    display:block;
  }
  .pa-copy{ white-space:nowrap; }

  /* mobile */
  @media (max-width: 768px){
    .pa-footer{
      padding:16px 20px 12px;
    }
    .pa-footer-top{
      grid-template-columns: 1fr;
      gap:14px;
    }
  .pa-footer-brand{
    max-width:none;
    margin:0 auto;              /* center the block */
    display:flex;
    flex-direction:column;      /* logo on top, text under */
    align-items:center;         /* center horizontally */
    text-align:center;          /* center the tagline text */
  }

    .pa-cwd-pill img{
    height:22px;
  }
    .pa-footer-logo{
      height:52px;
    }
    .pa-footer-contact ul{
      gap:6px;
    }
    .pa-footer-bottom{
      flex-direction:column;
      align-items:flex-start;
      gap:4px;
    }
    .pa-copy{
      white-space:normal;
    }
  }
</style>

<footer class="pa-footer">
  <div class="pa-footer-top">
    <!-- left: logo with tagline UNDER it -->
    <div class="pa-footer-brand">
      <img src="../assets/img/paragon_logo.png" alt="Paragon Accounting and Financial Services" class="pa-footer-logo">
      <p class="pa-footer-tag">Personal, accurate, and secure tax filing.</p>
    </div>

    <!-- right: contact list -->
    <div class="pa-footer-contact">
      <ul>
        <li>
          <img src="https://paragonafs.ca/assets/icons/map.png" alt="" class="pa-icon">
          <a href="https://www.google.com/maps?ll=43.66714,-79.733547&z=16&t=m&hl=en&gl=PH&mapclient=embed&q=1+Bartley+Bull+Pkwy+%2319a+Brampton,+ON+L6W+3T7+Canada" target="_blank" rel="noopener">
            #19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7
          </a>
        </li>
        <li>
          <img src="https://paragonafs.ca/assets/icons/gmail.png" alt="" class="pa-icon">
          <a href="mailto:info@paragonafs.ca">info@paragonafs.ca</a>
        </li>
        <li>
          <img src="https://paragonafs.ca/assets/icons/landline.png" alt="" class="pa-icon">
          <a href="tel:416-477-3359">416-477-3359</a>
        </li>
        <li>
          <img src="https://paragonafs.ca/assets/icons/iphone.png" alt="" class="pa-icon">
          <a href="tel:647-909-8484">647-909-8484</a>
        </li>
        <li>
          <img src="https://paragonafs.ca/assets/icons/iphone.png" alt="" class="pa-icon">
          <a href="tel:437-881-9175">437-881-9175</a>
        </li>
      </ul>
    </div>
  </div>

  <div class="pa-footer-bottom">
    <p class="pa-made">
      Designed and marketed by
      <a href="https://canadianwebdesigns.ca" target="_blank" rel="noopener">
        <span class="pa-cwd-pill">
          <img src="https://canadianwebdesigns.ca/wp-content/uploads/2019/07/cwd-white.png"
               alt="Canadian Website Designs">
        </span>
      </a>
    </p>
    <p class="pa-copy">© 2021 Paragon AFS. All rights reserved.</p>
  </div>
</footer>
<!-- ========== /FOOTER ========== -->


<!-- End of Footer -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="../assets/bootstrap/js/bootstrap.min.js"></script>
<script src="../assets/js/progresscircle.js"></script>
<script src="../assets/js/counto.min.js"></script>
<script src="../assets/js/countMe.min.js"></script>
<script>
    AOS.init();
</script>
<script>
    $(document).ready(function() {

        $(function() {
            $('.counter').counto(7500, 2000);
            $('.counter2').counto(3452, 5000);
            $(".counter3").countMe(15, 330);
            $(".counter4").countMe(72, 69);
        });

        $("#testimonial-slider").owlCarousel({
            items: 2,
            itemsDesktop: [1000, 2],
            itemsDesktopSmall: [979, 1],
            itemsTablet: [768, 1],
            pagination: true,
            navigation: false,
            navigationText: ["", ""],
            slideSpeed: 1000,
            autoPlay: true
        });
    });

    $(function() {
        $('.circlechart').circlechart();
    });
</script>
<script>
    $(document).ready(function() {
        $(window).scroll(function() {
            var scroll = $(window).scrollTop();
            if (scroll > 200) {
                $(".black").css("width", "100%");
                $('#nav-contact').fadeOut();
            } else {
                $(".black").css("background", "none");
                $(".black").css("width", "100%");
                $('#nav-contact').fadeIn();
            }
        })
    })
</script>
<script>
    function myFunction() {
        var x = document.getElementById("myTopnav");
        if (x.className === "topnav") {
            x.className += " responsive";
        } else {
            x.className = "topnav";
        }
    }

    // //Get the button
    // let mybutton = document.getElementById("btn-back-to-top");

    // // When the user scrolls down 20px from the top of the document, show the button
    // window.onscroll = function() {
    //     scrollFunction();
    // };

    // function scrollFunction() {
    //     if (
    //         document.body.scrollTop > 20 ||
    //         document.documentElement.scrollTop > 20
    //     ) {
    //         mybutton.style.display = "block";
    //     } else {
    //         mybutton.style.display = "none";
    //     }
    // }
    // // When the user clicks on the button, scroll to the top of the document
    // mybutton.addEventListener("click", backToTop);

    // function backToTop() {
    //     document.body.scrollTop = 0;
    //     document.documentElement.scrollTop = 0;
    // }
</script>
<script>
    $(document).ready(function() {

        var showHeaderAt = 0;

        if ($(window).width() > 990) {
            //Add your javascript for large screens here
            showHeaderAt = 150;
        } else {
            //Add your javascript for small screens here
            showHeaderAt = 450;
        }

        var win = $(window),
            body = $('body');

        // Show the fixed header only on larger screen devices

        if (win.width() > 900) {

            // When we scroll more than 150px down, we set the
            // "fixed" class on the body element.

            win.on('scroll', function(e) {

                if (win.scrollTop() > showHeaderAt) {
                    body.addClass('fixed');
                    $(".header-fixed").css("position", "fixed");
                    $(".header-fixed").css("transition", "top 1s");
                } else {
                    body.removeClass('fixed');
                    // $(".header-fixed").css("position", "absolute");
                    $(".header-fixed").css("display", "block");
                    $(".header-fixed").css("transition", "top 0.1s");
                    $(".header-fixed").css("z-index", "999");
                }
            });
        }


    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        //jquery for toggle sub menus
        $('.sub-btn').click(function() {
            $(this).next('.sub-menu').slideToggle();
            $(this).find('.dropdown').toggleClass('rotate');
        });

        //jquery for expand and collapse the sidebar
        $('.menu-btn').click(function() {
            $('.side-bar').addClass('active');
            $('.menu-btn').css("display", "none");
            $('.menu-backdrop').css("visibility", "visible");
        });

        $('.close-btn').click(function() {
            $('.side-bar').removeClass('active');
            $('.menu-btn').css("display", "block");
            $('.menu-backdrop').css("visibility", "hidden");
        });

        // $('.menu-backdrop').click(function() {
        //     $('.side-bar').removeClass('active');
        //     $('.menu-btn').css("visibility", "visible");
        //     $('.menu-backdrop').css("visibility", "hidden");
        // });

    });

    // $(document).click(function() {
    //     var container = $(".menu-backdrop");
    //     if (!container.is(event.target) && !container.has(event.target).length) {
    //         container.css("visibility", "visible");
    //     }
    // });

    const concernedElement = document.querySelector(".side-bar");
    const menubackdrop = document.querySelector(".menu-backdrop");

    document.addEventListener("mousedown", (event) => {
        if (concernedElement.contains(event.target)) {
            console.log("Clicked Inside");
        } else {
            $('.side-bar').removeClass('active');
            $('.menu-btn').css("visibility", "visible");
            $('.menu-backdrop').css("visibility", "hidden");
        }
    });
</script>
