<?php
session_start();
include '../../config.php';

// SESSION CHECK SET OR NOT
if (!isset($_SESSION['admin'])) {
    header('location:index.php');
    exit(); // Always exit after a redirect
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if all required fields are present
    if (!empty($_POST['add_notes_text'])) {
        $user_email_notes = $_POST['user_email_notes'];
        $add_notes_text = $_POST['add_notes_text'];

        // Insert the notes into the database
        $query = 'INSERT INTO `notes` SET admin_username=?, user_email=?, content=?, updated_at=NOW(), created_at=NOW()';
        $statement = $db->prepare($query);
        $success = $statement->execute([$_SESSION['username'], $user_email_notes, $add_notes_text]);

        if ($success) {
            // Return a success message
            echo "Note added successfully.";
        } else {
            // Return an error message
            echo "Failed to add notes. Please try again.";
        }
    } else {
        // Return an error message if required fields are missing
        echo "Please provide all required fields.";
    }
} else {
    // Return an error message if accessed via GET request
    echo "Invalid request method.";
}
?>