<?php
session_start();
include '../../config.php';

// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $username = $_POST['edit_admin_username'];
    $is_superadmin = $_POST['edit_admin_is_superadmin'];
    $first_name = $_POST['edit_admin_first_name'];
    $last_name = $_POST['edit_admin_last_name'];
    $phone = $_POST['edit_admin_phone'];
    $email = $_POST['edit_admin_email'];
    $password = $_POST['edit_admin_password']; // Note: You should hash the password before storing it in the database

    if (empty($password)) {

        // Update user admin information in the database
        $query = "UPDATE admin SET 
            username = ?, 
            is_superadmin = ?, 
            first_name = ?, 
            last_name = ?, 
            phone = ?
            WHERE email = ?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$username, $is_superadmin, $first_name, $last_name, $phone, $email]);

    } else {
        
        // Encrypt password according to encryption type defined in config.php
        if($encryptionType == 'sha1') {
            $password  = sha1($password );
        }
        elseif ($encryptionType == 'md5') {
            $password  = md5($password );
        }

        // Update user admin information in the database
        $query = "UPDATE admin SET 
            username = ?, 
            is_superadmin = ?, 
            first_name = ?, 
            last_name = ?, 
            phone = ?,
            password = ?
            WHERE email = ?";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$username, $is_superadmin, $first_name, $last_name, $phone, $password, $email]);
    }

    if ($result) {
        // Update successful
        echo "User Admin Information Updated Successfully!";
    } else {
        // Update failed
        echo "Error updating user admin information.";
    }
} else {
    // Invalid request method
    echo "Invalid request method.";
}
?>
