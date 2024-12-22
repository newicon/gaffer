<?php declare(strict_types=1);

namespace App\Form;

use Deform\Html\HtmlTag;
use Gaffer\Db\Model;
use Gaffer\Form\App;
use Psr\Http\Message\ServerRequestInterface;

/**
 * basic form processor
  */
abstract class FormModel extends \Deform\Form\FormModel
{
    /** @return Model */
    abstract function getModel():Model;

    /**
     * @param array $data
     * @return bool
     * @throws \Exception
     */
    public function validate(array $data): bool
    {
        /** @var array|bool $validateOrErrors */
        $validateOrErrors = $this->getModel()->validate($data);
        if ($validateOrErrors===true) {
            return true;
        }
        else {
            array_walk($validateOrErrors, function(&$error) {
                $error = implode(", ", $error).".";
            });
            $this->setErrors($validateOrErrors);
        }
        return false;
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool|int
     * @throws \Exception
     */
    public function processed(ServerRequestInterface $request): bool|int
    {
        $body = $request->getParsedBody();
        if (!$body || !isset($body[$this->getNamespace()])) {
            return false;
        }
        $formData = $body[$this->getNamespace()];
        $modelData = $this->populateFormData($formData);
        if ($this->validate($modelData)) {
            // handle file uploads if there are any
            $files = $request->getUploadedFiles();
            if (count($files)>0 && isset($files[$this->getNamespace()])) {
                foreach ($files[$this->getNamespace()] as $field=>$uploadedImage) {
                    if (!$this->hasComponent($field)) {
                        throw new \Exception("File was uploaded for '".$field."' but there is no corresponding form component");
                    }
                    switch ($uploadedImage->getError()) {
                        case UPLOAD_ERR_OK:
                            $uploadInfo = App::handleUploadedFile($uploadedImage);
                            $modelData[$field] = $uploadInfo['url'];
                            break;
                        case UPLOAD_ERR_NO_FILE:
                            // no upload file data present, so honour the $modelData entry
                            break;
                        default:
                            throw new \Exception("There was a problem processing an uploaded file for '".$field."' error number = ".$uploadedImage->getError());
                    }
                }
            }
            // populate and save
            if ($this->getModel()->populate($modelData)) {
                return $this->getModel()->save();
            }
        }
        return false;
    }

    /**
     * @return HtmlTag
     * @throws \Exception
     */
    public function html() : HtmlTag
    {
        $model = $this->getModel();
        $persistedFormData = $model->getPersistedDataArray();
        $this->populateFormData($persistedFormData, false);
        return $this->getFormHtml();
    }

}