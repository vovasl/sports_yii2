<?php

namespace backend\services\statistic\event\total;

use backend\services\statistic\event\Base;
use common\helpers\TotalHelper;
use frontend\models\sport\Odd;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;
use yii\db\ActiveQuery;
use yii\db\Expression;

class Player extends Base
{

    CONST TITLE = 'Even';

    /**
     * @return array
     */
    public function getStatistic(): array
    {

        /** validate event */
        if(!$this->validateEvent()) return [];

        /** get search params */
        $params = $this->getSearchParams();

        /** get players statistic */
        $data = $this->getPlayersStatistic($params);

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
            'round' => $this->getRound(),
            'moneyline' => ($this->type == Odd::ADD_TYPE['over'])
                ? TotalHelper::MONEYLINE['over']['min']
                : TotalHelper::MONEYLINE['under']['min'],
            'five_sets' => $this->event->five_sets
        ];
    }

    /**
     * @param array $params
     * @return array
     */
    public function getPlayersStatistic(array $params): array
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

        $query->groupBy('player_id');
        $query->orderBy([new Expression("FIELD(player_id, {$this->event->home}, {$this->event->away})")]);

        return $query->all();
    }

    /**
     * @param ActiveQuery $query
     * @param array $params
     * @return ActiveQuery
     */
    public function getWhere(ActiveQuery $query, array $params): ActiveQuery
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
        $query->andWhere(['<', 'tn_event.start_at', $this->event->start_at]);
        $query->andWhere(['tn_event.five_sets' => $params['five_sets']]);

        /** statistic */
        $query->andWhere(['IN', 'tn_statistic.player_id', [$this->event->home, $this->event->away]]);
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
            '/statistic/total/players',
            'PlayerTotalSearch[tour]' => $this->getTourFilter(),
            'PlayerTotalSearch[surface]' => $this->getSurface(),
            'PlayerTotalSearch[round]' => $this->getRoundFilter(),
            'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['equal'],
            'PlayerTotalSearch[five_sets]' => $this->event->five_sets,
            'PlayerTotalSearch[add_type]' => $this->type,
        ];
    }

    /**
     * @return false|int|int[]|string|null
     */
    public function getTour()
    {
        $filterValue = $this->getTourFilter();
        if(empty($filterValue)) return false;
        return Tour::getValue($filterValue);
    }

    /**
     * @return int|string
     */
    public function getTourFilter()
    {
        if($this->event->round == Round::QUALIFIER) return '';
        return Tour::getFilterValue($this->event->eventTournament->tour);
    }

    /**
     * @return int|null
     */
    public function getSurface(): ?int
    {
        return $this->event->eventTournament->surface;
    }

    /**
     * @return false|int|string
     */
    public function getRound()
    {
        $filterValue = $this->getRoundFilter();
        if(empty($filterValue)) return false;
        return $filterValue;
    }

    /**
     * @return int|string
     */
    public function getRoundFilter()
    {
        if($this->event->round == Round::QUALIFIER) return '';
        return Round::MAIN;
    }
}