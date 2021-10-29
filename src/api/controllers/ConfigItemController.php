<?php

namespace ccheng\config\api\controllers;

use ccheng\config\api\models\searchs\ConfigItemSearch;
use ccheng\config\api\models\searchs\ConfigSearch;
use ccheng\config\common\helpers\ModelHelpers;
use ccheng\config\common\models\Category;
use ccheng\config\common\models\Config;
use yii\web\UnprocessableEntityHttpException;

class ConfigItemController extends BaseController
{
    public $modelClass = Config::class;

    public function actionIndex()
    {
        $form = new ConfigSearch();
        $form->load($this->params, '');
        return $form->getList();
    }

    public function actionList()
    {
        $form = new ConfigItemSearch();
        $form->load($this->params, '');
        $list = ['list' => []];
        if ($form->validate()) {
            $list['list'] = $form->search();
        }
        return $list;
    }

    public function actionCreate()
    {
        $model = new $this->modelClass();
        if ($model->load($this->params, '') && $model->save()) {
            return $model;
        } else {
            $error = ModelHelpers::getModelError($model);
            throw new UnprocessableEntityHttpException($error);
        }
    }

    public function actionUpdate()
    {
        /** @var Config $model */
        $model = $this->getModel();
        $model->load($this->params, '');
        if (!empty($model->dirtyAttributes)) {
            if ($model->validate() && $model->save(false)) {
                return $model;
            } else {
                $error = ModelHelpers::getModelError($model);
                throw new UnprocessableEntityHttpException($error);
            }
        } else {
            throw new UnprocessableEntityHttpException('数据未更新');
        }
    }

    public function actionDelete()
    {
        /** @var Config $model */
        $model = $this->getModel();
        if ($model->delete()) {
            $model->unlinkAll('configValues', true);
        } else {
            throw new UnprocessableEntityHttpException(ModelHelpers::getModelError($model));
        }

    }
}