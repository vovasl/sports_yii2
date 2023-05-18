<?php

namespace backend\controllers;


use frontend\models\sport\Event;
use yii\filters\AccessControl;
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

    /**
     * @return string
     */
    public function actionTotal(): string
    {
        $methods = ['totalsOver', 'totalsUnder'];

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
    public function actionSetsTotal(): string
    {
        $methods = ['setsTotalsOver', 'setsTotalsUnder'];

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
    public function actionHandicap(): string
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
    public function actionSetsHandicap(): string
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