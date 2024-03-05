<?php

namespace backend\models\statistic\total;


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

class PlayerTotalSearch extends Statistic
{

    public $player_name;

    public $tour;

    public $surface;

    public $round;

    public $five_sets;

    public $favorite;

    public $event_ids;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['player_id', 'event_id', 'tour', 'surface', 'round', 'five_sets', 'count_events', 'profit_0', 'profit_1', 'profit_2', 'profit_3', 'profit_4', 'count_profit_0', 'count_profit_1', 'count_profit_2', 'count_profit_3', 'count_profit_4', 'percent_profit_0', 'percent_profit_1', 'percent_profit_2', 'percent_profit_3', 'percent_profit_4'], 'integer'],
            [['add_type', 'player_name', 'min_moneyline', 'favorite'], 'string', 'max' => 255],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::class, 'targetAttribute' => ['event_id' => 'id']],
            [['player_id'], 'exist', 'skipOnError' => true, 'targetClass' => Player::class, 'targetAttribute' => ['player_id' => 'id']],
            ['event_ids', 'each', 'rule' => ['integer']],
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
                'tn_statistic.player_id',
                'tn_statistic.add_type',
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
            ])
            ->joinWith([
                'event',
                'event.eventTournament.tournamentTour',
                'event.eventTournament.tournamentSurface',
                'player',
            ])
            ->with('playerTotal')
            ->groupBy('player_id')
        ;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'percent_profit_0' => SORT_DESC
                ],
                'attributes' => [
                    'player_id',
                    'count_events',
                    'percent_profit_0',
                    'percent_profit_1',
                    'percent_profit_2',
                    'percent_profit_3',
                    'percent_profit_4',
                ]
            ],
            /*
            'pagination' => [
                'pageSize' => 100,
            ],
            */
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        /** default search params */
        if(empty($params)) {
            $this->add_type = Odd::ADD_TYPE['over'];
            $this->min_moneyline = Statistic::TOTAL_FILTER['moneyline']['equal'];
            $this->count_events = 15;
            $this->round = Round::MAIN;
            $this->five_sets = 0;
        }

        /** default search params - event_ids */
        if(!is_null($this->event_ids)) {
            $this->add_type = Odd::ADD_TYPE['over'];
            $this->min_moneyline = Statistic::TOTAL_FILTER['moneyline']['equal'];
            $this->round = Round::MAIN;
            $this->five_sets = 0;

            if(is_null($this->tour)) $this->tour = -1;
            if(is_null($this->surface)) $this->surface = -1;
        }

        /** player filter */
        if(!empty(trim($this->player_name))) {
            $query->andFilterWhere(['LIKE', 'tn_player.name', trim($this->player_name)]);
            $this->count_events = null;
        }

        /** tour filter */
        if(!is_null($this->tour)) {
            $query->andFilterWhere(['IN', 'tn_tour.id', Tour::getValue($this->tour)]);
        }

        /** surface filter */
        if(!is_null($this->surface)) {
            $query->andFilterWhere(['IN', 'tn_surface.id', Surface::filterValue($this->surface)]);
        }

        /** round filter */
        if(!is_null($this->round)) {
            if($this->round == Round::MAIN) {
                $query->andFilterWhere(['<>', 'tn_event.round', Round::QUALIFIER]);
            }
            else {
                $query->andFilterWhere(['tn_event.round' => $this->round]);
            }
        }
        /** type filter */
        if(!is_null($this->add_type)) {
            $query->andFilterWhere(['tn_statistic.add_type' => $this->add_type]);
        }

        /** moneyline filter */
        if(!empty($this->min_moneyline)) {
            $moneyline = EventHelper::parseValueFilter($this->min_moneyline);
            if(!empty($moneyline)) {
                $moneylineOdd = Odd::setOdd($moneyline[1]);
                $query->andFilterWhere([$moneyline[2], 'min_moneyline', $moneylineOdd]);
            }
            else {
                $moneylineOdd = Odd::setOdd($this->min_moneyline);
                $query->andFilterWhere(['=', 'min_moneyline', $moneylineOdd]);
            }
        }

        /** events filter */
        if(!is_null($this->count_events)) {
            $query->having(['>=', 'count_events', $this->count_events]);
        }

        /** favorite filter */
        if(!empty($this->favorite)) {
            if($this->favorite == 'Yes') {
                $query->andWhere('tn_event.favorite = tn_statistic.player_id');
            }
            else if($this->favorite == 'No') {
                $query->andWhere('tn_event.favorite != tn_statistic.player_id');
            }
        }

        /** five sets filter */
        if(!is_null($this->five_sets)) {
            $query->andFilterWhere(['tn_event.five_sets' => $this->five_sets]);
        }

        /** event ids filter */
        if(!is_null($this->event_ids)) {
            $query->andWhere(['IN', 'tn_event.id', $this->event_ids]);
        }

        return $dataProvider;
    }

}