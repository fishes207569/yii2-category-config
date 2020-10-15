<?php

namespace ccheng\config\api\models\searchs;

use ccheng\config\common\base\BaseListSearch;
use ccheng\config\common\models\Category;
use ccheng\config\common\models\Config;
use ccheng\config\common\models\ConfigValue;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class ConfigValueSearch extends Model
{
    public $cc_config_value_app_id;
    public $cc_config_value_user_id;
    public $cc_config_category_id;

    public function rules()
    {
        return [
            ['cc_config_value_app_id', 'string'],
            ['cc_config_category_id', 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'cc_category_id'],
            ['cc_config_name', 'exist', 'targetClass' => Config::class, 'targetAttribute' => 'cc_config_name'],
        ];
    }

    public function search()
    {
        $query = Config::find();
        $query->where([
            'cc_config_category_id' => $this->cc_config_category_id,
        ]);
        $app_id = $this->cc_config_value_app_id;
        $user_id = $this->cc_config_value_user_id;
        $query->with(['configValue' => function (ActiveQuery $q) use ($app_id, $user_id) {
            $condition = [];
            $app_id && $condition['cc_config_value_app_id'] = $app_id;
            $user_id && $condition['cc_config_value_user_id'] = $user_id;
            !empty($condition) && $q->andOnCondition($condition);
            return $q;
        }]);
        $list = $query->all();
        $config_data = [];
        foreach ($list as $config) {
            /** @var $config Config */
            $item=$config->toArray();
            $item['cc_config_value'] = $config->configValue ? $config->configValue->cc_config_value_data : $config->cc_config_default_value;

            $config_data[] = $item;
        }
        return $config_data;
    }


}