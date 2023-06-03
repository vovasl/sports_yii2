<?php

use backend\components\pinnacle\Pinnacle;
use backend\components\sofascore\Sofascore;
use backend\services\EventSave;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/tournaments' => '/tournament/tournaments',
                '/tournament/<id:\d+>' => '/tournament/index',
                '/event/<id:\d+>' => '/event/index',
                '/event/add-line/<id:\d+>' => '/event/add-line',
                '/event/result-sofa/<data:.+>' => '/event/result-sofa',
            ],
        ],
        'event_save' => EventSave::class,
        'pinnacle' => Pinnacle::class,
        'sofascore' => Sofascore::class
    ],
    'params' => $params,
];
