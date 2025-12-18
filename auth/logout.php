<?php

session_start();
// Destroy the session and redirect to login pages
setcookie ('email', null, time() - 3600, '/');
setcookie ('userPassword', null, time() - 3600, '/');
setcookie ('user', null, time() - 3600, '/');
setcookie ('table', null, time() - 3600, '/');

if ($_SESSION['table'] == 'admin') {
    session_destroy();
    header('location:admin/index.php');

} else {
    session_destroy();
    header('location:../index.php');
}
?>