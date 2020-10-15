<?php
namespace ccheng\config\common\helpers;

class ModelHelpers{
    public static function getModelError($model)
    {
        /** @var ActiveRecord $model */
        $errors = $model->getErrors();
        if (!is_array($errors)) {
            return true;
        }
        $firstError = array_shift($errors);
        if (!is_array($firstError)) {
            return true;
        }

        return array_shift($firstError);
    }
}