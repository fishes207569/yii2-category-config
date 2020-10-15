<?php

namespace ccheng\config\api\models\searchs;

use ccheng\config\common\base\BaseListSearch;
use ccheng\config\common\models\Category;
use ccheng\config\common\models\Config;

class ConfigSearch extends BaseListSearch
{
    public $cc_config_app_id;
    public $cc_config_category_id;
    public $cc_config_title;
    public $cc_config_name;

    public $modelClass = Config::class;

    public function rules()
    {
        return array_merge(parent::rules(), [
            [['cc_config_app_id', 'cc_config_title','cc_config_name'], 'string'],
            ['cc_config_category_id', 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'cc_category_id'],
        ]);
    }

    public function search()
    {
        $query = Config::find();
        $query->filterWhere([
            'cc_config_app_id' => $this->cc_config_app_id,
            'cc_config_category_id' => $this->cc_config_category_id,
            'cc_config_name' => $this->cc_config_name,
        ]);
        $query->andFilterWhere(['like', 'cc_config_title', $this->cc_config_title]);


        $query->orderBy($this->order_by);

        $this->calcPageData($count = $query->count());
        if($count){
            $column = $this->modelClass::getTableSchema()->getColumnNames();
            $query->select($column);
            return $query->offset($this->offset)->limit($this->limit)->asArray()->all();
        }else{
            return [];
        }
    }



}