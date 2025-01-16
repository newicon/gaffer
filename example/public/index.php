<?php
use App\App;

require dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
$bootstrap = new App(__DIR__);
$bootstrap->run();
