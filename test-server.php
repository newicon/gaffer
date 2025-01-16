<?php

$process = proc_open(
    'XDEBUG_MODE=off php -S localhost:8001 -t example/public',
    [
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ],
    $pipes
);

if (!is_resource($process)) {
    echo "Failed to start PHP process.\n";
    exit(1);
}

stream_set_blocking($pipes[2], false);
$errorOutput = stream_get_contents($pipes[2]);
fclose($pipes[2]);

if ($errorOutput) {
    echo "PHP server error: " . $errorOutput . "\n";
    exit(1);
}

echo "PHP built-in server started successfully.\n";
fclose($pipes[1]);
proc_terminate($process);
