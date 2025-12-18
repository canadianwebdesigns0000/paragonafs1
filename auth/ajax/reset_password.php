<?php
    session_start();
    include '../config.php';

    $password   = post('password');
    $cpassword  = post('cpassword');
    $forgetKey = post('key');

// SELECT MATCH FROM THE DATABASE
if ($password == '' && $cpassword == '') {
    $output = responseError('Both of fields are required.');

} else if (strlen($password) < 6) {
    $output = responseError('Password must be greater than 6 character');

} else if ($password != $cpassword) {
    $output = responseError('Password and confirm password do not match');

} else {

    // Encrypt password according to encryption type defined in config.php
    if($encryptionType == 'sha1') {
        $password = sha1($password);

    } elseif ($encryptionType == 'md5') {
        $password = md5($password);
    }

    $query     = 'UPDATE `users` SET `password` = ? where `forget_key`=?';
    $statement = $db->prepare($query);
    $statement->execute(array($password,$forgetKey));

    $output   = responseSuccess('Password Changed successfully. Click <a href=\'/form/\'> <strong>here</strong></a> to Login', '', '');
}
echo json_encode($output);