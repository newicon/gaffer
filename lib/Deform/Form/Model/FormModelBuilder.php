<?php
namespace Deform\Form\Model;

trait FormModelBuilder
{
    public function buildFromModel(IModel $model)
    {
        $fields = $model->getFields();
        $table = $model->getTable();
        $form = new FormModel($table);
        foreach ($fields as $field) {
            switch ($field['type']) {

            }
        }
        return $form;
    }
}