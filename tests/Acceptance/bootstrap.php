<?php
$cwd = getcwd();
$routerFile = realpath("./tests/Support/Data/public/router.php");
if (!file_exists($routerFile)) {
    throw new \Exception("Failed to locate router file : ".$routerFile);
}
exec('php -S localhost:8000 '.$routerFile.' >/dev/null 2>&1 & echo $!', $output);
$pid = $output[0]; // Get process ID of the server

register_shutdown_function(function() use ($pid) {
    exec('kill ' . $pid); // This ensures the server is stopped when tests finish
});