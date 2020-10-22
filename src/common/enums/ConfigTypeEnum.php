<?php

namespace ccheng\config\common\enums;

use ccheng\config\common\abstracts\Enum;

/**
 * Class ConfigEnum
 * @package common\enums
 */
class ConfigTypeEnum extends Enum
{
    const CONFIG_TYPE_TEXT = 'text';
    const CONFIG_TYPE_NUMBER = 'number';
    const CONFIG_TYPE_PASSWORD = 'password';
    const CONFIG_TYPE_TEXTAREA = 'textarea';
    const CONFIG_TYPE_DATE = 'date';
    const CONFIG_TYPE_TIME = 'time';
    const CONFIG_TYPE_DATETIME = 'datetime';
    const CONFIG_TYPE_SELECT = 'select';
    const CONFIG_TYPE_INPUT_GROUP = 'input_group';
    const CONFIG_TYPE_RADIO = 'radio';
    const CONFIG_TYPE_CHECKBOX = 'checkbox';
    const CONFIG_TYPE_EDITOR = 'editor';
    const CONFIG_TYPE_IMAGE = 'image';
    const CONFIG_TYPE_FILE = 'file';
    const CONFIG_TYPE_JSON = 'json';

    public static function getMap(): array
    {
        return [
            static::CONFIG_TYPE_TEXT => '文本',
            static::CONFIG_TYPE_NUMBER => '数字',
            static::CONFIG_TYPE_PASSWORD => '密码',
            static::CONFIG_TYPE_TEXTAREA => '文本域',
            static::CONFIG_TYPE_DATE => '日期',
            static::CONFIG_TYPE_TIME => '时间',
            static::CONFIG_TYPE_DATETIME => '日期时间',
            static::CONFIG_TYPE_SELECT => '下拉框',
            static::CONFIG_TYPE_INPUT_GROUP => '输入组',
            static::CONFIG_TYPE_RADIO => '单选框',
            static::CONFIG_TYPE_CHECKBOX => '复选框',
            static::CONFIG_TYPE_EDITOR => '编辑器',
            static::CONFIG_TYPE_IMAGE => '图片上传',
            static::CONFIG_TYPE_FILE => '文件上传',
            static::CONFIG_TYPE_JSON => 'JSON'
        ];
    }
}