<?php

namespace ccheng\config\api\models\searchs;

use ccheng\config\common\models\Category;
use ccheng\config\common\models\Config;
use yii\base\Model;
use yii\db\ActiveQuery;

class ConfigItemSearch extends Model
{
    public $cc_config_app_id;


    public function rules()
    {
        return [
            ['cc_config_app_id', 'string'],
        ];
    }

    public function search()
    {
        $query = Config::find();
        $query->select('cc_config_category_id');
        $query->where([
            'cc_config_app_id' => $this->cc_config_app_id,
        ]);
        $category_ids = $query->column();
        $category_data = [];

        if (!empty($category_ids)) {
            $category_ids = array_unique($category_ids);
            $arr = $this->getCategoryConfig($category_ids);
            $category_data = $this->formatData($arr);
        }

        return $category_data;
    }

    private function getCategoryConfig($category_ids, $exist_ids = [])
    {
        static $result = [];
        $categorys = Category::findAll($category_ids);
        $parent_ids = [];
        foreach ($categorys as $category) {
            if (!is_numeric(array_search($category->cc_category_id, $exist_ids))) {
                $result[$category->cc_category_level][$category->cc_category_id] = $category->toArray();
                $exist_ids[] = $category->cc_category_id;
            }

            /** @var $category Category */
            if ($category->cc_category_p_id != 0) {
                $parent_ids[] = $category->cc_category_p_id;
            }
        }

        if (!empty($parent_ids)) {
            $this->getCategoryConfig($parent_ids, $exist_ids);
        }
        return $result;
    }

    private function formatData($data)
    {
        if (!empty($data)) {
            $result = [];
            for ($i = count($data); $i > 0; $i--) {
                foreach ($data[$i] as $item) {
                    isset($item['children']) && $category['children'] = $item['children'];
                    if ($item['cc_category_p_id'] == 0 || $i == 1) {
                        $result[] = $item;
                    } else {
                        $data[$item['cc_category_level'] - 1][$item['cc_category_p_id']]['children'][] = $item;
                    }
                }
            }
            return $result;
        } else {
            return [];
        }

    }
}