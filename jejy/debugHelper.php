<?php
// Create a debug file function
function debugLog($message, $data = null) {
    $debugFile = __DIR__ . '/DEBUG_OUTPUT.txt';
    $timestamp = date('Y-m-d H:i:s');
    $output = "\n[{$timestamp}] {$message}\n";
    
    if ($data !== null) {
        $output .= print_r($data, true) . "\n";
    }
    
    $output .= str_repeat('-', 80) . "\n";
    
    file_put_contents($debugFile, $output, FILE_APPEND);
}

// Clear the debug file at the start of each request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    file_put_contents(__DIR__ . '/DEBUG_OUTPUT.txt', "===== NEW FORM SUBMISSION =====\n");
}
?>