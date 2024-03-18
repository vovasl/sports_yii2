<?php

namespace backend\services\statistic\event\total;

use backend\services\statistic\event\Base;
use common\helpers\TotalHelper;
use frontend\models\sport\Round;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

class FavoritePlayer extends Base
{

    CONST TITLE = 'Favorite';
    CONST FAVORITE = true;

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = parent::getData();

        /** empty data */
        if(count($data) == 0) return $data;

        $data['favorite'] = self::FAVORITE;
        return $data;

    }

    /**
     * @return array
     */
    public function getStatistic(): array
    {

        /** validate event */
        if(!$this->validateEvent()) return [];

        /** get search params */
        $params = $this->getSearchParams();

        /** sort - favorite is first */
        $players = ($this->event->favorite == $this->event->home)
            ? ['home', 'away']
            : ['away', 'home']
        ;

        /** get player statistic - favorite and underdog */
        $data = [];
        foreach ($players as $player) {
            $params['player'] = $player;
            $params['favorite'] = $this->getFavorite($player);
            $data[] = $this->getPlayerStatistic($params);
        }

        $data = $this->prepareStatistic($data);

        return $data;
    }

    /**
     * @return bool
     */
    public function validateEvent(): bool
    {

        if(!parent::validateEvent()) return false;

        /** empty moneyline odds */
        if(is_null($this->event->favorite)) return false;

        return true;
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
            'moneyline' => TotalHelper::MONEYLINE['over']['favorite']['max'],
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
        $query->andWhere(['tn_statistic.player_id' => $this->event->{$params['player']}]);
        $query->andWhere(['tn_statistic.add_type' => $this->type]);
        $query->andWhere(['<', 'tn_statistic.min_moneyline', $params['moneyline']]);

        /** favorite or underdog filter */
        ($params['favorite'])
            ? $query->andWhere('tn_statistic.player_id = tn_event.favorite')
            : $query->andWhere('tn_statistic.player_id != tn_event.favorite')
        ;

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
            'PlayerTotalSearch[min_moneyline]' => Statistic::TOTAL_FILTER['moneyline']['favorite'],
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
        //if($this->event->round == Round::QUALIFIER) return '';
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

    /**
     * @param string $player
     * @return bool
     */
    public function getFavorite(string $player): bool
    {
        return ($this->event->{$player} == $this->event->favorite);
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