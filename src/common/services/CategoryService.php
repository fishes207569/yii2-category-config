<?php

namespace ccheng\config\common\services;

use ccheng\config\common\enums\StatusEnum;
use ccheng\config\common\models\Category;
use yii\db\ActiveQuery;
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
     * 根据 CODE 查分类
     * @param string $parent_code
     * @return Category|null
     */
    public static function getCategoryByCode(string $parent_code)
    {
        return Category::findOne(['cc_category_code' => $parent_code]);
    }

    /**
     * 根据父级 CODE 获取分类数据
     * @param string $parent_code
     * @param array $fields
     * @param string $orderBy 升序还是降序
     * @param int $limit 查询量
     * @return array
     */
    public static function getSubCategoryByCode(string $parent_code, array $fields = [], $orderMode = SORT_DESC, $limit = 99)
    {
        $parent = self::getCategoryByCode($parent_code);
        $query = self::getQuery(StatusEnum::STATUS_ENABLE);
        $query->where(['cc_category_p_id' => $parent->cc_category_id]);
        !empty($fields) && $query->select($fields);
        $query->orderBy(['cc_category_sort' => $orderMode]);
        $query->limit($limit);
        return $query->asArray()->all();
    }

    /**
     * 根据父级 CODE 获取分类 map
     * @param string $parent_code
     * @param string $index_field 具备唯一性的字段，处于 $fields 数组最后一个
     * @param array $fields
     * @param string $orderBy 升序还是降序
     * @param int $limit 查询量
     * @return array [1=>'xxxx',2=>'xxxx']
     */
    public static function getSubCategoryMapByCode(string $parent_code, string $index_field = 'id', array $fields = ['name' => 'cc_category_name', 'id' => 'cc_category_id'], $orderMode = SORT_DESC, $limit = 99)
    {
        $parent = self::getCategoryByCode($parent_code);
        $query = self::getQuery(StatusEnum::STATUS_ENABLE);
        $query->where(['cc_category_p_id' => $parent->cc_category_id]);
        $query->select($fields);
        $query->indexBy($index_field);
        $query->orderBy(['cc_category_sort' => $orderMode]);
        $query->limit($limit);
        return $query->column();
    }

    /**
     * 根据父级 ID 获取分类数据
     * @param int $parent_id
     * @param array $fields
     * @param string $orderBy 升序还是降序
     * @param int $limit 查询量
     * @return array
     */
    public static function getSubCategoryByParentId(int $parent_id, array $fields = [], $orderMode = SORT_DESC, $limit = 99)
    {
        $query = self::getQuery(StatusEnum::STATUS_ENABLE);
        $query->where(['cc_category_p_id' => $parent_id]);
        !empty($fields) && $query->select($fields);
        $query->orderBy(['cc_category_sort' => $orderMode]);
        $query->limit($limit);
        return $query->asArray()->all();
    }

    /**
     * 根据父级 ID 获取分类 map
     * @param int $parent_id
     * @param string $index_field 具备唯一性的字段，处于 $fields 数组最后一个
     * @param array $fields
     * @param string $orderBy 升序还是降序
     * @param int $limit 查询量
     * @return array [1=>'xxxx',2=>'xxxx']
     */
    public static function getSubCategoryMapByParentId(int $parent_id, string $index_field = 'id', array $fields = ['name' => 'cc_category_name', 'id' => 'cc_category_id'], $orderMode = SORT_DESC, $limit = 99)
    {
        $query = self::getQuery(StatusEnum::STATUS_ENABLE);
        $query->where(['cc_category_p_id' => $parent_id]);
        $query->select($fields);
        $query->indexBy($index_field);
        $query->orderBy(['cc_category_sort' => $orderMode]);
        $query->limit($limit);
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
        $parent = self::getCategoryByCode($parent_code);
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

    /**
     * 根据分类名查询指定分类下级
     * @param string $parent_code
     * @param string $category_name
     * @return array|\yii\db\ActiveRecord|null
     */
    public static function getCategoryByName(string $parent_code, string $category_name)
    {
        $query = self::getQuery(StatusEnum::STATUS_ENABLE);
        $query->alias('c')->innerJoinWith(['parent' => function (ActiveQuery $q) use ($parent_code) {
            return $q->alias('p')->andWhere(['p.cc_category_code' => $parent_code]);
        }])->where(['c.cc_category_name' => $category_name]);
        return $query->one();
    }

    /**
     * 更新分类排序
     * @param int $id
     * @param int $num
     * @return bool
     */
    public static function updateCategorySort(int $id, int $num)
    {
        return Category::updateAllCounters(['cc_category_sort' => $num], ['cc_category_id' => $id]);

    }
    /**
     * @param $filed
     * @param $code
     * @return mixed
     */
    public static function getFieldByCode($filed, $code)
    {
        return self::getQuery(StatusEnum::STATUS_ENABLE)
            ->select($filed)
            ->andWhere(['cc_category_code' => $code])
            ->scalar();
    }
}