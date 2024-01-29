<?php

namespace backend\controllers;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddMove;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
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
            ->where(['sp_odd_move.status' => OddMove::STATUSES['open']])
            ->andWhere(['<=', 'tn_event.start_at', new Expression('now()')])
            ->all()
        ;

        foreach ($events as $event) {
            $model = new OddMove();
            $model->addEvent($event, OddMove::STATUSES['finished']);
        }

        return $this->render('event-move-line', [
            'events' => $events
        ]);
    }

    public function actionEventMoneyline()
    {
        $events = Event::find()
            ->select(['id'])
            ->where(['favorite' => null])
            //->limit(100)
            ->all()
        ;

        foreach ($events as $event) {
            $moneyline = ArrayHelper::map(Odd::find()
                ->select(['player_id', 'odd'])
                ->where([
                    'event' => $event->id,
                    'type' => Odd::TYPE['moneyline']
                ])
                ->all(), 'player_id', 'odd')
            ;

            /** event without moneyline */
            if(count($moneyline) != 2) continue;

            /** get favorite */
            foreach ($moneyline as $player_id => $odd) {
                if(is_null($event->favorite) || $odd < reset($moneyline)) {
                    $event->favorite = $player_id;
                }
            }

            //$event->save(0);
        }

    }

}