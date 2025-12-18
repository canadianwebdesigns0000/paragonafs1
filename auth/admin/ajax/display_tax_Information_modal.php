<?php
    session_start();
    include '../../config.php';
    error_reporting(0);
// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

    $user_email = $_POST['email'];

    // Query To Get User Data
    $userData = $db->prepare('
        SELECT 
            users.id AS user_id,
            users.first_name AS user_first_name,
            users.last_name AS user_last_name,
            users.phone AS user_phone,
            users.email AS user_email,
            users.email_verified AS user_email_verified,
            users.lastlogin_at AS user_lastlogin_at,
            users.created_at AS user_created_at,
            tax_information.*
        FROM users
        LEFT JOIN tax_information ON users.email = tax_information.email
        WHERE tax_information.email = ?');
    $userData->execute(array($user_email));
    $row = $userData->fetch(PDO::FETCH_ASSOC);


    // Function to Encrypt Decrypt String
    function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        // Update your secret key before use
        $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/';
        // Update your secret iv before use
        $secret_iv = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';
        // hash
        $key = hash('sha256', $secret_key);
        
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    function formatDate($date) {
        // Convert the date to a DateTime object
        $dateTimeObj = DateTime::createFromFormat('m/d/Y', $date);
        
        // Format the date
        $formattedDate = $dateTimeObj->format('M j, Y');
        
        return $formattedDate;
    }
?>

<style>
    #demo p.fw-semibold {
        color: #0075BE;
    }
</style>

<div class="card" id="demo">
    <div class="row">

        <div class="col-lg-12">
            <div class="card-header border-bottom-dashed p-4">
                <div class="d-flex">

                    <div class="flex-grow-1">
                        <img src="assets/images/paragon_logo.png" class="card-logo card-logo-dark" alt="logo dark" height="60">
                        <img src="assets/images/paragon_logo.png" class="card-logo card-logo-light" alt="logo light" height="60">
                        
                        <div class="mt-sm-4 mt-3">
                            <h6 class="text-uppercase fw-semibold">Address</h6>
                            <p class="mb-1">#19A - 1, Bartley Bull Pkwy,</p>
                            <p class="mb-0">Brampton, Ontario L6W 3T7</p>
                        </div>
                    </div>


                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                        <h6><span class="fw-normal">Website:</span> <a href="https://paragonafs.ca/" class="link-primary" target="_blank" id="website">www.paragonafs.ca</a></h6>
                        <h6><span class="fw-normal">Email:</span><span id="email"> info@paragonafs.ca</span></h6>
                        <h6><span class="fw-normal">Office Number: </span><span id="contact-no"> +1 (416) 477 3359</span></h6>
                        <h6><span class="fw-normal">Phone Number: </span><span id="contact-no"> +1 (647) 909 8484</span></h6>
                        <h6 class="mb-0"><span class="fw-normal">Phone Number: </span><span id="contact-no"> +1 (437) 881 9175</span></h6>
                    </div>
                    

                </div>
            </div>
            <!--end card-header-->
        </div>

        <!--end col-->
        <div class="col-lg-12">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">First Name</p>
                        <h5 class="fs-14 mb-0"><?= $row['user_first_name']?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Last Name</p>
                        <h5 class="fs-14 mb-0"><?= $row['user_last_name']?></h5>
                    </div>
                    <!--end col-->
                    <?php if(isset($row['birth_date']) && !empty(trim($row['birth_date']))): ?>
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Date of Birth</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['birth_date']) ?></h5>
                    </div>
                    <?php endif; ?>
                    <!--end col-->
                    <?php if(isset($row['sin_number']) && !empty(trim($row['sin_number']))): ?>
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">SIN Number</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['sin_number']) ?></h5>
                    </div>
                    <?php endif; ?>
                    <!--end col-->
                    <?php if(isset($row['gender']) && !empty(trim($row['gender']))): ?>
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Gender</p>
                        <h5 class="fs-14 mb-0"><?= $row['gender']?></h5>
                    </div>
                    <?php endif; ?>
                    <!--end col-->
                    <div class="col-lg-3 col-4">    
                        <p class="mb-2 fw-semibold">Phone</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['user_phone']) ?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Email</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['user_email'])?></h5>
                    </div>
                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <!--end col-->
        <?php if(isset($row['ship_address']) && !empty(trim($row['ship_address']))): ?>

        <div class="col-lg-12">
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="row g-3">
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Street</p>
                        <h5 class="fs-14 mb-0"><?= $row['ship_address']?></h5>
                    </div>
                    <!--end col-->
                <?php if(isset($row['apartment_unit_number']) && !empty(trim($row['apartment_unit_number']))): ?>
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Apartment / Unit #</p>
                        <h5 class="fs-14 mb-0"><?= $row['apartment_unit_number']?></h5>
                    </div>
                <?php endif; ?>
                    <!--end col-->
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">City</p>
                        <h5 class="fs-14 mb-0"><?= $row['locality']?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">State/Province</p>
                        <h5 class="fs-14 mb-0"><?= $row['state']?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Postal Code</p>
                        <h5 class="fs-14 mb-0"><?= $row['postcode']?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4 col-4">    
                        <p class="mb-2 fw-semibold">Country/Region</p>
                        <h5 class="fs-14 mb-0"><?= $row['country']?></h5>
                    </div>
                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <!--end col-->

        <div class="col-lg-12">
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="row g-3">

                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Did you move to another province?</p>
                        <h5 class="fs-14 mb-0"><?= $row['another_province']?></h5>
                    </div>

                    
                <?php if($row['another_province'] !== 'No'): ?>
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">When did you move?</p>
                        <h5 class="fs-14 mb-0"><?= $row['move_date']?></h5>
                    </div>
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Province moved From?</p>
                        <h5 class="fs-14 mb-0"><?= $row['move_from']?></h5>
                    </div>
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Province moved To?</p>
                        <h5 class="fs-14 mb-0"><?= $row['move_to']?></h5>
                    </div>
                <?php endif; ?>
                    

                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Is this the first time you are filing tax?</p>
                        <h5 class="fs-14 mb-0"><?= $row['first_fillingtax']?></h5>
                    </div>


                <?php if($row['first_fillingtax'] !== 'Yes'): ?>
                    <div class="col-lg-6 col-6">
                        <p class="mb-2 fw-semibold">Did you file earlier with Paragon Tax Services?</p>
                        <h5 class="fs-14 mb-0"><?= $row['file_paragon']?></h5>
                    </div>
                    
                    <div class="col-lg-6 col-6">
                        <p class="mb-2 fw-semibold">Which years do you want to file tax returns?</p>
                        <h5 class="fs-14 mb-0"><?= $row['years_tax_return']?></h5>
                    </div>
                <?php endif; ?>


                <?php if($row['first_fillingtax'] !== 'No'): ?>
                    <div class="col-lg-6 col-6">
                        <p class="mb-2 fw-semibold">Date of Entry in Canada</p>
                        <h5 class="fs-14 mb-0"><?= $row['canada_entry']?></h5>
                    </div>
                    
                    <div class="col-lg-6 col-6">
                        <p class="mb-2 fw-semibold">Birth Country</p>
                        <h5 class="fs-14 mb-0"><?= $row['birth_country']?></h5>
                    </div>

                    <div class="col-lg-12 col-12">
                        <p class="mb-0 fw-semibold">What was your world income in last 3 years before coming to Canada (in CAD)?</p>
                    </div>

                    <div class="col-lg-2 col-2">
                        <h5 class="fs-14 mb-0"><?= $row['year1']?></h5>
                    </div>
                    
                    <div class="col-lg-2 col-2">
                        <h5 class="fs-14 mb-0"><?= $row['year1_income']?></h5>
                    </div>

                    <div class="col-lg-2 col-2">
                        <h5 class="fs-14 mb-0"><?= $row['year2']?></h5>
                    </div>
                    
                    <div class="col-lg-2 col-2">
                        <h5 class="fs-14 mb-0"><?= $row['year2_income']?></h5>
                    </div>

                    <div class="col-lg-2 col-2">
                        <h5 class="fs-14 mb-0"><?= $row['year3']?></h5>
                    </div>
                    
                    <div class="col-lg-2 col-2">
                        <h5 class="fs-14 mb-0"><?= $row['year3_income']?></h5>
                    </div>
                <?php endif; ?>

                    <div class="col-lg-12 col-12">    
                        <p class="mb-2 fw-semibold">Are you first time home buyer?</p>
                        <h5 class="fs-14 mb-0"><?= $row['first_time_buyer']?></h5>
                    </div>

                <?php if($row['first_time_buyer'] !== 'No'): ?>
                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">When did you purchase your first home?</p>
                        <h5 class="fs-14 mb-0"><?= $row['purchase_first_home']?></h5>
                    </div>
                <?php endif; ?>


                    <div class="col-lg-12 col-12">    
                        <p class="mb-2 fw-semibold">Marital Status</p>
                        <h5 class="fs-14 mb-0"><?= $row['marital_status']?></h5>
                    </div>


                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <!--end col-->

        <?php if ($row['marital_status'] === 'Married' || $row['marital_status'] === 'Common in Law'): ?>

        <div class="col-lg-12">
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="row g-3">

                    <div class="col-lg-12 col-12">
                        <h6 class="mb-0 fw-semibold">Spouse Information</h6>
                    </div>

                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">First Name</p>
                        <h5 class="fs-14 mb-0"><?= $row['spouse_first_name']?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Last Name</p>
                        <h5 class="fs-14 mb-0"><?= $row['spouse_last_name']?></h5>
                    </div>

                    <!--end col-->
                    <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Spouse Date of Birth</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_date_birth']) ?></h5>
                    </div>
                        <!--end col-->
                        <div class="col-lg-3 col-4">
                        <p class="mb-2 fw-semibold">Date of Marriage</p>
                        <h5 class="fs-14 mb-0"><?= $row['date_marriage'] ?></h5>
                    </div>

                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Residing in Canada</p>
                        <h5 class="fs-14 mb-0"><?= $row['residing_canada']?></h5>
                    </div>

                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>

        <div class="col-lg-12">
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="row g-3">

                <?php if($row['residing_canada'] !== 'Yes'): ?>
                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Spousal Annual Income outside Canada (Converted to CAD)</p>
                        <h5 class="fs-14 mb-0"><?= $row['spouse_annual_income_outside']?></h5>
                    </div>
                <?php endif; ?>

                <?php if ($row['residing_canada'] !== 'No'): ?>
                    <!--end col-->
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Spouse SIN</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_sin']) ?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4 col-4">    
                        <p class="mb-2 fw-semibold">Email</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_email']) ?></h5>
                    </div>
                    <!--end col-->
                    <div class="col-lg-4 col-4">
                        <p class="mb-2 fw-semibold">Phone</p>
                        <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_phone']) ?></h5>
                    </div>
                <?php endif; ?>

                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        
        <?php if ($row['residing_canada'] !== 'No'): ?>
        <div class="col-lg-12">
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="row g-3">

                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Does your spouse want to file taxes?</p>
                        <h5 class="fs-14 mb-0"><?= $row['spouse_file_tax']?></h5>
                    </div>

                    <?php if ($row['spouse_file_tax'] !== 'Yes'): ?>
                        <div class="col-lg-12 col-12">
                            <p class="mb-2 fw-semibold">Spouse Annual Income in CAD</p>
                            <h5 class="fs-14 mb-0"><?= $row['spouse_annual_income']?></h5>
                        </div>
                    <?php endif; ?>

                    <?php if ($row['spouse_file_tax'] !== 'No'): ?>

                        <div class="col-lg-12 col-12">
                            <p class="mb-2 fw-semibold">Is this the first time your spouse filing tax?</p>
                            <h5 class="fs-14 mb-0"><?= $row['spouse_first_tax']?></h5>
                        </div>
                        
                        <?php if($row['spouse_first_tax'] !== 'Yes'): ?>
                            <div class="col-lg-6 col-6">
                                <p class="mb-2 fw-semibold">Did Your Spouse file earlier with Paragon Tax Services?</p>
                                <h5 class="fs-14 mb-0"><?= $row['spouse_file_paragon']?></h5>
                            </div>
                        
                            <div class="col-lg-6 col-6">
                                <p class="mb-2 fw-semibold">Which Years Your Spouse want to file tax returns?</p>
                                <h5 class="fs-14 mb-0"><?= $row['spouse_years_tax_return']?></h5>
                            </div>
                        <?php endif; ?>


                        <?php if($row['spouse_first_tax'] !== 'No'): ?>
                            <div class="col-lg-6 col-6">
                                <p class="mb-2 fw-semibold">Date of Entry in Canada</p>
                                <h5 class="fs-14 mb-0"><?= $row['spouse_canada_entry'] ?></h5>
                            </div>
                        
                            <div class="col-lg-6 col-6">
                                <p class="mb-2 fw-semibold">Birth Country</p>
                                <h5 class="fs-14 mb-0"><?= $row['spouse_birth_country']?></h5>
                            </div>

                            <div class="col-lg-12 col-12">
                                <p class="mb-0 fw-semibold">What was your spouse world income in last 3 years before coming to Canada (in CAD)?</p>
                            </div>

                            <div class="col-lg-2 col-2">
                                <h5 class="fs-14 mb-0"><?= $row['spouse_year1']?></h5>
                            </div>
                        
                            <div class="col-lg-2 col-2">
                                <h5 class="fs-14 mb-0"><?= $row['spouse_year1_income']?></h5>
                            </div>

                            <div class="col-lg-2 col-2">
                                <h5 class="fs-14 mb-0"><?= $row['spouse_year2']?></h5>
                            </div>
                        
                            <div class="col-lg-2 col-2">
                                <h5 class="fs-14 mb-0"><?= $row['spouse_year2_income']?></h5>
                            </div>

                            <div class="col-lg-2 col-2">
                                <h5 class="fs-14 mb-0"><?= $row['spouse_year3']?></h5>
                            </div>
                        
                            <div class="col-lg-2 col-2">
                                <h5 class="fs-14 mb-0"><?= $row['spouse_year3_income']?></h5>
                            </div>
                        <?php endif; ?>


                    <?php endif; ?>

                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>
        <?php endif; ?>

        <div class="col-lg-12">
            <div class="card-body" style="padding: 0 24px 24px;">
                <div class="row g-3">

                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Do you have child?</p>
                        <h5 class="fs-14 mb-0"><?= $row['have_child']?></h5>
                    </div>

                <?php if ($row['have_child'] !== 'No'): ?>
                    <?php foreach (json_decode($row['child_first_name'], true) as $x): ?>
                        <!--end col-->
                        <div class="col-lg-4 col-4">
                            <p class="mb-2 fw-semibold">Child First Name</p>
                            <h5 class="fs-14 mb-0"><?= $x['child_first_name'] ?></h5>
                        </div>
                        <!--end col-->
                        <div class="col-lg-4 col-4">
                            <p class="mb-2 fw-semibold">Child Last Name</p>
                            <h5 class="fs-14 mb-0"><?= $x['child_last_name'] ?></h5>
                        </div>
                        <!--end col-->
                        <div class="col-lg-4 col-4">
                            <p class="mb-2 fw-semibold">Child Date of Birth</p>
                            <h5 class="fs-14 mb-0"><?= $x['child_date_birth'] ?></h5>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>

        <?php endif; ?>

        <?php if ($row['marital_status'] === 'Separated' || $row['marital_status'] === 'Widow' || $row['marital_status'] === 'Divorced'): ?>
            <div class="col-lg-12">
                <div class="card-body" style="padding: 0 24px 24px;">
                    <div class="row g-3">

                        <div class="col-lg-12 col-12">
                            <p class="mb-2 fw-semibold">Date of Marital Status Chage</p>
                            <h5 class="fs-14 mb-0"><?= $row['marital_change']?></h5>
                        </div>

                    </div>
                    <!--end row-->
                </div>
                <!--end card-body-->
            </div>
        <?php endif; ?>
        

        <div class="col-lg-12">
            <div class="card-body p-4 border-top border-top-dashed">
                <div class="row g-3">

                    <?php if(isset($row['income_delivery']) && !empty(trim($row['income_delivery']))): ?>
                    <div class="col-lg-12 col-12">
                        <p class="mb-2 fw-semibold">Do you have income from Uber/Skip/Lyft/Doordash etc.?</p>
                        <h5 class="fs-14 mb-0"><?= $row['income_delivery']?></h5>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($row['income_delivery'] === 'Yes'): ?>

                        <div class="col-lg-12 col-12">
                            <p class="mb-2 fw-semibold">Do you want to file HST for your Uber/Skip/Lyft/Doordash?</p>
                            <h5 class="fs-14 mb-0"><?= $row['delivery_hst']?></h5>
                        </div>

                        <?php if($row['delivery_hst'] === 'Yes'): ?>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">HST #</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['hst_number']) ?></h5>
                            </div>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">Access code</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['hst_access_code']) ?></h5>
                            </div>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">Start Date</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['hst_start_date']) ?></h5>
                            </div>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">End Date</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['hst_end_date']) ?></h5>
                            </div>
                        <?php endif; ?>
                        
                    <?php endif; ?>
                    

                    <?php if ($row['spouse_file_tax'] === 'Yes'): ?>


                        <?php if(isset($row['spouse_income_delivery']) && !empty(trim($row['spouse_income_delivery']))): ?>
                        <div class="col-lg-12 col-12">
                            <p class="mb-2 fw-semibold">Do your Spouse have income from Uber/Skip/Lyft/Doordash etc.?</p>
                            <h5 class="fs-14 mb-0"><?= $row['spouse_income_delivery']?></h5>
                        </div>
                        <?php endif; ?>


                        <?php if($row['spouse_income_delivery'] === 'Yes'): ?>

                            <div class="col-lg-12 col-12">
                                <p class="mb-2 fw-semibold">Does your Spouse want to file HST for Uber/Skip/Lyft/Doordash?</p>
                                <h5 class="fs-14 mb-0"><?= $row['spouse_delivery_hst']?></h5>
                            </div>

                            <?php if($row['spouse_delivery_hst'] === 'Yes'): ?>

                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">HST #</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_hst_number']) ?></h5>
                            </div>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">Access code</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_hst_access_code']) ?></h5>
                            </div>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">Start Date</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_hst_start_date']) ?></h5>
                            </div>
                            <div class="col-lg-3 col-3">
                                <p class="mb-2 fw-semibold">End Date</p>
                                <h5 class="fs-14 mb-0"><?= encrypt_decrypt('decrypt', $row['spouse_hst_end_date']) ?></h5>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
                <!--end row-->
            </div>
            <!--end card-body-->
        </div>


        <?php endif; ?>

        <!--end col-->
        <!-- <div class="col-lg-12">
            <div class="card-body p-4">
                <div class="mt-4">
                    <div class="alert alert-info">
                        <p class="mb-0"><span class="fw-semibold">NOTES:</span>
                            <span id="note">The Tax Information provided on this page is confidential and intended for authorized use only. Unauthorized access, disclosure, or use of this information is strictly prohibited and may be unlawful.</span>
                        </p>
                    </div>
                </div>
                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                    <a href="javascript:window.print()" class="btn btn-success"><i class="ri-printer-line align-bottom me-1"></i> Print</a>
                    <a href="javascript:void(0);" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download</a>
                </div>
            </div>
        </div> -->
        <!--end col-->

    </div>
    <!--end row-->
</div>