<?php
session_start();
  include '../../config.php';

// ADMIN SESSION CHECK SET OR NOT
if(!isset($_SESSION['username']))
{
      $output = responseError('Session is destroyed');
      die(json_encode($output));
}

$output = [];
$input = [];

$input['name'] = post('name');
$input['username'] = post('username');
$input['email'] = post('email');

// server side validation
// check name and email field is entered or not
$output = responseFormErrors($input);

if($output['error']) {
    echo json_encode($output);
    die;
}

// Email validation
if(!filter_var($input['email'], FILTER_VALIDATE_EMAIL))
{
    $output = responseError('Enter correct Email ID');

}else {
    // Insert into database

    $query  = 'UPDATE `admin` SET name = ? , email = ? , username = ? where id = '.post('id');
    $parameters = array($input['name'], $input['email'],$input['username']);
    $statement  = $db->prepare($query);
    $statement->execute($parameters);
    
    $statement = $db->prepare('SELECT * FROM `admin` where id=?');
    $statement->execute(array(post('id')));
    $data = $statement->fetch(PDO::FETCH_ASSOC);
    
    
    $_SESSION['name']	=	$data['name'];
    $_SESSION['username']	=	$data['username'];

    $output = responseSuccess('Profile Details Changed successfully');

}

  // output json
  echo json_encode($output);
?>