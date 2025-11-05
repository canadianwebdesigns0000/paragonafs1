<?php
session_start();

// email SESSION CHECK SET OR NOT
if (!isset($_SESSION['email'])) {
    header('location: ./auth');
    exit();
}

$servername = "localhost";
$username = "paragonafs";
$password = "W6j4jCV9zJj8Tk8";
$dbname = "paragonafs";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully";

// Get values from AJAX request
$inputName = $_POST['inputName'];
$inputValue = $_POST['inputValue'];

// SQL query to insert values into the tax_information table
$sql = "UPDATE tax_information SET $inputName=? WHERE email=?";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("ss", $inputValue, $_SESSION['email']);

    if ($stmt->execute()) {
        echo "Update successful!";
    } else {
        echo "Error updating record: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Prepare statement error: " . $conn->error;
}

$conn->close();
?>