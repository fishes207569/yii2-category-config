<?php
/**
 *  @SWG\Definition(
 *   definition="CategoryItem",
 *   description="分类列表元素",
 *   type="object",
 *   allOf={
 *       @SWG\Schema(ref="#/definitions/category"),
 *       @SWG\Schema(
 *           required={"children"},
 *           @SWG\Property(property="children",type="array",description="子分类数组",uniqueItems=true,@SWG\Items(ref="#/definitions/CategoryItem"))
 *       )
 *   }
 * )
 **/