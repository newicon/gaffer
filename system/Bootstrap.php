<?php declare(strict_types=1);

namespace System;

use \Psr\Http\Message\ServerRequestInterface;
use \Psr\Http\Message\ResponseInterface;

const ENV_PRODUCTION = 'production';
const ENV_STAGING = 'staging';
const ENV_DEVELOP = 'development';

abstract class Bootstrap
{
    public static ServerRequestInterface $request;

    /**
     * @param string $publicDir
     * @throws \Exception
     */
    public function __construct(string $publicDir)
    {
        define('PUBLIC_DIR', $publicDir);
        define('ROOT_DIR', dirname($publicDir));

        $appDir = ROOT_DIR.DIRECTORY_SEPARATOR."app";
        if (!is_dir($appDir)) {
            die("Missing app directory : ".$appDir);
        }
        define('APP_DIR', $appDir);

        $controllerDir =  APP_DIR.DIRECTORY_SEPARATOR."Controller";
        define('CONTROLLER_DIR', file_exists($controllerDir) ? $controllerDir : null);

        $viewDir =  APP_DIR.DIRECTORY_SEPARATOR."View";
        define('VIEW_DIR', file_exists($viewDir) ? $viewDir : null);

        $varDir = ROOT_DIR.DIRECTORY_SEPARATOR."var";
        define('VAR_DIR', $varDir);

        define('PROTOCOL_HOST', self::getHostProtocol().$_SERVER['HTTP_HOST']);

        $configIni = ROOT_DIR.DIRECTORY_SEPARATOR.'config.ini';
        $this->config($configIni, VAR_DIR);

        ini_set('error_log', LOG);
        if (DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', "TRUE");
            ini_set('display_startup_errors', "TRUE");
        }

        // always convert errors to exceptions for consistency
        set_error_handler(function (int $errNo, string $errStr, string $errFile, int $errLine) {
            throw new \ErrorException('ERROR : ' . $errStr, $errNo, 0, $errFile, $errLine);
        });
        //set_exception_handler([$this,'handleException']);
        register_shutdown_function([$this,'shutdown']);

        session_start();

        $this->setup();
    }

    /**
     * @param string $configIniFile
     * @param string $varDir
     * @throws \Exception
     */
    public function config(string $configIniFile, string $varDir): void
    {
        $ini = file_exists($configIniFile)
            ? parse_ini_file($configIniFile)
            : null;
        if (!$ini) {
            throw new \Exception("Failed to find ini file : ".$configIniFile);
        }
        define('ENV',$ini['ENV'] ?? ENV_PRODUCTION);
        define('DEBUG', isset($ini['DEBUG']) && $ini['DEBUG'] === 'true');
        define('EMAIL', $ini['EMAIL'] ?? null);
        define('LOG', $ini['LOG'] ?? $varDir.'/logs/php-error.log');
        define('TIMEZONE', $ini['TIMEZONE'] ?? 'Europe/London');

        // db stuff ... not obligatory!
        define('DB_HOST', $ini['DB_HOST'] ?? '127.0.0.1');
        define('DB_PORT', $ini['DB_PORT'] ?? '3306');
        define('DB_NAME', $ini['DB_NAME'] ?? null);
        define('DB_USER', $ini['DB_USER'] ?? null);
        define('DB_PASS', $ini['DB_PASS'] ?? null);
        define('DB_TYPE', $ini['DB_TYPE'] ?? 'mysql');
        define('DB_CHAR', $ini['DB_CHAR'] ?? 'utf8mb4');

        date_default_timezone_set(TIMEZONE);

        if (DB_NAME) {
            $dsn = DB_TYPE . ":dbname=" . DB_NAME . ";host=" . DB_HOST . ";port=" . DB_PORT . ";charset=" . DB_CHAR;
            $this->setupDatabase($dsn, DB_USER, DB_PASS);
        }
    }

    /**
     * @param string $dsnConnectionString
     * @param string $dbUser
     * @param string $dbPassword
     * @throws \Exception
     */
    protected function setupDatabase(string $dsnConnectionString, string $dbUser, string $dbPassword): void
    {
        //throw new \Exception("Database connection implementation as yet unspecified!");
    }

    /**
     * @return ServerRequestInterface
     */
    public abstract function getRequest(): \Psr\Http\Message\ServerRequestInterface;

    /**
     * @param ResponseInterface $response
     * @return mixed
     */
    public abstract function emit(ResponseInterface $response): bool;

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public abstract function getResponse(ServerRequestInterface $request): ResponseInterface;

    /**
     * @param \Exception $exc
     * @return ResponseInterface
     */
    public abstract function getErrorResponse(\Exception $exc): ResponseInterface;

    /**
     * override to perform app specific setup before processing a request
     */
    protected function setup(): void
    {
    }

    /**
     * initiate request handling
     */
    public function run(): void
    {
        self::$request = $this->getRequest();
        try {
            $response = $this->getResponse(self::$request);
        }
        catch (\Exception $exc) {
            $response  = $this->getErrorResponse($exc);
        }
        $this->emit($response);
    }

    /**
     * php shutdown functionality
     */
    public function shutdown(): void
    {
        $lastError = error_get_last();
        if ($lastError!==null) {
            if (DEBUG) {
                die("Eeek : <pre>".print_r($lastError,true)."</pre>");
            }
            else {
                die("There has been a monumental edifice of failure :-( <br> Please get in touch and let us know ".(EMAIL??''));
            }
        }
    }

    /**
     * @return string https request protocol
     */
    public static function getHostProtocol(): string
    {
        return (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
        ? 'https://'
        : 'http://';
    }


}