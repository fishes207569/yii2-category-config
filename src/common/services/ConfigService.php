<?php

namespace ccheng\config\common\services;

use ccheng\config\common\enums\StatusEnum;
use ccheng\config\common\models\Category;
use ccheng\config\common\models\Config;
use ccheng\config\common\constants\CacheConstant;
use yii\db\ActiveQuery;

class ConfigService
{
    public static function getQuery($status = null)
    {
        $query = Config::find();
        $query->filterWhere(['cc_category_status' => $status]);
        return $query;
    }

    /**
     * 从数据捞取配置
     * @param $code
     * @param string $app_id
     * @param int $user_id
     * @return array
     */
    public static function getConfigValueByCategoryCodeToDb($code, $app_id, $user_id)
    {
        $query = Category::find()->where(['cc_category_code' => $code])->innerJoinWith([
            'configs' => function (ActiveQuery $q1) use ($app_id, $user_id) {
                return $q1->andOnCondition(['cc_config_app_id' => $app_id]);
            },
            'configs.configValue' => function (ActiveQuery $q2) use ($user_id) {
                return $q2->andOnCondition(['cc_config_value_user_id' => $user_id]);
            }
        ]);
        $query->select(['cc_config_value_data', 'cc_config_name']);
        $query->indexBy('cc_config_name');
        return $query->column();
    }

    /**
     * 获取配置值
     * @param $code
     * @param string $app_id
     * @param int $user_id
     * @param bool $use_cache
     * @return array|mixed
     */
    public static function getConfigValueByCategoryCode($code, $app_id, $user_id, $use_cache = true)
    {
        if ($use_cache) {
            $cache_key = self::getConfigCacheKey($app_id, $user_id);
            if (\Yii::$app->cache->exists($cache_key)) {
                $result = \Yii::$app->cache->get($cache_key);
            } else {
                $result = self::getConfigValueByCategoryCodeToDb($code, $app_id, $user_id);
                \Yii::$app->cache->set($cache_key, $result);
            }
        } else {
            $result = self::getConfigValueByCategoryCodeToDb($code, $app_id, $user_id);
        }
        return $result;
    }

    public static function getConfigCacheKey($app_id, $user_id)
    {
        return CacheConstant::CACHE_CONFIG_PREFIX . $app_id . ':' . $user_id;
    }


}