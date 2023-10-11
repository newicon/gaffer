<?php
declare(strict_types=1);

namespace System;

use Intervention\Image\ImageManagerStatic;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Http\Message\ResponseInterface;

class ImageBootstrap extends Bootstrap
{
    const MAX_IMAGE_WIDTH = 1024;
    const MAX_IMAGE_HEIGHT = 1024;

    /**
     * @param string $publicDir
     * @throws \Exception
     */
    public function __construct(string $publicDir) {
        parent::__construct($publicDir);
        define('PUBLIC_IMAGES_DIR', $publicDir.DIRECTORY_SEPARATOR."images");
        define('PUBLIC_ASSETS_DIR', $publicDir.DIRECTORY_SEPARATOR."assets");
    }

    /**
     * @return \Psr\Http\Message\ServerRequestInterface
     */
    public function getRequest(): \Psr\Http\Message\ServerRequestInterface
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
     * @return bool|mixed
     */
    public function emit(ResponseInterface $response)
    {
        $sapiEmitter = new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter();
        return $sapiEmitter->emit($response);
    }

    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function getResponse(\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $pathInfo = pathinfo(PUBLIC_DIR . $path);
        if (preg_match('/(.+)_[0-9]+_[0-9]+$/', $pathInfo['filename'], $matches)) {

            $sourceFilename = $matches[1];

            $parts = explode("_", $pathInfo['filename']);
            if (count($parts)!==3) {
                throw new \Exception("Image not found : invalid image specification");
            }

            $height = intval(array_pop($parts));
            $width = intval(array_pop($parts));

            if (!$height || !$width) {
                throw new \Exception("Failed to extract require image size from '".$pathInfo['filename']."'");
            }
            $sourceImage = $pathInfo['dirname'] . DIRECTORY_SEPARATOR . $sourceFilename . "." . $pathInfo["extension"];
            if (file_exists($sourceImage)) {
                $widthInt = $width ? intval($width) : null;
                $heightInt = $height ? intval($height) : null;
                if (!$widthInt && !$heightInt) {
                    throw new \Exception("Image not found : invalid image specification");
                }
                if ($widthInt && ($widthInt<1 || $width>self::MAX_IMAGE_WIDTH)) {
                    throw new \Exception("Image not found : bad image width ".$widthInt);
                }

                if ($widthInt && ($widthInt<1 || $width>self::MAX_IMAGE_HEIGHT)) {
                    throw new \Exception("Image not found : bad image height ".$widthInt);
                }
                try {
                    $image = ImageManagerStatic::make($sourceImage);
                    $image->resize($widthInt, $heightInt);
                    $image->save($pathInfo['dirname'].DIRECTORY_SEPARATOR.$pathInfo['basename']);
                    return new RedirectResponse($uri);
                }
                catch (\Exception $exc) {
                    throw new \Exception("Image not found");
                }
            }
        }
        throw new \Exception("Image not found");
    }

    /**
     * @param \Exception $exc
     * @return ResponseInterface
     */
    public function getErrorResponse(\Exception $exc): \Psr\Http\Message\ResponseInterface
    {
        $response = new \Laminas\Diactoros\Response;
        $response->withStatus(404);
        $response->getBody()->write($exc->getMessage());
        return $response;
    }
}