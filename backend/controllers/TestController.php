<?php

namespace backend\controllers;

use backend\services\EventStatisticSave;
use common\helpers\statistic\MoneylineHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\OddMove;
use yii\db\Expression;
use yii\web\Controller;
use frontend\models\sport\Statistic;

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

    public function actionEventStatsMoneyline($type = 'moneyline')
    {
        /** get events */
        $events = Event::find()
            ->joinWith([
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface'
            ])
            ->active()
            ->andWhere(['IS NOT', 'tn_event.sofa_id', new Expression('null')])
            ->andWhere(['IS NOT', 'tn_event.pin_id', new Expression('null')])
            ->groupBy('tn_event.id')
            ->orderBy(['tn_event.id' => SORT_ASC])
            //->limit(25)
            ->all()
        ;

        foreach ($events as $event) {

            if(!isset($event->homeMoneyline[0]) || !isset($event->awayMoneyline[0])) continue;

            /** get odds settings */
            $oddsSettings = MoneylineHelper::ODDS;
            $oddsSettingsLastKey = array_key_last($oddsSettings);
            sort($oddsSettings);

            /** players */
            foreach (EventStatisticSave::PLAYER_TYPE as $player) {

                $playerMoneyline = "{$player}Moneyline";
                $moneyline = $event->$playerMoneyline[0];

                /** save model */
                $model = new Statistic();
                $model->player_id = $event->{$player};
                $model->event_id = $event->id;
                $model->type = (Odd::TYPE[$type]) ?? null;

                foreach ($oddsSettings as $k => $odd) {
                    if (($k == $oddsSettingsLastKey && $moneyline->odd >= $odd) || ($moneyline->odd >= $odd && $moneyline->odd < $oddsSettings[$k + 1])) {
                        $profitField = "profit_{$k}";
                        $idField = "odd_id_{$k}";

                        $model->{$profitField} = $moneyline->profit;
                        $model->{$idField} = $moneyline->id;
                        break;
                    }
                }

                //$model->save(0);
            }
        }

        return '';
    }

}