<?php

namespace ccheng\config\console\migrations;

use yii\db\Migration;

class m201009134709_cc_task extends Migration
{
    public function safeUp()
    {
        $sql = <<<EOF
        CREATE TABLE `cc_category` (
          `cc_category_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `cc_category_type` varchar(16) NOT NULL COMMENT '分类类型',
          `cc_category_name` varchar(255) NOT NULL COMMENT '分类名称',
          `cc_category_p_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
          `cc_category_level` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '级别',
          `cc_category_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]',
          `cc_category_created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          `cc_category_updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
          `cc_category_sort` int(11) unsigned DEFAULT '0' COMMENT '排序',
          `cc_category_icon` text COMMENT '图标',
          `cc_category_tree` varchar(300) DEFAULT NULL COMMENT '树',
          `cc_category_code` varchar(64) NOT NULL COMMENT '分类编码',
          `cc_category_subset_count` int(11) unsigned DEFAULT '0' COMMENT '子类型计数',
          PRIMARY KEY (`cc_category_id`) USING BTREE,
          UNIQUE KEY `idx_code` (`cc_category_code`),
          KEY `idx_category` (`cc_category_type`,`cc_category_p_id`,`cc_category_level`,`cc_category_status`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;
        CREATE TABLE `cc_config_value` (
          `cc_config_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `cc_config_value_app_id` varchar(20) NOT NULL DEFAULT '' COMMENT '应用',
          `cc_config_value_config_id` int(10) NOT NULL DEFAULT '0' COMMENT '配置id',
          `cc_config_value_user_id` int(10) unsigned DEFAULT '0' COMMENT '商户id',
          `cc_config_value_data` text COMMENT '配置值',
          `cc_config_value_created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          `cc_config_value_updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
          PRIMARY KEY (`cc_config_value_id`) USING BTREE,
          UNIQUE KEY `idx_config` (`cc_config_value_app_id`,`cc_config_value_user_id`,`cc_config_value_config_id`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COMMENT='公用_配置值表';
        CREATE TABLE `cc_config` (
          `cc_config_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
          `cc_config_title` varchar(50) NOT NULL DEFAULT '' COMMENT '配置标题',
          `cc_config_name` varchar(50) NOT NULL DEFAULT '' COMMENT '配置标识',
          `cc_config_app_id` varchar(20) NOT NULL DEFAULT '' COMMENT '应用',
          `cc_config_type` varchar(30) NOT NULL DEFAULT '' COMMENT '配置类型',
          `cc_config_category_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '配置分类',
          `cc_config_extra` json NOT NULL COMMENT '配置值',
          `cc_config_remark` varchar(1000) NOT NULL DEFAULT '' COMMENT '配置说明',
          `cc_config_default_value` varchar(500) DEFAULT '' COMMENT '默认配置',
          `cc_config_sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
          `cc_config_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态[-1:删除;0:禁用;1启用]',
          `cc_config_created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
          `cc_config_updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
          PRIMARY KEY (`cc_config_id`) USING BTREE,
          KEY `type` (`cc_config_type`) USING BTREE,
          KEY `group` (`cc_config_category_id`) USING BTREE,
          KEY `uk_name` (`cc_config_name`) USING BTREE
        ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='公用_配置表';

EOF;
        return $this->execute($sql);
    }

    public function safeDown()
    {
        $sql = <<<EOF
DROP TABLE `cc_category`;DROP TABLE `cc_config_value`;DROP TABLE `cc_config`;
EOF;
        return $this->execute($sql);
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
