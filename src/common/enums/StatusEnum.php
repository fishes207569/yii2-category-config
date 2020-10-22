<?php

namespace ccheng\config\common\enums;

use ccheng\config\common\abstracts\Enum;

/**
 * Class StatusEnum
 * @package common\enums
 */
class StatusEnum extends Enum
{
    const STATUS_DISABLED = 'disabled';
    const STATUS_ENABLE = 'enabled';

    public static function getMap(): array
    {
        return [
            static::STATUS_DISABLED => '禁用',
            static::STATUS_ENABLE => '启用',
        ];
    }
}