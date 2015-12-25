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
        'kupon/cron/kupikupon' => [
            'cron'      => '10 20 * * *',
        ],
        'kupon/cron/mirkuponov' => [
            'cron'      => '10 18 * * *',
        ],
        'kupon/cron/autokupon' => [
            'cron'      => '10 22 * * *',
        ],
        'kupon/cron/update' => [
            'cron'      => '15 7 * * *',
        ],
    ],
];
