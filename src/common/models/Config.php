<?php

namespace ccheng\config\common\models;

use ccheng\config\common\enums\ConfigTypeEnum;
use ccheng\config\common\helpers\ArrayHelper;

use common\helpers\StringHelper;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cc_config".
 *
 * @property int $cc_config_id
 * @property string $cc_config_title 配置类型
 * @property string $cc_config_type 配置类型
 * @property string $cc_config_name 配置名称
 * @property int $cc_config_category_id 配置分类
 * @property int $cc_config_app_id 应用
 * @property string $cc_config_extra 配置值
 * @property string $cc_config_remark 配置说明
 * @property string $cc_config_status 状态[-1:删除;0:禁用;1启用]
 * @property int $cc_config_created_at 创建时间
 * @property int $cc_config_updated_at 修改时间
 * @property int $cc_config_sort 排序
 * @property Category $category
 * @property ConfigValue $configValue
 * @property array $configValues
 */
class Config extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_config';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'cc_config_id',
                'cc_config_category_id',
                'cc_config_created_at',
                'cc_config_updated_at',
                'cc_config_sort'
            ], 'integer'],
            [['cc_config_app_id'], 'safe'],
            [['cc_config_name', 'cc_config_title', 'cc_config_status'], 'string', 'max' => 50],
            ['cc_config_type', 'in', 'range' => ConfigTypeEnum::getKeys()],
            [['cc_config_type'], 'string', 'max' => 30],
            ['cc_config_extra','checkJson'],
            [['cc_config_name', 'cc_config_remark'], 'string', 'max' => 255],
            ['cc_config_category_id', 'exist', 'targetClass' => Category::class, 'targetAttribute' => 'cc_category_id'],


        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cc_config_id' => 'ID',
            'cc_config_title' => '配置标题',
            'cc_config_type' => '配置类型',
            'cc_config_name' => '配置名称',
            'cc_config_category_id' => '父级ID',
            'cc_config_app_id' => '应用ID',
            'cc_config_extra' => '配置值',
            'cc_config_remark' => '配置说明',
            'cc_config_status' => '状态',
            'cc_config_created_at' => '创建时间',
            'cc_config_updated_at' => '更新时间',
            'cc_config_sort' => '排序'
        ];
    }

    /**
     * 关联父级
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['cc_category_id' => 'cc_config_category_id']);
    }

    public function checkJson($attribute, $params)
    {
        $this->$attribute = json_decode($this->$attribute, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            $this->addError($attribute, '数据不合法');
        } else {
            return true;
        }
    }

    /**
     * @param null $app_id
     * @param null $user_id
     * @return \yii\db\ActiveQuery
     */
    public function getConfigValue($user_id = null)
    {
        $query = $this->hasOne(ConfigValue::class, ['cc_config_value_config_id' => 'cc_config_id']);
        if ($user_id) {
            $query->andOnCondition(['cc_config_value_user_id' => $user_id]);
        }
        return $query;
    }

    public function getConfigValues()
    {
        return $this->hasMany(ConfigValue::class, ['cc_config_value_config_id' => 'cc_config_id']);
    }

    public function transactions()
    {
        return [Model::SCENARIO_DEFAULT => self::OP_ALL];
    }

    public function beforeDelete()
    {
        if (ConfigValue::deleteAll(['cc_config_value_config_id' => $this->cc_config_id])) {
            return parent::beforeDelete();
        } else {
            return false;
        }
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_config_created_at', 'cc_config_updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cc_config_updated_at']
                ]
            ]
        ];
    }

}