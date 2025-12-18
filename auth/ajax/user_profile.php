<?php
session_start();
include '../config.php';

// user SESSION CHECK SET OR NOT
if (!isset($_SESSION['user'])) {
    header('location:index.php');
    exit();
}

$output = [];
// When submit button is presssed
if (isset($_POST)) {

    $input = [];
    $input['name'] = post('name');
    $input['email'] = post('email');

    $output = responseFormErrors($input);

    if ($output['error']) {
        echo json_encode($output);
        die;

    } else if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {

        $output['errors']['email'] = 'Enter correct Email ID';
        $output['status'] = 'fail';
        echo json_encode($output);
        die;
    }


    $query = 'UPDATE `users` SET name = ?,email = ? where id=' . post('id');
    $parameters = array($input['name'], $input['email']);
    $statement = $db->prepare($query);
    $statement->execute($parameters);

    $_SESSION['name'] = post('name');
    $output = responseSuccess('Profile Updated successfully');

}

echo json_encode($output);

?>