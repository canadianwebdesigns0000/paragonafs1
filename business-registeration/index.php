<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title> Business Registration</title>
    <link rel="icon" type="image/x-icon" href="https://paragonafs.ca/assets/img/paragon_logo_icon.png" />
    <link rel="stylesheet" type="text/css" href="https://paragonafs.ca//multi-form.css" />
    <link rel="stylesheet" href="https://paragonafs.ca/assets/css/dropzone.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.css">
    <link rel="stylesheet" href="https://paragonafs.ca/assets/css/styles.css">
    <link rel="stylesheet" href="https://paragonafs.ca/assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="https://paragonafs.ca/assets/css/foundation-datepicker.css">
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script type="module" src="assets/js/index.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.17.0/dist/jquery.validate.min.js"></script>
    <script type="text/javascript" src="./multi-form.js?v2"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
    <style>
    .ui-widget-header {
            border: none !important;
            background: none !important;
            color: #333333;
            font-weight: bold;
        }

        .ui-state-default, .ui-widget-content .ui-state-default {
            border: none !important;
            background: none !important;
            text-align: center;
            font-weight: normal;
            color: #454545;
        }


.invalid-feedback {
   
    color: red;
}

.form-control.is-invalid, .was-validated .form-control:invalid{

  border-color:red;

}
        .ui-state-active, .ui-widget-content .ui-state-active {
            border: 1px solid #003eff !important;
            background: #007fff !important;
            font-weight: normal;
            color: #ffffff;
        }

        .ui-state-highlight, .ui-widget-content .ui-state-highlight {
            border: 1px solid #dad55e !important;
            background: #fffa90 !important;
            color: #777620;
        }
        .uploaded_filename{
            width:auto !important;
        }
        .contact-us i.fa-times{
            font-size: 20px;
            line-height: 20px;
            margin-top:0px;
            margin-bottom:0px;
        }

.contact-us form {

    margin-top:30px;

}

.contact-box {
    padding: 0px;
}

  #contact-submit {
    padding: 12px 30px;
    margin-top: 25px;
    width: 220px;

}
.corp_parent .invalid-feedback{
    position: absolute;
    left: 6.6%;
}
#contact-form{
    display: inline-block;
}
@media only screen and (max-width: 600px) {
#contact-form{
    display: grid;
}
.corp_parent .form-check-label{
    font-size: 13px !important;
}
.corp_parent{
    margin-top: 0px;
    display: flex;
}
.corp_parent .col-md-6{
	margin-top: 0px;
    width: 50%;
    padding-right: 0px;
}
.corp_parent .form-check-inline{
    margin-right: 0px;
}
.corp_parent .invalid-feedback{
    position: absolute;
    left: 5%;
}
.mobile_second_col{
    flex: 1;
    order: 2;
}
.mobile_first_col{
    flex: 1;
    order: 1;
}

.contact-us form{
    padding: 0em 1.3em;
}

.invalid-feedback {
   
    color: red;
}
}

    </style>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
</head>

<body>

    <?php include_once 'headers2.php'; ?>
      <?php include_once '../navbar.php'; ?>


   

    <!-- The content of your page would go here. -->

    <section style="background-image: url(/assets/images/contact-us.jpg);background-position: bottom center;background-repeat: no-repeat;background-size: cover;margin-bottom: 0px;height: 422px;z-index: 0;position: relative; ">
        <div style="height: 100%;width: 100%;top: 0;left: 0;position: absolute;background-color: #121212;opacity: 0.70;transition: background 0.3s, border-radius 0.3s, opacity 0.3s;border-style: none;border-color: rgba(33,37,41,0);"></div>
        <div class="text-white d-flex flex-column align-items-center container position-relative services_page">
      
        <h2 style="text-align:center; margin-top:15px;"> Register Your Business </h2>
        </div>
    </section>

    <section class="container services" style="padding: 100px 0px 100px;">
		<div id="pageMessages"></div>      
        <div class="contact-us row bg-white shadow-none mb-5">
            <div class="col-lg-4 text-center mobile_second_col" style="display: flex;
            flex-direction: row-reverse;">
                
                

                <div class="row mt-4">
                    <div class="col-lg-12">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-location-arrow"></i>
                            <h5 class="par-h5 mt-4">Office Location</h5>
                            <p class="par-p mt-3">#19A - 1, Bartley Bull Pkwy, Brampton, Ontario L6W 3T7</p>
                        </div>

                    </div>
                    <div class="col-lg-12">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-headphones-alt"></i>
                            <h5 class="par-h5 mt-4">Calling Support</h5>
                            <p class="par-p mt-3"><i class="fas fa-phone-square-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (416) 477 3359<br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (647) 909 8484 <br><i class="fas fa-mobile-alt" style="color: #FCBC45; font-size:20px; margin:0;line-height: 1;"></i> +1 (437) 881 9175</p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="contact-box">
                            <i aria-hidden="true" class="fas fa-mail-bulk"></i>
                            <h5 class="par-h5 mt-4">Email Information</h5>
                            <p class="par-p mt-3">info@paragonafs.ca <br><br><br></p>
                        </div>
                    </div>
                </div>

            </div>
      

            <div class="col-lg-8 mobile_first_col">
                
               
    
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
                
                    
                   

                <form method="POST" action="/newcorpmail.php" class="g-3 needs-validation" novalidate id="contact-form" style="max-width: 100%;">
                
             
                 
                    <h3 class="mt-5"><b>Register Your Business </b></h3>
      
 
                    <p class="" style="font-size: 16px; text-align:left">Please enter the details requested below to register your business successfully. All fields are mandatory.</p>
                    
           			<div class="row">
      					<div class="col-md-12">	
                         <div class="form_append_error"></div>
                        </div>  
     					<div class="col-md-12">
                            <h6 style="color: #0075be;margin:0px 0 0px;">Personal Information</h6>
                        </div>
      
                           <div class="col-md-12">
                             <label for="validationCustom02" class="form-label" style="margin-bottom:-10px">Name <span style="color: red;">*</span></label>
                        </div>
      
      
                        <div class="col-md-6">
                          
                            <input type="text" name="firstName" class="form-control" id="validationCustom02" placeholder="First Name" required="">
                            <div class="invalid-feedback" style="color:red">Name
                                First Name is required
                            </div>
                        </div>
                        <div class="col-md-6">
                           
                            <input type="text" name="lastName" class="form-control" id="validationCustom02" placeholder="Last Name" required="">
                            <div class="invalid-feedback">
                                Last Name is required
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="validationCustom03" class="form-label">Your SIN Number <span style="color: red;">*</span></label>
                            <input type="text" name="sin_number" value="" class="form-control" maxlength="9" required="">
                            <div class="invalid-feedback">
                                SIN Number is required
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your Phone Number <span style="color: red;">*</span></label>
                            <input type="text" name="phone" value="" class="form-control" required="">
                            <div class="invalid-feedback">
                                Phone Number is required
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Your Email Address <span style="color: red;">*</span></label>
                            <input type="email" name="email" class="form-control" value="" required="">
                            <div class="invalid-feedback">
                                Email is required
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Business Activity <span style="color: red;">*</span></label>
                            <input type="text" name="business_activity" value="" class="form-control" required="">
                            <div class="invalid-feedback">
                                Business Activity is required
                            </div>
                        </div>
                            <div class="row">
                              <label class="form-label">Corporation Type <span style="color: red;">*</span></label>
                            </div>
                            <div class="row corp_parent" style="margin-top:0px">
                            	<div class="col-md-6" style="margin-top:0px">
                            	 	<div class="form-check form-check-inline">
                                		<input type="radio" class="form-check-input" name="corporation_type" onchange="corpType(this.value)" id="hide_corporation_type" value="Number"  style="position: relative;top: 5px;" required="">
                                		<label for="hide_corporation_type" class="form-check-label" style="color:black;">Number corporation </label>
                                		 <div class="invalid-feedback"  style="margin-bottom:25px;" >
                                             Corporation Type is required
                                   		 </div>
                            		</div>
                                   
                            	</div>
                                <div class="col-md-6" style="margin-top:0px">
                                        <div class="form-check form-check-inline">
                            				<input type="radio" class="form-check-input" name="corporation_type" onchange="corpType(this.value)" id="show_corporation_type" value="Named" style="position: relative;top: 5px;">
                            				<label for="show_corporation_type" class="form-check-label" style="color:black;">Named Corporation</label>
                        				</div>
                                </div>
                                
                            </div>
                            
                                
                        <div class="col-md-6" style="display:none" id="name_corp">
                                            
                       
                            <input type="text" name="name_corp" value="" class="form-control"  placeholder=" Name of Corporation">
                                            
                    </div>
                                            
                                            
                                            
                                            
                                            <div class="col-md-12">
                                <hr style="margin: 10px 0 0;">
                            </div>
                           
                            <h6 style="color: #0075be;margin: 20px 0 10px;">Your Current Address</h6>

                            <div class="col-md-6">
                                <input type="text" id="ship_address" name="ship_address" value="" autocomplete="off" class="form-control" style="margin-bottom: 5px;" placeholder="Street" required>
                                            <div class="invalid-feedback">
                                Address is required
                            </div>
                            </div>

                            <div class="col-md-6">
                                <input type="text" id="apartment_unit_number" name="apartment_unit_number" value="<?= $rowUser['apartment_unit_number'] ?>" autocomplete="off" class="form-control" style="margin-bottom: 5px;" placeholder="Apartment, unit, suite, or floor #">
                           <div class="invalid-feedback">
                                Apartment no is required
                            </div>
                                            </div>

                            <div class="col-md-6">
                                <input type="text" id="locality" name="locality" value="<?= $rowUser['locality'] ?>" class="form-control" placeholder="City" style="margin-bottom: 5px;" required>
                            <div class="invalid-feedback">
                                City is required
                            </div>
                                            </div>

                            <div class="col-md-6">
                                <input type="text" id="state" name="state" value="<?= $rowUser['state'] ?>" class="form-control" placeholder="State/Province" style="margin-bottom: 5px;" required>
                          <div class="invalid-feedback">
                                State is required
                            </div>
                                            </div>

                            <div class="col-md-6">
                                <input type="text" id="postcode" name="postcode" value="<?= $rowUser['postcode'] ?>" class="form-control" placeholder="Postal Code" style="margin-bottom: 5px;" required>
                            <div class="invalid-feedback">
                               Postal code is required
                            </div>
                                            </div>

                            <div class="col-md-6">
                                <input type="text" id="country" name="country" value="<?= $rowUser['country'] ?>" class="form-control" placeholder="Country/Region" style="margin-bottom: 5px;" required>
                           <div class="invalid-feedback">
                                Country is required
                            </div>
                                            </div>

                           

              


                                            
                                
                        <div class="col-md-12">
                            <hr style="margin:30px 0 0;">
                        </div>
                        <h6 style="color: #0075be;margin: 20px 0 0px;">Required Documents</h6>
                         <div id="upload_id_proof">
                            <label style="font-size: 15px; margin-top:15px"><b>Driver License <span style="color: red;">*</span> </b><br></label>
                            
                            <div class="FileUpload">
                                <div class="wrapper id_proof_required" style="margin-bottom: 0px;">
                                    <input type="hidden" id="uploaded-links-input" name="id_proof" value="" required>
                                    <div class="invalid-feedback">
                                		Driver License is required
                            		</div>
                                    <div class="upload">
                                        <p>Drag files here or <span class="upload__button clickable_id_proof dz-clickable">Browse</span></p>
                                    </div>
									
                                    <ul id="sortable_id_proof" style="padding-left: 0;width: 100%;">
                                    
                                    </ul>
                                </div>
                            </div>
                                
                              
                                
                        </div>
                                
                                  <div class="g-recaptcha" data-sitekey="6Lem0r0qAAAAABrvaDsWTEVecvVzR0q9oDNIb3WK" style="margin-top:25px;"></div>
                        
                    <br>
                    <p>
                        <input type="submit" id="contact-submit" class="submit" value="Submit" style="float:left;"/>
                    </p>
                </form>
            </div>
        </div>
    </section>

    <?php include_once 'footer.php'; ?>
    
  
 
<script>
    function initAutocomplete() {
        var input = document.getElementById("ship_address");
        var autocomplete = new google.maps.places.Autocomplete(input, {
            componentRestrictions: { country: "CA" } // Restrict search to Canada (optional)
        });

        autocomplete.addListener("place_changed", function () {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                alert("No details available for input: '" + place.name + "'");
                return;
            }

            let addressComponents = {
                street_number: "",
                route: "",
                locality: "",
                administrative_area_level_1: "",
                postal_code: "",
                country: ""
            };

            place.address_components.forEach(component => {
                let componentType = component.types[0];
                if (addressComponents.hasOwnProperty(componentType)) {
                    addressComponents[componentType] = component.long_name;
                }
            });

            // Autofill Fields
            document.getElementById("ship_address").value = addressComponents.street_number + " " + addressComponents.route;
            document.getElementById("locality").value = addressComponents.locality;
            document.getElementById("state").value = addressComponents.administrative_area_level_1;
            document.getElementById("postcode").value = addressComponents.postal_code;
            document.getElementById("country").value = addressComponents.country;
        });
    }
</script>



    <script src="assets/js/jquery.repeater.min.js"></script>
    <script src="assets/js/foundation-datepicker.js"></script>
      <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDak-JxDYbQ7l9CGSkSHDaUPy7rmLBEUEw&callback=initAutocomplete&libraries=places&v=weekly" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
 
    <script src="https://js.upload.io/upload-js/v2"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.3.0/dropzone.js"></script>
    <!-- Demo ads. Please ignore and remove. -->
    <!-- <script src="http://cdn.tutorialzine.com/misc/enhance/v3.js" async></script> -->
    <script>
        const submitButton = document.querySelector('.submit');

        var uploaded_id_proof = <?php echo isset($rowUser['id_proof']) ? json_encode(explode('<br>', $rowUser['id_proof'])) : '[]' ?>;
        var myIdProofDropzone = new Dropzone("#upload_id_proof", {
            url: "../upload_local.php", // Specify the upload URL
            maxFilesize: 10, // Set your desired max file size
            acceptedFiles: "image/png,.jpeg,.jpg,.jpg,.tiff,application/pdf,.txt,.doc,.docx,.xls,.xlsx,.csv",
            parallelUploads: 1,
            previewsContainer: false,
            init: function () {

                this.on("addedfile", function(file) {
                    submitButton.disabled = true;
                    var fileName = file.name;
                    var fileSize = formatFileSize(file.size);

                    // Check if the file with the same name already exists
                    if (!fileExists(fileName, uploaded_id_proof)) {
                        var li = $("<li class='ui-state-default uploaded'><img class='rounded' id='' src='../assets/images/default-picture.png'><div class='file'><div class='file__name'><p data-dz-name class='uploaded_filename'>" + fileName + "</p><p data-dz-size>" + fileSize + "</p><i data-dz-remove class='delete-button fas fa-times'></i></div><div class='progress'><div class='progress-bar bg-success progress-bar-striped progress-bar-animated' data-dz-uploadprogress aria-valuemin='0' aria-valuemax='100' aria-valuenow='0'></div></div></div></li>");

                        li.find(".delete-button").click(function () {
                            removeItem(li, '#sortable_id_proof', myIdProofDropzone, file);
                        });

                        $("#sortable_id_proof").append(li);
                    } else {
                        // File with the same name already exists, skip processing
                        this.removeFile(file);
                        createAlert("Duplicate file: " + fileName, "", "", "warning", true, true, "pageMessages");
                    }
                });

                this.on("uploadprogress", function (file, progress, bytesSent) {
                    var fileName = file.name;
                    var li = $("#sortable_id_proof li:contains('" + fileName + "')");
                    
                    var progressBar = li.find(".progress-bar")[0];

                    progressBar.style.width = progress + "%";
                    progressBar.setAttribute("aria-valuenow", progress);
                });

                this.on("error", function (file, errorMessage) {
                    var fileName = file.name;
                    var li = $("#sortable_id_proof li:contains('" + fileName + "')");
                    li.remove();

                    createAlert(errorMessage, "", "", "danger", true, true, "pageMessages");
                });

                this.on("success", function (file, response) {
                    var fileName = file.name;

                    myIdProofDropzone.removeFile(file);
                    uploaded_id_proof.push(response);

                    console.log(uploaded_id_proof);

                    var li = $("#sortable_id_proof li:contains('" + fileName + "')");

                    var fileThumbnail = getFileThumbnail(response);
                    // Set the thumbnail path to the img src attribute
                    li.find("img").attr("src", fileThumbnail.thumbnail);
                    li.find("img").attr("id", response);

                
                    updateInputField("#sortable_id_proof");
                    submitButton.disabled = false; 

                    document.querySelector(".id_proof_required").style.border = " border: 2px solid red";
                    var errorLabel = document.querySelector("#errorLabel");
                    if (errorLabel) {errorLabel.remove();}

                    createAlert('', 'Nice Work!', 'Documents have been added', 'success', true, true, 'pageMessages');
                });
            },
            clickable: ".clickable_id_proof" // Define the element that should be used as click trigger to select files.
        });
        function formatFileSize(size) {
            var units = ["B", "KB", "MB", "GB", "TB"];
            var i = 0;
            while (size > 1024) {
                size /= 1024;
                i++;
            }
            return size.toFixed(2) + " " + units[i];
        }

        function getFileThumbnail(url) {
            var ext = getFileExtensionFromUrl(url); // Get extension
            var thumbnailPath = "";

            // Check extension
            if (ext === 'pdf') {
                thumbnailPath = "../assets/images/pdf_icon.png"; // default image path
            } else if (ext === 'docx') {
                thumbnailPath = "../assets/images/word_icon.png"; // default image path
            } else if (ext === 'doc') {
                thumbnailPath = "../assets/images/doc_icon.png"; // default image path
            } else if (ext === 'txt') {
                thumbnailPath = "../assets/images/txt_icon.png"; // default image path
            } else if (ext === 'xls') {
                thumbnailPath = "../assets/images/xls_icon.png"; // default image path
            } else if (ext === 'xlsx') {
                thumbnailPath = "../assets/images/xlsx_icon.png"; // default image path
            } else if (ext === 'csv') {
                thumbnailPath = "../assets/images/csv_icon.png"; // default image path for CSV
            } else {
                // For non-image files, return the original URL as well
                return { thumbnail: url, original: url };
            }

            return { thumbnail: thumbnailPath, original: url };
        }

        function getFileExtensionFromUrl(url) {
            var pathArray = url.split('.');
            if (pathArray.length > 1) {
                var extension = pathArray.pop().toLowerCase();
                return extension;
            }
            return null;
        }

        function fileExists(fileName, inputUrls) {
            // Extract the filename without path and timestamp
            var cleanFileName = fileName.replace(/^.*[\\\/]/, '').replace(/\.\w+$/, '');

            console.log('Clean FileName:', cleanFileName);

            return inputUrls.some(function (uploadedFile) {
                // Extract the filename without path and timestamp from uploaded file
                var fileNameWithTimestamp = uploadedFile.replace(/^.*[\\\/]/, '');
                var fileNameWithoutTimestamp = fileNameWithTimestamp.replace(/_\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}\.\w+$/, '').replace(/\.\w+$/, '');

                console.log('Clean Uploaded FileName:', fileNameWithoutTimestamp);

                // Compare filenames (case-insensitive)
                var exists = fileNameWithoutTimestamp.toLowerCase() === cleanFileName.toLowerCase();

                console.log('File Exists:', exists);

                return exists;
            });
        }

        function removeItem(li, selector, dropzone, current_file) {
            dropzone.removeFile(current_file);
            
            var img = li.find("img");
            var fullUrl = img.attr("id");

            // Extract the filename from the URL
            var parts = fullUrl.split('/');
            var filename = parts[parts.length - 1];

            // Replace spaces with underscores
            filename = filename.replace(/ /g, "_");

            console.log(filename);

            // Send an AJAX request to delete the file from your server
            $.ajax({
                url: "../delete.php",
                type: "POST",
                data: {
                    filename: filename
                },
                success: function (response) {
                    console.log(response);
                    if (response !== '') {
                        createAlert(response, "", "", "danger", true, true, "pageMessages");
                    }
                },
                error: function (error) {
                    console.error("Error deleting file:", error);
                }
            });

            // Update the hidden input field
            var inputField = $(selector).siblings('input');
            var currentValues = inputField.val().split('<br>');
            currentValues.splice(currentValues.indexOf(filename), 1);
            inputField.val(currentValues.join('<br>'));

            submitButton.disabled = false;
            // Call default removedfile function to remove the file preview
            li.remove();
            updateInputField(selector);
        }

        function updateInputField(selector) {
            var sortedValues = $(selector + " li").map(function () {
                return $(this).find("img").attr("id");
            }).get().join('<br>');
            
            var siblingInput = $(selector).siblings('input');
            var siblingName = siblingInput.attr('name');
            
            console.log("Sibling Name:", siblingName);
            
            siblingInput.val(sortedValues);

            // $.ajax({
            //     type: "POST",
            //     url: "../update_document_uploads.php",
            //     data: {
            //         inputName: siblingName,
            //         inputValue: sortedValues
            //     },
            //     success: function(response) {
            //         console.log("Data updated successfully:", response);
            //     },
            //     error: function(error) {
            //         console.error("Error updating data:", error);
            //     }
            // });

            console.log(selector);
        }
        function createAlert(title, summary, details, severity, dismissible, autoDismiss, appendToId) {
            var iconMap = {
                info: "fa fa-info-circle",
                success: "fa fa-thumbs-up",
                warning: "fa fa-exclamation-triangle",
                danger: "fa ffa fa-exclamation-circle"
            };

            var iconAdded = false;

            var alertClasses = ["alert", "animated", "flipInX"];
            alertClasses.push("alert-" + severity.toLowerCase());

            if (dismissible) {
                alertClasses.push("alert-dismissible");
            }

            var msgIcon = $("<i />", {
                "class": iconMap[severity] // you need to quote "class" since it's a reserved keyword
            });

            var msg = $("<div />", {
                "class": alertClasses.join(" ") // you need to quote "class" since it's a reserved keyword
            });

            if (title) {
                var msgTitle = $("<strong />", {
                    html: title
                }).appendTo(msg);

                if (!iconAdded) {
                    msgTitle.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (summary) {
                var msgSummary = $("<strong />", {
                    html: summary
                }).appendTo(msg);

                if (!iconAdded) {
                    msgSummary.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (details) {
                var msgDetails = $("<h6 />", {
                    html: details
                }).appendTo(msg);

                if (!iconAdded) {
                    msgDetails.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (dismissible) {
                var msgClose = $("<span />", {
                    "class": "close",
                    "data-dismiss": "alert",
                    html: "<i class='fa fa-times-circle'></i>"
                }).appendTo(msg);

                msgClose.on("click", function() {
                    msg.remove();
                });
            }

            $('#' + appendToId).prepend(msg);
            msg.fadeIn();

            if (autoDismiss) {
                setTimeout(function() {
                    msg.removeClass("flipInX").addClass("flipOutX");
                    setTimeout(function() {
                        msg.fadeOut(function() {
                            msg.remove();
                        });
                    }, 1000);
                }, 5000);
            }
        }
    </script>
    
    
    <script>
        $(document).ready(function() {
            var emailSent = new URLSearchParams(window.location.search).get("email_sent");
            if (emailSent === "success") {
                $('#emailSentModal').modal('show');
            }
        });
		function corpType(val){
        	if(val =='Named'){
            	$('#name_corp').css('display','block');
            }else if(val =='Number'){
            	$('#name_corp').css('display','none');
            }
        } 
	function createAlert(title, summary, details, severity, dismissible, autoDismiss, appendToId) {
            var iconMap = {
                info: "fa fa-info-circle",
                success: "fa fa-thumbs-up",
                warning: "fa fa-exclamation-triangle",
                danger: "fa ffa fa-exclamation-circle"
            };

            var iconAdded = false;

            var alertClasses = ["alert", "animated", "flipInX"];
            alertClasses.push("alert-" + severity.toLowerCase());

            if (dismissible) {
                alertClasses.push("alert-dismissible");
            }

            var msgIcon = $("<i />", {
                "class": iconMap[severity] // you need to quote "class" since it's a reserved keyword
            });

            var msg = $("<div />", {
                "class": alertClasses.join(" ") // you need to quote "class" since it's a reserved keyword
            });

            if (title) {
                var msgTitle = $("<strong />", {
                    html: title
                }).appendTo(msg);

                if (!iconAdded) {
                    msgTitle.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (summary) {
                var msgSummary = $("<strong />", {
                    html: summary
                }).appendTo(msg);

                if (!iconAdded) {
                    msgSummary.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (details) {
                var msgDetails = $("<h6 />", {
                    html: details
                }).appendTo(msg);

                if (!iconAdded) {
                    msgDetails.prepend(msgIcon);
                    iconAdded = true;
                }
            }

            if (dismissible) {
                var msgClose = $("<span />", {
                    "class": "close",
                    "data-dismiss": "alert",
                    html: "<i class='fa fa-times-circle'></i>"
                }).appendTo(msg);

                msgClose.on("click", function() {
                    msg.remove();
                });
            }

            $('#' + appendToId).prepend(msg);
            msg.fadeIn();

            if (autoDismiss) {
                setTimeout(function() {
                    msg.removeClass("flipInX").addClass("flipOutX");
                    setTimeout(function() {
                        msg.fadeOut(function() {
                            msg.remove();
                        });
                    }, 1000);
                }, 5000);
            }
        }
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
            firstName: {
                presence: {
                    allowEmpty: false
                },
                firstName: true
            },
    		id_proof: {
                presence: {
                    allowEmpty: false
                },
                id_proof: true
            },
            email: {
                presence: {
                    allowEmpty: false
                },
                email: true
            },
            sin_number: {
                presence: {
                    allowEmpty: false
                },
                sin_number: true
            },
            phone: {
                presence: {
                    allowEmpty: false
                },
                phone:true
            },
            business_activity: {
                presence: {
                    allowEmpty: false
                },
                business_activity:true
            },
            corporation_type: {
                presence: {
                    allowEmpty: false
                },
                corporation_type:true
            },
        };

        const form = document.getElementById('contact-form');

        form.addEventListener('submit', function(event) {
            
        	var uploadIdProofCheck2 = document.getElementById("uploaded-links-input");
    		var uploadIdProofContainer = document.getElementById("upload_id_proof");

        
          if (uploadIdProofCheck2.value.trim() === '') {
          
                // Display error message
                var errorLabel = document.querySelector("#errorLabel");

                if (!errorLabel) {
                    errorLabel = document.createElement("label");
                    errorLabel.innerHTML = "Driver License is required";
                    errorLabel.style.color = "red";
                    errorLabel.id = "errorLabel";
                    document.querySelector(".id_proof_required").appendChild(errorLabel);
                    document.querySelector(".id_proof_required").style.border = "2px solid red";
                }

                // Prevent form submission
                event.preventDefault();
          
          uploadIdProofContainer.scrollIntoView();

        const dangerAlert = `
            <div class="alert alert-danger alert-dismissible bg-danger border-danger text-white alert-label-icon shadow fade show" role="alert">
                <strong>Please Fill In All Required Fields</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        document.querySelector(".form_append_error").innerHTML = dangerAlert;
        createAlert(
            'Please fill in all required fields.',
            '',
            '',
            'danger',
            true,
            true,
            'pageMessages'
        );
            } else {
                // Remove error label if it exists
                var errorLabel = document.querySelector("#errorLabel");
                if (errorLabel) {
                    errorLabel.remove();
                }

            }
        
    
            const formValues = {
                firstName: form.elements.firstName.value,
                lastName: form.elements.lastName.value,
                sin_number: form.elements.sin_number.value,
                phone: form.elements.phone.value,
                email: form.elements.email.value,
                business_activity: form.elements.business_activity.value
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

            const dangerAlert = `
                <div class="alert alert-danger alert-dismissible bg-danger border-danger text-white alert-label-icon shadow fade show" role="alert">
               	<strong>Please Fill In All Required Fields</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
        		document.querySelector(".form_append_error").innerHTML = dangerAlert;
            }
        }, false);
    </script>

</body>

</html>