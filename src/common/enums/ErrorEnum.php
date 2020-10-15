<?php

namespace ccheng\config\common\enums;

use ccheng\task\common\abstracts\Enum;
use Exception;

class ErrorEnum extends Enum
{

    const  RESULT_CODE_SUCCESS = 0;
    const  RESULT_CODE_FAILED = 1;
    const ERROR_CODE_SYSTEM_ERROR = 10000;

    public static function getMap(): array
    {
        return [
            self::RESULT_CODE_SUCCESS => '成功',
            self::RESULT_CODE_FAILED => '失败',
            self::ERROR_CODE_SYSTEM_ERROR => '系统错误',
        ];
    }

    /**
     * 抛出异常
     * @param $code
     * @throws Exception
     */
    public static function throwException($code)
    {
        throw new Exception(self::getValue($code), $code);
    }
}