<?php declare(strict_types=1);

namespace App\Controller;

use App\Model\Page;
use Exception;
use Laminas\Diactoros\Response;
use League\Route\Http\Exception\NotFoundException;
use Psr\Http\Message\ServerRequestInterface;
use System\View\PlatesController;

class CmsController extends PlatesController
{
    /**
     * @throws NotFoundException
     * @throws Exception
     */
    public function actionIndex(ServerRequestInterface $request): Response
    {
        $page = Page::forUri($request->getUri());
        if ($page) {

            return $this->render("cms",['page'=>$page]);
        }
        else {
            throw new NotFoundException();
        }
    }
}