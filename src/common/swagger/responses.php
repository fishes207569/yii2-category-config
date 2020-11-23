<?php

/**
 * @SWG\Response(
 *   response="default",
 *   description="响应数据",
 *   @SWG\Schema(
 *     type="object",
 *     @SWG\Property(
 *       property="code",
 *       type="string",
 *       default="200",
 *       description="状态码"
 *     ),
 *     @SWG\Property(
 *       property="msg",
 *       type="string",
 *       default="ok",
 *       description="描述"
 *     ),
 *     @SWG\Property(
 *       property="data",
 *       type="object",
 *       description="数据"
 *     ),
 *     @SWG\Property(
 *       property="request_id",
 *       type="string",
 *       description="请求ID"
 *     ),
 *   )
 * )
 */