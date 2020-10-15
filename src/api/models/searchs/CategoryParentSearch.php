<?php

namespace ccheng\config\api\models\searchs;

use ccheng\config\common\enums\CategoryEnum;
use ccheng\config\common\models\Category;
use ccheng\config\common\services\CategoryService;
use yii\base\Model;

class CategoryParentSearch extends Model
{
    public $cc_category_id;


    public function rules()
    {
        return [
            ['cc_category_id', 'in', 'range' => CategoryEnum::getKeys()],
            ['cc_category_id', 'exist', 'targetClass' => Category::class,'targetAttribute'=>'id'],
        ];
    }

    public function search()
    {
        $query = Category::find();
        $condition = ['cc_category_level' => 1];
        if ($this->cc_category_code) {
            $condition = ['cc_category_code' => $this->cc_category_code];
        }else if($this->cc_category_type){
            $condition['cc_category_type'] = $this->cc_category_type;
        }
        $query->filterWhere($condition);;
        $list = $query->asArray()->all();
        $this->getChildren($list);
        return $list;

    }

    protected function getChildren(&$children)
    {
        foreach ($children as &$item) {
            if ($item['cc_category_subset_count'] > 0) {
                $child = CategoryService::getCategoryByPid($item['cc_category_id']);
                $item['children'] = $this->getChildren($child);
            }
        }
        return $children;
    }
}