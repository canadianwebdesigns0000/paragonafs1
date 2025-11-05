<?php
    session_start();
    include '../../config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['username'])) {
    header('location:index.php');
}

    $output = [];
    $password = post('passwordUpdate');

    $input = [];
    $input['nameUpdate'] = post('nameUpdate');
    $input['usernameUpdate'] = post('usernameUpdate');
    $input['emailUpdate'] = post('emailUpdate');

    $output = responseFormErrors($input);

if($output['error']) {

    foreach ($output['errors'] as $key => $out) {
        $output['errors'][$key] = preg_replace('/Update/', '', $out[0]);
    }

    echo json_encode($output);
    die;
}
else if (!filter_var($input['emailUpdate'], FILTER_VALIDATE_EMAIL)) {

    $output['errors']['emailUpdate'] = 'Enter correct Email ID';
    $output['status'] = 'fail';
    echo json_encode($output);
    die;
}

    $userId = post('id');

$parameters = '';
if ($_POST) {

    // SELECT MATCH FROM THE DATABASE
    $query     = 'SELECT * FROM `users` where username=? and id !='.$userId;
    $statement = $db->prepare($query);
    $statement->execute(array(post('usernameUpdate')));

    $userData = $db->prepare('SELECT * FROM users WHERE id=?');
    $userData->execute(array($userId));
    $rowUser = $userData->fetch(PDO::FETCH_ASSOC);

    if ($statement->rowCount() > 0) {

        $output['status'] = 'fail';
        $output['errors']['usernameUpdate'] = array('User with this username already exists.Try different username');
        echo json_encode($output);
        die;
        
    } else {

        // Encrypt password according to encryption type defined in config.php
        if($encryptionType == 'sha1') {
            $password = sha1(post('passwordUpdate'));

        } elseif ($encryptionType == 'md5') {
            $password = md5(post('passwordUpdate'));
        }



        // When no image is selected
        if ($_FILES['image']['name'] == '') {

            $query      = 'UPDATE  `users` SET name = ?, username=?, password=?, email = ?, status=?, email_verified =? where id=?';
            $parameters = array(post('nameUpdate'), post('usernameUpdate'), post('passwordUpdate') != null ? $password : $rowUser['password'], post('emailUpdate'), post('statusUpdate'), post('emailVerifiedUpdate'), $userId);

        } else {

            $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif', 'pjpeg');
            $imageName = generateNewFileName($_FILES['image']);
            $ext               = end((explode('.', $_FILES['image']['name'])));
            $path              = '../../' . $path . $imageName;
            $tmp               = $_FILES['image']['tmp_name'];

            if (!in_array($ext, $allowedFileTypes)) {

                $output = responseError('You uploaded wrong image format');
                echo json_encode($output);
                exit();

            } else {
                $moved = move_uploaded_file($tmp, $path);
                // Resize the uploaded avatar
                resize($path, '150', '150', $ext);

                $query      = 'UPDATE `users` SET name = ?, username=?, password=?, email = ?, avatar=?, status=?,email_verified =? where id=?';
                $parameters = array(post('nameUpdate'), post('usernameUpdate'), post('passwordUpdate') != null ? $password : $rowUser['password'], post('emailUpdate'),  $imageName, post('statusUpdate'), post('emailVerifiedUpdate'), $userId);
            }

        }

        $statement = $db->prepare($query);
        $statement->execute($parameters);

        $output = responseSuccess('User updated successfully');

    }

}
echo json_encode($output);
?>