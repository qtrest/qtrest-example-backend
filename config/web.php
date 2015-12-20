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
            'rules' => [
                ['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],
            ],
        ],
        'devicedetect' => [
            'class' => 'alexandernst\devicedetect\DeviceDetect'
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'appendTimestamp' => true,
            'bundles' => [
                // 'romdim\bootstrap\material\BootMaterialCssAsset' => [
                //     'sourcePath' => '@bower/bootstrap-material-design/dist',
                //     'css' => [
                //         YII_ENV_DEV ? 'css/ripples.css' : 'css/ripples.css',
                //         YII_ENV_DEV ? 'css/bootstrap-material-design.css' : 'css/bootstrap-material-design.css',
                //     ]
                // ],
                // 'romdim\bootstrap\material\BootMaterialJsAsset' => [
                //     'sourcePath' => '@bower/bootstrap-material-design/dist',
                //     'js' => [
                //         YII_ENV_DEV ? 'js/ripples.js' : 'js/ripples.js',
                //         YII_ENV_DEV ? 'js/material.js' : 'js/material.js',
                //     ]
                // ]
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
        'sitemap' => [
            'class' => 'himiklab\sitemap\Sitemap',
            'models' => [
                // your models
                'app\models\Coupon',
            ],
            'urls'=> [
                // your additional urls
                [
                    'loc' => '/coupon/actual',
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                    'coupon' => [
                        'publication'   => [
                            'name'          => 'Все актуальные купоны Казахстана',
                            'language'      => 'ru',
                        ],
                        'access'            => 'Subscription',
                        'genres'            => 'Blog, UserGenerated',
                        'publication_date'  => 'YYYY-MM-DDThh:mm:ssTZD',
                        'title'             => 'Все актуальные купоны и скидки Казахстана',
                        'keywords'          => 'купон, все купоны, все скидки Казахстана, все скидки, скидки, агрегатор скидок, поиск скидок, фильтр скидок, база скидок, 
                                                скидки Алматы,  скидки Астана,  скидки Актау,  скидки Актобе,  скидки Атырау,  скидки Балхаш,  скидки Жезказган,  скидки Караганда,  скидки Кокшетау,  скидки Костанай,  скидки Кызылорда,  скидки Павлодар,  скидки Петропавловск,  скидки Рудный,  скидки Семей,  скидки Талдыкорган,  скидки Тараз,  скидки Темиртау,  скидки Туркестан,  скидки Уральск,  скидки Усть-Каменогорск,  скидки Шымкент,  скидки Экибастуз,
                                                купоны Алматы,  купоны Астана,  купоны Актау,  купоны Актобе,  купоны Атырау,  купоны Балхаш,  купоны Жезказган,  купоны Караганда,  купоны Кокшетау,  купоны Костанай,  купоны Кызылорда,  купоны Павлодар,  купоны Петропавловск,  купоны Рудный,  купоны Семей,  купоны Талдыкорган,  купоны Тараз,  купоны Темиртау,  купоны Туркестан,  купоны Уральск,  купоны Усть-Каменогорск,  купоны Шымкент,  купоны Экибастуз',
                        //'stock_tickers'     => 'NASDAQ:A, NASDAQ:B',
                    ],
                    'images' => [
                        [
                            'loc'           => 'http://skid.kz/img/skid.jpg',
                            'caption'       => 'Все актуальные купоны и скидки Казахстана',
                            'geo_location'  => 'Kazakhstan, Astana',
                            'title'         => 'http://skid.kz/img/skid.jpg',
                            'license'       => 'http://skid.kz/site/about',
                        ],
                    ],
                ],
                [
                    'loc' => '/coupon/archive',
                    'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8,
                    'coupon' => [
                        'publication'   => [
                            'name'          => 'Все архивные купоны Казахстана',
                            'language'      => 'ru',
                        ],
                        'access'            => 'Subscription',
                        'genres'            => 'Blog, UserGenerated',
                        'publication_date'  => 'YYYY-MM-DDThh:mm:ssTZD',
                        'title'             => 'Все актуальные купоны и скидки Казахстана',
                        'keywords'          => 'купон, все купоны, все скидки Казахстана, все скидки, скидки, агрегатор скидок, поиск скидок, фильтр скидок, база скидок, 
                                                скидки Алматы,  скидки Астана,  скидки Актау,  скидки Актобе,  скидки Атырау,  скидки Балхаш,  скидки Жезказган,  скидки Караганда,  скидки Кокшетау,  скидки Костанай,  скидки Кызылорда,  скидки Павлодар,  скидки Петропавловск,  скидки Рудный,  скидки Семей,  скидки Талдыкорган,  скидки Тараз,  скидки Темиртау,  скидки Туркестан,  скидки Уральск,  скидки Усть-Каменогорск,  скидки Шымкент,  скидки Экибастуз,
                                                купоны Алматы,  купоны Астана,  купоны Актау,  купоны Актобе,  купоны Атырау,  купоны Балхаш,  купоны Жезказган,  купоны Караганда,  купоны Кокшетау,  купоны Костанай,  купоны Кызылорда,  купоны Павлодар,  купоны Петропавловск,  купоны Рудный,  купоны Семей,  купоны Талдыкорган,  купоны Тараз,  купоны Темиртау,  купоны Туркестан,  купоны Уральск,  купоны Усть-Каменогорск,  купоны Шымкент,  купоны Экибастуз',
                        //'stock_tickers'     => 'NASDAQ:A, NASDAQ:B',
                    ],
                    'images' => [
                        [
                            'loc'           => 'http://skid.kz/img/skid.jpg',
                            'caption'       => 'Все архивные купоны и скидки Казахстана',
                            'geo_location'  => 'Kazakhstan, Astana',
                            'title'         => 'http://skid.kz/img/skid.jpg',
                            'license'       => 'http://skid.kz/site/about',
                        ],
                    ],
                ],
            ],
            'enableGzip' => true, // default is false
            //'cacheExpire' => 1, // 1 second. Default is 24 hours
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
