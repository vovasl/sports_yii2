<?php

namespace backend\models\statistic\moneyline;

use common\helpers\EventHelper;
use frontend\models\sport\Event;
use frontend\models\sport\Odd;
use frontend\models\sport\Player;
use frontend\models\sport\Round;
use frontend\models\sport\Surface;
use frontend\models\sport\Statistic;
use frontend\models\sport\Tour;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class StatisticSearch extends Statistic
{

    public $tour;

    public $surface;

    public $tournament;

    public $tournament_id;

    public $round;

    public $five_sets;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'tour', 'surface', 'round', 'tournament_id', 'five_sets', 'count_events', 'count_profit_0', 'count_profit_1', 'count_profit_2', 'count_profit_3', 'count_profit_4', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'percent_profit', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['tournament', 'add_type', 'min_moneyline', 'date_from', 'date_to'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function scenarios(): array
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Statistic::find()
            ->select([
                'tn_statistic.*',
                'count(profit_0) count_profit_0',
                'count(profit_1) count_profit_1',
                'count(profit_2) count_profit_2',
                'count(profit_3) count_profit_3',
                'count(profit_4) count_profit_4',
                'round(sum(profit_0)/count(profit_0), 1) percent_profit_0',
                'round(sum(profit_1)/count(profit_1), 1) percent_profit_1',
                'round(sum(profit_2)/count(profit_2), 1) percent_profit_2',
                'round(sum(profit_3)/count(profit_3), 1) percent_profit_3',
                'round(sum(profit_4)/count(profit_4), 1) percent_profit_4',
            ])
            ->joinWith([
                'event',
                'event.eventTournament.tournamentTour',
                'event.eventTournament.tournamentSurface',
                'player',
            ])
        ;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                ]
            ],
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** default search params */
        if (empty($params)) {
            $this->round = Round::MAIN;
            $this->five_sets = 0;
        }

        /** total filter */
        $query->andFilterWhere(['tn_statistic.type' => Odd::TYPE['moneyline']]);

        /** date from filter */
        if (!empty($this->date_from)) {
            $query->andFilterWhere(['>', 'tn_event.start_at', $this->date_from]);
        }

        /** date to filter */
        if (!empty($this->date_to)) {
            $query->andFilterWhere(['<', 'tn_event.start_at', $this->date_to]);
        }

        /** tour filter */
        if(!is_null($this->tour)) {
            $query->andFilterWhere(['IN', 'tn_tour.id', Tour::getValue($this->tour)]);
        }

        /** surface filter */
        if(!is_null($this->surface)) {
            $query->andFilterWhere(['IN', 'tn_surface.id', Surface::filterValue($this->surface)]);
        }

        /** tournament filter */
        if(!is_null($this->tournament)) {
            $query->andFilterWhere(['LIKE', 'tn_tournament.name', trim($this->tournament)]);
        }

        /** tournament id filter */
        if(!is_null($this->tournament_id)) {
            $query->andFilterWhere(['tn_event.tournament' => $this->tournament_id]);
        }

        /** round filter */
        if (!empty($this->round)) {
            $query->andFilterWhere(['IN', 'tn_event.round', Round::filterValue($this->round)]);
        }

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['tn_event.five_sets' => $this->five_sets]);
        }

        return $dataProvider;
    }

}