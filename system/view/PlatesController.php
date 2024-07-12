<?php declare(strict_types=1);

namespace System\View;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use League\Plates\Engine;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;

/**
 * sample template controller for plates https://platesphp.com/
 * requires : League\Plates (most likely via composer)!
 */
class PlatesController
{
    /** @var Engine */
    protected Engine $templates;

    /**
     *
     */
    public function __construct()
    {
        $this->templates = new Engine(APP_DIR.DIRECTORY_SEPARATOR.'View');
        $this->templates->addFolder('partials',APP_DIR.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'partials');
        $this->templates->addFolder('errors',APP_DIR.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'errors');
        $this->templates->addFolder('admin',APP_DIR.DIRECTORY_SEPARATOR.'View'.DIRECTORY_SEPARATOR.'admin');

        $this->templates->loadExtension(new \League\Plates\Extension\Asset(PUBLIC_DIR, false));
    }

    /**
     * @param string $viewFile
     * @param array $params
     * @return Response
     */
    public function render(string $viewFile, array $params=[]): Response
    {
        $body = $this->templates->render($viewFile, $params);
        $response = new Response();
        $response->getBody()->write($body);
        return $response;
    }

    /**
     * @param ServerRequestInterface $request
     * @param array $args
     * @return Response
     */
    public function genericAdminHandler(ServerRequestInterface $request, array $args): Response
    {
        try {
            if (isset($args['action'])) {
                $action = 'action' . ucfirst(strtolower($args['action']));
                $id = $args['id'] ?? null;
                if (method_exists($this, $action)) {
                    return $this->{$action}($request, $id);
                } else {
                    return $this->render("admin::error",['exc'=>new NotFoundException()])
                        ->withStatus(404);
                }
            }
            return $this->render("admin::error",['exc'=>new NotFoundException()])
                ->withStatus(404);
        }
        catch(\Exception $exc) {
            return $this->render("admin::error",compact('exc'))
                ->withStatus(500);
        }
    }

    /**
     * @param string $url
     * @param int $statusCode
     * @return RedirectResponse
     */
    public function redirect(string $url, int $statusCode=302): RedirectResponse
    {
        ob_end_clean();
        return new RedirectResponse($url, $statusCode);
    }
}
