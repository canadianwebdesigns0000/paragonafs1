<?php
require 'vendor/autoload.php';

function sendToGoogleSheet($formData) {
    $client = new Google\Client();
    $client->setAuthConfig('credentials.json'); // Path to your JSON file
    $client->addScope(Google\Service\Sheets::SPREADSHEETS);

    $service = new Google\Service\Sheets($client);

    // Google Sheet ID and Range
    $spreadsheetId = '1p9z-26A0xGYGyiVPT-o_Iz9d9qwibvGci2nNN5XRTkw'; // Replace with your Google Sheet ID
    $range = 'Sheet1!A1'; // Adjust range based on your sheet layout

    // Include the status column in the values array
    $values = [
        [$formData['name'], $formData['email'], $formData['message'], 'Pending'] // Default status is 'Pending'
    ];

    $body = new Google\Service\Sheets\ValueRange([
        'values' => $values
    ]);
    $params = [
        'valueInputOption' => 'RAW'
    ];

    $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);

    return $result->getUpdates()->getUpdatedCells();
}

// Example form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $result = sendToGoogleSheet(['name' => $name, 'email' => $email, 'message' => $message]);
    if ($result) {
        echo "Data sent successfully!";
    } else {
        echo "Failed to send data.";
    }
}


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>


