<?php declare(strict_types=1);

namespace App\Controller;

use \System\View\PlatesController;

class ErrorController extends PlatesController
{
    public function http(int $code, string $message, $exception=null)
    {
        $templateName = (string)$code;
        if (!$this->templates->exists("errors::".$templateName)) {
            $templateName = '500';
        }
        if (!$this->templates->exists("errors::".$templateName)) {
            $templateName = 'default';
        }
        if ($this->templates->exists("errors::".$templateName)) {
            $message = $exception->getMessage();
            return $this->render('errors::'.$code, compact('code','message','exception'));
        }
        else {
            die("You must setup at least error page pages for code=".$code." message=".$message);
        }
    }
}