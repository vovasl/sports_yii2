<?php

namespace backend\controllers;


use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use backend\components\pinnacle\Pinnacle;
use backend\components\pinnacle\helpers\BaseHelper;

class EventController extends Controller
{

    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionIndex(int $id): string
    {
        return $this->render('index');
    }

    /**
     * @return string
     */
    public function actionAdd(): string
    {
        $settings = [
            'sportid' => Pinnacle::TENNIS,
            'tour' => Pinnacle::ATP
        ];
        $events = Yii::$app->pinnacle->run($settings);
        return $this->render('add', [
            'output' => Yii::$app->event_save->events($events)
        ]);
    }


    /**
     * @return string
     */
    public function actionAddLine(): string
    {
        /*
        $eventId = ;
        $home = ;
        $away = ;

        $moneyline = [
            'moneyline' => [
                'home' => ,
                'away' => ,
            ]
        ];


        $spreads = [
            'spreads' => [
                [
                    'hdp' => ,
                    'home' => ,
                    'away' => ,
                ]
            ]
        ];

        $odds = [
            //'sets' => $moneyline,
            //'games' => $spreads,
        ];

        $event = [
            'id' => $eventId,
            'home' => $home,
            'away' => $away,
            'odds' => $odds,
        ];

        return $this->render('add-line', [
            'output' => Yii::$app->event_save->addOdds($event),
            'event' => $event
        ]);
        */
    }

}