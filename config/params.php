<?php

return [
    'adminEmail' => 'v31337@gmail.com',
    'proxy' => 'tcp://127.0.0.1:8118',
    'cronJobs' =>[
        'kupon/cron/chocolife' => [
            'cron'      => '10 5 * * *',
        ],
        'kupon/cron/blizzard' => [
            'cron'      => '10 6 * * *',
        ],
        'kupon/cron/besmartkz' => [
            'cron'      => '10 7 * * *',
        ],
        'kupon/cron/mirkuponov' => [
            'cron'      => '10 8 * * *',
        ],
        'kupon/cron/autokupon' => [
            'cron'      => '10 9 * * *',
        ],
        'kupon/cron/kupikupon' => [
            'cron'      => '10 10 * * *',
        ],
        'kupon/cron/update' => [
            'cron'      => '10 5 3 * *',
        ],
        'kupon/cron/actualize-category' => [
            'cron'      => '0 */10 * * *',
        ],
    ],
];
