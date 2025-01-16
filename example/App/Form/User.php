<?php declare(strict_types=1);

namespace App\Form;

use App\Model\User as UserModel;
use Gaffer\Db\Model;
use League\Route\Http\Exception\NotFoundException;

class User extends FormModel
{
    public UserModel $user;

    public function __construct($id)
    {
        if ($id==='add') {
            $user = new UserModel();
        }
        elseif (is_numeric($id)) {
            /** @var User|null $page */
            $user = UserModel::hydrateOne('id=:id',compact('id'));
            if (!$user) {
                throw new NotFoundException("Failed to find user with id=".$id);
            }

        }
        else {
            throw new \Exception("id must either be an int or 'add'");
        }

        //$contentGenerators = [\App\Model\News::class, \App\Model\Work::class]
        $this->user = $user;
        parent::__construct('page');
        //self::addDisplay('id')->label('Page Id');
        self::addText("email");
        self::addSelect('status')->options(['active'=>'active','inactive'=>'inactive']);
        self::addSubmit('save')->value('Save');
        self::addCancelLink('/admin/users');
    }

    function getModel(): Model
    {
        return $this->user;
    }
}