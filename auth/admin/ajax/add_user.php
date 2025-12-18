<?php
    session_start();
    include '../../config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['username'])) {
    header('location:index.php');
}

    $output = [];

    $input = [];
    $input['name'] = post('name');
    $input['username'] = post('username');
    $input['email'] = post('email');
    $input['password'] = post('password');

    $output = responseFormErrors($input);

if($output['error']) {
    echo json_encode($output);
    die;
}
else if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {

    $output['errors']['email'] = 'Enter correct Email ID';
    $output['status'] = 'fail';
    echo json_encode($output);
    die;
}
if ($_POST) {

    // SELECT credentials MATCH FROM THE DATABASE
    $query     = 'SELECT * FROM `users` where username=?';
    $statement = $db->prepare($query);
    $statement->execute(array(post('username')));

    if ($statement->rowCount() > 0) {

        $output['status'] = 'fail';
        $output['errors']['username'] = array('User with this username already exists.Try different username');
        echo json_encode($output);
        die;
    }
    else {

        // Encrypt password according to encryption type defined in config.php
        if($encryptionType == 'sha1') {
            $input['password'] = sha1(post('password'));

        } elseif ($encryptionType == 'md5') {
            $input['password'] = md5(post('password'));
        }

        // When no image is selected
        if ($_FILES['image']['name'] == '') {
            $query      = 'INSERT INTO `users` SET name = ?, username=?, password=?, email = ?, status = ?, email_verified = ?';
            $parameters = array($input['name'], $input['username'], $input['password'], $input['email'], post('status'), post('emailVerified'));

        } else {
            $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif', 'pjpeg');

            $imageName = generateNewFileName($_FILES['image']);

            $ext               = end((explode('.', $_FILES['image']['name'])));
            $path              = '../../' . $path . $imageName;
            $tmp               = $_FILES['image']['tmp_name'];

            // Check if uploaded image of the format specified
            if (!in_array($ext, $allowedFileTypes)) {

                $output = responseError('You uploaded wrong image format');
                echo json_encode($output);
                exit();

            } else {
                $moved = move_uploaded_file($tmp, $path);
                // Resize the uploaded avatar
                resize($path, '150px', '150px', $ext);
                
                // Insert into database
                $query      = 'INSERT INTO `users` SET name = ?, username=?, password=?, email = ?, avatar=?, status=?, email_verified = ?';
                $parameters = array($input['name'], $input['username'], $input['password'], $input['email'], $imageName, post('status'), post('emailVerified'));

            }

        }

        $statement = $db->prepare($query);
        $statement->execute($parameters);

        $output = responseSuccess('New User added successfully');
    }

}
echo json_encode($output);
?>