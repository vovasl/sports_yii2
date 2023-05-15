<?php

use backend\components\pinnacle\Pinnacle;
use frontend\components\EventSave;

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'event_save' => EventSave::class,
        'pinnacle' => Pinnacle::class,
    ],
];
