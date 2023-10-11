<?php declare(strict_types=1);

namespace App\Controller;

use App\App;
use App\Model\Page;
use App\Model\User;
use Intervention\Image\ImageManagerStatic;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use System\View\PlatesController;

class AdminController extends PlatesController
{
    private ?User $user;

    public function __construct()
    {
        $this->user = User::getFromSession();
        if (!$this->user && parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) != '/admin') {
            // todo: - it'd be better if admin was a route group and this was handled properly by middleware
            throw new \Exception("Unathorised");
        }
        parent::__construct();
    }

    public function actionIndex(ServerRequestInterface $request): Response
    {
        if (!$this->user) {
            $error=false;
            if ($request->getMethod()=='POST') {
                $params = $request->getParsedBody();
                if (isset($params['email']) && isset($params['password'])) {
                    if (!$params['email']) {
                        $error='Email was blank';
                    }
                    elseif (!$params['password']) {
                        $error='Password was blank';
                    }
                    elseif (User::login($params['email'],$params['password'])) {
                        header('Location: /admin');
                        die();
                    }
                    else {
                        $error="Email not found or password incorrect.";
                    }
                }
            }
            return $this->render("admin::login", compact('error'));
        } else {
            return $this->render("admin::index");
        }
    }

    public function actionLogout(ServerRequestInterface $request): Response
    {
        $user = User::getFromSession();
        if ($user) {
            $user->logout();
        }
        header('Location: /');
        die();
    }

    /**
     * @param ServerRequestInterface $request
     * @param string|null $id
     * @return Response
     * @throws \Exception
     */
    public function actionPages(ServerRequestInterface $request, string $id = null): Response
    {
        if ($id) {
            $form = new \App\Form\Page($id);
            if ($form->processed($request)) {
                return $this->redirect('/admin/pages');
            }
            return $this->render("admin::page", compact( 'form'));
        } else {
            $pages = Page::hydrateMany();
            return $this->render("admin::pages", compact('pages'));
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param string|null $id
     * @return Response
     * @throws \Exception
     */
    public function actionMenu(ServerRequestInterface $request, string $id = null): Response
    {
        if ($id) {
            $form = new \App\Form\Menu($id);
            if ($form->processed($request)) {
                return $this->redirect('/admin/menu');
            }
            return $this->render("admin::menu", compact( 'form'));
        } else {
            $menu = Menu::hydrateMany(null,null,'`order` ASC');
            return $this->render("admin::menus", compact('menu'));
        }
    }

    public function actionWork(ServerRequestInterface $request, $id = null): Response
    {
        if ($id) {
            $form = new \App\Form\Work($id);
            if ($form->processed($request)) {
                return $this->redirect('/admin/work');
            }
            return $this->render("admin::work", compact( 'form'));
        }
        else {
            $works = Work::hydrateMany();
            return $this->render("admin::works", compact('works'));
        }
    }

    public function actionNews(ServerRequestInterface $request, $id = null): Response
    {
        if ($id) {
            $form = new \App\Form\News($id);
            if ($form->processed($request)) {
                return $this->redirect('/admin/news');
            }
            return $this->render("admin::newsitem", compact( 'form'));
        }
        else {
            $news = News::hydrateMany();
            return $this->render("admin::news", compact('news'));
        }
    }

    public function actionImages(ServerRequestInterface $request, $id = null): Response
    {
        if ($id && strpos($id,'..')===0) {
            throw new \Exception("Illegal URL for admin media : directory traversal is not permitted");
        }
        $imageDirStr = PUBLIC_DIR.DIRECTORY_SEPARATOR.'image'.($id ? DIRECTORY_SEPARATOR.$id : '');
        if (!is_dir($imageDirStr)) {
            throw new \Exception("Invalid images directory : ".$imageDirStr);
        }
        $iterator = new \DirectoryIterator($imageDirStr);
        $files = [];
        $realPath = realpath($imageDirStr);
        foreach($iterator as $fileInfo) {
            $filename = $fileInfo->getFilename();
            if (!$fileInfo->isDot() && !$fileInfo->isDir() && $filename!='.DS_Store') {
                $fullFilePath = $realPath . DIRECTORY_SEPARATOR . $filename;
                try {
                    $im = ImageManagerStatic::make($fullFilePath);
                    $files[] = [
                        'name' => $filename,
                        //'path' => $fullFilePath,
                        'url' => "/image/" . $filename,
                        'type' => mime_content_type($fileInfo->getRealPath()),
                        'size' => $fileInfo->getSize(),
                        'width' => $im->getWidth(),
                        'height' => $im->getHeight(),
                    ];
                }
                catch(\Intervention\Image\Exception\NotReadableException $exc) {
                    // ImageMagik can't read it ... it's not an image so ignore it...
                }
            }
        }
        $isAjax = 'xmlhttprequest' == strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' );
        if ($isAjax) {
            return new JsonResponse($files);
        }
        else {
            return $this->render('admin::images', compact('files', 'imageDirStr'));
        }
    }

    public function actionUploadimage(ServerRequestInterface $request, $id = null): JsonResponse
    {
        $uploadedFiles = $request->getUploadedFiles();

        /** @var UploadedFileInterface $uploadedFile */
        $uploadedFile = reset($uploadedFiles);

        try {
            $uploadInfo = App::handleUploadedFile($uploadedFile);
            $path = $uploadInfo['path'];
            $im = ImageManagerStatic::make($path);
            return new JsonResponse([
                'name' => basename($path),
                'url' => $uploadInfo['url'],
                'type' => mime_content_type($path),
                'size' => filesize($path),
                'width' => $im->getWidth(),
                'height' => $im->getHeight(),
            ]);
        }
        catch (\Exception $exc) {
            return new JsonResponse(['status'=>'error','message'=>$exc->getMessage()]);
        }
    }
}