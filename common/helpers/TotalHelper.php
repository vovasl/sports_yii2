<?php

namespace common\helpers;


use backend\models\total\EventTotalSearch;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
use frontend\models\sport\Tour;
use yii\db\Expression;

class TotalHelper
{

    CONST ODDS = [209, 197, 186, 176, 0];

    CONST OVER_MIN_MONEYLINE = 150;
    CONST UNDER_MIN_MONEYLINE = 100;
    CONST MIN_EVENTS = 15;
    CONST MIN_PERCENT = [
        'max' => 20,
        'min' => 0,
    ];
    CONST CURRENT = 1;

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
        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? self::OVER_MIN_MONEYLINE : self::UNDER_MIN_MONEYLINE;

        $query = Total::find();
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
        $query->andWhere(['IN', 'tn_tour.id', Tour::filterValue(self::getTour($event->eventTournament->tour))]);
        $query->andWhere(['IN', 'tn_surface.id', Surface::filterValue(self::getSurface($surface))]);
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
            $stats[] = $model->getPercentProfit() . '' . $model->count_events;
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
     * @param Event $event
     * @param string $type
     * @return array
     */
    public static function getEventPlayersStat(Event $event, string $type): array
    {

        /** empty surface value */
        if(is_null($event->eventTournament->surface)) return [];

        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? self::OVER_MIN_MONEYLINE : self::UNDER_MIN_MONEYLINE;

        $query = Total::find();
        $query->select([
            'sp_total.*',
            'count(event_id) count_events',
            'round(sum(profit_0)/count(profit_0)) percent_profit_0',
            'round(sum(profit_1)/count(profit_1)) percent_profit_1',
            'round(sum(profit_2)/count(profit_2)) percent_profit_2',
            'round(sum(profit_3)/count(profit_3)) percent_profit_3',
            'round(sum(profit_4)/count(profit_4)) percent_profit_4',
        ]);
        $query->joinWith([
            'event',
            'event.eventTournament.tournamentTour',
            'event.eventTournament.tournamentSurface',
        ]);
        $query->where(['<', 'tn_event.start_at', $event->start_at]);
        $query->andWhere(['IN', 'tn_tour.id', Tour::filterValue(self::getTour($event->eventTournament->tour))]);
        $query->andWhere(['IN', 'tn_surface.id', Surface::filterValue(self::getSurface($event->eventTournament->surface))]);
        $query->andWhere(['<>', 'tn_event.round', Round::QUALIFIER]);
        $query->andWhere(['>=', 'min_moneyline', $minMoneyline]);
        $query->andWhere(['sp_total.type' => $type]);
        $query->andWhere(['IN', 'sp_total.player_id', [$event->home, $event->away]]);
        $query->groupBy('player_id');
        $query->orderBy([new Expression("FIELD(player_id, $event->home, $event->away)")]);

        return $query->all();
    }

    public static function getTour($tour)
    {
        return (in_array($tour, [Tour::ATP, Tour::DAVIS_CUP])) ? '-1' : $tour;
    }

    public static function getSurface($surface)
    {
        return $surface;
        //return (in_array($surface, [Surface::SURFACES['hard'], Surface::SURFACES['indoor']])) ? '-1' : $surface;
    }
}