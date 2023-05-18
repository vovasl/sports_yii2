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
        $methods = ['homeMoneyline', 'awayMoneyline'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->groupBy('event.id')
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**TotalUnder
     * @return string
     */
    public function actionTotalUnder(): string
    {
        $methods = ['totalsUnder'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTotalOver(): string
    {
        $methods = ['totalsOver'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionSetsTotalUnder(): string
    {
        $methods = ['setsTotalsUnder'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionSetsTotalOver(): string
    {
        $methods = ['setsTotalsOver'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTeamTotalUnder(): string
    {
        $methods = ['homeTotalsUnder', 'awayTotalsUnder'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionTeamTotalOver(): string
    {
        $methods = ['homeTotalsOver', 'awayTotalsOver'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionSpread(): string
    {
        $methods = ['homeSpreads', 'awaySpreads'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }

    /**
     * @return string
     */
    public function actionSetsSpread(): string
    {
        $methods = ['homeSetsSpreads', 'awaySetsSpreads'];

        $events = Event::find()
            ->from(['event' => 'tn_event'])
            ->withData()
            ->with($methods)
            ->order()
            ->all()
        ;

        return $this->render('events', [
            'oddMethods' => $methods,
            'events' => $events
        ]);
    }
}