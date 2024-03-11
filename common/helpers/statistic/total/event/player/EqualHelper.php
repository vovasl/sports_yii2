<?php

namespace common\helpers\statistic\total\event\player;

use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use yii\db\Expression;

class EqualHelper
{
    /**
     * @param Event $event
     * @param string $type
     * @return array
     */
    public static function getStatistic(Event $event, string $type): array
    {

        /** empty surface value */
        if(is_null($event->eventTournament->surface)) return [];

        /** base values */
        $tour = self::getTour($event);
        $surface = Surface::filterValue($event->eventTournament->surface);
        $round = self::getRound($event);
        $minMoneyline = ($type == Odd::ADD_TYPE['over']) ? TotalHelper::MONEYLINE['over']['min'] : TotalHelper::MONEYLINE['under']['min'];

        $query = Statistic::find();
        $query->select([
            'tn_statistic.*',
            'count(event_id) count_events',
            'count(profit_0) count_profit_0',
            'count(profit_1) count_profit_1',
            'count(profit_2) count_profit_2',
            'count(profit_3) count_profit_3',
            'count(profit_4) count_profit_4',
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

        /** tour */
        if($tour) {
            $query->andWhere(['IN', 'tn_tour.id', $tour]);
        }

        $query->andWhere(['IN', 'tn_surface.id', $surface]);

        /** round */
        if($round) {
            if ($round == Round::MAIN) {
                $query->andWhere(['!=', 'tn_event.round', Round::QUALIFIER]);
            } else {
                $query->andWhere(['tn_event.round' => $round]);
            }
        }

        $query->andWhere(['tn_event.five_sets' => $event->five_sets]);
        $query->andWhere(['tn_statistic.add_type' => $type]);
        $query->andWhere(['IN', 'tn_statistic.player_id', [$event->home, $event->away]]);

        $query->andWhere(['>=', 'min_moneyline', $minMoneyline]);

        $query->groupBy('player_id');
        $query->orderBy([new Expression("FIELD(player_id, $event->home, $event->away)")]);

        return $query->all();
    }

    /**
     * @param Event $event
     * @return false|int|int[]|string|null
     */
    public static function getTour(Event $event)
    {
        $filterValue = self::getTourFilter($event);
        if(empty($filterValue)) return false;
        return Tour::getValue($filterValue);
    }

    /**
     * @param Event $event
     * @return int|string
     */
    public static function getTourFilter(Event $event)
    {
        if($event->round == Round::QUALIFIER) return '';
        return Tour::getFilterValue($event->eventTournament->tour);
    }

    /**
     * @param Event $event
     * @return false|int|string
     */
    public static function getRound(Event $event)
    {
        $filterValue = self::getRoundFilter($event);
        if(empty($filterValue)) return false;
        return $filterValue;
    }

    /**
     * @param Event $event
     * @return int|string
     */
    public static function getRoundFilter(Event $event)
    {
        if($event->round == Round::QUALIFIER) return '';
        return Round::MAIN;
    }
}