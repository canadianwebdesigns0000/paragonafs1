<?php
// Define the target directory for file deletion
$target_dir = "uploads/";

if(isset($_POST['filename'])){
    $filename = $_POST['filename']; 
 
    // Specify the path to the file within your local directory
    $file_path = $target_dir . $filename;

    // Check if the file exists before attempting deletion
    if (file_exists($file_path)) {
        // Attempt to delete the file
        if (unlink($file_path)) {
            echo 'File deleted successfully.';
        } else {
            // echo 'File deletion failed.';
        }
    } else {
        echo 'File not found.';
    }
} else {
    echo 'No filename specified.';
}
?>