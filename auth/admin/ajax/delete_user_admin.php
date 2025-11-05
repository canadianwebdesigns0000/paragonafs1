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

    $query_tax = 'DELETE FROM admin WHERE email = ?';
    $statement_tax = $db->prepare($query_tax);
    $statement_tax->execute([$user_email]);

    // Commit the transaction if both deletions are successful
    $db->commit();

    echo json_encode('User Admin successfully deleted');
} catch (Exception $e) {
    // Rollback the transaction if an error occurs
    $db->rollback();

    $output = responseError('Failed to delete user: ' . $e->getMessage());

    die(json_encode($output));
}
?>
