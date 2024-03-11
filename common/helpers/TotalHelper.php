<?php

namespace common\helpers;

use backend\models\statistic\total\EventTotalSearch;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;
use yii\db\Expression;

class TotalHelper
{

    CONST ODDS = [215, 201, 187, 175, 0];

    CONST MONEYLINE = [
        'over' => [
            'min' => 150,
            'favorite' => [
                'max' => 140
            ],
        ],
        'under' => [
            'min' => 100
        ],
    ];

    CONST MIN_EVENTS = 15;
    CONST MIN_PERCENT = [
        'max' => 20,
        'min' => 0,
    ];
    CONST CURRENT = 0;

    /**
     * @param $settings
     * @param null $current
     * @return array|mixed
     */
    public static function getStatsTitle($settings, $current = null)
    {
        $odds = $settings;
        sort($odds);
        $data = [];

        foreach ($odds as $k => $odd) {
            $prefix = '';
            $titles = [];

            /** first title */
            if($odd == reset($odds)) {
                $prefix = '<';
                $titles[] = $odds[$k + 1];
            }
            /** last title */
            else if($odd == end($odds)) {
                $prefix = '>=';
                $titles[] = $odd;
            }
            /** middle title */
            else {
                $titles[] = $odd;
                $titles[] = $odds[$k + 1];
            }

            /** prepare titles for odd view */
            array_walk($titles, function (&$odd) {
                $odd = round($odd / 100, 2);
            });

            $data[] = $prefix . implode("-", $titles);

            /** get one title */
            if(!is_null($current) && $current == $k) return end($data);
        }

        return $data;
    }

    /**
     * @param Event $event
     * @param string $type
     * @param EventTotalSearch $search
     * @return string
     */
    public static function getEventPlayersGeneralStat(Event $event, string $type, EventTotalSearch $search): string
    {

        /** get surface */
        $surface = (!empty($search->surface_id)) ? $search->surface_id : $event->eventTournament->surface;

        /** empty surface value */
        if(is_null($surface)) return '';

        /** get moneyline */
        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? self::MONEYLINE['over']['min'] : self::MONEYLINE['under']['min'];

        $query = Statistic::find();
        $query->select([
            'player_id',
            'round((round(sum(profit_0)/count(profit_0)) + round(sum(profit_1)/count(profit_1)))/2) percent_profit',
            //'round(sum(profit_0)/count(profit_0)) percent_profit',
            'count(event_id) count_events'
        ]);
        $query->joinWith([
            'event',
            'event.eventTournament.tournamentTour',
            'event.eventTournament.tournamentSurface',
        ]);
        $query->where(['type' => Odd::ADD_TYPE['over']]);
        $query->andWhere(['IN', 'tn_tour.id', Tour::getValue(self::getTour($event->eventTournament->tour))]);
        $query->andWhere(['IN', 'tn_surface.id', Surface::filterValue($surface)]);
        $query->andWhere(['<>', 'tn_event.round', Round::QUALIFIER]);
        $query->andWhere(['>=', 'min_moneyline', $minMoneyline]);
        $query->andWhere(['IN', 'player_id', [$event->home, $event->away]]);
        if(!self::CURRENT) {
            $query->andWhere(['<', 'tn_event.start_at', $event->start_at]);
        }
        $query->groupBy('player_id');
        $query->having(['>=', 'count(event_id)', !self::CURRENT ? self::MIN_EVENTS : 5]);
        $query->orderBy([new Expression("FIELD(player_id, $event->home, $event->away)")]);
        $models = $query->all();

        $output = "";
        if(count($models) != 2) return $output;

        /** get max and min percent */
        $maxPercentProfit = ($models[0]->percent_profit >= $models[1]->percent_profit)
            ? $models[0]->percent_profit:
            $models[1]->percent_profit;
        $minPercentProfit = ($models[0]->percent_profit <= $models[1]->percent_profit)
            ? $models[0]->percent_profit
            : $models[1]->percent_profit;

        /** filer by max and min percent */
        if ($maxPercentProfit < self::MIN_PERCENT['max'] || $minPercentProfit < self::MIN_PERCENT['min']) return $output;

        $stats = [];
        foreach ($models as $model) {
            //$stats[] = $model->getPercentProfit();
            $stats[] = TotalHelper::getPercent($model->getPercentProfit()) . '' . $model->count_events;
        }

        $output = join(' ', $stats);

        /** totalOver output markers */
        $totalOver = EventHelper::getOddStat($event->totalsOver);
/*        if(in_array($totalOver, ['5/5', '7/7', '4/5', '3/5', '2/5'])) $output .= ' QQQQQ';
        else if(in_array($totalOver, ['0/5', '0/6', '0/7', '1/7'])) $output .= ' WWWWW';
        else if(in_array($totalOver, ['1/5', '2/7'])) $output .= ' EEEEE';
        else $output .= ' TTTTT';*/


        return $output;
    }

    /**
     * @param $tour
     * @return int|mixed
     */
    public static function getTour($tour)
    {
        if(in_array($tour, Tour::ATP_ALL)) return -1;
        if(in_array($tour, Tour::WTA_ALL)) return -2;

        return $tour;
    }

    /**
     * @param $val
     * @return string
     */
    public static function getPercent($val): string
    {
        return "{$val}%";
    }

}