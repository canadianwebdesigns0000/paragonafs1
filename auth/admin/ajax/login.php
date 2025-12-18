<?php
    session_start();
	include '../../config.php';
	include '../../include/reply.php';

	$username	=	post('username');
	$password	=	post('password');

    // Encrypt password according to encryption type defined in config.php
    if ($encryptionType == 'sha1') {
        $password = sha1($password);

    } elseif ($encryptionType == 'md5') {
        $password = md5($password);
    }

	// SELECT credentials MATCH FROM THE DATABASE
	$query	=	'SELECT * FROM `admin` where username=? and password=?';
	$parameters	=	array($username, $password);
	$statement	=	$db->prepare($query);
	$statement->execute($parameters);

// If match found in database then login
if ($statement->rowCount() > 0) {

    $data = $statement->fetch(PDO::FETCH_ASSOC);

    // Create Session of Admin Name and admin
    $_SESSION['name'] = $data['name'];
    $_SESSION['username'] =	$data['username'];
    $_SESSION['first_name'] = $data['first_name'];
    $_SESSION['last_name'] = $data['last_name'];
    $_SESSION['is_superadmin'] = $data['is_superadmin'];
    $_SESSION['table'] = 'admin';
    $_SESSION['admin'] = true;

    // Last login update
    $query	=	'UPDATE `admin` SET lastlogin_at = NOW() where username=?';
    $statement	=	$db->prepare($query);
    $statement->execute(array($username));

    $output = responseRedirect('dashboard.php', 'Logged in Successfully');

}else {
    $output = responseError('Wrong Login Details');
}
	// output the json format of messages
	echo json_encode($output);
?>