<?php

namespace ccheng\config\common\base;

use ccheng\config\common\helpers\ModelHelpers;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\web\BadRequestHttpException;

/**
 * 列表表单基类
 * Class BaseListForm
 * @package api\forms
 * @property $count
 */
class BaseListSearch extends Model
{
    public $size = 10;
    public $page = 1;
    public $is_change = false;
    protected $limit;
    protected $offset = 0;
    protected $data;
    protected $count = 0;
    protected $pageCount = 0;

    public $order_by;

    /** @var ActiveRecord */
    public $modelClass;


    public function rules()
    {
        return [
            [['size'], 'integer'],
            ['page', 'integer', 'integerOnly' => true, 'min' => 1],
            ['order_by', 'filter', 'filter' => function ($value) {
                $order_str = '';
                if (is_array($value)) {
                    foreach ($value as $field => $sort) {
                        if (in_array($field, $this->modelClass::getTableSchema()->getColumnNames())) {
                            $order_str .= ($field . ' ' . $sort);
                        }
                    }
                } else {
                    $order_str = $this->getDefaultOrder();
                }
                return $order_str;
            }]
        ];
    }

    private function getDefaultOrder()
    {
        return $this->modelClass::getTableSchema()->primaryKey . ' asc';
    }

    /**
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function getList()
    {
        if ($this->validate()) {
            $result['list'] = $this->search();
            $result['page_size'] = $this->size;
            $result['page'] = $this->page;
            $result['total'] = $this->count;
            $result['page_count'] = $this->pageCount;
            return $result;
        } else {
            throw new BadRequestHttpException(ModelHelpers::getModelError($this));
        }
    }

    /**
     * 计算分页信息
     * @param $count
     */
    public function calcPageData($count)
    {
        if ($this->count = $count) {
            if ($this->is_change) {
                $this->pageCount = intval(ceil(($this->count - $this->size) / ($this->size - 1))) + 1;
            } else {
                $this->pageCount = intval(ceil($this->count / $this->size));
            }
        } else {
            $this->pageCount = 0;
        }

        $normal_offset = ($this->page - 1) * $this->size;
        $this->offset = $this->page == 1 ? 0 : ($this->is_change ? ($normal_offset + 1) : $normal_offset);
        $this->limit = $this->size;
    }


    //数据获取逻辑
    public function search()
    {
        //todo 子类具体实现
    }
}