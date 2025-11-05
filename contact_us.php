<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Contact Us</title>
    <link rel="icon" type="image/x-icon" href="assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" type="text/css" href="./multi-form.css?v2" />
    <link rel="stylesheet" href="assets/css/dropzone.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/foundation-datepicker.css">

<script src="https://www.google.com/recaptcha/api.js" async defer></script>


    <script type="module" src="assets/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="./multi-form.js?v2"></script>
</head>

<body>

    <?php include_once 'headers2.php'; ?>
	
     <?php include_once 'navbar.php'; ?>

    <!-- The content of your page would go here. -->

    <section style="background-image: url(/assets/images/contact-us.jpg);background-position: bottom center;background-repeat: no-repeat;background-size: cover;margin-bottom: 0px;height: 422px;z-index: 0;position: relative; ">
        <div style="height: 100%;width: 100%;top: 0;left: 0;position: absolute;background-color: #121212;opacity: 0.70;transition: background 0.3s, border-radius 0.3s, opacity 0.3s;border-style: none;border-color: rgba(33,37,41,0);"></div>
        <div class="text-white d-flex flex-column align-items-center container position-relative services_page">
            <h2>&nbsp;</h2>
        </div>
    </section>

    <section class="container services" style="padding: 100px 20px 100px;">

        <div class="contact-us row bg-white shadow-none mb-5">
            <div class="col-lg-6 text-center">
                <h4 class="par-h4 mt-5" data-aos="slide-right" data-aos-duration="2000" style="font-weight: 600">Paragon Accounting</h4>
                <h2 class="par-h2 mt-4 mb-5">We’re Paragon Accounting <br> To Help With Financial Business</h2>

                <div class="row mt-4">
                    <div class="col-lg-4">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-location-arrow"></i>
                            <h5 class="par-h5 mt-4">Office Location</h5>
                            <p class="par-p mt-3">#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</p>
                        </div>

                    </div>
                    <div class="col-lg-4">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-headphones-alt"></i>
                            <h5 class="par-h5 mt-4">Calling Support</h5>
                            <p class="par-p mt-3"><i class="fas fa-phone-square-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (416) 477 3359<br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (647) 909 8484 <br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (437) 881 9175</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-mail-bulk"></i>
                            <h5 class="par-h5 mt-4">Email Information</h5>
                            <p class="par-p mt-3">info@paragonafs.ca <br><br><br></p>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-lg-6">

                <div id="emailSentModal" class="modal fade" data-bs-keyboard="false">
                    <div class="modal-dialog modal-confirm">
                        <div class="modal-content">
                            <div class="modal-header justify-content-center">
                                <div class="icon-box">
                                    <i class="fas fa-thumbs-up" style="margin: 0; background: white;"></i>
                                </div>
                            </div>
                            <div class="modal-body text-center">
                                <h4>Message Received!</h4>
                                <p>You have successfully sent a message to us. Our Team will reach out to you soon. If you didnt hear from us within 48 hours, Please Contact Us.
                                </p>
                                <button class="btn btn-success" data-bs-dismiss="modal"><span>Okay</span></button>
                            </div>
                        </div>
                    </div>
                </div>

                <form method="POST" action="/contact_sendmail.php" class="row g-3 needs-validation" novalidate id="contact-form">

                    <div class="col-md-12">
                        <input type="text" name="name" placeholder="Your First Name" class="form-control" id="validationCustom01" required>
                        <div class="invalid-feedback">
                            First Name is required
                        </div>
                    </div>

                    <div class="col-md-12">
                        <input name="last_name" type="text" placeholder="Your Last Name" class="form-control" id="validationCustom01" required>
                        <div class="invalid-feedback">
                            Last Name is required
                        </div>
                    </div>

                    <div class="col-md-12">
                        <input name="email" type="text" placeholder="Your Email" class="form-control" id="validationCustom01" required>
                        <div class="invalid-feedback">
                            Email is required
                        </div>
                    </div>

                    <div class="col-md-12">
                        <input name="subject" type="text" placeholder="Your Subject" class="form-control" id="validationCustom01" required>
                        <div class="invalid-feedback">
                            Subject is required
                        </div>
                    </div>

                    <div class="col-md-12">
                        <textarea name="message" placeholder="Your Message" class="form-control" id="validationCustom01" required></textarea>
                        <div class="invalid-feedback">
                            Your Message is required
                        </div>
                    </div>
    <div class="g-recaptcha" data-sitekey="6Lem0r0qAAAAABrvaDsWTEVecvVzR0q9oDNIb3WK"></div>
                    <p>
                        <input type="submit" id="contact-submit" value="Send Message" />
                    </p>
                </form>
            </div>
        </div>
    </section>

    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2886.08800044863!2d-79.7335473!3d43.667139600000006!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x882b3fd159b604b5%3A0x7eb23c5a1f69f3d6!2s1%20Bartley%20Bull%20Pkwy%20%2319a%2C%20Brampton%2C%20ON%20L6W%203T7%2C%20Canada!5e0!3m2!1sen!2sph!4v1674484191141!5m2!1sen!2sph" width="100%" height="450px" style="border:0; margin-top: -180px; z-index:0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

    <script>
        $(document).ready(function() {
            var emailSent = new URLSearchParams(window.location.search).get("email_sent");
            if (emailSent === "success") {
                $('#emailSentModal').modal('show');
            }
        });
    </script>

    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()


        const constraints = {
            name: {
                presence: {
                    allowEmpty: false
                }
            },
            email: {
                presence: {
                    allowEmpty: false
                },
                email: true
            },
            subject: {
                presence: {
                    allowEmpty: false
                },
            },
            message: {
                presence: {
                    allowEmpty: false
                }
            }
        };

        const form = document.getElementById('contact-form');

        form.addEventListener('submit', function(event) {
            const formValues = {
                name: form.elements.name.value,
                email: form.elements.email.value,
                subject: form.elements.subject.value,
                message: form.elements.message.value
            };

            const errors = validate(formValues, constraints);

            if (errors) {
                event.preventDefault();
                const errorMessage = Object
                    .values(errors)
                    .map(function(fieldValues) {
                        return fieldValues.join(', ')
                    })
                    .join("\n");

                alert(errorMessage);
            }
        }, false);
    </script>

    <?php include_once 'footer.php'; ?>


    <script src="assets/js/jquery.repeater.min.js"></script>
    <script src="assets/js/foundation-datepicker.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7jJzMZURzl5OamoNrClsIy447MjmAENk&callback=initAutocomplete&libraries=places&v=weekly" defer></script>
    <script src="https://js.upload.io/upload-js/v2"></script>
    <!-- Demo ads. Please ignore and remove. -->
    <!-- <script src="http://cdn.tutorialzine.com/misc/enhance/v3.js" async></script> -->

</body>

</html>