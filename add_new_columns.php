<?php
/**
 * Add T4/T4A/T Slips password columns to tax_information table
 * 
 * This script adds the following columns:
 * - spouse_t_slips (TEXT) - stores spouse T4/T4A/T Slips file URLs
 * - t_slips_passwords (TEXT) - stores applicant T4/T4A/T Slips passwords (JSON)
 * - spouse_t_slips_passwords (TEXT) - stores spouse T4/T4A/T Slips passwords (JSON)
 * 
 * Safe to run multiple times - checks if columns exist before adding.
 * Run from browser: http://yoursite.com/add_tslips_password_columns.php
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

$results = [];
$errors = [];

// Function to check if column exists
function columnExists($conn, $table, $column) {
    $query = "SHOW COLUMNS FROM `$table` LIKE '$column'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// Function to add column safely
function addColumnSafely($conn, $table, $column, $definition, &$results, &$errors) {
    if (columnExists($conn, $table, $column)) {
        $results[] = "Column '$column' already exists - skipped";
        return false;
    }
    
    $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
    
    if (mysqli_query($conn, $sql)) {
        $results[] = "Successfully added column '$column'";
        return true;
    } else {
        $error = "Error adding column '$column': " . mysqli_error($conn);
        $errors[] = $error;
        return false;
    }
}

// Add columns
addColumnSafely($conn, 'tax_information', 'spouse_t_slips', 'TEXT NULL DEFAULT NULL', $results, $errors);
addColumnSafely($conn, 'tax_information', 't_slips_passwords', 'TEXT NULL DEFAULT NULL', $results, $errors);
addColumnSafely($conn, 'tax_information', 'spouse_t_slips_passwords', 'TEXT NULL DEFAULT NULL', $results, $errors);

mysqli_close($conn);

// Count results
$added = 0;
$omitted = 0;

foreach ($results as $result) {
    if (strpos($result, 'already exists') !== false) {
        $omitted++;
    } elseif (strpos($result, 'Successfully added') !== false) {
        $added++;
    }
}

// Simple text output
header('Content-Type: text/plain');
echo "Done adding columns\n";
echo "$added - added\n";
echo "$omitted - omitted\n";

if (!empty($errors)) {
    echo "\nErrors:\n";
    foreach ($errors as $error) {
        echo "- " . $error . "\n";
    }
}
