<?php
header("Content-Type: application/json");

// Get the JSON data sent from the client
$json_data = file_get_contents("php://input");
$data = json_decode($json_data, true);

// Check if the file path is set
if (isset($data["file_path"])) {
    $file_path = $data["file_path"];

    // Remove the "paragonafs.ca/" prefix from the file path
    $file_path = str_replace('paragonafs.ca/', '', $file_path);

    // Check if the file exists and delete it
    if (file_exists($file_path) && unlink($file_path)) {
        echo json_encode([
            "status" => "success",
            "message" => "File deleted successfully.",
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "File not found or could not be deleted.",
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "No file path provided.",
    ]);
}
?>