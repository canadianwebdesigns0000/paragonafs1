<?php
    session_start();
    include '../../config.php';

    // SESSION CHECK SET OR NOT
    if (!isset($_SESSION['admin'])) {
        header('location:index.php');
    }

    $input = [];

    $input['admin_username'] = post('admin_username');
    $input['admin_is_superadmin'] = post('admin_is_superadmin');
    $input['admin_first_name'] = post('admin_first_name');
    $input['admin_last_name'] = post('admin_last_name');
    $input['admin_phone'] = post('admin_phone');
    $input['admin_email'] = post('admin_email');
    $input['admin_password'] = post('admin_password');

if ($_POST) {

    // SELECT credentials MATCH FROM THE DATABASE
    $query     = 'SELECT * FROM `admin` where username=?';
    $statement = $db->prepare($query);
    $statement->execute(array(post('username')));

    if ($statement->rowCount() > 0) {

        $output['status'] = 'fail';
        $output['errors']['username'] = array('User with this username already exists.Try different username');
        echo json_encode($output);
        die;
    } else {

        // Encrypt password according to encryption type defined in config.php
        if($encryptionType == 'sha1') {
            $input['password'] = sha1($input['admin_password']);

        } elseif ($encryptionType == 'md5') {
            $input['password'] = md5($input['admin_password']);
        }
        
        $query      = 'INSERT INTO `admin` SET username = ?, is_superadmin=?, first_name=?, last_name=?, phone=?, email=?, password=?';
        $parameters = array($input['admin_username'], $input['admin_is_superadmin'], $input['admin_first_name'], $input['admin_last_name'], $input['admin_phone'], $input['admin_email'],  $input['password']);

        $statement = $db->prepare($query);
        $statement->execute($parameters);

        // $output = responseSuccess('New User added successfully');
    }

}
?>