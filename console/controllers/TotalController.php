<?php

namespace console\controllers;


use common\helpers\OddHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Total;
use Yii;
use yii\console\Controller;

class TotalController extends Controller
{

    public function actionAdd()
    {
        /** truncate table */
        Yii::$app->db->createCommand()->truncateTable(Total::tableName())->execute();

        /** get events */
        $events = Event::find()
            ->active()
            ->joinWith([
                'eventTournament',
                'eventTournament.tournamentTour',
                'eventTournament.tournamentSurface'
            ])
            //->limit(10)
            ->orderBy(['id' => SORT_ASC])
            ->all()
        ;

        $players = ['home', 'away'];
        $types = ['totalsOver', 'totalsUnder'];
        foreach ($events as $event) {
            if (count($event->totalsOver) == 0) continue;

            /** get moneyline */
            $homeMoneyline = $event->homeMoneyline[0]->odd;
            $awayMoneyline = $event->awayMoneyline[0]->odd;

            /** players */
            foreach ($players as $player) {
                /** types */
                foreach ($types as $type) {
                    /** save model */
                    $model = new Total();
                    $model->player_id = $event->{$player};
                    $model->event_id = $event->id;
                    $model->tour_id = $event->eventTournament->tournamentTour->id;
                    $model->surface_id = $event->eventTournament->tournamentSurface->id;
                    $model->five_sets = $event->five_sets;
                    $model->type = $event->{$type}[0]->add_type;
                    $model->min_moneyline = ($homeMoneyline <= $awayMoneyline) ? $homeMoneyline : $awayMoneyline;
                    $model = $this->getProfit($model, $event, $type);
                    $model->save(0);
                }
            }
        }
    }

    /**
     * @param Total $model
     * @param Event $event
     * @param string $type
     * @return Total
     */
    public function getProfit(Total $model, Event $event, string $type): Total
    {
        /** get odds settings */
        $oddsSettings = OddHelper::totalSettings();
        sort($oddsSettings);

        /** get profit values */
        foreach ($event->{$type} as $odd) {
            foreach ($oddsSettings as $k => $setting) {
                if (($k == array_key_last($oddsSettings) && $odd->odd >= $setting) || ($odd->odd >= $setting && $odd->odd < $oddsSettings[$k + 1])) {
                    $field = "profit_{$k}";

                    /** field without value */
                    if (is_null($model->{$field})) $model->{$field} = $odd->profit;

                    break;
                }
            }
        }

        return $model;
    }
}