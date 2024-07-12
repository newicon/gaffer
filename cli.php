<?php

use Support\Data\app\Model\User;

include __DIR__.'/vendor/autoload.php';

$scriptName = $argv[0];
$command = $argv[1] ?? null;
$params = array_slice($argv,2);

// this is really bodged ... there should be a dedicated CLI bootstrap!
$ini = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . "/config.ini");
$connectionString = "mysql:dbname=".$ini['DB_NAME'].";host=".$ini['DB_HOST'].";charset=".$ini['DB_CHAR'];
\Support\Data\app\Db\DB::init($connectionString, $ini['DB_USER'], $ini['DB_PASS']);

switch ($command)
{
    case 'add-user':
        if (count($params)<2) {
            die("Missing required parameters");
        }
        $email = $params[0];
        $password = $params[1];
        $user = User::getUserByEmail($email);
        if ($user) {
            die("User with email '".$email."' already exists");
        }
        $user = new User();
        $user->email = $email;
        $user->setPassword($password);
        $user->status='active';
        $user->save();
        echo "Added user '".$email."'".PHP_EOL;
        break;

    case 'set-password':
        if (count($params)<2) {
            die("Missing required parameters");
        }
        $email = $params[0];
        $password = $params[1];
        $user = User::getUserByEmail($email);
        if (!$user) {
            die("User with email '".$email."' doesn't exist");
        }
        $user->setPassword($password);
        $user->save();
        echo "Updated password for user '".$email."'".PHP_EOL;
        break;

    default:
        echo "Commands:".PHP_EOL;
        echo "  add-user email password".PHP_EOL;
        echo "  set-password email password".PHP_EOL;
        break;
}
