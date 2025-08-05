<?php
function logMessage($message) {
    $logFile = __DIR__ . "/logs/api.log";
    $timestamp = date("Y-m-d H:i:s");
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>
