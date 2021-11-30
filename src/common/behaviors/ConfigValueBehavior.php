<?php

namespace ccheng\config\common\behaviors;

use ccheng\config\common\models\ConfigValue;
use ccheng\config\common\services\ConfigService;
use yii\base\Behavior;
use yii\base\ModelEvent;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;
use yii\web\Controller;

class ConfigValueBehavior extends Behavior
{
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_DELETE => 'clearConfigCache',
            ActiveRecord::EVENT_AFTER_INSERT => 'clearConfigCache',
            ActiveRecord::EVENT_AFTER_UPDATE => 'clearConfigCache',
        ];
    }

    public function clearConfigCache($event)
    {
        $configValue = $event->sender;
        /** @var ConfigValue $configValue */
        $cache_key = ConfigService::getConfigCacheKey($configValue->config->cc_config_name,$configValue->config->cc_config_app_id, $configValue->cc_config_value_user_id);
        \Yii::$app->cache->delete($cache_key);
    }

}