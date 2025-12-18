<?php
session_start();
  include '../../config.php';

// ADMIN SESSION CHECK SET OR NOT
if(!isset($_SESSION['username']))
{
    $output = responseError('Session is destroyed');
    die(json_encode($output));
}

// Server side validation
// check if password and confirm password matched or not

$output = [];
$input = [];
$input['password'] = post('password');
$input['cpassword'] = post('cpassword');

$output = responseFormErrors($input);


if($output['error']) {

    foreach ($output['errors'] as $key => $out) {
        $output['errors'][$key] = preg_replace('/c/', 'confirm ', $out[0]);
    }

    echo json_encode($output);
    die;
}

if($input['password'] != $input['cpassword'] ) {

    $output = responseError('Password and confirm password do not match');

} else if(strlen($input['password']) < 5) // Length of password must be greater than 5 character
{
    $output = responseError('Password must be greater than 5 character');

}else{

    // Encrypt password according to encryption type defined in config.php
    if($encryptionType == 'sha1') {
        $input['password'] = sha1($input['password']);
    }
    elseif ($encryptionType == 'md5')
    {
        $input['password'] = md5($input['password']);
    }

    // Update database with new password
    $query      = 'UPDATE `admin` SET password = ? where id='.post('id');
    $parameters = array($input['password']);
    $statement  = $db->prepare($query);
    $statement->execute($parameters);

    $output = responseSuccess('Password Changed successfully');
}
   // output the json format
  echo json_encode($output);
?>