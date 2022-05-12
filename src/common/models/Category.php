<?php

namespace ccheng\config\common\models;

use ccheng\config\common\helpers\ArrayHelper;

use common\helpers\StringHelper;
use yii\base\Model;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UnprocessableEntityHttpException;

/**
 * This is the model class for table "cc_category".
 *
 * @property int $cc_category_id
 * @property string $cc_category_type 分类类型
 * @property string $cc_category_name 分类名称
 * @property int $cc_category_p_id 父级ID
 * @property int $cc_category_level 级别
 * @property status $cc_category_status 状态
 * @property int $cc_category_created_at 创建时间
 * @property int $cc_category_updated_at 修改时间
 * @property int $cc_category_sort 排序
 * @property int $cc_category_icon 分类图标
 * @property string $cc_category_tree 分类树
 * @property string $cc_category_code 分类编码
 * @property string $cc_category_config 分类配置
 * @property int $cc_category_subset_count 子分类计数
 * @property Category $parent
 * @property array $configs
 *
 * @SWG\Definition(
 *      definition="category",
 *      type="object",
 *      @SWG\Property(property="cc_category_id", type="integer", description="分类ID",example=1),
 *      @SWG\Property(property="cc_category_type", ref="#/definitions/category_type")),
 * @SWG\Property(property="cc_category_name", type="string", description="分类名称",example="系统配置"),
 * @SWG\Property(property="cc_category_p_id", type="integer", description="父级ID",example="0"),
 * @SWG\Property(property="cc_category_level", type="integer", description="级别",example=1),
 * @SWG\Property(property="cc_category_status", ref="#/definitions/model_status"),
 * @SWG\Property(property="cc_category_created_at", type="integer", description="创建时间", example="1604039236"),
 * @SWG\Property(property="cc_category_updated_at", type="integer", description="修改时间", example="1604039236"),
 * @SWG\Property(property="cc_category_sort", type="integer", description="排序", example="1"),
 * @SWG\Property(property="cc_category_icon", type="string", description="分类图标", example="https://bkimg.cdn.bcebos.com/pic/c8ea15ce36d3d539ea45c4bd3a87e950352ab050?x-bce-process=image/resize,m_lfit,w_174,limit_1"),
 * @SWG\Property(property="cc_category_tree", type="string", description="分类树", example="0 1"),
 * @SWG\Property(property="cc_category_code", type="string", description="分类编码", example="dsfsdgdfsg"),
 * @SWG\Property(property="cc_category_config", type="string", description="分类配置", example="{}"),
 * @SWG\Property(property="cc_category_subset_count", type="integer", description="子分类计数", example="2"),
 * @SWG\Property(property="Category",type="object",description="分类 Model"),
 * )
 */
class Category extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cc_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cc_category_name','cc_category_code','cc_category_type'],'required'],
            [[
                'cc_category_id',
                'cc_category_p_id',
                'cc_category_level',
                'cc_category_created_at',
                'cc_category_updated_at',
                'cc_category_sort',
                'cc_category_subset_count'
            ], 'integer'],
            ['cc_category_level', 'default', 'value' => function () {
                if ($this->cc_category_p_id) {
                    $parent_level = $this->parent->cc_category_level ?? 0;
                    return ++$parent_level;
                } else {
                    return 1;
                }
            }],
            ['cc_category_type', function ($attribute, $params) {
                if (!$this->isNewRecord && $this->cc_category_level == 1) {
                    $exists = self::find()->where(['and', ['!=', 'cc_category_id', $this->cc_category_id], ['cc_category_level' => 1, 'cc_category_type' => $this->cc_category_type]])->exists();
                    if ($exists) {
                        $this->addError($attribute, '同一类型的顶级分类已存在！');
                    }
                }
            }],
            [['cc_category_type', 'cc_category_code'], 'string', 'max' => 16],
            [['cc_category_name', 'cc_category_tree'], 'string', 'max' => 255],
            ['cc_category_icon', 'safe'],
            ['cc_category_code', 'default', 'value' => function () {
                return StringHelper::random(16);
            }],
            ['cc_category_code', 'unique'],
            ['cc_category_p_id', 'default', 'value' => 0],
            ['cc_category_config', 'default', 'value' => '{}'],
            ['cc_category_config', 'filter', 'filter' => function ($value) {
                if (is_array($value)) {
                    return $value;
                } else {
                    return json_decode($value,true);
                }
            }],
            ['cc_category_config', 'validateJson'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'cc_category_id' => 'ID',
            'cc_category_type' => '分类类型',
            'cc_category_name' => '分类名称',
            'cc_category_p_id' => '父级ID',
            'cc_category_level' => '层级',
            'cc_category_status' => '状态',
            'cc_category_created_at' => '创建时间',
            'cc_category_updated_at' => '更新时间',
            'cc_category_sort' => '排序',
            'cc_category_icon' => '分类图标',
            'cc_category_tree' => '树',
            'cc_category_code' => '分类编码',
            'cc_category_subset_count' => '子类计数',
            'cc_category_config' => '分类配置'
        ];
    }

    public function validateJson($attribute, $params)
    {
        if (!is_array($this->$attribute)) {
            json_decode(trim($this->$attribute));
            if (json_last_error() != JSON_ERROR_NONE) {
                return $this->addError($attribute, "不是Json格式");
            }
        }

    }

    /**
     * 关联父级
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::class, ['cc_category_id' => 'cc_category_p_id']);
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['cc_category_created_at', 'cc_category_updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['cc_category_updated_at']
                ]
            ]
        ];
    }


    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        // 处理上下级关系
        $this->autoUpdateTree();

        $old_pid = $this->getOldAttribute($this->cc_category_p_id);
        if ($this->cc_category_p_id != $old_pid) {
            self::updateAllCounters(['cc_category_subset_count' => 1], ['cc_category_id' => $this->cc_category_p_id]);
            self::updateAllCounters(['cc_category_subset_count' => -1], ['cc_category_id' => $old_pid]);
        }


        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->cc_category_level == 1) {
            $exists = self::find()->where(['and', ['!=', 'cc_category_id', $this->cc_category_id], ['cc_category_level' => 1, 'cc_category_type' => $this->cc_category_type]])->exists();
            if ($exists) {
                throw new UnprocessableEntityHttpException('同一类型的顶级分类已存在！');
            }
        }

        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        // 自动删除所有下级
        $this->autoDeleteTree();
        if ($this->parent) {
            $this->parent->updateCounters(['cc_category_subset_count' => -1]);
        }
        return parent::beforeDelete();
    }

    /**
     * 自动删除所有下级
     */
    protected function autoDeleteTree()
    {
        self::deleteAll(['like', 'cc_category_tree', $this->cc_category_tree .' '. $this->cc_category_id . '%', false]);
    }

    /**
     * 自动更新树
     *
     * @param bool $insert
     * @return bool
     */
    protected function autoUpdateTree()
    {
        if ($this->isNewRecord) {
            if ($this->cc_category_p_id == 0) {
                $this->cc_category_tree = 0;
            } else {
                list($level, $tree) = $this->getParentData();
                $this->cc_category_level = $level;
                $this->cc_category_tree = $tree;
            }
        } else {
            // 修改父类
            if (isset($this->oldAttributes['cc_category_p_id']) && $this->oldAttributes['cc_category_p_id'] != $this->cc_category_p_id) {
                list($level, $tree) = $this->getParentData();
                // 查找所有子级
                $list = self::find()
                    ->where(['like', 'cc_category_tree', $this->cc_category_tree . $this->cc_category_id . '%', false])
                    ->select(['cc_category_id', 'cc_category_level', 'cc_category_tree', 'cc_category_p_id'])
                    ->asArray()
                    ->all();

                $distanceLevel = $level - $this->cc_category_level;
                // 递归修改
                $data = ArrayHelper::itemsMerge($list, $this->cc_category_id, 'cc_category_id', 'cc_category_p_id');
                $this->recursionUpdate($data, $distanceLevel, $tree);

                $this->cc_category_level = $level;
                $this->cc_category_tree = $tree;
            }
        }
    }

    public function transactions()
    {
        return [Model::SCENARIO_DEFAULT => self::OP_ALL];
    }

    /**
     * 递归更新数据
     *
     * @param $data
     * @param $distanceLevel
     * @param $tree
     */
    protected function recursionUpdate($data, $distanceLevel, $tree)
    {
        $updateIds = [];
        $itemLevel = '';
        $itemTree = '';
        foreach ($data as $item) {
            $updateIds[] = $item['cc_category_id'];
            empty($itemLevel) && $itemLevel = $item['cc_category_level'] + $distanceLevel;
            empty($itemTree) && $itemTree = str_replace($this->cc_category_tree, $tree, $item[$this->cc_category_tree]);
            !empty($item['-']) && $this->recursionUpdate($item['-'], $distanceLevel, $tree);

            unset($item);
        }

        !empty($updateIds) && self::updateAll(['cc_category_level' => $itemLevel, $this->cc_category_tree => $itemTree], ['in', 'cc_category_id', $updateIds]);
    }

    /**
     * @return array
     */
    protected function getParentData()
    {
        if (!$parent = $this->parent) {
            return [1, 0];
        }

        $level = $parent->cc_category_level + 1;
        $tree = $parent->cc_category_tree . ' ' . ($parent->cc_category_id ?? 0);

        return [$level, $tree];
    }

    public function getConfigs()
    {
        return $this->hasMany(Config::class, ['cc_config_category_id' => 'cc_category_id']);
    }
}