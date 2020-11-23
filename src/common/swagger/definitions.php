<?php

namespace common\swagger;

/**
 * @SWG\Definition(
 *     definition="pageData",
 *     description="分页数据",
 *     type="object",
 *     @SWG\Property(
 *         property="page_size",
 *         description="分页数据量",
 *         type="integer"
 *     ),
 *     @SWG\Property(
 *          property="page",
 *          description="页码",
 *          type="integer"
 *      ),
 *     @SWG\Property(
 *         property="total",
 *         description="数据总量",
 *         type="integer"
 *      ),
 *      @SWG\Property(
 *         property="page_count",
 *         description="页码总数",
 *         type="integer"
 *      )
 *)
 *
 *
 * 枚举
 * @SWG\Definition(
 *   definition="category_type",
 *   type="string",
 *   description="config:配置",
 *   enum={"config"},
 *   default="config"
 * )
 *
 * @SWG\Definition(
 *   definition="model_status",
 *   type="string",
 *   description="enabled：启用，disabled：禁用，deleted：删除",
 *   enum={"enabled","disabled","deleted"},
 *   default="enabled"
 * )
 *
 */
