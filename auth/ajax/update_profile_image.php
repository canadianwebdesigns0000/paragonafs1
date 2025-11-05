<?php
session_start();
include '../config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['user'])) {
    header('location:index.php');
}

$output = '';
if($_FILES['image']['name'] != '') {
    // Image types allowed
    $allowedFileTypes = array('jpg', 'jpeg', 'png', 'gif', 'pjpeg');

    // Create a random image name
    $imageName = generateNewFileName($_FILES['image']);
    $ext               = end((explode('.', $_FILES['image']['name'])));
    $path      = '../'.$path . $imageName;
    $tmp       = $_FILES['image']['tmp_name'];

    // if uploaded image is in the format of above mentioned the proceed to else part
    if (!in_array($ext, $allowedFileTypes)) {
        $output = responseError('You uploaded wrong image format');
    }
    else {

        $moved = move_uploaded_file($tmp, $path);
        // Resize the uploaded avatar
        resize($path, '150', '150', $ext);
        // Update avatar
        $query      = 'UPDATE `users` SET avatar = ? where id='.post('id');
        $parameters = array($imageName);
        $statement  = $db->prepare($query);
        $statement->execute($parameters);

        $output = responseSuccess('Image successfully uploaded');
        $output['imageName'] = $imageName;
    }

}
else {
    $output = responseError('Please select a image');
}

    echo json_encode($output);
?>