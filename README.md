Category Config for Yii2
=================
Category Config By Ccheng

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ccheng/yii2-category-config "*"
```

or add

```
"yii2-category-config": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Once the extension is installed, simply use it in your code by  :

执行数据迁移以添加表结构:

```shell
yii migrate --migrationPath=@vendor/ccheng/yii2-category-config/src/console/migrations
```


添加配置管理接口模块
```php
return [
	'modules' => [
		'config' => [
                'class'=>'ccheng\config\api\Module',
		]
		...
	]
];
```
