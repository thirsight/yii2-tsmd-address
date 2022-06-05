<?php

/**
 * TSMD 模块配置文件
 *
 * @link https://tsmd.thirsight.com/
 * @copyright Copyright (c) 2008 thirsight
 * @license https://tsmd.thirsight.com/license/
 */

$dbTpl = require dirname(dirname(__DIR__)) . '/yii2-tsmd-base/config/_dbtpl-local.php';
return [
    // 设置路径别名，以便 Yii::autoload() 可自动加载 TSMD 自定的类
    'aliases' => [
        // yii2-tsmd-address 路径
        '@tsmd/address' => __DIR__ . '/../src',
    ],
    // 设置命令行模式控制器
    // ./yii migrate-address/create 'tsmd\address\migrations\M20081900...'
    // ./yii migrate-address/new
    // ./yii migrate-address/up
    // ./yii migrate-address/down 1
    'controllerMap' => [
        'migrate-address' => [
            'class' => 'yii\console\controllers\MigrateController',
            'db' => 'db',
            'migrationPath' => [],
            'migrationNamespaces' => [
                'tsmd\address\migrations',
            ]
        ],
    ],
];
