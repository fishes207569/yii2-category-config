<?php

namespace ccheng\config\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cc_config_value".
 *
 * @property int $cc_config_value_id
 * @property string $cc_config_value_app_id 应用类型
 * @property string $cc_config_value_config_id 配置类型
 * @property int $cc_config_value_user_id 配置名称
 * @property string $cc_config_value_data 配置分类
 * @property int $cc_config_value_created_at 创建时间
 * @property int $cc_config_value_updated_at 更新时间
 */
class ConfigValue extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_config_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'cc_config_value_id',
                'cc_config_value_config_id',
                'cc_config_value_created_at',
                'cc_config_value_updated_at',
                'cc_config_value_user_id'
            ], 'integer'],
            ['cc_config_value_config_id', 'exist', 'targetClass' => Config::class, 'targetAttribute' => 'cc_config_id'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cc_config_value_id' => 'ID',
            'cc_config_value_app_id' => '应用',
            'cc_config_value_config_id' => '配置ID',
            'cc_config_value_user_id' => '用户ID',
            'cc_config_value_data' => '配置数据',
            'cc_config_value_created_at' => '创建时间',
            'cc_config_value_updated_at' => '更新时间'
        ];
    }

    /**
     * 关联父级
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConfig()
    {
        return $this->hasOne(Config::class, ['cc_config_id' => 'cc_config_value_config_id']);
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_config_value_created_at', 'cc_config_value_updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cc_config_value_updated_at']
                ]
            ]
        ];
    }

}