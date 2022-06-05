<?php

/**
 * TSMD 模块配置文件
 *
 * @link https://tsmd.thirsight.com/
 * @copyright Copyright (c) 2008 thirsight
 * @license https://tsmd.thirsight.com/license/
 */

return [
    // 设置路径别名，以便 Yii::autoload() 可自动加载 TSMD 自定的类
    'aliases' => [
        // yii2-tsmd-address 路径
        '@tsmd/address' => __DIR__ . '/../src',
    ],

    // 设置模块
    'modules' => [
        'address' => [
            'class' => 'tsmd\address\Module',
        ],
    ],
];
