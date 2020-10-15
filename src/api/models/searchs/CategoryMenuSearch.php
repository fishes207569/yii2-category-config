<?php

namespace ccheng\config\api\models\searchs;

use ccheng\config\common\models\Category;
use ccheng\config\common\services\CategoryService;
use yii\base\Model;

class CategoryMenuSearch extends Model
{
    public $filter_ids;

    public $category_type;

    public function rules()
    {
        return [
            ['filter_ids', 'safe'],
            ['category_type', 'string']
        ];
    }

    public function search()
    {
        $query = Category::find();
        $query->select(['value' => 'cc_category_id', 'label' => 'cc_category_name', 'subset_count' => 'cc_category_subset_count']);
        $condition = ['!=', 'cc_category_id', $this->filter_ids];
        if ($this->category_type) {
            $condition = [
                'and',
                ['cc_category_type' => $this->category_type],
                $condition
            ];
        }
        $query->where(['cc_category_level' => 1]);
        $query->andFilterWhere($condition);
        $list = $query->asArray()->all();
        if ($this->category_type) {
            return $this->getChildren($list, $condition);
        } else {
            $root_node = [
                'value' => '0',
                'label' => '根分类',
                'children' => []
            ];

            $root_node['children'] = $this->getChildren($list, $condition);
            return [$root_node];
        }

    }

    protected function getChildren(&$children, $condition)
    {
        foreach ($children as &$item) {
            if ($item['subset_count'] > 0) {
                $child = CategoryService::getCategoryByPid($item['value'], ['value' => 'cc_category_id', 'label' => 'cc_category_name', 'subset_count' => 'cc_category_subset_count'], $condition);
                $item['children'] = $this->getChildren($child, $condition);
            }
        }
        return $children;
    }
}