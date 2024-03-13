<?php

namespace console\controllers;

use backend\components\ps3838\PS3838;
use backend\services\event\EventCheck;
use frontend\models\sport\Event;
use frontend\models\sport\OddMove;
use Yii;
use yii\console\Controller;
use yii\db\Expression;

class EventController extends Controller
{
    public function actionAdd()
    {
        Yii::$app->runAction('event/add-odd');
        Yii::$app->runAction('event/add-odd-move');
        Yii::$app->runAction('event/check');
    }

    public function actionAddOdd()
    {
        $settings = [
            'sportId' => PS3838::TENNIS,
            'tour' => PS3838::TOUR
        ];
        $events = Yii::$app->ps3838->run($settings);

        $i = 0;
        $output = "";
        $count = count($events);

        //Console::output('Events');
        //Console::startProgress($i, $count, 'Status');
        foreach ($events as $event) {
            $i++;
            //Console::updateProgress($i, $count);

            $output .= "\n";
            $output .= "{$event['tournament']} {$event['round']} \n";
            $output .= "{$event['o_starts']} {$event['home']} - {$event['away']} - ";
            $output .= Yii::$app->event_save->event($event) ? 'OK' : 'Error';

        }
        //Console::output($output);
    }

    public function actionAddOddMove()
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

    }

    public function actionCheck()
    {
        $eventCheck = new EventCheck();
        $eventCheck->process();
    }

}