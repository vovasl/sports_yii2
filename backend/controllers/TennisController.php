<?php

namespace backend\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddType;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TennisController extends Controller
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
     * @return string
     */
    public function actionMoneyline(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['homeMoneyline', 'awayMoneyline'])
            ->groupBy('event.id')
            ->order()
            ->all()
        ;

        return $this->render('moneyline', [
            'events' => $events
        ]);
    }

    /**TotalUnder
     * @return string
     */
    public function actionTotalUnder(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['totalsUnder'])
            ->order()
            ->all()
        ;

        return $this->render('totals', [
            'oddMethod' => 'totalsUnder',
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTotalOver(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['totalsOver'])
            ->order()
            ->all()
        ;

        return $this->render('totals', [
            'oddMethod' => 'totalsOver',
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionSetsTotalUnder(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['setsTotalsUnder'])
            ->order()
            ->all()
        ;

        return $this->render('totals', [
            'oddMethod' => 'setsTotalsUnder',
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionSetsTotalOver(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['setsTotalsOver'])
            ->order()
            ->all()
        ;

        return $this->render('totals', [
            'oddMethod' => 'setsTotalsOver',
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTeamTotalUnder(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['teamTotalsUnder'])
            ->order()
            ->all()
        ;

        return $this->render('totals', [
            'oddMethod' => 'teamTotalsUnder',
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTeamTotalOver(): string
    {
        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with(['teamTotalsOver'])
            ->order()
            ->all()
        ;

        return $this->render('totals', [
            'oddMethod' => 'teamTotalsOver',
            'events' => $events
        ]);
    }
}