<?php
    session_start();
    include '../config.php';
    error_reporting(0);
// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
}

    $userId = $_GET['id'];

    // Query To Get User Data
    $userData = $db->prepare('SELECT * FROM users WHERE id=?');
    $userData->execute(array($userId));
    $rowUser = $userData->fetch(PDO::FETCH_ASSOC);

    $image = file_exists('../'.$path.$rowUser['avatar']);
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
?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-green" style="width: 100%;">
            <i class="icon-pencil font-green"></i>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <span class="caption-subject bold uppercase">Edit User</span>
        </div>
    </div>
    <form id="userUpdate" enctype="multipart/form-data">
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group form-md-line-input">

                        <input type="hidden" name="id" value="<?php echo $rowUser['id'] ?>">
                        <input type="text" id="nameUpdate" name="nameUpdate" class="form-control input-sm" data-required="true"
                               value="<?php echo $rowUser['name'] ?>">
                        <label for="name" class="control-label">Name</label>
                        <span class="form-control-focus"> </span>
                    </div>

                    <div class="form-group form-md-line-input">

                        <input type="text" id="usernameUpdate" name="usernameUpdate" class="form-control input-sm"
                               data-required="true" value="<?php echo $rowUser['username'] ?>">
                        <label for="username" class="control-label">Username</label>
                        <span class="form-control-focus"> </span>
                    </div>
                    <div class="form-group form-md-line-input">
                        <input type="email" id="emailUpdate" name="emailUpdate" class="form-control input-sm"
                               data-parsley-type="email" data-required="true"
                               value="<?php echo $rowUser['email'] ?>">
                        <label for="email" class="control-label">Email</label>
                        <span class="form-control-focus"> </span>
                    </div>
                    <div class="form-group form-md-line-input form-md-floating-label">
                        <input type="text" id="passwordUpdate" name="passwordUpdate" class="form-control input-sm"
                               data-required="true" value="">
                        <label for="password" class="control-label">Password</label>
                        <span class="form-control-focus"> </span>
                        <span class="form-control-info"> (leave blank if you do not want to change password) </span>
                    </div>


                    <div class="form-group form-md-radios">
                        <label for="status">Status</label>
                        <div class="form-md-radios md-radio-inline">
                            <div class="md-radio">
                                <input type="radio" id="radio14" name="statusUpdate" class="md-radiobtn" value="enable" <?php echo ($rowUser['status'] === 'enable') ? 'checked' : ''; ?>>
                                <label for="radio14">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Enable</label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" id="radio15" name="statusUpdate" class="md-radiobtn" value="disable" <?php echo ($rowUser['status'] === 'disable') ? 'checked' : ''; ?>>
                                <label for="radio15">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Disable </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group form-md-line-input form-md-radios">
                        <label for="email_verified">Verified Email</label>
                        <div class="form-md-radios md-radio-inline ">
                            <div class="md-radio">
                                <input type="radio" id="radio16" name="emailVerifiedUpdate" class="md-radiobtn" value="yes" <?php echo ($rowUser['email_verified'] === 'yes') ? 'checked' : ''; ?>>
                                <label for="radio16">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> Yes - Email is verified</label>
                            </div>
                            <div class="md-radio">
                                <input type="radio" id="radio17" name="emailVerifiedUpdate" class="md-radiobtn" value="no" <?php echo ($rowUser['email_verified'] === 'no') ? 'checked' : ''; ?>>
                                <label for="radio17">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> No - Email is not verified </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group ">
                        <div class="fileinput fileinput-new" data-provides="fileinput">
                            <div class="fileinput-new thumbnail" style="width: 200px; height: 150px;">
                                <img  src="<?php echo ($image == 1) ? ('../'.$path.$rowUser['avatar']) : 'http://www.placehold.it/200x150/EFEFEF/AAAAAA'; ?>" alt="" /> </div>
                            <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"> </div>
                            <div>
                            <span class="btn btn-outline btn-file">
                            <span class="fileinput-new"> Select image </span>
                            <span class="fileinput-exists"> Change </span>
                            <input type="file" name="image" id="image"> </span>
                                <a href="javascript:;" class="btn red fileinput-exists btn-outline" data-dismiss="fileinput"> Remove </a>
                            </div>
                        </div>
                        <div class="clearfix margin-top-10">
                            <span class="label label-danger">NOTE!</span> Image preview only works in IE10+, FF3.6+, Safari6.0+, Chrome6.0+ and Opera11.1+. In older browsers the filename is shown instead. </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn dark btn-outline">Close</button>
            <button type="submit" class="btn green btn-outline" name="submit" onclick="updateUser();return false">Submit</button>
        </div>
    </form>
</div>

