<?php

namespace ccheng\config\common\models;

use ccheng\config\common\helpers\ArrayHelper;

use common\helpers\StringHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

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
 * @property int $cc_category_subset_count 子分类计数
 * @property Category $parent
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
                    $exists = self::find()->where(['and', ['!=', 'cc_category_id', $this->cc_category_id], ['cc_category_level' => 1]])->exists();
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
            'cc_category_subset_count' => '子类计数'
        ];
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

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        // 自动删除所有下级
        $this->autoDeleteTree();
        if ($this->cc_category_subset_count) {
            if ($this->parent) {
                $this->parent->updateCounters(['cc_category_subset_count' => -1]);
            }

        }
        return parent::beforeDelete();
    }

    /**
     * 自动删除所有下级
     */
    protected function autoDeleteTree()
    {
        self::deleteAll(['like', 'cc_category_tree', $this->cc_category_tree . $this->cc_category_id . '%', false]);
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
                $data = ArrayHelper::itemsMerge($list, $this->cc_category_id);
                $this->recursionUpdate($data, $distanceLevel, $tree);

                $this->cc_category_level = $level;
                $this->cc_category_tree = $tree;
            }
        }
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
            $updateIds[] = $item['id'];
            empty($itemLevel) && $itemLevel = $item['cc_category_level'] + $distanceLevel;
            empty($itemTree) && $itemTree = str_replace($this->cc_category_tree, $tree, $item[$this->cc_category_tree]);
            !empty($item['-']) && $this->recursionUpdate($item['-'], $distanceLevel, $tree);

            unset($item);
        }

        !empty($updateIds) && self::updateAll(['level' => $itemLevel, $this->cc_category_tree => $itemTree], ['in', 'id', $updateIds]);
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
}