<?php

namespace backend\services\statistic\event\total;

use backend\services\statistic\event\Base;
use common\helpers\total\PlayerHelper;
use common\helpers\TotalHelper;
use frontend\models\sport\PlayerTotal;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class OverPlayer extends Base
{

    CONST TITLE = 'vs Over Players';

    /**
     * @return array
     */
    public function getStatistic(): array
    {
        /** validate event */
        if(!$this->validateEvent()) return [];

        /** get search params */
        $params = $this->getSearchParams();

        /** sort players */
        $players = ['home', 'away'];

        /** get players statistic */
        $data = [];
        foreach ($players as $player) {

            $params['player'] = $player;

            /** get player events */
            $params['events'] = (PlayerTotal::find()
                ->where(['player_id' => $this->event->{$player}])
                ->andWhere(['type' => $this->type])
                ->andWhere(['IN', 'tour_id', $params['tour']])
                ->andWhere(['IN', 'surface_id', $params['surface']])
                ->all()
            )
                ? PlayerHelper::getEvents()
                : PlayerHelper::getEventsPlayerNotOver($this->event, $params)
            ;

            $data[] = self::getPlayerStatistic($params);
        }

        $data = $this->prepareStatistic($data);

        return $data;
    }

    /**
     * @return array
     */
    public function getSearchParams(): array
    {
        return [
            'tour' => $this->getTour(),
            'surface' => $this->getSurface(),
            'round' => Round::QUALIFIER,
            'moneyline' => TotalHelper::MONEYLINE['over']['min'],
            'five_sets' => $this->event->five_sets,
        ];
    }

    /**
     * @param array $params
     * @return array|ActiveRecord|null
     */
    public function getPlayerStatistic(array $params)
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
        $query = $this->getWhere($query, $params);

        return $query->one();
    }

    /**
     * @param ActiveQuery $query
     * @param array $params
     * @return ActiveQuery
     */
    public function getWhere(ActiveQuery $query, array $params): ActiveQuery
    {
        /** tour */
        $query->andWhere(['IN', 'tn_tournament.tour', $params['tour']]);

        /** surface */
        $query->andWhere(['IN', 'tn_tournament.surface', $params['surface']]);

        /** round */
        $query->andWhere(['!=', 'tn_event.round', $params['round']]);

        /** event */
        $query->andWhere(['tn_event.five_sets' => $params['five_sets']]);
        $query->andWhere(['IS NOT', 'tn_event.sofa_id', null]);
        $query->andWhere(['IN', 'tn_event.id', $params['events']]);

        /** statistic */
        $query->andWhere(['tn_statistic.player_id' => $this->event->{$params['player']}]);
        $query->andWhere(['tn_statistic.add_type' => $this->type]);
        $query->andWhere(['>=', 'tn_statistic.min_moneyline', $params['moneyline']]);

        return $query;
    }

    /**
     * @return array
     */
    public function getUrl(): array
    {
        return [
            '/statistic/total/players-over',
            'PlayerTotalSearch[tour]' => $this->getTourFilter(),
            'PlayerTotalSearch[surface]' => $this->getSurface(),
            'PlayerTotalSearch[round]' => Round::MAIN,
            'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['equal'],
            'PlayerTotalSearch[five_sets]' => $this->event->five_sets,
            'PlayerTotalSearch[add_type]' => $this->type,
        ];
    }

    /**
     * @return int|int[]
     */
    public function getTour()
    {
        return Tour::getValue($this->getTourFilter());
    }

    /**
     * @return int
     */
    public function getTourFilter(): int
    {
        return Tour::getFilterValue($this->event->eventTournament->tour);
    }

    /**
     * @return int|null
     */
    public function getSurface(): ?int
    {
        return $this->event->eventTournament->surface;
    }

}