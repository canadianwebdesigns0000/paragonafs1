<?php
$logFile = '/home/paragonafs/logs/php_log';

if (file_exists($logFile)) {
    $logContent = file_get_contents($logFile);
    echo nl2br($logContent); // nl2br() is used to maintain line breaks in the output
} else {
    echo "The log file does not exist.";
}

?>