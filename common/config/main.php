<?php

use backend\components\pinnacle\Pinnacle;
use backend\components\sofascore\Sofascore;
use backend\services\EventResultSave;
use backend\services\EventSave;

return [
    'name' => 'Tennis Odds',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'pinnacle' => Pinnacle::class,
        'sofascore' => Sofascore::class,
        'event_save' => EventSave::class,
        'result_save' => EventResultSave::class
    ],
];
