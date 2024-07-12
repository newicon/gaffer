<?php
// router.php
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUri = parse_url($requestUri);
if (isset($parsedUri['path']) && $parsedUri['path']!='/' && file_exists($file =__DIR__.DIRECTORY_SEPARATOR.$parsedUri['path'])) {
    $dot = strrpos($parsedUri['path'], '.');
    $extension = substr($parsedUri['path'], $dot + 1);
    $mimeType = match ($extension) {
        "css" => "text/css",
        "js" => "text/javascript",
        default => mime_content_type($file),
    };
    header('Content-type: '.$mimeType, true);
    include_once $file;
}
else {
    include_once __DIR__ . DIRECTORY_SEPARATOR . '/index.php';
}
