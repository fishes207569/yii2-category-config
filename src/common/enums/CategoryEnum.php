<?php

namespace ccheng\config\common\enums;

use ccheng\config\common\abstracts\Enum;

/**
 * Class SystemEnum
 * @package common\enums
 */
class CategoryEnum extends Enum
{
    const CATEGORY_TYPE_CONFIG = 'config';

    public static function getMap(): array
    {
        return [
            static::CATEGORY_TYPE_CONFIG => '配置'
        ];
    }
}