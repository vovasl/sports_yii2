<?php

namespace backend\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\OddMove;
use yii\db\Expression;
use yii\web\Controller;

class TestController extends Controller
{

    public function actionEventMoveLine()
    {

        /** get not started events */
        $events = Event::find()
            ->select(['tn_event.*',])
            ->joinWith([
                'homeMoneyline',
                'awayMoneyline',
                'oddsHistory'
            ])
            ->where(['>', 'tn_event.start_at', new Expression('now()')])
            ->groupBy('sp_odd_history.event')
            ->all()
        ;

        foreach ($events as $event) {
            $model = new OddMove();
            $model->addEvent($event);
        }

        /** get opened started events */
        $events = Event::find()
            ->joinWith([
                'oddsMove'
            ])
            ->where(['sp_odd_move.status' => OddMove::STATUS_OPEN])
            ->andWhere(['<=', 'tn_event.start_at', new Expression('now()')])
            ->all()
        ;

        foreach ($events as $event) {
            $model = new OddMove();
            $model->addEvent($event, OddMove::STATUS_FINISHED);
        }

        return $this->render('event-move-line', [
            'events' => $events
        ]);
    }

}