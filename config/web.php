<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'language' => 'ru',
    'charset'=>'utf-8',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'defaultRoute' => 'coupon',
    'vendorPath' => dirname(__DIR__)."/vendor",
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'kupons',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'flushInterval' => 1, // <-- here
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['kupon'],
                    'except' => ['application'],
                    'logVars' => [null],
                    'logFile' => '@app/runtime/logs/kupon/kupon.log',
                    'maxFileSize' => 1024 * 2,
                    'maxLogFiles' => 20,
                    'exportInterval' => 1, // <-- and here
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'tools' => [
            'class' => '\app\components\Tools',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'appendTimestamp' => true,
            'bundles' => [
                'romdim\bootstrap\material\BootMaterialCssAsset' => [
                    'sourcePath' => '@bower/bower-asset/bootstrap-material-design/dist',
                    'css' => [
                        YII_ENV_DEV ? 'css/ripples.css' : 'css/ripples.min.css',
                        YII_ENV_DEV ? 'css/material-fullpalette.css' : 'css/material-fullpalette.min.css',
                    ]
                ],
                'romdim\bootstrap\material\BootMaterialJsAsset' => [
                    'sourcePath' => '@bower/bower-asset/bootstrap-material-design/dist',
                    'js' => [
                        YII_ENV_DEV ? 'js/ripples.js' : 'js/ripples.min.js',
                        YII_ENV_DEV ? 'js/material.js' : 'js/material.min.js',
                    ]
                ]
            ]
        ]
    ],
	'modules' => [
		'kupon' => [
			'class' => 'app\modules\kupon\Kupon'
		],
        'autoproxy' => [
            'class' => 'app\modules\autoproxy\AutoProxy',
        ],
		'debug' => [
			'class' => 'yii\debug\Module',
			'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.102', '192.168.1.11', '192.168.1.5'],
		],
	],
    'params' => $params,
];

if (YII_ENV == 'dev') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.102', '10.0.2.2'],
            //'password' => '123456'
        ];
		
	$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
			'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.102', '192.168.1.11', '10.0.2.2'],
        ];
}

return $config;
