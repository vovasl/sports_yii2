<?php
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

                '/tournament-statistic/total/<tour:\d+>/<surface:\d+>/<qualifier:\d+>' => '/tournament-statistic/total',
                '/tournament-statistic/total/<tour:\d+>/<surface:\d+>' => '/tournament-statistic/total',
                '/tournament-statistic/total/<tour:\d+>' => '/tournament-statistic/total',

                '/statistic/total/<tour:\d+>/<surface:\d+>/<qualifier:.+>' => '/statistic/total',
                '/statistic/total/<tour:\d+>/<surface:\d+>' => '/statistic/total',
                '/statistic/total/<tour:\d+>' => '/statistic/total',

                '/event/add-line-log/<id:\d+>/<save:\d+>' => '/event/add-line-log',
                '/event/add-line-log/<id:\d+>' => '/event/add-line-log',

                '/tournament/<id:\d+>' => '/tournament/view',
                '/tournament/<id:\d+>/event' => '/tournament/event',
                '/event/<id:\d+>' => '/event/view',
                '/event/add-results/<data:.+>' => '/event/add-results',
            ],
        ],
    ],
    'params' => $params,
];
