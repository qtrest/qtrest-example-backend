<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
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
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'tools' => [
            'class' => '\app\components\Tools',
        ],
    ],
	'modules' => [
		'kupon' => [
			'class' => 'app\modules\kupon\Kupon'
		],
		'debug' => [
			'class' => 'yii\debug\Module',
			'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.102', '192.168.1.11'],
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
            'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.102'],
            //'password' => '123456'
        ];
		
	$config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
            'class' => 'yii\debug\Module',
			'allowedIPs' => ['127.0.0.1', '::1', '192.168.1.102', '192.168.1.11'],
        ];
}

return $config;
