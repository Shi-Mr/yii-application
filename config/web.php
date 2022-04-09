<?php

$params = require __DIR__ . '/params.php';

return [
    'id' => 'app',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'app\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'request' => [
            'csrfParam' => '_csrf-app',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'error/error',
        ]
    ],
    'params' => $params,
];
