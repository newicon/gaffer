<?php declare(strict_types=1);

namespace App;

use App\Controller\ErrorController;
use Deform\Component\Image;
use Gaffer\Bootstrap;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

class App extends Bootstrap
{
    public function getRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }

    public function emit(ResponseInterface $response): bool
    {
        $sapiEmitter = new SapiEmitter();
        return $sapiEmitter->emit($response);
    }

    public function getResponse(ServerRequestInterface $request): ResponseInterface
    {
        $router = new \League\Route\Router();
        $router->get('/admin', 'App\Controller\AdminController::actionIndex');
        $router->post('/admin', 'App\Controller\AdminController::actionIndex');
        $router->get('/admin/{action}', 'App\Controller\AdminController::genericAdminHandler');
        $router->post('/admin/{action}', 'App\Controller\AdminController::genericAdminHandler');
        $router->get('/admin/{action}/{id}', 'App\Controller\AdminController::genericAdminHandler');
        $router->post('/admin/{action}/{id}', 'App\Controller\AdminController::genericAdminHandler');
        $router->get('/{cms}', 'App\Controller\CmsController::actionIndex');
        $router->get('/', 'App\Controller\CmsController::actionIndex');
        return $router->dispatch($request);
    }

    public function getErrorResponse(\Exception $exc): ResponseInterface
    {
        $errorController = new ErrorController();
        $statusCode = ($exc instanceof \League\Route\Http\Exception)
            ? $exc->getStatusCode()
            : 500;
        if ($statusCode!==404 && $statusCode!==500) {
            $statusCode=500;
        }
        return $errorController
            ->http($statusCode, $exc->getMessage(), $exc)
            ->withStatus($statusCode);
    }

    protected function setupDatabase(string $dsnConnectionString, string $dbUser, string $dbPassword): void
    {
        \App\Db\DB::init($dsnConnectionString, $dbUser, $dbPassword);
    }


    /**
     * Handles an uploaded file and returns its URL
     * - if there is already a file with the same name and the md5 matches then that files URL is returned instead (i.e. instead of uploading it again)
     * - if there is already a file with the same name and the md5 doesn't match then an integer is suffixed to the name (i.e. to ensure it's unique)
     * @param UploadedFileInterface $uploadedFile
     * @return array [url:... ;path:....] the URL for accessing the file
     */
    public static function handleUploadedFile(UploadedFileInterface $uploadedFile): array
    {
        $filename = $uploadedFile->getClientFilename();
        $pathInfo = pathinfo($filename);
        $isImage = (in_array($pathInfo['extension'], ['svg', 'png', 'gif', 'jpeg', 'jpg', 'webp']));
        $saveDir = $isImage ? 'image' : 'media';

        $idx = 0;
        do {
            $saveFilename = $pathInfo['filename'] . ($idx > 0 ? $idx : '') . "." . $pathInfo['extension'];
            $saveFile = PUBLIC_DIR . DIRECTORY_SEPARATOR . $saveDir . DIRECTORY_SEPARATOR . $saveFilename;
            $idx++;
            if (file_exists($saveFile)) {
                $tempDir = sys_get_temp_dir();
                $tempFile = $tempDir . DIRECTORY_SEPARATOR . 'temp';
                $uploadedFile->moveTo($tempFile);
                $uploadedHash = md5_file($tempFile);
                $existingHash = md5_file($saveFile);
                unlink($tempFile);
                if ($uploadedHash === $existingHash) {
                    // this file has previously been uploaded!
                    return [
                        "url" => "/" . $saveDir . "/" . $saveFilename,
                        "path" => $saveFile
                    ];
                }
            }
        } while (file_exists($saveFile));
        $uploadedFile->moveTo($saveFile);
        return [
            "url" => "/".$saveDir."/".$saveFilename,
            "path" => $saveFile
        ];
    }

    public static function getPlaceholderImage(): string
    {
        return Image::PLACEHOLDER_IMAGE_BASE64;
    }
}