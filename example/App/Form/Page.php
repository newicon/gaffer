<?php declare(strict_types=1);

namespace App\Form;

use Gaffer\Db\Model;
use League\Route\Http\Exception\NotFoundException;

class Page extends FormModel
{
    public \App\Model\Page $page;

    public function __construct($id)
    {
        if ($id==='add') {
            $page = new \App\Model\Page();
        }
        elseif (is_numeric($id)) {
            /** @var Page|null $page */
            $page = \App\Model\Page::hydrateOne('id=:id',compact('id'));
            if (!$page) {
                throw new NotFoundException("Failed to find page with id=".$id);
            }

        }
        else {
            throw new \Exception("id must either be an int or 'add'");
        }

        $partials = ['' => "-- generate selected content --"];
        $dir = new \DirectoryIterator(VIEW_DIR.DIRECTORY_SEPARATOR."partials");
        foreach($dir as $fileInfo) {
            if (!$fileInfo->isDot() && !$fileInfo->isDir()) {
                $partials[] = "partials::".$fileInfo->getBasename(".".$fileInfo->getExtension());
            }
        }

        //$contentGenerators = [\App\Model\News::class, \App\Model\Work::class]
        $this->page = $page;
        parent::__construct('page');
        //self::addDisplay('id')->label('Page Id');
        self::addText('stub')->label('Url Stub')->required(true);
        self::addText('title')->label('Title')->required(true);
        self::addCheckbox('active')->label('Active');
        self::addText('description')->label('Description')->required(true);
        self::addTextArea('content')->label('Content')->required(true);
        self::addText('redirect')->label('Redirect')->hint('Perform a redirect if the page is inactive');
        self::addSubmit('save')->value('Save');
        self::addCancelLink('/admin/pages');
    }

    function getModel(): Model
    {
        return $this->page;
    }
}