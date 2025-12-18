<?php
/**
 * Reset test user database record
 * This script deletes the tax_information record(s) for the test user
 * identified by first_name = "Test" and last_name = "User1".
 * Run this from browser: http://localhost/reset_test_user.php
 */

// Database connection
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

// Test user identity
$testEmail     = "test@paragonafs.local";
$testFirstName = "Test";
$testLastName  = "User1";

// Delete the record(s) for this test user based on name
$sql = "DELETE FROM tax_information WHERE first_name = ? AND last_name = ?";
$stmt = $conn->prepare($sql);

$deleted = false;
if ($stmt) {
    $stmt->bind_param("ss", $testFirstName, $testLastName);
    
    if ($stmt->execute()) {
        $affectedRows = $stmt->affected_rows;
        if ($affectedRows > 0) {
            $deleted = true;
        }
    }
    
    $stmt->close();
}

$conn->close();

// Also check if using PDO (for consistency with jejy/index.php)
try {
    include 'auth/config.php';
    $pdoStmt = $db->prepare("DELETE FROM tax_information WHERE first_name = ? AND last_name = ?");
    $pdoStmt->execute(array($testFirstName, $testLastName));
    if ($pdoStmt->rowCount() > 0) {
        $deleted = true;
    }
} catch (Exception $e) {
    // Ignore if PDO connection fails
}

// Clear session data for test user (if currently logged in as this user)
session_start();
if (isset($_SESSION['email']) && $_SESSION['email'] === $testEmail) {
    // Clear session variables that might be used to auto-fill the form
    unset($_SESSION['first_name']);
    unset($_SESSION['last_name']);
    unset($_SESSION['phone']);
    $sessionCleared = true;
} else {
    $sessionCleared = false;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test User Reset</title>
</head>
<body>
    Test user DB records cleared, ready for testing as a new user.<br><br>
    Username: test@paragonafs.local<br>
    Password: test123
</body>
</html>

