<?php
    session_start();
    include('../config.php');
    error_reporting(0);
    // SESSION CHECK SET OR NOT
    if (!isset($_SESSION['name'])) {
        header('location:index.php');
        exit();
    }
    $filename = "exported-data" . time() . ".csv";

    //header to give the order to the browser
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=' . $filename);

    //select table to export the data
    $stmt = $db->prepare("SELECT * FROM users");
    $stmt->execute();
    $rows = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($rows) {
        getcsv(array_keys($rows));
    }
    while ($rows = $stmt->fetch(PDO::FETCH_ASSOC)) {
        getcsv($rows);

    }

    // Get total number of fields present in the database
    function getcsv($no_of_field_names)
    {
        $separate = '';


        // Do the action for all field names as field name
        foreach ($no_of_field_names as $field_name) {
            if (preg_match('/\\r|\\n|,|"/', $field_name)) {
                $field_name = '' . str_replace('', $field_name) . '';
            }
            echo $separate . $field_name;

            //separate with the comma
            $separate = ',';
        }

        // Make new row and line
        echo "\r\n";
    }

?>