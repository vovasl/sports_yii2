<?php

namespace common\helpers\statistic\total\event\player;

use common\helpers\TotalHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Surface;
use frontend\models\sport\Tour;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class FavoriteHelper
{
    /**
     * @param Event $event
     * @param string $type
     * @return array
     */
    public static function getStatistic(Event $event, string $type): array
    {

        /** empty moneyline odds */
        if(is_null($event->favorite)) return [];

        /** empty surface value */
        if(is_null($event->eventTournament->surface)) return [];

        /** search params */
        $params = [
            'tour' => self::getTour($event),
            'surface' => Surface::filterValue($event->eventTournament->surface),
            'round' => self::getRound($event),
            'type' => $type
        ];

        /** sort - favorite is first */
        $players = ($event->favorite == $event->home)
            ? ['home', 'away']
            : ['away', 'home']
        ;

        /** get player statistic - favorite and underdog */
        $data = [];
        foreach ($players as $player) {
            $params['player'] = $player;
            $params['favorite'] = self::getFavorite($event, $event->{$player});
            $data[] = self::getQuery($event, $params);
        }

        $data = self::prepare($data);

        return $data;
    }

    /**
     * @param Event $event
     * @param array $params
     * @return array|ActiveRecord|null
     */
    public static function getQuery(Event $event, array $params)
    {
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
        $query = self::getWhere($query, $event, $params);

        return $query->one();
    }

    /**
     * @param ActiveQuery $query
     * @param Event $event
     * @param array $params
     * @return ActiveQuery
     */
    public static function getWhere(ActiveQuery $query, Event $event, array $params): ActiveQuery
    {

        /** tour */
        if($params['tour']) {
            $query->andWhere(['IN', 'tn_tour.id', $params['tour']]);
        }

        /** surface */
        $query->andWhere(['IN', 'tn_surface.id', $params['surface']]);

        /** round */
        if($params['round']) {
            ($params['round'] == Round::MAIN)
                ? $query->andWhere(['!=', 'tn_event.round', Round::QUALIFIER])
                : $query->andWhere(['tn_event.round' => $params['round']])
            ;
        }

        /** event */
        $query->andWhere(['<', 'tn_event.start_at', $event->start_at]);
        $query->andWhere(['tn_event.five_sets' => $event->five_sets]);

        /** statistic */
        $query->andWhere(['tn_statistic.player_id' => $event->{$params['player']}]);
        $query->andWhere(['tn_statistic.add_type' => $params['type']]);
        $query->andWhere(['<', 'tn_statistic.min_moneyline', TotalHelper::MONEYLINE['over']['favorite']['max']]);

        /** favorite or underdog filter */
        ($params['favorite'])
            ? $query->andWhere('tn_statistic.player_id = tn_event.favorite')
            : $query->andWhere('tn_statistic.player_id != tn_event.favorite')
        ;

        return $query;
    }

    /**
     * @param array $data
     * @return array
     */
    public static function prepare(array $data)
    {
        foreach ($data as $k => $stat) {

            /** unset empty statistic */
            if($stat->count_events == 0) {
                unset($data[$k]);
            }
        }

        return $data;
    }

    /**
     * @param Event $event
     * @return array
     */
    public static function getPlayerUrlParams(Event $event): array
    {
        return [
            '/statistic/total/players',
            'PlayerTotalSearch[tour]' => self::getTourFilter($event),
            'PlayerTotalSearch[surface]' => $event->eventTournament->surface,
            'PlayerTotalSearch[round]' => self::getRoundFilter($event),
            'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['favorite'],
            'PlayerTotalSearch[five_sets]' => $event->five_sets,
            'PlayerTotalSearch[add_type]' => Odd::ADD_TYPE['over'],
            'PlayerTotalSearch[favorite]' => 'Yes',
        ];
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

    /**
     * @param Event $event
     * @param int $player
     * @return bool
     */
    public static function getFavorite(Event $event, int $player): bool
    {
        return ($player == $event->favorite);
    }

    /**
     * @param int $key
     * @return string
     */
    public static function getFavoriteFilter(int $key): string
    {
        return ($key == 0 ) ? 'Yes' : 'No';
    }
}