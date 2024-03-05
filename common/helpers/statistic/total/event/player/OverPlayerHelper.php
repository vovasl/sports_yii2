<?php

namespace common\helpers\statistic\total\event\player;

use common\helpers\total\PlayerHelper;
use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\PlayerTotal;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;

class OverPlayerHelper
{
    /**
     * @param Event $event
     * @return array
     */
    public static function getStatistic(Event $event): array
    {

        /** empty surface value */
        if(is_null($event->eventTournament->surface)) return [];

        /** base values */
        $tour = self::getTour($event);
        $surface = $event->eventTournament->surface;

        $stats = [];
        foreach (['away', 'home'] as $player) {

            /** get events */
            $events = (PlayerTotal::find()
                ->where(['player_id' => $event->{$player}])
                ->andWhere(['type' => Odd::ADD_TYPE['over']])
                ->andWhere(['IN', 'tour_id', $tour])
                ->andWhere(['IN', 'surface_id', $surface])
                ->all()
            )
                ? PlayerHelper::getEvents()
                : PlayerHelper::getEventsPlayerNotOver($event, $player, $tour, $surface)
            ;

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

            $query->andWhere(['IN', 'tn_tournament.tour', $tour]);
            $query->andWhere(['IN', 'tn_tournament.surface', $surface]);
            $query->andWhere(['!=', 'tn_event.round', Round::QUALIFIER]);
            $query->andWhere(['tn_event.five_sets' => $event->five_sets]);
            $query->andWhere(['IS NOT', 'tn_event.sofa_id', null]);
            $query->andWhere(['>=', 'min_moneyline', TotalHelper::OVER_MIN_MONEYLINE]);
            $query->andWhere(['tn_statistic.add_type' => Odd::ADD_TYPE['over']]);
            $query->andWhere(['IN', 'tn_statistic.player_id', [$event->{$player}]]);
            $query->andWhere(['IN', 'tn_event.id', $events]);

            $query->groupBy('player_id');

            $stats = array_merge_recursive($query->all(), $stats);
        }

        return $stats;
    }

    /**
     * @param Event $event
     * @return int|int[]
     */
    public static function getTour(Event $event)
    {
        return Tour::getValue(self::getTourFilter($event));
    }

    /**
     * @param Event $event
     * @return int
     */
    public static function getTourFilter(Event $event): int
    {
        return Tour::getFilterValue($event->eventTournament->tour);
    }

}