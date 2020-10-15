<?php

namespace ccheng\config\api\models\forms\config_value;

use ccheng\config\common\helpers\ModelHelpers;
use ccheng\config\common\models\ConfigValue;
use yii\base\Model;
use yii\web\UnprocessableEntityHttpException;

class SaveForm extends Model
{
    public $app_id;
    public $user_id;
    public $config_data;

    public function rules()
    {
        return [
            [['app_id', 'user_id'], 'required'],
            [['app_id', 'user_id'], 'integer'],
            ['config_data', function ($attribute, $params) {
                if (empty($this->$attribute)) {
                    $this->addError($attribute, '配置数据不能为空');
                }
            }]
        ];
    }

    public function save()
    {
        $config_ids = array_keys($this->config_data);
        $config_value_models = ConfigValue::find()
            ->where([
                'cc_config_value_app_id' => $this->app_id,
                'cc_config_value_user_id' => $this->user_id,
                'cc_config_value_config_id' => $config_ids
            ])->all();
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            foreach ($config_value_models as $config_value_model) {
                /** @var ConfigValue $config_value_model */
                $config_value_model->cc_config_value_data = $this->config_data[$config_value_model->cc_config_value_config_id];
                if (!$config_value_model->save()) {
                    $transaction->rollBack();
                    throw new \Exception(ModelHelpers::getModelError($config_value_model));
                }
                unset($this->config_data[$config_value_model->cc_config_value_config_id]);

            }
            $new_config_value_data = [];
            $time = time();
            foreach ($this->config_data as $config_id => $config_value) {
                $data = [];
                $data['cc_config_value_app_id'] = $this->app_id;
                $data['cc_config_value_user_id'] = $this->user_id;
                $data['cc_config_value_config_id'] = $config_id;
                $data['cc_config_value_data'] = $config_value;
                $data['cc_config_value_created_at'] = $time;
                $data['cc_config_value_updated_at'] = $time;
                $new_config_value_data[] = $data;
            }
            if (!empty($new_config_value_data)) {
                if (!\Yii::$app->db->createCommand()->batchInsert(ConfigValue::tableName(), array_keys($new_config_value_data[0]), $new_config_value_data)->execute()) {
                    $transaction->rollBack();
                    throw new \Exception('配置保存失败');
                }
            }
            $transaction->commit();
        } catch (\Exception $e) {
            throw new UnprocessableEntityHttpException($e->getMessage());
        }

    }
}