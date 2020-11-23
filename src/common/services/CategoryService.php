<?php

namespace ccheng\config\common\services;

use ccheng\config\common\enums\StatusEnum;
use ccheng\config\common\models\Category;
use yii\web\UnprocessableEntityHttpException;

class CategoryService
{
    public static function getQuery($status = null)
    {
        $query = Category::find();
        $query->filterWhere(['cc_category_status' => $status]);
        return $query;
    }

    /**
     * 根据父级获取子集数据
     * @param $id
     * @param array $fields
     * @param array $condition
     * @param int $status
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCategoryByPid($id, $fields = [], $condition = [], $status = StatusEnum::STATUS_ENABLE)
    {
        $query = self::getQuery($status);
        $query->andWhere(['cc_category_p_id' => $id]);
        $query->andFilterWhere($condition);
        !empty($fields) && $query->select($fields);
        return $query->asArray()->all();
    }

    /**
     * 根据查询条件获取
     * @param array $condition
     * @param int $status
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getCategoryByCondition($condition = [], $status = StatusEnum::STATUS_ENABLE)
    {
        $query = self::getQuery($status);
        $query->andFilterWhere($condition);
        return $query->asArray()->all();
    }

    /**
     * 根据分类ID查父级ID
     * @param $id
     * @return array
     */
    public static function getParentIdsById($id)
    {
        $parent_ids = [$id];
        $model = Category::findOne($id);
        $parent = $model->parent;
        while (is_object($parent)) {
            array_unshift($parent_ids, $parent->cc_category_id);
            $parent = $parent->parent;
        }
        return $parent_ids;
    }

    /**
     * 获取 map
     * @param string $parent_code
     * @param string $index_field 具备唯一性的字段，处于 $fields 数组最后一个
     * @param array $fields
     * @return array [1=>'xxxx',2=>'xxxx']
     */
    public static function getSubCategoryByCode(string $parent_code, string $index_field = 'id', array $fields = ['name' => 'cc_category_name', 'id' => 'cc_category_id'])
    {
        $parent = Category::findOne(['cc_category_code' => $parent_code]);
        $query = self::getQuery(StatusEnum::STATUS_ENABLE);
        $query->where(['cc_category_p_id' => $parent->cc_category_id]);
        $query->select($fields);
        $query->indexBy($index_field);
        return $query->column();
    }

    /**
     * 根据父级 CODE 创建分类
     * @param string $parent_code
     * @param array $data
     * @return Category|null
     * @throws UnprocessableEntityHttpException
     * @throws \Throwable
     */
    public static function createCategoryByParent(string $parent_code, array $data)
    {
        $parent = Category::findOne(['cc_category_code' => $parent_code]);
        if ($parent) {
            if (!isset($data['cc_category_type']) || !$data['cc_category_type']) {
                $data['cc_category_type'] = $parent->cc_category_type;
            }
            if (!isset($data['cc_category_p_id']) || !$data['cc_category_p_id']) {
                $data['cc_category_p_id'] = $parent->cc_category_id;
            }
            $category = new Category();
            $category->load($data, '');
            return $category->insert() ? $category : null;
        } else {
            throw new UnprocessableEntityHttpException('父级分类不存在');
        }

    }
}