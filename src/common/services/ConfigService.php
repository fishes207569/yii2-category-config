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
        $query->filterWhere(['cc_config_status' => $status]);
        return $query;
    }

    /**
     * 从分类捞取配置
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
        $query->select('cc_config_value_data');
        //$sql = $query->createCommand()->getRawSql();
        return $query->scalar();
    }

    /**
     * 从数据捞取配置
     * @param $code
     * @param string $app_id
     * @param int $user_id
     * @return array
     */
    public static function getConfigValueByConfigNameToDb($code, $app_id, $user_id , $status = StatusEnum::STATUS_ENABLE)
    {
        $query = self::getQuery($status);
        $query ->andWhere(['cc_config_name'=>$code,'cc_config_app_id'=>$app_id])
            ->innerJoinWith(['configValue' => function (ActiveQuery $q2) use ($user_id) {
                return $q2->andOnCondition(['cc_config_value_user_id' => $user_id]);
            }]);
        $query->select('cc_config_value_data');
        //$sql = $query->createCommand()->getRawSql();
        return $query->scalar();
    }


    public static function getConfigValueByCategoryCode($code, $app_id, $user_id, $use_cache = true)
    {
        if ($use_cache) {
            $cache_key = self::getConfigCacheKey($code, $app_id, $user_id);
            if (\Yii::$app->cache->exists($cache_key)) {
                $result = \Yii::$app->cache->get($cache_key);
            } else {
                $result = self::getConfigValueByConfigNameToDb($code, $app_id, $user_id);
                \Yii::$app->cache->set($cache_key, $result);
            }
        } else {
            $result = self::getConfigValueByConfigNameToDb($code, $app_id, $user_id);
        }
        return $result;
    }

    public static function getConfigCacheKey($code, $app_id, $user_id)
    {
        return CacheConstant::CACHE_CONFIG_PREFIX . $code .':'. $app_id . ':' . $user_id;
    }


}