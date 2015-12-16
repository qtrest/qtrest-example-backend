<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

$params = require(__DIR__ . '/params.php');
$db = require(__DIR__ . '/db.php');

return [
    'id' => 'basic-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'gii'],
    'controllerNamespace' => 'app\commands',
    'modules' => [
        'gii' => 'yii\gii\Module',
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
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
            'template' => '/../api/doc/restdoc.twig',
            'class' => '\pahanini\restdoc\controllers\BuildController',
            'sortProperty' => 'shortDescription', // <-- default value (how controllers will be sorted)
            'targetFile' => __DIR__ . '/../web/slate/index.md',
            'on afterAction' => function() { exec("bundle exec middleman build") }
        ],
    ],
    'params' => $params,
];
