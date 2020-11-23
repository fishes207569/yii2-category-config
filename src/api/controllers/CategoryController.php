<?php

namespace ccheng\config\api\controllers;

use ccheng\config\api\models\searchs\CategoryMenuSearch;
use ccheng\config\api\models\searchs\CategorySearch;
use ccheng\config\common\helpers\ModelHelpers;
use ccheng\config\common\models\Category;
use ccheng\config\common\services\CategoryService;
use Swagger\Annotations as SWG;
use yii\web\UnprocessableEntityHttpException;

class CategoryController extends BaseController
{
    public $modelClass = Category::class;


    /**
     * @SWG\Post(
     *     path="/config/category/index",
     *     summary="获取分类列表",
     *     tags={"分类管理"},
     *     description="查询分类数据列表",
     *     operationId="getCategoryList",
     *
     *     @SWG\Parameter(ref="#/parameters/header_timestamp"),
     *     @SWG\Parameter(ref="#/parameters/header_client_type"),
     *     @SWG\Parameter(ref="#/parameters/header_sign_auth_key"),
     *     @SWG\Parameter(ref="#/parameters/header_sign"),
     *     @SWG\Parameter(ref="#/parameters/header_x_device_code"),
     *     @SWG\Parameter(ref="#/parameters/header_x_api_token"),
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="category_type",
     *                  description="根据分类类型过滤",
     *                  ref="#/definitions/category_type"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_code",
     *                 type="string",
     *                 description="根据分类编码过滤",
     *             ),
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         ref="$/responses/default",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="$/definitions/pageData",
     *                  @SWG\Property(
     *                      property="list",
     *                      type="array",
     *                      @SWG\Items(ref="#/definitions/CategoryItem"),
     *                  )
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @SWG\Post(
     *     path="/config/category/menu",
     *     summary="获取分类树",
     *     tags={"分类管理"},
     *     description="查询分类数据列表",
     *     operationId="getCategoryMenu",
     *
     *     @SWG\Parameter(ref="#/parameters/header_timestamp"),
     *     @SWG\Parameter(ref="#/parameters/header_client_type"),
     *     @SWG\Parameter(ref="#/parameters/header_sign_auth_key"),
     *     @SWG\Parameter(ref="#/parameters/header_sign"),
     *     @SWG\Parameter(ref="#/parameters/header_x_device_code"),
     *     @SWG\Parameter(ref="#/parameters/header_x_api_token"),
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="filter_ids",
     *                  type="array",
     *                  description="排除不需要的分类",
     *                  @SWG\Items(type="integer")
     *              ),
     *              @SWG\Property(
     *                  property="category_type",
     *                  description="根据分类类型过滤",
     *                  ref="#/definitions/category_type"
     *             ),
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         ref="$/responses/default",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  @SWG\Property(
     *                      property="tree",
     *                      type="array",
     *                      @SWG\Items(ref="#/definitions/CategoryItem")
     *                  ),
     *              )
     *          )
     *      )
     * )
     */
    public function actionMenu()
    {
        $form = new CategoryMenuSearch();
        $form->load($this->params, '');
        $list['tree'] = $form->search();
        return $list;
    }

    /**
     * @SWG\Post(
     *     path="/config/category/create",
     *     summary="添加分类",
     *     tags={"分类管理"},
     *     description="添加分类数据",
     *     operationId="createCategory",
     *
     *     @SWG\Parameter(ref="#/parameters/header_timestamp"),
     *     @SWG\Parameter(ref="#/parameters/header_client_type"),
     *     @SWG\Parameter(ref="#/parameters/header_sign_auth_key"),
     *     @SWG\Parameter(ref="#/parameters/header_sign"),
     *     @SWG\Parameter(ref="#/parameters/header_x_device_code"),
     *     @SWG\Parameter(ref="#/parameters/header_x_api_token"),
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *              required={"cc_category_type","cc_category_p_id","cc_category_name","cc_category_code"},
     *             @SWG\Property(
     *                 property="cc_category_type",
     *                 description="分类类型",
     *                 ref="#/definitions/category_type"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_p_id",
     *                 type="integer",
     *                 description="父级ID"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_name",
     *                 type="string",
     *                 description="分类名称"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_code",
     *                 type="string",
     *                 description="分类编码"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_sort",
     *                 type="integer",
     *                 description="分类排序值",
     *                 default=0
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_icon",
     *                 type="string",
     *                 description="分类图标"
     *             ),
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         ref="$/responses/default",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/definitions/category"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @SWG\Post(
     *     path="/config/category/update",
     *     summary="编辑分类",
     *     tags={"分类管理"},
     *     description="编辑分类数据",
     *     operationId="updateCategory",
     *
     *     @SWG\Parameter(ref="#/parameters/header_timestamp"),
     *     @SWG\Parameter(ref="#/parameters/header_client_type"),
     *     @SWG\Parameter(ref="#/parameters/header_sign_auth_key"),
     *     @SWG\Parameter(ref="#/parameters/header_sign"),
     *     @SWG\Parameter(ref="#/parameters/header_x_device_code"),
     *     @SWG\Parameter(ref="#/parameters/header_x_api_token"),
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *              required={"cc_category_id","cc_category_type","cc_category_p_id","cc_category_name","cc_category_code"},
     *             @SWG\Property(
     *                 property="cc_category_id",
     *                 type="integer",
     *                 description="分类ID"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_type",
     *                 description="分类类型",
     *                 ref="#/definitions/category_type"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_p_id",
     *                 type="integer",
     *                 description="父级ID"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_name",
     *                 type="string",
     *                 description="分类名称"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_code",
     *                 type="string",
     *                 description="分类编码"
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_sort",
     *                 type="integer",
     *                 description="分类排序值",
     *                 default=0
     *             ),
     *             @SWG\Property(
     *                 property="cc_category_icon",
     *                 type="string",
     *                 description="分类图标"
     *             ),
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         ref="$/responses/default",
     *          @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  type="object",
     *                  ref="#/definitions/category"
     *              )
     *          )
     *      )
     * )
     */
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

    /**
     * @SWG\Post(
     *     path="/config/category/delete",
     *     summary="删除分类",
     *     tags={"分类管理"},
     *     description="删除分类数据",
     *     operationId="deleteCategory",
     *
     *     @SWG\Parameter(ref="#/parameters/header_timestamp"),
     *     @SWG\Parameter(ref="#/parameters/header_client_type"),
     *     @SWG\Parameter(ref="#/parameters/header_sign_auth_key"),
     *     @SWG\Parameter(ref="#/parameters/header_sign"),
     *     @SWG\Parameter(ref="#/parameters/header_x_device_code"),
     *     @SWG\Parameter(ref="#/parameters/header_x_api_token"),
     *
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *              required={"cc_category_id"},
     *             @SWG\Property(
     *                 property="cc_category_id",
     *                 type="integer",
     *                 description="分类ID"
     *             )
     *         )
     *     ),
     *
     *     @SWG\Response(
     *         response="200",
     *         ref="$/responses/default",
     *      )
     * )
     */
    public function actionDelete()
    {
        /** @var Category $model */
        $model = $this->getModel();
        if (!$model->delete()) {
            throw new UnprocessableEntityHttpException(ModelHelpers::getModelError($model));
        }

    }
}