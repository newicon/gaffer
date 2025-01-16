<?php
declare(strict_types=1);

namespace App;

use Intervention\Image\ImageManagerStatic;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ImageBootstrap extends \Gaffer\Bootstrap
{
    public const int MAX_IMAGE_WIDTH = 1024;
    public const int MAX_IMAGE_HEIGHT = 1024;

    /**
     * @param string $publicDir
     * @throws \Exception
     */
    public function __construct(string $publicDir)
    {
        parent::__construct($publicDir);
        define('PUBLIC_IMAGES_DIR', $publicDir.DIRECTORY_SEPARATOR."images");
        define('PUBLIC_ASSETS_DIR', $publicDir.DIRECTORY_SEPARATOR."assets");
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
    }

    /**
     * @param ResponseInterface $response
     * @return bool
     */
    public function emit(ResponseInterface $response): bool
    {
        $sapiEmitter = new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter();
        return $sapiEmitter->emit($response);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getResponse(ServerRequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $pathInfo = pathinfo(PUBLIC_DIR . $path);
        if (preg_match('/(.+?)_(\d+|x)_(\d+|x)$/', $pathInfo['filename'], $matches)) {
            $sourceFilename = $matches[1];
            $width = $matches[2] === 'x' ? null : (int)$matches[2];
            $height = $matches[3] === 'x' ? null : (int)$matches[3];


            if ($width !==null) {
                if ($width < 1) {
                    throw new \Exception("Image not found : width must be at least 1");
                }
                if ($width > self::MAX_IMAGE_WIDTH) {
                    throw new \Exception("Image not found : width must be less than " . self::MAX_IMAGE_WIDTH);
                }
            }
            if ($height!==null) {
                if ($height < 1) {
                    throw new \Exception("Image not found : height must be at least 1");
                }
                if ($height > self::MAX_IMAGE_WIDTH) {
                    throw new \Exception(
                        "Image not found : height must be less than " . self::MAX_IMAGE_HEIGHT
                    );
                }
            }
            $sourceImage = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $sourceFilename . "." . $pathInfo["extension"];
            if (file_exists($sourceImage)) {
                try {
                    if ($width===null && $height===null) {
                        $newSymlink = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $pathInfo['basename'];
                        symlink($sourceImage, $newSymlink );
                    }
                    else {
                        $image = ImageManagerStatic::make($sourceImage);
                        if ($width!==null && $width>$image->getWidth()) {
                            throw new \Exception(
                                "Image not found : specified width ".$width." was wider than the original width"
                            );
                        }
                        if ($height!==null && $height>$image->getHeight()) {
                            throw new \Exception(
                                "Image not found : specified height ".$height." was higher than the original height"
                            );
                        }
                        $image->resize($width, $height, function($constraint) {
                            $constraint->aspectRatio();
                        });
                        $image->save($pathInfo['dirname'].DIRECTORY_SEPARATOR.$pathInfo['basename']);
                    }
                    return new RedirectResponse($uri->getPath());
                }
                catch (\Exception $exc) {
                    throw new \Exception("Image not found : ".$exc->getMessage());
                }
            }
        }
        throw new \Exception("Image not found");
    }

    /**
     * @param \Exception $exc
     * @return ResponseInterface
     */
    public function getErrorResponse(\Exception $exc): ResponseInterface
    {
        $response = new Response;
        $response->withStatus(404);
        $response->getBody()->write($exc->getMessage());
        return $response;
    }
}