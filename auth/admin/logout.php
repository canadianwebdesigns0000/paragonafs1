<?php
    session_start();
// destroy session and redirect to login page
    session_destroy();
    setcookie ('username', null, time() - 3600, '/');
    setcookie ('userPassword', null, time() - 3600, '/');
    setcookie ('name', null, time() - 3600, '/');
    setcookie ('table', null, time() - 3600, '/');
    setcookie ('admin', null, time() - 3600, '/');

    header('location:index.php');

?>