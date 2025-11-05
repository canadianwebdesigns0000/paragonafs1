<?php
    session_start();
    include '../config.php';

if (!isset($_SESSION['user'])) {
    $output = responseError('Session is destroyed');
    die(json_encode($output));
}

$input = [];
$input['password'] = post('password');
$input['cpassword'] = post('cpassword');

$output = responseFormErrors($input);

foreach ($output['errors'] as $key => $out) {
    if($key == 'cpassword') {
        $output['errors'][$key] = preg_replace('/c/', 'confirm ', $output['errors'][$key]);
    }
}

if($output['error']) {
    echo json_encode($output);
    die;
}

if (strlen($input['password']) < 6) {
    $output['status'] = 'fail';
    $output['errors']['password'] = array('Password must be greater than 6 character');

} else if ($input['password'] != $input['cpassword']) {
    $output['status'] = 'fail';
    $output['errors']['cpassword'] = array('Password and confirm password do not match');

} else {

    // Encrypt password according to encryption type defined in config.php
    if ($encryptionType == 'sha1') {
        $input['password'] = sha1($input['password']);

    } elseif ($encryptionType == 'md5') {
        $input['password'] = md5($input['password']);
    }

    $query = 'UPDATE `users` SET password = ? where id='. post('id');
    $statement = $db->prepare($query);
    $statement->execute(array($input['password']));

    $output = responseSuccess('Password Changed successfully');
}
    echo json_encode($output);
?>