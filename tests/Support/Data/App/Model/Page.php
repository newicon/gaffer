<?php declare(strict_types=1);

namespace App\Model;

use App\Db\Model;

class Page extends Model
{
    public static string $table = 'page';

    /**
     * @validation required|max:255
     * @persist
     */
    public string $stub;

    /**
     * @validation required|max:255
     * @persist
     */
    public string $title;

    /**
     * @validation required|max:255
     * @persist
     */
    public string $description;

    /**
     * @persist
     */
    public string $content;

    /**
     * @validation boolean|default:0
     * @persist
     */
    public bool $active;

    /**
     * @validation nullable|max:255
     * @persist
     */
    public ?string $redirect = null;

    /**
     * @param \Psr\Http\Message\UriInterface $getUri
     * @return Model|null
     * @throws \Exception
     */
    public static function forUri(\Psr\Http\Message\UriInterface $getUri)
    {
        return self::hydrateOne("stub=:stub AND active=:active",['stub'=>$getUri->getPath(),'active'=>1]);
    }
}