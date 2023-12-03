<?php


namespace common\helpers;


use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Surface;
use frontend\models\sport\Total;
use yii\db\Expression;

class TotalHelper
{

    CONST OVER_MIN_MONEYLINE = 140;
    CONST UNDER_MIN_MONEYLINE = 100;

    /**
     * @param Event $event
     * @param string $type
     * @return string
     */
    public static function getEventPlayersGeneralStat(Event $event, string $type): string
    {
        $minPercentBoth = 20;
        $maxPercent = 20;
        $minPercent = -10;
        $minEvents = 25;
        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? self::OVER_MIN_MONEYLINE : self::UNDER_MIN_MONEYLINE;
        $surface = (in_array($event->eventTournament->surface, Surface::HARD_INDOOR))
            ? Surface::HARD_INDOOR
            : $event->eventTournament->surface;

        $query = Total::find();
        $query->select([
            'player_id',
            'round((round(sum(profit_0)/count(profit_0)) + round(sum(profit_1)/count(profit_1)))/2) percent_profit',
        ]);
        $query->joinWith(['event']);
        $query->where(['<', 'tn_event.start_at', $event->start_at]);
        $query->andWhere(['>=', 'min_moneyline', $minMoneyline]);
        $query->andWhere(['type' => Odd::ADD_TYPE['over']]);
        $query->andWhere(['IN', 'player_id', [$event->home, $event->away]]);
        $query->andWhere(['IN', 'surface_id', $surface]);
        $query->andWhere(['sp_total.five_sets' => $event->five_sets]);
        $query->groupBy('player_id');
        $query->having(['>=', 'count(event_id)', $minEvents]);
        //$query->andHaving(['>=', 'percent_profit', $minPercentBoth]);
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
        if($maxPercentProfit < $maxPercent || $minPercentProfit < $minPercent) return $output;

        $stats = [];
        foreach ($models as $model) {
            $stats[] = $model->getPercentProfit();
        }

        $output = join(' ', $stats);

        /** totalOver output markers */
        /*
        $totalOver = EventHelper::getOddStat($event->totalsOver);
        if(in_array($totalOver, ['5/5', '7/7', '4/5', '3/5', '2/5'])) $output .= ' QQQQQ';
        else if(in_array($totalOver, ['0/5', '0/6', '0/7', '1/7'])) $output .= ' WWWWW';
        else if(in_array($totalOver, ['1/5', '2/7'])) $output .= ' EEEEE';
        else $output .= ' TTTTT';
        */

        return $output;
    }

    /**
     * @param Event $event
     * @param string $type
     * @return array
     */
    public static function getEventPlayersStat(Event $event, string $type): array
    {
        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? self::OVER_MIN_MONEYLINE : self::UNDER_MIN_MONEYLINE;
        $surface = (in_array($event->eventTournament->surface, Surface::HARD_INDOOR))
            ? Surface::HARD_INDOOR
            : $event->eventTournament->surface;

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
        $query->with(['player']);
        $query->joinWith(['event']);
        $query->where(['<', 'tn_event.start_at', $event->start_at]);
        $query->andWhere(['>=', 'min_moneyline', $minMoneyline]);
        $query->andWhere(['type' => $type]);
        $query->andWhere(['IN', 'player_id', [$event->home, $event->away]]);
        $query->andWhere(['IN', 'surface_id', $surface]);
        $query->andWhere(['sp_total.five_sets' => $event->five_sets]);
        $query->groupBy('player_id');
        $query->orderBy([new Expression("FIELD(player_id, $event->home, $event->away)")]);

        return $query->all();
    }
}