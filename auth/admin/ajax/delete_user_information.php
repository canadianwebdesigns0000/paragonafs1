<?php
    session_start();
	include '../../config.php';

    $user_email = $_POST['user_email'];

// Check session of admin.If session is not set die it.
if(!isset($_SESSION['admin']))
{
    $output = responseError('Session is destroyed');

    die(json_encode($output));
}

try {
    // Start a transaction to ensure data consistency across tables
    $db->beginTransaction();

    // Insert data into deleted_tax_information table
    $query_insert = 'INSERT INTO deleted_tax_information
                    SELECT * FROM tax_information
                    WHERE email = ?';
    $statement_insert = $db->prepare($query_insert);
    $statement_insert->execute([$user_email]);

    // Check for errors
    if($statement_insert->errorInfo()[0] != "00000") {
        throw new Exception("Error in first INSERT: " . implode(", ", $statement_insert->errorInfo()));
    }

    // Insert data into deleted_users table
    $query_insert2 = 'INSERT INTO deleted_users
                    SELECT * FROM users
                    WHERE email = ?';
    $statement_insert2 = $db->prepare($query_insert2);
    $statement_insert2->execute([$user_email]);

    // Check for errors
    if($statement_insert2->errorInfo()[0] != "00000") {
        throw new Exception("Error in second INSERT: " . implode(", ", $statement_insert2->errorInfo()));
    }

    // First, delete from tax_information table
    $query_tax = 'DELETE FROM tax_information WHERE email = ?';
    $statement_tax = $db->prepare($query_tax);
    $statement_tax->execute([$user_email]);

    // Then, delete from users table
    $query_user = 'DELETE FROM users WHERE email = ?';
    $statement_user = $db->prepare($query_user);
    $statement_user->execute([$user_email]);

    // Commit the transaction if both deletions are successful
    $db->commit();

    echo json_encode('User successfully deleted');
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $db->rollback();

    $output = responseError('Failed to delete user: ' . $e->getMessage());

    die(json_encode($output));
}
?>
