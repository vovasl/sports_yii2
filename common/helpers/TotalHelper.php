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
        $minPercent = 10;
        $minEvents = 10;
        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? self::OVER_MIN_MONEYLINE : self::UNDER_MIN_MONEYLINE;
        $surface = (in_array($event->eventTournament->surface, Surface::HARD_INDOOR))
            ? Surface::HARD_INDOOR
            : $event->eventTournament->surface;

        $query = Total::find();
        $query->select([
            'player_id',
            'round((round(sum(profit_0)/count(event_id)) + round(sum(profit_1)/count(event_id)))/2) percent_profit',
        ]);
        $query->where(['>=', 'min_moneyline', $minMoneyline]);
        $query->andWhere(['type' => Odd::ADD_TYPE['over']]);
        $query->andWhere(['IN', 'player_id', [$event->home, $event->away]]);
        $query->andWhere(['IN', 'surface_id', $surface]);
        $query->andWhere(['five_sets' => $event->five_sets]);
        $query->groupBy('player_id');
        $query->having(['>=', 'count(event_id)', $minEvents]);
        $query->andHaving(['>=', 'percent_profit', $minPercent]);
        $query->orderBy([new Expression("FIELD(player_id, $event->home, $event->away)")]);
        $models = $query->all();

        $output = "";
        if(count($models) != 2) return $output;

        $stats = [];
        foreach ($models as $model) {
            $stats[] = $model->getPercentProfit();
        }

        $output = join(' ', $stats);

        /** totalOver output markers */
        /*
        $totalOver = EventHelper::getOddStat($event->totalsOver);
        if(in_array($totalOver, ['5/5', '7/7', '4/5', '3/5', '2/5'])) $output .= ' QQQQQ';
        else if($totalOver == '0/5') $output .= ' WWWWW';
        else if($totalOver == '1/5') $output .= ' EEEEE';
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
            'round(sum(profit_0)/count(event_id)) percent_profit_0',
            'round(sum(profit_1)/count(event_id)) percent_profit_1',
            'round(sum(profit_2)/count(event_id)) percent_profit_2',
            'round(sum(profit_3)/count(event_id)) percent_profit_3',
            'round(sum(profit_4)/count(event_id)) percent_profit_4',
        ]);
        $query->with(['player']);
        $query->where(['>=', 'min_moneyline', $minMoneyline]);
        $query->andWhere(['type' => $type]);
        $query->andWhere(['IN', 'player_id', [$event->home, $event->away]]);
        $query->andWhere(['IN', 'surface_id', $surface]);
        $query->andWhere(['five_sets' => $event->five_sets]);
        $query->groupBy('player_id');
        $query->orderBy([new Expression("FIELD(player_id, $event->home, $event->away)")]);

        return $query->all();
    }
}