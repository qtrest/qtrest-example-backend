<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

Yii::setAlias('@runnerScript', dirname(__DIR__) .'/yii');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'kupon'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
        'kupon' => [
            'class' => 'app\modules\kupon\Kupon'
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 100, // <-- here
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'logFile' => '@app/runtime/logs/console/cron.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 5,
                    'exportInterval' => 100, // <-- and here
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['kupon', ''],
                    'logFile' => '@app/runtime/logs/console/info.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 5,
                    'exportInterval' => 100, // <-- and here
                    'logVars' => ['_GET', '_POST', '_FILES', '_COOKIE', '_SESSION'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'categories' => ['kupon'],
                    'logFile' => '@app/runtime/logs/console/error.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 5,
                ],
            ],
        ],
        'db' => $db,
    ],
    'controllerMap' => [
        'build-rest-doc' => [
            'sourceDirs' => [
                __DIR__ . '/../api/modules/v1/controllers',   // <-- path to your API controllers
            ],
            'template' => '/../api/doc/restdoc.php',
            'class' => '\pahanini\restdoc\controllers\BuildController',
            'sortProperty' => 'shortDescription', // <-- default value (how controllers will be sorted)
            'targetFile' => __DIR__ . '/../web/slate/source/index.md',
            //'on afterAction' => function() { exec("bundle exec middleman build") }
        ],
        'cron' => [
           'class' => 'denisog\cronjobs\CronController'
        ],
    ],
    'params' => $params,
];
