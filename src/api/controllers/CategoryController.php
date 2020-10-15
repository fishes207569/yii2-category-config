<?php

namespace ccheng\config\api\controllers;

use ccheng\config\api\models\searchs\CategoryMenuSearch;
use ccheng\config\api\models\searchs\CategorySearch;
use ccheng\config\common\helpers\ModelHelpers;
use ccheng\config\common\models\Category;
use ccheng\config\common\services\CategoryService;
use yii\web\UnprocessableEntityHttpException;

class CategoryController extends BaseController
{
    public $modelClass = Category::class;

    public function actionIndex()
    {
        $form = new CategorySearch();
        $form->load($this->params, '');
        $list = ['list' => []];
        if ($form->validate()) {
            $list['list'] = $form->search();
        }
        return $list;
    }

    public function actionMenu()
    {
        $form = new CategoryMenuSearch();
        $form->load($this->params, '');
        $list['tree'] = $form->search();
        return $list;
    }

    public function actionParents()
    {
        $id = $this->params['cc_category_id'];
        return CategoryService::getParentIdsById($id);
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
        /** @var Category $model */
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
        /** @var Category $model */
        $model = $this->getModel();
        if (!$model->delete()) {
            throw new UnprocessableEntityHttpException(ModelHelpers::getModelError($model));
        }

    }
}