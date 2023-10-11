<?php
require dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
$bootstrap = new \System\ImageBootstrap(__DIR__);
$bootstrap->run();
