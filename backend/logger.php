<?php

function writeLog($action, $user = 'Guest')
{
    $baseLogDir = __DIR__ . '/../logs';

    // Create logs folder
    if (!is_dir($baseLogDir)) {
        mkdir($baseLogDir, 0755, true);
    }

    // Monthly folder
    $monthFolder = $baseLogDir . '/' . date('Y-m');

    if (!is_dir($monthFolder)) {
        mkdir($monthFolder, 0755, true);
    }

    // Daily file
    $logFile = $monthFolder . '/' . date('Y-m-d') . '.txt';

    $date = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

    $log = sprintf(
        "[%s] | USER: %s | IP: %s | ACTION: %s%s",
        $date,
        $user,
        $ip,
        $action,
        PHP_EOL
    );

    file_put_contents(
        $logFile,
        $log,
        FILE_APPEND | LOCK_EX
    );
}
?>