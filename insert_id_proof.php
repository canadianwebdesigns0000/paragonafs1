<?php

// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "paragon";

$servername = "localhost:3306";
$username = "paragonafs_dev";
$password = "Jhc+O*GM+hQ5";
$dbname = "paragonafs_clients";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die(json_encode(array("error" => "Connection failed: " . mysqli_connect_error())));
}

// Get the ID Proof data from the AJAX request
$idProofData = $_POST['idProofData'];

// Prepare and bind the SQL statement
// Replace `your_table` with the name of your database table and `id_proof` with the name of your ID Proof column
$stmt = $conn->prepare("INSERT INTO tax_information (id_proof) VALUES (?)");
$stmt->bind_param("s", $idProofData);

// Execute the prepared statement
if ($stmt->execute()) {
    $response = array('success' => true);
} else {
    $response = array('success' => false, 'error' => $stmt->error);
}

// Close statement and connection
$stmt->close();
$conn->close();

// Send the response
echo json_encode($response);
?>