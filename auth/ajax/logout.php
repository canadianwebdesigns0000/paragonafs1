<?php
    session_start();
// destroy session and redirect to login page
    session_destroy();

    header('location:../index.php');

?>