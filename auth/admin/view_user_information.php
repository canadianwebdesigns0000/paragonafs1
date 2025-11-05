<?php
    session_start();
    include '../config.php';
    // error_reporting(0);
// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

    $userEmail = $_GET['email'];

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

    // Query To Get User Data
    $userData = $db->prepare('SELECT * FROM tax_information WHERE email=?');
    $userData->execute(array(encrypt_decrypt('encrypt', $userEmail)));
    $rowUser = $userData->fetch(PDO::FETCH_ASSOC);

    // $image = file_exists('../'.$path.$rowUser['avatar']);

?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="cs-container">
    <div class="cs-invoice cs-style1">
        <div class="cs-invoice_in" id="download_section">

            <div class="cs-heading cs-style1 cs-f18 cs-primary_color cs-mb25 cs-semi_bold"><?= $rowUser['first_name'] ?> Personal Information</div>
            <ul class="cs-grid_row cs-col_2 cs-mb5">
                <li>
                    <p class="cs-mb20"><b class="cs-primary_color">First Name:</b> <br><span class="cs-primary_color"><?= $rowUser['first_name'] ?></span></p>
                    <?php if (!empty($rowUser['birth_date'])): ?>
                        <p class="cs-mb20"><b class="cs-primary_color">Birth Date:</b> <br><span class="cs-primary_color"><?= encrypt_decrypt('decrypt', $rowUser['birth_date']) ?></span></p>
                    <?php endif; ?>
                    <?php if (!empty($rowUser['gender'])): ?>
                        <p class="cs-mb20"><b class="cs-primary_color">Gender:</b> <br><span class="cs-primary_color"><?= $rowUser['gender'] ?></span></p>  
                    <?php endif; ?>
                </li>
                <li>
                    <p class="cs-mb20"><b class="cs-primary_color">Last Name: </b> <br><span class="cs-primary_color"><?= $rowUser['last_name'] ?></span></p>
                    <?php if (!empty($rowUser['sin_number'])): ?>
                        <p class="cs-mb20"><b class="cs-primary_color">SIN Number:</b> <br><span class="cs-primary_color"><?= encrypt_decrypt('decrypt', $rowUser['sin_number']) ?></span></p>
                    <?php endif; ?>
                </li>
            </ul>

            <div class="cs-heading cs-style1 cs-f18 cs-primary_color cs-mb25 cs-semi_bold">Address Information</div>
            <ul class="cs-grid_row cs-col_2 cs-mb5">
                <li>
                    <p class="cs-mb20"><b class="cs-primary_color">Street:</b> <br><span class="cs-primary_color"><?= $rowUser['ship_address'] ?></span></p>
                    <p class="cs-mb20"><b class="cs-primary_color">City:</b> <br><span class="cs-primary_color"><?= $rowUser['locality'] ?></span></p>
                    <p class="cs-mb20"><b class="cs-primary_color">Postal Code:</b> <br><span class="cs-primary_color"><?= $rowUser['postcode'] ?></span></p>  
                </li>
                <li>
                <?php if (!empty($rowUser['apartment_unit_number'])): ?>
                    <p class="cs-mb20"><b class="cs-primary_color">Apartment / Unit Number:</b> <br><span class="cs-primary_color"><?= $rowUser['apartment_unit_number'] ?></span></p>
                <?php endif; ?>                    
                    <p class="cs-mb20"><b class="cs-primary_color">State/Province:</b> <br><span class="cs-primary_color"><?= $rowUser['state'] ?></span></p>
                    <p class="cs-mb20"><b class="cs-primary_color">Country:</b> <br><span class="cs-primary_color"><?= $rowUser['country'] ?></span></p>
                </li>
            </ul>

            <div class="cs-heading cs-style1 cs-f18 cs-primary_color cs-mb25 cs-semi_bold">Contact Information</div>
            <ul class="cs-grid_row cs-col_2 cs-mb5">
                <li>
                    <p class="cs-mb20"><b class="cs-primary_color">Phone:</b> <br><span class="cs-primary_color"><?= encrypt_decrypt('decrypt', $rowUser['phone']) ?></span></p>
                </li>
                <li>
                    <p class="cs-mb20"><b class="cs-primary_color">Email:</b> <br><span class="cs-primary_color"><?= encrypt_decrypt('decrypt', $rowUser['email']) ?></span></p>
                </li>
            </ul>

            <div class="cs-heading cs-style1 cs-f18 cs-primary_color cs-mb25 cs-semi_bold">Other Information</div>
            <ul class="cs-grid_row cs-col_1 cs-mb5">
                <li>
                    <p class="cs-mb20"><b class="cs-primary_color">Did you move to another province?</b> <br><span class="cs-primary_color"><?= $rowUser['another_province'] ?></span></p>
                    <p class="cs-mb20"><b class="cs-primary_color">Email:</b> <br><span class="cs-primary_color"><?= $rowUser['country'] ?></span></p>
                </li>
            </ul>

        </div>
    </div>
</div>



<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
    <!-- <button type="submit" class="btn green btn-outline" name="submit" onclick="updateUser();return false">Submit</button> -->
</div>

