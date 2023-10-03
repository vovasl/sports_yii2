<?php

use backend\components\pinnacle\Pinnacle;
use backend\components\ps3838\PS3838;
use backend\components\sofascore\Sofascore;
use backend\services\EventResultSave;
use backend\services\EventSave;
use kartik\icons\Icon;

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
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'nullDisplay' => '',
        ],
        'pinnacle' => Pinnacle::class,
        'sofascore' => Sofascore::class,
        'ps3838' => PS3838::class,
        'event_save' => EventSave::class,
        'result_save' => EventResultSave::class
    ],
    'params' => [
        'icon-framework' => Icon::FAS,  // Font Awesome Icon framework
    ]
];
