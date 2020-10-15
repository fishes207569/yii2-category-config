<?php

namespace ccheng\config\common\services;

use ccheng\config\common\enums\StatusEnum;
use ccheng\config\common\models\Category;

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
}