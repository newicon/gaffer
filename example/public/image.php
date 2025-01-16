<?php
require dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
$bootstrap = new \App\ImageBootstrap(__DIR__);
$bootstrap->run();
